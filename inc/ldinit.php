<?php
/**
 * @package ld-connector
 *
 */
namespace Inc;

use Inc\logicaldocwdsl\ldapi;
use Inc\wpusers\linkcontact;
class ldinit
{
    public $ldoc;
    private static $instance = NULL;
    public function __construct()
    {
        linkcontact::create_self();
        add_action( 'user_register',array($this,'new_user_eventlistner'));
        // add_action( 'zpm_project_create',array($this,'project_create_eventlistner'));
        // add_action( 'zpm_project_update',array($this,'project_update_eventlistner'));
        // add_action( 'user_register',array($this,'new_user_eventlistner'));
        // add_action( 'deleted_user',array($this,'new_user_eventlistner'));
        $data_dump=get_userdata(18);
        $profile_data=$data_dump->data;
        $roles=$data_dump->roles;
        echo"<pre>";print_r($data_dump);echo"</pre>";die;

    }
    public function new_user_eventlistner($user_id)
    {
        $this->ldoc=ldapi::create_self();
        $this->ldoc::file_loggerr($user_id);
        $this->ldoc->ldc_new_user($user_id);
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
