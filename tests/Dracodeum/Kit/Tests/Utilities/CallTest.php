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
	
	/**
	 * Test <code>hash</code> method.
	 * 
	 * @dataProvider provideHashMethodData
	 * @testdox Call::hash({$function}, '$algorithm') === '$expected'
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param string $algorithm
	 * <p>The method <var>$algorithm</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testHashMethod($function, string $algorithm, string $expected): void
	{
		$this->assertSame($expected, UCall::hash($function, $algorithm));
		$this->assertSame(hex2bin($expected), UCall::hash($function, $algorithm, true));
	}
	
	/**
	 * Provide <code>hash</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>hash</code> method data.</p>
	 */
	public function provideHashMethodData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', 'MD5', '73d3a702db472629f27b06ac8f056476'],
			['strlen', 'SHA1', '6c19df52f4536474beeb594b4c186a34750bfbba'],
			[function () {}, 'MD5', '965cc11a3a4ab9adc6fbca323da5b689'],
			[function () {}, 'SHA1', '3484cced870a27abaf3f90f9fd765af07d2fd431'],
			[new CallTest_InvokeableClass(), 'MD5', '06678054507a08aa3179b82ad631ef77'],
			[new CallTest_InvokeableClass(), 'SHA1', 'cbb1e245087d78bcf998a89555f3517ceb491114'],
			[[$class, 'getString'], 'MD5', 'bd30850066e2deb385eae54d1369edfb'],
			[[$class, 'getString'], 'SHA1', 'b9a57503eae0f1f314b0cdb601643ba0425831be'],
			[[$class, 'getStaticString'], 'MD5', '606b220c861176152934f9382769c599'],
			[[$class, 'getStaticString'], 'SHA1', 'b159051679c6240c133cdef3c0580e94f7c82910'],
			[[$class, 'getProtectedInteger'], 'MD5', 'dd99454f0ed6bdf40cd53505eb95d82e'],
			[[$class, 'getProtectedInteger'], 'SHA1', '54a3af5a5a2e3834dc27b2330af720591f08bedd'],
			[[$class, 'getProtectedStaticInteger'], 'MD5', 'e133f75ff6daf5bcc1f49977f30b2539'],
			[[$class, 'getProtectedStaticInteger'], 'SHA1', '7eeb8eba694d404d4dd0ee373a440885e0656da9'],
			[[$class, 'getPrivateBoolean'], 'MD5', '9a48386d5b2337a69be15ee81d72c001'],
			[[$class, 'getPrivateBoolean'], 'SHA1', 'd404611ae4b96908c71461a068754952d13a3879'],
			[[$class, 'getPrivateStaticBoolean'], 'MD5', '66dbcf40dcb6a4256ed221f7732a59d8'],
			[[$class, 'getPrivateStaticBoolean'], 'SHA1', '8b6eecb43fe5e566a15712f3e8a8976fe88aaf79'],
			[[$class_abstract, 'getString'], 'MD5', 'ece33a1617681db17ead701866ecd9e7'],
			[[$class_abstract, 'getString'], 'SHA1', 'c3f476aae2ad3b67bb2d285262a2540a73bf04a1'],
			[[$class_abstract, 'getStaticString'], 'MD5', '7b7150342e21e913a53c81eb63eb287e'],
			[[$class_abstract, 'getStaticString'], 'SHA1', '19b0cfaaea5573ee99d5fd72c89010f30bccca78'],
			[[$class_abstract, 'getProtectedInteger'], 'MD5', '72eaa3cc4ce34e479b7cea9e88e43265'],
			[[$class_abstract, 'getProtectedInteger'], 'SHA1', '1830d3eb48fe3a46e09d7375035ecb1844305f7a'],
			[[$class_abstract, 'getProtectedStaticInteger'], 'MD5', '025ca1809056650db34bfc4fe408ea6e'],
			[[$class_abstract, 'getProtectedStaticInteger'], 'SHA1', '004bee0cf4cd8a093038ef1fb93833f9e9e7eab3'],
			[[$interface, 'getString'], 'MD5', '63bbda9db93a144d13e2cae345129925'],
			[[$interface, 'getString'], 'SHA1', '5ea3b983c1067d627caded88f2f084705a176370'],
			[[$interface, 'getStaticString'], 'MD5', 'c80138e2cedbe7fe7b3e6a36f30f6d1e'],
			[[$interface, 'getStaticString'], 'SHA1', '1714a7d457deace9a68c7ab3b012ec24f597d2b6']
		];
	}
	
	/**
	 * Test <code>modifiers</code> method.
	 * 
	 * @dataProvider provideModifiersMethodData
	 * @testdox Call::modifiers({$function}) === $expected
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param string[] $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testModifiersMethod($function, array $expected): void
	{
		$this->assertSame($expected, UCall::modifiers($function));
	}
	
	/**
	 * Provide <code>modifiers</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>modifiers</code> method data.</p>
	 */
	public function provideModifiersMethodData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', []],
			[function () {}, []],
			[new CallTest_InvokeableClass(), ['public']],
			[[$class, 'getString'], ['public']],
			[[$class, 'getStaticString'], ['public', 'static']],
			[[$class, 'getProtectedInteger'], ['protected']],
			[[$class, 'getProtectedStaticInteger'], ['protected', 'static']],
			[[$class, 'getPrivateBoolean'], ['private']],
			[[$class, 'getPrivateStaticBoolean'], ['private', 'static']],
			[[$class, 'getFinalString'], ['final', 'public']],
			[[$class, 'getFinalStaticString'], ['final', 'public', 'static']],
			[[$class, 'getFinalProtectedInteger'], ['final', 'protected']],
			[[$class, 'getFinalProtectedStaticInteger'], ['final', 'protected', 'static']],
			[[$class, 'getFinalPrivateBoolean'], ['final', 'private']],
			[[$class, 'getFinalPrivateStaticBoolean'], ['final', 'private', 'static']],
			[[$class_abstract, 'getString'], ['abstract', 'public']],
			[[$class_abstract, 'getStaticString'], ['abstract', 'public', 'static']],
			[[$class_abstract, 'getProtectedInteger'], ['abstract', 'protected']],
			[[$class_abstract, 'getProtectedStaticInteger'], ['abstract', 'protected', 'static']],
			[[$class_abstract, 'getFinalString'], ['final', 'public']],
			[[$class_abstract, 'getFinalStaticString'], ['final', 'public', 'static']],
			[[$class_abstract, 'getFinalProtectedInteger'], ['final', 'protected']],
			[[$class_abstract, 'getFinalProtectedStaticInteger'], ['final', 'protected', 'static']],
			[[$class_abstract, 'getFinalPrivateBoolean'], ['final', 'private']],
			[[$class_abstract, 'getFinalPrivateStaticBoolean'], ['final', 'private', 'static']],
			[[$interface, 'getString'], ['abstract', 'public']],
			[[$interface, 'getStaticString'], ['abstract', 'public', 'static']]
		];
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
	
	final public function getFinalString(): string
	{
		return '';
	}
	
	final public function setFinalString(string $string): void {}
	
	final public static function getFinalStaticString(): string
	{
		return '';
	}
	
	final public static function setFinalStaticString(string $string): void {}
	
	final protected function getFinalProtectedInteger(): int
	{
		return 0;
	}
	
	final protected function setFinalProtectedInteger(int $integer): void {}
	
	final protected static function getFinalProtectedStaticInteger(): int
	{
		return 0;
	}
	
	final protected static function setFinalProtectedStaticInteger(int $integer): void {}
	
	final private function getFinalPrivateBoolean(): bool
	{
		return false;
	}
	
	final private function setFinalPrivateBoolean(bool $boolean): void {}
	
	final private static function getFinalPrivateStaticBoolean(): bool
	{
		return false;
	}
	
	final private static function setFinalPrivateStaticBoolean(bool $boolean): void {}
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
	
	final public function getFinalString(): string
	{
		return '';
	}
	
	final public function setFinalString(string $string): void {}
	
	final public static function getFinalStaticString(): string
	{
		return '';
	}
	
	final public static function setFinalStaticString(string $string): void {}
	
	final protected function getFinalProtectedInteger(): int
	{
		return 0;
	}
	
	final protected function setFinalProtectedInteger(int $integer): void {}
	
	final protected static function getFinalProtectedStaticInteger(): int
	{
		return 0;
	}
	
	final protected static function setFinalProtectedStaticInteger(int $integer): void {}
	
	final private function getFinalPrivateBoolean(): bool
	{
		return false;
	}
	
	final private function setFinalPrivateBoolean(bool $boolean): void {}
	
	final private static function getFinalPrivateStaticBoolean(): bool
	{
		return false;
	}
	
	final private static function setFinalPrivateStaticBoolean(bool $boolean): void {}
}



/** Test case dummy interface. */
interface CallTest_Interface
{
	public function getString(): string;
	public function setString(string $string): void;
	public static function getStaticString(): string;
	public static function setStaticString(string $string): void;
}
