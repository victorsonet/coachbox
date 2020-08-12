<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Repository\UserRepository;
use App\Repository\CoachRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class UserController extends AbstractController
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

        return $this->render('user/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    
    /**
     * @Route("/user/{slug}", name="user_profile")
     */
    public function userprofile($slug, UserRepository $userrepo, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $userrepo->findOneBySlug($slug);
        
        // dump($user);exit;

        if(!$user)
        {
            throw $this->createNotFoundException('User doesnt exist!');
        }

        return $this->render('user/profile.html.twig',[
            'slug' => $slug,
            'users' => $user,
        ]);
    }
}
