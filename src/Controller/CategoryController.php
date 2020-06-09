<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CategoryType;
use App\Entity\Category;


class CategoryController extends AbstractController
{
    /**
     * Add a new category
     *
     * @Route("/wild/addCategory", name="wild_addCategory")
     * @param Request $request
     * @return Response A response instance
     */
    public function add(Request $request, EntityManagerInterface $em) : Response {

        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form ->isValid()) {
            $categoryRepository = $this->getDoctrine()
                ->getRepository(Category::class);
            $category = $form->getData();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('app_index');
        }

        return $this->render('wild/addCategory.html.twig',[
            'form'=>$form->createView()
            ]);
    }
}