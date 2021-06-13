<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Exception\UserCreationException;
use App\Utility\UserInputStrings;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @method User|null getUser()
 */
class AccountController extends AbstractController
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(
        RouterInterface $router,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function edit()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('account/edit.html.twig', ['user' => $this->getUser()]);
    }

    public function save(Request $request): Response
    {
        $requestData = $request->request->all();
        $email = UserInputStrings::trimMb4String($requestData['email'] ?? '');
        $password = isset($requestData['password']) ? $requestData['password'] : null;
        $errors = [];

        if (mb_strlen($email) === 0 || preg_match(UserInputStrings::REGEX_EMAIL, $email) !== 1) {
            $errors[] = 'Email is invalid';
        } elseif (mb_strlen($password) === 0 || preg_match(UserInputStrings::REGEX_PASSWORD, $password) !== 1) {
            $errors[] = 'Password is invalid';
        }

        /** @var User|null */
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([ 'email' => $email ]);
        $authenticatedUser = $this->getUser();

        if ($authenticatedUser === null) {
            $errors[] = 'User is not logged in';
        } elseif ($user !== null && $user->email !== $authenticatedUser->email) {
            $errors[] = 'User already exists';
        }

        if (count($errors) > 0) {
            throw (new UserCreationException(Response::HTTP_BAD_REQUEST, 'There was an error with provided inputs'))
                ->setErrors($errors);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $authenticatedUser->setEmail($email);
        $authenticatedUser->setPassword($this->passwordEncoder->encodePassword($authenticatedUser, $password));

        try {
            $entityManager->persist($authenticatedUser);
            $entityManager->flush();
            $entityManager->clear();
        } catch (Exception $e) {
            $error = $authenticatedUser !== null && in_array('ROLE_ADMIN', $authenticatedUser->getRoles())
                ? $e->getMessage()
                : "There was a problem saving the user: {$email}";
            throw (new UserCreationException(Response::HTTP_BAD_REQUEST, 'There was an error with provided inputs', $e))
                ->setErrors([$error]);
        }

        return $this->redirectToRoute('login');
    }
}
