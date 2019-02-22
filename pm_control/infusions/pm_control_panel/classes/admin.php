<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: pm_control_panel/classes/admin.php
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

class PmControlAdmin {
    private static $instance = NULL;
    private static $locale = [];
    private $allowed_section = ['pm_messages', 'pm_count', 'settings'];

    public function __construct() {
        require_once INCLUDES."infusions_include.php";
        self::$locale = fusion_get_locale("", PMC_LOCALE);
        $this->pmsettings = get_settings("pm_control_panel");

        $rowstart = filter_input(INPUT_GET, 'rowstart', FILTER_DEFAULT);
        $this->rowstart = (isset($rowstart) ? $rowstart : 0);
    }

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
        }
       return self::$instance;
    }

    public function DisplayAdmin() {
        $pm_section = filter_input(INPUT_GET, 'section', FILTER_DEFAULT);

        $section = !empty($pm_section) && in_array($pm_section, $this->allowed_section) ? $pm_section : $this->allowed_section[0];
        \PHPFusion\BreadCrumbs::getInstance()->addBreadCrumb(['link' => PMC_CLASS."admin.php".fusion_get_aidlink(), 'title' => self::$locale['PMC_003']]);
        // Sitetitle
        add_to_title(self::$locale['PMC_003']);

        $pm_tab = [
            'title' => [self::$locale['PMC_050'], self::$locale['PMC_051'], self::$locale['PMC_052']],
            'id'    => ['pm_messages', 'pm_count', 'settings'],
            'icon'  => ['fa fa-envelope-square', 'fa fa-info', 'fa fa-cogs']
        ];

        $delete_message_id = filter_input(INPUT_GET, 'delete_message_id', FILTER_VALIDATE_INT);
        $delete_all = filter_input(INPUT_POST, 'delete_all', FILTER_DEFAULT);
        opentable(self::$locale['PMC_053']);
        echo opentab($pm_tab, $section, 'pm_messages', TRUE, '', 'section', ['rowstart']);
        switch ($section) {
            case 'pm_count':
                $this->PmCount();
                break;
            case 'settings':
                $this->PmSettings();
                break;
            default:
                if (!empty($delete_message_id)) {
                    $this->DelMessages($delete_message_id);
                } else if (!empty($delete_all)) {
                    $this->DelAllMessages();
                }
                $this->PmMessages();
                break;
        }
        echo closetab();
        closetable();
    }

    private function PmSettings() {

        $save_settings = filter_input(INPUT_POST, 'save_settings', FILTER_DEFAULT);
        if (!empty($save_settings)) {
            self::PmSaveSt();
        }

        opentable(self::$locale['PMC_005']);
        $txt = "<div class='well'>".self::$locale['PMC_072']."</div>";
        $txt .= "<hr/>\n";
        $txt .= openform('pmcontrol', 'post', FUSION_REQUEST);
        $txt .= form_text('limit', self::$locale['PMC_054'], $this->pmsettings['limit'], [
            'inline'      => TRUE,
            'type'        => 'number',
            'inner_width' => '60px',
            'ext_tip'     => self::$locale['PMC_055']
        ]);

        $txt .= form_text('days', self::$locale['PMC_056'], $this->pmsettings['days'], [
            'inline'      => TRUE,
            'type'        => 'number',
            'inner_width' => '60px',
            'ext_tip'     => self::$locale['PMC_057']
        ]);

        $txt .= form_checkbox('bubble', self::$locale['PMC_058'], $this->pmsettings['bubble'], [
            'inline'  => TRUE,
            'ext_tip' => self::$locale['PMC_059']
        ]);

        $txt .= form_button('save_settings', self::$locale['save'], self::$locale['save'], ['class' => 'btn-success', 'icon' => 'fa fa-hdd-o']);
        $txt .= closeform();
        closetable();
        echo $txt;
    }

    private static function PmSaveSt() {
        $bubble = filter_input(INPUT_POST, 'bubble', FILTER_DEFAULT);
        $inputData = [
            'limit' => form_sanitizer(filter_input(INPUT_POST, 'limit', FILTER_VALIDATE_INT), 0, 'limit'),
            'days'  => form_sanitizer(filter_input(INPUT_POST, 'days', FILTER_VALIDATE_INT), 0, 'days'),
            'bubble'    => !empty($bubble) ? form_sanitizer($bubble, 0, 'bubble') : 0
        ];

        if (\defender::safe()) {
            foreach ($inputData as $settings_name => $settings_value) {
                $inputSettings = [
                    'settings_name'  => $settings_name,
                    'settings_value' => $settings_value,
                    'settings_inf'   => "pm_control_panel"
                ];
                dbquery_insert(DB_SETTINGS_INF, $inputSettings, 'update', ['primary_key' => 'settings_name']);
            }
            addNotice('success', self::$locale['PMC_100']);
            redirect(clean_request("", [""], FALSE));
        }
    }

    private static function DelMessages($messageid = NULL) {

        if (!empty($messageid)) {
            if (dbcount("('message_id')", DB_MESSAGES, "message_id = :messageid", [':messageid' => (int)$messageid]) && \defender::safe()) {
                dbquery("DELETE FROM ".DB_MESSAGES." WHERE message_id = :messageid", [':messageid' => (int)$messageid]);
                addNotice('success', self::$locale['PMC_101']);
                redirect(clean_request('', ['section', 'delete_message_id'], FALSE));
            }
        }

    }

    private static function DelAllMessages() {
        $message_id = filter_input(INPUT_POST, 'message_id', FILTER_DEFAULT);
        $input = (!empty($message_id)) ? explode(",", form_sanitizer($message_id, '', 'message_id')) : "";
        if (!empty($input)) {
            $dai = 0;
            foreach ($input as $messages_id) {
                if (dbcount("('message_id')", DB_MESSAGES, "message_id = :messageid", [':messageid' => (int)$messages_id]) && \defender::safe()) {
                    dbquery("DELETE FROM ".DB_MESSAGES." WHERE message_id = :messageid", [':messageid' => (int)$messages_id]);
                    $dai++;
                }
            }
            addNotice('warning', sprintf(self::$locale['PMC_102'], $dai));
            redirect(clean_request('', ['section', 'delete_message_id'], FALSE));
        }
        addNotice('warning', self::$locale['PMC_103']);
    }

    private function PmMessages() {
        $aidlink = fusion_get_aidlink();

        $limit = $this->pmsettings['limit'];
        $txt = '';
        $rows = dbrows(dbquery("SELECT x1.*, x2.user_name as from_name, x2.user_id as id_from, x3.user_name as to_name, x3.user_id as id_to
                FROM ".DB_MESSAGES." as x1
                LEFT JOIN ".DB_USERS." as x2 on x2.user_id = x1.message_from
                LEFT JOIN ".DB_USERS." as x3 on x3.user_id = x1.message_to
                WHERE x1.message_from = x2.user_id AND x1.message_to = x3.user_id
                ORDER BY message_id DESC"
        ));

        if ($rows) {
            $result = dbquery("SELECT x1.*, x2.user_name as from_name, x2.user_id as id_from, x3.user_name as to_name, x3.user_id as id_to
                FROM ".DB_MESSAGES." as x1
                LEFT JOIN ".DB_USERS." as x2 on x2.user_id = x1.message_from
                LEFT JOIN ".DB_USERS." as x3 on x3.user_id = x1.message_to
                WHERE x1.message_from = x2.user_id AND x1.message_to = x3.user_id
                ORDER BY message_id DESC
                LIMIT :rowstart, :limit", [':rowstart' => $this->rowstart, ':limit' => $limit]
            );

            $allnotread = dbcount("(message_id)", DB_MESSAGES, " message_folder ='0' && message_read = '0' ");
            $allread = dbcount("(message_id)", DB_MESSAGES, " message_folder ='0' && message_read = '1' ");
            $allarchiv = dbcount("(message_id)", DB_MESSAGES, " message_folder ='2' && message_read = '1' ");

            openside('');

            if ($rows > $limit) {
                $txt .= "<div class='clearfix'>\n";
                $txt .= "<div class='pull-right'>".makepagenav($this->rowstart, $limit, $rows, 3, FUSION_SELF.$aidlink."&amp;section=pm_messages&amp;")."</div>\n";
                $txt .= "</div>\n";
            }
            $txt .= "<div class='well'>".self::$locale['PMC_060']."</div>\n";
            $txt .= "<div class='text-right m-b-10'>".sprintf(self::$locale['PMC_071'], $allnotread, $allread, $allarchiv)."</div>\n";

            $txt .= "<div class='table-responsive'><table class='table table-hover'>\n";

            $txt .= "<thead>
                <tr>
                    <th></th>
                    <th>".self::$locale['PMC_061']."</th>
                    <th>".self::$locale['PMC_062']."</th>
                    <th>".self::$locale['PMC_063']."</th>
                    <th>".self::$locale['PMC_064']."</th>
                    <th>".self::$locale['PMC_065']."</th>
                    <th>".self::$locale['PMC_066']."</th>
                    <th>".self::$locale['PMC_067']."</th>
                    <th>".self::$locale['delete']."</th>
                </tr>
                </thead>
                <tbody>\n";
            $txt .= openform('pmcontrol_table', 'post', FUSION_SELF.$aidlink."&amp;section=pm_messages");

            while($data = dbarray($result))  {

                $message_read = self::$locale['PMC_068'][$data['message_read']];
                $message_folder = self::$locale['PMC_069'][$data['message_folder']];
                $message = parseubb(parse_textarea($data['message_message'], false, false));
                $text = "<div class='text-left'>".$message."</div>\n";

                $txt .= "<tr id='link-".$data['message_id']."' data-id=".$data['message_id']."'>
			        <td>".form_checkbox('message_id[]', '', '', [
			            'value'    => $data['message_id'],
			            'class'    => 'm-0',
			            'input_id' => 'link-id-'.$data['message_id']
			        ])."</td>
                    <td>[ <a href='".BASEDIR."profile.php?lookup=".$data['id_from']."' target='_new'>".$data['from_name']."</a> ]</td>
                    <td>[ <a href='".BASEDIR."profile.php?lookup=".$data['id_to']."' target='_new'>".$data['to_name']."</a> ]</td>
                    <td>".$data['message_subject']."</td>
                    <td>".fusion_parse_user('@'.$data['from_name'], $text)."</td>
                    <td>".date(self::$locale['PMC_datepicker'], $data['message_datestamp'])."</td>
                    <td>".$message_read."</td>
                    <td>".$message_folder."</td>
                    <td><a class='btn btn-default' href='".FUSION_SELF.$aidlink."&amp;section=pm_messages&amp;delete_message_id=".$data['message_id']."' onclick=\"return confirm('".self::$locale['PMC_104']."')\"><i class='fa fa-times white'></i> ".self::$locale['delete']."</a></td>
                </tr>\n";

                add_to_jquery('$("#link-id-'.$data['message_id'].'").click(function() {
                    if ($(this).prop("checked")) {
                        $("#link-'.$data['message_id'].'").addClass("active");
                    } else {
                        $("#link-'.$data['message_id'].'").removeClass("active");
                    }
	            });');
            }
            $txt .= "</tbody></table>\n";

            $txt .= form_checkbox('check_all', self::$locale['PMC_070'], '', [
                'reverse_label' => TRUE,
                'class'         => 'pull-left'
            ]);
            $txt .= form_button('delete_all', self::$locale['delete'], self::$locale['delete'], [
                'class' => 'btn-danger m-l-10',
                'icon'  => 'fa fa-fw fa-trash-o'
            ]);

            $txt .= closeform();

            add_to_jquery("
                $('#check_all').bind('click', function() {
                    if ($(this).is(':checked')) {
                        $('input[name^=message_id]:checkbox').prop('checked', true);
                        $('#links-table tbody tr').addClass('active');
                    } else {
                        $('input[name^=message_id]:checkbox').prop('checked', false);
                        $('#links-table tbody tr').removeClass('active');
                    }
                });
	        ");
            $txt .= "</div>";

            if ($rows > $limit) {
                $txt .= "<div class='clearfix'>\n";
                $txt .= "<div class='pull-right'>".makepagenav($this->rowstart, $limit, $rows, 3, FUSION_SELF.$aidlink."&amp;section=pm_messages&amp;")."</div>\n";
                $txt .= "</div>\n";
            }
            closeside();
        } else {

            $txt .= "<div class='text-center well'>".self::$locale['PMC_105']."</div>\n";
        }
        echo $txt;
    }

    private function PmCount() {

        $aidlink = fusion_get_aidlink();
        $limit = $this->pmsettings['limit'];

        $rows = dbcount("(message_id)", DB_MESSAGES, "");

        $txt = "<div class='well'>".self::$locale['PMC_080']."</div>\n";

        if ($rows) {
            if ($rows > $limit) {
                $txt .= "<div class='clearfix'>\n";
                $txt .= "<div class='pull-right'>".makepagenav($this->rowstart, $limit, $rows, 3, FUSION_SELF.$aidlink."&amp;section=pm_count&amp;")."</div>\n";
                $txt .= "</div>\n";
            }

            $result = dbquery("SELECT m.*, u.user_lastvisit, u.user_email, u.user_id, u.user_name, COUNT(message_to) AS message_to
                FROM ".DB_USERS." AS u
                LEFT JOIN ".DB_MESSAGES." AS m ON u.user_id = m.message_to
                GROUP BY user_id
                ORDER BY message_to DESC
                LIMIT :rowstart, :limit", [':rowstart' => $this->rowstart, ':limit' => $limit]
            );

            $txt .= "<div class='table-responsive'><table class='table table-hover'>\n";
            $txt .= "<thead>
                    <tr>
                        <th>".self::$locale['PMC_081']."</th>
                        <th>".self::$locale['PMC_082']."</th>
                        <th>".self::$locale['PMC_083']."</th>
                        <th>".self::$locale['PMC_084']."</th>
                    </tr>
                </thead>
                <tbody>\n";
            $db_to = 0; $db_from = 0;
            while($data = dbarray($result)) {
            	$mdb = dbcount("(message_id)", DB_MESSAGES, "message_from = :messagefrom", [':messagefrom' => (int)$data['user_id']]);
            	$db_to = $db_to + $data['message_to'];
            	$db_from = $db_from + $mdb;

            	$txt .= "<tr>
            	    <td>".fusion_parse_user('@'.$data['user_name'])."</td>
            	    <td><a href='mailto:".$data['user_email']."'>".$data['user_email']."</a></td>
            	    <td>".showdate("longdate", $data['user_lastvisit'])."</td>
            	    <td>".$data['message_to']."/".$mdb."</td>
                </tr>\n";
            }

            $txt .= "</tbody></table>\n";
            $txt .= "</div>\n";
            $txt .= "<div class='text-center'>".sprintf(self::$locale['PMC_085'], $db_to, $db_from)."</div>\n";

            if ($rows > $limit) {
                $txt .= "<div class='clearfix'>\n";
                $txt .= "<div class='pull-right'>".makepagenav($this->rowstart, $limit, $rows, 3, FUSION_SELF.$aidlink."&amp;section=pm_count&amp;")."</div>\n";
                $txt .= "</div>\n";
            }

        } else {
            $txt .= "<div class='text-center'>".self::$locale['PMC_105']."</div>\n";
        }
        echo $txt;
    }
}