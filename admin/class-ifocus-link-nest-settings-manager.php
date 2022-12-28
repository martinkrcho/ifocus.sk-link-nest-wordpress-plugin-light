<?php

/**
 * The file that defines the plugin settings class.
 *
 * @link       https://www.linkedin.com/in/martinkrcho/
 * @since      1.0.0
 *
 * @package    iFocus_Link_Nest
 * @subpackage iFocus_Link_Nest/admin
 */

/**
 * The plugin settings class.
 *
 * @since      1.0.0
 * @package    iFocus_Link_Nest
 * @subpackage iFocus_Link_Nest/includes
 * @author     Martin Krcho <martin.krcho@devstudio.sk>
 */
class iFocus_Link_Nest_Settings_Manager {

	/**
	 * @var string Name of the option storing the plugin settings.
	 */
	private static $option_name = 'ifocus_link_nest_settings';

	/**
	 * @var string Suffix of the option storing the timestamp of last plugin settings update.
	 */
	private static $last_updated_option_suffix = '_last_updated';

	/**
	 * @var string Name of the post meta storing the cached post content containing keyword links.
	 */
	private static $content_cache_meta_name = 'focus_link_nest_cache';

	/**
	 * Define the plugin settings functionality.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$options = self::get_settings();

		if ( ! class_exists( 'RationalOptionPages' ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'jeremyHixon' . DIRECTORY_SEPARATOR . 'RationalOptionPages' . DIRECTORY_SEPARATOR . 'RationalOptionPages.php';
		}

		$this->init_settings();

		add_filter( 'pre_update_option_' . self::$option_name, array( $this, 'handle_file_upload' ), 10, 3 );
		add_action( 'update_option_' . self::$option_name, array( $this, 'on_settings_updated' ), 10, 3 );
	}

	/**
	 * Handles file upload and data import before the settings are updated.
	 *
	 * @param mixed  $value The new, unserialized option value.
	 * @param string $option Name of the option.
	 * @param mixed  $old_value The old option value.
	 */
	public function handle_file_upload( $value, $option, $old_value ) {
		if ( ! is_array( $value ) || ! array_key_exists( iFocus_Link_Nest_Settings::CSV_FILE, $value ) ) {
			return $value;
		}

		if ( empty( $_FILES ) || ! array_key_exists( self::$option_name, $_FILES ) ) {
			return $value;
		}

		if ( ! array_key_exists( iFocus_Link_Nest_Settings::CSV_FILE, $_FILES[ self::$option_name ]['error'] ) || $_FILES[ self::$option_name ]['error'][ iFocus_Link_Nest_Settings::CSV_FILE ] > 0 ) {
			return $value;
		}

		// Parse and import CSV file.
		iFocus_Link_Nest_Csv_Import::import( $_FILES[ self::$option_name ]['tmp_name'][ iFocus_Link_Nest_Settings::CSV_FILE ] );

		$value[ iFocus_Link_Nest_Settings::CSV_FILE ] = time();

		return $value;
	}

	/**
	 * Fires after the plugin setting have been successfully updated.
	 *
	 * It stores the timestamp of the last update in a separate option.
	 *
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value The new option value.
	 * @param string $option Option name.
	 */
	public function on_settings_updated( $old_value, $value, $option ) {
		$last_updated_option_name = $option . self::$last_updated_option_suffix;
		$current_last_updated     = get_option( $last_updated_option_name );
		if ( false !== $current_last_updated ) {
			// Purge the cached content from all posts.
			delete_post_meta_by_key( self::$content_cache_meta_name . '_' . $current_last_updated );
		}

		update_option( $last_updated_option_name, time() );
	}

	/**
	 * Retrieve plugin settings.
	 *
	 * @return array
	 */
	public static function get_settings() {
		return wp_parse_args(
			get_option( self::$option_name, array() ),
			array(
				iFocus_Link_Nest_Settings::CSV_FILE       => '',
				iFocus_Link_Nest_Settings::PREVENT_DUPLICATES => 'yes',
				iFocus_Link_Nest_Settings::PROCESS_POSTS  => 'yes',
				iFocus_Link_Nest_Settings::PROCESS_PAGES  => 'yes',
				iFocus_Link_Nest_Settings::MAX_LINKS      => 3,
				iFocus_Link_Nest_Settings::MAX_KEYWORDS_LINKS => 1,
				iFocus_Link_Nest_Settings::MAX_SAME_URL   => 1,
				iFocus_Link_Nest_Settings::CASE_SENSITIVE => 'yes',
				iFocus_Link_Nest_Settings::OPEN_IN_NEW_WINDOW => 'yes',
				iFocus_Link_Nest_Settings::EXCLUDE_HEADINGS => 'yes',
				iFocus_Link_Nest_Settings::IGNORED_POSTS  => array(),
				iFocus_Link_Nest_Settings::IGNORED_WORDS  => array(),
			)
		);
	}

