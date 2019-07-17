<?php


namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductImageAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form)
    {
       $form->add('image', VichImageType::class, [
           'required'=> false,
    ]);
    }


}