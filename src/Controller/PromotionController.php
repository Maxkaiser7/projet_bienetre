<?php

namespace App\Controller;

use App\Entity\CategorieDeServices;
use App\Entity\Prestataire;
use App\Entity\Promotion;
use App\Form\PrestataireType;
use App\Form\PromotionType;
use Couchbase\Document;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PromotionController extends AbstractController
{

    #[Route('/promotion/ajouter/{id}', name: 'app_add_promotion')]
    public function ajouterPromotion(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $prestataire = $entityManager->getRepository(Prestataire::class)->find($id);


        //formulaire de promotion
        $promotion = new Promotion();
        $promotion->setPrestataire($prestataire);

        $form_promotion = $this->createForm(PromotionType::class, $promotion);
        $form_promotion->handleRequest($request);



        if ($form_promotion->isSubmitted() && $form_promotion->isValid()) {
            $data = $form_promotion->getData();
            $promotion->setPrestataire($prestataire);
            $file = $form_promotion['documentPdf']->getData();
            $uploads_directory = $this->getParameter('pdf_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $promotion->setDocumentPdf($filename);
            $entityManager->persist($promotion);
            $entityManager->flush();
            return $this->redirectToRoute('prestataire_show', [
                'id' => $prestataire->getId()
            ]);
        }


        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

        return $this->render('promotion/form_promotion.html.twig', [
            'categories' => $categories,
            'form_promotion' => $form_promotion->createView()

        ]);
    }
}