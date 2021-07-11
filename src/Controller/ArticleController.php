<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Entity\Theme;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function index(): Response
    {
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
        ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
        ->findAll();

        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'countries' => $countries,
            'themes' => $themes
        ]);
    }

        /**
     * @Route("/article/{id}", name="article_show")
     */
    public function show(Article $article)
    {

        $countries =  $this->getDoctrine()->getRepository(Pays::class)
        ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
        ->findAll();

        return $this->render('article/show.html.twig', [
            'publication' => $article,
            'countries' => $countries,
            'themes' => $themes
        ]);
    }
}
