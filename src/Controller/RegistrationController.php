<?php declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Exception\UserCreationException;
use App\Exception\UserModificationException;
use App\Security\LoginFormAuthenticator;
use App\Utility\UserInputStrings;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function form(): Response
    {
        return $this->render('security/create.html.twig', []);
    }

    public function register(
        LoginFormAuthenticator $authenticator,
        GuardAuthenticatorHandler $guardHandler,
        Request $request
    ): Response {
        $input = $this->parseInputs($request);

        $email = $input['email'];
        $password = $input['password'];

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

    public function edit(Request $request): Response
    {
        $input = $this->parseInputs($request);

        if ($input['email'] === null || preg_match(UserInputStrings::REGEX_EMAIL, $input['email']) !== 1) {
            throw (new UserModificationException(Response::HTTP_BAD_REQUEST, 'There was an error with provided inputs'))
                ->setErrors(['Email is invalid']);
        } elseif ($input['password'] === null || preg_match(UserInputStrings::REGEX_PASSWORD, $input['password']) !== 1
        ) {
            throw (new UserModificationException(Response::HTTP_BAD_REQUEST, 'There was an error with provided inputs'))
                ->setErrors(['Password is invalid.', UserInputStrings::PASSWORD_DESCRIPTION]);
        }

        /** @var User|null */
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([ 'email' => $input['email'] ]);
        $authenticatedUser = $this->getUser();

        if ($authenticatedUser === null) {
            throw (new UserModificationException(Response::HTTP_BAD_REQUEST, 'There was an error modifying the user'))
                ->setErrors(['User is not logged in']);
        } elseif ($user instanceof User && $user->email !== $authenticatedUser->email) {
            throw (new UserModificationException(Response::HTTP_BAD_REQUEST, 'There was an error modifying the user'))
                ->setErrors(['Error with provided email']);
        }

        $authenticatedUser->email = $input['email'];
        $authenticatedUser->setPassword($this->passwordEncoder->encodePassword($authenticatedUser, $input['password']));
        $authenticatedUser->firstName = $input['first-name'];
        $authenticatedUser->lastName = $input['last-name'];

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($authenticatedUser);
            $entityManager->flush();
            $entityManager->clear();
        } catch (Exception $e) {
            $error = $authenticatedUser !== null && in_array('ROLE_ADMIN', $authenticatedUser->getRoles())
                ? $e->getMessage()
                : "There was a problem saving the user: {$input['email']}";
            throw (new UserModificationException(Response::HTTP_BAD_REQUEST, 'There was an error with saving the user'))
                ->setErrors([$error]);
        }

        return $this->redirectToRoute('home');
    }

    /**
     *
     * @param Request $request
     * @return array{
     *     email: string|null
     *     password: string|null
     *     first-name: string|null
     *     last-nam: string|null
     * }
     * @throws LogicException
     */
    private function parseInputs(Request $request): array
    {
        $requestData = json_decode($request->getContent(), true); // $request->request->all();

        return [
            'email' => UserInputStrings::trimMb4String($requestData['email'] ?? '') ?: null,
            'password' => $requestData['password'] ?? null,
        ];
    }
}
