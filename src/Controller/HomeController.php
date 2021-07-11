<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(User $user = null)
    {

        $user = $this->getUser();

        $users =  $this->getDoctrine()->getRepository(User::class)
            ->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        if (empty($user)){
            return $this->redirectToRoute('homeVisitor');
        }
    

        return $this->render('pages/home/indexBEM.html.twig', [
            'users' => $users,
            'countries' => $countries,
            'themes' => $themes
        ]);
    }

        /**
     * @Route("/homeVisitor", name="homeVisitor")
     */
    public function indexVisitor()
    {
        $users =  $this->getDoctrine()->getRepository(User::class)
            ->findAll();
        $countries =  $this->getDoctrine()->getRepository(Pays::class)
            ->findAll();
        $themes =  $this->getDoctrine()->getRepository(Theme::class)
            ->findAll();
        if (isset($user)){
            return $this->redirectToRoute('homeVisitor');
        }
    

        return $this->render('pages/home/indexVisitor.html.twig', [
            'users' => $users,
            'countries' => $countries,
            'themes' => $themes
        ]);
    }
}
