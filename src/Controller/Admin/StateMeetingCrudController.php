<?php

namespace App\Controller\Admin;

use App\Entity\StateMeeting;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class StateMeetingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StateMeeting::class;
    }



}
