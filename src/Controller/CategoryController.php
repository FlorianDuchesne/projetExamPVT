<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Entity\Theme;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CategoryController extends AbstractController
{

    /**
     * @Route("/theme", name="theme")
     */
    public function indexTheme()
    {

        $themes =  $this->getDoctrine()->getRepository(Theme::class)
        ->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
        ->findAll();

        return $this->render('theme/index.html.twig', [
            'themes' => $themes,
            'countries' => $countries
        ]);
    }

    /**
     * @Route("/pays", name="pays")
     */
    public function indexPays(): Response
    {
        $pays =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
    

        return $this->render('pays/index.html.twig', [
            'pays' => $pays,
            'countries' => $pays,
            'themes' => $themes,
        ]);
    }

    /**
     * @Route("/pays/{id}", name="pays_show")
     */
    public function paysDetail(Pays $pays)
    {

        $themes =  $this->getDoctrine()->getRepository(Theme::class)
        ->findBy([], ['libelle' => 'ASC' ], 3);
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
        ->findAll();

        return $this->render('pays/show.html.twig', [
            'pays' => $pays,
            'countries' => $countries,
            'themes' => $themes
        ]);
    }

    /**
     * @Route("/theme/{id}", name="theme_show")
     */
    public function themeDetail(Theme $theme)
    {

        $pays =  $this->getDoctrine()->getRepository(Pays::class)
        ->findBy([], ['libelle' => 'ASC' ], 3);
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
        ->findBy([], ['libelle' => 'ASC' ], 3);
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
        ->findAll();

        return $this->render('theme/show.html.twig', [
            'pays' => $pays,
            'countries' => $countries,
            'theme' => $theme,
            'themes' => $themes
        ]);
    }

        /**
     * @Route("/pays/{idPays}/theme/{idTheme}", name="both_show")
     */
    public function bothCategoriesDetail(Pays $idPays, Theme $idTheme)
    {

        $themes =  $this->getDoctrine()->getRepository(Theme::class)
        ->findBy([], ['libelle' => 'ASC' ], 3);
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
        ->findAll();

        return $this->render('bothCategories/show.html.twig', [
            'pays' => $idPays,
            'countries' => $countries,
            'theme' => $idTheme,
            'themes' => $themes
        ]);
    }
}
