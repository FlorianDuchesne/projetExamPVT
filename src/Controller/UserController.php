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
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/messagerie/{id}", name="messagerieShow")
     * @Route("/messagerie/index", name="messagerie")
     */
    public function messagerie(User $correspondant = null, Request $request)
    {

        $user = $this->getUser();

        $messages = $this->getDoctrine()->getRepository(Message::class)
            ->findByUser($user);
        $correspondants =  $this->getDoctrine()->getRepository(User::class)
            ->findByMessages($user);

        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();

        if (isset($correspondant)) {
            $messagesUser = $this->getDoctrine()->getRepository(Message::class)
                ->findByUser($correspondant);

            $form = $this->createForm(ShortMessageType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $message = $form->getData();

                $message->setSend($this->getUser());
                $message->setReceived($correspondant);
                $message->setDateTime(new DateTime);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($message);
                $entityManager->flush();

                // return new JsonResponse(['id' => $correspondant->getId()]);

                return $this->redirectToRoute('messagerieShow', ['id' => $correspondant->getId()]);
            }

            // return new JsonResponse([
            //     'correspondantActuel' => $correspondant,
            //     'messages' => $messages,
            //     'messagesUser' => $messagesUser,
            // 'answerForm' => $form->createView(),
            // ]);

            return $this->render('pages/user/messagerie.html.twig', [
                'user' => $user,
                'correspondantActuel' => $correspondant,
                'correspondants' => $correspondants,
                'countries' => $countries,
                'themes' => $themes,
                'messages' => $messages,
                'messagesUser' => $messagesUser,
                'answerForm' => $form->createView(),
            ]);
        } else {

            $form = $this->createForm(MessageType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $message = $form->getData();

                $message->setSend($user);
                $message->setDateTime(new DateTime);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($message);
                $entityManager->flush();

                return $this->redirectToRoute('messagerieShow', ['id' => $message->getReceived()->getId()]);
            }

            return $this->render('pages/user/messagerie.html.twig', [
                'user' => $user,
                'correspondants' => $correspondants,
                'countries' => $countries,
                'themes' => $themes,
                'messages' => $messages,
            ]);
        }
    }
    /**
     * @Route("/newMessage", name="newMessage", methods={"POST"})
     */
    public function newMessage()
    {

        $form = $this->createForm(MessageType::class);

        return $this->render('components/newMessage.html.twig', [
            'answerForm' => $form->createView(),
        ]);
    }
}
