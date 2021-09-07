<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Commentaire;
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
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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

                $url = $this->generateUrl('reset_password_token', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

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
     * @Route("/reset_password_token/{token}", name="reset_password_token")
     */
    public function resetPasswordToken(EntityManagerInterface $manager, Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    {

        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();

        // $form = $this->createForm(ChangePasswordType::class);
        // $form->handleRequest($request);

        if ($request->isMethod('POST')) {

            // dd($token);

            $user = $manager->getRepository(User::class)
                ->findOneByToken($token);

            $password = $request->request->get(htmlspecialchars('password'));
            $passwordRepeat = $request->request->get(htmlspecialchars('passwordRepeat'));

            // dd($user);

            // if ($form->isSubmitted() && $form->isValid()) {

            if ($password === $passwordRepeat) {


                $user->setToken(NULL);

                // $newPassword = $form->get('password')->getData();

                $user->setPassword(
                    $passwordEncoder->encodePassword($user, $password)
                );
                // $manager->persist($user);

                $manager->flush();

                $this->addFlash('info', 'Votre mot de passe a bien été réinitialisé');

                return $this->redirectToRoute('app_login');
            } else {

                $this->addFlash('info', 'Les deux mots de passes ne correspondent pas !');
            }
        }

        return $this->render('pages/security/resetPassword.html.twig', [
            'token' => $token,
            // 'form' => $form->createView(),
            'title' => "Réinitialisation du mot de passe",
            'countries' => $countries,
            'themes' => $themes
        ]);
    }

    /**
     * @Route("/reset_password/{id}", name="reset_password")
     */
    public function resetPassword(EntityManagerInterface $manager, Request $request, User $user, UserInterface $userlogged, UserPasswordEncoderInterface $passwordEncoder)
    {

        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();

        // $form = $this->createForm(ChangePasswordType::class);
        // $form->handleRequest($request);

        if ($user->getEmail() == $userlogged->getUsername()) {

            if ($request->isMethod('POST')) {

                $submittedToken = $request->request->get('token_csrf');

                // 'delete-item' is the same value used in the template to generate the token
                if ($this->isCsrfTokenValid('reset_password', $submittedToken)) {
                    // ... do something, like deleting an object

                    $password = $request->request->get(htmlspecialchars('password'));
                    $passwordRepeat = $request->request->get(htmlspecialchars('passwordRepeat'));

                    if ($password === $passwordRepeat) {

                        $user->setPassword(
                            $passwordEncoder->encodePassword($user, $password)
                        );
                        // $manager->persist($user);

                        $manager->flush();

                        $this->addFlash('info', 'Votre mot de passe a bien été réinitialisé');

                        return $this->redirectToRoute('app_login');
                    } else {

                        $this->addFlash('info', 'Les deux mots de passes ne correspondent pas !');
                    }
                } else {

                    return $this->redirectToRoute('home');
                }
            }

            return $this->render('pages/security/resetPassword.html.twig', [
                'token' => $user->getToken(),
                // 'form' => $form->createView(),
                'title' => "Réinitialisation du mot de passe",
                'countries' => $countries,
                'themes' => $themes
            ]);
        } else {

            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/deleteUser/{id}", name="deleteUser")
     */
    public function delete(EntityManagerInterface $manager, User $user, UserInterface $userlogged, Request $request, TokenStorageInterface $tokenStorage)
    {

        if (($user->getEmail() == $userlogged->getUsername()) || ($userlogged->getRoles() != ['ROLE_USER'])) {

            $commentaires =  $this->getDoctrine()->getRepository(Commentaire::class)
                ->findByAuteur($user);
            foreach ($commentaires as $commentaire) {
                $commentaire->setAuteur(null);
                $commentaire->setTexte("Cet utilisateur a supprimé son compte. Ses commentaires ne sont plus accessibles.");
            }

            $manager->remove($user);

            $manager->flush();

            if ($user->getEmail() == $userlogged->getUsername()) {

                $request->getSession()->invalidate();

                $tokenStorage->setToken(); // TokenStorageInterface

            }
        }

        // dd($userlogged);
        return $this->redirectToRoute('home');
    }
}
