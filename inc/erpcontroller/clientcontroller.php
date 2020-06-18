<?php
/**
 * @package ld-connector
 *
 */
namespace Inc\erpcontroller;
use Ripcord\Ripcord;

class clientcontroller
{
    private $session_user=1;
    private $erp_user=null;
    private $erp_pass=null;
    private $erp_url=null;
    private $erp_db=null;
    private $rpc_client=null; 
    private $rpc_model=null;
    private static $instance = NULL;

    private function __construct()
    {
        $this->erp_user =get_option('erp_master');
        $this->erp_pass =get_option('erp_slave');
        $this->erp_url =get_option('erp_url');
        $this->erp_db =get_option('erp_db');
        $this->rpc_client=Ripcord::client($this->erp_url.'/xmlrpc/2/common');
        $this->rpc_model=Ripcord::client($this->erp_url.'/xmlrpc/2/object');
        $this->set_session();
    }
    private function set_session()
    {
        $this->session_user=$this->rpc_client->authenticate($this->erp_db, $this->erp_user, $this->erp_pass, array());
    }
    public static function create_self()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function getcustomers($fields=["id","name"],$additional_filter=false)
    {
        $default_filter=array
            (
                array('customer', '=', true),
                array('parent_id', '=', false),
                array('logicaldoc_folder_ref', '!=', 0),
                array('name', '!=', '')
            );

        if($additional_filter)
            {
                $default_filter=array
                (
                    $additional_filter
                );
            }

        return $this->rpc_model->execute_kw
            (
                $this->erp_db,
                intval($this->session_user),
                $this->erp_pass,
                'res.partner',
                'search_read', 
                array
                    (
                        $default_filter
                    ),
                array
                (
                    'fields'=>$fields,
                    'order'=> 'create_date desc'
                )
            );
    }

    
}
