<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Genre;
use App\Repository\GameRepository;
use App\Repository\GenreRepository;
use App\Repository\ProductRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class GenreController extends AbstractController
{
    /**
     * @Route("/genre/create", name="genre_new")
     */
    public function create(Request $request)
    {
        $genre = new Genre();

        $form = $this->createFormBuilder($genre)
        ->add('name', TextType::class)
        ->add('save', SubmitType::class, ['label'=>'Create genre'])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $genre = $form->getData();
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($genre);
            $entityManager->flush();

            return $this->redirectToRoute('show_genres');
        }
        return $this->render('genre/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/genres", name="show_genres")
     */
    public function index(GenreRepository $genreRepository, Request $request, PaginatorInterface $paginator, ProductRepository $productRepository)
    {
        $term = $request->query->get('term');

        if ($term) {
            $genres = $genreRepository->findByTerm($term);
        } else {
            $genres = $genreRepository->findAll();
        }

        $productrank = $productRepository->findByLimit(5);
        // $productgame = $productrank->getGame();

        $pagination = $paginator->paginate(
            $genres, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        // dump($productgame);exit;

        return $this->render('genre/index.html.twig', [
            'productrank'=>$productrank,
            'genres'=>$genres,
            'pagination'=>$pagination,
            'term'=>$term
        ]);
    }

    /**
     * @Route("/genres/{slug}", name="genre_show")
     */
    public function show($slug, GenreRepository $genreRepository, GameRepository $gameRepository)
    {
        $genre = $genreRepository->findOneBySlug($slug);
        $games = $genre->getGames(); 


        if (!$genre) {
            throw $this->createNotFoundException('There is no one under this slug!' .$slug);
        }

        return $this->render('genre/show.html.twig', [
            'genre'=>$genre,
            'games'=>$games
        ]);
    }

    /**
     * @Route("/genre/delete/{id}", name="genre_delete", methods={"DELETE"})
     */
    public function remove(GenreRepository $genreRepository, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $genre = $genreRepository->find($id);

        $entityManager->remove($genre);
        $entityManager->flush();

        return $this->redirectToRoute('show_genres');
    }

    /**
     * @Route("/genre/update/{id}", name="genre_update")
     */
    public function update(GenreRepository $genreRepository, $id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $genre = $genreRepository->find($id);

        $form = $this->createFormBuilder($genre)
        ->add('name', TextType::class)
        ->add('save', SubmitType::class, ['label'=>'Update genre'])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $genre = $form->getData();

            $entityManager->persist($genre);
            $entityManager->flush();

            return $this->redirectToRoute('show_genres');
        }
        return $this->render('genre/update.html.twig', [
            'form'=>$form->createView()
        ]);
    }
}
