<?php

/**
 * The file that defines the text processor.
 *
 * @link       https://www.linkedin.com/in/martinkrcho/
 * @since      1.0.0
 *
 * @package    Wp_Internal_Linking
 * @subpackage Wp_Internal_Linking/includes
 */

/**
 * Class handles the processing text and replacing keywords with hyperlinks.
 *
 * The replacements are based on given set of settings and a list of keywords.
 *
 * @since      1.0.0
 * @package    Wp_Internal_Linking
 * @subpackage Wp_Internal_Linking/includes
 * @author     Martin Krcho <martin.krcho@devstudio.sk>
 */
class Wp_Internal_Linking_Text_Processor {

	/**
	 * @var Wp_Internal_Linking_Settings Plugin settings.
	 */
	private $settings;

	/**
	 * @var \Wp_Internal_Linking_Keyword_Model[] List of keywords.
	 */
	private $keywords;

	/**
	 * Feeds the class with settings and keywords.
	 *
	 * @since    1.0.0
	 *
	 * @param Wp_Internal_Linking_Settings        $settings Plugin settings.
	 * @param Wp_Internal_Linking_Keyword_Model[] $keywords List of keywords.
	 */
	public function __construct( $settings, $keywords ) {
		$this->settings = $settings;
		$this->keywords = $keywords;
	}

	public function process( $text ) {
		// TODO run the actual processing and replacements
		return $text;
	}
}
