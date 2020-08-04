<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Genre;
use App\Repository\GameRepository;
use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class GameController extends AbstractController
{
    /**
     * @Route("/games/show", name="show_all_games")
     */
    public function index(Request $request, PaginatorInterface $paginator, GameRepository $gameRepository)
    {
        $term = $request->query->get('term');
        
        if ($term)
        {
            $games = $gameRepository->findByTerm($term);  
        } else {
            $games = $gameRepository->findAll();
        }
    
        $pagination = $paginator->paginate(
            $games, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/);
        
        return $this->render('game/index.html.twig', [
            'games' => $games,
            'term' => $term,
            'pagination' => $pagination,
        ]);
    }
    
    /**
     * @Route("/game/{slug}", name="show_game")
     */
    public function show_one($slug, GameRepository $gameRepository, GenreRepository $genreRepo)
    {
        
        $game = $gameRepository->findOneBySlug($slug);

        if (!$game) {
            throw $this->createNotFoundException('There is no one under this slug!' .$slug);
        }

        return $this->render('game/show.html.twig', [
            'game'=>$game, 
        ]);
    }

    /**
     * @Route("/games/create")
     */
    public function reg(Request $request)
    {

        $game = new Game();

        $form = $this->createFormBuilder($game)
            ->add('name', TextType::class)
            ->add('genres', EntityType::class, array (
                'class' => Genre::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ))
            ->add('save', SubmitType::class, ['label' => 'Create Game'])
            ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // $form->getData() holds the submitted values
                // but, the original `$task` variable has also been updated
                $game = $form->getData();
        
                // ... perform some action, such as saving the task to the database
                // for example, if Task is a Doctrine entity, save it!
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($game);
                $entityManager->flush();
        
                return $this->redirectToRoute('show_game', [
                    'slug'=>$game->getSlug() 
               ]);
            }

        return $this->render('game/create.html.twig', [
            'form' => $form->createView(),
            'game' => $game
        ]); 
    }

    /**
     * @Route("games/delete/{id}", name="delete_game", methods={"DELETE"})
     */
    public function delete($id, GameRepository $gameRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $game = $gameRepository->find($id);

        $entityManager->remove($game);
        $entityManager->flush($game);

        return $this->redirectToRoute('show_all_games');
    }

    /**
     * @Route("games/update/{id}", name="update_game")
     */
    public function update($id, GameRepository $gameRepository, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $game = $gameRepository->find($id);

        $form = $this->createFormBuilder($game)
        ->add('name', TextType::class)
        ->add('genres', EntityType::class, array (
            'class' => Genre::class,
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true
        ))
        ->add('save', SubmitType::class, ['label' => 'Update game'])
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $game = $form->getData();

            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('show_game', [
                'slug'=>$game->getSlug()
            ]);
        }
        return $this->render('game/update.html.twig', [
            'form'=>$form->createView()
        ]);
    }
}
