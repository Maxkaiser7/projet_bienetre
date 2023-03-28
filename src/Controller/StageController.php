<?php

namespace App\Controller;

use App\Entity\CategorieDeServices;
use App\Entity\Stage;
use App\Form\StagesType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class StageController extends AbstractController
{
    #[Route('/stage', name: 'app_stage')]
    public function index(EntityManagerInterface $entityManager, Security $security, PaginatorInterface $paginator, Request $request): Response
    {
        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);
        $user = $this->getUser();
        $prestataire_connecte = false;
        if ($user) {
            $prestataire_connecte = $user->getPrestataire();
        }
        if ($user !== null) {
            $prestataire_connecte = true;
        }
        $aujourdhui = new \DateTime();
        $stages = $entityManager->getRepository(Stage::class)->findAll();

        //affichage jusque la date actuelle
        $qb = $entityManager->createQuery('SELECT s FROM App\Entity\Stage s WHERE s.affichageJusque >= :aujourdhui ')->setParameter('aujourdhui', $aujourdhui);
        $stages_afficher = $qb->getResult();

        //pagination
        $pagination = $paginator->paginate($stages_afficher, $request->query->getInt('page', 1),
            5);
        return $this->render('stage/index.html.twig', [
            'categories' => $categories,
            'prestataire_connecte' => $prestataire_connecte,
            'stages' => $stages_afficher,
            'pagination' => $pagination
        ]);
    }

    #[Route('/stage/ajouter', name: 'app_stage_ajouter')]
    public function ajouterStage(EntityManagerInterface $entityManager, Request $request): Response
    {
        $stage = new Stage();
        $form = $this->createForm(StagesType::class, $stage);
        $form->handleRequest($request);

        $user = $this->getUser();
        $prestataire = $user->getPrestataire();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $stage->setPrestataire($prestataire);
            $affichageDebut = new \DateTime();
            $stage->setAffichageDe($affichageDebut);
            $affichageFin = clone $affichageDebut;
            $affichageFin->add(new \DateInterval('P14D'));
            $stage->setAffichageJusque($affichageFin);
            $entityManager->persist($stage);
            $entityManager->flush();
            return $this->redirectToRoute('app_stage');
        }
        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

        return $this->render('stage/stage_ajouter.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    #[Route('/stage/show/{nom}', name: 'app_stage_show')]
    public function showStage(EntityManagerInterface $entityManager, $nom)
    {
        $stage = $entityManager->getRepository(Stage::class)->findBy(['nom' => $nom]);
        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

        $prestataire = $stage[0]->getPrestataire();
        $user = $this->getUser();
        //if ($prestataire === $user)
            return $this->render('stage/stage_show.html.twig', [
                'categories' => $categories,
                'stage' => $stage,
                'prestataire' => $prestataire,
                'user' => $user
            ]);
    }

    #[Route('/stage/edit/{id}', name: 'app_stage_edit')]
    public function editStage($id, Request $request, EntityManagerInterface $entityManager)
    {
        $stage = $entityManager->getRepository(Stage::class)->findBy(['id' => $id]);
        $stage = $stage[0];
        $form = $this->createForm(StagesType::class, $stage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_stage_show', ['id' => $id]);
        }
        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);


        return $this->render('stage/edit.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
        ]);
    }
}
