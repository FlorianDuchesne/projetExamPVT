<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Entity\User;
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

        return $this->render('pages/theme/index.html.twig', [
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


        return $this->render('pages/pays/index.html.twig', [
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
            ->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $suggestionsUsers = $this->getDoctrine()->getRepository(User::class)->SharedPays($pays->getId());
        foreach ($suggestionsUsers as $suggestionsUsers) {
            $array[] = $this->getDoctrine()->getRepository(User::class)->findById($suggestionsUsers);
        }
        if (!isset($array)) {
            $array = true;
        }


        return $this->render('pages/pays/show.html.twig', [
            'pays' => $pays,
            'countries' => $countries,
            'themes' => $themes,
            'suggestionsUsers' => $array
        ]);
    }

    /**
     * @Route("/theme/{id}", name="theme_show")
     */
    public function themeDetail(Theme $theme)
    {

        $pays =  $this->getDoctrine()->getRepository(Pays::class)
            ->findBy([], ['libelle' => 'ASC'], 3);
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $suggestionsUsers = $this->getDoctrine()->getRepository(User::class)->SharedTheme($theme->getId());
        foreach ($suggestionsUsers as $suggestionsUsers) {
            $array[] = $this->getDoctrine()->getRepository(User::class)->findById($suggestionsUsers);
        }
        if (!isset($array)) {
            $array = true;
        }

        // dd($array);

        return $this->render('pages/theme/show.html.twig', [
            'pays' => $pays,
            'countries' => $countries,
            'theme' => $theme,
            'themes' => $themes,
            'suggestionsUsers' => $array
        ]);
    }

    /**
     * @Route("/pays/{idPays}/theme/{idTheme}", name="both_show")
     */
    public function bothCategoriesDetail(Pays $idPays, Theme $idTheme)
    {

        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();

        return $this->render('pages/bothCategories/show.html.twig', [
            'pays' => $idPays,
            'countries' => $countries,
            'theme' => $idTheme,
            'themes' => $themes
        ]);
    }
}
