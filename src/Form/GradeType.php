<?php
// src/Form/ExamType.php
namespace App\Form;

use App\Service\ListData;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GradeType
 * @package App\Form
 */
class GradeType extends AbstractType
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
            case 'Search':
                $this->search($builder);
                break;
            case 'Application':
                $this->application($builder, $options['formData']);
                break;
            case 'PaymentDate':
                $this->paymentDate($builder);
                break;
            case 'Rank':
                $this->rank($builder);
                break;
            case 'Reject':
                $this->reject($builder);
                break;
            case 'Schedule':
                $this->schedule($builder);
                break;
            case 'GradeValidate':
                $this->gradeValidate($builder, $options['formData']);
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
            ->add('GradeSessionDate', DateType::class, array('label' => 'Date Session', 'widget' => 'single_text', 'attr' => array('placeholder' => 'GradeSessionDate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('GradeSessionType', ChoiceType::class, array('label' => 'Type de Session', 'multiple' => false, 'expanded' => false, 'choices' => $list->getSessionType(0), 'attr' => array('placeholder' => 'GradeSessionType'), 'row_attr' => array('class' => 'form-floating'), 'placeholder' => 'Choisissez un type de session'))
            ->add('GradeSessionOpen', DateType::class, array('label' => 'Début des inscriptions', 'widget' => 'single_text', 'attr' => array('placeholder' => 'GradeSessionOpen'), 'row_attr' => array('class' => 'form-floating')))
            ->add('GradeSessionClose', DateType::class, array('label' => 'Fin des inscriptions', 'widget' => 'single_text', 'attr' => array('placeholder' => 'GradeSessionClose'), 'row_attr' => array('class' => 'form-floating')))
            ->add('GradeSessionPlace', TextType::class, array('label' => 'Nom de la salle', 'required' => false, 'attr' => array('placeholder' => 'GradeSessionPlace'), 'row_attr' => array('class' => 'form-floating')))
            ->add('GradeSessionStreet', TextType::class, array('label' => 'Adresse', 'required' => false, 'attr' => array('placeholder' => 'GradeSessionStreet'), 'row_attr' => array('class' => 'form-floating')))
            ->add('GradeSessionZip', IntegerType::class, array('label' => 'Code postal', 'required' => false, 'attr' => array('placeholder' => 'GradeSessionZip'), 'row_attr' => array('class' => 'form-floating')))
            ->add('GradeSessionCity', TextType::class, array('label' => 'Localité', 'required' => false, 'attr' => array('placeholder' => 'GradeSessionCity'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => $submitLabel, 'attr' => array('class' => 'btn btn-primary')))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function search(FormBuilderInterface $builder): void
    {
        $builder
            ->add('CandidateId', TextType::class, array('label' => 'N° de licence ou Nom/Prénom', 'required' => true, 'mapped' => false, 'attr' => array('placeholder' => 'CandidateId'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Ajouter/Chercher'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function application(FormBuilderInterface $builder, array $data): void
    {
        $list = new ListData();

        $builder
            ->add('GradeSessionCandidateRank', ChoiceType::class, array('label' => 'Grade présenté', 'multiple' => false, 'expanded' => false, 'choices' => $list->getGradeDan(), 'attr' => array('placeholder' => 'GradeSessionCandidateRank'), 'row_attr' => array('class' => 'form-floating'), 'placeholder' => 'Choisissez un grade'))
            ->add('GradeSessionCandidateComment', TextareaType::class, array('label' => 'Remarque(s)', 'required' => false, 'attr' => array('placeholder' => 'Remarque(s)', 'rows' => '3'), 'row_attr' => array('class' => 'form-floating')));

        if ($data['Type'] == 1)
        {
            $builder->add('Condition', CheckboxType::class, array('label' => 'La participation aux examens implique l\'engagement de ne faire aucun commentaire en public à propos de leur déroulement.', 'mapped' => false, 'required' => true));
        }

        $builder->add('Submit', SubmitType::class, array('label' => 'Ajouter la candidature'));
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function paymentDate(FormBuilderInterface $builder): void
    {
        $builder
            ->add('GradeSessionCandidatePaymentDate', DateType::class, array('label' => 'Date de paiement', 'widget' => 'single_text', 'attr' => array('placeholder' => 'GradeSessionCandidatePaymentDate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Valider', 'attr' => array('class' => 'btn btn-primary')))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function reject(FormBuilderInterface $builder): void
    {
        $builder
            ->add('GradeSessionCandidateStaffComment', TextareaType::class, array('label' => 'Motif(s) du refus', 'required' => true, 'attr' => array('placeholder' => 'Motif(s) du refus', 'rows' => '3'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Delete', CheckboxType::class, array('label' => 'Activer refus', 'mapped' => false, 'required' => true, 'label_attr' => array('class' => 'checkbox-switch')))
            ->add('Submit', SubmitType::class, array('label' => 'Refuser la candidature', 'attr' => array('class' => 'btn btn-danger')))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function rank(FormBuilderInterface $builder): void
    {
        $list = new ListData();

        $builder
            ->add('GradeSessionCandidateRank', ChoiceType::class, array('label' => 'Grade présenté', 'multiple' => false, 'expanded' => false, 'choices' => $list->getGradeDan(), 'attr' => array('placeholder' => 'GradeSessionCandidateRank'), 'row_attr' => array('class' => 'form-floating'), 'placeholder' => 'Choisissez un grade'))
            ->add('Submit', SubmitType::class, array('label' => 'Valider la candidature'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function schedule(FormBuilderInterface $builder): void
    {
        $builder
            ->add('GradeSessionShodan', TextType::class, array('label' => 'Début de la session Shodan', 'mapped' => false, 'required' => false, 'attr' => array('placeholder' => 'GradeSessionShodan'), 'row_attr' => array('class' => 'form-floating')))
            ->add('GradeSessionNidan', TextType::class , array('label' => 'Début de la session Nidan' , 'mapped' => false, 'required' => false, 'attr' => array('placeholder' => 'GradeSessionNidan') , 'row_attr' => array('class' => 'form-floating')))
            ->add('GradeSessionSandan', TextType::class, array('label' => 'Début de la session Sandan', 'mapped' => false, 'required' => false, 'attr' => array('placeholder' => 'GradeSessionSandan'), 'row_attr' => array('class' => 'form-floating')))
            ->add('GradeSessionYondan', TextType::class, array('label' => 'Début de la session Yondan', 'mapped' => false, 'required' => false, 'attr' => array('placeholder' => 'GradeSessionYondan'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Envoyer'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function gradeValidate(FormBuilderInterface $builder, array $data): void
    {
        $list = new ListData();

        if ($data['Action'] == 'Validate')
        {
            $builder
                ->add('GradeSessionCandidateJury', TextType::class, array('label' => 'N° de table', 'required' => false, 'attr' => array('placeholder' => 'GradeSessionCandidateJury'), 'row_attr' => array('class' => 'form-floating')))
                ->add('GradeSessionCandidatePosition', IntegerType::class, array('label' => 'Ordre de passage', 'required' => true, 'attr' => array('placeholder' => 'GradeSessionCandidatePosition'), 'row_attr' => array('class' => 'form-floating')))
                ->add('GradeSessionCandidateResult', ChoiceType::class, array('label' => 'Grade présenté', 'multiple' => false, 'required' => false, 'expanded' => false, 'choices' => $list->getExamResult(0), 'attr' => array('placeholder' => 'GradeSessionCandidateResult'), 'row_attr' => array('class' => 'form-floating'), 'placeholder' => 'Choisissez un résultat'))
                ->add('GradeSessionCandidateStaffComment', TextareaType::class, array('label' => 'Commentaire(s)', 'required' => false, 'attr' => array('placeholder' => 'Commentaire(s)', 'rows' => '3'), 'row_attr' => array('class' => 'form-floating')))
                ->add('Submit', SubmitType::class, array('label' => 'Enregistrer'))
            ;
        }
        else
        {
            $builder
                ->add('Submit', SubmitType::class, array('label' => 'Renvoyer dans la liste d\'attente', 'attr' => array('class' => 'btn btn-danger')))
            ;
        }

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
