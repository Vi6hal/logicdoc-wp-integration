<?php
/**
 * @package ld-connector
 *
 */
namespace Inc\usercontroller;
use Inc\erpcontroller\clientcontroller;
class registersettings
{
    private $erpclient=NULL;
    private static $instance = NULL;
    private function __construct()
    {
        $this->erpclient=clientcontroller::create_self();
        add_action( 'user_new_form', array($this,'list_erp_customers'));
        add_action( 'user_register', array($this,'save_erp_connect'),9);

        add_action( 'edit_user_profile', array($this,'edit_connected_client'));    
        add_action( 'edit_user_profile_update', array($this,'save_erp_connect') );
    }
    function list_erp_customers( $user )
    { ?>
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
    if($user->roles[0]!='zpm_client')
    {
        return;
    }
    ?>
    <table class="form-table">
        <tr>
            <th><label for="dropdown">Linked Contact</label></th>
            <td>
              <select name="ld_erp_linked_client" id="client">
                <?php
                    $temp_data=$this->erpclient->getcustomers();
                    echo('<option value="0">----Well Not Here----</option>');
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

    function save_erp_connect($user)
    {
        $role=$_POST['role'];
        if($role != 'zpm_client')
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
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return;
    }

}