<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: user_nations_include.php
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

$nation_set = 1; //0 = Just text; 1 = Icon + text; 2 = Just Icon.
include __DIR__.'/locale/'.LANGUAGE.'.php';

function show_flag($item) {
	$items = strtr($item, " ", '_');
    $icon = file_exists(INCLUDES.'user_fields/public/user_nations/flags/flag_'.mb_strtolower($items).'.png') ? INCLUDES.'user_fields/public/user_nations/flags/flag_'.mb_strtolower($items).'.png' : INCLUDES.'user_fields/public/user_nations/flags/-.png';
    $icname = translate_country_names($item);
    return "<img class='img-thumbnail rounded mx-auto d-block' src=".$icon." title='".$icname."'>";
}

for ($i = 0; $i < count($locale['nations']); $i++) {
    switch ($nation_set) {
        case 1:
            $value = show_flag($locale['nations'][$i])." ".translate_country_names($locale['nations'][$i]);
            break;
        case 2:
            $value = show_flag($locale['nations'][$i]);
            break;
        default:
            $value = translate_country_names($locale['nations'][$i]);
    }
    $locale['nations_array'][] = $value;
}

if ($profile_method == "input") {

	$options = [
        'inline'      => TRUE,
        'allowclear'  => TRUE,
        'placeholder' => $locale['uf_choose'],
        'error_text'  => $locale['uf_nations_error'],
        'options'     => $locale['nations_array']
    ] + $options;

    $user_fields = form_select('user_nations', $locale['uf_nations'], $field_value, $options);

} elseif ($profile_method == "display") {
    if ($field_value) {
        $user_fields = [
            'title' => $locale['uf_nations'],
            'value' => $locale['nations_array'][$field_value]
        ];
    }
}
