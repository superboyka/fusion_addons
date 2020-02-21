<?php
namespace PHPFusion\Bday;

use \PHPFusion\BreadCrumbs;
class Bdayadmin extends BdayServer {
    private static $instance = NULL;
    private $allowed_pages = [ "birthday", "settings" ];
    public $settings = [];

    public function __construct() {
        parent::__construct();
        //require_once BDAY_PATH."templates.php";
        //self::$locale = fusion_get_locale("", BDAY_LOCALE);
        BreadCrumbs::getInstance()->addBreadCrumb(["link" => BDAY_PATH.'admin.php'.fusion_get_aidlink(), "title" => self::$locale['BDAY_050']]);
        add_to_title(self::$locale['global_200'].self::$locale['BDAY_050']);
        $this->settings = self::CurrentSetup();
    }

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function displayadmin() {
        $section = get( 'section' );
        $section = isset($section) && in_array($section,  $this->allowed_pages) ? $section : $this->allowed_pages[1];

        $tab = [
            'title' => [
                0 => self::$locale['BDAY_051'],
                1 => self::$locale['BDAY_052']
            ],
            'id'    => $this->allowed_pages,
            'icon'  => [
                0 => "fa fa-fw fa-file-text",
                1 => "fa fa-fw fa-cogs"
            ]
        ];
        // Display Content
        opentable( self::$locale['BDAY_050'] );

        echo opentab( $tab, $section, "birthday_admin", TRUE, "", "section", [ 'birthday', 'rowstart' ] );
        switch ( $section ) {
            case "settings":
                self::daysettings();
                break;
            default:
                //ArticlesAdmin::getInstance()->displayArticlesAdmin();
        }
        echo closetab();
        closetable();
    }

