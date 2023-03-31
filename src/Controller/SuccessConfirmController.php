<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SuccessConfirmController extends AbstractController
{
    #[Route('/success/confirm', name: 'app_success_confirm')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user= $this->getUser();
        $user->setIsVerified(true);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->render('success_confirm/index.html.twig', [
            'message' => 'Vous avez confirmé votre email'
        ]);
    }
}
