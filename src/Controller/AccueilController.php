<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\CategorieDeServices;
use App\Entity\CodePostal;
use App\Entity\Commune;
use App\Entity\Internaute;
use App\Entity\Localite;
use App\Entity\Prestataire;
use App\Entity\Proposer;
use App\Entity\Utilisateur;
use App\Form\SearchType;
use App\Repository\InternauteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AccueilController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/', name: 'app_accueil')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {

        //récupérer les 4 derniers prestataires inscrits
        $query = $entityManager->createQuery(
            'SELECT p, u FROM App\Entity\Utilisateur u JOIN u.prestataire p ORDER BY u.inscription ASC'
        )->setMaxResults(4);
        $prestataires = $query->getResult();

        //récupérer les prestataires favoris
        $prestataires_favoris = 0;

        if ($this->tokenStorage->getToken()) {
            $user = $this->tokenStorage->getToken()->getUser();
            $prestataires_favoris = $user->getInternaute()->getPrestatairesFavoris();
            if (count($prestataires_favoris) > 4) {
                $prestataires_favoris = $prestataires_favoris->slice(0, 4);
            }
        }


// catégorie du mois
        $categorie_mois = $entityManager->getRepository(CategorieDeServices::class)->findBy(['enAvant' => true]);

        if (count($categorie_mois) > 0) {
            $categorie_mois_image = $categorie_mois[0]->getImages();
        } else {
            $categorie_mois_image = null;
        }

        //formulaire de recherche
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);


        //récupération des données du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $search_prestataire = $data['prestataire'];
            $search_localite = $data['localite'];
            $search_categorie = $data['categorie'];
            $search_cp = $data['cp'];
            $search_commune = $data['commune'];


            $session = $request->getSession();
            $session->set('prestataire', $search_prestataire);
            $session->set('localite', $search_localite);
            $session->set('categorie', $search_categorie);
            $session->set('cp', $search_cp);
            $session->set('commune', $search_commune);

            return $this->redirectToRoute('prestataire_search', [
                'prestataire' => $search_prestataire,
                'localite' => $search_localite,
                'cp' => $search_cp,
                'commune' => $search_commune,
            ]);
        }

        //récupérer les catégories pour la nav
        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

        return $this->render('base.html.twig', [
            'prestataires' => $prestataires,
            'categories' => $categories,
            'searchForm' => $form->createView(),
            'prestatairesFavoris' => $prestataires_favoris,
            'categorie_mois' => $categorie_mois,
            'categorie_mois_image' => $categorie_mois_image

        ]);
    }
}
