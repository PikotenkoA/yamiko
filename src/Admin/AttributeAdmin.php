<?php


namespace App\Admin;


use phpDocumentor\Reflection\Types\Callable_;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AttributeAdmin extends AbstractAdmin
{

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('category')
            ->addIdentifier('name')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('category')
            ->add('name')
        ;
    }

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('category')
            ->add('name')
            ->add('valuesList', TextareaType::class )
        ;

        $form->get('valuesList')->addModelTransformer(new CallbackTransformer(
            function ($valuesArray) {
                return $valuesArray ? implode("\n", $valuesArray) : '';
            },
            function ($valuesString){
                $values = explode("\n", $valuesString);
                $values = array_map('trim', $values);
                $values = array_filter($values);

                return $values;
            }
        ));

    }

}