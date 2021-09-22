<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Article;
use App\Entity\Hashtag;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(User $user = null)
    {

        $form = $this->createForm(CommentaireType::class);

        // Symfony nous permet d'identifier avec "getUser" si le visiteur est un utilisateur identifié ou non.
        $user = $this->getUser();

        // Si la variable $user est vide, on redirige vers la route nommée "homeVisitor".
        if (empty($user)) {
            return $this->redirectToRoute('homeVisitor');
        }

        // On rassemble les données nécessaires à la vue grâce aux repositories des entités User, Pays et Theme.
        // C'est Doctrine, l'ORM de Symfony qui nous permet d'accéder aux repositories des différentes classes.
        // On accède aux méthodes de chaque repository, ce qui nous permet d'utiliser la méthode findAll.
        // $users =  $this->getDoctrine()->getRepository(User::class)
        //     ->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        $following = $user->getFollowing();
        $hashtags =  $this->getDoctrine()->getRepository(Hashtag::class)
            ->findAll();

        $articles = $this->getDoctrine()->getRepository(Article::class)->findByFollow($following, $user);
        $tendances = $this->getDoctrine()->getRepository(Hashtag::class)->findByPopularity();
        $suggestions = $this->getDoctrine()->getRepository(User::class)->findByLikes($user);

        // On renvoie la vue twig avec les données instanciées dans la fonction.
        return $this->render('pages/home/indexBEM.html.twig', [
            // 'users' => $users,
            'countries' => $countries,
            'themes' => $themes,
            'publications' => $articles,
            'commentaire' => $form,
            'tendances' => $tendances,
            'suggestions' => $suggestions,
            'hashtags' => $hashtags
        ]);
    }

    /**
     * @Route("/homeVisitor", name="homeVisitor")
     */
    public function indexVisitor(Session $session = null)
    {
        // dd($session);
        // cette partie là ne marche pas très bien…
        if (($session->get('new')) == null) {
            $session->set('new', true);
        } else {
            $session->set('new', false);
        }
        // dd($session);

        $newVisitor = $session->get('new');

        // $users =  $this->getDoctrine()->getRepository(User::class)
        //     ->findAll();
        $hashtags =  $this->getDoctrine()->getRepository(Hashtag::class)
            ->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        $articles =  $this->getDoctrine()->getRepository(Article::class)
            ->findByStatut(1);
        return $this->render('pages/home/indexVisitor.html.twig', [
            // 'users' => $users,
            'countries' => $countries,
            'themes' => $themes,
            'articles' => $articles,
            'newVisitor' => $newVisitor,
            'hashtags' => $hashtags
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request)
    {
        // On récupère la recherche dans la requête et on l'enregistre dans une variable
        $search = $request->get('search');
        // On actionne dans le repository d'Article une recherche en fonction de la requête
        $resultArticles = $this->getDoctrine()->getRepository(Article::class)->findBySearch($request);
        // On actionne dans les repositories de User, Hashtag, Pays et Theme une recherche en fonction de la variable search
        $resultUsers = $this->getDoctrine()->getRepository(User::class)->findBySearch($search);
        $resultHashtags = $this->getDoctrine()->getRepository(Hashtag::class)->findBySearch($search);
        $resultPays = $this->getDoctrine()->getRepository(Pays::class)->findBySearch($search);
        $resultTheme = $this->getDoctrine()->getRepository(Theme::class)->findBySearch($search);

        // On stocke les résultats dans un tableau
        $results = [$resultArticles, $resultUsers, $resultHashtags, $resultPays, $resultTheme];

        $users =  $this->getDoctrine()->getRepository(User::class)
            ->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        $tags =  $this->getDoctrine()->getRepository(Hashtag::class)
            ->findAll();


        return $this->render('pages/home/search.html.twig', [
            'users' => $users,
            'countries' => $countries,
            'themes' => $themes,
            'hashtags' => $tags,
            'results' => $results,
            'search' => $search
        ]);
    }

    /**
     * @Route("/makeSearch", name="makeSearch")
     */
    public function makeSearch()
    {
        $users =  $this->getDoctrine()->getRepository(User::class)
            ->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        $hashtags =  $this->getDoctrine()->getRepository(Hashtag::class)
            ->findAll();

        return $this->render('pages/home/makeSearch.html.twig', [
            'users' => $users,
            'countries' => $countries,
            'themes' => $themes,
            'hashtags' => $hashtags
        ]);
    }

    /**
     * @Route("/aPropos", name="aPropos")
     */
    public function aPropos()
    {


        $users =  $this->getDoctrine()->getRepository(User::class)
            ->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        return $this->render('pages/home/apropos.html.twig', [
            'users' => $users,
            'countries' => $countries,
            'themes' => $themes,
        ]);
    }
}
