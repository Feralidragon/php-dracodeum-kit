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
use Dracodeum\Kit\Options\Text as TextOptions;
use stdClass;

/** @see \Dracodeum\Kit\Prototypes\Types\Boolean */
class BooleanTest extends TestCase
{
	//Public methods
	/**
	 * Test process.
	 * 
	 * @dataProvider provideProcessData
	 * @testdox Type->process(&{$value}) => value = $expected
	 * 
	 * @param mixed $value
	 * <p>The process value parameter to test with.</p>
	 * @param bool $expected
	 * <p>The expected processed value.</p>
	 * @return void
	 */
	public function testProcess(mixed $value, bool $expected): void
	{
		$this->assertNull(Component::build(Prototype::class)->process($value));
		$this->assertSame($expected, $value);
	}
	
	/**
	 * Provide process data.
	 * 
	 * @return array
	 * <p>The provided process data.</p>
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
	 * @dataProvider provideProcessData_NonInternal
	 * @testdox Type->process(&{$value}) => value = $expected [non-internal]
	 * 
	 * @param mixed $value
	 * <p>The process value parameter to test with.</p>
	 * @param bool $expected
	 * <p>The expected processed value.</p>
	 * @return void
	 */
	public function testProcess_NonInternal(mixed $value, bool $expected): void
	{
		$component = Component::build(Prototype::class);
		foreach (EContext::getValues() as $context) {
			if ($context !== EContext::INTERNAL) {
				$this->assertNull($component->process($value, $context));
				$this->assertSame($expected, $value);
			}
		}
	}
	
	/**
	 * Provide process data (non-internal).
	 * 
	 * @return array
	 * <p>The provided process data (non-internal).</p>
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
	 * @dataProvider provideProcessData_NonInternal_Error
	 * @testdox Type->process(&{$value}) => error [non-internal]
	 * 
	 * @param mixed $value
	 * <p>The process value parameter to test with.</p>
	 * @return void
	 */
	public function testProcess_NonInternal_Error(mixed $value): void
	{
		$component = Component::build(Prototype::class);
		foreach (EContext::getValues() as $context) {
			if ($context !== EContext::INTERNAL) {
				$v = $value;
				$this->assertInstanceOf(Error::class, $component->process($v, $context));
				$this->assertSame($value, $v);
			}
		}
	}
	
	/**
	 * Provide process data (non-internal error).
	 * 
	 * @return array
	 * <p>The provided process data (non-internal error).</p>
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
	 * Test <code>Dracodeum\Kit\Prototypes\Type\Interfaces\InformationProducer</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Prototypes\Type\Interfaces\InformationProducer
	 * @return void
	 */
	public function testInformationProducerInterface(): void
	{
		$component = Component::build(Prototype::class);
		$this->assertInstanceOf(Text::class, $component->getLabel());
		$this->assertInstanceOf(Text::class, $component->getDescription());
	}
	
	/**
	 * Test <code>Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier
	 * @return void
	 */
	public function testTextifierInterface(): void
	{
		//initialize
		$component = Component::build(Prototype::class);
		$text_options_enduser = TextOptions::build(['info_level' => EInfoLevel::ENDUSER]);
		$text_options_tech = TextOptions::build(['info_level' => EInfoLevel::TECHNICAL]);
		
		//false
		$text_false = $component->textify(false);
		$this->assertInstanceOf(Text::class, $text_false);
		$this->assertSame('no', $text_false->toString($text_options_enduser));
		$this->assertSame('false', $text_false->toString($text_options_tech));
		
		//true
		$text_true = $component->textify(true);
		$this->assertInstanceOf(Text::class, $text_true);
		$this->assertSame('yes', $text_true->toString($text_options_enduser));
		$this->assertSame('true', $text_true->toString($text_options_tech));
	}
}
