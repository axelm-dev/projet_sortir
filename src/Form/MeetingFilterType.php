<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use function Sodium\add;

class MeetingFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setMethod('GET')
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'multiple' => false,
                'required' => false,
            ])
            ->add('search', TextType::class, [
                'label' => 'Recherche',
                'required' => false,
            ])
            ->add('start_date', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Entre',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('end_date', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'et',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('organisateur', CheckboxType::class, [
                'required' => false,
                'label' => "Sorties que j'organise",
            ])
            ->add('inscrit', CheckboxType::class, [
                'required' => false,
                'label' => "Sorties auxquelles je suis inscrit.e",
            ])
            ->add('non_inscrit', CheckboxType::class, [
                'required' => false,
                'label' => "Sorties auxquelles je ne suis pas inscrit.e",
            ])
            ->add('state', CheckboxType::class, [
                'required' => false,
                'label' => "Sorties qui sont passées",
            ])
            ->add('rechercher', SubmitType::class, [
                'attr' => ['class' => 'btn btn-custom'],
                'label' => 'Rechercher'
            ])
            ->add('create_new', SubmitType::class, [
                'attr' => ['class' => 'btn btn-custom'],
                'label' => 'Créer une nouvelle sortie'
            ]);
    }

}
