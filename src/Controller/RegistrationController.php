<?php

namespace App\Controller;

use App\Entity\CategorieDeServices;
use App\Entity\Commune;
use App\Entity\Internaute;
use App\Entity\Prestataire;
use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\VarDumper\VarDumper;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelper;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    private $verifyEmailHelper;
    private $mailer;
    private EmailVerifier $emailVerifier;

    public function __construct(VerifyEmailHelperInterface $helper, MailerInterface $mailer, EmailVerifier $emailVerifier){
        $this->verifyEmailHelper = $helper;
        $this->emailVerifier = $emailVerifier;

        $this->mailer = $mailer;
    }
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/inscription', name: 'inscription')]
    public function register(Request $request, LoginFormAuthenticator $authenticator, UserAuthenticatorInterface $userAuthenticator, UserPasswordHasherInterface $userPasswordHasher,  UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager): Response
    {

        $user = new Utilisateur();
        $user->setInscription(new \DateTime());
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // encode the plain password
            $user->setMotdepasse(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            //rajouter l'id de l'internaute a l'user
            $internaute = new Internaute();
            $internaute->setName($user->getEmail());
            $user->setInternaute($internaute);

            //données dans la db
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('maxkaiser950@gmail.com'))
                    ->to($user->getEmail())
                    ->subject("Confirmation de votre email")
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

           return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );

        }
        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'categories' => $categories
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
            $user->setIsVerified(true);
            $entityManager->persist($user);
            $entityManager->flush();

        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());
            return $this->redirectToRoute('inscription');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');
        return $this->redirectToRoute('app_accueil');

    }
}
