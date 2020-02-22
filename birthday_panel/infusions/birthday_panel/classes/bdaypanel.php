<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: classes/bdaypanel.php
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
namespace PHPFusion\Bday;

class bdaypanel extends BdayServer {
    private static $instance = NULL;
    public $settings = [];

    public function __construct() {
        parent::__construct();
        $this->settings = self::CurrentSetup();
    }

    public static function getInstance() {
        if ( self::$instance === NULL ) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function display() {
        $month = explode( "|", self::$locale['months'] );
        $day = explode( "|", self::$locale['weekdays'] );

        $info = [
            'openside'    => "<i class='fa fa-gift fa-lg m-r-10'></i>".self::$locale['BDAY_010'],
            'searchlink'  => $this->settings['search'] ? BDAY_PATH.'search.php' : '',
            'daymsg'      => showdate( self::$locale['BDAY_today'], time() ),
            'nameday'     => self::Nameday( $this->settings['nameday'] ), //0 = disabled, 1 = day nameday, 2 = day and tomorrow nameday
            'birthdate'   => iMEMBER ? self::Birthdate( $this->settings['birthday'], $this->settings['age'] ) : '',
            'usernameday' => iMEMBER ? self::UserNameday( $this->settings['usernameday'] ) : '',
            'partday'     => $this->settings['napszak'] ? self::MorningEevening() : '',
            'zodiak'      => $this->settings['zodiac'] ? self::zodiac( date( "m" ), date( "d" ) ) : '',
            'leapyear'    => self::$locale['BDAY_021'][date( "L" )]
        ];

        Displayform( $info );
    }
}