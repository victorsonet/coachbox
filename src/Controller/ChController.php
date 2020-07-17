<?php

namespace App\Controller;

use App\Entity\Coach;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\CoachRepository;

class ChController extends AbstractController
{
    /**
     * @Route("/ch", name="ch_coach")
     */
    public function createCoach(ValidatorInterface $validator)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $coach = new Coach();
        $coach->setFirstName('Jonatan');
        $coach->setLastName('Lundberg');
        $coach->setGame('CSGO');
       $coach->setAchievements('3x Major');

       $entityManager->persist($coach);
        $entityManager->flush();

        $errors = $validator->validate($coach);
        if(count($errors)>0) {
            return new Response((string) $errors, 400);
        }
     return new Response('Saved new coach with ID! ' .$coach->getId()); 
    }


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
    public function reg()
    {
        return new Response('asd');
    }
}   

