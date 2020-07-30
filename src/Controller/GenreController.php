<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class GenreController extends AbstractController
{
    /**
     * @Route("/genre/create", name="genre_new")
     */
    public function create(Request $request, GenreRepository $genreRepository)
    {
        $genre = new Genre();

        $form = $this->createFormBuilder($genre)
        ->add('name', TextType::class)
        ->add('save', SubmitType::class, ['label'=>'Create genre'])
        ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
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
     * @Route("/genres" name="show_genres")
     */
    public function showgenres(GenreRepository $genreRepository, Request $request, PaginatorInterface $paginator)
    {
        $term = $request->query->get('term');

        if ($term)
        {
            $genres = $genreRepository->findByTerm($term);  
        } else {
            $genres = $genreRepository->findAll();
        }

        $pagination = $paginator->paginate(
            $genres, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/);

        return $this->render('index.html.twig', [
            'genres'=>$genres,
            'pagination'=>$pagination,
            'term'=>$term
        ]);
    }


}
