<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Utilities;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Utilities\Call as UCall;
use Dracodeum\Kit\Utilities\Call\Exceptions;

/** @see \Dracodeum\Kit\Utilities\Call */
class CallTest extends TestCase
{
	//Public methods
	/**
	 * Test <code>validate</code> method.
	 * 
	 * @dataProvider provideValidateMethodData
	 * @testdox Call::validate({$function}) === void
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @return void
	 */
	public function testValidateMethod($function): void
	{
		$this->assertNull(UCall::validate($function));
		$this->assertTrue(UCall::validate($function, true));
	}
	
	/**
	 * Provide <code>validate</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>validate</code> method data.</p>
	 */
	public function provideValidateMethodData(): array
	{
		return [
			['strlen'],
			[function () {}],
			[new CallTest_InvokeableClass()],
			[CallTest_Class::class . '::getString'],
			[CallTest_Class::class . '->getString'],
			[[CallTest_Class::class, 'getString']],
			[[new CallTest_Class(), 'getString']],
			[CallTest_Class::class . '::getStaticString'],
			[CallTest_Class::class . '->getStaticString'],
			[[CallTest_Class::class, 'getStaticString']],
			[[new CallTest_Class(), 'getStaticString']],
			[CallTest_Class::class . '::getProtectedInteger'],
			[CallTest_Class::class . '->getProtectedInteger'],
			[[CallTest_Class::class, 'getProtectedInteger']],
			[[new CallTest_Class(), 'getProtectedInteger']],
			[CallTest_Class::class . '::getProtectedStaticInteger'],
			[CallTest_Class::class . '->getProtectedStaticInteger'],
			[[CallTest_Class::class, 'getProtectedStaticInteger']],
			[[new CallTest_Class(), 'getProtectedStaticInteger']],
			[CallTest_Class::class . '::getPrivateBoolean'],
			[CallTest_Class::class . '->getPrivateBoolean'],
			[[CallTest_Class::class, 'getPrivateBoolean']],
			[[new CallTest_Class(), 'getPrivateBoolean']],
			[CallTest_Class::class . '::getPrivateStaticBoolean'],
			[CallTest_Class::class . '->getPrivateStaticBoolean'],
			[[CallTest_Class::class, 'getPrivateStaticBoolean']],
			[[new CallTest_Class(), 'getPrivateStaticBoolean']],
			[CallTest_AbstractClass::class . '::getString'],
			[[CallTest_AbstractClass::class, 'getString']],
			[CallTest_AbstractClass::class . '::getStaticString'],
			[[CallTest_AbstractClass::class, 'getStaticString']],
			[CallTest_AbstractClass::class . '::getProtectedInteger'],
			[[CallTest_AbstractClass::class, 'getProtectedInteger']],
			[CallTest_AbstractClass::class . '::getProtectedStaticInteger'],
			[[CallTest_AbstractClass::class, 'getProtectedStaticInteger']],
			[CallTest_Interface::class . '::getString'],
			[[CallTest_Interface::class, 'getString']],
			[CallTest_Interface::class . '::getStaticString'],
			[[CallTest_Interface::class, 'getStaticString']]
		];
	}
	
	/**
	 * Test <code>validate</code> method expecting an <code>InvalidFunction</code> exception to be thrown.
	 * 
	 * @dataProvider provideValidateMethodDataForInvalidFunctionException
	 * @testdox Call::validate({$function}) --> InvalidFunction exception
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @return void
	 */
	public function testValidateMethodInvalidFunctionException($function): void
	{
		$this->expectException(Exceptions\InvalidFunction::class);
		UCall::validate($function);
	}
	
	/**
	 * Test <code>validate</code> method with <var>$no_throw</var> set to <code>true</code>, 
	 * expecting boolean <code>false</code> to be returned.
	 * 
	 * @dataProvider provideValidateMethodDataForInvalidFunctionException
	 * @testdox Call::validate({$function}, true) === false
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @return void
	 */
	public function testValidateMethodNoThrowFalse($function): void
	{
		$this->assertFalse(UCall::validate($function, true));
	}
	
