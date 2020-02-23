<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: birthday_panel/upgrade/1.1.upgrade.inc
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
$inf_version = '1.1';

if (column_exists(DB_BDAY_ST, 'napszak', FALSE)) {
    $column_add = DB_BDAY_ST." CHANGE napszak napszak TINYINT(1) UNSIGNED NOT NULL DEFAULT '1'";
    dbquery("ALTER TABLE ".$column_add);
}

$check_array = [
    'latitude'  => " ADD latitude VARCHAR(100) NOT NULL DEFAULT '47.1048' AFTER birthimg",
    'longitude' => " ADD longitude VARCHAR(100) NOT NULL DEFAULT '19.3013' AFTER latitude",
    'timezone'  => " ADD zenith VARCHAR(100) NOT NULL DEFAULT 'Europe/Budapest' AFTER longitude"
];

foreach ($check_array as $key => $value) {
    if (!column_exists(DB_BDAY_ST, $key)) {
        $inf_altertable[] = DB_BDAY_ST.$value;
    }
}
