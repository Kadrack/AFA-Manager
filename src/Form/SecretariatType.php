<?php
// src/Form/SecretariatType.php
namespace App\Form;

use App\Service\ListData;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class TrainingType
 * @package App\Form
 */
class SecretariatType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        switch ($options['formData']['Form'])
        {
            case 'Login':
                $this->login($builder, $options['formData']);
                break;
            case 'Payment':
                $this->payment($builder, $options['formData']);
                break;
            case 'Email':
                $this->email($builder);
                break;
            case 'Cluster':
                $this->cluster($builder, $options['formData']);
                break;
            case 'ClusterMember':
                $this->clusterMember($builder, $options['formData']);
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
    private function login(FormBuilderInterface $builder, array $data): void
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
            $builder->add('Login', TextType::class, array('label' => 'Login', 'attr' => array('placeholder' => 'Login'), 'row_attr' => array('class' => 'form-floating')));
        }

        $builder
            ->add('UserFirstname', TextType::class, array('label' => 'Prénom', 'attr' => array('placeholder' => 'Firstname'), 'row_attr' => array('class' => 'form-floating')))
            ->add('UserRealName', TextType::class, array('label' => 'Nom', 'attr' => array('placeholder' => 'Name'), 'row_attr' => array('class' => 'form-floating')))
            ->add('UserEmail', EmailType::class, array('label' => 'Adresse mail', 'attr' => array('placeholder' => 'Email'), 'row_attr' => array('class' => 'form-floating')))
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
            ->add('MemberId', IntegerType::class, array('label' => 'N° Licence', 'mapped' => false, 'attr' => array('placeholder' => 'ClubTeacherMember'), 'row_attr' => array('class' => 'form-floating')))
            ->add('MemberLicencePaymentDate', DateType::class, array('label' => 'Date de paiment', 'widget' => 'single_text', 'attr' => array('placeholder' => 'MemberLicencePaymentDate'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Submit', SubmitType::class, array('label' => $submitLabel))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function email(FormBuilderInterface $builder): void
    {
        $builder
            ->add('To', TextType::class, array('label' => 'Destinataire', 'attr' => array('placeholder' => 'To'), 'row_attr' => array('class' => 'form-floating')))
            ->add('Text', TextareaType::class)
            ->add('Submit', SubmitType::class, array('label' => 'Envoyer'))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function cluster(FormBuilderInterface $builder, array $data): void
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
            ->add('ClusterName', TextType::class, array('label' => 'Nom du groupe', 'attr' => array('placeholder' => 'ClusterName'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClusterFreeTraining', CheckboxType::class, array('label' => 'Gratuités des stages', 'required' => false, 'label_attr' => array('class' => 'checkbox-switch')))
            ->add('ClusterUseTitle', CheckboxType::class, array('label' => 'Activer les titres', 'required' => false, 'label_attr' => array('class' => 'checkbox-switch')))
            ->add('ClusterUseEmail', CheckboxType::class, array('label' => 'Activer les emails de fonction', 'required' => false, 'label_attr' => array('class' => 'checkbox-switch')))
            ->add('Submit', SubmitType::class, array('label' => $submitLabel))
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

    /**
     * @param FormBuilderInterface $builder
     * @param array $data
     */
    private function clusterMember(FormBuilderInterface $builder, array $data): void
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

        if ($data['UseTitle'])
        {
            $builder
                ->add('ClusterMemberTitle', ChoiceType::class, array('label' => 'Fonction', 'multiple' => false, 'expanded' => false, 'placeholder' => 'Choississez une fonction', 'choices' => $list->getClusterTitle(0), 'attr' => array('placeholder' => 'ClusterMemberTitle'), 'row_attr' => array('class' => 'form-floating')));
        }

        if ($data['Action'] == 'Add' || ($data['Action'] == 'Edit' && $data['IsMember']))
        {
            $builder
                ->add('ClusterMember', IntegerType::class, array('label' => 'N° Licence', 'mapped' => false, 'required' => false, 'attr' => array('placeholder' => 'ClubTeacherMember'), 'row_attr' => array('class' => 'form-floating')));
        }

        if ($data['Action'] == 'Add' || ($data['Action'] == 'Edit' && $data['IsUser']))
        {
            $builder
                ->add('ClusterMemberUser', TextType::class, array('label' => 'Login', 'mapped' => false, 'required' => false, 'attr' => array('placeholder' => 'ClusterMemberUser'), 'row_attr' => array('class' => 'form-floating')));
        }

        if ($data['Action'] == 'Add' || ($data['Action'] == 'Edit' && !$data['IsMember'] && !$data['IsUser']))
        {
            $builder->add('ClusterMemberFirstname', TextType::class, array('label' => 'Prénom', 'required' => false, 'attr' => array('placeholder' => 'ClusterMemberFirstname'), 'row_attr' => array('class' => 'form-floating')))
                ->add('ClusterMemberName', TextType::class, array('label' => 'Nom', 'required' => false, 'attr' => array('placeholder' => 'ClusterMemberName'), 'row_attr' => array('class' => 'form-floating')));
        }

        if ($data['UseEmail'])
        {
            $builder
                ->add('ClusterMemberEmail', EmailType::class, array('label' => 'Email de fonction', 'attr' => array('placeholder' => 'ClusterMemberEmail'), 'row_attr' => array('class' => 'form-floating')));
        }

        $builder
            ->add('ClusterMemberDateIn', DateType::class, array('label' => 'Date d\'entrée', 'widget' => 'single_text', 'attr' => array('placeholder' => 'ClusterMemberDateIn'), 'row_attr' => array('class' => 'form-floating')))
            ->add('ClusterMemberDateOut', DateType::class, array('label' => 'Date de sortie', 'required' => false, 'widget' => 'single_text', 'attr' => array('placeholder' => 'ClusterMemberDateOut'), 'row_attr' => array('class' => 'form-floating')));

        $builder->add('Submit', SubmitType::class, array('label' => $submitLabel));
    }
}
