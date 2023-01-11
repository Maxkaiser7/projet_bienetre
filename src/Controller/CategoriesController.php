<?php

namespace App\Controller;

use App\Entity\Prestataire;
use App\Entity\Proposer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\CategorieDeServices;

class CategoriesController extends AbstractController
{
    #[Route('/categories', name: 'app_categories')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        /*
        $categorie = new CategorieDeServices();
        $categorie->setNom('Méditation');
        $categorie->setDescription('Tranquilité mentale, séance de méditations');
        $categorie->setEnAvant(false);
        $categorie->setValide(true);
        $entityManager->persist($categorie);
        $entityManager->flush();
*/
        $repository = $entityManager->getRepository(CategorieDeServices::class);
        $categories = $repository->findAll();

        return $this->render('categories/categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categories/show/{nom}', name: 'app_categorie_show')]
    public function categorie(EntityManagerInterface $entityManager, $nom): Response
    {

        $query = $entityManager->createQuery('DELETE FROM App\Entity\Proposer p WHERE p.prestataire IS NULL');
            $query->execute();

            $entityManager->flush();
            $repository = $entityManager->getRepository(CategorieDeServices::class);
            $categorie = $repository->findOneBy(['nom' => $nom]);
        //récupérer les prestataire de la catégorie
        $repository = $entityManager->getRepository(Proposer::class);
         $prestataires = $repository->findBy(['categorieDeServices' => $categorie]);
        return $this->render('categories/show.html.twig', [
            'categorie' => $categorie,
            'prestataires' => $prestataires
        ]);
    }
}
