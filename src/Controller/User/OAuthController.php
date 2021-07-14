<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Exception\OAuthException;
use App\Exception\UserModificationException;
use App\Security\TokenAuthority;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method User|null getUser()
 */
class OAuthController extends AbstractController
{
    public function generateOAuth(): Response
    {
        $authenticatedUser = $this->getUser();

        if ($authenticatedUser === null) {
            throw (new OAuthException(Response::HTTP_NOT_FOUND, 'There was an error modifying the user'))
                ->setErrors(['User is not logged in']);
        }

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($authenticatedUser);
            $entityManager->flush();
            $entityManager->clear();
        } catch (Exception $e) {
            $error = $authenticatedUser !== null && in_array('ROLE_ADMIN', $authenticatedUser->getRoles())
                ? $e->getMessage()
                : "There was a problem saving the user: {$authenticatedUser->getEmail()}";
            throw (new UserModificationException(Response::HTTP_BAD_REQUEST, 'There was an error creating the token'))
                ->setErrors([$error]);
        }

        return new JsonResponse(
            [
                'access_token' => 'fake token',
                'token_type' => 'bearer',
            ],
            200,
            [
                'Cache-Control' => 'no-store',
                'Pragma' => 'no-cache',
            ]
        );
    }

    /**
     * This function will check for a provided App token. If non provided, should redirect to the OAuth Login.
     */
    public function oAuthRegister(Request $request, TokenAuthority $tokenAuthority): Response
    {
        $appToken = $request->get('appToken');
        $returnUrl = $request->get('returnUrl');

        if ($appToken === null) {
            return $this->redirectToRoute('oauth.login', ['returnUrl' =>$returnUrl]);
        }

        $user = $tokenAuthority->validateToken($appToken);

        if ($user === null) {
            return $this->redirectToRoute('oauth.login', ['returnUrl' =>$returnUrl]);
        }

        // From here, need to provide a refresh token, and an access token.
        // Need to build new entities.
        http_build_query([
            'access_token' => 'AQXNnd2kXITHELmWblJigbHEuoFdfRhOwGA0QNnumBI8XOVSs0HtOHEU-wvaKrkMLfxxaB1O4poRg2svCWWgwhebQhqrETYlLikJJMgRAvH1ostjXd3DP3BtwzCGeTQ7K9vvAqfQK5iG_eyS-q-y8WNt2SnZKZumGaeUw_zKqtgCQavfEVCddKHcHLaLPGVUvjCH_KW0DJIdUMXd90kWqwuw3UKH27ki5raFDPuMyQXLYxkqq4mYU-IUuZRwq1pcrYp1Vv-ltbA_svUxGt_xeWeSxKkmgivY_DlT3jQylL44q36ybGBSbaFn-UU7zzio4EmOzdmm2tlGwG7dDeivdPDsGbj5ig',
            'expires_in' => 86400,
            'refresh_token' => 'AQWAft_WjYZKwuWXLC5hQlghgTam-tuT8CvFej9-XxGyqeER_7jTr8HmjiGjqil13i7gMFjyDxh1g7C_G1gyTZmfcD0Bo2oEHofNAkr_76mSk84sppsGbygwW-5oLsb_OH_EXADPIFo0kppznrK55VMIBv_d7SINunt-7DtXCRAv0YnET5KroQOlmAhc1_HwW68EZniFw1YnB2dgDSxCkXnrfHYq7h63w0hjFXmgrdxeeAuOHBHnFFYHOWWjI8sLLenPy_EBrgYIitXsAkLUGvZXlCjAWl-W459feNjHZ0SIsyTVwzAQtl5lmw1ht08z5Du-RiQahQE0sv89eimHVg9VSNOaTvw',
            'refresh_token_expires_in' =>  525600,
        ]);

        return new RedirectResponse($returnUrl);
    }
}
