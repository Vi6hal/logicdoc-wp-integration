<?php
/**
 * @package ld-connector
 *
 */
namespace Inc\usercontroller;
class foldercontroller
{
    private static $ldoc=NULL;
    private static $user=NULL;
    private static $ldocuser_meta_id='logicaldoc_user_id';
    private static $ldocuser_meta_name='logicaldoc_username';
    private static $ldocuser_meta_rootf='logicaldoc_folder_ref';

    // client specific keys
    private static $ldocclient_meta_invf='logicaldoc_inv_folder_ref';
    private static $ldocclient_meta_quotf='logicaldoc_quote_folder_ref';
    private static $ldocclient_meta_prof='logicaldoc_project_folder_ref';





    public static function setup_permission($user,$ldoc_obj)
    {
        self::$ldoc=$ldoc_obj;
        self::$user=$user;
        $role=self::$user->roles[0];
        switch($role)
        {
            case'zpm_client':
                self::zpm_client();
                break;
            case'zpm_user':
                self::zpm_user();
                break;
            case'zpm_frontend_user':
                self::zpm_frontend_user();
                break;
            case'zpm_manager':
                self::zpm_manager();
                break;
            case'zpm_admin':
                self::zpm_admin();
                break;
            case'administrator':
                return 'admin';
                break; 
            case'subscriber':
                self::zpm_client();
                break;
            default:
                return 'guest';
                break;           
        }
        // extract user role
        // get users logicaldoc user
        // based on user role call differnt function for access control
        // 
    }
    private static function zpm_client()
    {
        $ld_usr=self::get_ldoc_userID();
        self::grant_client_rootfolder($ld_usr);
    }
    private static function zpm_user()
    {

    }
    private static function zpm_frontend_user()
    {

    }
    private static function zpm_manager()
    {

    }
    private static function zpm_admin()
    {

    }
    private static function administrator()
    {

    }
    private static function subscriber()
    {

    }
    private static function guest()
    {
        // NOTHING TO BE DONE
    }

    private static function grant_client_rootfolder($logicaldoc_usr_id)
    {
        $root_folder=self::get_root_folder();
        return self::$ldoc->folder_access_controller($root_folder,$logicaldoc_usr_id,1,true)??false;
    }



    // generic wrappers
    private static function get_ldoc_userID()
    {
        return get_user_meta(self::$user->ID,self::$ldocuser_meta_id,true);    
    }
    private static function get_root_folder()
    {
        return get_user_meta(self::$user->ID,self::$ldocuser_meta_rootf,true);    
    }

}   