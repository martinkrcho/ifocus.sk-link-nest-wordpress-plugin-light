<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.linkedin.com/in/martinkrcho/
 * @since      1.0.0
 *
 * @package    iFocus_Link_Nest
 * @subpackage iFocus_Link_Nest/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    iFocus_Link_Nest
 * @subpackage iFocus_Link_Nest/admin
 * @author     Martin Krcho <martin.krcho@devstudio.sk>
 */
class iFocus_Link_Nest_Admin {

	const AJAX_ACTION_UPDATE = 'ifocus-link-nest-update';

	const AJAX_ACTION_DELETE = 'ifocus-link-nest-delete';

	/**
	 * @var iFocus_Link_Nest_Settings_Manager $settings
	 */
	public $settings;

	/**
	 * @var iFocus_Link_Nest_Database $database
	 */
	public $database;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->settings = new iFocus_Link_Nest_Settings_Manager();
		$this->database = new iFocus_Link_Nest_Database();

		add_action( 'wp_ajax_' . self::AJAX_ACTION_UPDATE, array( $this, 'update_keyword_entry' ) );
		add_action( 'wp_ajax_' . self::AJAX_ACTION_DELETE, array( $this, 'delete_keyword_entry' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		if ( iFocus_Link_Nest_Settings_Manager::is_settings_screen() ) {
			wp_enqueue_style( $this->plugin_name . '-tabulator', plugin_dir_url( __FILE__ ) . 'css/tabulator_semanticui.min.css', array(), '5.4.3', 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if ( iFocus_Link_Nest_Settings_Manager::is_settings_screen() ) {
			wp_enqueue_script( $this->plugin_name . '-tabulator', plugin_dir_url( __FILE__ ) . 'js/tabulator.min.js', array(), '5.4.3', true );
		}
	}

	public function update_keyword_entry() {
		$data = $_POST;

		if ( ! array_key_exists( 'nonce', $data ) || ! wp_verify_nonce( $data['nonce'], self::AJAX_ACTION_UPDATE ) ) {
			wp_send_json_error();
		}

		$model          = new iFocus_Link_Nest_Keyword_Model();
		$model->id      = $data['id'];
		$model->keyword = $data['keyword'];
		$model->title   = $data['title'];
		$model->rel     = $data['rel'];
		$model->href    = $data['href'];

		wp_send_json_success(
			array(
				'id' => $model->save(),
			)
		);
	}

	public function delete_keyword_entry() {
		$data = $_POST;

		if ( ! array_key_exists( 'nonce', $data ) || ! wp_verify_nonce( $data['nonce'], self::AJAX_ACTION_DELETE ) ) {
			wp_send_json_error();
		}

		$model     = new iFocus_Link_Nest_Keyword_Model();
		$model->id = $data['id'];

		$model->delete();
		wp_send_json_success();
	}

}
