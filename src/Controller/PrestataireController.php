<?php

namespace App\Controller;

use App\Entity\CategorieDeServices;
use App\Entity\Favori;
use App\Entity\Images;
use App\Entity\Internaute;
use App\Entity\Localite;
use App\Entity\Prestataire;
use App\Entity\Proposer;
use App\Entity\Utilisateur;
use App\Form\PrestataireType;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PrestataireController extends AbstractController
{

    #[Route('/prestataire', name: 'app_prestataire')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        //récupérer les prestataires
        $repository = $entityManager->getRepository(Prestataire::class);
        $prestataires = $repository->findAll();

        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

        /*$query = $entityManager->createQuery(
            'SELECT categorieDeServices.id c, prestataire.id presta FROM App:Proposer p JOIN p.categorieDeServices categorieDeServices JOIN p.prestataire prestataire'
        );
        $result = $query->getResult();
*/
        $repository = $entityManager->getRepository(Proposer::class);

        $query = $repository->createQueryBuilder('p')
            ->select('p, categorieDeServices, presta, images')
            ->join('p.categorieDeServices', 'categorieDeServices')
            ->join('p.prestataire', 'presta')
            ->join('presta.images', 'images')
            ->getQuery('');
        $result = $query->getResult();

        //formulaire de recherche
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $prestataire = $data['prestataire'];
            $localite = $data['localite'];
            $categorie = $data['categorie'];
            $cp = $data['cp'];
            $commune = $data['commune'];


            return $this->redirectToRoute('prestataire_search', [
                'prestataire' => $prestataire,
                'localite' => $localite,
                'cp' => $cp,
                'commune' => $commune
            ]);
        }

        return $this->render('prestataire/prestataires.html.twig', [
            'proposer' => $result,
            'categories' => $categories,
            'searchForm' => $form->createView()
        ]);
    }

    #[Route('/prestataire_success', name: 'prestataire_success')]
    public function success(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

        return $this->render('prestataire/prestataire_success.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/prestataire/form/{id}', name: 'app_prestataire_form')]
    public function prestataireForm(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $prestataire = new Prestataire();
        $proposer = new Proposer();

        $form = $this->createForm(PrestataireType::class, $prestataire);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            //Donner à l'utilisateur le statut de prestataire
            $repository = $entityManager->getRepository(Utilisateur::class);
            $utilisateur = $repository->find($id);
            $utilisateur->setPrestataire($prestataire);
            $proposer->setPrestataire($prestataire);
            $prestataireId = $prestataire->getId();
            $categorie = $form->get('categorieDeServices')->getData();
            $proposer->setCategorieDeServices($categorie[0]);
            $entityManager->persist($proposer);
            $entityManager->persist($prestataire);
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('prestataire_success');
        }
        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);


        return $this->render('prestataire/prestataire_form.html.twig', [
            'prestataireForm' => $form->createView(),
            'categories' => $categories
        ]);
    }

    #[Route('/prestataire/show/{id}', name: 'prestataire_show')]
    public function showPrestataire(int $id, EntityManagerInterface $entityManager): Response
    {
        $prestataire = $entityManager->getRepository(Prestataire::class)->find($id);
        /*$query = $entityManager->createQuery(
            'SELECT categorieDeServices.id c FROM App:Proposer proposer JOIN proposer.categorieDeServices categorieDeServices
             WHERE proposer.prestataire = :prestataireId')
            ->setParameter('prestataireId', $id);
*/

        $result = $entityManager->getRepository(Proposer::class)->findCategByPrestataire($id);
        $categorieId = $result[0]['c'];
        $categorie = $entityManager->getRepository(CategorieDeServices::class)->find($categorieId);

        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);
        $internauteId = $prestataire->getUtilisateur();
        //trouver la valeur du like

        return $this->render('prestataire/prestataire_show.html.twig', [
            'prestataire' => $prestataire,
            'categorie' => $categorie,
            'categories' => $categories
        ]);
    }

    #[Route('/prestataire/show/{id}/like/{userId}', name: 'prestataire_like')]
    public function likePrestataire(EntityManagerInterface $entityManager, Request $request, int $id, int $userId)
    {

        $form = $this->createForm(Internaute::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $value = $data['like-btn'];
            dump($value);die;
        }
        /*
        $value = $request->get('like-btn');

        $repo = $entityManager->getRepository(Utilisateur::class);
        $user = $repo->find($userId);
        $repo = $entityManager->getRepository(Internaute::class);
        $internaute = $repo->find($userId);

        $prestataire = $entityManager->getRepository(Prestataire::class)->find($id);
        if($value === 'like'){
            $internaute->addPrestatairesFavori($prestataire);
        } else {
            $internaute->removePrestatairesFavori($prestataire);
        }
        $entityManager->flush();
*/


        //$prestataire = $entityManager->getRepository(Prestataire::class)->find($id);
        $result = $entityManager->getRepository(Proposer::class)->findCategByPrestataire($id);
        $categorieId = $result[0]['c'];
        $categorie = $entityManager->getRepository(CategorieDeServices::class)->find($categorieId);
        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);
        return $this->render('prestataire/prestataire_show.html.twig', [
            'prestataire' => $prestataire,
            'categorie' => $categorie,
            'categories' => $categories,
            'value' => $value
        ]);
    }

    #[Route('/prestataire/search/', name: 'prestataire_search')]
    public function searchPrestataire(EntityManagerInterface $entityManager, Request $request): Response
    {
        $repository = $entityManager->getRepository(Proposer::class);

        $session = $request->getSession();
        $prestataire = $session->get('prestataire');
        $localite = $session->get('localite');
        $categorie = $session->get('categorie');
        $cp = $session->get('cp');
        $commune = $session->get('commune');
        $query = $repository->createQueryBuilder('p')
            ->select('p, prestataire, localite, utilisateur, categorieDeServices, codePostal')
            ->join('p.categorieDeServices', 'categorieDeServices')
            ->join('p.prestataire', 'prestataire')
            ->join('prestataire.utilisateur', 'utilisateur')
            ->join('utilisateur.localite', 'localite')
            ->join('utilisateur.commune', 'commune')
            ->join('utilisateur.codePostal', 'codePostal');
        if ($prestataire) {
            $query->andWhere('prestataire.nom LIKE :prestataire')
                ->setParameter("prestataire", "%" . $prestataire . "%");
        }
        if ($localite) {
            $query->andWhere('localite.Localite LIKE :localite')
                ->setParameter("localite", "%" . $localite . "%");
        }
        if ($categorie) {
            $query->andWhere('categorieDeServices.nom LIKE :categorie')
                ->setParameter("categorie", "%" . $categorie . "%");
        }
        if ($commune) {
            $query->andWhere('commune.commune LIKE :commune')
                ->setParameter("cp", "%" . $commune . "%");
        }
        if ($cp) {
            $query->andWhere('codePostal.codePostal LIKE :cp')
                ->setParameter("cp", $cp);
        }
        $query = $query->getQuery();
        $result = $query->getResult();

        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

        return $this->render('prestataire/prestataire_search.html.twig', [
            'proposer' => $result,
            'categories' => $categories
        ]);
    }
}
