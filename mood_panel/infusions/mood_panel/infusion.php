<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion.php
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

$locale = fusion_get_locale("", MOOD_LOCALE);

$inf_title = $locale['MOOD_000'];
$inf_description = $locale['MOOD_001'];
$inf_version = "1.0";
$inf_developer = "karrak";
$inf_email = "admin@fusionjatek.hu";
$inf_weburl = "https://fusionjatek.hu";
$inf_folder = "mood_panel";
$inf_image = "mood.png";

$inf_mlt[] = [
    'title'  => $inf_title,
    'rights' => "MOOD"
];

if (!column_exists(DB_USERS, 'user_mood', FALSE)) {
    $inf_altertable[] = DB_USERS." ADD user_mood INT(1) UNSIGNED NOT NULL AFTER user_actiontime";
}

$inf_insertdbrow[] = DB_PANELS." (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list, panel_restriction, panel_languages) VALUES('".$locale['MOOD_ST0']."', '".$inf_folder."', '', '4', '3', 'file', '0', '0', '1', '', '3', '".fusion_get_settings('enabled_languages')."')";

$enabled_languages = makefilelist(LOCALE, ".|..", TRUE, "folders");

if (!empty($enabled_languages)) {
    foreach($enabled_languages as $language) {
        include INFUSIONS.$inf_folder."/locale/".$language."/mood.php";

        $mlt_adminpanel[$language][] = [
            'rights'   => "MOOD",
            'image'    => $inf_image,
            'title'    => $locale['MOOD_002'],
            'panel'    => "admin.php",
            'page'     => 5,
            'language' => $language
        ];

        $mlt_deldbrow[$language][] = DB_ADMIN." WHERE admin_rights='MOOD' AND admin_language='".$language."'";
    }
} else {
    $inf_adminpanel[] = [
        'rights'   => "MOOD",
        'image'    => $inf_image,
        'title'    => $locale['MOOD_002'],
        'panel'    => "admin.php",
        'page'     => 5,
        'language' => LANGUAGE
    ];
}

$inf_dropcol[] = ['table' => DB_USERS, 'column' => 'user_mood'];
$inf_deldbrow[] = DB_PANELS." WHERE panel_filename='".$inf_folder."'";
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='MOOD'";
$inf_deldbrow[] = DB_LANGUAGE_TABLES." WHERE mlt_rights='MOOD'";
