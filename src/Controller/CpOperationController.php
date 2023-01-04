<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CodePostalRepository;
use Doctrine\ORM\EntityManagerInterface;

class CpOperationController extends AbstractController
{
    private CodePostalRepository $codePostalRepository;
    private EntityManagerInterface $entityManager;

    #[Route('/cp/operation/remove', name: 'app_cp_operation')]
    public function __construct(CodePostalRepository $codePostalRepository, EntityManagerInterface $entityManager)
    {
        $this->codePostalRepository = $codePostalRepository;
        $this->entityManager = $entityManager;
    }

    public function removeDuplicatesAction(): Response
    {
        // Exécuter la méthode de suppression de doublons de CodePostalRepository
        $this->codePostalRepository->removeDuplicates();

        // Enregistrer les modifications en base de données
        $this->entityManager->flush();

        // Renvoyer une réponse au client
        return new Response('Doublons supprimés avec succès !');
    }
}
