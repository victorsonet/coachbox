<?php

namespace App\Controller;

use App\Entity\Coach;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\CoachRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ChController extends AbstractController
{

    /**
     * @Route("/ch/{id}", name="show_coach")
     */
    public function show_id($id, CoachRepository $coachRepository)
    {
        $coach = $coachRepository->find($id);
        $teams = [
            'Vox Eminor',
            'Renegades',
            'FaZe',
        ];

        if (!$coach) {
            throw $this->createNotFoundException('There is no one under this id!' .$id);
        }
        return $this->render('coaches/show.html.twig', [
            'coach'=>$coach, 'teams'=>$teams
        ]);
    }

    /**
     * @Route("/ch-edit/{id}", name="ch_update")
     */

    public function update($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $coach = $entityManager->getRepository(Coach::class)->find($id);
        if (!$coach)
        {
            throw $this->createNotFoundException('No coach under this id' .$id);
        }

        $coach->setFirstName('Ja');
        $entityManager->flush();

        return $this->redirectToRoute('show_coach', [
            'id' => $coach->getId()
        ]);
    }

    /**
     * @Route("/ch-remove/{id}")
     */

    public function remove($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $coach = $entityManager->getRepository(Coach::class)->find($id);
        $entityManager->remove($coach);
        $entityManager->flush();
        return $this->redirectToRoute('show_coach', [
            'id'=>$id
        ]);
    }

    /**
     * @Route("/", name="show_coaches")
     */
    public function index()
    {
        $en=$this->getDoctrine()->getRepository(Coach::class);
        $coaches=$en->findAll();
        
        return $this->render('coaches/homepage.html.twig', [
            'coachlist' => $coaches
        ]);
    }

    /**
     * @Route("/signup", name="coach_reg")
     */
    public function reg(Request $request)
    {
        $coach = new Coach();

        $form = $this->createFormBuilder($coach)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('game', TextType::class)
            ->add('achievements', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Task'])
            ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // $form->getData() holds the submitted values
                // but, the original `$task` variable has also been updated
                $coach = $form->getData();
        
                // ... perform some action, such as saving the task to the database
                // for example, if Task is a Doctrine entity, save it!
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($coach);
                $entityManager->flush();
        
                return $this->redirectToRoute('show_coach', [
                    'id'=>$coach->getId() 
               ]);
            }

        return $this->render('coaches/signup.html.twig', [
            'form' => $form->createView(),
        ]); 
    }
}   

