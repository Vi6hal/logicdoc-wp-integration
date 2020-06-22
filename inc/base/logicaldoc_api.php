<?php
/**
 * @package ld-connector
 *
 */
namespace Inc\base;

class logicaldoc_api
{
    private $session_var=null;
    private $ldoc_user=null;
    private $ldoc_pass=null;
    private $ldoc_url=null;
    private $authClient=null;
    private $securityClient=null;
    private $folderClient=null;
    // TODO
    private function __construct()
    {
        $this->ldoc_user =get_option('dms_master');
        $this->ldoc_pass =get_option('dms_slave');
        $this->ldoc_url =get_option('dms_url');
        $this->authClient = new SoapClient($this->ldoc_url . '/services/Auth?wsdl');
        $this->securityClient = new SoapClient($this->ldoc_url . '/services/Security?wsdl');
        $this->folderClient=new SoapClient($this->ldoc_url . '/services/Folder?wsdl');
    }

    //connect
    public function set_session()
    {
        try {
            $loginParams = array('username' => $this->ldoc_user, 'password' => $this->ldoc_pass);
            $result = $this->authClient->login($loginParams);
            $this->session_var = $result->return;
        }
        catch(SoapFault $e)
        {
            // 
        }
    }

    // Disconnect
    public function destroy_session()
    {
        $logoutParams = array ('sid' => $this->session_var );
        try {
            $result = $this->authClient->logout($logoutParams);
            $this->session_var=NULL;
        }
        catch(SoapFault $e)
        {
            ldapi::file_loggerr($e);
        }
        
    }

    //create new user 
    // https://docs.logicaldoc.com/resources/wsdoc/soap/7.7.4/Security.html#method3
    // send in an array of required fileds
    public function ldc_register_user($user_username,$user_email,$user_groupIds=array())
    {
        $req_data=array(
            'username'=>$user_username??'',
            'email'=>$user_email??'',
            'groupIds'=>$user_groupIds??[-1],
            'password'=>$this->makepassword(),
            'language'=>'en',
            'enabled'=>1, 
            'type'=>0,
            'passwordExpires'=>0,
            'source'=>0,
            'quota'=>-1,
            'quotaCount'=>0,
        );
        if($this->session_var==NULL || empty($req_data))
        {
            return false;
        }
        try {
                $userParams = array('sid' => $this->session_var, 'user' => $req_data);
                $result = $this->securityClient->storeUser($userParams);
                return $result??FALSE;
            }
        catch(SoapFault $e)
        {
            ldapi::file_loggerr($e);
        }
    }
    private function makepassword($length = 10){
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
}
