<?php
// src/Form/MemberType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MemberType
 * @package App\Form
 */
class NewsletterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        switch ($options['formData']['Form'])
        {
            case 'Create':
                $this->create($builder);
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array('data_class' => null, 'formData' => array()));
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function create(FormBuilderInterface $builder): void
    {
        $builder
            ->add('NewsletterTitle', TextType::class, array('label' => 'Titre', 'attr' => array('placeholder' => 'Subject'), 'row_attr' => array('class' => 'form-floating')))
            ->add('NewsletterText', TextareaType::class, array('attr' => array('class' => 'tinymce form-floating'), 'required' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Enregistrer'))
        ;
    }
}
