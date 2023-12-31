<?php

namespace App\Controller;

use App\Entity\Category;
Use App\Form\CategoryType;
Use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale}/category')]
class CategoryController extends AbstractController
{   
   
    
    #[Route('/', name: 'app_category')]
    public function index( EntityManagerInterface $em , Request $request, TranslatorInterface $translator ): Response
    {   
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // le formulaire a été soumis et est valider 
            $em->persist($category); // prépare la sauvgarde
            $em->flush(); // executer

            $this->addFlash('succes', $translator->trans('category.cree'));
        }

        $categorys = $em->getRepository (Category::class)->findAll();
        return $this->render('category/index.html.twig', [
            'categorys' => $categorys,
            'ajout' => $form->createView(),
        ]);
    }
    #[Route('/{id}', name: 'category')]
    public function category( Category $category, Request $request, EntityManagerInterface $em ): Response
    {

        if($category == null) {
            $this->addFlash('danger','Category introuvable');
            return $this->redirectToRoute('app_category');
        }
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
        $em->persist($category);
            $em->flush();
            $this->addFlash('success','Catégorie mis a jour');
        }

        return $this->render("category/show.html.twig" , [
            'category' => $category,
            'edit' => $form -> createView()
        ]);
    }
    #[Route('/delete/{id}', name:'delete_category')]
    public function delete( Category $category = null , EntityManagerInterface $em ){
        if($category == null) { 
            $this->addFlash('danger','Catégorie introuvable');
            return $this->redirectToRoute('app_category');
    }

    $em->remove($category);
    $em->flush();

    $this->addFlash('warning','Categorie Supprimer');
    return $this->redirectToRoute('app_category');
  }
}

