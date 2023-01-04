<?php

namespace App\Controller;

use App\Entity\CodePostal;
use App\Entity\Commune;
use App\Entity\Localite;
use App\Entity\Prestataire;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        //récupérer les 4 derniers prestataires inscrits
        $query = $entityManager->createQuery(
            'SELECT p, u FROM App\Entity\Utilisateur u JOIN u.prestataire p ORDER BY u.inscription DESC'
        )->setMaxResults(4);

        $prestataires = $query->getResult();

        return $this->render('base.html.twig', [
            'controller_name' => 'AccueilController',
            'prestataires' => $prestataires
        ]);
    }
}
