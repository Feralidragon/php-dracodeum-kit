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
use Dracodeum\Kit\Primitives\Error;
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
			[new stdClass, true],
			[fopen(__FILE__, 'r'), true]
		];
	}
	
	/**
	 * Test process non-internal.
	 * 
	 * @dataProvider provideProcessNonInternalData
	 * @testdox Type->process(&{$value}) => value = $expected [non-internal]
	 * 
	 * @param mixed $value
	 * <p>The process value parameter to test with.</p>
	 * @param bool $expected
	 * <p>The expected processed value.</p>
	 * @return void
	 */
	public function testProcessNonInternal(mixed $value, bool $expected): void
	{
		$component = Component::build(Prototype::class);
		foreach (EContext::getValues() as $context) {
			if ($context !== EContext::INTERNAL) {
				$component->context = $context;
				$this->assertNull($component->process($value));
				$this->assertSame($expected, $value);
			}
		}
	}
	
	/**
	 * Provide process non-internal data.
	 * 
	 * @return array
	 * <p>The provided process non-internal data.</p>
	 */
	public function provideProcessNonInternalData(): array
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
	 * Test process non-internal error.
	 * 
	 * @dataProvider provideProcessNonInternalErrorData
	 * @testdox Type->process(&{$value}) => error [non-internal]
	 * 
	 * @param mixed $value
	 * <p>The process value parameter to test with.</p>
	 * @return void
	 */
	public function testProcessNonInternalError(mixed $value): void
	{
		$component = Component::build(Prototype::class);
		foreach (EContext::getValues() as $context) {
			if ($context !== EContext::INTERNAL) {
				$v = $value;
				$component->context = $context;
				$this->assertInstanceOf(Error::class, $component->process($v));
				$this->assertSame($value, $v);
			}
		}
	}
	
	/**
	 * Provide process non-internal error data.
	 * 
	 * @return array
	 * <p>The provided process non-internal error data.</p>
	 */
	public function provideProcessNonInternalErrorData(): array
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
			[new stdClass],
			[fopen(__FILE__, 'r')]
		];
	}
}
