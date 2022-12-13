<?php

/**
 * The file that defines the plugin settings class.
 *
 * @link       https://www.linkedin.com/in/martinkrcho/
 * @since      1.0.0
 *
 * @package    Wp_Internal_Linking
 * @subpackage Wp_Internal_Linking/admin
 */

/**
 * The plugin settings class.
 *
 * @since      1.0.0
 * @package    Wp_Internal_Linking
 * @subpackage Wp_Internal_Linking/includes
 * @author     Martin Krcho <martin.krcho@devstudio.sk>
 */
class Wp_Internal_Linking_Settings {

	const CSV_FILE = 'csv_file';

	const PREVENT_DUPLICATES = 'prevent_duplicates';

	const PROCESS_POSTS = 'process_posts';

	const PROCESS_PAGES = 'process_pages';

	const MAX_LINKS = 'max_links';

	const MAX_KEYWORDS_LINKS = 'max_keywords_links';

	const MAX_SAME_URL = 'max_same_url';

	const CASE_SENSITIVE = 'case_sensitive';

	const OPEN_IN_NEW_WINDOW = 'open_in_new_windows';

	const EXCLUDE_HEADINGS = 'exclude_headings';

	const IGNORED_POSTS = 'ignored_posts';

	const IGNORED_WORDS = 'ignored_words';

	/**
	 * Name of the option storing the plugin settings.
	 *
	 * @var string
	 */
	private static $option_name = 'wp_internal_linking_settings';

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

