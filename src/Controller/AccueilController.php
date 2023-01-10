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
use Symfony\Component\Security\Core\Security;


class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(EntityManagerInterface $entityManager): Response
    {
       /*$query = $entityManager->createQuery(
            'DELETE FROM App\Entity\Prestataire p WHERE p.id = 14'
        );
        $query->execute();*/
        //récupérer les 4 derniers prestataires inscrits
        $query = $entityManager->createQuery(
            'SELECT p, u FROM App\Entity\Utilisateur u JOIN u.prestataire p ORDER BY u.inscription ASC'
        )->setMaxResults(4);
        $prestataires = $query->getResult();
        return $this->render('base.html.twig', [
            'prestataires' => $prestataires,
        ]);
    }
}
