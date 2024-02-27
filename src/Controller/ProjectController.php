<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProjectController extends AbstractController
{

    public const STATE_MEETING_CREATED = "Créée";
    public const STATE_MEETING_OPENED = "Ouverte";
    public const STATE_MEETING_CLOSED = "Clôturée";
    public const STATE_MEETING_ACTIVITY = "Activité en cours";
    public const STATE_MEETING_PASSED = "Passée";
    public const STATE_MEETING_ARCHIVED = "Archivée";
    public const STATE_MEETING_CANCELED = "Annulée";



}