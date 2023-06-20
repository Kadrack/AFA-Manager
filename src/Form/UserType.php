<?php
// src/Form/UserType.php
namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class MemberType
 * @package App\Form
 */
class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        switch ($options['form'])
        {
            case 'loginEdit':
                $this->loginEdit($builder);
                break;
            case 'passwordEdit':
                $this->passwordEdit($builder);
                break;
            case 'createLogin':
                $this->createLogin($builder);
                break;
            case 'createLoginLink':
                $this->createLoginLink($builder);
                break;
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array('data_class' => User::class, 'form' => ''));
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function loginEdit(FormBuilderInterface $builder): void
    {
        $builder
            ->add('Login', TextType::class, array('label' => 'Nouveau login', 'mapped' => false, 'attr' => array('placeholder' => 'Login'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function passwordEdit(FormBuilderInterface $builder): void
    {
        $builder
            ->add('Password', RepeatedType::class, array('type' => PasswordType::class, 'invalid_message' => 'Les deux mot de passe doivent correspondre', 'required' => true, 'first_options' => array('label' => 'Mot de passe', 'attr' => array('placeholder' => 'Password'), 'row_attr' => array('class' => 'form-floating')), 'second_options' => array('label' => 'Vérification du mot de passe', 'attr' => array('placeholder' => 'Password'), 'row_attr' => array('class' => 'form-floating'))))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function createLogin(FormBuilderInterface $builder): void
    {
        $builder
            ->add('MemberId', IntegerType::class, array('label' => '# Licence', 'mapped' => false, 'attr' => array('placeholder' => 'MemberId'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Firstname', TextType::class, array('label' => 'Prénom', 'mapped' => false, 'attr' => array('placeholder' => 'Firstname'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Name', TextType::class, array('label' => 'Nom', 'mapped' => false, 'attr' => array('placeholder' => 'Name'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Email', EmailType::class, array('label' => 'Adresse mail', 'mapped' => false, 'attr' => array('placeholder' => 'Email'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Créer'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function createLoginLink(FormBuilderInterface $builder): void
    {
        $builder
            ->add('Login', TextType::class, array('label' => 'Login', 'mapped' => false, 'attr' => array('placeholder' => 'Login'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Email', EmailType::class, array('label' => 'Email', 'mapped' => false, 'attr' => array('placeholder' => 'Email'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Envoyer'))
        ;
    }
}
