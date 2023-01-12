<?php
/**
 * Class LinkStrippingTest
 *
 * @package iFocus_Link_Nest
 */

class LinkStrippingTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @dataProvider dataForLinkStripping
	 */
	public function test_link_stripping( $originalText, $expectedResult ) {
		$this->assertEquals( $expectedResult, iFocus_Link_Nest_Text_Processor::strip_links( $originalText ) );
	}

	public function dataForLinkStripping() {
		return [
			'single link without matching CSS class' => [
				// original text
				'<div class="content-wrapper"><p>Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id '
				. '<a href="https://www.strava.com/" title="vianočné filmy" class="out-traffic" rel="next noopener" target="_blank">repellat</a>. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt.</p></div>',

				// expected result
				'<div class="content-wrapper"><p>Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id '
				. '<a href="https://www.strava.com/" title="vianočné filmy" class="out-traffic" rel="next noopener" target="_blank">repellat</a>. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt.</p></div>',
			],

			'single link with matching CSS class' => [
				// original text
				'<div class="content-wrapper"><p>Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id '
				. '<a href="https://www.strava.com/" title="vianočné filmy" class="ifocus-link-nest" rel="next noopener" target="_blank">repellat</a>. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt.</p></div>',

				// expected result
				'<div class="content-wrapper"><p>Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. '
				. 'Ad repellendus id repellat. Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt.</p></div>',
			],

			'single link with matching CSS class (1st attribute)' => [
				// original text
				'<div class="content-wrapper"><p>Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id '
				. '<a class="ifocus-link-nest" href="https://www.strava.com/" title="vianočné filmy" rel="next noopener" target="_blank">repellat</a>. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt.</p></div>',

				// expected result
				'<div class="content-wrapper"><p>Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. '
				. 'Ad repellendus id repellat. Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt.</p></div>',
			],

			'single link with matching CSS class (last attribute)' => [
				// original text
				'<div class="content-wrapper"><p>Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id '
				. '<a href="https://www.strava.com/" title="vianočné filmy" rel="next noopener" target="_blank" class="ifocus-link-nest">repellat</a>. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt.</p></div>',

				// expected result
				'<div class="content-wrapper"><p>Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. '
				. 'Ad repellendus id repellat. Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt.</p></div>',
			],

			'single link with matching CSS class (only attribute)' => [
				// original text
				'<div class="content-wrapper"><p>Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id '
				. '<a class="ifocus-link-nest">repellat</a>. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt.</p></div>',

				// expected result
				'<div class="content-wrapper"><p>Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. '
				. 'Ad repellendus id repellat. Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt.</p></div>',
			],

			'unclosed link with matching CSS class' => [
				// original text
				'<div class="content-wrapper"><p>Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id '
				. '<a href="https://www.strava.com/" title="vianočné filmy" class="ifocus-link-nest" rel="next noopener" target="_blank">repellat<a>. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt.</p></div>',

				// expected result
				'<div class="content-wrapper"><p>Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id '
				. '<a href="https://www.strava.com/" title="vianočné filmy" class="ifocus-link-nest" rel="next noopener" target="_blank">repellat<a>. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt.</p></div>',
			],

			'different element with matching CSS class (link not matching)' => [
				// original text
				'<div class="ifocus-link-nest"><p>Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id '
				. '<a href="https://www.strava.com/" title="vianočné filmy" class="out-traffic" rel="next noopener" target="_blank">repellat</a>. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt.</p></div>',

				// expected result
				'<div class="ifocus-link-nest"><p>Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id '
				. '<a href="https://www.strava.com/" title="vianočné filmy" class="out-traffic" rel="next noopener" target="_blank">repellat</a>. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt.</p></div>',
			],

			'different element with matching CSS class (link matching)' => [
				// original text
				'<div class="ifocus-link-nest"><p>Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id '
				. '<a href="https://www.strava.com/" title="vianočné filmy" class="ifocus-link-nest" rel="next noopener" target="_blank">repellat</a>. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt.</p></div>',

				// expected result
				'<div class="ifocus-link-nest"><p>Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. '
				. 'Ad repellendus id repellat. Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt.</p></div>',
			],

			'all links removed' => [
				// original text
				'<p>Atque debitis eaque nihil veritatis et officiis. Voluptatem quo quia conseaas sdquatur. '
				. 'Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id '
				. '<a href="https://www.strava.com/" title="vianočné filmy" class="ifocus-link-nest" rel="next noopener" target="_blank">repellat</a>. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt. Et repellendus voluptas odio quas qui dolor qui. '
				. 'Necessitatibus quibusdam dolor eum maxime laborum odit ipsam. Testiky Molestiae ab sunt at maiores. '
				. 'Provident quia aut ut velit amet. Voluptatem est est placeat iure. Sit sunt quam est commodi voluptate labore occaecati. '
				. 'Minus hic ut aliquid <a href="https://dennikn.sk/3159726/mate-vypit-osem-poharov-vody-denne-je-to-mytus-riadte-sa-smadom-ukazal-vyskum/?ref=tit" class="ifocus-link-nest" title="riaďte sa smädom" rel="last noopener" target="_blank">deserunt</a>. '
				. 'Quidem ullam et exercitationem voluptas ratione. Et nesciunt consequuntur esse libero porro. Eligendi magni '
				. '<a href="https://dennikn.sk/3159726/mate-vypit-osem-poharov-vody-denne-je-to-mytus-riadte-sa-smadom-ukazal-vyskum/?ref=tit" title="riaďte sa smädom" class="ifocus-link-nest" rel="last noopener" target="_blank">deserunt</a> '
				. 'illo voluptas et dignissimos. Aut qui qui non fugiat. Eos suscipit occaecati iste quasi. '
				. '<a class="ifocus-link-nest" href="https://www.energy.gov/energysaver/installing-and-maintaining-small-wind-electric-system" title="wind turbines" rel="windy noopener" target="_blank">Quisquam</a> '
				. 'necessitatibus dolores voluptas provident cupiditate. Perspiciatis exercitationem vel enim ut laudantium repellendus corrupti. '
				. 'Et qui adipisci sed dolores. Blanditiis cum dignissimos dolor quis quo praesentium. Excepturi rerum consequuntur dolorem qui aut ut. '
				. 'Et excepturi ad excepturi eum ab.</p>',

				// expected result
				'<p>Atque debitis eaque nihil veritatis et officiis. Voluptatem quo quia conseaas sdquatur. '
				. 'Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id repellat. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt. Et repellendus voluptas odio quas qui dolor qui. '
				. 'Necessitatibus quibusdam dolor eum maxime laborum odit ipsam. Testiky Molestiae ab sunt at maiores. '
				. 'Provident quia aut ut velit amet. Voluptatem est est placeat iure. Sit sunt quam est commodi voluptate labore occaecati. '
				. 'Minus hic ut aliquid deserunt. '
				. 'Quidem ullam et exercitationem voluptas ratione. Et nesciunt consequuntur esse libero porro. Eligendi magni deserunt '
				. 'illo voluptas et dignissimos. Aut qui qui non fugiat. Eos suscipit occaecati iste quasi. Quisquam '
				. 'necessitatibus dolores voluptas provident cupiditate. Perspiciatis exercitationem vel enim ut laudantium repellendus corrupti. '
				. 'Et qui adipisci sed dolores. Blanditiis cum dignissimos dolor quis quo praesentium. Excepturi rerum consequuntur dolorem qui aut ut. '
				. 'Et excepturi ad excepturi eum ab.</p>',
			],

			'only link nest links removed' => [
				// original text
				'<p>Atque debitis eaque nihil veritatis et officiis. Voluptatem quo quia conseaas sdquatur. '
				. 'Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id '
				. '<a href="https://www.strava.com/" title="vianočné filmy" class="ifocus-link-nest" rel="next noopener" target="_blank">repellat</a>. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt. Et repellendus voluptas odio quas qui dolor qui. '
				. 'Necessitatibus quibusdam dolor eum maxime laborum odit ipsam. Testiky Molestiae ab sunt at maiores. '
				. 'Provident quia aut ut velit amet. Voluptatem est est placeat iure. Sit sunt quam est commodi voluptate labore occaecati. '
				. 'Minus hic ut aliquid <a href="https://dennikn.sk/3159726/mate-vypit-osem-poharov-vody-denne-je-to-mytus-riadte-sa-smadom-ukazal-vyskum/?ref=tit" class="cta-link" title="riaďte sa smädom" rel="last noopener" target="_blank">deserunt</a>. '
				. 'Quidem ullam et exercitationem voluptas ratione. Et nesciunt consequuntur esse libero porro. Eligendi magni '
				. '<a href="https://dennikn.sk/3159726/mate-vypit-osem-poharov-vody-denne-je-to-mytus-riadte-sa-smadom-ukazal-vyskum/?ref=tit" title="riaďte sa smädom" class="ifocus-link-nest" rel="last noopener" target="_blank">deserunt</a> '
				. 'illo voluptas et dignissimos. Aut qui qui non fugiat. Eos suscipit occaecati iste quasi. '
				. '<a class="ifocus-link-nest" href="https://www.energy.gov/energysaver/installing-and-maintaining-small-wind-electric-system" title="wind turbines" rel="windy noopener" target="_blank">Quisquam</a> '
				. 'necessitatibus dolores voluptas provident cupiditate. Perspiciatis exercitationem vel enim ut laudantium repellendus corrupti. '
				. 'Et qui adipisci sed dolores. Blanditiis cum dignissimos dolor quis quo praesentium. Excepturi rerum consequuntur dolorem qui aut ut. '
				. 'Et excepturi ad excepturi eum ab.</p>',

				// expected result
				'<p>Atque debitis eaque nihil veritatis et officiis. Voluptatem quo quia conseaas sdquatur. '
				. 'Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id repellat. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt. Et repellendus voluptas odio quas qui dolor qui. '
				. 'Necessitatibus quibusdam dolor eum maxime laborum odit ipsam. Testiky Molestiae ab sunt at maiores. '
				. 'Provident quia aut ut velit amet. Voluptatem est est placeat iure. Sit sunt quam est commodi voluptate labore occaecati. '
				. 'Minus hic ut aliquid <a href="https://dennikn.sk/3159726/mate-vypit-osem-poharov-vody-denne-je-to-mytus-riadte-sa-smadom-ukazal-vyskum/?ref=tit" class="cta-link" title="riaďte sa smädom" rel="last noopener" target="_blank">deserunt</a>. '
				. 'Quidem ullam et exercitationem voluptas ratione. Et nesciunt consequuntur esse libero porro. Eligendi magni deserunt '
				. 'illo voluptas et dignissimos. Aut qui qui non fugiat. Eos suscipit occaecati iste quasi. Quisquam '
				. 'necessitatibus dolores voluptas provident cupiditate. Perspiciatis exercitationem vel enim ut laudantium repellendus corrupti. '
				. 'Et qui adipisci sed dolores. Blanditiis cum dignissimos dolor quis quo praesentium. Excepturi rerum consequuntur dolorem qui aut ut. '
				. 'Et excepturi ad excepturi eum ab.</p>',
			],

			'no links removed' => [
				// original text
				'<p>Atque debitis eaque nihil veritatis et officiis. Voluptatem quo quia conseaas sdquatur. '
				. 'Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id '
				. '<a href="https://www.strava.com/" title="vianočné filmy" class="cross-sell-link" rel="next noopener" target="_blank">repellat</a>. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt. Et repellendus voluptas odio quas qui dolor qui. '
				. 'Necessitatibus quibusdam dolor eum maxime laborum odit ipsam. Testiky Molestiae ab sunt at maiores. '
				. 'Provident quia aut ut velit amet. Voluptatem est est placeat iure. Sit sunt quam est commodi voluptate labore occaecati. '
				. 'Minus hic ut aliquid <a href="https://dennikn.sk/3159726/mate-vypit-osem-poharov-vody-denne-je-to-mytus-riadte-sa-smadom-ukazal-vyskum/?ref=tit" class="cta-link" title="riaďte sa smädom" rel="last noopener" target="_blank">deserunt</a>. '
				. 'Quidem ullam et exercitationem voluptas ratione. Et nesciunt consequuntur esse libero porro. Eligendi magni '
				. '<a href="https://dennikn.sk/3159726/mate-vypit-osem-poharov-vody-denne-je-to-mytus-riadte-sa-smadom-ukazal-vyskum/?ref=tit" title="riaďte sa smädom" class="up-sell-link" rel="last noopener" target="_blank">deserunt</a> '
				. 'illo voluptas et dignissimos. Aut qui qui non fugiat. Eos suscipit occaecati iste quasi. '
				. '<a class="marketing-lead-gen" href="https://www.energy.gov/energysaver/installing-and-maintaining-small-wind-electric-system" title="wind turbines" rel="windy noopener" target="_blank">Quisquam</a> '
				. 'necessitatibus dolores voluptas provident cupiditate. Perspiciatis exercitationem vel enim ut laudantium repellendus corrupti. '
				. 'Et qui adipisci sed dolores. Blanditiis cum dignissimos dolor quis quo praesentium. Excepturi rerum consequuntur dolorem qui aut ut. '
				. 'Et excepturi ad excepturi eum ab.</p>',

				// expected result
				'<p>Atque debitis eaque nihil veritatis et officiis. Voluptatem quo quia conseaas sdquatur. '
				. 'Sed quia corporis esse ut. Accusantium perferendis vasdasoluptatem neque. Ad repellendus id '
				. '<a href="https://www.strava.com/" title="vianočné filmy" class="cross-sell-link" rel="next noopener" target="_blank">repellat</a>. '
				. 'Nemo et tempore porro eligendi. Et similique et rerum dolorem sunt. Et repellendus voluptas odio quas qui dolor qui. '
				. 'Necessitatibus quibusdam dolor eum maxime laborum odit ipsam. Testiky Molestiae ab sunt at maiores. '
				. 'Provident quia aut ut velit amet. Voluptatem est est placeat iure. Sit sunt quam est commodi voluptate labore occaecati. '
				. 'Minus hic ut aliquid <a href="https://dennikn.sk/3159726/mate-vypit-osem-poharov-vody-denne-je-to-mytus-riadte-sa-smadom-ukazal-vyskum/?ref=tit" class="cta-link" title="riaďte sa smädom" rel="last noopener" target="_blank">deserunt</a>. '
				. 'Quidem ullam et exercitationem voluptas ratione. Et nesciunt consequuntur esse libero porro. Eligendi magni '
				. '<a href="https://dennikn.sk/3159726/mate-vypit-osem-poharov-vody-denne-je-to-mytus-riadte-sa-smadom-ukazal-vyskum/?ref=tit" title="riaďte sa smädom" class="up-sell-link" rel="last noopener" target="_blank">deserunt</a> '
				. 'illo voluptas et dignissimos. Aut qui qui non fugiat. Eos suscipit occaecati iste quasi. '
				. '<a class="marketing-lead-gen" href="https://www.energy.gov/energysaver/installing-and-maintaining-small-wind-electric-system" title="wind turbines" rel="windy noopener" target="_blank">Quisquam</a> '
				. 'necessitatibus dolores voluptas provident cupiditate. Perspiciatis exercitationem vel enim ut laudantium repellendus corrupti. '
				. 'Et qui adipisci sed dolores. Blanditiis cum dignissimos dolor quis quo praesentium. Excepturi rerum consequuntur dolorem qui aut ut. '
				. 'Et excepturi ad excepturi eum ab.</p>',
			],

		];
	}
}
