<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Entity\User;
use App\Entity\Theme;
use App\Form\ChangePasswordType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Form\ForgottenPasswordType;
use Symfony\Component\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
        ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
        ->findAll();


        return $this->render('pages/security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error,
            'countries' => $countries,
            'themes' => $themes
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/forgotten_password", name="forgotten_password")
     */
    public function forgotten_password(EntityManagerInterface $manager, UserRepository $userRepository, Request $request, TokenGeneratorInterface $tokenGenerator, MailerInterface $mailer)
    {

        $countries =  $this->getDoctrine()->getRepository(Pays::class)
        ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
        ->findAll();

        $form = $this->createForm(ForgottenPasswordType::class);
        $form->handleRequest($request);

        $email = $form->get('emailResetPass')->getData();

        $user = $userRepository->findOneByEmail($email);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($request->isMethod('POST')) {

                $token = $tokenGenerator->generateToken();
                try {

                    $user->setToken($token);

                    $manager->flush();
                } catch (\Exception $e) {
                    $this->addFlash('Warning', $e->getMessage());
                    return $this->redirectToRoute('app_login');
                }

                $url = $this->generateUrl('reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

                $message = (new Email())
                    ->from('testsDLCOLMAR@gmail.com')
                    ->to($user->getEmail())
                    ->subject('Recuperation de mdp test')
                    ->text("Voici le lien de récupération de votre mot de passe : " . $url, 'text/html')
                    ->html("<p> Ceci est un test : " . $url, 'text/html' . "</p>");

                $mailer->send($message);

                $this->addFlash('info', 'Le mail de récupération de mot de passe a bien été envoyé, vous pouvez aller le chercher');
            }
        }

        return $this->render('pages/security/forgottenPassword.html.twig', [
            'form' => $form->createView(),
            'title' => "Reinitialisation du mot de passe",
            'countries' => $countries,
            'themes' => $themes
        ]);
    }


    /**
     * @Route("/reset_password/{token}", name="reset_password")
     */
    public function resetPassword(EntityManagerInterface $manager, Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    {

        $countries =  $this->getDoctrine()->getRepository(Pays::class)
        ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
        ->findAll();

        // $form = $this->createForm(ChangePasswordType::class);
        // $form->handleRequest($request);

        if ($request->isMethod('POST')) {

            $user = $manager->getRepository(User::class)
                ->findOneByToken($token);

            // dd($user);

            // if ($form->isSubmitted() && $form->isValid()) {


            $user->setToken(NULL);

            // $newPassword = $form->get('password')->getData();

            $user->setPassword(
                $passwordEncoder->encodePassword($user, $request->request->get('password'))
            );
            // $manager->persist($user);

            $manager->flush();

            $this->addFlash('info', 'Votre mot de passe a bien été réinitialisé');

            return $this->redirectToRoute('app_login');
            // }
        }

        return $this->render('pages/security/resetPassword.html.twig', [
            'token' => $token,
            // 'form' => $form->createView(),
            'title' => "Réinitialisation du mot de passe",
            'countries' => $countries,
            'themes' => $themes
        ]);
    }
}