	private function init_settings() {
		$intro_html  = '<p>';
		$intro_html .= esc_html__( 'With iFOCUS.sk Link Nest plugin you can easily and automatically link from keywords and phrases in posts and pages to corresponding posts and pages or any other URL. Set the following settings to your own needs and let iFOCUS.sk Link Nest plugin do the work for you.', 'ifocus-link-nest' );
		$intro_html .= '</p>';
		$intro_html .= '<p>';
		$intro_html .= esc_html__( 'If you find any bugs or you have ideas for the plugin, let us know at:', 'ifocus-link-nest' );
		$intro_html .= '<br />';
		$intro_html .= '<a href="http://wordpress.org/support/plugin/ifocus-link-nest" target="_blank">http://wordpress.org/support/plugin/ifocus-link-nest</a>';
		$intro_html .= '</p>';

		$pages = array(
			self::$option_name => array(
				'menu_title'  => esc_html__( 'Internal linking', 'ifocus-link-nest' ),
				'menu_slug'   => 'ifocus-link-nest',
				'parent_slug' => 'options-general.php',
				'page_title'  => esc_html__( 'iFOCUS.sk Link Nest plugin - Internal linking', 'ifocus-link-nest' ),
				'text'        => $intro_html,
				'sections'    => array(
					'custom-keywords' => array(
						'title'    => esc_html__( 'Custom Keywords', 'ifocus-link-nest' ),
						'callback' => array( $this, 'custom_keyword_intro' ),
						'fields'   => array(
							iFocus_Link_Nest_Settings::CSV_FILE           => array(
								'id'          => iFocus_Link_Nest_Settings::CSV_FILE,
								'title'       => esc_html__( 'Import from CSV', 'ifocus-link-nest' ),
								'type'        => 'file',
								'placeholder' => esc_html__( 'select CSV file', 'ifocus-link-nest' ),
								'text'        => esc_html__( 'Load custom keywords and urls from a CSV file.', 'ifocus-link-nest' ),
							),
							iFocus_Link_Nest_Settings::PREVENT_DUPLICATES => array(
								'title' => esc_html__( 'Grouped keywords', 'ifocus-link-nest' ),
								'type'  => 'checkbox',
								'text'  => esc_html__( 'Prevent duplicates in text. Will link only first of the keywords found in text.', 'ifocus-link-nest' ),
							),
						),

					),
					'targeting'       => array(
						'title'    => esc_html__( 'Internal links / Targeting', 'ifocus-link-nest' ),
						'callback' => array( $this, 'targeting_intro' ),
						'fields'   => array(
							iFocus_Link_Nest_Settings::PROCESS_POSTS      => array(
								'title' => esc_html__( 'Posts', 'ifocus-link-nest' ),
								'type'  => 'checkbox',
								'text'  => esc_html__( 'Enable. Search and process posts', 'ifocus-link-nest' ),
							),
							iFocus_Link_Nest_Settings::PROCESS_PAGES      => array(
								'title' => esc_html__( 'Pages', 'ifocus-link-nest' ),
								'type'  => 'checkbox',
								'text'  => esc_html__( 'Enable. Search and process pages', 'ifocus-link-nest' ),
							),
							iFocus_Link_Nest_Settings::MAX_LINKS          => array(
								'title' => esc_html__( 'Max links', 'ifocus-link-nest' ),
								'type'  => 'number',
								'text'  => esc_html__( 'You can limit the maximum number of different links that will be generated per post or page. Set to 0 for no limit.', 'ifocus-link-nest' ),
							),
							iFocus_Link_Nest_Settings::MAX_KEYWORDS_LINKS => array(
								'title' => esc_html__( 'Max keywords links', 'ifocus-link-nest' ),
								'type'  => 'number',
								'text'  => esc_html__( 'You can limit the maximum number of links created with the same keyword. Set to 0 for no limit.', 'ifocus-link-nest' ),
							),
							iFocus_Link_Nest_Settings::MAX_SAME_URL       => array(
								'title' => esc_html__( 'Max same URLs', 'ifocus-link-nest' ),
								'type'  => 'number',
								'text'  => esc_html__( 'Limit number of same URLs the plugin will link to. Works only when Max Keyword Links above is set to 1. Set to 0 for no limit.', 'ifocus-link-nest' ),
							),
							iFocus_Link_Nest_Settings::CASE_SENSITIVE     => array(
								'title' => esc_html__( 'Case sensitive', 'ifocus-link-nest' ),
								'type'  => 'checkbox',
								'text'  => esc_html__( 'Enable', 'ifocus-link-nest' ),
							),
							iFocus_Link_Nest_Settings::OPEN_IN_NEW_WINDOW => array(
								'title' => esc_html__( 'Open in new window', 'ifocus-link-nest' ),
								'type'  => 'checkbox',
								'text'  => esc_html__( 'Enable. Open the external links in a new window. ', 'ifocus-link-nest' ),
							),
						),

					),
					'excluding'       => array(
						'title'    => esc_html__( 'Excluding', 'ifocus-link-nest' ),
						'callback' => array( $this, 'excluding_intro' ),
						'fields'   => array(
							iFocus_Link_Nest_Settings::EXCLUDE_HEADINGS => array(
								'title' => esc_html__( 'Headings', 'ifocus-link-nest' ),
								'type'  => 'checkbox',
								'text'  => esc_html__( 'Enable. Prevent linking in heading tags (h1, h2, h3, h4, h5 and h6)', 'ifocus-link-nest' ),
							),
							iFocus_Link_Nest_Settings::IGNORED_POSTS    => array(
								'title'    => esc_html__( 'Ignore post/pages', 'ifocus-link-nest' ),
								'text'     => esc_html__( 'Exclude certain posts or pages. Separate them by comma (ID, slug or name).', 'ifocus-link-nest' ),
								'custom'   => true,
								'type'     => 'select2',
								'sanitize' => false,
								'callback' => array( $this, 'render_post_select_field' ),
							),
							iFocus_Link_Nest_Settings::IGNORED_WORDS    => array(
								'title'    => esc_html__( 'Ignore words', 'ifocus-link-nest' ),
								'text'     => esc_html__( 'Exclude certain words or phrases from automatic linking. Separate them by comma.', 'ifocus-link-nest' ),
								'custom'   => true,
								'type'     => 'select2',
								'sanitize' => false,
								'callback' => array( $this, 'render_word_select_field' ),
							),
						),

					),

				),
			),
		);

		new \RationalOptionPages( $pages );
	}

