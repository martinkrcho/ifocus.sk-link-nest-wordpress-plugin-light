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

			'multiword keyword expression' => array(
				// keywords
				array(
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
						array(
							'efficitur lacus at libero',
							'online marketing',
							'help',
							'https://linking.objav.digital/services/',
						)
					),
				),

				// original text
				'Etiam eu posuere ex, quis pretium est. Nulla venenatis, ligula in sollicitudin molestie, nibh arcu vehicula velit, sed facilisis urna justo vel turpis. '
				.'Etiam tempus elit eu pharetra pretium. Sed in ullamcorper nibh, vitae imperdiet turpis. Suspendisse vestibulum interdum purus, a interdum justo elementum ut. '
				.'Integer hendrerit fringilla bibendum. Maecenas efficitur lacus at libero vestibulum tempor. Nunc tincidunt elementum turpis, vel venenatis nulla hendrerit ut.'
				.' Mauris porta, ante in tempus dictum, lacus quam convallis nisi, sit amet faucibus metus felis tempor ipsum. Praesent ac blandit felis, eget faucibus ex. '
				.'Praesent aliquet elit et vulputate ullamcorper. Nam egestas sodales urna, in tincidunt ante elementum vitae. Fusce in arcu et dolor gravida rutrum vitae vel mi.'
				.' Proin eget felis consectetur, ullamcorper lacus id, convallis nibh.',

				// expected result
				'Etiam eu posuere ex, quis pretium est. Nulla venenatis, ligula in sollicitudin molestie, nibh arcu vehicula velit, sed facilisis urna justo vel turpis. '
				.'Etiam tempus elit eu pharetra pretium. Sed in ullamcorper nibh, vitae imperdiet turpis. Suspendisse vestibulum interdum purus, a interdum justo elementum ut. '
				.'Integer hendrerit fringilla bibendum. Maecenas '
				. '<a href="https://linking.objav.digital/services/" title="online marketing" class="ifocus-link-nest" rel="help" target="_blank">efficitur lacus at libero</a>'
				.' vestibulum tempor. Nunc tincidunt elementum turpis, vel venenatis nulla hendrerit ut.'
				.' Mauris porta, ante in tempus dictum, lacus quam convallis nisi, sit amet faucibus metus felis tempor ipsum. Praesent ac blandit felis, eget faucibus ex. '
				.'Praesent aliquet elit et vulputate ullamcorper. Nam egestas sodales urna, in tincidunt ante elementum vitae. Fusce in arcu et dolor gravida rutrum vitae vel mi.'
				.' Proin eget felis consectetur, ullamcorper lacus id, convallis nibh.',

			),

		);
	}
}
