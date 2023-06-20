<?php
// src/Form/MemberType.php
namespace App\Form;

use App\Entity\Club;

use App\Service\ListData;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MemberType
 * @package App\Form
 */
class MemberType extends AbstractType
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
            case 'Licence':
                $this->licence($builder, $options['formData']);
                break;
            case 'Grade':
                $this->grade($builder, $options['formData']);
                break;
            case 'StartPractice':
                $this->startPractice($builder);
                break;
            case 'Title':
                $this->title($builder, $options['formData']);
                break;
            case 'Formation':
                $this->formation($builder, $options['formData']);
                break;
            case 'Photo':
                $this->photo($builder);
                break;
            case 'Name':
                $this->name($builder);
                break;
            case 'Birthday':
                $this->birthday($builder);
                break;
            case 'Sex':
                $this->sex($builder);
                break;
            case 'Address':
                $this->address($builder);
                break;
            case 'Phone':
                $this->phone($builder);
                break;
            case 'Email':
                $this->email($builder);
                break;
            case 'Mailing':
                $this->mailing($builder);
                break;
            case 'AikikaiId':
                $this->aikikaiId($builder);
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
            ->add('Submit', SubmitType::class, array('label' => 'Rechercher'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function licence(FormBuilderInterface $builder, array $data): void
    {
        if ($data['Action'] == 'Add')
        {
            $submitLabel = 'Renouveller';
        }
        elseif ($data['Action'] == 'Edit')
        {
            $submitLabel = 'Modifier';
        }

        $builder
            ->add('MemberLicenceClub', EntityType::class, array('label' => 'Club', 'class' => Club::class, 'choice_label' => 'club_id', 'attr' => array('placeholder' => 'MemberLicenceClub'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MemberLicenceDeadline', DateType::class, array('label' => 'Date échéance', 'widget' => 'single_text', 'attr' => array('placeholder' => 'MemberLicenceDeadline'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => $submitLabel))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function grade(FormBuilderInterface $builder, array $data): void
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
            ->add('GradeRank', ChoiceType::class, array('label' => 'Grade', 'multiple' => false, 'expanded' => false, 'choices' => $data['Choices'], 'attr' => array('placeholder' => 'GradeRank'), 'row_attr' => array('class' => 'form-floating'), 'placeholder' => 'Choisissez un grade', 'disabled' => $data['IsFromExamSession']))
            ->add('GradeStatus', ChoiceType::class, array('label' => 'Type', 'multiple' => false, 'expanded' => false, 'choices' => $list->getGradeType(0), 'attr' => array('placeholder' => 'GradeRank'), 'row_attr' => array('class' => 'form-floating'), 'disabled' => $data['IsFromExamSession']))
            ->add('GradeDate', DateType::class, array('label' => 'Date', 'widget' => 'single_text', 'attr' => array('placeholder' => 'GradeDate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('GradeCertificate', TextType::class, array('label' => 'N° Diplôme', 'required' => false, 'attr' => array('placeholder' => 'GradeCertificate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => $submitLabel, 'attr' => array('class' => 'btn btn-primary')))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function startPractice(FormBuilderInterface $builder): void
    {
        $builder
            ->add('MemberStartPractice', DateType::class, array('label' => 'Début de pratique ', 'widget' => 'single_text', 'attr' => array('placeholder' => 'MemberStartPractice'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function title(FormBuilderInterface $builder, array $data): void
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
            ->add('TitleRank', ChoiceType::class, array('label' => 'Titre', 'multiple' => false, 'expanded' => false, 'choices' => $list->getTitleAikikai(0), 'attr' => array('placeholder' => 'GradeTitleRank'), 'row_attr' => array('class' => 'form-floating'), 'placeholder' => 'Choisissez un titre'))
            ->add('TitleDate', DateType::class, array('label' => 'Date', 'widget' => 'single_text', 'attr' => array('placeholder' => 'GradeTitleDate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('TitleCertificate', TextType::class, array('label' => 'N° Diplôme', 'required' => false, 'attr' => array('placeholder' => 'GradeTitleCertificate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => $submitLabel, 'attr' => array('class' => 'btn btn-primary')))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function formation(FormBuilderInterface $builder, array $data): void
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
            ->add('FormationRank', ChoiceType::class, array('label' => 'Titre', 'multiple' => false, 'expanded' => false, 'choices' => $list->getFormation(0), 'attr' => array('placeholder' => 'FormationRank'), 'row_attr' => array('class' => 'form-floating'), 'placeholder' => 'Choisissez un rang de formation'))
            ->add('FormationDate', DateType::class, array('label' => 'Date', 'widget' => 'single_text', 'attr' => array('placeholder' => 'FormationDate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('FormationCertificate', TextType::class, array('label' => 'N° Diplôme', 'required' => false, 'attr' => array('placeholder' => 'FormationCertificate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => $submitLabel, 'attr' => array('class' => 'btn btn-primary')))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function photo(FormBuilderInterface $builder): void
    {
        $builder
            ->add('MemberPhoto', FileType::class, array('label' => 'Photo', 'data_class' => null, 'empty_data' => ''))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function name(FormBuilderInterface $builder): void
    {
        $builder
            ->add('MemberFirstname', TextType::class, array('label' => 'Prénom', 'attr' => array('placeholder' => 'MemberFirstname'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MemberName', TextType::class, array('label' => 'Nom', 'attr' => array('placeholder' => 'MemberName'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function birthday(FormBuilderInterface $builder): void
    {
        $builder
            ->add('MemberBirthday', BirthdayType::class, array('label' => 'Date de naissance'))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function sex(FormBuilderInterface $builder): void
    {
        $list = new ListData();

        $builder
            ->add('MemberSex', ChoiceType::class, array('label' => 'Sexe', 'multiple' => false, 'expanded' => false, 'choices' => $list->getSex(0), 'attr' => array('placeholder' => 'MemberSex'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function address(FormBuilderInterface $builder): void
    {
        $builder
            ->add('MemberAddress', TextType::class, array('label' => 'Adresse', 'attr' => array('placeholder' => 'MemberAddress'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MemberZip', IntegerType::class, array('label' => 'Code postal', 'attr' => array('placeholder' => 'MemberZip'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MemberCity', TextType::class, array('label' => 'Localité', 'attr' => array('placeholder' => 'MemberCity'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MemberCountry', CountryType::class, array('label' => 'Pays : ', 'choice_translation_locale' => 'fr', 'preferred_choices' => array('BE', 'FR'), 'attr' => array('placeholder' => 'MemberCountry'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function phone(FormBuilderInterface $builder): void
    {
        $builder
            ->add('MemberPhone', TextType::class, array('label' => 'Numéro de téléphone', 'attr' => array('placeholder' => 'MemberPhone'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function email(FormBuilderInterface $builder): void
    {
        $builder
            ->add('MemberEmail', EmailType::class, array('label' => 'Adresse Email', 'attr' => array('placeholder' => 'MemberEmail'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function aikikaiId(FormBuilderInterface $builder): void
    {
        $builder
            ->add('MemberAikikaiId', IntegerType::class, array('label' => 'N° licence aïkikaï', 'attr' => array('placeholder' => 'MemberAikikaiId'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function mailing(FormBuilderInterface $builder): void
    {
        $builder
            ->add('Manager', CheckboxType::class, array('label' => 'Copie au(x) Dojo-cho(s)', 'mapped' => false, 'required' => false, 'label_attr' => array('class' => 'checkbox-switch')))
            ->add('DojoCho', CheckboxType::class, array('label' => 'Copie au(x) Gestionnaire(s)', 'mapped' => false, 'required' => false, 'label_attr' => array('class' => 'checkbox-switch')))
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
