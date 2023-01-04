<?php

namespace App\Controller;

use App\Entity\CategorieDeServices;
use App\Entity\Images;
use App\Entity\Prestataire;
use App\Entity\Proposer;
use App\Entity\Utilisateur;
use App\Form\PrestataireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PrestataireController extends AbstractController
{
    #[Route('/prestataire', name: 'app_prestataire')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        //récupérer les prestataires
        $repository = $entityManager->getRepository(Prestataire::class);
        $prestataires = $repository->findAll();

        $repoCateg = $entityManager->getRepository(CategorieDeServices::class);
        $categ = $repoCateg->find(1);

        $prestataire = $repository->find(1);
        $proposer = new Proposer();
        $proposer->setPrestataire($prestataire);
        $proposer->setCategorieDeServices($categ);
        $entityManager->persist($proposer);
        $entityManager->flush();



        return $this->render('prestataire/prestataires.html.twig', [
            'prestataires' => $prestataires,
        ]);
    }

    #[Route('/prestataire_success', name: 'prestataire_success')]
    public function success(): Response
    {
        return $this->render('prestataire/prestataire_success.html.twig');
    }

    #[Route('/prestataire/form/{id}', name: 'app_prestataire_form')]
    public function prestataireForm(int $id,Request $request, EntityManagerInterface $entityManager): Response
    {
        $prestataire = new Prestataire();
        $form = $this->createForm(PrestataireType::class, $prestataire);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            //Donner à l'utilisateur le statut de prestataire
            $repository = $entityManager->getRepository(Utilisateur::class);
            $utilisateur = $repository->find($id);
            $utilisateur->setPrestataire($prestataire);

            $entityManager->persist($prestataire);
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('prestataire_success');
        }

        return $this->render('prestataire/prestataire_form.html.twig', [
            'prestataireForm' => $form->createView()
        ]);
    }
}
