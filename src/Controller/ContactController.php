<?php

namespace App\Controller;

use App\Entity\CategorieDeServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

        return $this->render('contact/form_promotion.html.twig', [
            'categories' => $categories
        ]);
    }
}