		add_filter( 'pre_update_option_' . self::$option_name, [ $this, 'handle_file_upload' ], 10, 3 );
		add_action( 'update_option_' . self::$option_name, [ $this, 'on_settings_updated' ], 10, 3 );
	}

	/**
	 * Handles file upload and data import before the settings are updated.
	 *
	 * @param mixed  $value The new, unserialized option value.
	 * @param string $option Name of the option.
	 * @param mixed  $old_value The old option value.
	 */
	public function handle_file_upload( $value, $option, $old_value ) {

		if ( ! is_array( $value ) || ! array_key_exists( self::CSV_FILE, $value ) ) {
			return $value;
		}

		if ( empty( $_FILES ) || ! array_key_exists( self::$option_name, $_FILES ) ) {
			return $value;
		}

		if ( ! array_key_exists( self::CSV_FILE, $_FILES[ self::$option_name ]['error'] ) || $_FILES[ self::$option_name ]['error'][ self::CSV_FILE ] > 0 ) {
			return $value;
		}

		// Parse and import CSV file.
		Wp_Internal_Linking_Csv_Import::import( $_FILES[ self::$option_name ]['tmp_name'][ self::CSV_FILE ] );

		$value[ self::CSV_FILE ] = time();

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
		update_option( $option . '_last_updated', time() );
	}

	/**
	 * Retrieve plugin settings.
	 *
	 * @return array
	 */
	public static function get_settings() {
		return wp_parse_args(
			get_option( self::$option_name, [] ),
			[
				self::CSV_FILE           => '',
				self::PREVENT_DUPLICATES => 'yes',
				self::PROCESS_POSTS      => 'yes',
				self::PROCESS_PAGES      => 'yes',
				self::MAX_LINKS          => 3,
				self::MAX_KEYWORDS_LINKS => 1,
				self::MAX_SAME_URL       => 1,
				self::CASE_SENSITIVE     => 'yes',
				self::OPEN_IN_NEW_WINDOW => 'yes',
				self::EXCLUDE_HEADINGS   => 'yes',
				self::IGNORED_POSTS      => [],
				self::IGNORED_WORDS      => [],
			]
		);
	}

	private function init_settings() {
		$intro_html  = '<p>';
		$intro_html .= esc_html__( 'With iFOCUS.sk Link Nest plugin you can easily and automatically link from keywords and phrases in posts and pages to corresponding posts and pages or any other URL. Set the following settings to your own needs and let iFOCUS.sk Link Nest plugin do the work for you.', 'admin-notices-manager' );
		$intro_html .= '</p>';
		$intro_html .= '<p>';
		$intro_html .= esc_html__( 'If you find any bugs or you have ideas for the plugin, let us know at:', 'admin-notices-manager' );
		$intro_html .= '<br />';
		$intro_html .= '<a href="http://wordpress.org/support/plugin/ifocus-link-nest" target="_blank">http://wordpress.org/support/plugin/ifocus-link-nest</a>';
		$intro_html .= '</p>';

		$pages = [
			self::$option_name => [
				'menu_title'  => esc_html__( 'Internal linking', 'admin-notices-manager' ),
				'menu_slug'   => 'wp-internal-linking',
				'parent_slug' => 'options-general.php',
				'page_title'  => esc_html__( 'iFOCUS.sk Link Nest plugin - Internal linking', 'admin-notices-manager' ),
				'text'        => $intro_html,
				'sections'    => [
					'custom-keywords' => [
						'title'    => esc_html__( 'Custom Keywords', 'admin-notices-manager' ),
						'callback' => [ $this, 'custom_keyword_intro' ],
						'fields'   => [
							self::CSV_FILE           => [
								'id'          => self::CSV_FILE,
								'title'       => esc_html__( 'Import from CSV', 'sample-domain' ),
								'type'        => 'file',
								'placeholder' => esc_html__( 'select CSV file', 'sample-domain' ),
								'text'        => esc_html__( 'Load custom keywords and urls from a CSV file.', 'sample-domain' ),
							],
							self::PREVENT_DUPLICATES => [
								'title' => esc_html__( 'Grouped keywords', 'sample-domain' ),
								'type'  => 'checkbox',
								'text'  => esc_html__( 'Prevent duplicates in text. Will link only first of the keywords found in text.', 'sample-domain' ),
							],
						],

					],
					'targeting'       => [
						'title'    => esc_html__( 'Internal links / Targeting', 'admin-notices-manager' ),
						'callback' => [ $this, 'targeting_intro' ],
						'fields'   => [
							self::PROCESS_POSTS      => [
								'title' => esc_html__( 'Posts', 'sample-domain' ),
								'type'  => 'checkbox',
								'text'  => esc_html__( 'Enable. Search and process posts', 'sample-domain' ),
							],
							self::PROCESS_PAGES      => [
								'title' => esc_html__( 'Pages', 'sample-domain' ),
								'type'  => 'checkbox',
								'text'  => esc_html__( 'Enable. Search and process pages', 'sample-domain' ),
							],
							self::MAX_LINKS          => [
								'title' => esc_html__( 'Max links', 'sample-domain' ),
								'type'  => 'number',
								'text'  => esc_html__( 'You can limit the maximum number of different links that will be generated per post or page. Set to 0 for no limit.', 'sample-domain' ),
							],
							self::MAX_KEYWORDS_LINKS => [
								'title' => esc_html__( 'Max keywords links', 'sample-domain' ),
								'type'  => 'number',
								'text'  => esc_html__( 'You can limit the maximum number of links created with the same keyword. Set to 0 for no limit.', 'sample-domain' ),
							],
							self::MAX_SAME_URL       => [
								'title' => esc_html__( 'Max same URLs', 'sample-domain' ),
								'type'  => 'number',
								'text'  => esc_html__( 'Limit number of same URLs the plugin will link to. Works only when Max Keyword Links above is set to 1. Set to 0 for no limit.', 'sample-domain' ),
							],
							self::CASE_SENSITIVE     => [
								'title' => esc_html__( 'Case sensitive', 'sample-domain' ),
								'type'  => 'checkbox',
								'text'  => esc_html__( 'Enable', 'sample-domain' ),
							],
							self::OPEN_IN_NEW_WINDOW => [
								'title' => esc_html__( 'Open in new window', 'sample-domain' ),
								'type'  => 'checkbox',
								'text'  => esc_html__( 'Enable. Open the external links in a new window. ', 'sample-domain' ),
							],
						],

					],
					'excluding'       => [
						'title'    => esc_html__( 'Excluding', 'admin-notices-manager' ),
						'callback' => [ $this, 'excluding_intro' ],
						'fields'   => [
							self::EXCLUDE_HEADINGS => [
								'title' => esc_html__( 'Headings', 'sample-domain' ),
								'type'  => 'checkbox',
								'text'  => esc_html__( 'Enable. Prevent linking in heading tags (h1, h2, h3, h4, h5 and h6)', 'sample-domain' ),
							],
							self::IGNORED_POSTS    => [
								'title'    => esc_html__( 'Ignore post/pages', 'sample-domain' ),
								'text'     => esc_html__( 'Exclude certain posts or pages. Separate them by comma (ID, slug or name).', 'sample-domain' ),
								'custom'   => true,
								'type'     => 'select2',
								'sanitize' => false,
								'callback' => [ $this, 'render_post_select_field' ],
							],
							self::IGNORED_WORDS    => [
								'title'    => esc_html__( 'Ignore words', 'sample-domain' ),
								'text'     => esc_html__( 'Exclude certain words or phrases from automatic linking. Separate them by comma.', 'sample-domain' ),
								'custom'   => true,
								'type'     => 'select2',
								'sanitize' => false,
								'callback' => [ $this, 'render_word_select_field' ],
							],
						],

					],

				],
			],
		];

		new \RationalOptionPages( $pages );
	}

	public function custom_keyword_intro( $section ) {
		echo '<p>';
		esc_html_e( 'Here you can manually enter the extra keywords you want to automatically link. Use comma to separate keywords and add target url at the end. Use a new line for new url and set of keywords. You can link to any url, not only your site.' );
		echo '</p>';
		echo '<div id="keywords-editor">';
		echo '</div>';
		echo '<a class="button button-primary" id="add-row">' . __( 'Add new line', 'sample-domain' ) . '</a>';

		$keywords = Wp_Internal_Linking_Keyword_Model::get_all();

		echo '<script type="application/javascript">';
		// @formatter:off
		?>
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
			data.action = 'wp-internal-linking-' + action;

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
		esc_html_e( 'iFOCUS.sk Link nest plugin can search and process your posts, pages for keywords to automatically interlink from <div>, <p>, <ul>, <li> and other html tags.', 'wp-internal-linking' );
		echo '</p>';
	}

	public function excluding_intro( $section ) {
		echo '<p>';
		esc_html_e( 'Setup what and how it can be excluded from internal linking.', 'wp-internal-linking' );
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
			[
				'placeholder' => esc_html__( 'select post(s) and/or page(s)', 'admin-notices-manager' ),
				'name'        => $page_key . '[' . $field['id'] . '][]',
				'width'       => 500,
				'data-type'   => 'post',
				'multiple'    => true,
				'selected'    => isset( $options[ $field['id'] ] ) ? $options[ $field['id'] ] : [],
			]
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
			[
				'placeholder' => esc_html__( 'type word(s)', 'admin-notices-manager' ),
				'name'        => $page_key . '[' . $field['id'] . '][]',
				'width'       => 500,
				'tags'        => true,
				'multiple'    => true,
				'selected'    => isset( $options[ $field['id'] ] ) ? $options[ $field['id'] ] : [],
			]
		);

		echo '</fieldset>';
	}

	public static function is_settings_screen() {
		return is_admin() && array_key_exists( 'page', $_GET ) && 'wp-internal-linking' === $_GET['page'];
	}
}
