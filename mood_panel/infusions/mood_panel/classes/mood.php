<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: classes/mood.php
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
namespace PHPFusion\Infusions\Mood_panel\Classes;

class Mood {
    private static $instance = NULL;
    private $mood = 0;
    private static $locale = [];

    public function __construct() {
        include_once MOOD_CLASS."templates.php";
        self::$locale = fusion_get_locale("", MOOD_LOCALE);
        $this->mood = iMEMBER ? fusion_get_userdata('user_mood') : 0;
    }
    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
        }
       return self::$instance;
    }

    public function StartMood() {
    	$mood = filter_input(INPUT_POST, 'mood', FILTER_VALIDATE_INT);
        if( iMEMBER && !empty( $mood) ) {
            $ertek = form_sanitizer($mood, 0, 'mood');
            if (\defender::safe()) {
                dbquery( "UPDATE ".DB_USERS." SET user_mood = :mood WHERE user_id = :userid", [ ':mood' => (int)$ertek, ':userid' => fusion_get_userdata('user_id') ] );
                addNotice( "success", self::$locale['MOOD_015'] );
                redirect( FUSION_SELF );
            }
        }

        $info = [
            'openside'  => "<i class='fa fa-star-half-empty fa-lg m-r-10'></i>".self::$locale['MOOD_010'],
	        'moonform'  => iMEMBER ? self::MoodForm() : '',
	        'alluser'   => $this->alluser(),
	        'userdb'    => $this->userdb()
        ];
        DisplayMood($info);
	}

    private function MoodForm(){
    	$info = [
    	    'openform'  => openform('moodform', 'post', FUSION_SELF, ['max_tokens' => 1]),
    	    'closeform' => closeform(),
    	    'select'    => form_select('mood', self::$locale['MOOD_011'], $this->mood, [
    	        'inner_width' => '100%',
    	        'options'     => self::$locale['MOOD_ARR'],
    	        'onchange'    => 'document.moodform.submit()'
    	    ])
    	];
        return $info;
    }

    private function alluser() {
        $alluser = dbarray( dbquery( "SELECT SUM(user_mood) as mons FROM ".DB_USERS." WHERE user_mood != 0".(multilang_table( "MOOD" ) ? " AND ".in_group( 'user_language', LANGUAGE ) : '' ) ) );
        return $alluser['mons'];
    }

    private function userdb() {
        return dbcount( "(user_id)", DB_USERS, "user_mood != 0".( multilang_table( "MOOD" ) ? " AND ".in_group( 'user_language', LANGUAGE ) : '' ) );
    }
}
