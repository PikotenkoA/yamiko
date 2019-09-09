<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
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
    public function show(Category $category, ProductRepository $productRepository, Request $request)
    {
        $form = $this->getSearchForm($category);
        $form->handleRequest($request);
        $products = $productRepository->findByFilter($category, $form->getData());

        return $this->render('category/show.html.twig',[
            'category'=>$category,
            'products' =>$products,
            'form' => $form->createView(),
        ]);
    }

    public function headerlist(CategoryRepository $categoryRepository)
    {
        return $this->render('category/_header_list.html.twig',[
            'categories'=> $categoryRepository->findBy(['parent' => null]),
    ]);
    }

    private function getSearchForm(Category $category)
    {
        $formBilder = $this->createFormBuilder([]);
        $formBilder->setMethod('GET');

        foreach ($category->getAttributes() as $attribute){
            $values = $attribute->getValuesList();
            $choices = array_combine($values, $values);

            $formBilder->add ('attr'.$attribute->getId(), ChoiceType::class,[
                'multiple' => true,
                'expanded' => true,
                'choices' => $choices,
                'label' => $attribute->getName(),
            ]);
        }

        return $formBilder->getForm();
    }
}



