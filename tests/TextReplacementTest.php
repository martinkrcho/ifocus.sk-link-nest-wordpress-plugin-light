<?php
/**
 * Class TextReplacementTest
 *
 * @package Wp_Internal_Linking
 */

class TextReplacementTest extends \PHPUnit\Framework\TestCase {

	public function test_replacements_with_default_settings() {
		$settings = new Wp_Internal_Linking_Settings();

		$keywords = array(
			Wp_Internal_Linking_Keyword_Model::build_from_generic_array(
				array(
					'non',
					'online marketing',
					'help',
					'https://linking.objav.digital/services/',
				)
			),
			Wp_Internal_Linking_Keyword_Model::build_from_generic_array(
				array(
					'et',
					'seo audit',
					'help',
					'https://linking.objav.digital/services/seo-audit/',
				)
			),
			Wp_Internal_Linking_Keyword_Model::build_from_generic_array(
				array(
					'amet',
					'about linking',
					'help',
					'https://linking.objav.digital/credits/',
				)
			),
			Wp_Internal_Linking_Keyword_Model::build_from_generic_array(
				array(
					'sed',
					'online marketing agency',
					'help',
					'https://linking.objav.digital/',
				)
			),
			Wp_Internal_Linking_Keyword_Model::build_from_generic_array(
				array(
					'maximus',
					'ifocus agency',
					'help',
					'https://linking.objav.digital/about/',
				)
			),
			Wp_Internal_Linking_Keyword_Model::build_from_generic_array(
				array(
					'nulla',
					'ifocus kontakt',
					'help',
					'https://linking.objav.digital/about/',
				)
			),
			Wp_Internal_Linking_Keyword_Model::build_from_generic_array(
				array(
					'ipsum',
					'social media service',
					'help',
					'https://linking.objav.digital/',
				)
			),
			Wp_Internal_Linking_Keyword_Model::build_from_generic_array(
				array(
					'dictum',
					'ppc campaigns',
					'help',
					'https://linking.objav.digital/services/ppc-audit/',
				)
			),
		);

		$processor = new Wp_Internal_Linking_Text_Processor( $settings, $keywords );

		$text = 'Curabitur tempus quam nec purus luctus, a pulvinar ex ultrices. Aenean varius nisl id tempor feugiat. Vestibulum sem neque, vehicula in dolor non, hendrerit maximus tellus. In malesuada rhoncus sapien, ut scelerisque quam aliquet in. Sed nec sem in purus condimentum consequat. Ut pulvinar magna lectus, vel suscipit eros posuere nec. Vestibulum non mi aliquam nisl laoreet mattis sodales vitae neque. Praesent convallis blandit neque, sit amet venenatis risus sagittis ac. Aliquam libero eros, semper eu dui eget, suscipit suscipit nulla. Donec imperdiet nisi eget mauris volutpat efficitur. Pellentesque rutrum varius urna sit amet tempus. Praesent luctus gravida erat a volutpat.';
		$this->assertEquals( $text, $processor->process( $text ) );
	}
}
