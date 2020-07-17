<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoachController extends AbstractController
{

    /**
     * @Route("coaches/{slug}", name="app_team_show")
     */

    public function show($slug)
    {
        $teams = [
            'Vox Eminor',
            'Renegades',
            'FaZe',
        ];


        return $this->render('coaches/show.html.twig',[
            'coach'=>$slug,
            'teams'=>$teams,
        ]);
    }
}