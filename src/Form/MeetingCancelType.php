<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Meeting;
use App\Entity\Place;
use App\Entity\StateMeeting;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeetingCancelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('annulation_reason', TextType::class, [
                'label' => 'Raison de l\'annulation'
            ])
        ;
    }
}
