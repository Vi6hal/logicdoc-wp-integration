<?php
/**
 * @package ld-connector
 *
 */
namespace Inc\logicaldocwdsl;
use SoapClient;
use Inc\usercontroller\datacontroller;

class ldapi
{
    public static $logfile = NULL;
    private static $instance = null;
    private $session_var=null;
    private $ldoc_user=null;
    private $ldoc_pass=null;
    private $ldoc_url=null;
    private $authClient=null;
    private $securityClient=null;
    private $folderClient=null;

    private function __construct()
    {
        $this->ldoc_user =get_option('dms_master');
        $this->ldoc_pass =get_option('dms_slave');
        $this->ldoc_url =get_option('dms_url');
        $this->authClient = new SoapClient($this->ldoc_url . '/services/Auth?wsdl');
        $this->securityClient = new SoapClient($this->ldoc_url . '/services/Security?wsdl');
        $this->folderClient=new SoapClient($this->ldoc_url . '/services/Folder?wsdl');
        $this->set_session();
    }

    private function set_session()
    {
        try {
            $loginParams = array('username' => $this->ldoc_user, 'password' => $this->ldoc_pass);
            $result = $this->authClient->login($loginParams);
            $this->session_var = $result->return;
        }
        catch(SoapFault $e)
        {
            ldapi::file_loggerr($e);
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
            ldapi::file_loggerr($e);
        }
        
    }

    public function ldc_new_user($user_id)
    {
        try {
            $temp_usr=datacontroller::ld_set_user_data($user_id);
            if ($temp_usr && $this->session_var) 
            {
                $temp_usr['groupIds']=$this->set_ld_user_group($user_id,$temp_usr['groupIds']);
                $result=$this->ldc_register_user($temp_usr);
                ldapi::file_loggerr("new usercreation triggerd for logicaldoc");
                $temp_usr['logicdoc_user_id']=$result->userId;                
                datacontroller::update_ld_user_details($user_id,$temp_usr);
                ldapi::file_loggerr("updated data to db");

            }
        }
        catch(exception $e)
        {
            ldapi::file_loggerr($e);
        }
    }
    public function folder_access_controller($folder_id,$user_id,$acc_flag=0,$recursive=false)
    {
        // acc_flag '0'= revoke '1' = grant
        try {
            $userParams = array
            (
                'sid' => $this->session_var,
                'folderId'=>$folder_id,
                'userId' => $user_id,
                'permissions'=>$acc_flag,
                'recursive'=>$recursive,
            );
            $result = $this->folderClient->storeUser($userParams);
            return $result??FALSE;
            }
        catch(exception $e)
        {
            ldapi::file_loggerr($e);
        }
    }
    public function ldc_register_user($user_data)
    {
        try {
                $userParams = array('sid' => $this->session_var, 'user' => $user_data);
                $result = $this->securityClient->storeUser($userParams);
                return $result??FALSE;
            }
        catch(SoapFault $e)
        {
            ldapi::file_loggerr($e);
        }
    }
    public static function create_self()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    public static function file_loggerr($log_data)
    {
        self::$logfile=dirname(__FILE__, 3).'/'.'apilog.txt';

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
                ldapi::file_loggerr($group->name);
                return [$group->id];
            }
        }
    }

}
