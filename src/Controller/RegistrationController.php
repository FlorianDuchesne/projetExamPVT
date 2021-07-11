<?php

namespace App\Controller;

use DateTime;
use App\Entity\Pays;
use App\Entity\User;
use App\Entity\Theme;
use App\Form\InscriptionType;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
            // encode the plain password
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
}
