<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Entity\User;
use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class UserController extends AbstractController
{
    /**
     * @Route("/usersList", name="users")
     */
    public function index()
    {
        $membres = $this->getDoctrine()->getRepository(User::class)->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
        ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
        ->findAll();

        return $this->render('user/list.html.twig', [
            'membres' => $membres,
            'countries' => $countries,
            'themes' => $themes
        ]);
    }

            /**
     * @Route("/user/{id}", name="user_show")
     */
    public function show(User $user)
    {

        $countries =  $this->getDoctrine()->getRepository(Pays::class)
        ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
        ->findAll();

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'countries' => $countries,
            'themes' => $themes
        ]);
    }
}
