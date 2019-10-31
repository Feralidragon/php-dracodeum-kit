<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Utilities;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Utilities\Base64 as UBase64;

/** @see \Dracodeum\Kit\Utilities\Base64 */
class Base64Test extends TestCase
{
	//Public methods
	/**
	 * Test <code>encoded</code> method.
	 * 
	 * @dataProvider provideEncodedMethodData
	 * @testdox Base64::encoded('$string', $url_safe) === $expected
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param bool|null $url_safe
	 * <p>The method <var>$url_safe</var> parameter to test with.</p>
	 * @param bool $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testEncodedMethod(string $string, ?bool $url_safe, bool $expected): void
	{
		$this->assertSame($expected, UBase64::encoded($string, $url_safe));
	}
	
	/**
	 * Provide <code>encoded</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>encoded</code> method data.</p>
	 */
	public function provideEncodedMethodData(): array
	{
		return [
			['', null, false],
			['', false, false],
			['', true, false],
			[' ', null, false],
			[' ', false, false],
			[' ', true, false],
			['=', null, false],
			['=', false, false],
			['=', true, false],
			['a', null, false],
			['a', false, false],
			['a', true, false],
			['aB/C', null, true],
			['aB/C=', null, false],
			['aB/C==', null, false],
			['aB/Cd', null, false],
			['aB/Cd=', null, false],
			['aB/Cd==', null, false],
			['aB/Cd3', null, true],
			['aB/Cd3=', null, false],
			['aB/Cd3==', null, true],
			['aB/Cd3+', null, true],
			['aB/Cd3+=', null, true],
			['aB/Cd3+==', null, false],
			['aB/Cd3+', false, true],
			['aB/Cd3+=', false, true],
			['aB/Cd3+==', false, false],
			['aB/Cd3+', true, false],
			['aB/Cd3+=', true, false],
			['aB/Cd3+==', true, false],
			['aB_Cd3-', null, true],
			['aB_Cd3-=', null, false],
			['aB_Cd3-==', null, false],
			['aB_Cd3-', false, false],
			['aB_Cd3-=', false, false],
			['aB_Cd3-==', false, false],
			['aB_Cd3-', true, true],
			['aB_Cd3-=', true, false],
			['aB_Cd3-==', true, false],
			['aB/Cd3-', null, false],
			['aB_Cd3+=', null, false],
			['aB/Cd3-', false, false],
			['aB_Cd3+=', false, false],
			['aB/Cd3-', true, false],
			['aB_Cd3+=', true, false]
		];
	}
}
