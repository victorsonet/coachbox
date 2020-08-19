<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Entity\Product;
use App\Entity\Game;
use App\Entity\Review;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CoachRepository;
use App\Repository\GameRepository;
use App\Repository\GenreRepository;
use App\Repository\ProductRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ChController extends AbstractController
{

    /**
     * @Route("/ch/{slug}", name="show_coach")
     */
    public function show_id($slug,ReviewRepository $reviewRepository, CoachRepository $coachRepository, ProductRepository $productRepository,Request $request, UserRepository $userrepo)
    {
        $coach = $coachRepository->findOneBySlug($slug);
        $products = $coach->getProducts();
        $product = new Product();
        $product->setCoach($coach);
        $review = new Review();
        $review->setCoach($coach);
        $reviewitems = $coach->getReviews();
        $reviewavg = $reviewRepository->getAvg($coach->getId());
        $user = $coach->getUser();

        // dump($reviewavg['avg']);exit;

        $reviewform = $this->createFormBuilder($review)
            ->add('stars', ChoiceType::class, 
                [
                'choices' => [
                '1' => 1,
                '2' => 2,
                '3' => 3,
                '4' => 4,
                '5' => 5,
                ]])
                ->add('description', TextareaType::class)
                ->add('save', SubmitType::class, ['label' => 'Submit Review'])
                ->getForm();

        $reviewform->handleRequest($request);
        if ($reviewform->isSubmitted() && $reviewform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $review = $reviewform->getData();
    
            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($review);
            $entityManager->flush();
        }

        $form = $this->createFormBuilder($product)
            ->add('Type', TextType::class)
            ->add('price', IntegerType::class)
            ->add('description', TextType::class)
            ->add('game', EntityType::class, array(
                'class' => Game::class,
                'choice_label' => 'name'
            ))
            ->add('save', SubmitType::class, ['label' => 'Create Product'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $product = $form->getData();

    
            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('show_coach', [
                'slug'=>$slug
            ]);
        }

        if (!$coach) {
            throw $this->createNotFoundException('There is no one under this slug!' .$slug);
        }

        return $this->render('coaches/show.html.twig', [
            'user'=>$user,
            'reviewavg'=>$reviewavg,
            'reviewitems'=>$reviewitems,
            'coach'=>$coach, 
            'products'=>$products,
            'form' => $form->createView(),
            'reviewform' => $reviewform->createView()
        ]);
    }

    public function createProductForm($product)
    {
        $form = $this->createFormBuilder($product)
            ->add('Type', TextType::class)
            ->add('price', IntegerType::class)
            ->add('description', TextType::class)
            ->add('game', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Product'])
            ->getForm();

        return $form->createView();
    }

    /**
     * @Route("/ch-edit/{id}", name="ch_update")
     */

    public function update(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $coach = $entityManager->getRepository(Coach::class)->find($id);
        if (!$coach)
        {
            throw $this->createNotFoundException('No coach under this id' .$id);
        }

        $form = $this->createFormBuilder($coach)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('games', EntityType::class, array (
                'class' => Game::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ))
            ->add('achievements', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Update Coach'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $coach = $form->getData();
    
            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
    
            return $this->redirectToRoute('show_coach', [
                'slug'=>$coach->getSlug() 
        ]);
        }


        return $this->render('coaches/edit.html.twig', [
            'form'=>$form->createView()
        ]);


    }

    /**
     * @Route("/ch-remove/{id}", name="remove_id", methods={"DELETE"})
     */

    public function remove($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $coach = $entityManager->getRepository(Coach::class)->find($id);
        $entityManager->remove($coach);
        $entityManager->flush();
        return $this->redirectToRoute('show_coaches');
    }

    /**
     * @Route("/", name="show_coaches")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $term = $request->query->get('term');
        $en=$this->getDoctrine()->getManager();
        $coachrepo=$en->getRepository(Coach::class);
        $limit = $request->query->getInt('limit', 10);

        if ($term)
        {
            $coaches = $coachrepo->findByTerm($term);  
        } else {
            $coaches = $coachrepo->findAll();
        }
    
        $pagination = $paginator->paginate(
            $coaches, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            $limit /*limit per page*/);
        
        return $this->render('coaches/homepage.html.twig', [
            'coachlist' => $coaches,
            'term' => $term,
            'pagination' => $pagination,
            ]);
    }

    /**
     * @Route("/signup", name="coach_reg")
     */
    public function reg(Request $request, GameRepository $gamerepo)
    {
        $coach = new Coach();
        $games = $gamerepo->findAll();
        $user = $this->getUser();
        $coach->setUser($user);
        $roles = $user->getRoles();
        array_push ($roles, 'ROLE_COACH');
        // dump($roles);exit;
        
        if (!$this->isGranted('ROLE_COACH', $user))
        {
            // dump($roles);exit;
            $user->setRoles($roles);
        }

        $form = $this->createFormBuilder($coach)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('games', EntityType::class, array (
                'class' => Game::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ))
            ->add('achievements', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Coach!'])
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
                    'slug'=>$coach->getSlug(),
                    ]);
                }

        return $this->render('coaches/signup.html.twig', [
            'form' => $form->createView(),
            'games' => $games
        ]); 
    }

    /**
     * @Route("/browse", name="browse")
     */
    public function search(CoachRepository $coachrepo, GenreRepository $genreRepo, GameRepository $gamerepo)
    {
        

        $coaches = $coachrepo->findByLimit(5);

        $genres = $genreRepo->findByLimit(5);

        $games = $gamerepo->findByLimit(5);



        return $this->render('coaches/search.html.twig', [
            'coaches' => $coaches,
            'genres' => $genres,
            'games' => $games
        ]);
    }
}   

