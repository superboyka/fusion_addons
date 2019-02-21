<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: pm_control_panel/templates.php
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

if (!function_exists("DisplayPm")) {
    function DisplayPm($info) {
        $html = \PHPFusion\Template::getInstance('pmcontrolform');
        $html->set_template(PMC_CLASS.'templates/pmcontrol.html');
        $html->set_locale(fusion_get_locale());
        $html->set_tag('openside', fusion_get_function('openside', $info['openside']));
        $html->set_tag('closeside', fusion_get_function('closeside'));

        $html->set_tag('info', $info['info']);

        foreach ($info['item'] as $data) {
            $html->set_block('pmcontrol', [
                'avatar'   => $data['avatar'],
                'profile'  => $data['profile'],
                'dates'    => $data['dates'],
                'pmtag'    => $data['pmtag']
            ]);
        }
        echo $html->get_output();
    }
}