	public function custom_keyword_intro( $section ) {
		echo '<p>';
		esc_html_e( 'Here you can manually enter the extra keywords you want to automatically link. Use comma to separate keywords and add target url at the end. Use a new line for new url and set of keywords. You can link to any url, not only your site.' );
		echo '</p>';
		echo '<div id="keywords-editor">';
		echo '</div>';
		echo '<a class="button button-primary" id="add-row">' . __( 'Add new line', 'ifocus-link-nest' ) . '</a>';

		$keywords = iFocus_Link_Nest_Keyword_Model::get_all();

		echo '<script type="application/javascript">';
		// @formatter:off
		?>
		var nonces = {
			update: '<?php echo wp_create_nonce( iFocus_Link_Nest_Admin::AJAX_ACTION_UPDATE ); ?>',
			delete: '<?php echo wp_create_nonce( iFocus_Link_Nest_Admin::AJAX_ACTION_DELETE ); ?>'
		};

		var tableData = [
		<?php foreach ( $keywords as $keyword ) : ?>
			{
				id: <?php echo $keyword->id; ?>,
				keyword: "<?php echo $keyword->keyword; ?>",
				title: "<?php echo $keyword->title; ?>",
				href: "<?php echo $keyword->href; ?>",
				rel: "<?php echo $keyword->rel; ?>",
			},
		<?php endforeach; ?>
		];

		var triggerModelChange = function( data, action, callback ) {
			data.action = 'ifocus-link-nest-' + action;
			data.nonce = nonces[action];

			jQuery.post( ajaxurl, data, function( response ) {
				if ( callback ) {
					callback( response );
				}
			});
		}

		var handleCellChange = function( cell ) {
			var data = cell.getData();
			if ( data.id ) {
				triggerModelChange( data, 'update' );
			} else {
				triggerModelChange( data, 'update', function( responseData ){
					cell.getRow().update({id: responseData.data.id});
				} );
			}
		}

		var handleRowDeletion = function( row ) {
			triggerModelChange( row.getData(), 'delete' );
		}

		var buildFieldDefinition = function( title, id ) {
			return {
				title: title,
				field: id,
				headerSort: false,
				editor: "input",
				cellEdited: handleCellChange
			};
		}

		jQuery(window).load(function () {
			var table = new Tabulator("#keywords-editor", {
				layout: "fitColumns",
				data: tableData,
				index: "id",
				columns:[
					{ title:"id", field:"id", visible:false, download:true, headerSort: false },
					buildFieldDefinition( "Keyword", "keyword" ),
					buildFieldDefinition( "Attribute title", "title" ),
					buildFieldDefinition( "Attribute rel", "rel" ),
					buildFieldDefinition( "Link (href)", "href", ),
					{ formatter:"buttonCross", headerSort: false, width:40, align:"center", cellClick:function(e, cell){
						cell.getRow().delete();
					}},
				],
			});

			table.on( "rowDeleted", handleRowDeletion );

			//Add row on "Add Row" button click
			document.getElementById("add-row").addEventListener("click", function(){
				table.addRow({});
			});

			//Delete row on "Delete Row" button click
			jQuery.on( 'click', '.delete-row' ).addEventListener("click", function(){
				table.deleteRow( jQuery(this).data('id') );
			});
		});
		<?php
		// @formatter:on
		echo '</script>';
	}

