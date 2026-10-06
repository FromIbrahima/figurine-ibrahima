<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

final class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly EmailVerifier $emailVerifier,
    ) {
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_figurine_index');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Hachage du mot de passe avant l'enregistrement
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // Envoi d'un e-mail contenant le lien de vérification signé
            try {
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('no-reply@figurine-ibrahima.local', 'FiguriThèque'))
                        ->to((string) $user->getEmail())
                        ->subject('Confirmez votre adresse e-mail')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                        ->context(['user' => $user])
                );
            } catch (TransportExceptionInterface) {
                $this->addFlash('danger', 'Votre compte a été créé mais l\'e-mail de confirmation n\'a pas pu être envoyé.');
            }

            $this->addFlash('info', sprintf('Inscription réussie, bienvenue parmi nous %s ! Vous pouvez maintenant vous connecter.', $user->getFirstname()));

            return $this->redirectToRoute('app_login');
        }

        if ($form->isSubmitted()) {
            $this->addFlash('danger', 'L\'inscription a échoué : veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ], new Response(null, $form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK));
    }

    #[Route('/verify/email', name: 'app_verify_email', methods: ['GET'])]
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            $this->addFlash('danger', 'Lien de vérification invalide.');

            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            $this->addFlash('danger', 'Utilisateur introuvable.');

            return $this->redirectToRoute('app_register');
        }

        // Valide le lien de confirmation, puis passe User::isVerified à true
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('danger', 'Le lien de vérification est invalide ou a expiré.');

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Votre adresse e-mail a bien été vérifiée.');

        return $this->redirectToRoute($this->getUser() ? 'app_account' : 'app_login');
    }
}
