<?php

namespace App\Controller;

use App\Entity\Product;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/products/{id}", name="show_product")
     */
    public function product($id)
    {
        $em=$this->getDoctrine()->getManager();
        $prdRepo=$em->getRepository(Product::class);
        $product=$prdRepo->find($id);

        if (!$product) {
            throw $this->createNotFoundException('There is no one under this id! -' .$id);
        }
        return $this->render('product/show.html.twig', [
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
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();  
            return $this->redirectToRoute('product/homepage.html.twig'); 
        }
            return $this->render('product/create.html.twig', [
                'form' => $form->createView(),]
            ); 
    }

    /**
     * @Route("/products-rm/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete($id, Response $response)
    {
        $em=$this->getDoctrine()->getManager();
        $prdRepo=$em->getRepository(Product::class);
        $product=$prdRepo->find($id);

        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('products_show');
    }


}
