<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Meeting;
use App\Entity\Place;
use App\Entity\StateMeeting;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeetingCancelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('date')
            ->add('usersMax')
            ->add('textNote')
            ->add('duration')
            ->add('limitDate')
            ->add('annulation_reason')
            ->add('organizer', EntityType::class, [
                'class' => User::class,
'choice_label' => 'id',
            ])
            ->add('participants', EntityType::class, [
                'class' => User::class,
'choice_label' => 'id',
'multiple' => true,
            ])
            ->add('state', EntityType::class, [
                'class' => StateMeeting::class,
'choice_label' => 'id',
            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
'choice_label' => 'id',
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
'choice_label' => 'id',
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
