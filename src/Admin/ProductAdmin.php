<?php
/**
 * Created by PhpStorm.
 * User: skillup_student
 * Date: 03.07.19
 * Time: 19:38
 */

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class CategoryAdmin extends AbstractAdmin
{
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->addIdentifier('name');

    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
       $filter
           ->add('id')
           ->add('name');
    }

    protected function configureFromFields(FormMapper $form)
    {
        $form
            ->add('id')
            ->add('name');
    }

}