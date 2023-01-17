<?php

namespace App\Controller;

use App\Entity\Prestataire;
use App\Entity\Proposer;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        $categories = $repository->findBy(['valide' => 1]);

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

        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

        return $this->render('categories/show.html.twig', [
            'categorie' => $categorie,
            'categories' => $categories,
            'prestataires' => $prestataires
        ]);
    }

    #[Route('/categories/proposition', name: 'app_categorie_proposer')]
    public function categorieProposer( EntityManagerInterface $entityManager, Request $request ): Response
    {
        $categorie = new CategorieDeServices();

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $data= $form->getData();
            $categorie->setDescription('');
            $categorie->setEnAvant(false);
            $categorie->setValide(false);
            $categorie_nom = $form->get('nom')->getData();
            $categorie->setNom($categorie_nom);

            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('app_accueil');
        }
        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

        return $this->render('categories/categorie_form.html.twig', [
            'categorieForm' => $form->createView(),
            'categories' => $categories
        ]);
    }
}
