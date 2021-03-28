<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Stringables;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Trim as Prototype;

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Trim */
class TrimTest extends TestCase
{
	//Public methods
	/**
	 * Test process.
	 * 
	 * @testdox Process
	 * @dataProvider provideProcessData
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param mixed $expected
	 * The expected value.
	 * 
	 * @return void
	 */
	public function testProcess(mixed $value, mixed $expected): void
	{
		$this->assertNull(Component::build(Prototype::class)->process($value));
		$this->assertSame($expected, $value);
	}
	
	/**
	 * Provide process data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData(): array
	{
		return [
			[' ', ''],
			['a', 'a'],
			[' a', 'a'],
			['a ', 'a'],
			[' a ', 'a'],
			['foo bar', 'foo bar'],
			[' foo bar', 'foo bar'],
			['foo bar ', 'foo bar'],
			[' foo bar ', 'foo bar'],
			["  foo\nbar\t\n", "foo\nbar"],
			["\n\tfoo\tbar\n", "foo\tbar"]
		];
	}
}
