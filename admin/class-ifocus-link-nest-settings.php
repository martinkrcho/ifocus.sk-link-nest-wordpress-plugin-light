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

	public const MAX_SAME_URL = 'max_same_url';

	public const PROCESS_PAGES = 'process_pages';

	public const MAX_LINKS = 'max_links';

	public const MAX_KEYWORDS_LINKS = 'max_keywords_links';

	public const IGNORED_WORDS = 'ignored_words';

	public const PREVENT_DUPLICATES = 'prevent_duplicates';

	public const EXCLUDE_HEADINGS = 'exclude_headings';

	private $prevent_duplicates = 'yes';

	private $process_posts = 'yes';

	private $process_pages = 'yes';

	private $max_links = 3;

	private $max_keywords_links = 1;

	private $max_same_url = 1;

	private $case_sensitive = 'yes';

	private $open_in_new_window = 'yes';

	private $exclude_headings = 'yes';

	private $ignored_posts = [];

	private $ignored_words = [];

	public function __construct() {
	}

	public function to_array() {
		return [
			self::PREVENT_DUPLICATES => $this->prevent_duplicates,
			self::PROCESS_POSTS      => $this->process_posts,
			self::PROCESS_PAGES      => $this->process_pages,
			self::MAX_LINKS          => $this->max_links,
			self::MAX_KEYWORDS_LINKS => $this->max_keywords_links,
			self::MAX_SAME_URL       => $this->max_same_url,
			self::CASE_SENSITIVE     => $this->case_sensitive,
			self::OPEN_IN_NEW_WINDOW => $this->open_in_new_window,
			self::EXCLUDE_HEADINGS   => $this->exclude_headings,
			self::IGNORED_POSTS      => $this->ignored_posts,
			self::IGNORED_WORDS      => $this->ignored_words,
		];
	}

	public function canProcessPages() {
		return 'yes' === $this->process_pages;
	}

	public function canProcessPosts() {
		return 'yes' === $this->process_posts;
	}
}


