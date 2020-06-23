<?php
/**
 * @package ld-connector
 *
 */
namespace Inc\usercontroller;
use Inc\erpcontroller\clientcontroller;
use Inc\logicaldocwdsl\ldapi;
class registersettings
{
    private $erpclient=NULL;
    private static $instance = NULL;
    private function __construct()
    {
        ldapi::file_loggerr("adding trigger for new_user form to fetch customer list from erp");
        add_action( 'user_new_form', array($this,'list_erp_customers'),10);
        ldapi::file_loggerr("trigger added for user_new_form for function list_erp_customers with priority 10");
        add_action( 'user_register', array($this,'save_erp_connect'),9);
        add_action( 'edit_user_profile', array($this,'edit_connected_client'));    
    }
    function list_erp_customers( $user )
    { 
        ldapi::file_loggerr("creating new erp controller");
        $this->erpclient=clientcontroller::create_self();
        ldapi::file_loggerr("created new erp controller object");

        ldapi::file_loggerr("New User Form Loaded");
        ?>
        <table class="form-table">
            <tr>
                <th><label for="dropdown">Linked Contact</label></th>
                <td>
                  <select name="ld_erp_linked_client" id="client">
                    <?php
                        $temp_data=$this->erpclient->getcustomers();
                        echo('<option value="0">----Select Here----</option>');
                        foreach($temp_data as $client)
                            {
                                echo('<option value="'.$client['id'].'">'.$client['name'].'</option>');
                            }
                    ?>
                    </select>
                </td>
            </tr>
        </table>
    <?php }

function edit_connected_client($user)
{ 
    ldapi::file_loggerr("creating new erp controller");
    $this->erpclient=clientcontroller::create_self();
    ldapi::file_loggerr("created new erp controller object");
    if($user->roles[0]!='zpm_client' && $user->roles[0]!='subscriber')
    {
        return;
    }
    echo "CKSLD";
    ?>
    <table class="form-table">
        <tr>
            <th><label for="dropdown">Linked Contact</label></th>
            <td>
              <select name="ld_erp_linked_client" id="client" disabled>
                <?php

                    $linked_contact=get_user_meta($user->ID,'erp_contact_id',true);
                    $temp_data=$this->erpclient->getcustomers();
                    echo('<option value="0">----Here Over Here----</option>');
                    foreach($temp_data as $client)
                        {
                            echo('<option value="'.$client['id'].'"'.($client['id']==$linked_contact?'selected':'').'>'.$client['name'].'</option>');
                        }
                ?>
                </select>
            </td>
        </tr>
    </table>
    <?php 
}

    function save_erp_connect($user)
    {
        ldapi::file_loggerr("creating new erp controller");
        $this->erpclient=clientcontroller::create_self();
        ldapi::file_loggerr("created new erp controller object");

        ldapi::file_loggerr("user_register hook called the save_erp_connect function");
        $role=$_POST['role'];
        if($role != 'zpm_client' && $role != 'subscriber')
        {
            return;
        }
        if(isset($_POST['ld_erp_linked_client']) && $_POST['ld_erp_linked_client']!=0)
        {
            
            $link_id=sanitize_text_field($_POST['ld_erp_linked_client']);
            $fields=["id","name","logicaldoc_folder_ref", "logicaldoc_inv_folder_ref", "logicaldoc_quote_folder_ref", "logicaldoc_project_folder_ref"];
            $filter=array('id','=',$link_id);
            $response_erp=$this->erpclient->getcustomers($fields,$filter);
            $temp_data=$response_erp[0];
            $temp_data['erp_contact_name']=$temp_data['name'];
            $temp_data['erp_contact_id']=$temp_data['id'];
            unset($temp_data['name']);
            unset($temp_data['id']);
            foreach($temp_data as $key=>$value)
            {
                update_user_meta($user,$key,$value);
            }
        }
    }
    public static function create_self()
    {
        ldapi::file_loggerr("checking for existing object");
        if (is_null(self::$instance)) 
        {
            ldapi::file_loggerr("object not found calling constructor");
            self::$instance = new self;
        }
        ldapi::file_loggerr("returning new object");
        return self::$instance;
    }

}
