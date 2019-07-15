<?php


namespace App\Admin;


use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAdmin extends AbstractAdmin
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        $code,
        $class,
        $baseControllerName,
        UserPasswordEncoderInterface $passwordEncoder
    ){
        parent::__construct($code, $class, $baseControllerName);

        $this->passwordEncoder = $passwordEncoder;
    }


    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->addIdentifier('email')
            ->addIdentifier('roles', null,[
                'template'=> 'admin/user/list_roles.html.twig',
            ])
            ->addIdentifier('firstName')
            ->addIdentifier('lastName')
            ->addIdentifier('address');

    }


    protected function configureDatagridFilters(DatagridMapper $filter)
{
    $filter

        ->add('email')
        ->add('roles', null, [],ChoiceType::class, [
            'choices'=>[
                'Пользователь'=> 'ROLE_USER',
                'Администратор' =>'ROLE_ADMIN',
            ],
        ])
        ->add('firstName')
        ->add('lastName')
    ;
}

    protected function configureFormFields(FormMapper $form)
    {
        $form


            ->add('email')
            ->add('plainPassword', TextType::class)
            ->add('roles',ChoiceType::class, [
                'choices'=>[
                    'Пользователь'=> 'ROLE_USER',
                    'Администратор' =>'ROLE_ADMIN',
                ],
                'multiple'=> true,
                'expanded'=> true,
            ])
            ->add('firstName')
            ->add('lastName')
            ->add('address', null, [
                'required' => false,
            ])
        ;
    }


    /**
     * @param $object
     */
    public function prePersist($object)
    {
        $this->updatePassword($object);
    }


    /**
     * @param $object
     */
    public function preUpdate($object)
    {
        $this->updatePassword($object);
    }

    private function updatePassword(User $user)
    {
        if (!$user->getPlainPassword()) {
            return;
        }

        $hash = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($hash);
    }
}