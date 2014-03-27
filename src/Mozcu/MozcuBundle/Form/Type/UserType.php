<?php
namespace Mozcu\MozcuBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array(
            'attr' => array('class' => 'mail', 'placeholder' => 'Correo electronico')));
        $builder->add('password', 'password', array(
            'attr' => array('class' => 'pass', 'placeholder' => 'Contrase&ntilde;a')));
        $builder->add('username', 'text', array(
            'attr' => array('class' => 'user', 'placeholder' => 'Nombre de usuario')));
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mozcu\MozcuBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}