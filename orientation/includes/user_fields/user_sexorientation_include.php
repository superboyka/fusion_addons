<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: user_sexorientation_include.php
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
    die("Access Denied");
}
include __DIR__.'/locale/'.LANGUAGE.'.php';

$sorient_set = 1; //0 = Just text; 1 = Icon + text; 2 = Just Icon.
for ($i = 0; $i < count($locale['uf_sexo']); $i++) {
    switch ($sorient_set) {
        case 1:
            $value = "<i class='fa ".$locale['uf_sexe'][$i]." fa-lg m-r-10'></i>".$locale['uf_sexo'][$i];
            break;
        case 2:
            $value = "<i class='fa ".$locale['uf_sexe'][$i]." fa-lg'></i>";
            break;
        default:
            $value = $locale['uf_sexo'][$i];
    }
    $locale['orient_array'][] = $value;
}
// Display user field input
if ($profile_method == "input") {
    $options = [
        'inline'     => TRUE,
        'allowclear' => TRUE,
        'error_text' => $locale['uf_orientation_error'],
        'options'    => $locale['orient_array']
    ] + $options;

    $user_fields = form_select('user_sexorientation', $locale['uf_orientation'], $field_value, $options);

} elseif ($profile_method == "display") {
    if ($field_value) {
        $user_fields = [
            'title' => $locale['uf_orientation'],
            'value' => $locale['orient_array'][$field_value]
        ];
    }
}
