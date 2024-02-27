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

    public const PERM_MEETING_EDIT = "EDIT_MEETING";
    public const PERM_MEETING_DELETE = "DELETE_MEETING";
    public const PERM_MEETING_VIEW = "VIEW_MEETING";
    public const PERM_MEETING_CREATE = "CREATE_MEETING";
    public const PERM_MEETING_CLOSE = "CLOSE_MEETING";
    public const PERM_MEETING_OPEN = "OPEN_MEETING";
    public const PERM_MEETING_CANCEL = "CANCEL_MEETING";

    public const PERM_MEETING_PUBLISH = "PUBLISH_MEETING";

    public const PERM_MEETING_NEW = "NEW_MEETING";



}