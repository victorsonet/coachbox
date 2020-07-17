<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;

class CommentController extends AbstractController
{
    /**
     * @Route("/comments/{id}/vote/{direction<up|down>}", methods="POST")
     */
    public function commentVote($id, $direction, LoggerInterface $logger)
    {
        if ($direction==='up'){
            $logger->info('Voting up');
            $currentVotecount=rand(7,100);
        } else {
            $currentVotecount=rand(0,5);
            $logger->info('Voting down');
        }

        return new JsonResponse(['votes'=>$currentVotecount]);
    }
}