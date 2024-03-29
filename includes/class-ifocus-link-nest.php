<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.linkedin.com/in/martinkrcho/
 * @since      1.0.0
 *
 * @package    iFocus_Link_Nest
 * @subpackage iFocus_Link_Nest/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    iFocus_Link_Nest
 * @subpackage iFocus_Link_Nest/includes
 * @author     Martin Krcho <martin.krcho@devstudio.sk>
 */
class iFocus_Link_Nest {

	/**
	 * Prevents the the_content filter from being applied recursively.
	 *
	 * @var bool True if the content is already being processed.
	 */
	protected $already_processing_content = false;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      iFocus_Link_Nest_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'IFOCUS_LINK_NEST_VERSION' ) ) {
			$this->version = IFOCUS_LINK_NEST_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ifocus-sk-link-nest-lite';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		$this->loader->add_filter( 'the_content', $this, 'process_post_content', 0, 1 );
		$this->loader->add_action( 'post_updated', $this, 'on_post_updated', 10, 3 );
	}

	/**
	 * Purges the post settings fingerprint when a post is updated.
	 *
	 * @param int     $post_ID Post ID.
	 * @param WP_Post $post_after Post object following the update.
	 * @param WP_Post $post_before Post object before the update.
	 */
	public function on_post_updated( $post_ID, $post_after, $post_before ) {
		delete_post_meta( $post_ID, iFocus_Link_Nest_Post_Content_Handler::$fingerprint_meta_name );
	}

	public function process_post_content( $content ) {
		if ( $this->already_processing_content ) {
			return $content;
		}

		$this->already_processing_content = true;

		$handler = new iFocus_Link_Nest_Post_Content_Handler( get_the_ID(), $content );
		$handler->execute();

		return $handler->get_result();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - iFocus_Link_Nest_Loader. Orchestrates the hooks of the plugin.
	 * - iFocus_Link_Nest_i18n. Defines internationalization functionality.
	 * - iFocus_Link_Nest_Admin. Defines all hooks for the admin area.
	 * - iFocus_Link_Nest_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ifocus-link-nest-csv-import.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ifocus-link-nest-keyword-model.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ifocus-link-nest-post-content-handler.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ifocus-link-nest-text-processor.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ifocus-link-nest-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ifocus-link-nest-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ifocus-link-nest-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ifocus-link-nest-database.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ifocus-link-nest-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ifocus-link-nest-settings-manager.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ifocus-link-nest-public.php';

		$this->loader = new iFocus_Link_Nest_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the iFocus_Link_Nest_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new iFocus_Link_Nest_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain', 5 );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new iFocus_Link_Nest_Admin( $this->get_plugin_name(), $this->get_version(), $this->loader );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new iFocus_Link_Nest_Public( $this->get_plugin_name(), $this->get_version() );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    iFocus_Link_Nest_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
