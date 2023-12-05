<?php

namespace App\Controller;
Use App\Form\ProductType;
use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
Use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/product')]
class ProductController extends AbstractController
{

    #[Route('/', name: 'app_product')]
    public function index( EntityManagerInterface $em , Request $request ): Response
    {   
        $product = new Produit();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // le formulaire a été soumis et est valider 
            $em->persist($product); // prépare la sauvgarde
            $em->flush(); // executer

            $this->addFlash('succes', 'Produit ajoutée');
        }

        $product = $em->getRepository (Produit::class)->findAll();
        return $this->render('product/index.html.twig', [
            'product' => $product,
            'ajout' => $form->createView(),
        ]);
    }
    #[Route('/{id}', name: 'product')]
    public function category( Produit $produit, Request $request, EntityManagerInterface $em ): Response
    {

        if($produit == null) {
            $this->addFlash('danger','Produit introuvable');
            return $this->redirectToRoute('app_product');
        }
        $form = $this->createForm(ProductType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
        $em->persist($produit);
            $em->flush();
            $this->addFlash('success','Produit mis a jour');
        }

        return $this->render("product/show.html.twig" , [
            'product' => $produit,
            'edit' => $form -> createView()
        ]);
    }
    #[Route('/delete/{id}', name:'delete_product')]
    public function delete( Produit $produit = null , EntityManagerInterface $em ){
        if($produit == null) { 
            $this->addFlash('danger','Produit introuvable');
            return $this->redirectToRoute('app_product');
    }

    $em->remove($produit);
    $em->flush();

    $this->addFlash('warning','Produit Supprimer');
    return $this->redirectToRoute('app_product');
  }
}
