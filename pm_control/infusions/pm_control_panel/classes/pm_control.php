<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: pm_control_panel/classes/pm_control.php
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
namespace PHPFusion\PmControl;

class PmControl {
    private static $instance = NULL;
    private static $locale = [];

    public function __construct() {
        require_once INCLUDES."infusions_include.php";
        require_once PMC_CLASS."templates.php";
        self::$locale = fusion_get_locale("", PMC_LOCALE);
        $this->pmsettings = get_settings("pm_control_panel");
    }

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
        }
       return self::$instance;
    }

    public function DisplayPmcontrol() {
        if ($this->CheckDay()) {
            self::DailyChecks();
        }

        if ($this->CountMessages()) {
            $info = [
                'openside' => self::$locale['PMC_010'],
                'info'      => sprintf(self::$locale['PMC_D01'], $this->pmsettings['days']),
                'item'      => $this->PmUser()
            ];

            DisplayPm($info);
        }
    }

    private function CountMessages() {
        return dbcount("(message_id)", DB_MESSAGES, "message_to = :mto AND message_read = :mread AND message_folder = :mfolder ORDER BY message_datestamp DESC LIMIT :limit", [':mto' => fusion_get_userdata('user_id'), ':mread' => '0', ':mfolder' => '0', ':limit' => $this->pmsettings['limit']]);
    }

    private function PmUser() {
        $result = dbquery("SELECT pc.*, pu.user_id, pu.user_name, pu.user_avatar, pu.user_status
            FROM ".DB_MESSAGES." AS pc
            LEFT JOIN ".DB_USERS." AS pu ON pc.message_from = pu.user_id
            WHERE message_to = :mto AND message_folder = :mfolder AND message_read = :mread
            ORDER BY message_datestamp DESC
            LIMIT :limit", [':mto' => fusion_get_userdata('user_id'), ':mfolder' => '0', ':mread' => '0', ':limit' => $this->pmsettings['limit']]
        );

        $info = [];
        while($data = dbarray($result)) {

            $text = "<div class='text-left'>".($data['message_smileys'] == "y" ? parseubb(parsesmileys($data['message_message'])) : parseubb($data['message_message']))."</div>";
            $inf = [
                'avatar'  => display_avatar($data, '50px', '', '', ''),
                'profile' => profile_link($data['user_id'], $data['user_name'], $data['user_status']),
                'dates'   => showdate(self::$locale['PMC_date'], $data['message_datestamp']),
                'pmtag'   => self::PmTags($data, $data['message_subject'], $text)
            ];

            $info[$data['message_id']] = $inf;
        }

    	return $info;
    }

    private function CheckDay() {
        $days = date(mktime(0,0,0,date("m"),date("d"),date("Y")));
        $info = ($this->pmsettings['control'] < $days || empty($this->pmsettings['control'])) ? TRUE : FALSE;
    	return $info;
    }

    private function DailyChecks() {
        $dates = (time() - ($this->pmsettings['days'] * 86400));

        $result = dbquery("SELECT *
            FROM ".DB_MESSAGES."
            WHERE  message_folder=:folder AND message_read=:read AND message_datestamp<:datest
            ORDER BY message_datestamp DESC", [':folder' => '0', ':read' => '0', ':datest' => $dates]
        );

        if (dbrows($result)) {
		    // Removing unread messages from the database
            dbquery("DELETE FROM ".DB_MESSAGES." WHERE message_folder=:folder AND message_read=:read AND message_datestamp<:datest", [':folder' => '0', ':read' => '0', ':datest' => $dates]);

            $message= dbrows($result).self::$locale['PMC_021'].$this->pmsettings['days'].self::$locale['PMC_022'];
            dbquery("INSERT INTO ".DB_MESSAGES." (message_to, message_from, message_user, message_subject, message_message, message_smileys, message_read, message_datestamp, message_folder) VALUES('1', '1', '1', '".self::$locale['PMC_023']."', '".$message."', 'y', '0', '".time()."', '0')");
        }

        $inputSettings = [
            'settings_name' => 'control', 'settings_value' => time(), 'settings_inf' => 'pm_control_panel',
        ];

        dbquery_insert(DB_SETTINGS_INF, $inputSettings, 'update', ['primary_key' => 'settings_name']);
    }

    private static function PmTags($data, $subject, $text) {

        add_to_jquery("$('[data-toggle=\"user-pmtags\"]').popover();");
        $avatar = !empty($data['user_avatar']) ? "<div class='pull-left m-r-10'>".display_avatar($data, '50px', '', FALSE, '')."</div>" : '';
        $title = "<div class='user-pmtags'>".$avatar."<div class='clearfix'>".profile_link($data['user_id'], $data['user_name'], $data['user_status'])."</div>";
        $content = $text."<a class='btn btn-block btn-primary' href='".BASEDIR."messages.php?msg_send=".$data['user_id']."'>".self::$locale['send_message']."</a>";
        $content .= "<a class='btn btn-block btn-primary' href='".BASEDIR."messages.php?folder=inbox&amp;msg_read=".$data['message_id']."'>".self::$locale['PMC_020']."</a>";
        $html = '<a class="strong pointer" tabindex="0" role="button" data-html="true" data-trigger="focus" data-placement="top" data-toggle="user-pmtags" title="'.$title.'" data-content="'.$content.'">';
        $html .= "<span class='user-label'>".$subject."</span>";
        //$html .= "</a>\n";
        return $html;
    }

}