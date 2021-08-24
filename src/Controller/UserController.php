<?php

namespace App\Controller;

use DateTime;
use App\Entity\Pays;
use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Message;
use App\Form\MessageType;
use App\Form\ShortMessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/messagerie/{id}", name="messagerie")
     */
    public function messagerieIndex(User $user)
    {
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();

        $messages = $this->getDoctrine()->getRepository(Message::class)
            ->findByUser($user);
        $correspondants =  $this->getDoctrine()->getRepository(User::class)
            ->findByMessages($user);


        return $this->render('pages/user/messagerie.html.twig', [
            'user' => $user,
            'countries' => $countries,
            'themes' => $themes,
            'messages' => $messages,
            'correspondants' => $correspondants,
            // 'messagesSend' => $messagesSend
        ]);
    }

    /**
     * @Route("/messagerieShow/{id}", name="messagerieShow")
     */
    public function messagerieShow(User $user, Request $request)
    {
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        $messages = $this->getDoctrine()->getRepository(Message::class)
            ->findByUser($this->getUser());
        $messagesUser = $this->getDoctrine()->getRepository(Message::class)
            ->findByUser($user);
        $correspondants =  $this->getDoctrine()->getRepository(User::class)
            ->findByMessages($this->getUser());

        $form = $this->createForm(ShortMessageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $message = $form->getData();

            $message->setSend($this->getUser());
            $message->setReceived($user);

            $message->setDateTime(new DateTime);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            // $entityManager->persist($galerie);
            $entityManager->flush();
        }

        return $this->render('pages/user/messagerie.html.twig', [
            'user' => $this->getUser(),
            'correspondantActuel' => $user,
            'countries' => $countries,
            'themes' => $themes,
            'messages' => $messages,
            'messagesUser' => $messagesUser,
            'correspondants' => $correspondants,
            'answerForm' => $form->createView(),
            // 'messagesSend' => $messagesSend
        ]);
    }

    /**
     * @Route("/newMessage/{id}", name="newMessage")
     */
    public function newMessage(User $user, Request $request)
    {
        $messages = $this->getDoctrine()->getRepository(Message::class)
            ->findByUser($user);

        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        $correspondants =  $this->getDoctrine()->getRepository(User::class)
            ->findByMessages($user);

        $form = $this->createForm(MessageType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $message = $form->getData();

            $message->setSend($user);
            $message->setDateTime(new DateTime);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            // $entityManager->persist($galerie);
            $entityManager->flush();
        }


        return $this->render('pages/user/messagerie.html.twig', [
            'user' => $user,
            'countries' => $countries,
            'themes' => $themes,
            'newMessageForm' => $form->createView(),
            'messages' => $messages,
            'correspondants' => $correspondants,

            // 'messagesSend' => $messagesSend
        ]);
    }
}
