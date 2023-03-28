<?php

namespace App\Controller;

use App\Entity\CategorieDeServices;
use App\Entity\Commentaire;
use App\Entity\Favori;
use App\Entity\Images;
use App\Entity\Internaute;
use App\Entity\Localite;
use App\Entity\Prestataire;
use App\Entity\Promotion;
use App\Entity\Proposer;
use App\Entity\Utilisateur;
use App\Form\CommentaireType;
use App\Form\GalerieType;
use App\Form\ImageCategorieType;
use App\Form\LikeType;
use App\Form\PrestataireType;
use App\Form\PromotionType;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Optios\BelgianRegionZip\BelgianRegionZipHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;

use Symfony\Component\HttpFoundation\Request;

class PrestataireController extends AbstractController
{

    #[Route('/prestataire', name: 'app_prestataire')]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        //récupérer les prestataires
        $repository = $entityManager->getRepository(Prestataire::class);
        $prestataires = $repository->findAll();

        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);

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

        //pagination
        $pagination = $paginator->paginate($result, $request->query->getInt('page', 1),
            5);
        return $this->render('prestataire/prestataires.html.twig', [
            'proposer' => $result,
            'categories' => $categories,
            'searchForm' => $form->createView(),
            'pagination' => $pagination
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
    public function showPrestataire(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $prestataire = $entityManager->getRepository(Prestataire::class)->find($id);
        /*$query = $entityManager->createQuery(
            'SELECT categorieDeServices.id c FROM App:Proposer proposer JOIN proposer.categorieDeServices categorieDeServices
             WHERE proposer.prestataire = :prestataireId')
            ->setParameter('prestataireId', $id);
*/
        //map google

        $user = $prestataire->getUtilisateur();
        //dump($user== $this->getUser());die;
        $codePostal = $user->getCodePostal();
        $commune = $user->getCommune();
        $localite = $user->getLocalite();
        $rue = $user->getAdresseRue();
        $n = $user->getAdresseN();

        $apiKey = 'AIzaSyCdmFEs3IVycKJt6S7Y1LpMc0sqqCdadXI';
// Combiner les informations pour former une adresse complète
// Récupérer l'adresse complète de l'utilisateur
        $adresseComplete = sprintf('%s, %s %s %s %s %s', $localite, $codePostal, $commune, 'rue', $rue, $n);

// Construire l'URL de l'API Google Maps
        $adresseEncodee = urlencode($adresseComplete);
        $apiUrl = sprintf('https://maps.googleapis.com/maps/api/geocode/json?address=%s&key=%s', $adresseEncodee, $apiKey);

// Appeler l'API Google Maps pour récupérer les coordonnées géographiques
        //$response = file_get_contents($apiUrl);
        //$resultats = json_decode($response, true);
// Vérifier si des résultats ont été retournés par l'API Google Maps
        if (isset($resultats['results']) && !empty($resultats['results'])) {
            // Extraire les coordonnées géographiques à partir des résultats
            $coordonnees = $resultats['results'][0]['geometry']['location'];
            $iframe = sprintf('<iframe src="https://www.google.com/maps/embed/v1/place?q=%s,%s&key=%s"></iframe>', $coordonnees['lat'], $coordonnees['lng'], $apiKey);
            // Utiliser les coordonnées géographiques pour afficher une carte Google Maps
        } else {
            // Afficher un message d'erreur si aucun résultat n'a été trouvé
            $messageErreur = "L'adresse du prestataire n'a pas pu être trouvée sur Google Maps.";

        }
        // Utiliser les coordonnées géographiques pour afficher une carte Google Maps

        //systeme d'ajout de like, essayer de le faire avec un toggle sans formulaire
        $form = $this->createForm(LikeType::class);
        $form->handleRequest($request);
        //formulaire like
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($user === null) {
                $this->redirectToRoute('app_login');
            }
            $internaute = $user->getInternaute();
            $internaute->addPrestatairesFavori($prestataire);
            $prestataire->addInternautesFavoris($internaute);
        }

        $result = $entityManager->getRepository(Proposer::class)->findCategByPrestataire($id);
        $categorieId = $result[0]['c'];
        $categorie = $entityManager->getRepository(CategorieDeServices::class)->find($categorieId);

        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);
        $internauteId = $prestataire->getUtilisateur();
        //trouver la valeur du like
        $favoris = 0;

        if (count($prestataire->getInternautesFavoris()) > 0) {
            $favoris = count($prestataire->getInternautesFavoris());

        }
        //vérifier si l'internaute aime déjà ce prestataire
        $display_like = true;

        if ($this->getUser()) {
            $internaute = $this->getUser()->getInternaute();
            $display_like = true;
            if (!$internaute->getPrestatairesFavoris()->contains($prestataire)) {
                $display_like = false;
            }
        }


        //ajout de commentaire
        $commentaire = new Commentaire();
        $form_commentaire = $this->createForm(CommentaireType::class, $commentaire);
        $form_commentaire->handleRequest($request);

        if ($form_commentaire->isSubmitted() && $form_commentaire->isValid()) {

            $data = $form_commentaire->getData();
            $commentaire->setInternaute($internaute);
            $commentaire->setPrestataire($prestataire);
            $commentaire->setEncodage(new \DateTime());
            $entityManager->persist($commentaire);
            $entityManager->flush();

        }
        $stages = $prestataire->getStages();
        //affichage de commentaire
        $commentaires = $prestataire->getCommentaires();

        //ajout d'images a la galerie
        $image = new Images();
        $form = $this->createForm(GalerieType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $file = $form['Image']->getData();
            $uploads_directory = $this->getParameter('images_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $image->setImage($filename);
            $image->setPrestataire($prestataire);
            $image->setOrdre(1);
            $entityManager->persist($image);
            $entityManager->flush();

        }
        //trouver prestatairs similaires
        $prestataires_simi = $entityManager->getRepository(Proposer::class)->findBy(['categorieDeServices' => $categorie]);
        array_shift($prestataires_simi);
        /*
                //télécharger le pdf de promotion
                $promotions = $entityManager->getRepository(Promotion::class)->findBy(['prestataire' => $prestataire]);
                $pdfPath = $this->getParameter('pdf_directory');
                $response = new Response();
                $promotionsDownloads = [];
                for($i = 0; $i<count($promotions); $i++){
                    $disposition[$i] = $response->headers->makeDisposition(
                        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                        $promotions[$i]->getDocumentPdf()
                    );
                    $response->headers->set('Content-Disposition', $disposition[$i]);
                    $response->headers->set('Content-Type', 'application/pdf');
                    $pdfContent = file_get_contents($pdfPath . '/' . $promotions[$i]->getDocumentPdf());
                    $response->setContent($pdfContent);
                    $promotionDownloads[] = [
                        'name' => $promotions[$i]->getDocumentPdf(),
                        'url' => $this->generateUrl('download_promotion_pdf', ['id' => $promotions[$i]->getId()])
                    ];
                }*/
        $promotions = $entityManager->getRepository(Promotion::class)->findBy(['prestataire' => $prestataire]);
        //formulaire de recherche
        $searchform = $this->createForm(SearchType::class);
        $searchform->handleRequest($request);
        if ($searchform->isSubmitted() && $searchform->isValid()) {
            $data = $searchform->getData();
            $prestataire = $data['prestataire'];

            $localite = $data['localite'];
            $categorie = $data['categorie'];
            $cp = $data['cp'];
            $commune = $data['commune'];


            return $this->redirectToRoute('prestataire_search', [
                'prestataire' => $prestataire,
                'localite' => $localite,
                'cp' => $cp,
                'commune' => $commune,
            ]);
        }
        return $this->render('prestataire/prestataire_show.html.twig', [
            'prestataire' => $prestataire,
            'categorie' => $categorie,
            'categories' => $categories,
            'likeForm' => $form->createView(),
            'favoris' => $favoris,
            'display_like' => $display_like,
            'form_commentaire' => $form_commentaire->createView(),
            'commentaires' => $commentaires,
            'stages' => $stages,
            'prestataires_simi' => $prestataires_simi,
            'messageErreur' => $messageErreur,
            'adresseComplete' => $adresseComplete,
            'user' => $user,
            'form' => $form->createView(),
            'searchForm' => $searchform->createView(),
            'promotions' => $promotions,

        ]);
    }


    #[Route('/promotion/download/{id}/{idPromo}', name:'download_promotion_pdf' )]
    public function downloadPdf($id, EntityManagerInterface $entityManager)
    {
        $prestataire = $entityManager->getRepository(Prestataire::class)->find($id);

        //télécharger le pdf de promotion
        $promotions = $entityManager->getRepository(Promotion::class)->findBy(['prestataire' => $prestataire]);
        $pdfPath = $this->getParameter('pdf_directory');
        $response = new Response();
        $promotionsDownloads = [];
        for ($i = 0; $i < count($promotions); $i++) {
            $disposition[$i] = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $promotions[$i]->getDocumentPdf()
            );
            $response->headers->set('Content-Disposition', $disposition[$i]);
            $response->headers->set('Content-Type', 'application/pdf');
            $pdfContent = file_get_contents($pdfPath . '/' . $promotions[$i]->getDocumentPdf());
            $response->setContent($pdfContent);

            return $response;
        }

    }

    #[Route('/prestataire/show/{id}/like/{userId}', name: 'prestataire_like')]
    public function likePrestataire(EntityManagerInterface $entityManager, Request $request, int $id, int $userId)
    {


        $value = $request->get('like-btn');

        $repo = $entityManager->getRepository(Utilisateur::class);
        $user = $repo->find($userId);
        $repo = $entityManager->getRepository(Internaute::class);
        $internaute = $repo->find($userId);

        $prestataire = $entityManager->getRepository(Prestataire::class)->find($id);
        if ($value === 'like') {
            $internaute->addPrestatairesFavori($prestataire);
            $prestataire->addInternautesFavori($internaute);
        } else {
            $internaute->removePrestatairesFavori($prestataire);
            $prestataire->removeInternautesFavori($internaute);
        }

        $entityManager->flush();
        return $this->forward('App\Controller\PrestataireController::showPrestataire', [
            'id' => $id
        ]);


        /*
                //$prestataire = $entityManager->getRepository(Prestataire::class)->find($id);
                $result = $entityManager->getRepository(Proposer::class)->findCategByPrestataire($id);
                $categorieId = $result[0]['c'];
                $categorie = $entityManager->getRepository(CategorieDeServices::class)->find($categorieId);
                $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);
                return $this->render('prestataire/prestataire_show.html.twig', [
                    'prestataire' => $prestataire,
                    'categorie' => $categorie,
                    'categories' => $categories,
                    'value' => $value,
                ]);
        */
    }

    #[Route('/prestataire/search/', name: 'prestataire_search')]
    public function searchPrestataire(EntityManagerInterface $entityManager, Request $request): Response
    {
        $repository = $entityManager->getRepository(Proposer::class);

        //récupération des données envoyées via le formulaire de recherche
        $session = $request->getSession();
        $prestataire = $session->get('prestataire');
        $localite = $session->get('localite');
        $categorie = $session->get('categorie');
        $cp = $session->get('cp');
        $commune = $session->get('commune');

        $session->clear();
        //trier les données
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
        if ($localite && !$cp) {
            $query->andWhere('localite.Localite LIKE :localite')
                ->setParameter("localite", "%" . $localite . "%");
        }
        if ($categorie) {
            $query->andWhere('categorieDeServices.nom LIKE :categorie')
                ->setParameter("categorie", "%" . $categorie . "%");

        }
        if ($commune) {
            $query->andWhere('commune.commune LIKE :commune')
                ->setParameter("commune", "%" . $commune . "%");

            dump($commune);
        }
        if ($cp) {
            $query->andWhere('codePostal.codePostal LIKE :cp')
                ->setParameter("cp", $cp->getCodePostal());
            dump($cp->getCodePostal());

        }
        $query = $query->getQuery();
        $result = $query->getResult();


        $categories = $entityManager->getRepository(CategorieDeServices::class)->findBy(['valide' => 1]);
        //formulaire de recherche
        $searchform = $this->createForm(SearchType::class);
        $searchform->handleRequest($request);
        if ($searchform->isSubmitted() && $searchform->isValid()) {

            $data = $searchform->getData();
            $prestataire = $data['prestataire'];

            $localite = $data['localite'];
            $categorie = $data['categorie'];
            $cp = $data['cp'];
            $commune = $data['commune'];

            return $this->redirectToRoute('prestataire_search', [
                'prestataire' => $prestataire,
                'localite' => $localite,
                'cp' => $cp,
                'commune' => $commune,
            ]);
        }


        return $this->render('prestataire/prestataire_search.html.twig', [
            'proposer' => $result,
            'categories' => $categories,
            'searchForm' => $searchform->createView()
        ]);
    }
}