	public function targeting_intro( $section ) {
		echo '<p>';
		esc_html_e( 'iFOCUS.sk Link nest plugin can search and process your posts, pages for keywords to automatically interlink from <div>, <p>, <ul>, <li> and other html tags.', 'ifocus-link-nest' );
		echo '</p>';
	}

	public function excluding_intro( $section ) {
		echo '<p>';
		esc_html_e( 'Setup what and how it can be excluded from internal linking.', 'ifocus-link-nest' );
		echo '</p>';
	}

	/**
	 * Renders custom post selection field(s).
	 *
	 * @param array                $field Field data.
	 * @param string               $page_key Settings page key.
	 * @param string               $section_key Settings section key.
	 * @param string               $field_key Field key.
	 * @param \RationalOptionPages $option_pages Rational option pages object.
	 */
	public function render_post_select_field( $field, $page_key, $section_key, $field_key, $option_pages ) {
		if ( ! class_exists( '\S24WP' ) ) {
			return;
		}

		echo '<fieldset><legend class="screen-reader-text">' . $field['title'] . '</legend>';

		$options = $option_pages->get_options();
		\S24WP::insert(
			array(
				'placeholder' => esc_html__( 'select post(s) and/or page(s)', 'ifocus-link-nest' ),
				'name'        => $page_key . '[' . $field['id'] . '][]',
				'width'       => 500,
				'data-type'   => 'post',
				'multiple'    => true,
				'selected'    => isset( $options[ $field['id'] ] ) ? $options[ $field['id'] ] : array(),
			)
		);

		echo '</fieldset>';
	}

	/**
	 * Renders custom post selection field(s).
	 *
	 * @param array                $field Field data.
	 * @param string               $page_key Settings page key.
	 * @param string               $section_key Settings section key.
	 * @param string               $field_key Field key.
	 * @param \RationalOptionPages $option_pages Rational option pages object.
	 */
	public function render_word_select_field( $field, $page_key, $section_key, $field_key, $option_pages ) {
		if ( ! class_exists( '\S24WP' ) ) {
			return;
		}

		echo '<fieldset><legend class="screen-reader-text">' . $field['title'] . '</legend>';

		$options = $option_pages->get_options();
		\S24WP::insert(
			array(
				'placeholder' => esc_html__( 'type word(s)', 'ifocus-link-nest' ),
				'name'        => $page_key . '[' . $field['id'] . '][]',
				'width'       => 500,
				'tags'        => true,
				'multiple'    => true,
				'selected'    => isset( $options[ $field['id'] ] ) ? $options[ $field['id'] ] : array(),
			)
		);

		echo '</fieldset>';
	}

	public static function is_settings_screen() {
		return is_admin() && array_key_exists( 'page', $_GET ) && 'ifocus-link-nest' === $_GET['page'];
	}
}
