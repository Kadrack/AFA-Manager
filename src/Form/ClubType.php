<?php
// src/Form/ClubType.php
namespace App\Form;

use App\Entity\ClubDojo;

use App\Service\ListData;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ClubType
 * @package App\Form
 */
class ClubType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        switch ($options['formData']['Form'])
        {
            case 'Club':
                $this->club($builder, $options['formData']);
                break;
            case 'Member':
                $this->member($builder, $options['formData']);
                break;
            case 'Payment':
                $this->payment($builder, $options['formData']);
                break;
            case 'Teacher':
                $this->teacher($builder, $options['formData']);
                break;
            case 'Mailing':
                $this->mailing($builder, $options['formData']);
                break;
            case 'Dojo':
                $this->dojo($builder, $options['formData']);
                break;
            case 'Class':
                $this->class($builder, $options['formData']);
                break;
            case 'History':
                $this->history($builder);
                break;
            case 'Manager':
                $this->manager($builder, $options['formData']);
                break;
            case 'Lesson':
                $this->lesson($builder, $options['formData']);
                break;
            case 'LessonOld':
                $this->lessonOld($builder);
                break;
            case 'SecretariatOld':
                $this->secretariatOld($builder);
                break;
            case 'Subscription':
                $this->subscription($builder);
                break;
            case 'Association':
                $this->association($builder);
                break;
            case 'Socials':
                $this->socials($builder);
                break;
            case 'Website':
                $this->website($builder);
                break;
            case 'Commitee':
                $this->commitee($builder);
                break;
            case 'Photo':
                $this->photo($builder);
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
    private function club(FormBuilderInterface $builder, array $data): void
    {
        $list = new ListData();

        if ($data['Action'] == 'Add')
        {
            $submitLabel = 'Créer';
        }
        elseif ($data['Action'] == 'Edit')
        {
            $submitLabel = 'Modifier';
        }

        $builder
            ->add('ClubId', IntegerType::class, array('label' => 'Numéro du club', 'attr' => array('placeholder' => 'ClubId'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubName', TextType::class, array('label' => 'Nom du club', 'attr' => array('placeholder' => 'ClubName'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubAddress', TextType::class, array('label' => 'Adresse', 'attr' => array('placeholder' => 'ClubAddress'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubZip', IntegerType::class, array('label' => 'Code postal', 'attr' => array('placeholder' => 'ClubZip'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubCity', TextType::class, array('label' => 'Localité', 'attr' => array('placeholder' => 'ClubCity'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubProvince', ChoiceType::class, array('label' => 'Province', 'multiple' => false, 'expanded' => false, 'placeholder' => 'Choississez une province', 'choices' => $list->getProvince(0), 'attr' => array('placeholder' => 'ClubProvince'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubCreation', DateType::class, array('label' => 'Date de création', 'widget' => 'single_text', 'mapped' => false, 'attr' => array('placeholder' => 'ClubCreation'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubType', ChoiceType::class, array('label' => 'Type d\'association', 'multiple' => false, 'expanded' => false, 'placeholder' => 'Choississez le type d\'association', 'choices' => $list->getClubType(0), 'attr' => array('placeholder' => 'ClubType'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => $submitLabel))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function member(FormBuilderInterface $builder, array $data): void
    {
        $list = new ListData();

        if ($data['Action'] == 'Add')
        {
            $submitLabel = 'Créer';
        }
        elseif ($data['Action'] == 'Edit')
        {
            $submitLabel = 'Modifier';
        }

        $builder
            ->add('MemberFirstname', TextType::class, array('label' => 'Prénom', 'attr' => array('placeholder' => 'MemberFirstname'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MemberName', TextType::class, array('label' => 'Nom', 'attr' => array('placeholder' => 'MemberName'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MemberPhoto', FileType::class, array('label' => 'Photo', 'required'=> false, 'data_class' => null, 'empty_data' => ''))
            ->add('MemberSex', ChoiceType::class, array('label' => 'Sexe', 'multiple' => false, 'expanded' => false, 'placeholder' => 'Choississez le sexe du membre', 'choices' => $list->getSex(0), 'attr' => array('placeholder' => 'MemberSex'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MemberAddress', TextType::class, array('label' => 'Adresse', 'attr' => array('placeholder' => 'MemberAddress'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MemberZip', IntegerType::class, array('label' => 'Code postal', 'attr' => array('placeholder' => 'MemberZip'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MemberCity', TextType::class, array('label' => 'Localité', 'attr' => array('placeholder' => 'MemberCity'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MemberCountry', CountryType::class, array('label' => 'Pays', 'choice_translation_locale' => 'fr', 'preferred_choices' => array('BE', 'FR'), 'placeholder' => 'Choississez un pays', 'attr' => array('placeholder' => 'MemberCountry'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MemberEmail', EmailType::class, array('label' => 'Adresse Email', 'required' => false, 'attr' => array('placeholder' => 'MemberEmail'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MemberPhone', TextType::class, array('label' => 'N° téléphone', 'required' => false, 'attr' => array('placeholder' => 'MemberPhone'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MemberBirthday', BirthdayType::class, array('label' => 'Date de naissance'))
            ->add('GradeRank', ChoiceType::class, array('label' => 'Grade', 'placeholder' => 'Choississez un grade', 'choices' => $list->getGrade(), 'required' => false, 'mapped' => false, 'attr' => array('placeholder' => 'GradeRank'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MemberLicenceMedicalCertificate', DateType::class, array('label' => 'Date du certificat ou du consentement', 'widget' => 'single_text', 'mapped' => false, 'attr' => array('placeholder' => 'MemberLicenceMedicalCertificate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => $submitLabel))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function payment(FormBuilderInterface $builder, array $data): void
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
            ->add('LicenceNumber', TextType::class, array('label' => 'N° de licence (séparé par un virgule)', 'mapped' => false, 'attr' => array('placeholder' => 'LicenceNumber'), 'row_attr' => array('class' => 'form-floating')))
            ->add('PaymentDate', DateType::class, array('label' => 'Date de paiment', 'widget' => 'single_text', 'mapped' => false, 'attr' => array('placeholder' => 'PaymentDate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => $submitLabel))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function teacher(FormBuilderInterface $builder, array $data): void
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
            ->add('ClubTeacherTitle', ChoiceType::class, array('label' => 'Fonction', 'multiple' => false, 'expanded' => false, 'required' => false, 'placeholder' => 'Choississez une fonction', 'choices' => $list->getTeacherTitle(0), 'attr' => array('placeholder' => 'ClubTeacherTitle'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubTeacherType', ChoiceType::class, array('label' => 'Type', 'multiple' => false, 'expanded' => false, 'required' => false, 'placeholder' => 'Choississez un type', 'choices' => $list->getTeacherType(0), 'attr' => array('placeholder' => 'ClubTeacherType'), 'row_attr' => array('class' => 'form-floating')));

        if ($data['Action'] == 'Add' || ($data['Action'] == 'Edit' && $data['IsMember']))
        {
            $builder
                ->add('ClubTeacherMember', IntegerType::class, array('label' => 'N° Licence', 'required' => false, 'mapped' => false, 'attr' => array('placeholder' => 'ClubTeacherMember'), 'row_attr' => array('class' => 'form-floating')));
        }

        if ($data['Action'] == 'Add' || ($data['Action'] == 'Edit' && !$data['IsMember']))
        {
            $builder->add('ClubTeacherFirstname', TextType::class, array('label' => 'Prénom', 'required' => false, 'attr' => array('placeholder' => 'ClubTeacherFirstname'), 'row_attr' => array('class' => 'form-floating')))
                ->add('ClubTeacherName', TextType::class, array('label' => 'Nom', 'required' => false, 'attr' => array('placeholder' => 'ClubTeacherName'), 'row_attr' => array('class' => 'form-floating')))
                ->add('ClubTeacherGrade', ChoiceType::class, array('label' => 'Grade', 'multiple' => false, 'expanded' => false, 'placeholder' => 'Choississez un grade', 'choices' => $list->getGrade(), 'required' => false, 'attr' => array('placeholder' => 'ClubTeacherGrade'), 'row_attr' => array('class' => 'form-floating')))
                ->add('ClubTeacherTitleAikikai', ChoiceType::class, array('label' => 'Titre enseignement', 'multiple' => false, 'expanded' => false, 'placeholder' => 'Choississez un titre d\'enseignement', 'choices' => $list->getTitleAikikai(0), 'required' => false, 'attr' => array('placeholder' => 'ClubTeacherGradeTitleAikikai'), 'row_attr' => array('class' => 'form-floating')))
                ->add('ClubTeacherTitleAdeps', ChoiceType::class, array('label' => 'Niveau de formation ADEPS', 'multiple' => false, 'expanded' => false, 'placeholder' => 'Choississez un rang de formation', 'choices' => $list->getFormation(0), 'required' => false, 'attr' => array('placeholder' => 'ClubTeacherGradeTitleAdeps'), 'row_attr' => array('class' => 'form-floating')));
        }

        $builder->add('Submit', SubmitType::class, array('label' => $submitLabel));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function mailing(FormBuilderInterface $builder, array $data): void
    {
        $data = $data['Data'];

        $builder
            ->add('To', ChoiceType::class, array('label' => 'Destinataire', 'multiple' => false, 'expanded' => false, 'placeholder' => 'Choississez les destinataires', 'choices' => $data['List'], 'required' => false, 'attr' => array('placeholder' => 'To'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Subject', TextType::class, array('label' => 'Sujet', 'attr' => array('placeholder' => 'Subject'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Text', TextareaType::class, array('attr' => array('class' => 'tinymce form-floating'), 'required' => false))
            ->add('Attachment', FileType::class, array('label' => 'Fichier attaché', 'mapped' => false, 'required' => false, 'multiple' => true, 'data_class' => null, 'empty_data' => ''))
            ->add('Submit', SubmitType::class, array('label' => 'Envoyer'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function dojo(FormBuilderInterface $builder, array $data): void
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
            ->add('ClubDojoName', TextType::class, array('label' => 'Nom de la salle', 'required' => false, 'attr' => array('placeholder' => 'ClubDojoName'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubDojoStreet', TextType::class, array('label' => 'Adresse', 'attr' => array('placeholder' => 'ClubDojoStreet'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubDojoZip', IntegerType::class, array('label' => 'Code postal', 'attr' => array('placeholder' => 'ClubDojoZip'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubDojoCity', TextType::class, array('label' => 'Localité', 'attr' => array('placeholder' => 'ClubDojoCity'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubDojoTatamis', IntegerType::class, array('label' => 'Tatamis (m²)', 'required' => false, 'attr' => array('placeholder' => 'ClubDojoTatamis'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubDojoDEA', ChoiceType::class, array('label' => 'Présence d\'un DEA', 'multiple' => false, 'expanded' => false, 'placeholder' => 'Indiquez la présence d\'un DEA', 'choices' => array('Non' => 0, 'Oui' => 1), 'attr' => array('placeholder' => 'ClubDojoDEA'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => $submitLabel))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function class(FormBuilderInterface $builder, array $data): void
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
            ->add('ClubClassDay', ChoiceType::class, array('label' => 'Jour : ', 'placeholder' => 'Choississez un jour', 'choices' => $list->getDay(0)))
            ->add('ClubClassStartingHour', TimeType::class, array ('label' => 'Début : '))
            ->add('ClubClassEndingHour', TimeType::class, array ('label' => 'Fin : '))
            ->add('ClubClassType', ChoiceType::class, array('label' => 'Type de cours : ', 'placeholder' => 'Choississez un type de cours', 'choices' => $list->getClassType(0)))
            ->add('ClubClassDojo', EntityType::class, array ('label' => 'Adresse : ', 'class' => ClubDojo::class, 'choices' => $data['Choices'], 'choice_label' => 'club_dojo_street'))
            ->add('Submit', SubmitType::class, array('label' => $submitLabel))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function history(FormBuilderInterface $builder): void
    {
        $builder
            ->add('CreationDate', DateType::class, array('label' => 'Date de création du club', 'widget' => 'single_text', 'mapped' => false, 'attr' => array('placeholder' => 'PaymentDate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MembershipDate', DateType::class, array('label' => 'Date d\'affiliation', 'widget' => 'single_text', 'required' => false, 'mapped' => false, 'attr' => array('placeholder' => 'PaymentDate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('RetireDate', DateType::class, array('label' => 'Date de retrait', 'widget' => 'single_text', 'required' => false, 'mapped' => false, 'attr' => array('placeholder' => 'PaymentDate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function manager(FormBuilderInterface $builder, array $data): void
    {
        if ($data['Action'] == 'Add')
        {
            $submitLabel = 'Ajouter';
        }
        elseif ($data['Action'] == 'Edit')
        {
            $submitLabel = 'Modifier';
        }

        if ($data['Action'] == 'Add')
        {
            $builder
                ->add('ClubManagerMember', IntegerType::class, array('label' => 'N° Licence', 'required' => false, 'mapped' => false, 'attr' => array('placeholder' => 'ClubTeacherMember'), 'row_attr' => array('class' => 'form-floating')))
                ->add('ClubManagerLogin', TextType::class, array('label' => 'Login', 'required' => false, 'mapped' => false, 'attr' => array('placeholder' => 'ClubTeacherMember'), 'row_attr' => array('class' => 'form-floating')));
        }

        $builder
            ->add('ClubManagerIsMain', CheckboxType::class, array('label' => 'Définir comme gestionnaire principal', 'required' => false, 'mapped' => true, 'label_attr' => array('class' => 'checkbox-switch')))
            ->add('Submit', SubmitType::class, array('label' => $submitLabel))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function lesson(FormBuilderInterface $builder, array $data): void
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
            ->add('LessonDate', DateType::class, array('label' => 'Date du cours', 'widget' => 'single_text', 'attr' => array('placeholder' => 'TrainingSessionDate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('LessonStartingHour', TimeType::class, array ('label' => 'Début'))
            ->add('LessonEndingHour', TimeType::class, array ('label' => 'Fin', 'mapped' => false))
            ->add('LessonType', ChoiceType::class, array('label' => 'Type de cours', 'choices' => $list->getClassType(0)))
            ->add('Submit', SubmitType::class, array('label' => $submitLabel))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function lessonOld(FormBuilderInterface $builder): void
    {
        $builder
            ->add('Date', DateType::class, array('label' => 'Date recherchée', 'widget' => 'single_text', 'attr' => array('placeholder' => 'Date'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Valider'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function secretariatOld(FormBuilderInterface $builder): void
    {
        $builder
            ->add('Date', DateType::class, array('label' => 'Date recherchée', 'widget' => 'single_text', 'attr' => array('placeholder' => 'Date'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Valider'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function subscription(FormBuilderInterface $builder): void
    {
        $list = new ListData();

        $builder
            ->add('MemberSubscriptionList', ChoiceType::class, array('label' => 'Cours suivi', 'choices' => $list->getClassType(0)))
            ->add('MemberSubscriptionStatus', ChoiceType::class, array('label' => 'Mode d\'expiration', 'choices' => array('Jamais' => 1, 'Date d\'expiration' => 2, 'Ne pratique plus' => 3)))
            ->add('MemberSubscriptionValidity', DateType::class, array('label' => 'Date d\'expiration', 'widget' => 'single_text', 'required' => false, 'attr' => array('placeholder' => 'Date'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function association(FormBuilderInterface $builder): void
    {
        $list = new ListData();

        $builder
            ->add('ClubName', TextType::class, array('label' => 'Nom du club', 'attr' => array('placeholder' => 'ClubName'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubType', ChoiceType::class, array('label' => 'Type d\'association', 'multiple' => false, 'expanded' => false, 'placeholder' => 'Choississez un type d\'association', 'choices' => $list->getClubType(0), 'attr' => array('placeholder' => 'ClubType'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubAddress', TextType::class, array('label' => 'Adresse', 'attr' => array('placeholder' => 'ClubAddress'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubZip', IntegerType::class, array('label' => 'Code postal', 'attr' => array('placeholder' => 'ClubZip'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubCity', TextType::class, array('label' => 'Localité', 'attr' => array('placeholder' => 'ClubCity'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubIban', TextType::class, array('label' => 'N° de compte (IBAN)', 'required' => false, 'attr' => array('placeholder' => 'ClubIban'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubBceNumber', TextType::class, array('label' => 'N° d\'entreprise', 'required' => false, 'attr' => array('placeholder' => 'ClubBceNumber'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function socials(FormBuilderInterface $builder): void
    {
        $builder
            ->add('ClubFacebook', TextType::class, array('label' => 'Page Facebook', 'required' => false, 'attr' => array('placeholder' => 'ClubFb'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubInstagram', TextType::class, array('label' => 'Page Instagram', 'required' => false, 'attr' => array('placeholder' => 'ClubFb'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubYoutube', TextType::class, array('label' => 'Page Youtube', 'required' => false, 'attr' => array('placeholder' => 'ClubFb'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function website(FormBuilderInterface $builder): void
    {
        $builder
            ->add('ClubUrl', TextType::class, array('label' => 'Site internet', 'required' => false, 'attr' => array('placeholder' => 'ClubUrl'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubContactPublic', TextType::class, array('label' => 'Personne de contact', 'required' => false, 'attr' => array('placeholder' => 'ClubContactPublic'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubPhonePublic', TextType::class, array('label' => 'Numéro de téléphone', 'required' => false, 'attr' => array('placeholder' => 'ClubPhonePublic'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubEmailPublic', EmailType::class, array('label' => 'Email publique', 'required' => false, 'attr' => array('placeholder' => 'ClubEmailPublic'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function commitee(FormBuilderInterface $builder): void
    {
        $builder
            ->add('ClubPresident', TextType::class, array('label' => 'Nom du président', 'required' => false, 'attr' => array('placeholder' => 'ClubPresident'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubSecretary', TextType::class, array('label' => 'Nom du secrétaire', 'required' => false, 'attr' => array('placeholder' => 'ClubSecretary'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClubTreasurer', TextType::class, array('label' => 'Nom du trésorier', 'required' => false, 'attr' => array('placeholder' => 'ClubTreasurer'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => 'Enregistrer'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function photo(FormBuilderInterface $builder): void
    {
        $builder
            ->add('ClubPhoto', FileType::class, array('label' => 'Photo', 'mapped' => false))
            ->add('Submit', SubmitType::class, array('label' => 'Modifier'))
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
