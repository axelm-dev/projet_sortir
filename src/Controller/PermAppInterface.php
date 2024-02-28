<?php

namespace App\Controller;

interface PermAppInterface
{
    public const PERM_MEETING_EDIT = "EDIT_MEETING";
    public const PERM_MEETING_DELETE = "DELETE_MEETING";
    public const PERM_MEETING_VIEW = "VIEW_MEETING";
    public const PERM_MEETING_CLOSE = "CLOSE_MEETING";
    public const PERM_MEETING_OPEN = "OPEN_MEETING";
    public const PERM_MEETING_CANCEL = "CANCEL_MEETING";

    public const PERM_MEETING_PUBLISH = "PUBLISH_MEETING";

    public const PERM_MEETING_NEW = "NEW_MEETING";
    public const PERM_PLACE_EDIT = "EDIT_PLACE";
    public const PERM_PLACE_DELETE = "DELETE_PLACE";
    public const PERM_PLACE_VIEW = "VIEW_PLACE";
    public const PERM_PLACE_NEW = "NEW_PLACE";

}