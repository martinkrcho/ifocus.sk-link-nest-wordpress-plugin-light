<?php
/**
 * Class TextReplacementTest
 *
 * @package iFocus_Link_Nest
 */

class TextReplacementTest extends \PHPUnit\Framework\TestCase {

	protected function getKeywords() {
		return array(
			iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
				array(
					'non',
					'online marketing',
					'help',
					'https://linking.objav.digital/services/',
				)
			),
			iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
				array(
					'et',
					'seo audit',
					'help',
					'https://linking.objav.digital/services/seo-audit/',
				)
			),
			iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
				array(
					'amet',
					'about linking',
					'help',
					'https://linking.objav.digital/credits/',
				)
			),
			iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
				array(
					'sed',
					'online marketing agency',
					'help',
					'https://linking.objav.digital/',
				)
			),
			iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
				array(
					'maximus',
					'ifocus agency',
					'help',
					'https://linking.objav.digital/about/',
				)
			),
			iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
				array(
					'nulla',
					'ifocus kontakt',
					'help',
					'https://linking.objav.digital/about/',
				)
			),
			iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
				array(
					'ipsum',
					'social media service',
					'help',
					'https://linking.objav.digital/',
				)
			),
			iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
				array(
					'dictum',
					'ppc campaigns',
					'help',
					'https://linking.objav.digital/services/ppc-audit/',
				)
			),
			iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
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
	public function test_replacements_with_default_settings( $keywords, $originalText, $expectedResult ) {
		$settings  = new iFocus_Link_Nest_Settings();
		$processor = new iFocus_Link_Nest_Text_Processor( $settings, $keywords );
		$this->assertEquals( $expectedResult, $processor->process( $originalText ) );
	}

	public function dataReplacements() {
		return array(
			'basic scenario with default settings' => array(
				// keywords
				array(
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
						array(
							'non',
							'online marketing',
							'help',
							'https://linking.objav.digital/services/',
						)
					),
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
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
				. '<a href="https://linking.objav.digital/services/" title="online marketing" class="ifocus-link-nest" rel="help" target="_blank">non</a>, '
				. 'hendrerit <a href="https://linking.objav.digital/about/" title="ifocus agency" class="ifocus-link-nest" rel="help" target="_blank">maximus</a> '
				. 'tellus.',
			),

			'skipping HTML attributes with default settings' => array(
				// keywords
				array(
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
						array(
							'non',
							'online marketing',
							'help',
							'https://linking.objav.digital/services/',
						)
					),
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
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
				. '<a href="https://linking.objav.digital/services/" title="online marketing" class="ifocus-link-nest" rel="help" target="_blank">non</a>, '
				. 'hendrerit maximus tellus.',
			),

			'skipping existing hyperlinks with default settings' => array(
				// keywords
				array(
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
						array(
							'non',
							'online marketing',
							'help',
							'https://linking.objav.digital/services/',
						)
					),
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
						array(
							'online marketing',
							'ifocus agency',
							'help',
							'https://linking.objav.digital/about/',
						)
					),
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
						array(
							'services',
							'exclude dynamic hyperlinks',
							'help',
							'https://linking.objav.digital/services/ppc-audit/',
						)
					),
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
						array(
							'ultrices',
							'exclude static hyperlinks',
							'help',
							'https://regex101.com/',
						)
					),
				),

				// original text
				'Curabitur tempus quam nec purus luctus, a <a href="https://www.strava.com/" class="primary">pulvinar ex ultrices</a>. Aenean varius nisl id tempor feugiat. '
				. 'Vestibulum sem neque, vehicula in dolor non, hendrerit maximus tellus.',

				// expected result
				'Curabitur tempus quam nec purus luctus, a <a href="https://www.strava.com/" class="primary">pulvinar ex ultrices</a>. Aenean varius nisl id tempor feugiat. '
				. 'Vestibulum sem neque, vehicula in dolor '
				. '<a href="https://linking.objav.digital/services/" title="online marketing" class="ifocus-link-nest" rel="help" target="_blank">non</a>, '
				. 'hendrerit maximus tellus.',
			),
		);
	}
}
