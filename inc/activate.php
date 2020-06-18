<?php
/**
 * @package ld-connector
 */

//  https://developer.wordpress.org/reference/functions/update_option/

namespace Inc;
class activate
{
    static function set_ldwp_options()
    {
        global $wp;
        // update_option( string $option, mixed $value, string|bool $autoload = null )
        update_option('dms_url','http://startupdms.kennovation-services.com:8080',false);
        update_option('dms_master','admin',false);
        update_option('dms_slave','T7IJGXsghnK',false);

        update_option('erp_url','http://startuperp.kennovation-services.com:8069',false);
        update_option('erp_db','TEST_2020',false);
        update_option('erp_master','admin',false);
        update_option('erp_slave','Kanj@Admin!2020',false);
    }
}