<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Utilities;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Utilities\Base32 as UBase32;
use Dracodeum\Kit\Enumerations\Base32\Alphabet as EAlphabet;
//use Dracodeum\Kit\Utilities\Base32\Exceptions;

/** @see \Dracodeum\Kit\Utilities\Base32 */
class Base32Test extends TestCase
{
	//Public methods
	/**
	 * Test <code>encode</code> method.
	 * 
	 * @dataProvider provideEncodeMethodData
	 * @testdox Base32::encode('$string', $url_safe, '$alphabet') === '$expected'
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param bool $url_safe
	 * <p>The method <var>$url_safe</var> parameter to test with.</p>
	 * @param string $alphabet
	 * <p>The method <var>$alphabet</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testEncodeMethod(string $string, bool $url_safe, string $alphabet, string $expected): void
	{
		$this->assertSame($expected, UBase32::encode($string, $url_safe, $alphabet));
	}
	
	/**
	 * Provide <code>encode</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>encode</code> method data.</p>
	 */
	public function provideEncodeMethodData(): array
	{
		return [
			['', false, EAlphabet::RFC4648, ''],
			['', true, EAlphabet::RFC4648, ''],
			['a', false, EAlphabet::RFC4648, 'ME======'],
			['a', true, EAlphabet::RFC4648, 'ME'],
			['a', true, EAlphabet::ZBASE32, 'cr'],
			['a', true, EAlphabet::GEOHASH, 'd4'],
			['88', false, EAlphabet::RFC4648, 'HA4A===='],
			['88', true, EAlphabet::RFC4648, 'HA4A'],
			['88', true, EAlphabet::ZBASE32, '8yhy'],
			['88', true, EAlphabet::GEOHASH, '70w0'],
			['foo', false, EAlphabet::RFC4648, 'MZXW6==='],
			['foo', true, EAlphabet::RFC4648, 'MZXW6'],
			['foo', true, EAlphabet::ZBASE32, 'c3zs6'],
			['foo', true, EAlphabet::GEOHASH, 'dtrqy'],
			['bar8', false, EAlphabet::RFC4648, 'MJQXEOA='],
			['bar8', true, EAlphabet::RFC4648, 'MJQXEOA'],
			['bar8', true, EAlphabet::ZBASE32, 'cjozrqy'],
			['bar8', true, EAlphabet::GEOHASH, 'd9hr4f0'],
			['foobar', false, EAlphabet::RFC4648, 'MZXW6YTBOI======'],
			['foobar', true, EAlphabet::RFC4648, 'MZXW6YTBOI'],
			['foobar', true, EAlphabet::ZBASE32, 'c3zs6aubqe'],
			['foobar', true, EAlphabet::GEOHASH, 'dtrqysm1f8'],
			['foobarABCD', false, EAlphabet::RFC4648, 'MZXW6YTBOJAUEQ2E'],
			['foobarABCD', true, EAlphabet::RFC4648, 'MZXW6YTBOJAUEQ2E'],
			['foobarABCD', true, EAlphabet::ZBASE32, 'c3zs6aubqjywro4r'],
			['foobarABCD', true, EAlphabet::GEOHASH, 'dtrqysm1f90n4hu4'],
			["foobar\xfc\xfe\xfe\x0f", false, EAlphabet::RFC4648, 'MZXW6YTBOL6P57QP'],
			["foobar\xfc\xfe\xfe\x0f", true, EAlphabet::RFC4648, 'MZXW6YTBOL6P57QP'],
			["foobar\xfc\xfe\xfe\x0f", true, EAlphabet::ZBASE32, 'c3zs6aubqm6x79ox'],
			["foobar\xfc\xfe\xfe\x0f", true, EAlphabet::GEOHASH, 'dtrqysm1fcygxzhg'],
			["foobar\xfc\xfe\xfe\x0f.!", false, EAlphabet::RFC4648, 'MZXW6YTBOL6P57QPFYQQ===='],
			["foobar\xfc\xfe\xfe\x0f.!", true, EAlphabet::RFC4648, 'MZXW6YTBOL6P57QPFYQQ'],
			["foobar\xfc\xfe\xfe\x0f.!", true, EAlphabet::ZBASE32, 'c3zs6aubqm6x79oxfaoo'],
			["foobar\xfc\xfe\xfe\x0f.!", true, EAlphabet::GEOHASH, 'dtrqysm1fcygxzhg5shh'],
			["\x8c\x9d\xa6\x8b\x59\xee\x53\x94\x7a\xb3", false, EAlphabet::RFC4648, 'RSO2NC2Z5ZJZI6VT'],
			["\x8c\x9d\xa6\x8b\x59\xee\x53\x94\x7a\xb3", true, EAlphabet::RFC4648, 'RSO2NC2Z5ZJZI6VT'],
			["\x8c\x9d\xa6\x8b\x59\xee\x53\x94\x7a\xb3", true, EAlphabet::ZBASE32, 't1q4pn4373j3e6iu'],
			["\x8c\x9d\xa6\x8b\x59\xee\x53\x94\x7a\xb3", true, EAlphabet::GEOHASH, 'jkfue2utxt9t8ypm'],
			["\x05\x00\x1f\x0c\x3f\xf8\xcd\x2f\x33\x7e\xb6", false, EAlphabet::RFC4648, 'AUAB6DB77DGS6M36WY======'],
			["\x05\x00\x1f\x0c\x3f\xf8\xcd\x2f\x33\x7e\xb6", true, EAlphabet::RFC4648, 'AUAB6DB77DGS6M36WY'],
			["\x05\x00\x1f\x0c\x3f\xf8\xcd\x2f\x33\x7e\xb6", true, EAlphabet::ZBASE32, 'ywyb6db99dg16c56sa'],
			["\x05\x00\x1f\x0c\x3f\xf8\xcd\x2f\x33\x7e\xb6", true, EAlphabet::GEOHASH, '0n01y31zz36kydvyqs'],
			["\xea\xcd\x66\xa0\xb9\x95\x75\x1e\xe1\x89\x13\x45", false, EAlphabet::RFC4648, '5LGWNIFZSV2R5YMJCNCQ===='],
			["\xea\xcd\x66\xa0\xb9\x95\x75\x1e\xe1\x89\x13\x45", true, EAlphabet::RFC4648, '5LGWNIFZSV2R5YMJCNCQ'],
			["\xea\xcd\x66\xa0\xb9\x95\x75\x1e\xe1\x89\x13\x45", true, EAlphabet::ZBASE32, '7mgspef31i4t7acjnpno'],
			["\xea\xcd\x66\xa0\xb9\x95\x75\x1e\xe1\x89\x13\x45", true, EAlphabet::GEOHASH, 'xc6qe85tkpujxsd92e2h']
		];
	}
}
