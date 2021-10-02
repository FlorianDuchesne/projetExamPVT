<?php

namespace App\Controller;

use DateTime;
use App\Entity\Like;
use App\Entity\Pays;
use App\Entity\User;
use App\Entity\Place;
use App\Entity\Theme;
use App\Entity\Article;
use App\Entity\Galerie;
use App\Entity\Hashtag;
use App\Form\ArticleType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/indexArticles/{id}", name="indexArticles")
     */
    public function indexAuteur(User $user): Response
    {
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        $articles = $this->getDoctrine()->getRepository(Article::class)->findByAuteurArticleAndStatut($user);
        $form = $this->createForm(CommentaireType::class);

        return $this->render('pages/article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'countries' => $countries,
            'themes' => $themes,
            'publications' => $articles,
            'user' => $user,
            'brouillon' => false,
            'commentaire' => $form
        ]);
    }

    /**
     * @Route("/indexbyTag/{id}", name="indexTag")
     */
    public function indexTag(Hashtag $tag)
    {
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        $articles = $this->getDoctrine()->getRepository(Article::class)->findByTag($tag->getId());
        $form = $this->createForm(CommentaireType::class);

        return $this->render('pages/article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'countries' => $countries,
            'themes' => $themes,
            'publications' => $articles,
            'brouillon' => false,
            'tag' => $tag,
            'commentaire' => $form
        ]);
    }

    /**
     * @Route("/indexbyLieu/{id}", name="indexLieu")
     */
    public function indexLieu(Place $place)
    {
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        $articles = $this->getDoctrine()->getRepository(Article::class)->findByLieu($place->getName());
        $form = $this->createForm(CommentaireType::class);

        return $this->render('pages/article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'countries' => $countries,
            'themes' => $themes,
            'publications' => $articles,
            'brouillon' => false,
            'lieu' => $place->getName(),
            'commentaire' => $form
        ]);
    }

    /**
     * @Route("/brouillons", name="brouillons")
     */
    public function brouillons(): Response
    {
        $user = $this->getUser();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        $articles = $this->getDoctrine()->getRepository(Article::class)->findBy(['auteurArticle' => $user, 'statut' => 0]);

        return $this->render('pages/article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'countries' => $countries,
            'themes' => $themes,
            'publications' => $articles,
            'user' => $user,
            'brouillon' => true
        ]);
    }


    /**
     * @Route("/article/{id}", name="article_show")
     */
    public function show(Article $article)
    {

        $form = $this->createForm(CommentaireType::class);

        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        if (($article->getStatut() == 0) && !($article->getAuteurArticle() == $this->getUser())) {
            return $this->redirectToRoute('home');
        }
        if ($article->getAuteurArticle() == $this->getUser()) {

            $commentaires = $this->getDoctrine()->getRepository(Commentaire::class)
                ->findByArticle($article);

            foreach ($commentaires as $commentaire) {

                $commentaire->setNewComment("0");
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
            }
        }

        return $this->render('pages/article/show.html.twig', [
            'publication' => $article,
            'countries' => $countries,
            'themes' => $themes,
            'commentaire' => $form,
        ]);
    }

    /**
     * @Route("/ajoutArticle/{idAuteur}", name="ajoutArticle")
     * @Route("/ajoutArticle/{idAuteur}/{idArticle}", name="modifierArticle")
     */
    public function addNewArticle(Request $request, User $idAuteur, UserInterface $userlogged, Article $idArticle = null): Response
    {

        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();

        // $form = $this->createForm(ArticleType::class);
        // $form->handleRequest($request);

        if (!$idArticle) {
            $article = new Article;
        } else {
            $article = $idArticle;
        }
        if (($idAuteur->getEmail() == $userlogged->getUsername())) {

            $form = $this->createForm(ArticleType::class, $article);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // encode the plain password

                // dd($form->get('hashtags'));

                $article = $form->getData();

                if (count($article->getGaleries()) > 5) {

                    $this->addFlash('maxImages', 'Il ne peut y avoir que cinq images maximum par article');

                    return $this->render('pages/article/newArticle.html.twig', [
                        'newArticleForm' => $form->createView(),
                        'countries' => $countries,
                        'themes' => $themes,
                        'editMode' => $article->getId() !== null
                    ]);
                }

                if (!empty($form->get('placeName')->getViewData())) {
                    // Si le champ est rempli…
                    $place = $this->getDoctrine()->getRepository(Place::class)
                        ->findOneByName($form->get('placeName')->getViewData());
                    if (!$place) {
                        $place = new Place;
                        $place->setName($form->get('placeName')->getViewData());
                        $place->setPlaceId($form->get('placeId')->getViewData());
                        $place->addArticle($article);
                        // dd("lieu pas trouvé");
                    } else {
                        $place->addArticle($article);
                    }
                } else {
                    $place = $article->getPlace();
                    if ($place) {
                        $place->removeArticle($article);
                    }
                };

                $article->setAuteurArticle($idAuteur);
                $article->setDateCreation(new DateTime);
                // $article->addHashtag()
                if ($form->get('brouillon')->isClicked()) {
                    $article->setStatut("0");
                } else {
                    $article->setStatut("1");
                }
                // $galerie = new Galerie;
                // $galerie = $form->get('galeries');

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($article);
                // $entityManager->persist($galerie);
                $entityManager->flush();
                // do anything else you need here, like send an email

                return $this->redirectToRoute('home');
            }

            return $this->render('pages/article/newArticle.html.twig', [
                'newArticleForm' => $form->createView(),
                'countries' => $countries,
                'themes' => $themes,
                'editMode' => $article->getId() !== null,
                'article' => $article,

            ]);
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/supprimerArticle/{idArticle}/{idAuteur}", name="supprimerArticle")
     */
    public function supprimerArticle(Article $idArticle, User $idAuteur, EntityManagerInterface $manager, UserInterface $userlogged)
    {

        // $countries =  $this->getDoctrine()->getRepository(Pays::class)
        // ->findAll();
        // $themes =  $this->getDoctrine()->getRepository(Theme::class)
        // ->findAll();
        // $articles = $this->getDoctrine()->getRepository(Article::class)->findByAuteurArticle($idAuteur);

        if (($idAuteur->getEmail() == $userlogged->getUsername()) || ($userlogged->getRoles() != ['ROLE_USER'])) {

            $manager->remove($idArticle);

            $manager->flush();
        }

        return $this->redirectToRoute('home');
        // return $this->render('pages/article/index.html.twig', [
        //     'controller_name' => 'ArticleController',
        //     'countries' => $countries,
        //     'themes' => $themes,
        //     'publications' => $articles,
        //     'user' => $idAuteur
        // ]);
    }

    /**
     * @Route("/likeArticle/{idArticle}/{idCommentaire}", name="likeArticle", methods={"POST"})
     * @Route("/likeCommentaire/{idCommentaire}", name="likeCommentaire", methods={"POST"})
     * @return Response
     */
    public function like(Article $idArticle = null, Commentaire $idCommentaire = null, EntityManagerInterface $manager)
    {

        $user = $this->getUser();
        if (!$idCommentaire) {
            $likeArticle = $manager->getRepository(Like::class)->findOneBy(['post' => $idArticle, 'user' => $user]);

            if (!($likeArticle)) {

                $like = new Like;
                $like->setPost($idArticle);
                $like->setUser($user);
                $manager->persist($like);
                $manager->flush();
            } else {

                $manager->remove($likeArticle);
                $manager->flush();
            }

            return new JsonResponse(['nbLikes' => count($idArticle->getLikes()), 'idArticle' => $idArticle->getId()]);
        } else {
            $likeCommentaire = $manager->getRepository(Like::class)->findOneBy(['commentaire' => $idCommentaire, 'user' => $user]);

            if (!($likeCommentaire)) {

                $like = new Like;
                $like->setCommentaire($idCommentaire);
                $like->setUser($user);
                $manager->persist($like);
                $manager->flush();
            } else {

                $manager->remove($likeCommentaire);
                $manager->flush();
            }

            return new JsonResponse(['nbLikes' => count($idCommentaire->getLikes()), 'idCommentaire' => $idCommentaire->getId()]);
        }

        // return $this->redirectToRoute('home');
    }

    /**
     * @Route("/article/addComment/{id}/", name="addComment")
     */
    public function addComment(Article $article, Request $request)
    {
        // dd($request);
        $user = $this->getUser();

        $form = $this->createForm(CommentaireType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $commentaire = $form->getData();

            $commentaire->setAuteur($user);
            $commentaire->setArticle($article);
            $commentaire->setDateTime(new DateTime);
            $commentaire->setNewComment(1);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commentaire);
            $entityManager->flush();

            $referer = $request->headers->get('referer');

            return new RedirectResponse($referer);
        }
    }

    /**
     * @Route("/article/deleteComm/{id}/", name="deleteComm")
     */
    public function deleteComm(Commentaire $commentaire, Request $request)
    {
        // dd($request);
        $user = $this->getUser();

        if (($commentaire->getAuteur() == $user) || ($user->getRoles() != ['ROLE_USER'])) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($commentaire);
            $entityManager->flush();

            $referer = $request->headers->get('referer');

            return new RedirectResponse($referer);
        }

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/Carte", name="carteArticles")
     */
    public function carteArticles()
    {
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();

        return $this->render('pages/article/indexCarte.html.twig', [
            'countries' => $countries,
            'themes' => $themes,
        ]);
    }

    /**
     * @Route("/searchPlaceIdArticles", name="searchPlaceIdArticles", methods={"POST"})
     */
    public function searchPlaceIdArticles(EntityManagerInterface $manager)
    {
        // Grâce à une fonction du repository de Place,
        // J'enregistre dans une variable les lieux dont la collection "membres" 
        //contient l'utilisateur paramétré
        $places = $manager->getRepository(Place::class)->findByArticles();
        $placesID = [];
        $placesName = [];
        $identifiantsPlace = [];
        // Pour chaque lieu enregistré dans la variable, 
        //j'ajoute son placeId, son nom et son statut dans des tableaux distincts,
        // puis je les renvoie au format Json
        foreach ($places as $place) {
            $placesID[] = $place->getPlaceId();
            $placesName[] = $place->getName();
            $identifiantsPlace[] = $place->getId();
        }
        return new JsonResponse([
            'placesId' => $placesID,
            'placesNames' => $placesName,
            'id' => $identifiantsPlace,
        ]);
    }
}
