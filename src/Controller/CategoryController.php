<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category/", name="category")
     */
    public function index(CategoryRepository $categoryRepository)

    {


        return $this->render('category/index.html.twig', [
            'category' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/category/{id}", name="category_show")
     */
    public function show(Category $category)
    {
        return $this->render('category/show.html.twig',[
            'category'=>$category,
        ]);
    }

}



