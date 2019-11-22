<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: user_mood_include.php
| Author: karrak
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at http://www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
defined('IN_FUSION') || exit;

require_once(INFUSIONS.'mood_panel/user_fields/user_mood_include.php');
$locale = fusion_get_locale("", MOOD_LOCALE);

if ($profile_method == "input") {
    $user_fields = '';
    if (defined('ADMIN_PANEL')) {
        $user_fields = "<div class='well m-t-5 text-center'>".$locale['uf_mood']."</div>";
    }
} elseif ($profile_method == "display") {
    if ($field_value) {
        $user_fields = [
            'title' => $locale['uf_mood'],
            'value' => $locale['MOOD_ARR'][$field_value]
        ];
    }
}
