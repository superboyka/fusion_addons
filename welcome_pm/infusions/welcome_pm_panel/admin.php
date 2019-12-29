<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: admin.php
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
require_once "../../maincore.php";
require_once THEMES."templates/admin_header.php";

pageAccess( "WPM" );

$locale = fusion_get_locale( "", WPM_LOCALE );

$welcpmsettings = dbarray( dbquery( "SELECT * FROM ".DB_WELCOME_PM.( multilang_table( "WPM" ) ? " WHERE ".in_group( 'wp_language', LANGUAGE ) : '' ) ) );
\PHPFusion\BreadCrumbs::getInstance()->addBreadCrumb( [ 'link' => INFUSIONS."welcome_pm_panel/admin.php".fusion_get_aidlink(), 'title' => $locale['WPM_000'] ] );
add_to_title( $locale['WPM_000'] );

if ( post( 'savesettings' ) ) {

    $welcpmsettings = [
        'wp_id'       => $welcpmsettings['wp_id'],
        'wp_active'   => sanitizer( 'wp_active', 0, 'wp_active' ),
        'wp_sbox'     => sanitizer( 'wp_sbox', 0, 'wp_sbox' ),
        'wp_userid'   => sanitizer( 'wp_userid', 0, 'wp_userid' ),
        'wp_language' => LANGUAGE
    ];

    dbquery_insert( DB_WELCOME_PM, $welcpmsettings, 'update' );
    addNotice( 'success', $locale['WPM_008'] );
    redirect( FUSION_REQUEST );
}

$result = dbquery( "SELECT user_id, user_name FROM ".DB_USERS." WHERE user_level <= :userLevel", [ ':userLevel' => USER_LEVEL_ADMIN ] );
$wpuser = [];
if ( dbrows( $result ) ) {
    while ( $data = dbarray( $result ) ) {
    	$wpuser[$data['user_id']] = $data['user_name'];
    }
}

opentable( $locale['WPM_009'] );
echo"<div class='well m-t-10'>".$locale['WPM_001']."</div>\n";
echo openform( 'settingsform', 'post', FUSION_REQUEST, [ 'class' => 'spacer-sm' ] );
echo "<hr/>\n";
echo "<div class='row'>\n<div class='col-xs-12 col-sm-3'>\n";
echo "<h4 class='m-0'>".$locale['WPM_009']."</h4>";
echo "</div>\n<div class='col-xs-12 col-sm-9'>\n";
echo form_checkbox( 'wp_active', $locale['WPM_010'], $welcpmsettings['wp_active'], [ 'reverse_label' => TRUE ] );
echo form_checkbox( 'wp_sbox', $locale['WPM_011'], $welcpmsettings['wp_sbox'], [ 'reverse_label' => TRUE ] );
echo "</div>\n</div>\n";

echo form_select( 'wp_userid', $locale['WPM_013'], $welcpmsettings['wp_userid'], [
    'inline'     => TRUE,
    'allowclear' => TRUE,
    'options'    => $wpuser
] );

echo form_button( 'savesettings', $locale['save'], $locale['save'], [ 'class' => 'btn-success' ] );
echo closeform();
closetable();
require_once THEMES."templates/footer.php";
