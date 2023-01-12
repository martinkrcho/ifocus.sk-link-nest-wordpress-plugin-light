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
class iFocus_Link_Nest_Post_Content_Handler {

	/**
	 * @var string Name of the post meta storing the content fingerprint.
	 */
	public static $fingerprint_meta_name = 'focus_link_nest_fingerprint';

	/**
	 * @var int
	 */
	private $post_id;

	/**
	 * @var string
	 */
	private $content;

	/**
	 * @var iFocus_Link_Nest_Settings
	 */
	private $settings;

	/**
	 * @var bool True if links should be removed from the content.
	 */
	private $clear_links = false;

	/**
	 * @var bool True if the keywords should be replaced in the content.
	 */
	private $replace_keywords = false;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $post_id, $content ) {
		$this->post_id  = $post_id;
		$this->content  = $content;
		$this->settings = iFocus_Link_Nest_Settings_Manager::build_settings();
	}

	public function execute() {

		$post_type = get_post_type( $this->post_id );
		if ( ! in_array( $post_type, array( 'post', 'page' ) ) ) {
			return;
		}

		if ( 'page' === $post_type && ! $this->settings->can_process_pages() ) {
			$this->clear_links = true;

			return;
		}

		if ( 'post' === $post_type && ! $this->settings->can_process_posts() ) {
			$this->clear_links = true;

			return;
		}

		if ( $this->settings->is_post_excluded( $this->post_id ) ) {
			$this->clear_links = true;

			return;
		}

		$last_settings_update = iFocus_Link_Nest_Settings_Manager::get_last_updated_time();
		$fingerprint_value    = get_post_meta( $this->post_id, self::$fingerprint_meta_name, true );
		if ( $fingerprint_value !== $last_settings_update ) {
			$this->clear_links      = true;
			$this->replace_keywords = true;
		}
	}

	public function get_result() {

		$post_needs_updating = false;
		$content_before      = $this->content;
		if ( $this->clear_links ) {
			// Remove existing links.
			$this->content = iFocus_Link_Nest_Text_Processor::strip_links( $this->content );

			// Purge the settings fingerprint.
			delete_post_meta( $this->post_id, self::$fingerprint_meta_name );

			$post_needs_updating = true;
		}

		if ( $this->replace_keywords ) {
			// Highlight keywords.
			$keywords      = iFocus_Link_Nest_Keyword_Model::get_all();
			$processor     = new iFocus_Link_Nest_Text_Processor( $this->settings, $keywords );
			$this->content = $processor->process( $this->content );

			$post_needs_updating &= $processor->has_text_changed();
		}

		if ( $post_needs_updating && $content_before !== $this->content ) {

			// Update post content and save fingerprint meta.
			wp_update_post(
				array(
					'ID'           => $this->post_id,
					'post_content' => $this->content,
				),
				false,
				false
			);
			add_post_meta( $this->post_id, self::$fingerprint_meta_name, iFocus_Link_Nest_Settings_Manager::get_last_updated_time(), true );
		}

		return $this->content;
	}
}
