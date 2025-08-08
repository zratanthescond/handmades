<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FileInput extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ...
            ->add('brochure', FileType::class, [
                'label' => 'Brochure (PDF file)',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'application/vnd.ms-excel',
                            'application/vnd.ms-excel.addin.macroEnabled.12',
                            'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
                            'application/vnd.ms-excel.sheet.macroEnabled.12',
                            'application/vnd.ms-excel.template.macroEnabled.12',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'

                        ],
                        'mimeTypesMessage' => 'Please upload a valid Xls document',
                    ])
                ],
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'save'],
            ]);
            // ...
        ;
    }
}
