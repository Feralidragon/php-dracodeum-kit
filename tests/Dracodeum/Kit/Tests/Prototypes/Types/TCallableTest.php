<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Types;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Types\TCallable as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Utilities\Call as UCall;
use stdClass;
use Closure;

/** @see \Dracodeum\Kit\Prototypes\Types\TCallable */
class TCallableTest extends TestCase
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
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess(mixed $value, array $properties = []): void
	{
		$this->assertNull(Component::build(Prototype::class, $properties)->process($value));
	}
	
	/**
	 * Test process (closurify).
	 * 
	 * @testdox Process (closurify)
	 * @dataProvider provideProcessData
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess_Closure(mixed $value, array $properties = []): void
	{
		$v = $value;
		$this->assertNull(Component::build(Prototype::class, ['closurify' => true] + $properties)->process($v));
		$this->assertInstanceOf(Closure::class, $v);
		$this->assertSame(UCall::hash($value), UCall::hash($v));
	}
	
	/**
	 * Provide process data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData(): array
	{
		//initialize
		$c = new TCallableTest_Class();
		$ci = new TCallableTest_InvokeableClass();
		$class = TCallableTest_Class::class;
		
		//return
		return [
			['strlen'],
			[function () {}],
			[$ci],
			[[$c, 'getString']],
			["{$class}::getStaticString"],
			[[$class, 'getStaticString']],
			[[$c, 'getStaticString']],
			[$c->getGetProtectedIntegerClosure()],
			[$class::getGetProtectedStaticIntegerClosure()],
			[$c->getGetPrivateBooleanClosure()],
			[$class::getGetPrivateStaticBooleanClosure()],
			['strlen', ['template' => 'strlen']],
			['strlen', ['template' => function (string $string) {}]],
			['strlen', ['template' => function (string $string): int {}]],
			[function ($string): int {}, ['template' => 'strlen']],
			[function (string $string): int {}, ['template' => 'strlen']],
			[$ci, ['template' => function () {}]],
			[$ci, ['template' => function (): ?TCallableTest_Class {}]],
			[function ($n = null): ?TCallableTest_Class2 {}, ['template' => $ci]],
			[$ci, ['template' => function (): void {}]],
			[[$c, 'getString'], ['template' => [$class, 'getStaticString']]],
			[[$c, 'setString'], ['template' => [$class, 'setStaticString']]],
			[[$class, 'getStaticString'], ['template' => [$c, 'getString']]],
			[[$class, 'setStaticString'], ['template' => [$c, 'setString']]],
			[[$c, 'getString'], ['template' => function (): string {}]],
			[[$c, 'getString'], ['template' => function (): ?string {}]],
			[[$c, 'setString'], ['template' => function (string $s): void {}]],
			[[$class, 'getStaticString'], ['template' => function (): string {}]],
			[[$class, 'getStaticString'], ['template' => function (): ?string {}]],
			[[$class, 'setStaticString'], ['template' => function (string $s): void {}]],
			[$c->getGetProtectedIntegerClosure(), ['template' => $class::getGetProtectedStaticIntegerClosure()]],
			[$c->getSetProtectedIntegerClosure(), ['template' => $class::getSetProtectedStaticIntegerClosure()]],
			[$class::getGetProtectedStaticIntegerClosure(), ['template' => $c->getGetProtectedIntegerClosure()]],
			[$class::getSetProtectedStaticIntegerClosure(), ['template' => $c->getSetProtectedIntegerClosure()]],
			[$c->getGetProtectedIntegerClosure(), ['template' => function (): int {}]],
			[$c->getGetProtectedIntegerClosure(), ['template' => function (): ?int {}]],
			[$c->getSetProtectedIntegerClosure(), ['template' => function (int $i): void {}]],
			[$class::getGetProtectedStaticIntegerClosure(), ['template' => function (): int {}]],
			[$class::getGetProtectedStaticIntegerClosure(), ['template' => function (): ?int {}]],
			[$class::getSetProtectedStaticIntegerClosure(), ['template' => function (int $i): void {}]],
			[$c->getGetPrivateBooleanClosure(), ['template' => $class::getGetPrivateStaticBooleanClosure()]],
			[$c->getSetPrivateBooleanClosure(), ['template' => $class::getSetPrivateStaticBooleanClosure()]],
			[$class::getGetPrivateStaticBooleanClosure(), ['template' => $c->getGetPrivateBooleanClosure()]],
			[$class::getSetPrivateStaticBooleanClosure(), ['template' => $c->getSetPrivateBooleanClosure()]],
			[$c->getGetPrivateBooleanClosure(), ['template' => function (): bool {}]],
			[$c->getGetPrivateBooleanClosure(), ['template' => function (): ?bool {}]],
			[$c->getSetPrivateBooleanClosure(), ['template' => function (bool $b): void {}]],
			[$class::getGetPrivateStaticBooleanClosure(), ['template' => function (): bool {}]],
			[$class::getGetPrivateStaticBooleanClosure(), ['template' => function (): ?bool {}]],
			[$class::getSetPrivateStaticBooleanClosure(), ['template' => function (bool $b): void {}]],
			[
				function (
					string $s, stdClass $sc, ?object $o = null, float &$f = 0.0, ?int ...$i
				): TCallableTest_Class2 {},
				['template' => function (
					string $s, stdClass $sc, ?object $o = null, float &$f = 0.0, ?int ...$i
				): TCallableTest_Class {}]
			],
			[
				function (
					?string $s, object $sc, ?object $o = null, float &$f = 0.0, ?int ...$i
				): TCallableTest_Class2 {},
				['template' => function (
					string $s, stdClass $sc, stdClass $o, float &$f = 1.0, int ...$i
				): TCallableTest_Class {}]
			],
			[
				function (
					?string $s, object $sc, ?object $o = null, float &$f = 0.0, ?int ...$i
				): TCallableTest_Class2 {},
				['template' => function (
					string $s, stdClass $sc, stdClass $o, float &$f, int ...$i
				): TCallableTest_Class {}]
			]
		];
	}
	
	/**
	 * Test process (error).
	 * 
	 * @testdox Process (error)
	 * @dataProvider provideProcessData_Error
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess_Error(mixed $value, array $properties = []): void
	{
		$this->assertInstanceOf(Error::class, Component::build(Prototype::class, $properties)->process($value));
	}
	
	/**
	 * Provide process data (error).
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData_Error(): array
	{
		//initialize
		$c = new TCallableTest_Class();
		$ci = new TCallableTest_InvokeableClass();
		$class = TCallableTest_Class::class;
		
		//return
		return [
			[null],
			[false],
			[true],
			[1],
			[1.1],
			[''],
			[' '],
			['123'],
			['foo'],
			[[]],
			[new stdClass()],
			[fopen(__FILE__, 'r')],
			['strlens'],
			[' strlen '],
			['strlen()'],
			['NonExistentClass::getNonExistent'],
			['TCallableTest_Class::getStaticString'],
			[TCallableTest_InvokeableClass::class],
			["{$class}::getString"],
			["{$class}->getString"],
			["{$class}::getString()"],
			["{$class}->getString()"],
			["{$class}::getNonExistent"],
			["{$class}->getStaticString"],
			[['strlen']],
			[[function () {}]],
			[[$ci]],
			[[$class, 'getString()']],
			[[$c, 'getString()']],
			[[$class, 'getNonExistent']],
			[[$c, 'getNonExistent']],
			[[$class, 'getString', null]],
			[[$c, 'getString', null]],
			[[$c, 'getProtectedInteger']],
			["{$class}::getProtectedStaticInteger"],
			[[$class, 'getProtectedStaticInteger']],
			[[$c, 'getProtectedStaticInteger']],
			[[$c, 'getPrivateBoolean']],
			["{$class}::getPrivateStaticBoolean"],
			[[$class, 'getPrivateStaticBoolean']],
			[[$c, 'getPrivateStaticBoolean']],
			['strlen', ['template' => 'str_repeat']],
			['str_repeat', ['template' => 'strlen']],
			['strlen', ['template' => function ($string) {}]],
			['strlen', ['template' => function ($string): int {}]],
			[function ($string) {}, ['template' => 'strlen']],
			[function (string $string) {}, ['template' => 'strlen']],
			[function () {}, ['template' => $ci]],
			[$ci, ['template' => function (): TCallableTest_Class2 {}]],
			[$ci, ['template' => function (): TCallableTest_Class {}]],
			[$ci, ['template' => function ($n) {}]],
			[function ($n): ?TCallableTest_Class2 {}, ['template' => $ci]],
			[$ci, ['template' => function ($n = null) {}]],
			[function (): void {}, ['template' => $ci]],
			[$ci, ['template' => function (): bool {}]],
			[function (): bool {}, ['template' => $ci]],
			[[$c, 'getString'], ['template' => [$class, 'setStaticString']]],
			[[$c, 'setString'], ['template' => [$class, 'getStaticString']]],
			[[$class, 'getStaticString'], ['template' => function (): object {}]],
			[[$class, 'setStaticString'], ['template' => function (?string $s): void {}]],
			[[$class, 'setStaticString'], ['template' => function (string $s = ''): void {}]],
			[$c->getGetProtectedIntegerClosure(), ['template' => $class::getSetProtectedStaticIntegerClosure()]],
			[$c->getSetProtectedIntegerClosure(), ['template' => $class::getGetProtectedStaticIntegerClosure()]],
			[$class::getGetProtectedStaticIntegerClosure(), ['template' => function (): object {}]],
			[$class::getSetProtectedStaticIntegerClosure(), ['template' => function (?int $i): void {}]],
			[$class::getSetProtectedStaticIntegerClosure(), [
				'template' => function (int $i = 0): void {}
			]],
			[$c->getGetPrivateBooleanClosure(), ['template' => $class::getSetPrivateStaticBooleanClosure()]],
			[$c->getSetPrivateBooleanClosure(), ['template' => $class::getGetPrivateStaticBooleanClosure()]],
			[$class::getGetPrivateStaticBooleanClosure(), ['template' => function (): object {}]],
			[$class::getSetPrivateStaticBooleanClosure(), ['template' => function (?bool $b): void {}]],
			[$class::getSetPrivateStaticBooleanClosure(), [
				'template' => function (bool $b = false): void {}
			]],
			[
				function (
					string $s, stdClass $sc, ?object $o = null, float &$f = 0.0, ?int ...$i
				): TCallableTest_Class {},
				['template' => function (
					string $s, stdClass $sc, ?object $o = null, float &$f = 0.0, ?int ...$i
				): TCallableTest_Class2 {}]
			],
			[
				function (
					string $s, object $sc, ?object $o = null, float &$f = 0.0, ?int ...$i
				): TCallableTest_Class2 {},
				['template' => function (
					?string $s, stdClass $sc, stdClass $o, float &$f = 1.0, int ...$i
				): TCallableTest_Class {}]
			],
			[
				function (
					?string $s, stdClass $sc, ?object $o = null, float &$f = 0.0, ?int ...$i
				): TCallableTest_Class2 {},
				['template' => function (
					string $s, object $sc, stdClass $o, float &$f = 1.0, int ...$i
				): TCallableTest_Class {}]
			],
			[
				function (
					?string $s, object $sc, stdClass $o = null, float &$f = 0.0, ?int ...$i
				): TCallableTest_Class2 {},
				['template' => function (
					string $s, stdClass $sc, ?object $o, float &$f = 1.0, int ...$i
				): TCallableTest_Class {}]
			],
			[
				function (
					?string $s, object $sc, ?object $o = null, float &$f = 0.0, ?int ...$i
				): TCallableTest_Class2 {},
				['template' => function (
					string $s, stdClass $sc, stdClass $o, int &$f = 1, int ...$i
				): TCallableTest_Class {}]
			],
			[
				function (
					?string $s, object $sc, ?object $o = null, float &$f = 0.0, ?int ...$i
				): TCallableTest_Class2 {},
				['template' => function (
					string $s, stdClass $sc, stdClass $o, float $f = 1.0, int ...$i
				): TCallableTest_Class {}]
			],
			[
				function (
					?string $s, object $sc, ?object $o = null, float &$f = 0.0, ?int ...$i
				): TCallableTest_Class2 {},
				['template' => function (
					string $s, stdClass $sc, stdClass $o, float &$f = 0.0, int $i = 1
				): TCallableTest_Class {}]
			]
		];
	}
	
	/**
	 * Test `Textifier` interface.
	 * 
	 * @testdox Textifier interface
	 * @dataProvider provideTextifierInterfaceData
	 * 
	 * @see \Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param string $expected
	 * The expected textified value.
	 */
	public function testTextifierInterface(mixed $value, string $expected): void
	{
		$text = Component::build(Prototype::class)->textify($value);
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($expected, $text->toString());
	}
	
	/**
	 * Provide `Textifier` interface data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideTextifierInterfaceData(): array
	{
		//initialize
		$c = new TCallableTest_Class();
		$ci = new TCallableTest_InvokeableClass();
		$class = TCallableTest_Class::class;
		$class_invokeable = TCallableTest_InvokeableClass::class;
		
		//return
		return [
			['strlen', "callable<strlen>"],
			[function () {}, "callable<anonymous@" . __FILE__ . ":358>"],
			[$ci, "callable<{$class_invokeable}::__invoke>"],
			[[$c, 'getString'], "callable<{$class}::getString>"],
			["{$class}::getStaticString", "callable<{$class}::getStaticString>"],
			[[$class, 'getStaticString'], "callable<{$class}::getStaticString>"],
			[[$c, 'getStaticString'], "callable<{$class}::getStaticString>"],
			[$c->getGetProtectedIntegerClosure(), "callable<{$class}::getProtectedInteger>"],
			[$class::getGetProtectedStaticIntegerClosure(), "callable<{$class}::getProtectedStaticInteger>"],
			[$c->getGetPrivateBooleanClosure(), "callable<{$class}::getPrivateBoolean>"],
			[$class::getGetPrivateStaticBooleanClosure(), "callable<{$class}::getPrivateStaticBoolean>"]
		];
	}
}



/** Test case dummy invokeable class. */
class TCallableTest_InvokeableClass
{
	public function __invoke(): ?TCallableTest_Class {}
}



