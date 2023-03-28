<?php

namespace App\Controller;

use App\Entity\CategorieDeServices;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UtilisateurRepository;
use function PHPUnit\Framework\throwException;

class SecurityController extends AbstractController
{
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(UtilisateurRepository $utilisateurRepository){
        $this->utilisateurRepository = $utilisateurRepository;

    }
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager, Request $request, UtilisateurRepository $utilisateurRepository): Response
    {
        //récupération de la dernière route utilisée
        $referer = $request->headers->get('referer');
        $session = $request->getSession();
        $session->set('url', $referer);

        // login error si elle exist
        $error = $authenticationUtils->getLastAuthenticationError();
        // dernier username entré
        $lastUsername = $authenticationUtils->getLastUsername();
        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

        $email = $session->get('_security.last_username');
        $user = $this->utilisateurRepository->findOneByEmail($email);
        $banni = false;
        if ($user){
            if(!$user->isVerified() == false){
                return $this->render('security/error.html.twig', ['message' => 'Votre compte n\'est pas vérifié.']);
            }
        }

        if ($user && $user->isBanni()) {
            $banni = true;
        }
        $session->remove('_security.last_username');
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'categories' => $categories, 'banni' => $banni]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(SessionInterface $session): void
    {
        $session->remove('_security.last_username');
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
