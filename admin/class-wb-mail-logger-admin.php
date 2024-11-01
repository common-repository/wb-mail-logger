<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/webbuilder143/
 * @since      1.0.0
 *
 * @package    Wb_Mail_Logger
 * @subpackage Wb_Mail_Logger/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wb_Mail_Logger
 * @subpackage Wb_Mail_Logger/admin
 * @author     Web Builder 143 <webbuilder143@gmail.com>
 */
class Wb_Mail_Logger_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private static $instance = null;

	private $email_db_id = 0; /* last inserted DB ID */

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public static function get_instance($plugin_name, $version) {
		if(self::$instance == null) {
			self::$instance = new Wb_Mail_Logger_Admin($plugin_name, $version);
		}
		return self::$instance;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		if(isset($_GET['page']) && sanitize_text_field($_GET['page'])==WB_MAIL_LOGGER_PLUGIN_NAME){
			
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wb-mail-logger-admin.css', array(), $this->version, 'all');
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		if(isset($_GET['page']) && sanitize_text_field($_GET['page'])==WB_MAIL_LOGGER_PLUGIN_NAME){

			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wb-mail-logger-admin.js', array('jquery'), $this->version, false);
			$offset = (isset($_GET['offset']) ? absint($_GET['offset']) : 0);
			$search = (isset($_GET['wb_mlr_search']) ? sanitize_text_field($_GET['wb_mlr_search']) : '');
			wp_localize_script($this->plugin_name, 'wb_mlr_params', array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'delete_url' => admin_url('tools.php?page='.WB_MAIL_LOGGER_PLUGIN_NAME.'&offset='.$offset.'&wb_mlr_search='.$search.'&wb_mlr_delete_id='),
				'nonce' => wp_create_nonce(WB_MAIL_LOGGER_PLUGIN_NAME),
				'labels'=>array(
					'unabletoload'=>__('Unable to load email content', 'wb-mail-logger'),
					'areusure'=>__('Are you sure?', 'wb-mail-logger'),
					'chooseforbulk'=>__('Please choose atleast one.', 'wb-mail-logger'),
				)		
			));
		}
	}

	/**
	 * Plugin action links
	 *
	 * @since    1.0.0
	 */
	public function plugin_action_links($links) {
		$links[]='<a href="'.admin_url('tools.php?page='.WB_MAIL_LOGGER_PLUGIN_NAME).'">'.__('Settings', 'wb-mail-logger').'</a>';
		return $links;
	}

	/**
	 * Admin menu
	 *
	 * @since    1.0.0
	 */
	public function admin_menu() {
		add_management_page(
			__('Mail Log', 'wb-mail-logger'),
			__('Mail Log', 'wb-mail-logger'),
			'manage_options',
			WB_MAIL_LOGGER_PLUGIN_NAME,
			array($this, 'logs_page')
		);
	}

	/**
	 * Logs page
	 *
	 * @since    1.0.0
	 */
	public function logs_page() {
		// Lock out non admins
		if (!current_user_can('manage_options')){
		    wp_die(__('You do not have sufficient permission to perform this operation', 'wb-mail-logger'));
		}
		$offset = (isset($_GET['offset']) ? absint($_GET['offset']) : 0);
		$limit = (isset($_GET['limit']) ? absint($_GET['limit']) : 20);
		$search = (isset($_GET['wb_mlr_search']) ? sanitize_text_field($_GET['wb_mlr_search']) : '');
		$mail_list = $this->get_mail_list($offset, $limit, $search);
		$mail_list_count = $this->get_mail_list_count($search);
		require_once plugin_dir_path(__FILE__).'partials/wb-mail-logger-admin-display.php';
	}

	public function delete_mail_log() {
		if(isset($_GET['wb_mlr_delete_id'])) {
			$id = sanitize_text_field($_GET['wb_mlr_delete_id']);
			$id_array = explode(",", $id);
			$id_array_for_delete = array();
			foreach($id_array as $id){
				$id = absint($id);
				if($id > 0){
					$id_array_for_delete[] = $id;
				}
			}

			if(!empty($id_array_for_delete)) {
				
				global $wpdb;
				$table_name = $wpdb->prefix.Wb_Mail_Logger::$mails_tb;
				$placeholders = array_fill(0, count($id_array_for_delete), '%d');

				$sql = $wpdb->prepare("DELETE FROM `$table_name` WHERE `id_wb_mlr_data` IN(".implode(",", $placeholders).")", $id_array_for_delete);
				$wpdb->query($sql);

				$offset = (isset($_GET['offset']) ? absint($_GET['offset']) : 0);
				$search = (isset($_GET['wb_mlr_search']) ? sanitize_text_field($_GET['wb_mlr_search']) : '');
				wp_redirect(admin_url('tools.php?page='.WB_MAIL_LOGGER_PLUGIN_NAME.'&offset='.$offset.'&wb_mlr_search='.$search));
				exit();
			}
		}
	}

	public function mail_detail_page() {		
		$nonce=isset($_POST['security']) && is_string($_POST['security']) ? sanitize_text_field($_POST['security']) : '';
		if(wp_verify_nonce($nonce, WB_MAIL_LOGGER_PLUGIN_NAME)){

			$id = (isset($_POST['wb_mlr_id']) ? absint($_POST['wb_mlr_id']) : 0);
			if($id > 0){

				$mail_data = $this->get_mail_data_by_id($id);
				if($mail_data){
					
					/**
					 * 	Exclude style tag
					 * 	@since 	1.0.3
					 */
					add_filter('wp_kses_allowed_html', array($this, 'exclude_style_tag_from_kses_post'), 10, 2);

					require_once plugin_dir_path(__FILE__).'partials/_detail-view.php';
				}
			}
		}
		exit();
	}

	private function get_sql_search_query_data($search){
		$query_where = "WHERE `to_email` LIKE %s OR `subject` LIKE %s";
		$query_where_values = array_fill(0, 2, "%".$search."%");
		return array($query_where, $query_where_values);
	}

	public function get_mail_list_count($search = "") {
		global $wpdb;
		$table_name = $wpdb->prefix.Wb_Mail_Logger::$mails_tb;
		$sql = "SELECT COUNT(id_wb_mlr_data) as total_rows FROM `$table_name`";
		$query_where = "";
		if(trim($search)!=""){
			$query_where_data = $this->get_sql_search_query_data($search);
			$sql = $wpdb->prepare($sql." ".$query_where_data[0], $query_where_data[1]);
		}

		$row = $wpdb->get_row($sql, ARRAY_A);
		return ($row && isset($row['total_rows']) ? $row['total_rows'] : 0);
	}

	public function get_mail_list($offset = 0, $limit = 20, $search = "") {
		global $wpdb;
		$table_name = $wpdb->prefix.Wb_Mail_Logger::$mails_tb;

		$db_prepare_values = array($offset, $limit);
		$query_where = "";

		if(trim($search)!=""){
			$query_where_data = $this->get_sql_search_query_data($search);
			$query_where = $query_where_data[0];
			$db_prepare_values = array_merge($query_where_data[1], $db_prepare_values);
		}

		$sql = $wpdb->prepare("SELECT * FROM `$table_name` $query_where ORDER BY `id_wb_mlr_data` DESC LIMIT %d, %d", $db_prepare_values);
		$list = $wpdb->get_results($sql, ARRAY_A);
		return ($list ? $list : array());
	}

	public function get_mail_data_by_id($id) {
		global $wpdb;
		$table_name = $wpdb->prefix.Wb_Mail_Logger::$mails_tb;
		$sql = $wpdb->prepare("SELECT * FROM `$table_name` WHERE `id_wb_mlr_data`=%d", $id);
		return $wpdb->get_row($sql, ARRAY_A);
	}

	public function capture_mail_failed($wp_error) {
		if(!$this->email_db_id) {
			return;
		}

		global $wpdb;
		$table_name = $wpdb->prefix.Wb_Mail_Logger::$mails_tb;
		$update_data = array(
			'status' => 0,
			'status_msg' => $wp_error->get_error_message()
		);
		$update_format = array('%d', '%s');

		$where_data = array(
			'id_wb_mlr_data' => $this->email_db_id
		);
		$where_format=array('%d');

		$wpdb->update($table_name, $update_data, $where_data, $update_format, $where_format);
	}

	public function capture_mail_data($mail_data) {
		global $wpdb;
		$table_name = $wpdb->prefix.Wb_Mail_Logger::$mails_tb;
		$attachments = $this->prepare_mail_entity($mail_data, 'attachments', 'array');
		$attachments = $this->abspath_to_basepath($attachments);

		$insert_data=array(
			'to_email' => $this->prepare_mail_entity($mail_data, 'to'),
			'subject' => $mail_data['subject'],
			'message' => $this->prepare_mail_message($mail_data),
			'headers' => $this->prepare_mail_entity($mail_data, 'headers'),
			'attachments' => $attachments,
			'sent_date' => time(),
			'status' => 1, //Assumes mail will success
			'status_msg' => '',
			'created_at' => time(),
		);
		$insert_data_format = array('%s','%s','%s','%s','%s','%d','%d','%s','%d');

		$wpdb->insert($table_name, $insert_data, $insert_data_format);
		$this->email_db_id = $wpdb->insert_id;
		return $mail_data;
	}

	private function prepare_mail_message($mail_data) {
		$message = '';
		if(isset($mail_data['message'])) {
            $message = $mail_data['message'];
        } elseif(isset($mail['html'])) {
            $message = $mail_data['html'];
        }
        return $message;
	}

	private function truncate_string_mid($text, $max_length = 15, $filler = "...", $side_padding = 4)
	{
		$text_length = strlen($text);
		if($text_length > ($max_length + ($side_padding * 2))) {
			return substr_replace($text, $filler, ($max_length/2), ($text_length-$max_length));	
		}
		return $text;
	}

	private function basepath_to_absurl($path_arr) {
		$out = array();
		foreach($path_arr as $k => $path) {
			$file_name = basename($path);
			if(strpos($path, '://') === false) { /* relative path, so convert it to absolute */
				$out[$file_name] = content_url($path);
			} else {
				$out[$file_name] = $path;
			}
		}
		return $out;
	}

	private function abspath_to_basepath($path_arr, $return='string') {
		$content_dir_name = basename(WP_CONTENT_DIR);
		foreach($path_arr as $k => $path) {
			$path_parts = explode($content_dir_name, $path);
			$path_arr[$k] = trim(end($path_parts), "\/");
		}
		return ($return == 'string' ? maybe_serialize($path_arr) : $path_arr);
	}

	private function prepare_mail_entity($mail_data, $entity_key, $return = 'string') {
		$entity = (isset($mail_data[$entity_key]) ? $mail_data[$entity_key] : array());
		if(is_string($entity)) {
			$entity = $this->split_mail_entity($entity);
		}
		return ($return == 'string' ? maybe_serialize($entity) : $entity);
	}

	private function split_mail_entity($txt)
	{
		$arr = preg_split("/(,|,\s)/", $txt);
		return (is_array($arr) ? $arr : array());
	}


	/**
	 *  Exclude style tag while sanitizing email content before printing
	 * 
	 * 	@since 	1.0.3
	 * 	@param 	array 	$tags 		array of tags
	 * 	@param 	string 	$context 	Context
	 */
	public function exclude_style_tag_from_kses_post($tags, $context)
	{
		if('post' === $context)
		{
	        $tags['style'] = array(
	            'type' => true,
	            'scoped' => true,
	        );
	    }

	    return $tags;
	}
}