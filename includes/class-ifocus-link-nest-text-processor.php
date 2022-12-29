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

	public function process( $text ) {
		$this->text = $text;

		// Run the actual processing and replacements.
		foreach ( $this->keywords as $keyword ) {
			$this->apply_keyword( $keyword );
		}

		return $this->text;
	}

	/**
	 * @param iFocus_Link_Nest_Keyword_Model $keyword
	 */
	private function apply_keyword( $keyword ) {
		$hyperlink_markup = sprintf(
			'<a href="%1$s" title="%2$s" rel="%3$s">%4$s</a>',
			esc_attr( $keyword->href ),
			esc_attr( $keyword->title ),
			esc_attr( $keyword->rel ),
			esc_html( $keyword->keyword )
		);

		$allow_titles = '';
		$lookaround   = '(?=[^>]*(<|$))';
		$pattern      = '/\b' . $keyword->keyword . '\b' . $lookaround . '/';
		$this->text   = preg_replace( $pattern, $hyperlink_markup, $this->text );

		return $this->text;
	}
}
