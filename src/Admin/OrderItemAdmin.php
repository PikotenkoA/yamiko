<?php


namespace App\Admin;


use App\Form\MoneyTransformer;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class OrderItemAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('product')
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