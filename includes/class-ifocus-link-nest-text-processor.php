<?php

/**
 * The file that defines the text processor.
 *
 * @link       https://www.linkedin.com/in/martinkrcho/
 * @since      1.0.0
 *
 * @package    iFocus_Link_Nest
 * @subpackage iFocus_Link_Nest/includes
 */

/**
 * Class handles the processing text and replacing keywords with hyperlinks.
 *
 * The replacements are based on given set of settings and a list of keywords.
 *
 * @since      1.0.0
 * @package    iFocus_Link_Nest
 * @subpackage iFocus_Link_Nest/includes
 * @author     Martin Krcho <martin.krcho@devstudio.sk>
 */
class iFocus_Link_Nest_Text_Processor {

	const CSS_CLASS = 'ifocus-link-nest';

	/**
	 * @var string Lookaround expression cache for the regular expression.
	 */
	private $lookaround_expression;

	/**
	 * @var string Text being processed.
	 */
	private $text;

	/**
	 * @var iFocus_Link_Nest_Settings Plugin settings.
	 */
	private $settings;

	/**
	 * @var iFocus_Link_Nest_Keyword_Model[] List of keywords.
	 */
	private $keywords;

	/**
	 * @var array List of found keywords along with their positions.
	 */
	private $positions = array();

	/**
	 * @var int Offset to be added to the original keyword positions as the replacements are performed.
	 */
	private $offset = 0;

	/**
	 * @var int Total number of replacements done so far.
	 */
	private $replacements_performed = 0;

	/**
	 * Feeds the class with settings and keywords.
	 *
	 * @since    1.0.0
	 *
	 * @param iFocus_Link_Nest_Settings        $settings Plugin settings.
	 * @param iFocus_Link_Nest_Keyword_Model[] $keywords List of keywords.
	 */
	public function __construct( $settings, $keywords ) {
		$this->settings = $settings;
		$this->keywords = $keywords;
	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public function process( $text ) {
		$this->text = $text;

		// Find positions of all keywords.
		foreach ( $this->keywords as $keyword ) {
			if ( $this->settings->is_keyword_excluded( $keyword ) ) {
				continue;
			}

			$pattern = $this->get_regex_pattern( $keyword->keyword );
			preg_match_all( $pattern, $this->text, $matches, PREG_OFFSET_CAPTURE );
			if ( ! empty( $matches ) ) {
				foreach ( $matches as $match_group ) {
					foreach ( $match_group as $match ) {
						if ( 2 === count( $match ) && $keyword->keyword === $match[0] ) {
							$this->positions[ $match[1] ] = $keyword;
						}
					}
				}
			}
		}

		if ( empty( $this->positions ) ) {
			return $this->text;
		}

		ksort( $this->positions, SORT_NUMERIC );

		// Run the actual processing and replacements.
		$max_links_count = $this->settings->get_max_links_count();
		foreach ( $this->positions as $position => $keyword ) {
			$this->apply_keyword( $keyword, $position + $this->offset );
			if ( $max_links_count > 0 && $this->replacements_performed >= $max_links_count ) {
				break;
			}
		}

		return $this->text;
	}

	/**
	 * @param iFocus_Link_Nest_Keyword_Model $keyword
	 * @param int                            $start_position
	 */
	private function apply_keyword( $keyword, $start_position ) {
		$hyperlink_markup = sprintf(
			'<a href="%1$s" title="%2$s" class="%3$s" rel="%4$s"%5$s>$1</a>',
			esc_attr( $keyword->href ),
			esc_attr( $keyword->title ),
			self::CSS_CLASS,
			esc_attr( $keyword->rel ),
			$this->settings->should_open_in_new_window() ? ' target="_blank"' : ''
		);

		$pattern = $this->get_regex_pattern( $keyword->keyword );

		$first_part  = substr( $this->text, 0, $start_position );
		$second_part = substr( $this->text, $start_position );

		// Use $limit and $count args to respect plugin settings
		$replacements_done = 0;
		$second_part       = preg_replace( $pattern, $hyperlink_markup, $second_part, 1, $replacements_done );

		$this->replacements_performed += $replacements_done;
		if ( $replacements_done > 0 ) {
			$this->offset += strlen( $hyperlink_markup ) - strlen( $keyword->keyword );
			$this->text    = $first_part . $second_part;
		}
	}

	/**
	 * @param string $keyword
	 *
	 * @return string
	 */
	public function get_regex_pattern( $keyword ) {
		$lookaround_expression = $this->get_lookaround_expression();

		$result = '/\b(' . $keyword . ')\b' . $lookaround_expression . '/m';
		if ( ! $this->settings->is_case_sensitive() ) {
			$result .= 'i';
		}

		return $result;
	}

	/**
	 * @return string
	 */
	public function get_lookaround_expression() {
		if ( is_null( $this->lookaround_expression ) ) {
			$tags_to_exclude = array( 'a' );
			if ( $this->settings->should_exclude_headings() ) {
				for ( $i = 1; $i <= 6; $i ++ ) {
					$tags_to_exclude[] = 'h' . $i;
				}
			}

			$lookaround_exclude_tags       = '(?![^<]*<\/(' . implode( '|', $tags_to_exclude ) . ')>)';
			$lookaround_exclude_attributes = '(?=[^>]*(<|$))';

			$this->lookaround_expression = $lookaround_exclude_tags . $lookaround_exclude_attributes;
		}

		return $this->lookaround_expression;
	}

	/**
	 * @return bool
	 */
	public function has_text_changed() {
		return $this->replacements_performed > 0;
	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public static function strip_links( $text ) {

		$pattern = '/<a ([^>]+)?class="' . preg_quote( self::CSS_CLASS ) . '"([^>]+)?>([^>]+)<\/a>/i';

		return preg_replace( $pattern, '$3', $text );
	}
}
