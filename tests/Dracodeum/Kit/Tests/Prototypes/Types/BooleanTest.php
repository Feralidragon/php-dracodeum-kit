<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Types;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Types\Boolean as Prototype;
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;
use stdClass;

/** @see \Dracodeum\Kit\Prototypes\Types\Boolean */
class BooleanTest extends TestCase
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
	 * The expected processed value.
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
			[null, false],
			[false, false],
			[true, true],
			[0, false],
			[1, true],
			[10, true],
			[-1, true],
			[-10, true],
			[0.0, false],
			[1.0, true],
			[10.0, true],
			[-1.0, true],
			[-10.0, true],
			['', false],
			[' ', true],
			['0', false],
			['1', true],
			['f', true],
			['t', true],
			['false', true],
			['true', true],
			['off', true],
			['on', true],
			['no', true],
			['yes', true],
			[[], false],
			[[''], true],
			[new stdClass(), true],
			[fopen(__FILE__, 'r'), true]
		];
	}
	
	/**
	 * Test process (non-internal).
	 * 
	 * @testdox Process (non-internal)
	 * @dataProvider provideProcessData_NonInternal
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param mixed $expected
	 * The expected processed value.
	 * 
	 * @return void
	 */
	public function testProcess_NonInternal(mixed $value, mixed $expected): void
	{
		$component = Component::build(Prototype::class);
		foreach (EContext::getValues() as $context) {
			if ($context !== EContext::INTERNAL) {
				$v = $value;
				$this->assertNull($component->process($v, $context));
				$this->assertSame($expected, $v);
			}
		}
	}
	
	/**
	 * Provide process data (non-internal).
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData_NonInternal(): array
	{
		return [
			[false, false],
			[true, true],
			[0, false],
			[1, true],
			['0', false],
			['1', true],
			['f', false],
			['t', true],
			['false', false],
			['true', true],
			['off', false],
			['on', true],
			['no', false],
			['yes', true]
		];
	}
	
	/**
	 * Test process (non-internal error).
	 * 
	 * @testdox Process (non-internal error)
	 * @dataProvider provideProcessData_NonInternal_Error
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @return void
	 */
	public function testProcess_NonInternal_Error(mixed $value): void
	{
		$component = Component::build(Prototype::class);
		foreach (EContext::getValues() as $context) {
			if ($context !== EContext::INTERNAL) {
				$this->assertInstanceOf(Error::class, $component->process($value, $context));
			}
		}
	}
	
	/**
	 * Provide process data (non-internal error).
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData_NonInternal_Error(): array
	{
		return [
			[null],
			[10],
			[-1],
			[-10],
			[0.0],
			[1.0],
			[10.0],
			[-1.0],
			[-10.0],
			[''],
			[' '],
			[[]],
			[['']],
			[new stdClass()],
			[fopen(__FILE__, 'r')]
		];
	}
	
	/**
	 * Test `Textifier` interface.
	 * 
	 * @testdox Textifier interface
	 * 
	 * @see \Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier
	 * 
	 * @return void
	 */
	public function testTextifierInterface(): void
	{
		//initialize
		$component = Component::build(Prototype::class);
		
		//false
		$text_false = $component->textify(false);
		$this->assertInstanceOf(Text::class, $text_false);
		$this->assertSame('no', $text_false->toString(['info_level' => EInfoLevel::ENDUSER]));
		$this->assertSame('false', $text_false->toString(['info_level' => EInfoLevel::TECHNICAL]));
		
		//true
		$text_true = $component->textify(true);
		$this->assertInstanceOf(Text::class, $text_true);
		$this->assertSame('yes', $text_true->toString(['info_level' => EInfoLevel::ENDUSER]));
		$this->assertSame('true', $text_true->toString(['info_level' => EInfoLevel::TECHNICAL]));
	}
}
