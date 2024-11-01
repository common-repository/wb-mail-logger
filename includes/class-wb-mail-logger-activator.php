<?php

/**
 * Fired during plugin activation
 *
 * @link       https://profiles.wordpress.org/webbuilder143/
 * @since      1.0.0
 *
 * @package    Wb_Mail_Logger
 * @subpackage Wb_Mail_Logger/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wb_Mail_Logger
 * @subpackage Wb_Mail_Logger/includes
 * @author     Web Builder 143 <webbuilder143@gmail.com>
 */
class Wb_Mail_Logger_Activator {

	/**
     *
     * @since    1.0.0
     */
    public static function activate() {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );       
        if(is_multisite()) 
        {
            // Get all blogs in the network and activate plugin on each one
            $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            foreach($blog_ids as $blog_id ) 
            {
                switch_to_blog( $blog_id );
                self::install_tables();
                restore_current_blog();
            }
        }
        else 
        {
            self::install_tables();
        }
    }

    public static function install_tables()
    {
        global $wpdb;
        //install necessary tables
        
        //creating table for mail data================
        $search_query = "SHOW TABLES LIKE %s";
        $charset_collate = $wpdb->get_charset_collate();
        $tb='wb_mlr_data';
        $like = '%' . $wpdb->prefix.$tb.'%';
        $table_name = $wpdb->prefix.$tb;
        if(!$wpdb->get_results($wpdb->prepare($search_query, $like), ARRAY_N)) 
        {
            $sql="CREATE TABLE IF NOT EXISTS `$table_name` (
                `id_wb_mlr_data` INT NOT NULL AUTO_INCREMENT, 
                `to_email` text COLLATE utf8mb4_general_ci NOT NULL,
                `subject` text COLLATE utf8mb4_general_ci NOT NULL,
                `message` longtext COLLATE utf8mb4_general_ci NOT NULL,
                `headers` text COLLATE utf8mb4_general_ci NOT NULL,
                `attachments` text COLLATE utf8mb4_general_ci NOT NULL,
                `sent_date` int(11) NOT NULL DEFAULT '0',
                `status` int(11) NOT NULL DEFAULT '0',
                `status_msg` text COLLATE utf8mb4_general_ci NOT NULL,
                `created_at` int(11) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id_wb_mlr_data`)
            ) DEFAULT CHARSET=utf8;";
            dbDelta($sql);
        }
        //creating table for mail data================
    }

}
