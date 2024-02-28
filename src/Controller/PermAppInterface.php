<?php

namespace App\Controller;

interface PermAppInterface
{
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