/** Test case dummy class. */
class TCallableTest_Class
{
	public function getString(): string
	{
		return '';
	}
	
	public function setString(string $string): void {}
	
	public static function getStaticString(): string
	{
		return '';
	}
	
	public static function setStaticString(string $string): void {}
	
	protected function getProtectedInteger(): int
	{
		return 0;
	}
	
	protected function setProtectedInteger(int $integer): void {}
	
	protected static function getProtectedStaticInteger(): int
	{
		return 0;
	}
	
	protected static function setProtectedStaticInteger(int $integer): void {}
	
	private function getPrivateBoolean(): bool
	{
		return false;
	}
	
	private function setPrivateBoolean(bool $boolean): void {}
	
	private static function getPrivateStaticBoolean(): bool
	{
		return false;
	}
	
	private static function setPrivateStaticBoolean(bool $boolean): void {}
	
	public function getGetProtectedIntegerClosure(): Closure
	{
		return Closure::fromCallable([$this, 'getProtectedInteger']);
	}
	
	public function getSetProtectedIntegerClosure(): Closure
	{
		return Closure::fromCallable([$this, 'setProtectedInteger']);
	}
	
	public static function getGetProtectedStaticIntegerClosure(): Closure
	{
		return Closure::fromCallable([self::class, 'getProtectedStaticInteger']);
	}
	
	public static function getSetProtectedStaticIntegerClosure(): Closure
	{
		return Closure::fromCallable([self::class, 'setProtectedStaticInteger']);
	}
	
	public function getGetPrivateBooleanClosure(): Closure
	{
		return Closure::fromCallable([$this, 'getPrivateBoolean']);
	}
	
	public function getSetPrivateBooleanClosure(): Closure
	{
		return Closure::fromCallable([$this, 'setPrivateBoolean']);
	}
	
	public static function getGetPrivateStaticBooleanClosure(): Closure
	{
		return Closure::fromCallable([self::class, 'getPrivateStaticBoolean']);
	}
	
	public static function getSetPrivateStaticBooleanClosure(): Closure
	{
		return Closure::fromCallable([self::class, 'setPrivateStaticBoolean']);
	}
}



/** Test case dummy class 2. */
class TCallableTest_Class2 extends TCallableTest_Class {}
