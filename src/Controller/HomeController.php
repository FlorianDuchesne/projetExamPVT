<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Article;
use App\Entity\Hashtag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(User $user = null)
    {
        // Symfony nous permet d'identifier avec "getUser" si le visiteur est un utilisateur identifié ou non.
        $user = $this->getUser();

        // Si la variable $user est vide, on redirige vers la route nommée "homeVisitor".
        if (empty($user)) {
            return $this->redirectToRoute('homeVisitor');
        }


        // On rassemble les données nécessaires à la vue grâce aux repositories des entités User, Pays et Theme.
        // C'est Doctrine, l'ORM de Symfony qui nous permet d'accéder aux repositories des différentes classes.
        // On accède aux méthodes de chaque repository, ce qui nous permet d'utiliser la méthode findAll.
        $users =  $this->getDoctrine()->getRepository(User::class)
            ->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        $following = $user->getFollowing();

        $articles = $this->getDoctrine()->getRepository(Article::class)->findByFollow($following, $user);


        // On renvoie la vue twig avec les données instanciées dans la fonction.
        return $this->render('pages/home/indexBEM.html.twig', [
            'users' => $users,
            'countries' => $countries,
            'themes' => $themes,
            'publications' => $articles
        ]);
    }

    /**
     * @Route("/homeVisitor", name="homeVisitor")
     */
    public function indexVisitor()
    {
        $users =  $this->getDoctrine()->getRepository(User::class)
            ->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        $articles =  $this->getDoctrine()->getRepository(Article::class)
            ->findByStatut(1);
        return $this->render('pages/home/indexVisitor.html.twig', [
            'users' => $users,
            'countries' => $countries,
            'themes' => $themes,
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request)
    {
        // dd($request);
        $search = $request->get('search');
        // dd($search);
        $resultArticles = $this->getDoctrine()->getRepository(Article::class)->findBySearch($request);
        // dd($resultArticles);
        $resultUsers = $this->getDoctrine()->getRepository(User::class)->findBySearch($search);
        $resultHashtags = $this->getDoctrine()->getRepository(Hashtag::class)->findBySearch($search);

        $results = [$resultArticles, $resultUsers, $resultHashtags];

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
}
