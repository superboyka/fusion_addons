<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion_db.php
| Author: karrak
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
defined('IN_FUSION') || exit;

\PHPFusion\Admins::getInstance()->setAdminPageIcons("MOOD", "<i class='admin-ico fa fa-fw fa-commenting'></i>");

if (!defined("MOOD_CLASS")) {
    define("MOOD_CLASS", INFUSIONS."mood_panel/");
}

if (!defined("MOOD_LOCALE")) {
    if (file_exists(MOOD_CLASS."locale/".LOCALESET."mood.php")) {
        define("MOOD_LOCALE", MOOD_CLASS."locale/".LOCALESET."mood.php");
    } else {
        define("MOOD_LOCALE", MOOD_CLASS."locale/Hungarian/mood.php");
    }
}