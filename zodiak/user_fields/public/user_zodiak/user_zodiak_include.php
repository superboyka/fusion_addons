<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: user_zodiak_include.php
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
$zodiak_set = 1; //0 = Just text; 1 = Icon + text; 2 = Just Icon.

for ($i = 0; $i < count($locale['uf_zodiak_name']); $i++) {    if ($profile_method == "input") {    	$value = $locale['uf_zodiak_name'][$i].$locale['uf_zodiak_date'][$i];    } else {        $icon = file_exists(INCLUDES.'user_fields/public/user_zodiak/images/'.$i.'.png') ? INCLUDES.'user_fields/public/user_zodiak/images/'.$i.'.png' : INCLUDES.'user_fields/public/user_zodiak/images/0.png';
        switch ($zodiak_set) {
            case 1:
                $value = "<img class='img-thumbnail rounded mx-auto d-block' src=".$icon." title='".$locale['uf_zodiak_name'][$i].$locale['uf_zodiak_date'][$i]."'> ".$locale['uf_zodiak_name'][$i].$locale['uf_zodiak_date'][$i];
                break;
            case 2:
                $value = "<img class='img-thumbnail rounded mx-auto d-block' src=".$icon." title='".$locale['uf_zodiak_name'][$i].$locale['uf_zodiak_date'][$i]."'> ";
                break;
            default:
                $value = $locale['uf_zodiak_name'][$i].$locale['uf_zodiak_date'][$i];
        }
    }
    $locale['zodiak_array'][] = $value;
}

if ($profile_method == "input") {    $options = [
        'inline'      => TRUE,
        'options'     => $locale['zodiak_array'],
        'error_text'  => $locale['uf_zodiak_error']
    ] + $options;

    $user_fields = form_select('user_zodiak', $locale['uf_zodiak'], $field_value, $options);

// Display user field input
} elseif ($profile_method == "display") {
    if ($field_value) {        $user_fields = [
            'title' => $locale['uf_zodiak'],
            'value' => $locale['zodiak_array'][$field_value]
        ];
    }
}