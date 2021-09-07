<?php

namespace App\Controller;

use DateTime;
use App\Entity\Pays;
use App\Entity\Place;
use App\Entity\User;
use App\Entity\Theme;
// use App\Form\InscriptionType;
use App\Form\EditUserType;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form);
            foreach ($form->get('projetsVoyages') as $item) {
                $place = new Place;
                // dd($item->get('name')->getViewData());
                $place->setName($item->get('name')->getViewData());
                $place->setPlaceId($item->get('placeId')->getViewData());
                $place->setStatut("0");
                $place->addUser($user);
            }
            foreach ($form->get('voyagesAccomplis') as $item) {
                $place = new Place;
                $place->setName($item->get('name')->getViewData());
                $place->setPlaceId($item->get('placeId')->getViewData());
                $place->setStatut("1");
                $place->addUser($user);
            }
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setDateCreation(new DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('pages/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'countries' => $countries,
            'themes' => $themes
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/{id}/edit", name="editProfil")
     */
    public function edit(Request $request, User $user, TokenStorageInterface $tokenStorage): Response
    {

        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();

        $userConnected = $tokenStorage->getToken()->getUser();


        // checker id user connecté et id route et conditionner la suite (user interface ?)
        if ($user == $userConnected) {
            $form = $this->createForm(EditUserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // encode the plain password
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                // do anything else you need here, like send an email

                return $this->redirectToRoute('home');
            }
            return $this->render('pages/registration/edit.html.twig', [
                'editUserForm' => $form->createView(),
                'countries' => $countries,
                'themes' => $themes
            ]);
        } else {
            // $this->addFlash('essaiHacking', 'Vous ne pouvez modifier ou supprimer que votre propre compte.');

            return $this->redirectToRoute('home');
        }
    }
}


// <textarea name="text" oninput='this.style.height = "";this.style.height = this.scrollHeight + "px"'></textarea>
