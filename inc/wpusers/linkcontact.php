<?php
/**
 * @package ld-connector
 *
 */
namespace Inc\wpusers;
class linkcontact
{
    private static $instance = null;
    private function __construct()
    {
        add_action( 'user_new_form', array($this,'register_contact_field'));
        add_action( 'show_user_profile', array($this,'register_contact_field'));
        // add_action( 'edit_user_profile', array($this,'show_contact_field'));    
        add_action( 'edit_user_profile', array($this,'register_contact_field'));    
        add_action( 'user_register', array($this,'save_erp_link'),9);
        // add_action( 'personal_options_update', array($this,'save_erp_link') );
        add_action( 'edit_user_profile_update', array($this,'save_erp_link') );



    }
    function register_contact_field( $user )
    { ?>
        <table class="form-table">
            <tr>
                <th><label for="dropdown">Linked Contact</label></th>
                <td>
                <input type="select" id="linked_client" name="client-link" value=''/>
                <datalist id="clients">
                    <option value="1">Kenn</option>
                    <option value="2">Merex</option>
                    <option value="3">Cipla</option>
                    <option value="4">XYz</option>
                </datalist>
                </td>
            </tr>
        </table>
    <?php }

    function show_contact_field( $user )
    { ?>
        <table class="form-table">
            <tr>
                <th><label for="dropdown">Linked Contact</label></th>
                <td>
                    <input type="text" disabled value=<?php echo esc_attr( $user->ID??'Cannot change it once it is set'); ?>><br/>
                </td>
            </tr>
        </table>
    <?php }
    function save_erp_link($user)
    {
        update_user_meta($user,'logicdoc_rf',$_POST['client-link']);
    }
    public static function create_self()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return;
    }

}