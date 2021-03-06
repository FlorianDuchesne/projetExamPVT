<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Entity\User;
use App\Entity\Theme;
use App\Form\PaysType;
use App\Entity\Hashtag;
use App\Form\ThemeType;
use App\Form\HashtagType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function dashboard()
    {
        $membres = $this->getDoctrine()->getRepository(User::class)->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        $hashtags =  $this->getDoctrine()->getRepository(Hashtag::class)
            ->findAll();


        return $this->render('pages/admin/adminDashboard.html.twig', [
            'membres' => $membres,
            'countries' => $countries,
            'themes' => $themes,
            'hashtags' => $hashtags
        ]);
    }

    /**
     * @Route("/admin/ajoutPays", name="ajoutPays")
     * @Route("/admin/editPays/{id}", name="editPays")
     */
    public function ajoutPays(Pays $pays = null, Request $request)
    {
        $membres = $this->getDoctrine()->getRepository(User::class)->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();

        if (!$pays) {
            $pays = new Pays();
        }

        $form = $this->createForm(PaysType::class, $pays);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pays = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pays);
            $entityManager->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('pages/pays/ajoutPays.html.twig', [
            'formAddPays' => $form->createView(),
            'editMode' => $pays->getId() !== null,
            'pays' => $pays,
            'membres' => $membres,
            'countries' => $countries,
            'themes' => $themes
        ]);
    }

    /**
     * @Route("/admin/ajoutTheme", name="ajoutTheme")
     * @Route("/admin/editTheme/{id}", name="editTheme")
     */
    public function ajoutTheme(Theme $theme = null, Request $request)
    {
        $membres = $this->getDoctrine()->getRepository(User::class)->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();

        if (!$theme) {
            $theme = new Theme();
        }

        $form = $this->createForm(ThemeType::class, $theme);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $theme = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($theme);
            $entityManager->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('pages/theme/ajoutTheme.html.twig', [
            'formAddTheme' => $form->createView(),
            'theme' => $theme,
            'editMode' => $theme->getId() !== null,
            'membres' => $membres,
            'countries' => $countries,
            'themes' => $themes
        ]);
    }

    /**
     * @Route("/admin/deleteTag/{id}", name="deleteTag")
     */
    public function deleteTag(Hashtag $tag, EntityManagerInterface $manager)
    {
        $manager->remove($tag);

        $manager->flush();
        return $this->redirectToRoute('admin');
    }


    /**
     * @Route("/admin/deleteTheme/{id}", name="deleteTheme")
     */
    public function deleteTheme(Theme $theme, EntityManagerInterface $manager)
    {
        $manager->remove($theme);

        $manager->flush();
        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/admin/deletePays/{id}", name="deletePays")
     */
    public function deletePays(Pays $pays, EntityManagerInterface $manager)
    {
        $manager->remove($pays);

        $manager->flush();
        return $this->redirectToRoute('admin');
    }
}
