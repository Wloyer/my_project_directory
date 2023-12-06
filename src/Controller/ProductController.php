<?php

namespace App\Controller;
Use App\Form\ProductType;
use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
Use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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

            $imageFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Impossible d\ajouter l\image');
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $product->setImage($newFilename);
            }

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
