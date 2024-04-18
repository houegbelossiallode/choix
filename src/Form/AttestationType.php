<?php

namespace App\Form;

use App\Entity\Attestation;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AttestationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $Entreprise = $options['Entreprise'];
        $builder
        ->add('contrat_stage',FileType::class, [
            'label' => "Uploader votre curriculum vitae",

            // unmapped means that this field is not associated to any entity property
            'mapped' => false,

            // make it optional so you don't have to re-upload the PDF file
            // every time you edit the Product details
            'required' => true,

            // unmapped fields can't define their validation using attributes
            // in the associated entity, so you can use the PHP constraint classes
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/png',
                        'image/jpeg',
                        'image/gif',
                        'image/webp',
                    ],
                    'mimeTypesMessage' => "L'image ne correspond pas aux contraintes",
                ])
            ],
        ])
        ->add('carte_scolaire',FileType::class, [
            'label' => "Uploader votre curriculum vitae",

            // unmapped means that this field is not associated to any entity property
            'mapped' => false,

            // make it optional so you don't have to re-upload the PDF file
            // every time you edit the Product details
            'required' => true,

            // unmapped fields can't define their validation using attributes
            // in the associated entity, so you can use the PHP constraint classes
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/png',
                        'image/jpeg',
                        'image/gif',
                        'image/webp',
                    ],
                    'mimeTypesMessage' => "L'image ne correspond pas aux contraintes",
                ])
            ],
        ])
            ->add('entreprise',EntityType::class,array(
                'class'=> User::class,
                'choice_label'=>'nom',
                'label'=> 'Entreprise',
                'group_by'=> 'nom',
                'query_builder'=> function(UserRepository $cr)
                use($Entreprise)
                {
                    return $cr->createQueryBuilder('U')
                    ->where('U.type = :Entreprise')
                    ->setParameter('Entreprise',$Entreprise)
                    ->orderBy('U.nom', 'ASC');
                   // ->getQuery()
                   // ->getResult();
                    
                    
                }
                
            ))
            ->add('Valider',SubmitType::class)
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Attestation::class,
            'Entreprise'=> 'Entreprise',
        ]);
    }
}