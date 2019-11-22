<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: templates.php
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

if (!function_exists("DisplayMood")) {
    function DisplayMood($info) {
        $html = \PHPFusion\Template::getInstance('moodform');
        $html->set_template(__DIR__.'/templates/mood.html');
        $locale = fusion_get_locale();
        $html->set_locale($locale);
        $html->set_tag('openside', fusion_get_function('openside', $info['openside']));
        $html->set_tag('closeside', fusion_get_function('closeside'));

        $html->set_tag('heads', $locale['MOOD_012']);
        $star = ($info['userdb'] > 0 ? "[ ".ceil($info['alluser'] / $info['userdb'])." ] ".str_repeat("<img src='".MOOD_CLASS."images/star.gif' alt='*' style='vertical-align:middle' />", ceil($info['alluser'] / $info['userdb'])) : $locale['MOOD_013']);
        $html->set_tag('stars', $star);
        $html->set_tag('footers', $info['alluser'] > 0 ? sprintf( $locale['MOOD_014'], $info['userdb'] ) : '');

        if (!empty($info['moonform'])) {
            $html->set_block('moonform', [
                'select' => $info['moonform']['select'],
            ]);
        }

        echo isset($info['moonform']['openform']) ? $info['moonform']['openform'] : '';
        echo $html->get_output();
        echo isset($info['moonform']['closeform']) ? $info['moonform']['closeform'] : '';
    }
}
