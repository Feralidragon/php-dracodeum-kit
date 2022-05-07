<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Type\Interfaces;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier as ITextifier;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/** @see \Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier */
class TextifierTest extends TestCase
{
	//Public methods
	/**
	 * Test.
	 * 
	 * @testdox Test
	 * @dataProvider provideData
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param string $expected
	 * The expected textified value.
	 * 
	 * @return void
	 */
	public function test(mixed $value, string $expected): void
	{
		//build
		$component = Component::build(TextifierTest_Prototype::class, ['nullable' => true]);
		
		//assert
		foreach ([false, true] as $no_throw) {
			$text = $component->textify($value, no_throw: $no_throw);
			$this->assertInstanceOf(Text::class, $text);
			$this->assertSame($expected, (string)$text);
		}
	}
	
	/**
	 * Provide data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideData(): array
	{
		return [
			[105, '105'],
			[108, '1 0 8'],
			['foo', 'T:foo'],
			[-719.5, '- 7 1 9 . 5'],
			['foobar', 'f o o b a r'],
			[null, 'null']
		];
	}
}



/** Test case dummy prototype class. */
class TextifierTest_Prototype extends Prototype implements ITextifier
{
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		return null;
	}
	
	public function textify(mixed $value)
	{
		return $value === 'foo'
			? Text::build("T:{{value}}")->setParameter('value', $value)
			: ($value === 105 ? null : implode(' ', str_split($value)));
	}
}
