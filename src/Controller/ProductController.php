<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Product;
use App\Entity\Coach;
use App\Repository\CoachRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/products/{id}", name="show_product")
     */
    public function product($id, CoachRepository $coachrepo)
    {
        $em=$this->getDoctrine()->getManager();

        $prdRepo=$em->getRepository(Product::class);
        $product=$prdRepo->find($id);

        if (!$product) {
            throw $this->createNotFoundException('There is no one under this id' .$id);
        }
        
        return $this->render('product/show.html.twig', [
            'id'=>$id,
            'product'=>$product,
        ]);
    }

    /**
     * @Route("/products", name="products_show")
     */
    public function productslist(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $prdRepo=$em->getRepository(Product::class);
        $products=$prdRepo->findAll();

        return $this->render('product/homepage.html.twig', [
            'products'=>$products
        ]);
    }

    /**
     * @Route("/product/create", name="product_create")
     */
    public function create(Request $request)
    {
        $product = new Product();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder($product)
            ->add('Type', TextType::class)
            ->add('price', IntegerType::class)
            ->add('description', TextType::class)
            ->add('game', EntityType::class, array(
                'class' => Game::class,
                'choice_label' => 'name'
            ))
            // ->add('game', TextType::class)
            ->add('coach', EntityType::class, array(
                'class' => Coach::class,
                'choice_label' => 'last_name'
            ))
            ->add('save', SubmitType::class, ['label' => 'Create Product'])
            ->getForm();

            

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // $form->getData() holds the submitted values
                // but, the original `$task` variable has also been updated
                $product = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();  
            return $this->redirectToRoute('products_show'); 
        }
            return $this->render('product/create.html.twig', [
                'form' => $form->createView(),]
            ); 
    }

    /**
     * @Route("/products-rm/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete($id)
    {
        $em = $this->getDoctrine()->getManager();

        $prdRepo = $em->getRepository(Product::class);

        $product = $prdRepo->find($id);
        $coach = $product->getCoach();

        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('show_coach',[
            'slug'=>$coach->getSlug()
                    ]);
    }
    /**
     * @Route("product/update/{id}", name="product_update")
     */
    public function update($id, Request $request){
        $em=$this->getDoctrine()->getManager();
        $prdRepo=$em->getRepository(Product::class);
        $product=$prdRepo->find($id);
        $coach = $product->getCoach();

        if(!$product)
        {
            return new Response('There is no product on this id!' .$id);
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

            return $this->redirectToRoute('show_coach',[
                'slug'=>$coach->getSlug()
            ]);
        }
        return $this->render('product/update.html.twig', [
            'form'=>$form->createView()]);
    }

}
