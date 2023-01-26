<?php

namespace App\Controller;

use App\Entity\CategorieDeServices;
use App\Form\StagesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StageController extends AbstractController
{
    #[Route('/stage', name: 'app_stage')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

        return $this->render('stage/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/stage/ajouter', name: 'app_stage_ajouter')]
    public function ajouterStage(EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(StagesType::class);
    }

}
