<?php


namespace App\Admin;


use App\Entity\Product;
use App\Form\MoneyTransformer;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class OrderItemAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('product', null, [
                'choice_attr' => function (Product $choice, $key, $value) {
                return [
                    'data-price' =>$choice->getPrice()/100,
                ];
            },
            ])
            ->add('price', null, [
                'attr' => [
                    'readonly'=> true,
                    'class' => 'js-update-amount',
                ],
            ])
            ->add('count', null, [
                'attr' => [
                    'class' => 'js-update-amount',
                    ]
                ])
            //->add('amount')
        ;

        $form->get('price')->addModelTransformer(new MoneyTransformer());
    }
}