    private function daysettings() {
        if ( post( 'savesettings' ) ) {
            $savesettings = [
        	    'nsid'        => $this->settings['nsid'],
        	    'bfrom'       => sanitizer( 'bfrom', '1', 'bfrom' ),
        	    'search'      => sanitizer( 'search', '0', 'search' ),
        	    'zodiac'      => sanitizer( 'zodiac', '0', 'zodiac' ),
        	    'age'         => sanitizer( 'age', '0', 'age' ),
        	    'birthday'    => sanitizer( 'birthday', '0', 'birthday' ),
        	    'nameday'     => sanitizer( 'nameday', '0', 'nameday' ),
        	    'usernameday' => sanitizer( 'usernameday', '0', 'usernameday' ),
        	    'nevinfo'     => sanitizer( 'nevinfo', '0', 'nevinfo' ),
        	    'zodiakimg'   => sanitizer( 'zodiakimg', '1', 'zodiakimg' ),
        	    'nameimg'     => sanitizer( 'nameimg', '', 'nameimg' ),
        	    'birthimg'    => sanitizer( 'birthimg', '', 'birthimg' ),
        	    'napszak'     => sanitizer( 'napszak', 0, 'napszak')
        	];

            if ( fusion_safe() ) {
            	dbquery_insert( DB_BDAY_ST, $savesettings, 'update' );
            	addNotice( 'success', self::$locale['BDAY_054'] );
            	redirect( FUSION_REQUEST );
            }
        }

        $result = dbquery( "SELECT user_name, user_id FROM ".DB_USERS." WHERE user_level <= :userlevel", [ ':userlevel' => USER_LEVEL_SUPER_ADMIN ] );
        $ufrom = [];
        while ( $u = dbarray( $result ) ) {
        	$ufrom[$u['user_id']] = $u['user_name'];
        }

        echo openform( 'admin_form', 'post', FUSION_REQUEST, [ 'class' => 'spacer-sm' ] );
        $yesno = [ '1' => self::$locale['yes'], '0' => self::$locale['no'] ];

        echo form_select( 'bfrom', self::$locale['BDAY_060'], $this->settings['bfrom'], [
            'inline'      => TRUE,
            'keyflip'     => TRUE,
            'options'     => $ufrom,
            'placeholder' => self::$locale['choose'],
            'width'       => '100%'
        ] );

        echo form_select( 'search', self::$locale['BDAY_061'], $this->settings['search'], [
            'inline'      => TRUE,
            'options'     => $yesno,
            'placeholder' => self::$locale['choose'],
            'width'       => '100%'
        ] );

        echo form_select( 'usernameday', self::$locale['BDAY_062'], $this->settings['usernameday'], [
            'inline'      => TRUE,
            'options'     => self::$locale['BDAY_055'],
            'placeholder' => self::$locale['choose'],
            'width'       => '100%'
        ] );

        echo form_select( 'birthday', self::$locale['BDAY_063'], $this->settings['birthday'], [
            'inline'      => TRUE,
            'options'     => self::$locale['BDAY_055'],
            'placeholder' => self::$locale['choose'],
            'width'       => '100%'
        ] );

        echo form_select( 'age', self::$locale['BDAY_064'], $this->settings['age'], [
            'inline'      => TRUE,
            'options'     => $yesno,
            'placeholder' => self::$locale['choose'],
            'width'       => '100%'
        ] );

        echo form_select( 'nameday', self::$locale['BDAY_065'], $this->settings['nameday'], [
            'inline'      => TRUE,
            'options'     => self::$locale['BDAY_055'],
            'placeholder' => self::$locale['choose'],
            'width'       => '100%'
        ] );

        echo form_select( 'nevinfo', self::$locale['BDAY_066'], $this->settings['nevinfo'], [
            'inline'      => TRUE,
            'options'     => $yesno,
            'placeholder' => self::$locale['choose'],
            'width'       => '100%'
        ] );

        echo form_text( 'nameimg', self::$locale['BDAY_067'], $this->settings['nameimg'], [
            'inline'     => TRUE,
            'required'   => TRUE,
            'max_length' => 100,
            'error_text' => self::$locale['choose']
        ] );

        echo form_text( 'birthimg', self::$locale['BDAY_068'], $this->settings['birthimg'], [
            'required'   => TRUE,
            'inline'     => TRUE,
            'max_length' => 100,
            'error_text' => self::$locale['choose']
        ] );

        echo form_select('napszak', self::$locale['BDAY_071'], $this->settings['napszak'], [
            'options'     => [ '1' => self::$locale['BDAY_072'][1], '0' => self::$locale['BDAY_072'][0] ],
            'placeholder' => self::$locale['choose'],
            'width'       => '100%',
            'inline'      => TRUE
        ] );

        echo form_select( 'zodiac', self::$locale['BDAY_069'], $this->settings['zodiac'], [
            'inline'      => TRUE,
            'options'     => $yesno,
            'placeholder' => self::$locale['choose'],
            'width'       => '100%'
        ] );

        echo "<div class='row'>\n";
        echo "<div class='col-sm-2 col-md text-left'><b>".self::$locale['BDAY_070']."</b></div>\n";
        echo self::zodiakimg();
        echo "</div>\n";
        echo form_button( 'savesettings', self::$locale['save'], self::$locale['save'], [ 'class' => 'btn-primary' ] );
        echo closeform();

    }

    private function zodiakimg() {
        $tx = "";
        for ( $i = 1; $i < 5; $i++ ) {
            $tx .= "<div class='col-sm-2 col-md-2 text-center'>";
            $tx .= "<img src='".BDAY_PATH."images/set".$i."_aquarius.".( $i < 3 ? "png" : "jpg" )."' height='70px'><br />";
            $tx .= form_checkbox( 'zodiakimg', '', $i == $this->settings['zodiakimg'], [ 'reverse_label' => TRUE, 'inner_width' => '100%', 'value' => $i, 'input_id' => 'reset-id-'.$i, 'type'=> 'radio' ] );
            $tx .= "</div>\n";
        }
    	return $tx;
    }
}