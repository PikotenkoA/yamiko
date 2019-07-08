<?php
/**
 * Created by PhpStorm.
 * User: skillup_student
 * Date: 03.07.19
 * Time: 19:38
 */

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdminuseuseuseuse Sonata\AdminBundle\Form\FormMapper; Symfony\Component\DomCrawler\Form; Sonata\AdminBundle\Datagrid\DatagridMapper; Sonata\AdminBundle\Datagrid\ListMapper;

class CatagoryAdmin extends AbstractAdmin
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

    Potected function configureFromFields(FormMapper $form)
    {
        $from
            ->add('id')
            ->add('name');
    }

    }



}