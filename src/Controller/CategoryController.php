<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Hashtag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $suggestionsUsers = $this->getDoctrine()->getRepository(User::class)->SharedPaysAndTheme($idPays->getId(), $idTheme->getId());
        foreach ($suggestionsUsers as $suggestionsUsers) {
            $array[] = $this->getDoctrine()->getRepository(User::class)->findById($suggestionsUsers);
        }
        if (!isset($array)) {
            $array = true;
        }


        return $this->render('pages/bothCategories/show.html.twig', [
            'pays' => $idPays,
            'countries' => $countries,
            'theme' => $idTheme,
            'themes' => $themes,
            'suggestionsUsers' => $array
        ]);
    }

    /**
     * @Route("/tags/ajout/ajax/{label}", name="ajoutTag", methods={"POST"})
     * @return Response
     * @Route("/editTag/{id}", name="editTag")
     */
    public function addHashtag(string $label = null, EntityManagerInterface $manager, Hashtag $tag = null, Request $request): Response
    {
        // dd("fonction déclenchée");
        if (!$tag) {
            $tag = new Hashtag;
            $tag->setName(trim(strip_tags($label)));
            $manager->persist($tag);
            $manager->flush();
            $id = $tag->getId();
            return new JsonResponse(['id' => $id]);
        }
        if (isset($tag)) {

            $membres = $this->getDoctrine()->getRepository(User::class)->findAll();
            $countries =  $this->getDoctrine()->getRepository(Pays::class)
                ->findAll();
            $themes =  $this->getDoctrine()->getRepository(Theme::class)
                ->findAll();

            $form = $this->createForm(HashtagType::class, $tag);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $tag = $form->getData();
                $manager->flush();

                return $this->redirectToRoute('admin');

                $tag->setName(trim(strip_tags($label)));
                $manager->flush();
                return $this->redirectToRoute('admin');
            }
            return $this->render('pages/tag/ajoutTag.html.twig', [
                'formAddTag' => $form->createView(),
                'membres' => $membres,
                'countries' => $countries,
                'themes' => $themes,
                'tag' => $tag
            ]);
        }
    }
}
