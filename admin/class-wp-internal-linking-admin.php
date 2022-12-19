<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.linkedin.com/in/martinkrcho/
 * @since      1.0.0
 *
 * @package    Wp_Internal_Linking
 * @subpackage Wp_Internal_Linking/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Internal_Linking
 * @subpackage Wp_Internal_Linking/admin
 * @author     Martin Krcho <martin.krcho@devstudio.sk>
 */
class Wp_Internal_Linking_Admin {

	const AJAX_ACTION_UPDATE = 'wp-internal-linking-update';

	const AJAX_ACTION_DELETE = 'wp-internal-linking-delete';

	/**
	 * @var Wp_Internal_Linking_Settings $settings
	 */
	public $settings;

	/**
	 * @var Wp_Internal_Linking_Database $database
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

		$this->settings = new Wp_Internal_Linking_Settings();
		$this->database = new Wp_Internal_Linking_Database();

		add_action( 'wp_ajax_' . self::AJAX_ACTION_UPDATE, [ $this, 'update_keyword_entry' ] );
		add_action( 'wp_ajax_' . self::AJAX_ACTION_DELETE, [ $this, 'delete_keyword_entry' ] );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( Wp_Internal_Linking_Settings::is_settings_screen() ) {
			wp_enqueue_style( $this->plugin_name . '-tabulator', plugin_dir_url( __FILE__ ) . 'css/tabulator_semanticui.min.css', [], '5.4.3', 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( Wp_Internal_Linking_Settings::is_settings_screen() ) {
			wp_enqueue_script( $this->plugin_name . '-tabulator', plugin_dir_url( __FILE__ ) . 'js/tabulator.min.js', [], '5.4.3', true );
		}

	}

	public function update_keyword_entry() {
		$data = $_POST;

		if ( !array_key_exists('nonce', $data) || !wp_verify_nonce( $data['nonce'], self::AJAX_ACTION_UPDATE ) ) {
			wp_send_json_error();
		}

		$model          = new Wp_Internal_Linking_Keyword_Model();
		$model->id      = $data['id'];
		$model->keyword = $data['keyword'];
		$model->title   = $data['title'];
		$model->rel     = $data['rel'];
		$model->href    = $data['href'];

		wp_send_json_success(
			[
				'id' => $model->save(),
			]
		);
	}

	public function delete_keyword_entry() {
		$data = $_POST;

		if ( !array_key_exists('nonce', $data) || !wp_verify_nonce( $data['nonce'], self::AJAX_ACTION_DELETE ) ) {
			wp_send_json_error();
		}

		$model     = new Wp_Internal_Linking_Keyword_Model();
		$model->id = $data['id'];

		$model->delete();
		wp_send_json_success();
	}

}
