<?php declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Exception\UserCreationException;
use App\Security\LoginFormAuthenticator;
use App\Utility\UserInputStrings;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    private UserPasswordEncoderInterface $passwordEncoder;
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function form(): Response
    {
        return $this->render('security/create.html.twig', []);
    }

    public function register(
        LoginFormAuthenticator $authenticator,
        GuardAuthenticatorHandler $guardHandler,
        Request $request
    ): ?Response {
        $input = $this->parseInputs($request);
        $token = new CsrfToken('user-create', $input['csrf_token']);

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $email = $input['email'];
        $password = $input['password'] ?? '';

        if ($email === null || preg_match(UserInputStrings::REGEX_EMAIL, $email) !== 1) {
            throw (new UserCreationException(Response::HTTP_BAD_REQUEST, 'There was an error with provided inputs'))
                ->setErrors(['Email is invalid.']);
        }

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([ 'email' => $email ]);

        if ($user !== null) {
            // Going to opt to not inform that the username already exists...
            throw (new UserCreationException(Response::HTTP_BAD_REQUEST, 'There was an error with provided inputs'))
                ->setErrors(['Email is invalid.']);
        } elseif (mb_strlen($password) === 0 || preg_match(UserInputStrings::REGEX_PASSWORD, $password) !== 1) {
            throw (new UserCreationException(Response::HTTP_BAD_REQUEST, 'There was an error with provided inputs'))
                ->setErrors(['Password is invalid.', UserInputStrings::PASSWORD_DESCRIPTION]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();

        $user->setEmail($email);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
        $user->setCreatedDate(new DateTime());

        try {
            $entityManager->persist($user);
            $entityManager->flush();
            $entityManager->clear();
        } catch (Exception $e) {
            $thisUser = $this->getUser();
            $error = $thisUser !== null && in_array('ROLE_ADMIN', $thisUser->getRoles())
                ? $e->getMessage()
                : "There was a problem creating the user: {$email}";
            throw (new UserCreationException(Response::HTTP_BAD_REQUEST, 'There was a problem saving the user'))
                ->setErrors([$error]);
        }

        return $guardHandler->authenticateUserAndHandleSuccess(
            $user,          // the User object you just created
            $request,
            $authenticator, // authenticator whose onAuthenticationSuccess you want to use
            'default'       // the name of your firewall in security.yaml
        );
    }

    /**
     * @return array{ email: ?non-falsy-string, password: ?string, csrf_token: ?string }
     */
    private function parseInputs(Request $request): array
    {
        $requestData = json_decode($request->getContent(), true); // $request->request->all();

        return [
            'email' => UserInputStrings::trimMb4String($requestData['email'] ?? '') ?: null,
            'password' => !empty($requestData['password']) ? (string)$requestData['password'] : null,
            'csrf_token' => !empty($requestData['token']) ? (string)$requestData['token'] : null,
        ];
    }
}
