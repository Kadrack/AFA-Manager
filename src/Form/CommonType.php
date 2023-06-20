<?php
// src/Form/CommonType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ClubType
 * @package App\Form
 */
class CommonType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        switch ($options['formData']['Form'])
        {
            case 'Mailing':
                $this->mailing($builder, $options['formData']);
                break;
            case 'Delete':
                $this->delete($builder);
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array('data_class' => null, 'formData' => ''));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function mailing(FormBuilderInterface $builder, array $data): void
    {
        $data = $data['Data'];

        $builder
            ->add('To', ChoiceType::class, array('label' => 'Destinataire', 'multiple' => true, 'expanded' => true, 'placeholder' => 'Choississez les destinataires', 'choices' => $data['List'], 'required' => false, 'attr' => array('placeholder' => 'To'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Subject', TextType::class, array('label' => 'Sujet', 'attr' => array('placeholder' => 'Subject'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Text', TextareaType::class, array('attr' => array('class' => 'tinymce form-floating'), 'required' => false))
            ->add('Attachment', FileType::class, array('label' => 'Fichier attaché', 'mapped' => false, 'required' => false, 'multiple' => true, 'data_class' => null, 'empty_data' => ''))
            ->add('Submit', SubmitType::class, array('label' => 'Envoyer'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function delete(FormBuilderInterface $builder): void
    {
        $builder
            ->add('Delete', CheckboxType::class, array('label' => 'Activer suppression', 'mapped' => false, 'required' => true, 'label_attr' => array('class' => 'checkbox-switch')))
            ->add('Submit', SubmitType::class, array('label' => 'Supprimer', 'attr' => array('class' => 'btn btn-danger')))
        ;
    }
}
