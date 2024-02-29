<?php

namespace App\Controller\Admin;

use App\Entity\Meeting;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Iterable_;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MeetingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Meeting::class;
    }

    public function configureFields(string $pageName): iterable
    {
        Yield TextField::new('name');
        Yield DateTimeField::new('date');
        Yield IntegerField::new('usersMax');
        Yield TextField::new('textNote');
        Yield IntegerField::new('duration');
        Yield DateTimeField::new('limitDate');
        Yield AssociationField::new('participants')
            ->setFormTypeOptions([
                'by_reference' => false,
            ])->autocomplete()
            ->setColumns(12);
        Yield AssociationField::new('state')
            ->setFormTypeOptions([
            'by_reference' => true,
            ])->autocomplete()
            ->setColumns(12);

        Yield TextField::new('annulation_reason')->onlyOnForms()->setDisabled(true);

        Yield AssociationField::new('place')
            ->setFormTypeOptions([
                'by_reference' => true,
            ])->autocomplete()
            ->setColumns(12);
        Yield AssociationField::new('campus')
            ->setFormTypeOptions([
                'by_reference' => true,
            ])->autocomplete()
            ->setColumns(12);

        Yield AssociationField::new('organizer')
            ->setFormTypeOptions([
                'by_reference' => true,
            ])->autocomplete()
            ->setColumns(12);
    }
    */
}
