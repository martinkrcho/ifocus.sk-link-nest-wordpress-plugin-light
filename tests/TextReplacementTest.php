<?php
/**
 * Class TextReplacementTest
 *
 * @package iFocus_Link_Nest
 */

class TextReplacementTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @dataProvider dataForDefaultSettings
	 */
	public function test_replacements_with_default_settings( $keywords, $originalText, $expectedResult ) {
		$settings  = new iFocus_Link_Nest_Settings();
		$processor = new iFocus_Link_Nest_Text_Processor( $settings, $keywords );
		$this->assertEquals( $expectedResult, $processor->process( $originalText ) );
	}

	/**
	 * @dataProvider dataForMaxLinksVariations
	 */
	public function test_max_links_variations( $max_links, $text, $expected_result ) {

		$keywords = [
			iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
				[
					'maximus',
					'ifocus agency',
					'help',
					'https://linking.objav.digital/about/',
				]
			),
			iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
				[
					'vene',
					'online marketing',
					'help',
					'https://linking.objav.digital/services/',
				]
			),
			iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
				[
					'malesuada',
					'latin blurb',
					'noopener',
					'https://linking.objav.digital/hiring/',
				]
			),

		];

		$settings = new iFocus_Link_Nest_Settings( [
			iFocus_Link_Nest_Settings::MAX_LINKS => $max_links,
		] );

		$processor = new iFocus_Link_Nest_Text_Processor( $settings, $keywords );
		$this->assertEquals( $expected_result, $processor->process( $text ) );
	}

	public function dataForDefaultSettings() {
		return [
			'basic scenario with default settings' => [
				// keywords
				[
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
						[
							'non',
							'online marketing',
							'help',
							'https://linking.objav.digital/services/',
						]
					),
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
						[
							'maximus',
							'ifocus agency',
							'help',
							'https://linking.objav.digital/about/',
						]
					),
				],

				// original text
				'Curabitur tempus quam nec purus luctus, a pulvinar ex ultrices. Aenean varius nisl id tempor feugiat. '
				. 'Vestibulum sem neque, vehicula in dolor non, hendrerit maximus tellus.',

				// expected result
				'Curabitur tempus quam nec purus luctus, a pulvinar ex ultrices. Aenean varius nisl id tempor feugiat. '
				. 'Vestibulum sem neque, vehicula in dolor '
				. '<a href="https://linking.objav.digital/services/" title="online marketing" class="ifocus-link-nest" rel="help" target="_blank">non</a>, '
				. 'hendrerit <a href="https://linking.objav.digital/about/" title="ifocus agency" class="ifocus-link-nest" rel="help" target="_blank">maximus</a> '
				. 'tellus.',
			],

			'skipping HTML attributes with default settings' => [
				// keywords
				[
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
						[
							'non',
							'online marketing',
							'help',
							'https://linking.objav.digital/services/',
						]
					),
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
						[
							'online marketing',
							'ifocus agency',
							'help',
							'https://linking.objav.digital/about/',
						]
					),
				],

				// original text
				'Curabitur tempus quam nec purus luctus, a pulvinar ex ultrices. Aenean varius nisl id tempor feugiat. '
				. 'Vestibulum sem neque, vehicula in dolor non, hendrerit maximus tellus.',

				// expected result
				'Curabitur tempus quam nec purus luctus, a pulvinar ex ultrices. Aenean varius nisl id tempor feugiat. '
				. 'Vestibulum sem neque, vehicula in dolor '
				. '<a href="https://linking.objav.digital/services/" title="online marketing" class="ifocus-link-nest" rel="help" target="_blank">non</a>, '
				. 'hendrerit maximus tellus.',
			],

			'skipping existing hyperlinks with default settings' => [
				// keywords
				[
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
						[
							'non',
							'online marketing',
							'help',
							'https://linking.objav.digital/services/',
						]
					),
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
						[
							'online marketing',
							'ifocus agency',
							'help',
							'https://linking.objav.digital/about/',
						]
					),
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
						[
							'services',
							'exclude dynamic hyperlinks',
							'help',
							'https://linking.objav.digital/services/ppc-audit/',
						]
					),
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
						[
							'ultrices',
							'exclude static hyperlinks',
							'help',
							'https://regex101.com/',
						]
					),
				],

				// original text
				'Curabitur tempus quam nec purus luctus, a <a href="https://www.strava.com/" class="primary">pulvinar ex ultrices</a>. Aenean varius nisl id tempor feugiat. '
				. 'Vestibulum sem neque, vehicula in dolor non, hendrerit maximus tellus.',

				// expected result
				'Curabitur tempus quam nec purus luctus, a <a href="https://www.strava.com/" class="primary">pulvinar ex ultrices</a>. Aenean varius nisl id tempor feugiat. '
				. 'Vestibulum sem neque, vehicula in dolor '
				. '<a href="https://linking.objav.digital/services/" title="online marketing" class="ifocus-link-nest" rel="help" target="_blank">non</a>, '
				. 'hendrerit maximus tellus.',
			],

			'multiword keyword expression' => [
				// keywords
				[
					iFocus_Link_Nest_Keyword_Model::build_from_generic_array(
						[
							'efficitur lacus at libero',
							'online marketing',
							'help',
							'https://linking.objav.digital/services/',
						]
					),
				],

				// original text
				'Etiam eu posuere ex, quis pretium est. Nulla venenatis, ligula in sollicitudin molestie, nibh arcu vehicula velit, sed facilisis urna justo vel turpis. '
				. 'Etiam tempus elit eu pharetra pretium. Sed in ullamcorper nibh, vitae imperdiet turpis. Suspendisse vestibulum interdum purus, a interdum justo elementum ut. '
				. 'Integer hendrerit fringilla bibendum. Maecenas efficitur lacus at libero vestibulum tempor. Nunc tincidunt elementum turpis, vel venenatis nulla hendrerit ut.'
				. ' Mauris porta, ante in tempus dictum, lacus quam convallis nisi, sit amet faucibus metus felis tempor ipsum. Praesent ac blandit felis, eget faucibus ex. '
				. 'Praesent aliquet elit et vulputate ullamcorper. Nam egestas sodales urna, in tincidunt ante elementum vitae. Fusce in arcu et dolor gravida rutrum vitae vel mi.'
				. ' Proin eget felis consectetur, ullamcorper lacus id, convallis nibh.',

				// expected result
				'Etiam eu posuere ex, quis pretium est. Nulla venenatis, ligula in sollicitudin molestie, nibh arcu vehicula velit, sed facilisis urna justo vel turpis. '
				. 'Etiam tempus elit eu pharetra pretium. Sed in ullamcorper nibh, vitae imperdiet turpis. Suspendisse vestibulum interdum purus, a interdum justo elementum ut. '
				. 'Integer hendrerit fringilla bibendum. Maecenas '
				. '<a href="https://linking.objav.digital/services/" title="online marketing" class="ifocus-link-nest" rel="help" target="_blank">efficitur lacus at libero</a>'
				. ' vestibulum tempor. Nunc tincidunt elementum turpis, vel venenatis nulla hendrerit ut.'
				. ' Mauris porta, ante in tempus dictum, lacus quam convallis nisi, sit amet faucibus metus felis tempor ipsum. Praesent ac blandit felis, eget faucibus ex. '
				. 'Praesent aliquet elit et vulputate ullamcorper. Nam egestas sodales urna, in tincidunt ante elementum vitae. Fusce in arcu et dolor gravida rutrum vitae vel mi.'
				. ' Proin eget felis consectetur, ullamcorper lacus id, convallis nibh.',

			],
		];
	}

	public function dataForMaxLinksVariations() {
		// the test is replacing words maximus, vene and malesuada (in this particular order)
		return [
			'no duplicates' => [
				// number of keywords to replace
				3,

				// original text
				'Amet luctus maximus venenatis lectus magna fringilla urna porttitor. Amet est placerat in egestas erat imperdiet sed euismod.'
				. ' Tempor nec feugiat nisl pretium fusce. Aenean sed adipiscing diam donec adipiscing tristique. '
				. 'Faucibus nisl tincidunt eget nullam non nisi est sit amet. Maecenas volutpat blandit aliquam etiam. '
				. 'Adipiscing elit duis tristique sollicitudin nibh sit vene amet commodo. Venenatis tellus in metus vulputate eu scelerisque. '
				. 'Enim nunc faucibus a pellentesque sit amet. Et malesuada fames ac turpis egestas sed tempus urna. '
				. 'A pellentesque sit amet porttitor eget dolor morbi non.',

				// expected result
				'Amet luctus '
				. '<a href="https://linking.objav.digital/about/" title="ifocus agency" class="ifocus-link-nest" rel="help" target="_blank">maximus</a>'
				. ' venenatis lectus magna fringilla urna porttitor. Amet est placerat in egestas erat imperdiet sed euismod.'
				. ' Tempor nec feugiat nisl pretium fusce. Aenean sed adipiscing diam donec adipiscing tristique. '
				. 'Faucibus nisl tincidunt eget nullam non nisi est sit amet. Maecenas volutpat blandit aliquam etiam. '
				. 'Adipiscing elit duis tristique sollicitudin nibh sit '
				. '<a href="https://linking.objav.digital/services/" title="online marketing" class="ifocus-link-nest" rel="help" target="_blank">vene</a>'
				. ' amet commodo. Venenatis tellus in metus vulputate eu scelerisque. '
				. 'Enim nunc faucibus a pellentesque sit amet. Et '
				. '<a href="https://linking.objav.digital/hiring/" title="latin blurb" class="ifocus-link-nest" rel="noopener" target="_blank">malesuada</a>'
				. ' fames ac turpis egestas sed tempus urna. '
				. 'A pellentesque sit amet porttitor eget dolor morbi non.',
			],

			'duplicates, only 3 need replacing' => [
				// number of keywords to replace
				3,

				// original text
				'Amet luctus maximus venenatis lectus magna malesuada fringilla urna porttitor. Amet est placerat vene in egestas erat imperdiet sed euismod.'
				. ' Tempor nec feugiat nisl pretium fusce. Aenean sed adipiscing diam donec adipiscing tristique. '
				. 'Faucibus nisl tincidunt eget nullam non nisi est sit amet. Maecenas volutpat blandit aliquam etiam. '
				. 'Adipiscing elit duis tristique sollicitudin nibh sit vene amet commodo. Venenatis tellus in metus vulputate eu scelerisque. '
				. 'Enim nunc faucibus a pellentesque sit amet. Et malesuada fames ac turpis egestas sed tempus urna. '
				. 'A pellentesque sit amet porttitor eget maximus dolor morbi non.',

				// expected result
				'Amet luctus '
				. '<a href="https://linking.objav.digital/about/" title="ifocus agency" class="ifocus-link-nest" rel="help" target="_blank">maximus</a>'
				. ' venenatis lectus magna '
				. '<a href="https://linking.objav.digital/hiring/" title="latin blurb" class="ifocus-link-nest" rel="noopener" target="_blank">malesuada</a>'
				. ' fringilla urna porttitor. Amet est placerat '
				. '<a href="https://linking.objav.digital/services/" title="online marketing" class="ifocus-link-nest" rel="help" target="_blank">vene</a>'
				. ' in egestas erat imperdiet sed euismod.'
				. ' Tempor nec feugiat nisl pretium fusce. Aenean sed adipiscing diam donec adipiscing tristique. '
				. 'Faucibus nisl tincidunt eget nullam non nisi est sit amet. Maecenas volutpat blandit aliquam etiam. '
				. 'Adipiscing elit duis tristique sollicitudin nibh sit vene amet commodo. Venenatis tellus in metus vulputate eu scelerisque. '
				. 'Enim nunc faucibus a pellentesque sit amet. Et malesuada fames ac turpis egestas sed tempus urna. '
				. 'A pellentesque sit amet porttitor eget maximus dolor morbi non.',
			],

			'only 1 replacement, 1st in order' => [
				// number of keywords to replace
				1,

				// original text
				'Amet luctus maximus venenatis lectus magna vene fringilla urna malesuada porttitor.',

				// expected result
				'Amet luctus '
				. '<a href="https://linking.objav.digital/about/" title="ifocus agency" class="ifocus-link-nest" rel="help" target="_blank">maximus</a>'
				. ' venenatis lectus magna vene fringilla urna malesuada porttitor.'
			],

			'only 1 replacement, 2nd in order' => [
				// number of keywords to replace
				1,

				// original text
				'Amet luctus vene venenatis lectus maximus vene fringilla urna malesuada porttitor.',

				// expected result
				'Amet luctus '
				. '<a href="https://linking.objav.digital/services/" title="online marketing" class="ifocus-link-nest" rel="help" target="_blank">vene</a>'
				. ' venenatis lectus magna maximus fringilla urna malesuada porttitor.'
			],

			'only 1 replacement, 3rd in order' => [
				// number of keywords to replace
				1,

				// original text
				'Amet luctus malesuada venenatis lectus maximus fringilla urna vene porttitor.',

				// expected result
				'Amet luctus '
				. '<a href="https://linking.objav.digital/hiring/" title="latin blurb" class="ifocus-link-nest" rel="noopener" target="_blank">malesuada</a>'
				. ' venenatis lectus magna maximus fringilla urna vene porttitor.'
			],

			// '<a href="https://linking.objav.digital/about/" title="ifocus agency" class="ifocus-link-nest" rel="help" target="_blank">maximus</a>'
			// '<a href="https://linking.objav.digital/hiring/" title="latin blurb" class="ifocus-link-nest" rel="noopener" target="_blank">malesuada</a>'
			// '<a href="https://linking.objav.digital/services/" title="online marketing" class="ifocus-link-nest" rel="help" target="_blank">vene</a>'
		];
	}
}
