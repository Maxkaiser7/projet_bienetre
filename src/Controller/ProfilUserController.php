<?php

namespace App\Controller;

use App\Entity\CategorieDeServices;
use App\Entity\Images;
use App\Entity\Prestataire;
use App\Form\ImageType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilUserController extends AbstractController
{
    #[Route('/profil/user/{id}', name: 'app_profil_user')]
    public function index(ManagerRegistry $doctrine, SluggerInterface $slugger, $id, Request $request, Prestataire $prestataire, EntityManagerInterface $entityManager, Security $security): Response
    {

        $repository = $entityManager->getRepository(Prestataire::class);
        $user = $repository->find($id);
        $image_current = $doctrine->getRepository(Images::class)->findOneBy(['prestataireId' => $id]);

        $image = new Images();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

        if ($form->isSubmitted() && $form->isValid()) {
            //suppression des anciennes photos si elles existent
            if ($image_current) {
                $query = $entityManager->createQuery('DELETE FROM App\Entity\Images i WHERE i.prestataireId = :id')
                    ->setParameter('id', $id);
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
            $image->setPrestataire($user);

            $entityManager->persist($image);
            $entityManager->flush();


            return $this->redirectToRoute('app_profil_user', [
                'id' => $id,
                'image' => $image,
                'prestataire' => $prestataire
            ]);
        }

        return $this->render('profil_user/profil_user.html.twig', [
            'user' => $user,
            'imageForm' => $form->createView(),
            'image' => $image,
            'image_current' => $image_current,
            'prestataire' => $prestataire,
            'categories' => $categories
        ]);
    }

    #[Route('/profil/user/{id}/edit', name: 'app_edit_user')]
    public function editUser($id, EntityManagerInterface $entityManager, Request $request)
    {

        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();

            return $this->redirectToRoute('app_profil_user', ['id' => $user->getId()]);
        }
        return $this->render('profil_user/edit_user.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories
        ]);
    }

}
