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

        return $this->render('pages/user/list.html.twig', [
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

        return $this->render('pages/user/show.html.twig', [
            'user' => $user,
            'countries' => $countries,
            'themes' => $themes
        ]);
    }

    /**
     * @Route("/following/{id}", name="following")
     */
    public function follow(User $user, EntityManagerInterface $manager)
    {
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();

        $userFollowing = $this->getUser();

        $userFollowing->addFollowing($user);

        $manager->flush();

        return $this->render('pages/user/show.html.twig', [
            'user' => $user,
            'countries' => $countries,
            'themes' => $themes
        ]);
    }

    /**
     * @Route("/unfollowing/{id}", name="unfollowing")
     */
    public function unfollow(User $user, EntityManagerInterface $manager)
    {
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();

        $userFollowing = $this->getUser();

        $userFollowing->removeFollowing($user);

        $manager->flush();

        return $this->render('pages/user/show.html.twig', [
            'user' => $user,
            'countries' => $countries,
            'themes' => $themes
        ]);
    }

    /**
     * @Route("/followers/{id}", name="followers")
     */
    public function followers(User $user, EntityManagerInterface $manager)
    {
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();

        $followers = $user->getFollowers();
        // dd($followers);

        return $this->render('pages/user/list.html.twig', [
            'user' => $user,
            'membres' => $followers,
            'countries' => $countries,
            'themes' => $themes,
            'followers' => true
        ]);
    }

    /**
     * @Route("/follows/{id}", name="follows")
     */
    public function abonnements(User $user, EntityManagerInterface $manager)
    {
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();

        $following = $user->getFollowing();


        return $this->render('pages/user/list.html.twig', [
            'user' => $user,
            'membres' => $following,
            'countries' => $countries,
            'themes' => $themes,
            'following' => true
        ]);
    }
}