	/**
	 * Provide <code>validate</code> method data for an <code>InvalidFunction</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The provided <code>validate</code> method data for an <code>InvalidFunction</code> exception to be thrown.</p>
	 */
	public function provideValidateMethodDataForInvalidFunctionException(): array
	{
		return [
			[null],
			[false],
			[true],
			[0],
			[0.0],
			[''],
			['strlens'],
			[' strlen '],
			['strlen()'],
			['NonExistentClass::getNonExistent'],
			['CallTest_Class::getString'],
			['CallTest_AbstractClass::getString'],
			['CallTest_Interface::getString'],
			[CallTest_InvokeableClass::class],
			[CallTest_Class::class . '::getString()'],
			[CallTest_Class::class . '->getString()'],
			[CallTest_Class::class . '::getNonExistent'],
			[CallTest_AbstractClass::class . '::getNonExistent'],
			[CallTest_Interface::class . '::getNonExistent'],
			[[]],
			[['strlen']],
			[[function () {}]],
			[[new CallTest_InvokeableClass()]],
			[[CallTest_Class::class, 'getString()']],
			[[new CallTest_Class(), 'getString()']],
			[[CallTest_Class::class, 'getNonExistent']],
			[[new CallTest_Class(), 'getNonExistent']],
			[[CallTest_AbstractClass::class, 'getNonExistent']],
			[[CallTest_Interface::class, 'getNonExistent']],
			[[CallTest_Class::class, 'getString', null]],
			[[new CallTest_Class(), 'getString', null]],
			[new \stdClass()],
			[fopen(__FILE__, 'r')]
		];
	}
	
	/**
	 * Test <code>reflection</code> method.
	 * 
	 * @dataProvider provideReflectionMethodData
	 * @testdox Call::reflection({$function}) === $expected_class
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param string $expected_class
	 * <p>The expected method return instance class.</p>
	 * @return void
	 */
	public function testReflectionMethod($function, string $expected_class): void
	{
		foreach ([false, true] as $no_throw) {
			$this->assertInstanceOf($expected_class, UCall::reflection($function, $no_throw));
		}
	}
	
