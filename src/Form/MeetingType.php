<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Meeting;
use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeetingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie'
            ])
            ->add('campus',EntityType::class,[
                'class'=>Campus::class,
                'choice_label' => 'name',
                'placeholder'=>'Choisir un campus',
                'required'=>true,
            ])
            ->add('place',EntityType::class,[
                'class'=>Place::class,
                'choice_label' => 'name',
                'placeholder'=>'Choisir un lieu',
                'required'=>true,
            ])
            ->add('duration')
            ->add('date', DateTimeType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d h:i')
                ]
            ])
            ->add('usersMax')
            ->add('limitDate', DateType::class, [
                'label' => 'Date limite d\'inscription',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d')
                ]
            ])
            ->add('textNote',TextType::class,[
                'label'=>'Description',
                'required'=>false,
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-custom'],
                'label' => 'Enregistrer'
            ])
            ->add('publish', SubmitType::class, [
                'attr' => ['class' => 'btn btn-custom'],
                'label' => 'Publier'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Meeting::class,
        ]);
    }
}
