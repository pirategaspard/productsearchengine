<?php

namespace App\Form;

use App\Entity\Source;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class SourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('id',HiddenType::class,['required' => true])
            ->add('title',TextType::class,['required' => true])
            ->add('url',TextType::class,['required' => true])
            ->add('dateadded',HiddenType::class,['required' => false, 'data'=>date('Y-m-d H:i:s')])
            ->add('save', SubmitType::class, array('label' => 'Save'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {        
        $resolver->setDefaults([
            'data_class' => Source::class,
            //'empty_data' => ['dateadded'=>new \DateTime('now')],			
        ]);        
    }
}
