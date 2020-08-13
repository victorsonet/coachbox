<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\CoachRepository;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
    public function userprofile($slug, UserRepository $userrepo, Request $request, CoachRepository $coachrepo)
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

    /**
     * @Route("/user/{slug}/settings", name="user_settings")
     */
    public function usersettings($slug, UserRepository $userrepo, UserPasswordEncoderInterface $passwordEncoder, Request $request, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator )
    {
        $em = $this->getDoctrine()->getManager();
        $user = $userrepo->findOneBySlug($slug);
        // dump($user->getUsername());exit;

        $form=$this->createFormBuilder($user)
        ->add('email', EmailType::class,[
            'required'=>false,
        ])
        ->add('plainPassword', RepeatedType::class, [
            'mapped' => false,
            'type' => PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => true,
            'first_options' => ['label'=>'Password'],
            'second_options' => ['label'=>'Repeat Password'],
                    ])
        ->add('save', SubmitType::class, ['label' => 'Update user'])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('/user/settings.html.twig',[
            'form'=>$form->createView(),
            'user'=>$user
        ]);
    }
}
