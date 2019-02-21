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
if (!defined("IN_FUSION")) {
    die("Access Denied!");
}

\PHPFusion\Admins::getInstance()->setAdminPageIcons("PMC", "<i class='admin-ico fa fa-fw fa-commenting'></i>");

if (!defined("PMC_CLASS")) {
    define("PMC_CLASS", INFUSIONS."pm_control_panel/");
}

// Locale
if (!defined("PMC_LOCALE")) {
    if (file_exists(PMC_CLASS."locale/".LOCALESET."control.php")) {
        define("PMC_LOCALE", PMC_CLASS."locale/".LOCALESET."control.php");
    } else {
        define("PMC_LOCALE", PMC_CLASS."locale/Hungarian/control.php");
    }
}
