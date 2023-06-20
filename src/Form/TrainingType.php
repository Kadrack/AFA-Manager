<?php
// src/Form/ClubType.php
namespace App\Form;

use App\Entity\TrainingSession;

use App\Service\ListData;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TrainingType
 * @package App\Form
 */
class TrainingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        switch ($options['formData']['Form'])
        {
            case 'Search':
                $this->search($builder);
                break;
            case 'Attendance':
                $this->attendance($builder, $options['formData']);
                break;
            case 'Session':
                $this->session($builder, $options['formData']);
                break;
            case 'Training':
                $this->training($builder, $options['formData']);
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
     */
    private function search(FormBuilderInterface $builder): void
    {
        $builder
            ->add('Search', TextType::class, array('label' => 'N° de licence ou Nom/Prénom', 'mapped' => false, 'attr' => array('placeholder' => 'Search'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter/Chercher'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function attendance(FormBuilderInterface $builder, array $data): void
    {
        $list = new ListData();

        $builder->add('TrainingAttendanceSubscribe', ChoiceType::class, array('label' => 'Session : ', 'multiple' => false, 'expanded' => false, 'choices' => array('Inscription' => 1, 'Pre-Paiement' => 2), 'mapped' => false, 'disabled' => !$data['Subscribe']));

        if ($data['Foreign'])
        {
            $builder
                ->add('TrainingAttendanceName', TextType::class, array('label' => 'Nom', 'required' => true, 'attr' => array('placeholder' => 'TrainingAttendanceName'), 'row_attr' => array('class' => 'form-floating')))
                ->add('TrainingAttendanceSex', ChoiceType::class, array('label' => 'Sexe', 'multiple' => false, 'expanded' => true, 'choices' => $list->getSex(0), 'label_attr' => array('class' => 'radio-inline')))
                ->add('TrainingAttendanceCountry', CountryType::class, array('label' => 'Pays : ', 'choice_translation_locale' => 'fr', 'preferred_choices' => array('BE', 'FR'), 'required' => true, 'placeholder' => 'Choississez un pays d\'origine'));
        }

        if ($data['Lessons'])
        {
            $builder->add('TrainingAttendanceSessions', ChoiceType::class, array('label' => 'Session : ', 'multiple' => true, 'expanded' => true, 'choices' => $data['Choices'], 'mapped' => false, 'choice_value' => 'training_session_id', 'choice_label' => 'training_session_choice_name'));
        }

        if (!$data['Free'])
        {
            $builder
                ->add('TrainingAttendancePayment', MoneyType::class, array('label' => 'Paiement', 'required' => true, 'divisor' => 100, 'mapped' => false))
                ->add('TrainingAttendancePaymentType', ChoiceType::class, array('label' => 'Mode de paiement : ', 'multiple' => false, 'expanded' => true, 'choices' => $list->getPaymentType(0), 'mapped' => false, 'label_attr' => array('class' => 'radio-inline')));
        }

        $builder->add('Submit', SubmitType::class, array('label' => $data['Edit'] ? 'Modifier' : 'Ajouter'));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function session(FormBuilderInterface $builder, array $data): void
    {
        if ($data['Action'] == 'Add')
        {
            $submitLabel = 'Ajouter';
        }
        elseif ($data['Action'] == 'Edit')
        {
            $submitLabel = 'Modifier';
        }

        $builder
            ->add('TrainingSessionDate', DateType::class, array('label' => 'Date', 'widget' => 'single_text', 'attr' => array('placeholder' => 'TrainingSessionDate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('TrainingSessionStartingHour', TimeType::class, array ('label' => 'Début', 'widget' => 'single_text', 'attr' => array('placeholder' => 'TrainingSessionStartingHour'), 'row_attr' => array('class' => 'form-floating')))
            ->add('TrainingSessionEndingHour', TimeType::class, array ('label' => 'Fin', 'widget' => 'single_text', 'attr' => array('placeholder' => 'TrainingSessionEndingHour'), 'row_attr' => array('class' => 'form-floating')));

        if ($data['Action'] != 'First')
        {
            $builder->add('Submit', SubmitType::class, array('label' => $submitLabel));
        }
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function training(FormBuilderInterface $builder, array $data): void
    {
        $list = new ListData();

        if ($data['Action'] == 'Add')
        {
            $submitLabel = 'Ajouter';
        }
        elseif ($data['Action'] == 'Edit')
        {
            $submitLabel = 'Modifier';
        }

        $builder
            ->add('TrainingName', TextType::class, array('label' => 'Nom', 'attr' => array('placeholder' => 'TrainingName'), 'row_attr' => array('class' => 'form-floating')))
            ->add('TrainingStatus', ChoiceType::class, array('label' => 'Type de stage : ', 'placeholder' => 'Choississez un type', 'choices' => $list->getTrainingStatus(0)));

        if ($data['Action'] == 'Add')
        {
            $builder->add('Session', TrainingType::class, array('label' => 'Premier Cours', 'formData' => array('Form' => 'Session', 'Action' => 'First'), 'data_class' => TrainingSession::class, 'attr' => array('placeholder' => 'Session'), 'row_attr' => array('class' => 'form-floating')));
        }

        $builder->add('Submit', SubmitType::class, array('label' => $submitLabel));
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
