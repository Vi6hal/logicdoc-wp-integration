<?php
/**
 * @package ld-connector
 *
 */
namespace Inc\usercontroller;
class datacontroller
{

    static function makepassword($length = 8) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
    
    static function ld_set_user_data($user_id=-1)
    {
        if($user_id<1)
        {
            return false;
        }
        global $wp;
        $user=[];
        $data_dump=get_userdata($user_id);
        $profile_data=$data_dump->data;
        $roles=$data_dump->roles;
        $user['email']=$profile_data->user_email??$profile_data->user_nicename.$profile_data->ID.'@kennovation-services.com';
        $user['username']=$profile_data->user_login??'Client'.$profile_data->ID;
        $user['firstName'] =$profile_data->user_nicename??'Client'.$profile_data->ID;
        $user['name'] =$profile_data->user_nicename??'Client'.$profile_data->ID;

        // passing just the role[0] as the primanry role (please refer line 63 get_human_rolename)
        $user['groupIds'] = $roles[0];      

        // login creds
        $user['userName'] = $profile_data->user_login??'Client'.$profile_data->ID;
        $user['password'] = datacontroller::makepassword(); // default 8 chars min passwd length 

        //end here 
        // Default fields needed by ldoc
        $user['language'] = 'en';
        $user['enabled'] = 1;
        $user['id'] = 0;
        $user['passwordExpires'] = 0; // if 1 the password is eligible for expiration, if 0 the password never expires
        $user['quota'] = -1;  // maximum allowed user's quota expressed in bytes, -1 for no limits
        $user['quotaCount'] = 0;
        $user['source'] = 0; // must be 0
        $user['type'] = 0; // must be 0

        return $user;
    }

    static function update_ld_user_details($user_id,$temp_usr)
    {
        $user_details['logicaldoc_username']=$temp_usr['username'];
        $user_details['logicaldoc_pass']=$temp_usr['password'];
        $user_details['logicaldoc_user_id']=$temp_usr['logicdoc_user_id'];
        global $wp;
        foreach($user_details as $key=>$val)
        {
            update_user_meta($user_id,$key,$val);
        }
    }

    static function get_human_rolename($role)
    {
        switch($role)
        {
            case'zpm_client':
                return 'Client';
                break;
            case'zpm_user':
                return 'Associate';
                break;
            case'zpm_frontend_user':
                return 'Associate';
                break;
            case'zpm_manager':
                return 'Manager';
                break;
            case'zpm_admin':
                return 'Partner';
                break;
            case'administrator':
                return 'admin';
                break; 
            case'subscriber':
                return 'Client';
                break;
            default:
                return 'guest';
                break;           
        }
    }
}