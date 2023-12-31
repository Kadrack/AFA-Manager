<?php
// src/Form/FormationType.php
namespace App\Form;

use App\Service\ListData;

use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FormationType
 * @package App\Form
 */
class FormationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        switch ($options['formData']['Form'])
        {
            case 'Session':
                $this->session($builder, $options['formData']);
                break;
            case 'Subscription':
                $this->subscription($builder, $options['formData']);
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
        $resolver->setDefaults(array('data_class' => null, 'formData' => array()));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function session(FormBuilderInterface $builder, array $data): void
    {
        if ($data['Action'] == 'Add')
        {
            $submitLabel = 'Créer';
        }
        elseif ($data['Action'] == 'Edit')
        {
            $submitLabel = 'Modifier';
        }

        $list = new ListData();

        $builder
            ->add('FormationSessionDate', DateType::class, array('label' => 'Date Session', 'widget' => 'single_text', 'attr' => array('placeholder' => 'FormationSessionDate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('FormationSessionType', ChoiceType::class, array('label' => 'Type de Session', 'multiple' => false, 'expanded' => false, 'choices' => $list->getFormationType(0), 'attr' => array('placeholder' => 'FormationSessionType'), 'row_attr' => array('class' => 'form-floating'), 'placeholder' => 'Choisissez un type de session'))
            ->add('FormationSessionOpen', DateType::class, array('label' => 'Début des inscriptions', 'widget' => 'single_text', 'attr' => array('placeholder' => 'FormationSessionOpen'), 'row_attr' => array('class' => 'form-floating')))
            ->add('FormationSessionClose', DateType::class, array('label' => 'Fin des inscriptions', 'widget' => 'single_text', 'attr' => array('placeholder' => 'FormationSessionClose'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => $submitLabel, 'attr' => array('class' => 'btn btn-primary')))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function subscription(FormBuilderInterface $builder, array $data): void
    {
        $list = new ListData();

        $builder
            ->add('FormationSessionCandidateFirstname', TextType::class, array('label' => 'Prénom', 'attr' => array('placeholder' => 'FormationSessionCandidateFirstname'), 'row_attr' => array('class' => 'form-floating')))
            ->add('FormationSessionCandidateName', TextType::class, array('label' => 'Nom', 'attr' => array('placeholder' => 'FormationSessionCandidateName'), 'row_attr' => array('class' => 'form-floating')));

        if ($data['formType'] == 1)
        {
            $builder
                ->add('FormationSessionCandidateMember', IntegerType::class, array('label' => 'Numéro de licence', 'mapped' => false, 'attr' => array('placeholder' => 'FormationSessionCandidateMember'), 'row_attr' => array('class' => 'form-floating')));
        }
        else if ($data['formType'] == 2)
        {
            $builder
                ->add('FormationSessionCandidateBirthday', BirthdayType::class, array('label' => 'Date de naissance'))
                ->add('FormationSessionCandidateSex', ChoiceType::class, array('label' => 'Sexe', 'multiple' => false, 'expanded' => false, 'choices' => $list->getSex(0), 'attr' => array('placeholder' => 'FormationSessionCandidateSex'), 'row_attr' => array('class' => 'form-floating')))
                ->add('FormationSessionCandidateAddress', TextType::class, array('label' => 'Adresse', 'attr' => array('placeholder' => 'FormationSessionCandidateAddress'), 'row_attr' => array('class' => 'form-floating')))
                ->add('FormationSessionCandidateZip', IntegerType::class, array('label' => 'Code postal', 'attr' => array('placeholder' => 'FormationSessionCandidateZip'), 'row_attr' => array('class' => 'form-floating')))
                ->add('FormationSessionCandidateCity', TextType::class, array('label' => 'Localité', 'attr' => array('placeholder' => 'FormationSessionCandidateCity'), 'row_attr' => array('class' => 'form-floating')))
                ->add('FormationSessionCandidateCountry', CountryType::class, array('label' => 'Pays', 'choice_translation_locale' => 'fr', 'preferred_choices' => array('BE', 'FR'), 'attr' => array('placeholder' => 'FormationSessionCandidateCountry'), 'row_attr' => array('class' => 'form-floating')))
                ->add('FormationSessionCandidatePhone', TextType::class, array('label' => 'Numéro de téléphone', 'required' => false, 'attr' => array('placeholder' => 'FormationSessionCandidatePhone'), 'row_attr' => array('class' => 'form-floating')))
                ->add('FormationSessionCandidateGrade', ChoiceType::class, array('label' => 'Grade', 'required' => true, 'multiple' => false, 'expanded' => false, 'choices' => $list->getGradeFormation(0), 'attr' => array('placeholder' => 'FormationSessionCandidateGrade'), 'row_attr' => array('class' => 'form-floating')))
                ->add('FormationSessionCandidateClub', TextType::class, array('label' => 'Nom du club actuel', 'required' => true, 'attr' => array('placeholder' => 'FormationSessionCandidateClub'), 'row_attr' => array('class' => 'form-floating')))
                ->add('FormationSessionCandidateLicence', TextType::class, array('label' => 'N° de licence', 'required' => true, 'attr' => array('placeholder' => 'FormationSessionCandidateLicence'), 'row_attr' => array('class' => 'form-floating')));
        }

        $builder
            ->add('FormationSessionCandidateEmail', RepeatedType::class, array('type' => EmailType::class, 'invalid_message' => 'Les deux adresses doivent être les mêmes', 'required' => true, 'first_options' => array('label' => 'Adresse Email', 'attr' => array('placeholder' => 'FormationSessionCandidateEmail'), 'row_attr' => array('class' => 'form-floating')), 'second_options' => array('label' => 'Vérification de l\'adresse Email', 'attr' => array('placeholder' => 'FormationSessionCandidateEmail'), 'row_attr' => array('class' => 'form-floating'))))
            ->add('Captcha', Recaptcha3Type::class, ['constraints' => new Recaptcha3(), 'action_name' => 'formation', 'locale' => 'fr',])
            ->add('Submit', SubmitType::class, array('label' => 'Inscription'))
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
