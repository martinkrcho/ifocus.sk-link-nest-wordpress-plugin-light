<?php
/**
 * Class TextReplacementTest
 *
 * @package Wp_Internal_Linking
 */

class TextReplacementTest extends \PHPUnit\Framework\TestCase {

	protected function getKeywords() {
		return array(
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
			Wp_Internal_Linking_Keyword_Model::build_from_generic_array(
				array(
					'services',
					'exclude hyperlinks',
					'help',
					'https://linking.objav.digital/services/ppc-audit/',
				)
			),
		);

	}

	/**
	 * @dataProvider dataReplacements
	 */
	public function test_replacements_with_default_settings($keywords, $originalText, $expectedResult) {
		$settings = new Wp_Internal_Linking_Settings();
		$processor = new Wp_Internal_Linking_Text_Processor( $settings, $keywords );
		$this->assertEquals( $expectedResult, $processor->process( $originalText ) );
	}

	public function dataReplacements() {
		return array(
			'basic scenario with default settings' => [
				// keywords
				array(
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
							'maximus',
							'ifocus agency',
							'help',
							'https://linking.objav.digital/about/',
						)
					),
				),

				// original text
				'Curabitur tempus quam nec purus luctus, a pulvinar ex ultrices. Aenean varius nisl id tempor feugiat. '
				. 'Vestibulum sem neque, vehicula in dolor non, hendrerit maximus tellus.',

				// expected result
				'Curabitur tempus quam nec purus luctus, a pulvinar ex ultrices. Aenean varius nisl id tempor feugiat. '
				. 'Vestibulum sem neque, vehicula in dolor '
				. '<a href="https://linking.objav.digital/services/" title="online marketing" rel="help">non</a>, '
				. 'hendrerit <a href="https://linking.objav.digital/about/" title="ifocus agency" rel="help">maximus</a> '
				. 'tellus.'
			],

			'skipping HTML attributes with default settings' => [
				// keywords
				array(
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
							'online marketing',
							'ifocus agency',
							'help',
							'https://linking.objav.digital/about/',
						)
					),
				),

				// original text
				'Curabitur tempus quam nec purus luctus, a pulvinar ex ultrices. Aenean varius nisl id tempor feugiat. '
				. 'Vestibulum sem neque, vehicula in dolor non, hendrerit maximus tellus.',

				// expected result
				'Curabitur tempus quam nec purus luctus, a pulvinar ex ultrices. Aenean varius nisl id tempor feugiat. '
				. 'Vestibulum sem neque, vehicula in dolor '
				. '<a href="https://linking.objav.digital/services/" title="online marketing" rel="help">non</a>, '
				. 'hendrerit maximus tellus.'
			]
		);
	}
}
