<?php
/**
 * @package ld-connector
 *
 */
namespace Inc;

use Inc\logicaldocwdsl\ldapi;
use Inc\usercontroller\registersettings;
use Inc\usercontroller\foldercontroller;
use Inc\erpcontroller\clientcontroller;

class ldinit
{
    public $ldoc;
    public $usersettings;
    
    private static $instance = NULL;
    public function __construct()
    {
        $this->usersettings=registersettings::create_self();
        $this->ldoc=ldapi::create_self();

        // new user register
        // add_action( 'user_new_form', array($this,'register_settings'),8);
        
        //trigger dms creation on completion of  new user registeration process
        add_action( 'user_register',array($this,'new_user_eventlistner'));

        // old user modify show linked contact only
        add_action( 'edit_user_profile', array($this,'register_settings'),8);    
        // add_action( 'zpm_project_create',array($this,'project_create_eventlistner'));
        // add_action( 'zpm_project_update',array($this,'project_update_eventlistner'));
        // add_action( 'user_register',array($this,'new_user_eventlistner'));
        // add_action( 'deleted_user',array($this,'new_user_eventlistner'));
    }
    // public function register_settings($user_obj)
    // {
    // }
    public function new_user_eventlistner($user_obj)
    {
        $this->ldoc::file_loggerr($user_obj);
        $this->ldoc->ldc_new_user($user_obj);
        foldercontroller::setup_permission($user_obj,$this->ldoc);
        $this->ldoc->destroy_session();
        ldapi::file_loggerr("triggered logicaldoc new user function");
    }
    
    public function project_create_eventlistner($data)
    {
        //TODO
        $this->ldoc::file_loggerr($data);
    }
    public function project_update_eventlistner($data)
    {
        //TODO
        $this->ldoc::file_loggerr($data);
    }

    public static function create_self()
    {
        if ( is_null( self::$instance ) ) 
        {
            self::$instance = new self;
        }
        return self::$instance;
    }
}
