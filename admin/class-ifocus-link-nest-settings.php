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
class iFocus_Link_Nest_Settings {

	public const IGNORED_POSTS = 'ignored_posts';

	public const OPEN_IN_NEW_WINDOW = 'open_in_new_window';

	public const CASE_SENSITIVE = 'case_sensitive';

	public const CSV_FILE = 'csv_file';

	public const PROCESS_POSTS = 'process_posts';

	public const PROCESS_PAGES = 'process_pages';

	public const MAX_LINKS = 'max_links';

	public const IGNORED_WORDS = 'ignored_words';

	public const PROCESS_HEADINGS = 'process_headings';

	private $process_posts = 'on';

	private $process_pages = 'on';

	private $max_links = 3;

	private $case_sensitive = 'off';

	private $open_in_new_window = 'off';

	private $process_headings = 'off';

	private $ignored_posts = array();

	private $ignored_words = array();

	public function __construct( $seed = array() ) {
		foreach ( $seed as $key => $value ) {
			$prop = strtolower( $key );
			if ( property_exists( $this, $prop ) ) {
				$this->$prop = $value;
			}
		}
	}

	public function to_array() {
		return array(
			self::PROCESS_POSTS      => $this->process_posts,
			self::PROCESS_PAGES      => $this->process_pages,
			self::MAX_LINKS          => $this->max_links,
			self::CASE_SENSITIVE     => $this->case_sensitive,
			self::OPEN_IN_NEW_WINDOW => $this->open_in_new_window,
			self::PROCESS_HEADINGS   => $this->process_headings,
			self::IGNORED_POSTS      => $this->ignored_posts,
			self::IGNORED_WORDS      => $this->ignored_words,
		);
	}

	/**
	 * @return bool
	 */
	public function can_process_pages() {
		return 'on' === $this->process_pages;
	}

	/**
	 * @return bool
	 */
	public function can_process_posts() {
		return 'on' === $this->process_posts;
	}

	/**
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public function is_post_excluded( $post_id ) {
		return in_array( $post_id, $this->ignored_posts );
	}

	/**
	 * @param iFocus_Link_Nest_Keyword_Model $keyword
	 *
	 * @return bool
	 */
	public function is_keyword_excluded( $keyword ) {
		return in_array( $keyword->keyword, $this->ignored_words );
	}

	/**
	 * @return bool
	 */
	public function should_open_in_new_window() {
		return 'on' === $this->open_in_new_window;
	}

	/**
	 * @return bool
	 */
	public function is_case_sensitive() {
		return 'on' === $this->case_sensitive;
	}

	/**
	 * @return bool
	 */
	public function should_exclude_headings() {
		return 'off' === $this->process_headings;
	}

	/**
	 * @return int
	 */
	public function get_max_links_count() {
		return intval( $this->max_links );
	}
}