	/**
	 * Provide <code>reflection</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>reflection</code> method data.</p>
	 */
	public function provideReflectionMethodData(): array
	{
		return [
			['strlen', \ReflectionFunction::class],
			[function () {}, \ReflectionFunction::class],
			[new CallTest_InvokeableClass(), \ReflectionMethod::class],
			[CallTest_Class::class . '::getString', \ReflectionMethod::class],
			[CallTest_Class::class . '->getString', \ReflectionMethod::class],
			[[CallTest_Class::class, 'getString'], \ReflectionMethod::class],
			[[new CallTest_Class(), 'getString'], \ReflectionMethod::class],
			[CallTest_Class::class . '::getStaticString', \ReflectionMethod::class],
			[CallTest_Class::class . '->getStaticString', \ReflectionMethod::class],
			[[CallTest_Class::class, 'getStaticString'], \ReflectionMethod::class],
			[[new CallTest_Class(), 'getStaticString'], \ReflectionMethod::class],
			[CallTest_Class::class . '::getProtectedInteger', \ReflectionMethod::class],
			[CallTest_Class::class . '->getProtectedInteger', \ReflectionMethod::class],
			[[CallTest_Class::class, 'getProtectedInteger'], \ReflectionMethod::class],
			[[new CallTest_Class(), 'getProtectedInteger'], \ReflectionMethod::class],
			[CallTest_Class::class . '::getProtectedStaticInteger', \ReflectionMethod::class],
			[CallTest_Class::class . '->getProtectedStaticInteger', \ReflectionMethod::class],
			[[CallTest_Class::class, 'getProtectedStaticInteger'], \ReflectionMethod::class],
			[[new CallTest_Class(), 'getProtectedStaticInteger'], \ReflectionMethod::class],
			[CallTest_Class::class . '::getPrivateBoolean', \ReflectionMethod::class],
			[CallTest_Class::class . '->getPrivateBoolean', \ReflectionMethod::class],
			[[CallTest_Class::class, 'getPrivateBoolean'], \ReflectionMethod::class],
			[[new CallTest_Class(), 'getPrivateBoolean'], \ReflectionMethod::class],
			[CallTest_Class::class . '::getPrivateStaticBoolean', \ReflectionMethod::class],
			[CallTest_Class::class . '->getPrivateStaticBoolean', \ReflectionMethod::class],
			[[CallTest_Class::class, 'getPrivateStaticBoolean'], \ReflectionMethod::class],
			[[new CallTest_Class(), 'getPrivateStaticBoolean'], \ReflectionMethod::class],
			[CallTest_AbstractClass::class . '::getString', \ReflectionMethod::class],
			[[CallTest_AbstractClass::class, 'getString'], \ReflectionMethod::class],
			[CallTest_AbstractClass::class . '::getStaticString', \ReflectionMethod::class],
			[[CallTest_AbstractClass::class, 'getStaticString'], \ReflectionMethod::class],
			[CallTest_AbstractClass::class . '::getProtectedInteger', \ReflectionMethod::class],
			[[CallTest_AbstractClass::class, 'getProtectedInteger'], \ReflectionMethod::class],
			[CallTest_AbstractClass::class . '::getProtectedStaticInteger', \ReflectionMethod::class],
			[[CallTest_AbstractClass::class, 'getProtectedStaticInteger'], \ReflectionMethod::class],
			[CallTest_Interface::class . '::getString', \ReflectionMethod::class],
			[[CallTest_Interface::class, 'getString'], \ReflectionMethod::class],
			[CallTest_Interface::class . '::getStaticString', \ReflectionMethod::class],
			[[CallTest_Interface::class, 'getStaticString'], \ReflectionMethod::class]
		];
	}
	
	/**
	 * Test <code>reflection</code> method expecting an <code>InvalidFunction</code> exception to be thrown.
	 * 
	 * @dataProvider provideValidateMethodDataForInvalidFunctionException
	 * @testdox Call::reflection({$function}) --> InvalidFunction exception
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @return void
	 */
	public function testReflectionMethodInvalidFunctionException($function): void
	{
		$this->expectException(Exceptions\InvalidFunction::class);
		UCall::reflection($function);
	}
	
	/**
	 * Test <code>reflection</code> method with <var>$no_throw</var> set to <code>true</code>, 
	 * expecting <code>null</code> to be returned.
	 * 
	 * @dataProvider provideValidateMethodDataForInvalidFunctionException
	 * @testdox Call::reflection({$function}, true) === false
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @return void
	 */
	public function testReflectionMethodNoThrowNull($function): void
	{
		$this->assertNull(UCall::reflection($function, true));
	}
}



/** Test case dummy invokeable class. */
class CallTest_InvokeableClass
{
	public function __invoke() {}
}



/** Test case dummy class. */
class CallTest_Class
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
}



/** Test case dummy abstract class. */
abstract class CallTest_AbstractClass
{
	abstract public function getString(): string;
	abstract public function setString(string $string): void;
	abstract public static function getStaticString(): string;
	abstract public static function setStaticString(string $string): void;
	abstract protected function getProtectedInteger(): int;
	abstract protected function setProtectedInteger(int $integer): void;
	abstract protected static function getProtectedStaticInteger(): int;
	abstract protected static function setProtectedStaticInteger(int $integer): void;
}



/** Test case dummy interface. */
interface CallTest_Interface
{
	public function getString(): string;
	public function setString(string $string): void;
	public static function getStaticString(): string;
	public static function setStaticString(string $string): void;
}
