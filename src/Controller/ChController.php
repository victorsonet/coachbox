<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Entity\Product;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\CoachRepository;
use App\Repository\ProductRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Knp\Component\Pager\PaginatorInterface;
class ChController extends AbstractController
{

    /**
     * @Route("/ch/{slug}", name="show_coach")
     */
    public function show_id($slug, CoachRepository $coachRepository, ProductRepository $productRepository,Request $request)
    {
        $coach = $coachRepository->findOneBySlug($slug);
        $products = $coach->getProducts();
        $product = new Product();
        $product->setCoach($coach);

        $form = $this->createFormBuilder($product)
            ->add('Type', TextType::class)
            ->add('price', IntegerType::class)
            ->add('description', TextType::class)
            ->add('game', TextType::class)
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
        }

        if (!$coach) {
            throw $this->createNotFoundException('There is no one under this slug!' .$slug);
        }

        return $this->render('coaches/show.html.twig', [
            'coach'=>$coach, 
            'products'=>$products,
            'form' => $form->createView(),
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
            ->add('game', TextType::class)
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
        
        if ($term)
        {
            $coaches = $coachrepo->findByTerm($term);  
        } else {
            $coaches = $coachrepo->findAll();
        }
    
        $pagination = $paginator->paginate(
            $coaches, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/);
        
        return $this->render('coaches/homepage.html.twig', [
            'coachlist' => $coaches,
            'term' => $term,
            'pagination' => $pagination,
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
                    'slug'=>$coach->getSlug() 
               ]);
            }

        return $this->render('coaches/signup.html.twig', [
            'form' => $form->createView(),
        ]); 
    }

    public function product(Request $request, $id)
    {

    }
}   

