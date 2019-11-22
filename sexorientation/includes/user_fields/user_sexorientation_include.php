<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: user_sexorientation_include_var.php
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

// Variation Customization
$gen_set = 1; //0 = Just text; 1 = Icon + text.
$with_secret = FALSE; // True for additiona unspecified option
$input_type = 'form_select'; // form_select or form_checkbox (up to you)
// Definitions
$uf_kep = [
    '0' => '', // Text
    '1' => [ 'fa fa-genderless', 'fa fa-intersex', 'fa fa-transgender-alt', 'fa-mars-double', 'fa-venus-double', 'fa-user-secret' ]
];

$uf_orientation = [
    0 => $locale['uf_orientation_0'],
    1 => $locale['uf_orientation_1'],
    2 => $locale['uf_orientation_2'],
    3 => $locale['uf_orientation_3'],
    4 => $locale['uf_orientation_4'],
    5 => $locale['uf_orientation_5']
];
for ( $i = 0; $i < count( $uf_orientation ); $i++ ) {
    switch ($gen_set) {
        case 1:
            $value = "<i class='fa ".$uf_kep[$gen_set][$i]." fa-fw fa-lg m-r-10'></i>".$uf_orientation[$i];
            break;
        default:
            $value = $uf_orientation[$i];
    }
    $uf_orientation_szkep[] = $value;
}

if (!$with_secret) {
    unset( $uf_orientation_szkep[count( $uf_orientation_szkep ) - 1] );
}
// Display user field input
if ( $profile_method == "input" ) {

    $options = [
        'inline'     => TRUE,
        'type'       => 'radio',
        'error_text' => $locale['uf_orientation_error'],
        'options'    => $uf_orientation_szkep
    ] + $options;

	$user_fields = form_select( 'user_sexorientation', $locale['uf_orientation'], $field_value, $options );

} elseif ( $profile_method == "display" ) {
    if ( $field_value ) {
        $user_fields = [
            'title' => $locale['uf_orientation'],
            'value' => $uf_orientation_szkep[$field_value]
        ];
    }
}