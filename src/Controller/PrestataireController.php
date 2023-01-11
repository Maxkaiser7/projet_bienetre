<?php

namespace App\Controller;

use App\Entity\CategorieDeServices;
use App\Entity\Images;
use App\Entity\Localite;
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

        //dump($result[0]);die;

        return $this->render('prestataire/prestataires.html.twig', [
            'proposer' => $result,
        ]);
    }

    #[Route('/prestataire_success', name: 'prestataire_success')]
    public function success(): Response
    {
        return $this->render('prestataire/prestataire_success.html.twig');
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

        return $this->render('prestataire/prestataire_form.html.twig', [
            'prestataireForm' => $form->createView()
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
        return $this->render('prestataire/prestataire_show.html.twig', [
            'prestataire' => $prestataire,
            'categorie' => $categorie

        ]);
    }

    #[Route('/prestataire/search/{search}', name: 'prestataire_search')]
    public function searchPrestataire(string $search, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Proposer::class);
        $query = $repository->createQueryBuilder('p')
            ->select('p, categorieDeServices, presta, images')
            ->join('p.categorieDeServices', 'categorieDeServices')
            ->join('p.prestataire', 'presta')
            ->join('presta.images', 'images')
            ->where("presta.nom LIKE :search OR categorieDeServices.nom LIKE :search ")
            ->setParameter("search", "%" . $search . "%")
            ->getQuery('');
        $result = $query->getResult();

     /*A voir : trouver par ville
      *    $repoLocalite = $entityManager->getRepository(Localite::class)->findBy([
            'Localite'=>$search
        ]);
        $localiteId = $repoLocalite[0]->getId();
        $repoUtilisateur = $entityManager->getRepository(Utilisateur::class)->findByLocaliteId($user);
*/

        return $this->render('prestataire/prestataire_search.html.twig', [
            'proposer' => $result
        ]);
    }
}
