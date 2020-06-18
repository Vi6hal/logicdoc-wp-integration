<?php
/**
 * @package ld-connector
 *
 */
namespace Inc\logicaldocwdsl;
use SoapClient;
use Inc\wpusers\datacontroller;


class ldapi
{
    public static $logfile = 'apilog.txt';
    private static $instance = null;
    private $session_var=null;
    public $ldoc_user=null;
    public $ldoc_pass=null;
    public $ldoc_url=null;
    public $authClient=null;
    public $securityClient=null;
    private function __construct()
    {
        $this->ldoc_user ='admin';
        $this->ldoc_pass ='T7IJGXsghnK';
        $this->ldoc_url= 'http://startupdms.kennovation-services.com:8080';
        $this->authClient = new SoapClient($this->ldoc_url . '/services/Auth?wsdl');
        $this->securityClient = new SoapClient($this->ldoc_url . '/services/Security?wsdl');
        $this->set_session();
    }

    private function set_session()
    {
        try {
            $loginParams = array('username' => $this->ldoc_user, 'password' => $this->ldoc_pass);
            $result = $this->authClient->login($loginParams);
            $this->session_var = $result->return;
            ldapi::file_loggerr('session set:'.$result->return??'undefined');
        }
        catch(SoapFault $e)
        {
            self::$instance->file_loggerr($e);
        }
    } 
    private function destroy_session()
    {
        $logoutParams = array ('sid' => $this->session_var );
        try {
            $result = $this->authClient->logout($logoutParams);
        }
        catch(SoapFault $e)
        {
            self::$instance->file_loggerr($e);
        }
        
    }

    public function ldc_new_user($user_id)
    {
        try {
            $temp_usr=datacontroller::ld_set_user_data($user_id);
            if ($temp_usr && $this->session_var) 
            {
                self::$instance->file_loggerr("ld user created against user:".print_r($temp_usr['groupIds']));
                $temp_usr['groupIds']=$this->set_ld_user_group($user_id,$temp_usr['groupIds']);
                $userParams = array('sid' => $this->session_var, 'user' => $temp_usr);
                $result = $this->securityClient->storeUser($userParams);
                self::$instance->file_loggerr("ld user created against user:".$user_id);
                $temp_usr['logicdoc_user_id']=$result->userId;                
                datacontroller::update_ld_user_details($user_id,$temp_usr);
                $this->destroy_session();
            }
        }
        catch(exception $e)
        {
            self::$instance->file_loggerr($e);
        }
    }
    public static function create_self()
    {
        self::$logfile=dirname(__FILE__, 3).'/'.ldapi::$logfile;
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    public static function file_loggerr($log_data)
    {
        // self::$instance->file_loggerr("calling create self");
        if(!file_exists(self::$logfile))
        {
            $fp=fopen(self::$logfile, "w");
            fclose($fp);
        }
        $fp = fopen(self::$logfile, 'a');
        fwrite($fp, $log_data."\t".date("d-m-Y H:i:s")."\n");
        fclose($fp);
    }
    public function set_ld_user_group($user_id,$user_role)
    {
        $human_role=datacontroller::get_human_rolename($user_role);
        $searchparams=array ('sid'=>$this->session_var);
        $result = $this->securityClient->listGroups($searchparams);
        $group_list=$result->group;
        foreach($group_list as $group)
        {
            if($group->name==$human_role) 
            {
                self::$instance->file_loggerr($group->name);
                return [$group->id];
            }
        }
    }

}