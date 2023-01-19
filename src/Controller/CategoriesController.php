<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Prestataire;
use App\Entity\Proposer;
use App\Form\CategorieType;
use App\Form\ImageCategorieType;
use App\Form\ImageType;
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

        $repository = $entityManager->getRepository(CategorieDeServices::class);
        $categories = $repository->findBy(['valide' => 1]);

        return $this->render('categories/categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categories/show/{nom}', name: 'app_categorie_show')]
    public function categorie(EntityManagerInterface $entityManager, $nom, Request $request): Response
    {
        //trouver la categorie via l'url
        $repository = $entityManager->getRepository(CategorieDeServices::class);
        $categorie = $repository->findOneBy(['nom' => $nom]);
        //ajout/changement de l'image par l'admin
        $image = new Images();
        $form = $this->createForm(ImageCategorieType::class, $image);
        $image_current = $entityManager->getRepository(Images::class)->findOneBy(['categorieDeServices' => $categorie]);


        $categorieId = $categorie->getId();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            //suppression des anciennes photos si elles existent
            if ($image_current){
                $query = $entityManager->createQuery('DELETE FROM App\Entity\Images i WHERE i.categorieDeServicesId = :id')
                    ->setParameter('id', $categorieId);
                $query->execute();

            }
            $file = $form['Image']->getData();
            $uploads_directory = $this->getParameter('images_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $image->setImage($filename);
            $image->setCategorieDeServices($categorie);
            $entityManager->persist($image);
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_show', [
                'nom' => $nom,
                'image' => $image
            ]);
        }



        //récupérer les prestataire de la catégorie
        $repository = $entityManager->getRepository(Proposer::class);
        $query = $repository->createQueryBuilder('p')
            ->select('p, categorieDeServices, presta, images')
            ->join('p.categorieDeServices', 'categorieDeServices')
            ->join('p.prestataire', 'presta')
            ->join('presta.images', 'images')
            ->andWhere('categorieDeServices.nom LIKE :nom')
            ->setParameter('nom', $nom)
            ->getQuery();
        $result = $query->getResult();
        $prestataires = $repository->findBy(['categorieDeServices' => $categorie]);

        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

        return $this->render('categories/show.html.twig', [
            'categorie' => $categorie,
            'categories' => $categories,
            'prestataires' => $prestataires,
            'proposer' => $result,
            'formImageCategorie' => $form->createView(),
            'image_current' => $image_current,
            'image'=> $image
        ]);
    }

    #[Route('/categories/proposition', name: 'app_categorie_proposer')]
    public function categorieProposer(EntityManagerInterface $entityManager, Request $request): Response
    {
        $categorie = new CategorieDeServices();

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
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
