<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Utilities;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Utilities\Call as UCall;
use Dracodeum\Kit\Utilities\Call\Exceptions;
use Dracodeum\Kit\Root\System;

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
			[function () {}, 'MD5', '4fcbf21e9765f9e09cabe17b77e7014e'],
			[function () {}, 'SHA1', '5351a27e6e63e80f5f13fb37f3f0ded612a26d49'],
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
	
	/**
	 * Test <code>name</code> method.
	 * 
	 * @dataProvider provideNameMethodData
	 * @testdox Call::name({$function}, $full, $short) === {$expected}
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param bool $full
	 * <p>The method <var>$full</var> parameter to test with.</p>
	 * @param bool $short
	 * <p>The method <var>$short</var> parameter to test with.</p>
	 * @param string|null $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testNameMethod($function, bool $full, bool $short, ?string $expected): void
	{
		$this->assertSame($expected, UCall::name($function, $full, $short));
	}
	
	/**
	 * Provide <code>name</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>name</code> method data.</p>
	 */
	public function provideNameMethodData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$class_invokeable = CallTest_InvokeableClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', false, false, 'strlen'],
			['strlen', false, true, 'strlen'],
			['strlen', true, false, 'strlen'],
			['strlen', true, true, 'strlen'],
			[function () {}, false, false, null],
			[function () {}, false, true, null],
			[function () {}, true, false, null],
			[function () {}, true, true, null],
			[new $class_invokeable(), false, false, '__invoke'],
			[new $class_invokeable(), false, true, '__invoke'],
			[new $class_invokeable(), true, false, "{$class_invokeable}::__invoke"],
			[new $class_invokeable(), true, true, 'CallTest_InvokeableClass::__invoke'],
			[[$class, 'getString'], false, false, 'getString'],
			[[$class, 'getString'], false, true, 'getString'],
			[[$class, 'getString'], true, false, "{$class}::getString"],
			[[$class, 'getString'], true, true, 'CallTest_Class::getString'],
			[[$class, 'getStaticString'], false, false, 'getStaticString'],
			[[$class, 'getStaticString'], false, true, 'getStaticString'],
			[[$class, 'getStaticString'], true, false, "{$class}::getStaticString"],
			[[$class, 'getStaticString'], true, true, 'CallTest_Class::getStaticString'],
			[[$class, 'getProtectedInteger'], false, false, 'getProtectedInteger'],
			[[$class, 'getProtectedInteger'], false, true, 'getProtectedInteger'],
			[[$class, 'getProtectedInteger'], true, false, "{$class}::getProtectedInteger"],
			[[$class, 'getProtectedInteger'], true, true, 'CallTest_Class::getProtectedInteger'],
			[[$class, 'getProtectedStaticInteger'], false, false, 'getProtectedStaticInteger'],
			[[$class, 'getProtectedStaticInteger'], false, true, 'getProtectedStaticInteger'],
			[[$class, 'getProtectedStaticInteger'], true, false, "{$class}::getProtectedStaticInteger"],
			[[$class, 'getProtectedStaticInteger'], true, true, 'CallTest_Class::getProtectedStaticInteger'],
			[[$class, 'getPrivateBoolean'], false, false, 'getPrivateBoolean'],
			[[$class, 'getPrivateBoolean'], false, true, 'getPrivateBoolean'],
			[[$class, 'getPrivateBoolean'], true, false, "{$class}::getPrivateBoolean"],
			[[$class, 'getPrivateBoolean'], true, true, 'CallTest_Class::getPrivateBoolean'],
			[[$class, 'getPrivateStaticBoolean'], false, false, 'getPrivateStaticBoolean'],
			[[$class, 'getPrivateStaticBoolean'], false, true, 'getPrivateStaticBoolean'],
			[[$class, 'getPrivateStaticBoolean'], true, false, "{$class}::getPrivateStaticBoolean"],
			[[$class, 'getPrivateStaticBoolean'], true, true, 'CallTest_Class::getPrivateStaticBoolean'],
			[[$class_abstract, 'getString'], false, false, 'getString'],
			[[$class_abstract, 'getString'], false, true, 'getString'],
			[[$class_abstract, 'getString'], true, false, "{$class_abstract}::getString"],
			[[$class_abstract, 'getString'], true, true, 'CallTest_AbstractClass::getString'],
			[[$class_abstract, 'getStaticString'], false, false, 'getStaticString'],
			[[$class_abstract, 'getStaticString'], false, true, 'getStaticString'],
			[[$class_abstract, 'getStaticString'], true, false, "{$class_abstract}::getStaticString"],
			[[$class_abstract, 'getStaticString'], true, true, 'CallTest_AbstractClass::getStaticString'],
			[[$class_abstract, 'getProtectedInteger'], false, false, 'getProtectedInteger'],
			[[$class_abstract, 'getProtectedInteger'], false, true, 'getProtectedInteger'],
			[[$class_abstract, 'getProtectedInteger'], true, false, "{$class_abstract}::getProtectedInteger"],
			[[$class_abstract, 'getProtectedInteger'], true, true, 'CallTest_AbstractClass::getProtectedInteger'],
			[[$class_abstract, 'getProtectedStaticInteger'], false, false, 'getProtectedStaticInteger'],
			[[$class_abstract, 'getProtectedStaticInteger'], false, true, 'getProtectedStaticInteger'],
			[[$class_abstract, 'getProtectedStaticInteger'], true, false,
				"{$class_abstract}::getProtectedStaticInteger"],
			[[$class_abstract, 'getProtectedStaticInteger'], true, true,
				'CallTest_AbstractClass::getProtectedStaticInteger'],
			[[$interface, 'getString'], false, false, 'getString'],
			[[$interface, 'getString'], false, true, 'getString'],
			[[$interface, 'getString'], true, false, "{$interface}::getString"],
			[[$interface, 'getString'], true, true, 'CallTest_Interface::getString'],
			[[$interface, 'getStaticString'], false, false, 'getStaticString'],
			[[$interface, 'getStaticString'], false, true, 'getStaticString'],
			[[$interface, 'getStaticString'], true, false, "{$interface}::getStaticString"],
			[[$interface, 'getStaticString'], true, true, 'CallTest_Interface::getStaticString']
		];
	}
	
	/**
	 * Test <code>parameters</code> method.
	 * 
	 * @dataProvider provideParametersMethodData
	 * @testdox Call::parameters({$function}, $flags) === $expected
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param array $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testParametersMethod($function, int $flags, array $expected): void
	{
		$this->assertSame($expected, UCall::parameters($function, $flags));
	}
	
	/**
	 * Provide <code>parameters</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>parameters</code> method data.</p>
	 */
	public function provideParametersMethodData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', 0x00, ['mixed $str']],
			['strlen', UCall::PARAMETERS_NO_MIXED_TYPE, ['$str']],
			[function () {}, 0x00, []],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null) {}, 0x00,
				[$class . ' $a' , 'stdClass $b', 'bool $e = false', 'mixed $k = null']],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null) {},
				UCall::PARAMETERS_TYPES_SHORT_NAMES,
				['CallTest_Class $a' , 'stdClass $b', 'bool $e = false', 'mixed $k = null']],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null) {},
				UCall::PARAMETERS_NAMESPACES_LEADING_SLASH,
				['\\' . $class . ' $a' , '\\stdClass $b', 'bool $e = false', 'mixed $k = null']],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null) {}, UCall::PARAMETERS_NO_MIXED_TYPE,
				[$class . ' $a' , 'stdClass $b', 'bool $e = false', '$k = null']],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null) {},
				UCall::PARAMETERS_TYPES_SHORT_NAMES | UCall::PARAMETERS_NAMESPACES_LEADING_SLASH,
				['CallTest_Class $a' , 'stdClass $b', 'bool $e = false', 'mixed $k = null']],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null) {},
				UCall::PARAMETERS_TYPES_SHORT_NAMES | UCall::PARAMETERS_NO_MIXED_TYPE,
				['CallTest_Class $a' , 'stdClass $b', 'bool $e = false', '$k = null']],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null) {},
				UCall::PARAMETERS_NAMESPACES_LEADING_SLASH | UCall::PARAMETERS_NO_MIXED_TYPE,
				['\\' . $class . ' $a' , '\\stdClass $b', 'bool $e = false', '$k = null']],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null) {},
				UCall::PARAMETERS_TYPES_SHORT_NAMES | UCall::PARAMETERS_NAMESPACES_LEADING_SLASH | 
				UCall::PARAMETERS_NO_MIXED_TYPE,
				['CallTest_Class $a' , 'stdClass $b', 'bool $e = false', '$k = null']],
			[function (CallTest_AbstractClass $ac, ?CallTest_Interface $i) {}, 0x00,
				[$class_abstract . ' $ac' , '?' . $interface . ' $i']],
			[function (CallTest_AbstractClass $ac, ?CallTest_Interface $i) {}, UCall::PARAMETERS_TYPES_SHORT_NAMES,
				['CallTest_AbstractClass $ac' , '?CallTest_Interface $i']],
			[function (CallTest_AbstractClass $ac, ?CallTest_Interface $i) {},
				UCall::PARAMETERS_NAMESPACES_LEADING_SLASH,
				['\\' . $class_abstract . ' $ac' , '?\\' . $interface . ' $i']],
			[function (CallTest_AbstractClass $ac, ?CallTest_Interface $i) {},
				UCall::PARAMETERS_TYPES_SHORT_NAMES | UCall::PARAMETERS_NAMESPACES_LEADING_SLASH,
				['CallTest_AbstractClass $ac' , '?CallTest_Interface $i']],
			[new CallTest_InvokeableClass(), 0x00, []],
			[new CallTest_InvokeableClass2(), 0x00,
				['string $s_foo = ' . CallTest_InvokeableClass2::class . '::FOO_CONSTANT']],
			[new CallTest_InvokeableClass2(), UCall::PARAMETERS_CONSTANTS_VALUES,
				['string $s_foo = "bar2foo"']],
			[new CallTest_InvokeableClass2(), UCall::PARAMETERS_TYPES_SHORT_NAMES,
				['string $s_foo = CallTest_InvokeableClass2::FOO_CONSTANT']],
			[new CallTest_InvokeableClass2(), UCall::PARAMETERS_NAMESPACES_LEADING_SLASH,
				['string $s_foo = \\' . CallTest_InvokeableClass2::class . '::FOO_CONSTANT']],
			[new CallTest_InvokeableClass2(),
				UCall::PARAMETERS_CONSTANTS_VALUES | UCall::PARAMETERS_TYPES_SHORT_NAMES,
				['string $s_foo = "bar2foo"']],
			[new CallTest_InvokeableClass2(),
				UCall::PARAMETERS_CONSTANTS_VALUES | UCall::PARAMETERS_NAMESPACES_LEADING_SLASH,
				['string $s_foo = "bar2foo"']],
			[new CallTest_InvokeableClass2(),
				UCall::PARAMETERS_TYPES_SHORT_NAMES | UCall::PARAMETERS_NAMESPACES_LEADING_SLASH,
				['string $s_foo = CallTest_InvokeableClass2::FOO_CONSTANT']],
			[new CallTest_InvokeableClass2(),
				UCall::PARAMETERS_CONSTANTS_VALUES | UCall::PARAMETERS_TYPES_SHORT_NAMES | 
				UCall::PARAMETERS_NAMESPACES_LEADING_SLASH,
				['string $s_foo = "bar2foo"']],
			[[$class, 'getString'], 0x00, []],
			[[$class, 'setString'], 0x00, ['string $string']],
			[[$class, 'getStaticString'], 0x00, []],
			[[$class, 'setStaticString'], 0x00, ['string $string']],
			[[$class, 'getProtectedInteger'], 0x00, []],
			[[$class, 'setProtectedInteger'], 0x00, ['int $integer']],
			[[$class, 'getProtectedStaticInteger'], 0x00, []],
			[[$class, 'setProtectedStaticInteger'], 0x00, ['int $integer']],
			[[$class, 'getPrivateBoolean'], 0x00, []],
			[[$class, 'setPrivateBoolean'], 0x00, ['bool $boolean']],
			[[$class, 'getPrivateStaticBoolean'], 0x00, []],
			[[$class, 'setPrivateStaticBoolean'], 0x00, ['bool $boolean']],
			[[$class_abstract, 'getString'], 0x00, []],
			[[$class_abstract, 'setString'], 0x00, ['string $string']],
			[[$class_abstract, 'getStaticString'], 0x00, []],
			[[$class_abstract, 'setStaticString'], 0x00, ['string $string']],
			[[$class_abstract, 'getProtectedInteger'], 0x00, []],
			[[$class_abstract, 'setProtectedInteger'], 0x00, ['int $integer']],
			[[$class_abstract, 'getProtectedStaticInteger'], 0x00, []],
			[[$class_abstract, 'setProtectedStaticInteger'], 0x00, ['int $integer']],
			[[$interface, 'getString'], 0x00, []],
			[[$interface, 'setString'], 0x00, ['string $string']],
			[[$interface, 'getStaticString'], 0x00, []],
			[[$interface, 'setStaticString'], 0x00, ['string $string']],
			[[$class, 'doStuff'], 0x00,
				['?float $fnumber', $class_abstract . ' $ac', '?' . $class . ' &$c', 'mixed $options',
				'callable $c_function', 'string $farboo = ' . $class . '::A_S', 'array $foob = ' . $class . '::B_ARRAY',
				'int $cint = ' . $class . '::C_CONSTANT', 'bool &$enable = ' . $class . '::D_ENABLE',
				'?stdClass $std = null', 'mixed $flags = SORT_STRING', 'mixed ...$p']],
			[[$class, 'doStuff'], UCall::PARAMETERS_CONSTANTS_VALUES,
				['?float $fnumber', $class_abstract . ' $ac', '?' . $class . ' &$c', 'mixed $options',
				'callable $c_function', 'string $farboo = "Aaa"', 'array $foob = ["foo"=>false,"bar"=>null]',
				'int $cint = 1200', 'bool &$enable = true', '?stdClass $std = null', 'mixed $flags = 2',
				'mixed ...$p']],
			[[$class, 'doStuff'], UCall::PARAMETERS_TYPES_SHORT_NAMES,
				['?float $fnumber', 'CallTest_AbstractClass $ac', '?CallTest_Class &$c', 'mixed $options',
				'callable $c_function', 'string $farboo = CallTest_Class::A_S', 'array $foob = CallTest_Class::B_ARRAY',
				'int $cint = CallTest_Class::C_CONSTANT', 'bool &$enable = CallTest_Class::D_ENABLE',
				'?stdClass $std = null', 'mixed $flags = SORT_STRING', 'mixed ...$p']],
			[[$class, 'doStuff'], UCall::PARAMETERS_NAMESPACES_LEADING_SLASH,
				['?float $fnumber', '\\' . $class_abstract . ' $ac', '?\\' . $class . ' &$c', 'mixed $options',
				'callable $c_function', 'string $farboo = \\' . $class . '::A_S',
				'array $foob = \\' . $class . '::B_ARRAY', 'int $cint = \\' . $class . '::C_CONSTANT',
				'bool &$enable = \\' . $class . '::D_ENABLE', '?\\stdClass $std = null', 'mixed $flags = \\SORT_STRING',
				'mixed ...$p']],
			[[$class, 'doStuff'], UCall::PARAMETERS_NO_MIXED_TYPE,
				['?float $fnumber', $class_abstract . ' $ac', '?' . $class . ' &$c', '$options', 'callable $c_function',
				'string $farboo = ' . $class . '::A_S', 'array $foob = ' . $class . '::B_ARRAY',
				'int $cint = ' . $class . '::C_CONSTANT', 'bool &$enable = ' . $class . '::D_ENABLE',
				'?stdClass $std = null', '$flags = SORT_STRING', '...$p']],
			[[$class, 'doStuff'], UCall::PARAMETERS_CONSTANTS_VALUES | UCall::PARAMETERS_TYPES_SHORT_NAMES,
				['?float $fnumber', 'CallTest_AbstractClass $ac', '?CallTest_Class &$c', 'mixed $options',
				'callable $c_function', 'string $farboo = "Aaa"', 'array $foob = ["foo"=>false,"bar"=>null]',
				'int $cint = 1200', 'bool &$enable = true', '?stdClass $std = null', 'mixed $flags = 2',
				'mixed ...$p']],
			[[$class, 'doStuff'], UCall::PARAMETERS_CONSTANTS_VALUES | UCall::PARAMETERS_NAMESPACES_LEADING_SLASH,
				['?float $fnumber', '\\' . $class_abstract . ' $ac', '?\\' . $class . ' &$c', 'mixed $options',
				'callable $c_function', 'string $farboo = "Aaa"', 'array $foob = ["foo"=>false,"bar"=>null]',
				'int $cint = 1200', 'bool &$enable = true', '?\\stdClass $std = null', 'mixed $flags = 2',
				'mixed ...$p']],
			[[$class, 'doStuff'], UCall::PARAMETERS_CONSTANTS_VALUES | UCall::PARAMETERS_NO_MIXED_TYPE,
				['?float $fnumber', $class_abstract . ' $ac', '?' . $class . ' &$c', '$options',
				'callable $c_function', 'string $farboo = "Aaa"', 'array $foob = ["foo"=>false,"bar"=>null]',
				'int $cint = 1200', 'bool &$enable = true', '?stdClass $std = null', '$flags = 2', '...$p']],
			[[$class, 'doStuff'], UCall::PARAMETERS_TYPES_SHORT_NAMES | UCall::PARAMETERS_NAMESPACES_LEADING_SLASH,
				['?float $fnumber', 'CallTest_AbstractClass $ac', '?CallTest_Class &$c', 'mixed $options',
				'callable $c_function', 'string $farboo = CallTest_Class::A_S', 'array $foob = CallTest_Class::B_ARRAY',
				'int $cint = CallTest_Class::C_CONSTANT', 'bool &$enable = CallTest_Class::D_ENABLE',
				'?stdClass $std = null', 'mixed $flags = \\SORT_STRING', 'mixed ...$p']],
			[[$class, 'doStuff'], UCall::PARAMETERS_TYPES_SHORT_NAMES | UCall::PARAMETERS_NO_MIXED_TYPE,
				['?float $fnumber', 'CallTest_AbstractClass $ac', '?CallTest_Class &$c', '$options',
				'callable $c_function', 'string $farboo = CallTest_Class::A_S', 'array $foob = CallTest_Class::B_ARRAY',
				'int $cint = CallTest_Class::C_CONSTANT', 'bool &$enable = CallTest_Class::D_ENABLE',
				'?stdClass $std = null', '$flags = SORT_STRING', '...$p']],
			[[$class, 'doStuff'], UCall::PARAMETERS_NAMESPACES_LEADING_SLASH | UCall::PARAMETERS_NO_MIXED_TYPE,
				['?float $fnumber', '\\' . $class_abstract . ' $ac', '?\\' . $class . ' &$c', '$options',
				'callable $c_function', 'string $farboo = \\' . $class . '::A_S',
				'array $foob = \\' . $class . '::B_ARRAY', 'int $cint = \\' . $class . '::C_CONSTANT',
				'bool &$enable = \\' . $class . '::D_ENABLE', '?\\stdClass $std = null', '$flags = \\SORT_STRING',
				'...$p']],
			[[$class, 'doStuff'],
				UCall::PARAMETERS_CONSTANTS_VALUES | UCall::PARAMETERS_TYPES_SHORT_NAMES | 
				UCall::PARAMETERS_NAMESPACES_LEADING_SLASH,
				['?float $fnumber', 'CallTest_AbstractClass $ac', '?CallTest_Class &$c', 'mixed $options',
				'callable $c_function', 'string $farboo = "Aaa"', 'array $foob = ["foo"=>false,"bar"=>null]',
				'int $cint = 1200', 'bool &$enable = true', '?stdClass $std = null', 'mixed $flags = 2',
				'mixed ...$p']],
			[[$class, 'doStuff'],
				UCall::PARAMETERS_CONSTANTS_VALUES | UCall::PARAMETERS_TYPES_SHORT_NAMES | 
				UCall::PARAMETERS_NO_MIXED_TYPE,
				['?float $fnumber', 'CallTest_AbstractClass $ac', '?CallTest_Class &$c', '$options',
				'callable $c_function', 'string $farboo = "Aaa"', 'array $foob = ["foo"=>false,"bar"=>null]',
				'int $cint = 1200', 'bool &$enable = true', '?stdClass $std = null', '$flags = 2', '...$p']],
			[[$class, 'doStuff'],
				UCall::PARAMETERS_CONSTANTS_VALUES | UCall::PARAMETERS_NAMESPACES_LEADING_SLASH | 
				UCall::PARAMETERS_NO_MIXED_TYPE,
				['?float $fnumber', '\\' . $class_abstract . ' $ac', '?\\' . $class . ' &$c', '$options',
				'callable $c_function', 'string $farboo = "Aaa"', 'array $foob = ["foo"=>false,"bar"=>null]',
				'int $cint = 1200', 'bool &$enable = true', '?\\stdClass $std = null', '$flags = 2', '...$p']],
			[[$class, 'doStuff'],
				UCall::PARAMETERS_TYPES_SHORT_NAMES | UCall::PARAMETERS_NAMESPACES_LEADING_SLASH | 
				UCall::PARAMETERS_NO_MIXED_TYPE,
				['?float $fnumber', 'CallTest_AbstractClass $ac', '?CallTest_Class &$c', '$options',
				'callable $c_function', 'string $farboo = CallTest_Class::A_S', 'array $foob = CallTest_Class::B_ARRAY',
				'int $cint = CallTest_Class::C_CONSTANT', 'bool &$enable = CallTest_Class::D_ENABLE',
				'?stdClass $std = null', '$flags = \\SORT_STRING', '...$p']],
			[[$class, 'doStuff'],
				UCall::PARAMETERS_CONSTANTS_VALUES | UCall::PARAMETERS_TYPES_SHORT_NAMES | 
				UCall::PARAMETERS_NAMESPACES_LEADING_SLASH | UCall::PARAMETERS_NO_MIXED_TYPE,
				['?float $fnumber', 'CallTest_AbstractClass $ac', '?CallTest_Class &$c', '$options',
				'callable $c_function', 'string $farboo = "Aaa"', 'array $foob = ["foo"=>false,"bar"=>null]',
				'int $cint = 1200', 'bool &$enable = true', '?stdClass $std = null', '$flags = 2', '...$p']]
		];
	}
	
	/**
	 * Test <code>type</code> method.
	 * 
	 * @dataProvider provideTypeMethodData
	 * @testdox Call::type({$function}, $flags) === '$expected'
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testTypeMethod($function, int $flags, string $expected): void
	{
		$this->assertSame($expected, UCall::type($function, $flags));
	}
	
	/**
	 * Provide <code>type</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>type</code> method data.</p>
	 */
	public function provideTypeMethodData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', 0x00, 'mixed'],
			['strlen', UCall::TYPE_NO_MIXED, ''],
			[function () {}, 0x00, 'mixed'],
			[function () {}, UCall::TYPE_NO_MIXED, ''],
			[function (): void {}, 0x00, 'void'],
			[function (): bool {}, 0x00, 'bool'],
			[function (): ?bool {}, 0x00, '?bool'],
			[function (): int {}, 0x00, 'int'],
			[function (): ?int {}, 0x00, '?int'],
			[function (): float {}, 0x00, 'float'],
			[function (): ?float {}, 0x00, '?float'],
			[function (): string {}, 0x00, 'string'],
			[function (): ?string {}, 0x00, '?string'],
			[function (): array {}, 0x00, 'array'],
			[function (): ?array {}, 0x00, '?array'],
			[function (): callable {}, 0x00, 'callable'],
			[function (): ?callable {}, 0x00, '?callable'],
			[function (): object {}, 0x00, 'object'],
			[function (): ?object {}, 0x00, '?object'],
			[function (): \stdClass {}, 0x00, 'stdClass'],
			[function (): ?\stdClass {}, 0x00, '?stdClass'],
			[function (): \stdClass {}, UCall::TYPE_SHORT_NAME, 'stdClass'],
			[function (): ?\stdClass {}, UCall::TYPE_SHORT_NAME, '?stdClass'],
			[function (): \stdClass {}, UCall::TYPE_NAMESPACE_LEADING_SLASH, '\\stdClass'],
			[function (): ?\stdClass {}, UCall::TYPE_NAMESPACE_LEADING_SLASH, '?\\stdClass'],
			[function (): \stdClass {}, UCall::TYPE_SHORT_NAME | UCall::TYPE_NAMESPACE_LEADING_SLASH, 'stdClass'],
			[function (): ?\stdClass {}, UCall::TYPE_SHORT_NAME | UCall::TYPE_NAMESPACE_LEADING_SLASH,
				'?stdClass'],
			[function (): CallTest_Class {}, 0x00, $class],
			[function (): ?CallTest_Class {}, 0x00, "?{$class}"],
			[function (): CallTest_Class {}, UCall::TYPE_SHORT_NAME, 'CallTest_Class'],
			[function (): ?CallTest_Class {}, UCall::TYPE_SHORT_NAME, '?CallTest_Class'],
			[function (): CallTest_Class {}, UCall::TYPE_NAMESPACE_LEADING_SLASH, "\\{$class}"],
			[function (): ?CallTest_Class {}, UCall::TYPE_NAMESPACE_LEADING_SLASH, "?\\{$class}"],
			[function (): CallTest_Class {}, UCall::TYPE_SHORT_NAME | UCall::TYPE_NAMESPACE_LEADING_SLASH,
				'CallTest_Class'],
			[function (): ?CallTest_Class {}, UCall::TYPE_SHORT_NAME | UCall::TYPE_NAMESPACE_LEADING_SLASH,
				'?CallTest_Class'],
			[function (): CallTest_AbstractClass {}, 0x00, $class_abstract],
			[function (): ?CallTest_AbstractClass {}, 0x00, "?{$class_abstract}"],
			[function (): CallTest_AbstractClass {}, UCall::TYPE_SHORT_NAME, 'CallTest_AbstractClass'],
			[function (): ?CallTest_AbstractClass {}, UCall::TYPE_SHORT_NAME, '?CallTest_AbstractClass'],
			[function (): CallTest_AbstractClass {}, UCall::TYPE_NAMESPACE_LEADING_SLASH, "\\{$class_abstract}"],
			[function (): ?CallTest_AbstractClass {}, UCall::TYPE_NAMESPACE_LEADING_SLASH, "?\\{$class_abstract}"],
			[function (): CallTest_AbstractClass {}, UCall::TYPE_SHORT_NAME | UCall::TYPE_NAMESPACE_LEADING_SLASH,
				'CallTest_AbstractClass'],
			[function (): ?CallTest_AbstractClass {}, UCall::TYPE_SHORT_NAME | UCall::TYPE_NAMESPACE_LEADING_SLASH,
				'?CallTest_AbstractClass'],
			[function (): CallTest_Interface {}, 0x00, $interface],
			[function (): ?CallTest_Interface {}, 0x00, "?{$interface}"],
			[function (): CallTest_Interface {}, UCall::TYPE_SHORT_NAME, 'CallTest_Interface'],
			[function (): ?CallTest_Interface {}, UCall::TYPE_SHORT_NAME, '?CallTest_Interface'],
			[function (): CallTest_Interface {}, UCall::TYPE_NAMESPACE_LEADING_SLASH, "\\{$interface}"],
			[function (): ?CallTest_Interface {}, UCall::TYPE_NAMESPACE_LEADING_SLASH, "?\\{$interface}"],
			[function (): CallTest_Interface {}, UCall::TYPE_SHORT_NAME | UCall::TYPE_NAMESPACE_LEADING_SLASH,
				'CallTest_Interface'],
			[function (): ?CallTest_Interface {}, UCall::TYPE_SHORT_NAME | UCall::TYPE_NAMESPACE_LEADING_SLASH,
				'?CallTest_Interface'],
			[new CallTest_InvokeableClass(), 0x00, "?{$class}"],
			[new CallTest_InvokeableClass(), UCall::TYPE_SHORT_NAME, '?CallTest_Class'],
			[new CallTest_InvokeableClass(), UCall::TYPE_NAMESPACE_LEADING_SLASH, "?\\{$class}"],
			[new CallTest_InvokeableClass(), UCall::TYPE_SHORT_NAME | UCall::TYPE_NAMESPACE_LEADING_SLASH,
				'?CallTest_Class'],
			[new CallTest_InvokeableClass2(), 0x00, 'void'],
			[[$class, 'getString'], 0x00, 'string'],
			[[$class, 'setString'], 0x00, 'void'],
			[[$class, 'getStaticString'], 0x00, 'string'],
			[[$class, 'setStaticString'], 0x00, 'void'],
			[[$class, 'getProtectedInteger'], 0x00, 'int'],
			[[$class, 'setProtectedInteger'], 0x00, 'void'],
			[[$class, 'getProtectedStaticInteger'], 0x00, 'int'],
			[[$class, 'setProtectedStaticInteger'], 0x00, 'void'],
			[[$class, 'getPrivateBoolean'], 0x00, 'bool'],
			[[$class, 'setPrivateBoolean'], 0x00, 'void'],
			[[$class, 'getPrivateStaticBoolean'], 0x00, 'bool'],
			[[$class, 'setPrivateStaticBoolean'], 0x00, 'void'],
			[[$class_abstract, 'getString'], 0x00, 'string'],
			[[$class_abstract, 'setString'], 0x00, 'void'],
			[[$class_abstract, 'getStaticString'], 0x00, 'string'],
			[[$class_abstract, 'setStaticString'], 0x00, 'void'],
			[[$class_abstract, 'getProtectedInteger'], 0x00, 'int'],
			[[$class_abstract, 'setProtectedInteger'], 0x00, 'void'],
			[[$class_abstract, 'getProtectedStaticInteger'], 0x00, 'int'],
			[[$class_abstract, 'setProtectedStaticInteger'], 0x00, 'void'],
			[[$interface, 'getString'], 0x00, 'string'],
			[[$interface, 'setString'], 0x00, 'void'],
			[[$interface, 'getStaticString'], 0x00, 'string'],
			[[$interface, 'setStaticString'], 0x00, 'void'],
			[[$class, 'doStuff'], 0x00, "?{$interface}"],
			[[$class, 'doStuff'], UCall::TYPE_SHORT_NAME, '?CallTest_Interface'],
			[[$class, 'doStuff'], UCall::TYPE_NAMESPACE_LEADING_SLASH, "?\\{$interface}"],
			[[$class, 'doStuff'], UCall::TYPE_SHORT_NAME | UCall::TYPE_NAMESPACE_LEADING_SLASH,
				'?CallTest_Interface']
		];
	}
	
	/**
	 * Test <code>header</code> method.
	 * 
	 * @dataProvider provideHeaderMethodData
	 * @testdox Call::header({$function}, $flags) === '$expected'
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testHeaderMethod($function, int $flags, string $expected): void
	{
		$this->assertSame($expected, UCall::header($function, $flags));
	}
	
	/**
	 * Provide <code>header</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>header</code> method data.</p>
	 */
	public function provideHeaderMethodData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', 0x00, 'function strlen(mixed $str): mixed'],
			['strlen', UCall::HEADER_NO_MIXED_TYPE, 'function strlen($str)'],
			[function () {}, 0x00, 'function (): mixed'],
			[function () {}, UCall::HEADER_NO_MIXED_TYPE, 'function ()'],
			[function (): void {}, 0x00, 'function (): void'],
			[function (): bool {}, 0x00, 'function (): bool'],
			[function (): ?bool {}, 0x00, 'function (): ?bool'],
			[function (): int {}, 0x00, 'function (): int'],
			[function (): ?int {}, 0x00, 'function (): ?int'],
			[function (): float {}, 0x00, 'function (): float'],
			[function (): ?float {}, 0x00, 'function (): ?float'],
			[function (): string {}, 0x00, 'function (): string'],
			[function (): ?string {}, 0x00, 'function (): ?string'],
			[function (): array {}, 0x00, 'function (): array'],
			[function (): ?array {}, 0x00, 'function (): ?array'],
			[function (): callable {}, 0x00, 'function (): callable'],
			[function (): ?callable {}, 0x00, 'function (): ?callable'],
			[function (): object {}, 0x00, 'function (): object'],
			[function (): ?object {}, 0x00, 'function (): ?object'],
			[function (): \stdClass {}, 0x00, 'function (): stdClass'],
			[function (): ?\stdClass {}, 0x00, 'function (): ?stdClass'],
			[function (): \stdClass {}, UCall::HEADER_TYPES_SHORT_NAMES, 'function (): stdClass'],
			[function (): ?\stdClass {}, UCall::HEADER_TYPES_SHORT_NAMES, 'function (): ?stdClass'],
			[function (): \stdClass {}, UCall::HEADER_NAMESPACES_LEADING_SLASH, 'function (): \\stdClass'],
			[function (): ?\stdClass {}, UCall::HEADER_NAMESPACES_LEADING_SLASH, 'function (): ?\\stdClass'],
			[function (): \stdClass {}, UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (): stdClass'],
			[function (): ?\stdClass {}, UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (): ?stdClass'],
			[function (): CallTest_Class {}, 0x00, 'function (): ' . $class],
			[function (): ?CallTest_Class {}, 0x00, 'function (): ?' . $class],
			[function (): CallTest_Class {}, UCall::HEADER_TYPES_SHORT_NAMES, 'function (): CallTest_Class'],
			[function (): ?CallTest_Class {}, UCall::HEADER_TYPES_SHORT_NAMES, 'function (): ?CallTest_Class'],
			[function (): CallTest_Class {}, UCall::HEADER_NAMESPACES_LEADING_SLASH, 'function (): \\' . $class],
			[function (): ?CallTest_Class {}, UCall::HEADER_NAMESPACES_LEADING_SLASH, 'function (): ?\\' . $class],
			[function (): CallTest_Class {}, UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (): CallTest_Class'],
			[function (): ?CallTest_Class {}, UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (): ?CallTest_Class'],
			[function (): CallTest_AbstractClass {}, 0x00, 'function (): ' . $class_abstract],
			[function (): ?CallTest_AbstractClass {}, 0x00, 'function (): ?' . $class_abstract],
			[function (): CallTest_AbstractClass {}, UCall::HEADER_TYPES_SHORT_NAMES,
				'function (): CallTest_AbstractClass'],
			[function (): ?CallTest_AbstractClass {}, UCall::HEADER_TYPES_SHORT_NAMES,
				'function (): ?CallTest_AbstractClass'],
			[function (): CallTest_AbstractClass {}, UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (): \\' . $class_abstract],
			[function (): ?CallTest_AbstractClass {}, UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (): ?\\' . $class_abstract],
			[function (): CallTest_AbstractClass {},
				UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (): CallTest_AbstractClass'],
			[function (): ?CallTest_AbstractClass {},
				UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (): ?CallTest_AbstractClass'],
			[function (): CallTest_Interface {}, 0x00, 'function (): ' . $interface],
			[function (): ?CallTest_Interface {}, 0x00, 'function (): ?' . $interface],
			[function (): CallTest_Interface {}, UCall::HEADER_TYPES_SHORT_NAMES, 'function (): CallTest_Interface'],
			[function (): ?CallTest_Interface {}, UCall::HEADER_TYPES_SHORT_NAMES, 'function (): ?CallTest_Interface'],
			[function (): CallTest_Interface {}, UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (): \\' . $interface],
			[function (): ?CallTest_Interface {}, UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (): ?\\' . $interface],
			[function (): CallTest_Interface {},
				UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (): CallTest_Interface'],
			[function (): ?CallTest_Interface {},
				UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (): ?CallTest_Interface'],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null): void {}, 0x00,
				'function (' . $class . ' $a, stdClass $b, bool $e = false, mixed $k = null): void'],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null): void {},
				UCall::HEADER_TYPES_SHORT_NAMES,
				'function (CallTest_Class $a, stdClass $b, bool $e = false, mixed $k = null): void'],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null) {},
				UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (\\' . $class . ' $a, \\stdClass $b, bool $e = false, mixed $k = null): mixed'],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null) {}, UCall::HEADER_NO_MIXED_TYPE,
				'function (' . $class . ' $a, stdClass $b, bool $e = false, $k = null)'],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null): CallTest_AbstractClass {},
				UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (CallTest_Class $a, stdClass $b, bool $e = false, mixed $k = null): CallTest_AbstractClass'],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null): CallTest_AbstractClass {},
				UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NO_MIXED_TYPE,
				'function (CallTest_Class $a, stdClass $b, bool $e = false, $k = null): CallTest_AbstractClass'],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null): CallTest_AbstractClass {},
				UCall::HEADER_NAMESPACES_LEADING_SLASH | UCall::HEADER_NO_MIXED_TYPE,
				'function (\\' . $class . ' $a, \\stdClass $b, bool $e = false, $k = null): \\' . $class_abstract],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null): int {},
				UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH | 
				UCall::HEADER_NO_MIXED_TYPE,
				'function (CallTest_Class $a, stdClass $b, bool $e = false, $k = null): int'],
			[function (CallTest_AbstractClass $ac, ?CallTest_Interface $i): string {}, 0x00,
				'function (' . $class_abstract . ' $ac, ?' . $interface . ' $i): string'],
			[function (CallTest_AbstractClass $ac, ?CallTest_Interface $i) {}, UCall::HEADER_TYPES_SHORT_NAMES,
				'function (CallTest_AbstractClass $ac, ?CallTest_Interface $i): mixed'],
			[function (CallTest_AbstractClass $ac, ?CallTest_Interface $i): ?\stdClass {},
				UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (\\' . $class_abstract . ' $ac, ?\\' . $interface . ' $i): ?\\stdClass'],
			[function (CallTest_AbstractClass $ac, ?CallTest_Interface $i): ?callable {},
				UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (CallTest_AbstractClass $ac, ?CallTest_Interface $i): ?callable'],
			[new CallTest_InvokeableClass(), 0x00, 'public function __invoke(): ?' . $class],
			[new CallTest_InvokeableClass(), UCall::HEADER_TYPES_SHORT_NAMES,
				'public function __invoke(): ?CallTest_Class'],
			[new CallTest_InvokeableClass(), UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'public function __invoke(): ?\\' . $class],
			[new CallTest_InvokeableClass(), UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'public function __invoke(): ?CallTest_Class'],
			[new CallTest_InvokeableClass2(), 0x00,
				'public function __invoke(string $s_foo = ' . CallTest_InvokeableClass2::class . 
				'::FOO_CONSTANT): void'],
			[new CallTest_InvokeableClass2(), UCall::HEADER_CONSTANTS_VALUES,
				'public function __invoke(string $s_foo = "bar2foo"): void'],
			[new CallTest_InvokeableClass2(), UCall::HEADER_TYPES_SHORT_NAMES,
				'public function __invoke(string $s_foo = CallTest_InvokeableClass2::FOO_CONSTANT): void'],
			[new CallTest_InvokeableClass2(), UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'public function __invoke(string $s_foo = \\' . CallTest_InvokeableClass2::class . 
				'::FOO_CONSTANT): void'],
			[new CallTest_InvokeableClass2(),
				UCall::HEADER_CONSTANTS_VALUES | UCall::HEADER_TYPES_SHORT_NAMES,
				'public function __invoke(string $s_foo = "bar2foo"): void'],
			[new CallTest_InvokeableClass2(),
				UCall::HEADER_CONSTANTS_VALUES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'public function __invoke(string $s_foo = "bar2foo"): void'],
			[new CallTest_InvokeableClass2(),
				UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'public function __invoke(string $s_foo = CallTest_InvokeableClass2::FOO_CONSTANT): void'],
			[new CallTest_InvokeableClass2(),
				UCall::HEADER_CONSTANTS_VALUES | UCall::HEADER_TYPES_SHORT_NAMES | 
				UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'public function __invoke(string $s_foo = "bar2foo"): void'],
			[[$class, 'getString'], 0x00, 'public function getString(): string'],
			[[$class, 'setString'], 0x00, 'public function setString(string $string): void'],
			[[$class, 'getStaticString'], 0x00, 'public static function getStaticString(): string'],
			[[$class, 'setStaticString'], 0x00, 'public static function setStaticString(string $string): void'],
			[[$class, 'getProtectedInteger'], 0x00, 'protected function getProtectedInteger(): int'],
			[[$class, 'setProtectedInteger'], 0x00, 'protected function setProtectedInteger(int $integer): void'],
			[[$class, 'getProtectedStaticInteger'], 0x00, 'protected static function getProtectedStaticInteger(): int'],
			[[$class, 'setProtectedStaticInteger'], 0x00,
				'protected static function setProtectedStaticInteger(int $integer): void'],
			[[$class, 'getPrivateBoolean'], 0x00, 'private function getPrivateBoolean(): bool'],
			[[$class, 'setPrivateBoolean'], 0x00, 'private function setPrivateBoolean(bool $boolean): void'],
			[[$class, 'getPrivateStaticBoolean'], 0x00, 'private static function getPrivateStaticBoolean(): bool'],
			[[$class, 'setPrivateStaticBoolean'], 0x00,
				'private static function setPrivateStaticBoolean(bool $boolean): void'],
			[[$class, 'getFinalString'], 0x00, 'final public function getFinalString(): string'],
			[[$class, 'setFinalString'], 0x00, 'final public function setFinalString(string $string): void'],
			[[$class, 'getFinalStaticString'], 0x00, 'final public static function getFinalStaticString(): string'],
			[[$class, 'setFinalStaticString'], 0x00,
				'final public static function setFinalStaticString(string $string): void'],
			[[$class, 'getFinalProtectedInteger'], 0x00, 'final protected function getFinalProtectedInteger(): int'],
			[[$class, 'setFinalProtectedInteger'], 0x00,
				'final protected function setFinalProtectedInteger(int $integer): void'],
			[[$class, 'getFinalProtectedStaticInteger'], 0x00,
				'final protected static function getFinalProtectedStaticInteger(): int'],
			[[$class, 'setFinalProtectedStaticInteger'], 0x00,
				'final protected static function setFinalProtectedStaticInteger(int $integer): void'],
			[[$class, 'getFinalPrivateBoolean'], 0x00, 'final private function getFinalPrivateBoolean(): bool'],
			[[$class, 'setFinalPrivateBoolean'], 0x00,
				'final private function setFinalPrivateBoolean(bool $boolean): void'],
			[[$class, 'getFinalPrivateStaticBoolean'], 0x00,
				'final private static function getFinalPrivateStaticBoolean(): bool'],
			[[$class, 'setFinalPrivateStaticBoolean'], 0x00,
				'final private static function setFinalPrivateStaticBoolean(bool $boolean): void'],
			[[$class_abstract, 'getString'], 0x00, 'abstract public function getString(): string'],
			[[$class_abstract, 'setString'], 0x00, 'abstract public function setString(string $string): void'],
			[[$class_abstract, 'getStaticString'], 0x00, 'abstract public static function getStaticString(): string'],
			[[$class_abstract, 'setStaticString'], 0x00,
				'abstract public static function setStaticString(string $string): void'],
			[[$class_abstract, 'getProtectedInteger'], 0x00, 'abstract protected function getProtectedInteger(): int'],
			[[$class_abstract, 'setProtectedInteger'], 0x00,
				'abstract protected function setProtectedInteger(int $integer): void'],
			[[$class_abstract, 'getProtectedStaticInteger'], 0x00,
				'abstract protected static function getProtectedStaticInteger(): int'],
			[[$class_abstract, 'setProtectedStaticInteger'], 0x00,
				'abstract protected static function setProtectedStaticInteger(int $integer): void'],
			[[$class_abstract, 'getFinalString'], 0x00, 'final public function getFinalString(): string'],
			[[$class_abstract, 'setFinalString'], 0x00, 'final public function setFinalString(string $string): void'],
			[[$class_abstract, 'getFinalStaticString'], 0x00,
				'final public static function getFinalStaticString(): string'],
			[[$class_abstract, 'setFinalStaticString'], 0x00,
				'final public static function setFinalStaticString(string $string): void'],
			[[$class_abstract, 'getFinalProtectedInteger'], 0x00,
				'final protected function getFinalProtectedInteger(): int'],
			[[$class_abstract, 'setFinalProtectedInteger'], 0x00,
				'final protected function setFinalProtectedInteger(int $integer): void'],
			[[$class_abstract, 'getFinalProtectedStaticInteger'], 0x00,
				'final protected static function getFinalProtectedStaticInteger(): int'],
			[[$class_abstract, 'setFinalProtectedStaticInteger'], 0x00,
				'final protected static function setFinalProtectedStaticInteger(int $integer): void'],
			[[$class_abstract, 'getFinalPrivateBoolean'], 0x00,
				'final private function getFinalPrivateBoolean(): bool'],
			[[$class_abstract, 'setFinalPrivateBoolean'], 0x00,
				'final private function setFinalPrivateBoolean(bool $boolean): void'],
			[[$class_abstract, 'getFinalPrivateStaticBoolean'], 0x00,
				'final private static function getFinalPrivateStaticBoolean(): bool'],
			[[$class_abstract, 'setFinalPrivateStaticBoolean'], 0x00,
				'final private static function setFinalPrivateStaticBoolean(bool $boolean): void'],
			[[$interface, 'getString'], 0x00, 'abstract public function getString(): string'],
			[[$interface, 'setString'], 0x00, 'abstract public function setString(string $string): void'],
			[[$interface, 'getStaticString'], 0x00, 'abstract public static function getStaticString(): string'],
			[[$interface, 'setStaticString'], 0x00,
				'abstract public static function setStaticString(string $string): void'],
			[[$class, 'doStuff'], 0x00,
				'public function doStuff(?float $fnumber, ' . $class_abstract . ' $ac, ?' . $class . ' &$c, ' . 
				'mixed $options, callable $c_function, string $farboo = ' . $class . '::A_S, array $foob = ' . $class . 
				'::B_ARRAY, int $cint = ' . $class . '::C_CONSTANT, bool &$enable = ' . $class . '::D_ENABLE, ' . 
				'?stdClass $std = null, mixed $flags = SORT_STRING, mixed ...$p): ?' . $interface],
			[[$class, 'doStuff'], UCall::HEADER_CONSTANTS_VALUES,
				'public function doStuff(?float $fnumber, ' . $class_abstract . ' $ac, ?' . $class . ' &$c, ' . 
				'mixed $options, callable $c_function, string $farboo = "Aaa", ' . 
				'array $foob = ["foo"=>false,"bar"=>null], int $cint = 1200, bool &$enable = true, ' . 
				'?stdClass $std = null, mixed $flags = 2, mixed ...$p): ?' . $interface],
			[[$class, 'doStuff'], UCall::HEADER_TYPES_SHORT_NAMES,
				'public function doStuff(?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, ' . 
				'mixed $options, callable $c_function, string $farboo = CallTest_Class::A_S, ' . 
				'array $foob = CallTest_Class::B_ARRAY, int $cint = CallTest_Class::C_CONSTANT, ' . 
				'bool &$enable = CallTest_Class::D_ENABLE, ?stdClass $std = null, mixed $flags = SORT_STRING, ' . 
				'mixed ...$p): ?CallTest_Interface'],
			[[$class, 'doStuff'], UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'public function doStuff(?float $fnumber, \\' . $class_abstract . ' $ac, ?\\' . $class . ' &$c, ' . 
				'mixed $options, callable $c_function, string $farboo = \\' . $class . '::A_S, ' . 
				'array $foob = \\' . $class . '::B_ARRAY, int $cint = \\' . $class . '::C_CONSTANT, ' . 
				'bool &$enable = \\' . $class . '::D_ENABLE, ?\\stdClass $std = null, mixed $flags = \\SORT_STRING, ' . 
				'mixed ...$p): ?\\' . $interface],
			[[$class, 'doStuff'], UCall::HEADER_NO_MIXED_TYPE,
				'public function doStuff(?float $fnumber, ' . $class_abstract . ' $ac, ?' . $class . ' &$c, ' . 
				'$options, callable $c_function, string $farboo = ' . $class . '::A_S, ' . 
				'array $foob = ' . $class . '::B_ARRAY, int $cint = ' . $class . '::C_CONSTANT, ' . 
				'bool &$enable = ' . $class . '::D_ENABLE, ?stdClass $std = null, $flags = SORT_STRING, ' . 
				'...$p): ?' . $interface],
			[[$class, 'doStuff'], UCall::HEADER_CONSTANTS_VALUES | UCall::HEADER_TYPES_SHORT_NAMES,
				'public function doStuff(?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, ' . 
				'mixed $options, callable $c_function, string $farboo = "Aaa", ' . 
				'array $foob = ["foo"=>false,"bar"=>null], int $cint = 1200, bool &$enable = true, ' . 
				'?stdClass $std = null, mixed $flags = 2, mixed ...$p): ?CallTest_Interface'],
			[[$class, 'doStuff'], UCall::HEADER_CONSTANTS_VALUES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'public function doStuff(?float $fnumber, \\' . $class_abstract . ' $ac, ?\\' . $class . ' &$c, ' . 
				'mixed $options, callable $c_function, string $farboo = "Aaa", ' . 
				'array $foob = ["foo"=>false,"bar"=>null], int $cint = 1200, bool &$enable = true, ' . 
				'?\\stdClass $std = null, mixed $flags = 2, mixed ...$p): ?\\' . $interface],
			[[$class, 'doStuff'], UCall::HEADER_CONSTANTS_VALUES | UCall::HEADER_NO_MIXED_TYPE,
				'public function doStuff(?float $fnumber, ' . $class_abstract . ' $ac, ?' . $class . ' &$c, ' . 
				'$options, callable $c_function, string $farboo = "Aaa", array $foob = ["foo"=>false,"bar"=>null], ' . 
				'int $cint = 1200, bool &$enable = true, ?stdClass $std = null, $flags = 2, ...$p): ?' . $interface],
			[[$class, 'doStuff'], UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'public function doStuff(?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, ' . 
				'mixed $options, callable $c_function, string $farboo = CallTest_Class::A_S, ' . 
				'array $foob = CallTest_Class::B_ARRAY, int $cint = CallTest_Class::C_CONSTANT, ' . 
				'bool &$enable = CallTest_Class::D_ENABLE, ?stdClass $std = null, mixed $flags = \\SORT_STRING, ' . 
				'mixed ...$p): ?CallTest_Interface'],
			[[$class, 'doStuff'], UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NO_MIXED_TYPE,
				'public function doStuff(?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, ' . 
				'$options, callable $c_function, string $farboo = CallTest_Class::A_S, ' . 
				'array $foob = CallTest_Class::B_ARRAY, int $cint = CallTest_Class::C_CONSTANT, ' . 
				'bool &$enable = CallTest_Class::D_ENABLE, ?stdClass $std = null, $flags = SORT_STRING, ' . 
				'...$p): ?CallTest_Interface'],
			[[$class, 'doStuff'], UCall::HEADER_NAMESPACES_LEADING_SLASH | UCall::HEADER_NO_MIXED_TYPE,
				'public function doStuff(?float $fnumber, \\' . $class_abstract . ' $ac, ?\\' . $class . ' &$c, ' . 
				'$options, callable $c_function, string $farboo = \\' . $class . '::A_S, ' . 
				'array $foob = \\' . $class . '::B_ARRAY, int $cint = \\' . $class . '::C_CONSTANT, ' . 
				'bool &$enable = \\' . $class . '::D_ENABLE, ?\\stdClass $std = null, $flags = \\SORT_STRING, ' . 
				'...$p): ?\\' . $interface],
			[[$class, 'doStuff'],
				UCall::HEADER_CONSTANTS_VALUES | UCall::HEADER_TYPES_SHORT_NAMES | 
				UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'public function doStuff(?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, ' . 
				'mixed $options, callable $c_function, string $farboo = "Aaa", ' . 
				'array $foob = ["foo"=>false,"bar"=>null], int $cint = 1200, bool &$enable = true, ' . 
				'?stdClass $std = null, mixed $flags = 2, mixed ...$p): ?CallTest_Interface'],
			[[$class, 'doStuff'],
				UCall::HEADER_CONSTANTS_VALUES | UCall::HEADER_TYPES_SHORT_NAMES | 
				UCall::HEADER_NO_MIXED_TYPE,
				'public function doStuff(?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, ' . 
				'$options, callable $c_function, string $farboo = "Aaa", array $foob = ["foo"=>false,"bar"=>null], ' . 
				'int $cint = 1200, bool &$enable = true, ?stdClass $std = null, $flags = 2, ' . 
				'...$p): ?CallTest_Interface'],
			[[$class, 'doStuff'],
				UCall::HEADER_CONSTANTS_VALUES | UCall::HEADER_NAMESPACES_LEADING_SLASH | 
				UCall::HEADER_NO_MIXED_TYPE,
				'public function doStuff(?float $fnumber, \\' . $class_abstract . ' $ac, ?\\' . $class . ' &$c, ' . 
				'$options, callable $c_function, string $farboo = "Aaa", array $foob = ["foo"=>false,"bar"=>null], ' . 
				'int $cint = 1200, bool &$enable = true, ?\\stdClass $std = null, $flags = 2, ' . 
				'...$p): ?\\' . $interface],
			[[$class, 'doStuff'],
				UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH | 
				UCall::HEADER_NO_MIXED_TYPE,
				'public function doStuff(?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, ' . 
				'$options, callable $c_function, string $farboo = CallTest_Class::A_S, ' . 
				'array $foob = CallTest_Class::B_ARRAY, int $cint = CallTest_Class::C_CONSTANT, ' . 
				'bool &$enable = CallTest_Class::D_ENABLE, ?stdClass $std = null, $flags = \\SORT_STRING, ' . 
				'...$p): ?CallTest_Interface'],
			[[$class, 'doStuff'],
				UCall::HEADER_CONSTANTS_VALUES | UCall::HEADER_TYPES_SHORT_NAMES | 
				UCall::HEADER_NAMESPACES_LEADING_SLASH | UCall::HEADER_NO_MIXED_TYPE,
				'public function doStuff(?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, ' . 
				'$options, callable $c_function, string $farboo = "Aaa", array $foob = ["foo"=>false,"bar"=>null], ' . 
				'int $cint = 1200, bool &$enable = true, ?stdClass $std = null, $flags = 2, ' . 
				'...$p): ?CallTest_Interface']
		];
	}
	
	/**
	 * Test <code>body</code> method.
	 * 
	 * @dataProvider provideBodyMethodData
	 * @testdox Call::body({$function}) === '$expected'
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testBodyMethod($function, string $expected): void
	{
		$this->assertSame($expected, UCall::body($function));
	}
	
	/**
	 * Provide <code>body</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>body</code> method data.</p>
	 */
	public function provideBodyMethodData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', ''],
			[function () {}, ''],
			[function () {return 'foo2bar';}, "return 'foo2bar';"],
			[
				function () {
					return "foo2bar";
				},
				"return \"foo2bar\";"
			], [
				function (int $i) {
					if ($i > 2) {
						return $i + 1;
					}
					return null;
				},
				"if (\$i > 2) {\n\treturn \$i + 1;\n}\nreturn null;"
			],
			[new CallTest_InvokeableClass(), ''],
			[new CallTest_InvokeableClass2(),
				"//condition\nif (strlen(\$s_foo) > 45) {\n\t\$s_foo = substr(\$s_foo, 0, 45);\n}"],
			[[$class, 'getString'], "return '';"],
			[[$class, 'setString'], ''],
			[[$class, 'getStaticString'], "return '';"],
			[[$class, 'setStaticString'], ''],
			[[$class, 'getProtectedInteger'], "return 0;"],
			[[$class, 'setProtectedInteger'], ''],
			[[$class, 'getProtectedStaticInteger'], "return 0;"],
			[[$class, 'setProtectedStaticInteger'], ''],
			[[$class, 'getPrivateBoolean'], "return false;"],
			[[$class, 'setPrivateBoolean'], ''],
			[[$class, 'getPrivateStaticBoolean'], "return false;"],
			[[$class, 'setPrivateStaticBoolean'], ''],
			[[$class_abstract, 'getString'], ''],
			[[$class_abstract, 'setString'], ''],
			[[$class_abstract, 'getStaticString'], ''],
			[[$class_abstract, 'setStaticString'], ''],
			[[$class_abstract, 'getProtectedInteger'], ''],
			[[$class_abstract, 'setProtectedInteger'], ''],
			[[$class_abstract, 'getProtectedStaticInteger'], ''],
			[[$class_abstract, 'setProtectedStaticInteger'], ''],
			[[$interface, 'getString'], ''],
			[[$interface, 'setString'], ''],
			[[$interface, 'getStaticString'], ''],
			[[$interface, 'setStaticString'], ''],
			[[$class, 'doStuff'],
				"//iterate\nforeach (\$foob as \$foo) {\n\t\$fnumber *= \$foo;\n}\n\n" . 
				"//do something\n\$farboo = \"{\$fnumber}{\$cint}\";\nif (\$enable) {\n" . 
				"\tif (UCall::object(\$c_function) === null) {\n\t\t\$enable = false;\n" . 
				"\t} elseif (\$flags & SORT_STRING) {\n\t\t\$std->barobj = \$ac;\n\t\t\$c = \$ac;\n" . 
				"\t}\n}\n\n" . 
				"//return\nreturn new class implements CallTest_Interface\n{\n" . 
				"\tpublic function getString(): string {}\n\tpublic function setString(string \$string): void {}\n" . 
				"\tpublic static function getStaticString(): string {}\n" . 
				"\tpublic static function setStaticString(string \$string): void {}\n};"
			]
		];
	}
	
	/**
	 * Test <code>source</code> method.
	 * 
	 * @dataProvider provideSourceMethodData
	 * @testdox Call::source({$function}, $flags) === '$expected'
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testSourceMethod($function, int $flags, string $expected): void
	{
		$this->assertSame($expected, UCall::source($function, $flags));
	}
	
	/**
	 * Provide <code>source</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>source</code> method data.</p>
	 */
	public function provideSourceMethodData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		$invoke_body = "\n{\n" . 
			"\t//condition\n\tif (strlen(\$s_foo) > 45) {\n\t\t\$s_foo = substr(\$s_foo, 0, 45);\n\t}" . 
			"\n}";
		$string_body = "\n{\n\treturn '';\n}";
		$integer_body = "\n{\n\treturn 0;\n}";
		$boolean_body = "\n{\n\treturn false;\n}";
		$dostuff_body = "\n{\n" . 
			"\t//iterate\n\tforeach (\$foob as \$foo) {\n\t\t\$fnumber *= \$foo;\n\t}\n\t\n" . 
			"\t//do something\n\t\$farboo = \"{\$fnumber}{\$cint}\";\n\tif (\$enable) {\n" . 
			"\t\tif (UCall::object(\$c_function) === null) {\n\t\t\t\$enable = false;\n" . 
			"\t\t} elseif (\$flags & SORT_STRING) {\n\t\t\t\$std->barobj = \$ac;\n\t\t\t\$c = \$ac;\n" . 
			"\t\t}\n\t}\n\t\n" . 
			"\t//return\n\treturn new class implements CallTest_Interface\n\t{\n" . 
			"\t\tpublic function getString(): string {}\n\t\tpublic function setString(string \$string): void {}\n" . 
			"\t\tpublic static function getStaticString(): string {}\n" . 
			"\t\tpublic static function setStaticString(string \$string): void {}\n\t};" . 
			"\n}";
		
		//return
		return [
			['strlen', 0x00, 'function strlen(mixed $str): mixed {}'],
			['strlen', UCall::SOURCE_NO_MIXED_TYPE, 'function strlen($str) {}'],
			[function () {}, 0x00, 'function (): mixed {}'],
			[function () {}, UCall::SOURCE_NO_MIXED_TYPE, 'function () {}'],
			[function () {return 'foo2bar';}, 0x00, "function (): mixed\n{\n\treturn 'foo2bar';\n}"],
			[function () {return 'foo2bar';}, UCall::SOURCE_NO_MIXED_TYPE, "function ()\n{\n\treturn 'foo2bar';\n}"],
			[
				function () {
					return "foo2bar";
				},
				0x00,
				"function (): mixed\n{\n\treturn \"foo2bar\";\n}"
			], [
				function () {
					return "foo2bar";
				},
				UCall::SOURCE_NO_MIXED_TYPE,
				"function ()\n{\n\treturn \"foo2bar\";\n}"
			], [
				function ($i) {
					if ($i > 2) {
						return $i + 1;
					}
					return null;
				},
				0x00,
				"function (mixed \$i): mixed\n{\n\tif (\$i > 2) {\n\t\treturn \$i + 1;\n\t}\n\treturn null;\n}"
			], [
				function ($i) {
					if ($i > 2) {
						return $i + 1;
					}
					return null;
				},
				UCall::SOURCE_NO_MIXED_TYPE,
				"function (\$i)\n{\n\tif (\$i > 2) {\n\t\treturn \$i + 1;\n\t}\n\treturn null;\n}"
			],
			[function (): void {}, 0x00, 'function (): void {}'],
			[function (): bool {}, 0x00, 'function (): bool {}'],
			[function (int $i): ?bool {}, 0x00, 'function (int $i): ?bool {}'],
			[function (bool $b, ?string $s): int {}, 0x00, 'function (bool $b, ?string $s): int {}'],
			[function (): ?int {}, 0x00, 'function (): ?int {}'],
			[function (&$ref): float {}, 0x00, 'function (mixed &$ref): float {}'],
			[function (array $array, int $n = 12): ?float {}, 0x00, 'function (array $array, int $n = 12): ?float {}'],
			[function (callable $c, int &$ii = 739): string {}, 0x00,
				'function (callable $c, int &$ii = 739): string {}'],
			[function (?object $obj = null): ?string {}, 0x00, 'function (?object $obj = null): ?string {}'],
			[function (): \stdClass {}, 0x00, 'function (): stdClass {}'],
			[function (): ?\stdClass {}, 0x00, 'function (): ?stdClass {}'],
			[function (): \stdClass {}, UCall::SOURCE_TYPES_SHORT_NAMES, 'function (): stdClass {}'],
			[function (): ?\stdClass {}, UCall::SOURCE_TYPES_SHORT_NAMES, 'function (): ?stdClass {}'],
			[function (): \stdClass {}, UCall::SOURCE_NAMESPACES_LEADING_SLASH, 'function (): \\stdClass {}'],
			[function (): ?\stdClass {}, UCall::SOURCE_NAMESPACES_LEADING_SLASH, 'function (): ?\\stdClass {}'],
			[function (): \stdClass {}, UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (): stdClass {}'],
			[function (): ?\stdClass {}, UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (): ?stdClass {}'],
			[function (): CallTest_Class {}, 0x00, 'function (): ' . $class . ' {}'],
			[function (): ?CallTest_Class {}, 0x00, 'function (): ?' . $class . ' {}'],
			[function (): CallTest_Class {}, UCall::SOURCE_TYPES_SHORT_NAMES, 'function (): CallTest_Class {}'],
			[function (): ?CallTest_Class {}, UCall::SOURCE_TYPES_SHORT_NAMES, 'function (): ?CallTest_Class {}'],
			[function (): CallTest_Class {}, UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (): \\' . $class . ' {}'],
			[function (): ?CallTest_Class {}, UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (): ?\\' . $class . ' {}'],
			[function (): CallTest_Class {}, UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (): CallTest_Class {}'],
			[function (): ?CallTest_Class {}, UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (): ?CallTest_Class {}'],
			[function (): CallTest_AbstractClass {}, 0x00, 'function (): ' . $class_abstract . ' {}'],
			[function (): ?CallTest_AbstractClass {}, 0x00, 'function (): ?' . $class_abstract . ' {}'],
			[function (): CallTest_AbstractClass {}, UCall::SOURCE_TYPES_SHORT_NAMES,
				'function (): CallTest_AbstractClass {}'],
			[function (): ?CallTest_AbstractClass {}, UCall::SOURCE_TYPES_SHORT_NAMES,
				'function (): ?CallTest_AbstractClass {}'],
			[function (): CallTest_AbstractClass {}, UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (): \\' . $class_abstract . ' {}'],
			[function (): ?CallTest_AbstractClass {}, UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (): ?\\' . $class_abstract . ' {}'],
			[function (): CallTest_AbstractClass {},
				UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (): CallTest_AbstractClass {}'],
			[function (): ?CallTest_AbstractClass {},
				UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (): ?CallTest_AbstractClass {}'],
			[function (): CallTest_Interface {}, 0x00, 'function (): ' . $interface . ' {}'],
			[function (): ?CallTest_Interface {}, 0x00, 'function (): ?' . $interface . ' {}'],
			[function (): CallTest_Interface {}, UCall::SOURCE_TYPES_SHORT_NAMES, 'function (): CallTest_Interface {}'],
			[function (): ?CallTest_Interface {}, UCall::SOURCE_TYPES_SHORT_NAMES,
				'function (): ?CallTest_Interface {}'],
			[function (): CallTest_Interface {}, UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (): \\' . $interface . ' {}'],
			[function (): ?CallTest_Interface {}, UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (): ?\\' . $interface . ' {}'],
			[function (): CallTest_Interface {},
				UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (): CallTest_Interface {}'],
			[function (): ?CallTest_Interface {},
				UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (): ?CallTest_Interface {}'],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null): void {}, 0x00,
				'function (' . $class . ' $a, stdClass $b, bool $e = false, mixed $k = null): void {}'],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null): void {},
				UCall::SOURCE_TYPES_SHORT_NAMES,
				'function (CallTest_Class $a, stdClass $b, bool $e = false, mixed $k = null): void {}'],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null) {},
				UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (\\' . $class . ' $a, \\stdClass $b, bool $e = false, mixed $k = null): mixed {}'],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null) {}, UCall::SOURCE_NO_MIXED_TYPE,
				'function (' . $class . ' $a, stdClass $b, bool $e = false, $k = null) {}'],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null): CallTest_AbstractClass {},
				UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (CallTest_Class $a, stdClass $b, bool $e = false, mixed $k = null): ' . 
				'CallTest_AbstractClass {}'],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null): CallTest_AbstractClass {},
				UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NO_MIXED_TYPE,
				'function (CallTest_Class $a, stdClass $b, bool $e = false, $k = null): CallTest_AbstractClass {}'],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null): CallTest_AbstractClass {},
				UCall::SOURCE_NAMESPACES_LEADING_SLASH | UCall::SOURCE_NO_MIXED_TYPE,
				'function (\\' . $class . ' $a, \\stdClass $b, bool $e = false, $k = null): ' . 
				'\\' . $class_abstract . ' {}'],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null): int {},
				UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH | 
				UCall::SOURCE_NO_MIXED_TYPE,
				'function (CallTest_Class $a, stdClass $b, bool $e = false, $k = null): int {}'],
			[function (CallTest_AbstractClass $ac, ?CallTest_Interface $i): string {}, 0x00,
				'function (' . $class_abstract . ' $ac, ?' . $interface . ' $i): string {}'],
			[function (CallTest_AbstractClass $ac, ?CallTest_Interface $i) {}, UCall::SOURCE_TYPES_SHORT_NAMES,
				'function (CallTest_AbstractClass $ac, ?CallTest_Interface $i): mixed {}'],
			[function (CallTest_AbstractClass $ac, ?CallTest_Interface $i): ?\stdClass {},
				UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (\\' . $class_abstract . ' $ac, ?\\' . $interface . ' $i): ?\\stdClass {}'],
			[function (CallTest_AbstractClass $ac, ?CallTest_Interface $i): ?callable {},
				UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (CallTest_AbstractClass $ac, ?CallTest_Interface $i): ?callable {}'],
			[new CallTest_InvokeableClass(), 0x00, 'public function __invoke(): ?' . $class . ' {}'],
			[new CallTest_InvokeableClass(), UCall::SOURCE_TYPES_SHORT_NAMES,
				'public function __invoke(): ?CallTest_Class {}'],
			[new CallTest_InvokeableClass(), UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'public function __invoke(): ?\\' . $class . ' {}'],
			[new CallTest_InvokeableClass(), UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'public function __invoke(): ?CallTest_Class {}'],
			[new CallTest_InvokeableClass2(), 0x00,
				'public function __invoke(string $s_foo = ' . CallTest_InvokeableClass2::class . 
				'::FOO_CONSTANT): void' . $invoke_body],
			[new CallTest_InvokeableClass2(), UCall::SOURCE_CONSTANTS_VALUES,
				'public function __invoke(string $s_foo = "bar2foo"): void' . $invoke_body],
			[new CallTest_InvokeableClass2(), UCall::SOURCE_TYPES_SHORT_NAMES,
				'public function __invoke(string $s_foo = CallTest_InvokeableClass2::FOO_CONSTANT): void' . 
				$invoke_body],
			[new CallTest_InvokeableClass2(), UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'public function __invoke(string $s_foo = \\' . CallTest_InvokeableClass2::class . 
				'::FOO_CONSTANT): void' . $invoke_body],
			[new CallTest_InvokeableClass2(),
				UCall::SOURCE_CONSTANTS_VALUES | UCall::SOURCE_TYPES_SHORT_NAMES,
				'public function __invoke(string $s_foo = "bar2foo"): void' . $invoke_body],
			[new CallTest_InvokeableClass2(),
				UCall::SOURCE_CONSTANTS_VALUES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'public function __invoke(string $s_foo = "bar2foo"): void' . $invoke_body],
			[new CallTest_InvokeableClass2(),
				UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'public function __invoke(string $s_foo = CallTest_InvokeableClass2::FOO_CONSTANT): void' . 
				$invoke_body],
			[new CallTest_InvokeableClass2(),
				UCall::SOURCE_CONSTANTS_VALUES | UCall::SOURCE_TYPES_SHORT_NAMES | 
				UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'public function __invoke(string $s_foo = "bar2foo"): void' . $invoke_body],
			[[$class, 'getString'], 0x00, 'public function getString(): string' . $string_body],
			[[$class, 'setString'], 0x00, 'public function setString(string $string): void {}'],
			[[$class, 'getStaticString'], 0x00, 'public static function getStaticString(): string' . $string_body],
			[[$class, 'setStaticString'], 0x00, 'public static function setStaticString(string $string): void {}'],
			[[$class, 'getProtectedInteger'], 0x00, 'protected function getProtectedInteger(): int' . $integer_body],
			[[$class, 'setProtectedInteger'], 0x00, 'protected function setProtectedInteger(int $integer): void {}'],
			[[$class, 'getProtectedStaticInteger'], 0x00,
				'protected static function getProtectedStaticInteger(): int' . $integer_body],
			[[$class, 'setProtectedStaticInteger'], 0x00,
				'protected static function setProtectedStaticInteger(int $integer): void {}'],
			[[$class, 'getPrivateBoolean'], 0x00, 'private function getPrivateBoolean(): bool' . $boolean_body],
			[[$class, 'setPrivateBoolean'], 0x00, 'private function setPrivateBoolean(bool $boolean): void {}'],
			[[$class, 'getPrivateStaticBoolean'], 0x00,
				'private static function getPrivateStaticBoolean(): bool' . $boolean_body],
			[[$class, 'setPrivateStaticBoolean'], 0x00,
				'private static function setPrivateStaticBoolean(bool $boolean): void {}'],
			[[$class, 'getFinalString'], 0x00, 'final public function getFinalString(): string' . $string_body],
			[[$class, 'setFinalString'], 0x00, 'final public function setFinalString(string $string): void {}'],
			[[$class, 'getFinalStaticString'], 0x00,
				'final public static function getFinalStaticString(): string' . $string_body],
			[[$class, 'setFinalStaticString'], 0x00,
				'final public static function setFinalStaticString(string $string): void {}'],
			[[$class, 'getFinalProtectedInteger'], 0x00,
				'final protected function getFinalProtectedInteger(): int' . $integer_body],
			[[$class, 'setFinalProtectedInteger'], 0x00,
				'final protected function setFinalProtectedInteger(int $integer): void {}'],
			[[$class, 'getFinalProtectedStaticInteger'], 0x00,
				'final protected static function getFinalProtectedStaticInteger(): int' . $integer_body],
			[[$class, 'setFinalProtectedStaticInteger'], 0x00,
				'final protected static function setFinalProtectedStaticInteger(int $integer): void {}'],
			[[$class, 'getFinalPrivateBoolean'], 0x00,
				'final private function getFinalPrivateBoolean(): bool' . $boolean_body],
			[[$class, 'setFinalPrivateBoolean'], 0x00,
				'final private function setFinalPrivateBoolean(bool $boolean): void {}'],
			[[$class, 'getFinalPrivateStaticBoolean'], 0x00,
				'final private static function getFinalPrivateStaticBoolean(): bool' . $boolean_body],
			[[$class, 'setFinalPrivateStaticBoolean'], 0x00,
				'final private static function setFinalPrivateStaticBoolean(bool $boolean): void {}'],
			[[$class_abstract, 'getString'], 0x00, 'abstract public function getString(): string;'],
			[[$class_abstract, 'setString'], 0x00, 'abstract public function setString(string $string): void;'],
			[[$class_abstract, 'getStaticString'], 0x00, 'abstract public static function getStaticString(): string;'],
			[[$class_abstract, 'setStaticString'], 0x00,
				'abstract public static function setStaticString(string $string): void;'],
			[[$class_abstract, 'getProtectedInteger'], 0x00, 'abstract protected function getProtectedInteger(): int;'],
			[[$class_abstract, 'setProtectedInteger'], 0x00,
				'abstract protected function setProtectedInteger(int $integer): void;'],
			[[$class_abstract, 'getProtectedStaticInteger'], 0x00,
				'abstract protected static function getProtectedStaticInteger(): int;'],
			[[$class_abstract, 'setProtectedStaticInteger'], 0x00,
				'abstract protected static function setProtectedStaticInteger(int $integer): void;'],
			[[$class_abstract, 'getFinalString'], 0x00, 'final public function getFinalString(): string' . $string_body],
			[[$class_abstract, 'setFinalString'], 0x00, 'final public function setFinalString(string $string): void {}'],
			[[$class_abstract, 'getFinalStaticString'], 0x00,
				'final public static function getFinalStaticString(): string' . $string_body],
			[[$class_abstract, 'setFinalStaticString'], 0x00,
				'final public static function setFinalStaticString(string $string): void {}'],
			[[$class_abstract, 'getFinalProtectedInteger'], 0x00,
				'final protected function getFinalProtectedInteger(): int' . $integer_body],
			[[$class_abstract, 'setFinalProtectedInteger'], 0x00,
				'final protected function setFinalProtectedInteger(int $integer): void {}'],
			[[$class_abstract, 'getFinalProtectedStaticInteger'], 0x00,
				'final protected static function getFinalProtectedStaticInteger(): int' . $integer_body],
			[[$class_abstract, 'setFinalProtectedStaticInteger'], 0x00,
				'final protected static function setFinalProtectedStaticInteger(int $integer): void {}'],
			[[$class_abstract, 'getFinalPrivateBoolean'], 0x00,
				'final private function getFinalPrivateBoolean(): bool' . $boolean_body],
			[[$class_abstract, 'setFinalPrivateBoolean'], 0x00,
				'final private function setFinalPrivateBoolean(bool $boolean): void {}'],
			[[$class_abstract, 'getFinalPrivateStaticBoolean'], 0x00,
				'final private static function getFinalPrivateStaticBoolean(): bool' . $boolean_body],
			[[$class_abstract, 'setFinalPrivateStaticBoolean'], 0x00,
				'final private static function setFinalPrivateStaticBoolean(bool $boolean): void {}'],
			[[$interface, 'getString'], 0x00, 'abstract public function getString(): string;'],
			[[$interface, 'setString'], 0x00, 'abstract public function setString(string $string): void;'],
			[[$interface, 'getStaticString'], 0x00, 'abstract public static function getStaticString(): string;'],
			[[$interface, 'setStaticString'], 0x00,
				'abstract public static function setStaticString(string $string): void;'],
			[[$class, 'doStuff'], 0x00,
				'public function doStuff(?float $fnumber, ' . $class_abstract . ' $ac, ?' . $class . ' &$c, ' . 
				'mixed $options, callable $c_function, string $farboo = ' . $class . '::A_S, array $foob = ' . $class . 
				'::B_ARRAY, int $cint = ' . $class . '::C_CONSTANT, bool &$enable = ' . $class . '::D_ENABLE, ' . 
				'?stdClass $std = null, mixed $flags = SORT_STRING, mixed ...$p): ?' . $interface . $dostuff_body],
			[[$class, 'doStuff'], UCall::SOURCE_CONSTANTS_VALUES,
				'public function doStuff(?float $fnumber, ' . $class_abstract . ' $ac, ?' . $class . ' &$c, ' . 
				'mixed $options, callable $c_function, string $farboo = "Aaa", ' . 
				'array $foob = ["foo"=>false,"bar"=>null], int $cint = 1200, bool &$enable = true, ' . 
				'?stdClass $std = null, mixed $flags = 2, mixed ...$p): ?' . $interface . $dostuff_body],
			[[$class, 'doStuff'], UCall::SOURCE_TYPES_SHORT_NAMES,
				'public function doStuff(?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, ' . 
				'mixed $options, callable $c_function, string $farboo = CallTest_Class::A_S, ' . 
				'array $foob = CallTest_Class::B_ARRAY, int $cint = CallTest_Class::C_CONSTANT, ' . 
				'bool &$enable = CallTest_Class::D_ENABLE, ?stdClass $std = null, mixed $flags = SORT_STRING, ' . 
				'mixed ...$p): ?CallTest_Interface' . $dostuff_body],
			[[$class, 'doStuff'], UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'public function doStuff(?float $fnumber, \\' . $class_abstract . ' $ac, ?\\' . $class . ' &$c, ' . 
				'mixed $options, callable $c_function, string $farboo = \\' . $class . '::A_S, ' . 
				'array $foob = \\' . $class . '::B_ARRAY, int $cint = \\' . $class . '::C_CONSTANT, ' . 
				'bool &$enable = \\' . $class . '::D_ENABLE, ?\\stdClass $std = null, mixed $flags = \\SORT_STRING, ' . 
				'mixed ...$p): ?\\' . $interface . $dostuff_body],
			[[$class, 'doStuff'], UCall::SOURCE_NO_MIXED_TYPE,
				'public function doStuff(?float $fnumber, ' . $class_abstract . ' $ac, ?' . $class . ' &$c, ' . 
				'$options, callable $c_function, string $farboo = ' . $class . '::A_S, ' . 
				'array $foob = ' . $class . '::B_ARRAY, int $cint = ' . $class . '::C_CONSTANT, ' . 
				'bool &$enable = ' . $class . '::D_ENABLE, ?stdClass $std = null, $flags = SORT_STRING, ' . 
				'...$p): ?' . $interface . $dostuff_body],
			[[$class, 'doStuff'], UCall::SOURCE_CONSTANTS_VALUES | UCall::SOURCE_TYPES_SHORT_NAMES,
				'public function doStuff(?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, ' . 
				'mixed $options, callable $c_function, string $farboo = "Aaa", ' . 
				'array $foob = ["foo"=>false,"bar"=>null], int $cint = 1200, bool &$enable = true, ' . 
				'?stdClass $std = null, mixed $flags = 2, mixed ...$p): ?CallTest_Interface' . $dostuff_body],
			[[$class, 'doStuff'], UCall::SOURCE_CONSTANTS_VALUES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'public function doStuff(?float $fnumber, \\' . $class_abstract . ' $ac, ?\\' . $class . ' &$c, ' . 
				'mixed $options, callable $c_function, string $farboo = "Aaa", ' . 
				'array $foob = ["foo"=>false,"bar"=>null], int $cint = 1200, bool &$enable = true, ' . 
				'?\\stdClass $std = null, mixed $flags = 2, mixed ...$p): ?\\' . $interface . $dostuff_body],
			[[$class, 'doStuff'], UCall::SOURCE_CONSTANTS_VALUES | UCall::SOURCE_NO_MIXED_TYPE,
				'public function doStuff(?float $fnumber, ' . $class_abstract . ' $ac, ?' . $class . ' &$c, ' . 
				'$options, callable $c_function, string $farboo = "Aaa", array $foob = ["foo"=>false,"bar"=>null], ' . 
				'int $cint = 1200, bool &$enable = true, ?stdClass $std = null, $flags = 2, ...$p): ?' . $interface . 
				$dostuff_body],
			[[$class, 'doStuff'], UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'public function doStuff(?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, ' . 
				'mixed $options, callable $c_function, string $farboo = CallTest_Class::A_S, ' . 
				'array $foob = CallTest_Class::B_ARRAY, int $cint = CallTest_Class::C_CONSTANT, ' . 
				'bool &$enable = CallTest_Class::D_ENABLE, ?stdClass $std = null, mixed $flags = \\SORT_STRING, ' . 
				'mixed ...$p): ?CallTest_Interface' . $dostuff_body],
			[[$class, 'doStuff'], UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NO_MIXED_TYPE,
				'public function doStuff(?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, ' . 
				'$options, callable $c_function, string $farboo = CallTest_Class::A_S, ' . 
				'array $foob = CallTest_Class::B_ARRAY, int $cint = CallTest_Class::C_CONSTANT, ' . 
				'bool &$enable = CallTest_Class::D_ENABLE, ?stdClass $std = null, $flags = SORT_STRING, ' . 
				'...$p): ?CallTest_Interface' . $dostuff_body],
			[[$class, 'doStuff'], UCall::SOURCE_NAMESPACES_LEADING_SLASH | UCall::SOURCE_NO_MIXED_TYPE,
				'public function doStuff(?float $fnumber, \\' . $class_abstract . ' $ac, ?\\' . $class . ' &$c, ' . 
				'$options, callable $c_function, string $farboo = \\' . $class . '::A_S, ' . 
				'array $foob = \\' . $class . '::B_ARRAY, int $cint = \\' . $class . '::C_CONSTANT, ' . 
				'bool &$enable = \\' . $class . '::D_ENABLE, ?\\stdClass $std = null, $flags = \\SORT_STRING, ' . 
				'...$p): ?\\' . $interface . $dostuff_body],
			[[$class, 'doStuff'],
				UCall::SOURCE_CONSTANTS_VALUES | UCall::SOURCE_TYPES_SHORT_NAMES | 
				UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'public function doStuff(?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, ' . 
				'mixed $options, callable $c_function, string $farboo = "Aaa", ' . 
				'array $foob = ["foo"=>false,"bar"=>null], int $cint = 1200, bool &$enable = true, ' . 
				'?stdClass $std = null, mixed $flags = 2, mixed ...$p): ?CallTest_Interface' . $dostuff_body],
			[[$class, 'doStuff'],
				UCall::SOURCE_CONSTANTS_VALUES | UCall::SOURCE_TYPES_SHORT_NAMES | 
				UCall::SOURCE_NO_MIXED_TYPE,
				'public function doStuff(?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, ' . 
				'$options, callable $c_function, string $farboo = "Aaa", array $foob = ["foo"=>false,"bar"=>null], ' . 
				'int $cint = 1200, bool &$enable = true, ?stdClass $std = null, $flags = 2, ' . 
				'...$p): ?CallTest_Interface' . $dostuff_body],
			[[$class, 'doStuff'],
				UCall::SOURCE_CONSTANTS_VALUES | UCall::SOURCE_NAMESPACES_LEADING_SLASH | 
				UCall::SOURCE_NO_MIXED_TYPE,
				'public function doStuff(?float $fnumber, \\' . $class_abstract . ' $ac, ?\\' . $class . ' &$c, ' . 
				'$options, callable $c_function, string $farboo = "Aaa", array $foob = ["foo"=>false,"bar"=>null], ' . 
				'int $cint = 1200, bool &$enable = true, ?\\stdClass $std = null, $flags = 2, ' . 
				'...$p): ?\\' . $interface . $dostuff_body],
			[[$class, 'doStuff'],
				UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH | 
				UCall::SOURCE_NO_MIXED_TYPE,
				'public function doStuff(?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, ' . 
				'$options, callable $c_function, string $farboo = CallTest_Class::A_S, ' . 
				'array $foob = CallTest_Class::B_ARRAY, int $cint = CallTest_Class::C_CONSTANT, ' . 
				'bool &$enable = CallTest_Class::D_ENABLE, ?stdClass $std = null, $flags = \\SORT_STRING, ' . 
				'...$p): ?CallTest_Interface' . $dostuff_body],
			[[$class, 'doStuff'],
				UCall::SOURCE_CONSTANTS_VALUES | UCall::SOURCE_TYPES_SHORT_NAMES | 
				UCall::SOURCE_NAMESPACES_LEADING_SLASH | UCall::SOURCE_NO_MIXED_TYPE,
				'public function doStuff(?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, ' . 
				'$options, callable $c_function, string $farboo = "Aaa", array $foob = ["foo"=>false,"bar"=>null], ' . 
				'int $cint = 1200, bool &$enable = true, ?stdClass $std = null, $flags = 2, ' . 
				'...$p): ?CallTest_Interface' . $dostuff_body]
		];
	}
	
	/**
	 * Test <code>signature</code> method.
	 * 
	 * @dataProvider provideSignatureMethodData
	 * @testdox Call::signature({$function}) === '$expected'
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testSignatureMethod($function, string $expected): void
	{
		$this->assertSame($expected, UCall::signature($function));
	}
	
	/**
	 * Provide <code>signature</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>signature</code> method data.</p>
	 */
	public function provideSignatureMethodData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', '( mixed ): mixed'],
			[function () {}, '(): mixed'],
			[function (): void {}, '(): void'],
			[function (): bool {}, '(): bool'],
			[function (): ?bool {}, '(): ?bool'],
			[function (): int {}, '(): int'],
			[function (): ?int {}, '(): ?int'],
			[function (): float {}, '(): float'],
			[function (): ?float {}, '(): ?float'],
			[function (): string {}, '(): string'],
			[function (): ?string {}, '(): ?string'],
			[function (): array {}, '(): array'],
			[function (): ?array {}, '(): ?array'],
			[function (): callable {}, '(): callable'],
			[function (): ?callable {}, '(): ?callable'],
			[function (): object {}, '(): object'],
			[function (): ?object {}, '(): ?object'],
			[function (): \stdClass {}, '(): stdClass'],
			[function (): ?\stdClass {}, '(): ?stdClass'],
			[function (): CallTest_Class {}, '(): ' . $class],
			[function (): ?CallTest_Class {}, '(): ?' . $class],
			[function (): CallTest_AbstractClass {}, '(): ' . $class_abstract],
			[function (): ?CallTest_AbstractClass {}, '(): ?' . $class_abstract],
			[function (): CallTest_Interface {}, '(): ' . $interface],
			[function (): ?CallTest_Interface {}, '(): ?' . $interface],
			[function (int $i): ?bool {}, '( int ): ?bool'],
			[function (bool $b, ?string $s): int {}, '( bool , ?string ): int'],
			[function (&$ref): float {}, '( &mixed ): float'],
			[function (array $array, int $n = 12): ?float {}, '( array [, int ]): ?float'],
			[function (callable $c, int &$ii = 739): string {}, '( callable [, &int ]): string'],
			[function (?object $obj = null): ?string {}, '([ ?object ]): ?string'],
			[function (string $soo, \stdClass $std, ?object $obj = null, float &$f = 0.0, ?int ...$i): ?string {},
				'( string , stdClass [, ?object [, &float [, ...?int ]]]): ?string'],
			[function (CallTest_Class $a, \stdClass $b, bool $e = false, $k = null): void {},
				'( ' . $class . ' , stdClass [, bool [, mixed ]]): void'],
			[function (CallTest_AbstractClass $ac, ?CallTest_Interface $i): ?callable {},
				'( ' . $class_abstract . ' , ?' . $interface . ' ): ?callable'],
			[new CallTest_InvokeableClass(), '(): ?' . $class],
			[new CallTest_InvokeableClass2(), '([ string ]): void'],
			[[$class, 'getString'], '(): string'],
			[[$class, 'setString'], '( string ): void'],
			[[$class, 'getStaticString'], '(): string'],
			[[$class, 'setStaticString'], '( string ): void'],
			[[$class, 'getProtectedInteger'], '(): int'],
			[[$class, 'setProtectedInteger'], '( int ): void'],
			[[$class, 'getProtectedStaticInteger'], '(): int'],
			[[$class, 'setProtectedStaticInteger'], '( int ): void'],
			[[$class, 'getPrivateBoolean'], '(): bool'],
			[[$class, 'setPrivateBoolean'], '( bool ): void'],
			[[$class, 'getPrivateStaticBoolean'], '(): bool'],
			[[$class, 'setPrivateStaticBoolean'], '( bool ): void'],
			[[$class_abstract, 'getString'], '(): string'],
			[[$class_abstract, 'setString'], '( string ): void'],
			[[$class_abstract, 'getStaticString'], '(): string'],
			[[$class_abstract, 'setStaticString'], '( string ): void'],
			[[$class_abstract, 'getProtectedInteger'], '(): int'],
			[[$class_abstract, 'setProtectedInteger'], '( int ): void'],
			[[$class_abstract, 'getProtectedStaticInteger'], '(): int'],
			[[$class_abstract, 'setProtectedStaticInteger'], '( int ): void'],
			[[$interface, 'getString'], '(): string'],
			[[$interface, 'setString'], '( string ): void'],
			[[$interface, 'getStaticString'], '(): string'],
			[[$interface, 'setStaticString'], '( string ): void'],
			[[$class, 'doStuff'],
				'( ?float , ' . $class_abstract . ' , &?' . $class . ' , mixed , callable [, string [, array [, int ' . 
				'[, &bool [, ?stdClass [, mixed [, ...mixed ]]]]]]]): ?' . $interface]
		];
	}
	
	/**
	 * Test <code>compatible</code> method.
	 * 
	 * @dataProvider provideCompatibleMethodData
	 * @testdox Call::compatible({$function}, {$template}) === $expected
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param callable|array|string $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @param bool $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testCompatibleMethod($function, $template, bool $expected): void
	{
		$this->assertSame($expected, UCall::compatible($function, $template));
	}
	
	/**
	 * Provide <code>compatible</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>compatible</code> method data.</p>
	 */
	public function provideCompatibleMethodData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', 'strlen', true],
			['strlen', 'str_repeat', false],
			['str_repeat', 'strlen', false],
			['strlen', function ($string) {}, true],
			['strlen', function (string $string) {}, true],
			['strlen', function ($string): int {}, false],
			['strlen', function (string $string): int {}, false],
			[function ($string) {}, 'strlen', true],
			[function (string $string) {}, 'strlen', false],
			[function ($string): int {}, 'strlen', true],
			[function (string $string): int {}, 'strlen', false],
			[function () {}, function () {}, true],
			[function () {}, function (): void {}, true],
			[function () {}, function (): bool {}, false],
			[function () {}, function (): ?bool {}, false],
			[function () {}, function (): int {}, false],
			[function () {}, function (): ?int {}, false],
			[function () {}, function (): float {}, false],
			[function () {}, function (): ?float {}, false],
			[function () {}, function (): string {}, false],
			[function () {}, function (): ?string {}, false],
			[function () {}, function (): array {}, false],
			[function () {}, function (): ?array {}, false],
			[function () {}, function (): callable {}, false],
			[function () {}, function (): ?callable {}, false],
			[function () {}, function (): object {}, false],
			[function () {}, function (): ?object {}, false],
			[function () {}, function (): \stdClass {}, false],
			[function () {}, function (): ?\stdClass {}, false],
			[function () {}, function (): CallTest_Class {}, false],
			[function () {}, function (): ?CallTest_Class {}, false],
			[function () {}, function (): CallTest_AbstractClass {}, false],
			[function () {}, function (): ?CallTest_AbstractClass {}, false],
			[function () {}, function (): CallTest_Interface {}, false],
			[function () {}, function (): ?CallTest_Interface {}, false],
			[function (): void {}, function () {}, false],
			[function (): bool {}, function () {}, true],
			[function (): ?bool {}, function () {}, true],
			[function (): int {}, function () {}, true],
			[function (): ?int {}, function () {}, true],
			[function (): float {}, function () {}, true],
			[function (): ?float {}, function () {}, true],
			[function (): string {}, function () {}, true],
			[function (): ?string {}, function () {}, true],
			[function (): array {}, function () {}, true],
			[function (): ?array {}, function () {}, true],
			[function (): callable {}, function () {}, true],
			[function (): ?callable {}, function () {}, true],
			[function (): object {}, function () {}, true],
			[function (): ?object {}, function () {}, true],
			[function (): \stdClass {}, function () {}, true],
			[function (): ?\stdClass {}, function () {}, true],
			[function (): CallTest_Class {}, function () {}, true],
			[function (): ?CallTest_Class {}, function () {}, true],
			[function (): CallTest_AbstractClass {}, function () {}, true],
			[function (): ?CallTest_AbstractClass {}, function () {}, true],
			[function (): CallTest_Interface {}, function () {}, true],
			[function (): ?CallTest_Interface {}, function () {}, true],
			[function (): void {}, function (): void {}, true],
			[function (): void {}, function (): bool {}, false],
			[function (): bool {}, function (): void {}, true],
			[function (): bool {}, function (): bool {}, true],
			[function (): bool {}, function (): ?bool {}, true],
			[function (): ?bool {}, function (): bool {}, false],
			[function (): ?bool {}, function (): ?bool {}, true],
			[function (): void {}, function (): void {}, true],
			[function (): void {}, function (): int {}, false],
			[function (): int {}, function (): void {}, true],
			[function (): int {}, function (): int {}, true],
			[function (): int {}, function (): ?int {}, true],
			[function (): ?int {}, function (): int {}, false],
			[function (): ?int {}, function (): ?int {}, true],
			[function (): int {}, function (): float {}, false],
			[function (): float {}, function (): int {}, false],
			[function (): \stdClass {}, function (): \stdClass {}, true],
			[function (): CallTest_Class {}, function (): CallTest_Class {}, true],
			[function (): \stdClass {}, function (): CallTest_Class {}, false],
			[function (): CallTest_Class {}, function (): \stdClass {}, false],
			[function (): CallTest_Class {}, function (): CallTest_Class2 {}, false],
			[function (): CallTest_Class2 {}, function (): CallTest_Class {}, true],
			[function (): CallTest_Class {}, function (): ?CallTest_Class2 {}, false],
			[function (): ?CallTest_Class2 {}, function (): CallTest_Class {}, false],
			[function (): ?CallTest_Class {}, function (): CallTest_Class2 {}, false],
			[function (): CallTest_Class2 {}, function (): ?CallTest_Class {}, true],
			[function (): ?CallTest_Class {}, function (): ?CallTest_Class2 {}, false],
			[function (): ?CallTest_Class2 {}, function (): ?CallTest_Class {}, true],
			[function (): CallTest_Interface {}, function (): CallTest_Interface {}, true],
			[function (): \stdClass {}, function (): CallTest_Interface {}, false],
			[function (): CallTest_Interface {}, function (): \stdClass {}, false],
			[function (): CallTest_Interface {}, function (): CallTest_InterfaceClass {}, false],
			[function (): CallTest_InterfaceClass {}, function (): CallTest_Interface {}, true],
			[function (): CallTest_Interface {}, function (): ?CallTest_InterfaceClass {}, false],
			[function (): ?CallTest_InterfaceClass {}, function (): CallTest_Interface {}, false],
			[function (): ?CallTest_Interface {}, function (): CallTest_InterfaceClass {}, false],
			[function (): CallTest_InterfaceClass {}, function (): ?CallTest_Interface {}, true],
			[function (): ?CallTest_Interface {}, function (): ?CallTest_InterfaceClass {}, false],
			[function (): ?CallTest_InterfaceClass {}, function (): ?CallTest_Interface {}, true],
			[function (): object {}, function (): object {}, true],
			[function (): object {}, function (): ?object {}, true],
			[function (): ?object {}, function (): object {}, false],
			[function (): ?object {}, function (): ?object {}, true],
			[function (): object {}, function (): CallTest_Class {}, false],
			[function (): object {}, function (): ?CallTest_Class {}, false],
			[function (): CallTest_Class {}, function (): object {}, true],
			[function (): ?CallTest_Class {}, function (): object {}, false],
			[function (): ?object {}, function (): CallTest_Class {}, false],
			[function (): ?object {}, function (): ?CallTest_Class {}, false],
			[function (): CallTest_Class {}, function (): ?object {}, true],
			[function (): ?CallTest_Class {}, function (): ?object {}, true],
			[function (): object {}, function (): CallTest_Interface {}, false],
			[function (): object {}, function (): ?CallTest_Interface {}, false],
			[function (): CallTest_Interface {}, function (): object {}, true],
			[function (): ?CallTest_Interface {}, function (): object {}, false],
			[function (): ?object {}, function (): CallTest_Interface {}, false],
			[function (): ?object {}, function (): ?CallTest_Interface {}, false],
			[function (): CallTest_Interface {}, function (): ?object {}, true],
			[function (): ?CallTest_Interface {}, function (): ?object {}, true],
			[function ($b) {}, function (bool $b) {}, true],
			[function ($b) {}, function (?bool $b) {}, true],
			[function ($i) {}, function (int $i) {}, true],
			[function ($i) {}, function (?int $i) {}, true],
			[function ($f) {}, function (float $f) {}, true],
			[function ($f) {}, function (?float $f) {}, true],
			[function ($s) {}, function (string $s) {}, true],
			[function ($s) {}, function (?string $s) {}, true],
			[function ($a) {}, function (array $a) {}, true],
			[function ($a) {}, function (?array $a) {}, true],
			[function ($c) {}, function (callable $c) {}, true],
			[function ($c) {}, function (?callable $c) {}, true],
			[function ($o) {}, function (object $o) {}, true],
			[function ($o) {}, function (?object $o) {}, true],
			[function ($std) {}, function (\stdClass $std) {}, true],
			[function ($std) {}, function (?\stdClass $std) {}, true],
			[function ($c) {}, function (CallTest_Class $c) {}, true],
			[function ($c) {}, function (?CallTest_Class $c) {}, true],
			[function ($ac) {}, function (CallTest_AbstractClass $ac) {}, true],
			[function ($ac) {}, function (?CallTest_AbstractClass $ac) {}, true],
			[function ($i) {}, function (CallTest_Interface $i) {}, true],
			[function ($i) {}, function (?CallTest_Interface $i) {}, true],
			[function (bool $b) {}, function ($b) {}, false],
			[function (?bool $b) {}, function ($b) {}, false],
			[function (int $i) {}, function ($i) {}, false],
			[function (?int $i) {}, function ($i) {}, false],
			[function (float $f) {}, function ($f) {}, false],
			[function (?float $f) {}, function ($f) {}, false],
			[function (string $s) {}, function ($s) {}, false],
			[function (?string $s) {}, function ($s) {}, false],
			[function (array $a) {}, function ($a) {}, false],
			[function (?array $a) {}, function ($a) {}, false],
			[function (callable $c) {}, function ($c) {}, false],
			[function (?callable $c) {}, function ($c) {}, false],
			[function (object $o) {}, function ($o) {}, false],
			[function (?object $o) {}, function ($o) {}, false],
			[function (\stdClass $std) {}, function ($std) {}, false],
			[function (?\stdClass $std) {}, function ($std) {}, false],
			[function (CallTest_Class $c) {}, function ($c) {}, false],
			[function (?CallTest_Class $c) {}, function ($c) {}, false],
			[function (CallTest_AbstractClass $ac) {}, function ($ac) {}, false],
			[function (?CallTest_AbstractClass $ac) {}, function ($ac) {}, false],
			[function (CallTest_Interface $i) {}, function ($i) {}, false],
			[function (?CallTest_Interface $i) {}, function ($i) {}, false],
			[function (bool $b) {}, function (bool $b) {}, true],
			[function (bool $b) {}, function (?bool $b) {}, false],
			[function (?bool $b) {}, function (bool $b) {}, true],
			[function (?bool $b) {}, function (?bool $b) {}, true],
			[function (int $i) {}, function (int $i) {}, true],
			[function (int $i) {}, function (?int $i) {}, false],
			[function (?int $i) {}, function (int $i) {}, true],
			[function (?int $i) {}, function (?int $i) {}, true],
			[function (int $i) {}, function (float $f) {}, false],
			[function (float $f) {}, function (int $i) {}, false],
			[function (\stdClass $std) {}, function (\stdClass $std) {}, true],
			[function (CallTest_Class $c) {}, function (CallTest_Class $c) {}, true],
			[function (\stdClass $std) {}, function (CallTest_Class $c) {}, false],
			[function (CallTest_Class $c) {}, function (\stdClass $std) {}, false],
			[function (CallTest_Class $c) {}, function (CallTest_Class2 $c) {}, true],
			[function (CallTest_Class2 $c) {}, function (CallTest_Class $c) {}, false],
			[function (?CallTest_Class $c) {}, function (CallTest_Class2 $c) {}, true],
			[function (CallTest_Class2 $c) {}, function (?CallTest_Class $c) {}, false],
			[function (CallTest_Class $c) {}, function (?CallTest_Class2 $c) {}, false],
			[function (?CallTest_Class2 $c) {}, function (CallTest_Class $c) {}, false],
			[function (?CallTest_Class $c) {}, function (?CallTest_Class2 $c) {}, true],
			[function (?CallTest_Class2 $c) {}, function (?CallTest_Class $c) {}, false],
			[function (\stdClass $std) {}, function (\stdClass $std) {}, true],
			[function (CallTest_Interface $i) {}, function (CallTest_Interface $i) {}, true],
			[function (\stdClass $std) {}, function (CallTest_Interface $i) {}, false],
			[function (CallTest_Interface $i) {}, function (\stdClass $std) {}, false],
			[function (CallTest_Interface $i) {}, function (CallTest_InterfaceClass $c) {}, true],
			[function (CallTest_InterfaceClass $c) {}, function (CallTest_Interface $i) {}, false],
			[function (?CallTest_Interface $i) {}, function (CallTest_InterfaceClass $c) {}, true],
			[function (CallTest_InterfaceClass $c) {}, function (?CallTest_Interface $i) {}, false],
			[function (CallTest_Interface $i) {}, function (?CallTest_InterfaceClass $c) {}, false],
			[function (?CallTest_InterfaceClass $c) {}, function (CallTest_Interface $i) {}, false],
			[function (?CallTest_Interface $i) {}, function (?CallTest_InterfaceClass $c) {}, true],
			[function (?CallTest_InterfaceClass $c) {}, function (?CallTest_Interface $i) {}, false],
			[function (object $o) {}, function (object $o) {}, true],
			[function (object $o) {}, function (?object $o) {}, false],
			[function (?object $o) {}, function (object $o) {}, true],
			[function (?object $o) {}, function (?object $o) {}, true],
			[function (object $o) {}, function (CallTest_Class $c) {}, true],
			[function (object $o) {}, function (?CallTest_Class $c) {}, false],
			[function (CallTest_Class $c) {}, function (object $o) {}, false],
			[function (?CallTest_Class $c) {}, function (object $o) {}, false],
			[function (?object $o) {}, function (CallTest_Class $c) {}, true],
			[function (?object $o) {}, function (?CallTest_Class $c) {}, true],
			[function (CallTest_Class $c) {}, function (?object $o) {}, false],
			[function (?CallTest_Class $c) {}, function (?object $o) {}, false],
			[function (object $o) {}, function (CallTest_Interface $c) {}, true],
			[function (object $o) {}, function (?CallTest_Interface $c) {}, false],
			[function (CallTest_Interface $c) {}, function (object $o) {}, false],
			[function (?CallTest_Interface $c) {}, function (object $o) {}, false],
			[function (?object $o) {}, function (CallTest_Interface $c) {}, true],
			[function (?object $o) {}, function (?CallTest_Interface $c) {}, true],
			[function (CallTest_Interface $c) {}, function (?object $o) {}, false],
			[function (?CallTest_Interface $c) {}, function (?object $o) {}, false],
			[function (string &$ref) {}, function (string &$ref) {}, true],
			[function (string $ref) {}, function (string &$ref) {}, false],
			[function (string &$ref) {}, function (string $ref) {}, false],
			[function (string ...$var) {}, function (string ...$var) {}, true],
			[function (string $var) {}, function (string ...$var) {}, false],
			[function (string ...$var) {}, function (string $var) {}, false],
			[function (string $s, int $i = 0) {}, function (string $s, int $i) {}, true],
			[function (string $s, int $i) {}, function (string $s, int $i = 0) {}, false],
			[function (string $s, \stdClass $sc, ?object $o = null, float &$f = 0.0, ?int ...$i): CallTest_Class2 {},
				function (string $s, \stdClass $sc, ?object $o = null, float &$f = 0.0, ?int ...$i): CallTest_Class {},
				true],
			[function (string $s, \stdClass $sc, ?object $o = null, float &$f = 0.0, ?int ...$i): CallTest_Class {},
				function (string $s, \stdClass $sc, ?object $o = null, float &$f = 0.0, ?int ...$i): CallTest_Class2 {},
				false],
			[function (?string $s, object $sc, ?object $o = null, float &$f = 0.0, ?int ...$i): CallTest_Class2 {},
				function (string $s, \stdClass $sc, \stdClass $o, float &$f = 1.0, int ...$i): CallTest_Class {},
				true],
			[function (string $s, object $sc, ?object $o = null, float &$f = 0.0, ?int ...$i): CallTest_Class2 {},
				function (?string $s, \stdClass $sc, \stdClass $o, float &$f = 1.0, int ...$i): CallTest_Class {},
				false],
			[function (?string $s, \stdClass $sc, ?object $o = null, float &$f = 0.0, ?int ...$i): CallTest_Class2 {},
				function (string $s, object $sc, \stdClass $o, float &$f = 1.0, int ...$i): CallTest_Class {},
				false],
			[function (?string $s, object $sc, \stdClass $o = null, float &$f = 0.0, ?int ...$i): CallTest_Class2 {},
				function (string $s, \stdClass $sc, ?object $o, float &$f = 1.0, int ...$i): CallTest_Class {},
				false],
			[function (?string $s, object $sc, ?object $o = null, float &$f = 0.0, ?int ...$i): CallTest_Class2 {},
				function (string $s, \stdClass $sc, \stdClass $o, int &$f = 1, int ...$i): CallTest_Class {},
				false],
			[function (?string $s, object $sc, ?object $o = null, float &$f = 0.0, ?int ...$i): CallTest_Class2 {},
				function (string $s, \stdClass $sc, \stdClass $o, float $f = 1.0, int ...$i): CallTest_Class {},
				false],
			[function (?string $s, object $sc, ?object $o = null, float &$f = 0.0, ?int ...$i): CallTest_Class2 {},
				function (string $s, \stdClass $sc, \stdClass $o, float &$f, int ...$i): CallTest_Class {},
				true],
			[function (?string $s, object $sc, ?object $o = null, float &$f = 0.0, ?int ...$i): CallTest_Class2 {},
				function (string $s, \stdClass $sc, \stdClass $o, float &$f = 0.0, int $i = 1): CallTest_Class {},
				false],
			[new CallTest_InvokeableClass(), function () {}, true],
			[function () {}, new CallTest_InvokeableClass(), false],
			[new CallTest_InvokeableClass(), function (): CallTest_Class2 {}, false],
			[new CallTest_InvokeableClass(), function (): ?CallTest_Class {}, true],
			[new CallTest_InvokeableClass(), function (): CallTest_Class {}, false],
			[new CallTest_InvokeableClass(), function ($n) {}, false],
			[function ($n): ?CallTest_Class2 {}, new CallTest_InvokeableClass(), false],
			[new CallTest_InvokeableClass(), function ($n = null) {}, false],
			[function ($n = null): ?CallTest_Class2 {}, new CallTest_InvokeableClass(), true],
			[new CallTest_InvokeableClass(), function (): void {}, true],
			[function (): void {}, new CallTest_InvokeableClass(), false],
			[new CallTest_InvokeableClass(), function (): bool {}, false],
			[function (): bool {}, new CallTest_InvokeableClass(), false],
			[new CallTest_InvokeableClass2(), function (string $s): void {}, true],
			[new CallTest_InvokeableClass2(), function (string $s = ''): void {}, true],
			[new CallTest_InvokeableClass2(), function (string $s) {}, false],
			[new CallTest_InvokeableClass2(), function (?string $s): void {}, false],
			[[$class, 'getString'], [$class, 'getStaticString'], true],
			[[$class, 'getString'], [$class, 'setStaticString'], false],
			[[$class, 'setString'], [$class, 'setStaticString'], true],
			[[$class, 'setString'], [$class, 'getStaticString'], false],
			[[$class, 'getStaticString'], function (): string {}, true],
			[[$class, 'getStaticString'], function (): ?string {}, true],
			[[$class, 'getStaticString'], function (): object {}, false],
			[[$class, 'setStaticString'], function (string $s): void {}, true],
			[[$class, 'setStaticString'], function (?string $s): void {}, false],
			[[$class, 'setStaticString'], function (string $s = ''): void {}, false],
			[[$class, 'getProtectedInteger'], [$class, 'getProtectedStaticInteger'], true],
			[[$class, 'getProtectedInteger'], [$class, 'setProtectedStaticInteger'], false],
			[[$class, 'setProtectedInteger'], [$class, 'setProtectedStaticInteger'], true],
			[[$class, 'setProtectedInteger'], [$class, 'getProtectedStaticInteger'], false],
			[[$class, 'getProtectedStaticInteger'], function (): int {}, true],
			[[$class, 'getProtectedStaticInteger'], function (): ?int {}, true],
			[[$class, 'getProtectedStaticInteger'], function (): object {}, false],
			[[$class, 'setProtectedStaticInteger'], function (int $i): void {}, true],
			[[$class, 'setProtectedStaticInteger'], function (?int $i): void {}, false],
			[[$class, 'setProtectedStaticInteger'], function (int $i = 0): void {}, false],
			[[$class, 'getPrivateBoolean'], [$class, 'getPrivateStaticBoolean'], true],
			[[$class, 'getPrivateBoolean'], [$class, 'setPrivateStaticBoolean'], false],
			[[$class, 'setPrivateBoolean'], [$class, 'setPrivateStaticBoolean'], true],
			[[$class, 'setPrivateBoolean'], [$class, 'getPrivateStaticBoolean'], false],
			[[$class, 'getPrivateStaticBoolean'], function (): bool {}, true],
			[[$class, 'getPrivateStaticBoolean'], function (): ?bool {}, true],
			[[$class, 'getPrivateStaticBoolean'], function (): object {}, false],
			[[$class, 'setPrivateStaticBoolean'], function (bool $b): void {}, true],
			[[$class, 'setPrivateStaticBoolean'], function (?bool $b): void {}, false],
			[[$class, 'setPrivateStaticBoolean'], function (bool $b = false): void {}, false],
			[[$class_abstract, 'getString'], [$class_abstract, 'getStaticString'], true],
			[[$class_abstract, 'getString'], [$class_abstract, 'setStaticString'], false],
			[[$class_abstract, 'setString'], [$class_abstract, 'setStaticString'], true],
			[[$class_abstract, 'setString'], [$class_abstract, 'getStaticString'], false],
			[[$class_abstract, 'getStaticString'], function (): string {}, true],
			[[$class_abstract, 'getStaticString'], function (): ?string {}, true],
			[[$class_abstract, 'getStaticString'], function (): object {}, false],
			[[$class_abstract, 'setStaticString'], function (string $s): void {}, true],
			[[$class_abstract, 'setStaticString'], function (?string $s): void {}, false],
			[[$class_abstract, 'setStaticString'], function (string $s = ''): void {}, false],
			[[$class_abstract, 'getProtectedInteger'], [$class_abstract, 'getProtectedStaticInteger'], true],
			[[$class_abstract, 'getProtectedInteger'], [$class_abstract, 'setProtectedStaticInteger'], false],
			[[$class_abstract, 'setProtectedInteger'], [$class_abstract, 'setProtectedStaticInteger'], true],
			[[$class_abstract, 'setProtectedInteger'], [$class_abstract, 'getProtectedStaticInteger'], false],
			[[$class_abstract, 'getProtectedStaticInteger'], function (): int {}, true],
			[[$class_abstract, 'getProtectedStaticInteger'], function (): ?int {}, true],
			[[$class_abstract, 'getProtectedStaticInteger'], function (): object {}, false],
			[[$class_abstract, 'setProtectedStaticInteger'], function (int $i): void {}, true],
			[[$class_abstract, 'setProtectedStaticInteger'], function (?int $i): void {}, false],
			[[$class_abstract, 'setProtectedStaticInteger'], function (int $i = 0): void {}, false],
			[[$interface, 'getString'], [$interface, 'getStaticString'], true],
			[[$interface, 'getString'], [$interface, 'setStaticString'], false],
			[[$interface, 'setString'], [$interface, 'setStaticString'], true],
			[[$interface, 'setString'], [$interface, 'getStaticString'], false],
			[[$interface, 'getStaticString'], function (): string {}, true],
			[[$interface, 'getStaticString'], function (): ?string {}, true],
			[[$interface, 'getStaticString'], function (): object {}, false],
			[[$interface, 'setStaticString'], function (string $s): void {}, true],
			[[$interface, 'setStaticString'], function (?string $s): void {}, false],
			[[$interface, 'setStaticString'], function (string $s = ''): void {}, false]
		];
	}
	
	/**
	 * Test <code>assert</code> method.
	 * 
	 * @dataProvider provideAssertMethodData
	 * @testdox Call::assert('foobar', {$function}, {$template}) === void
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param callable|array|string $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testAssertMethod($function, $template): void
	{
		$this->assertNull(UCall::assert('foobar', $function, $template));
		$this->assertTrue(UCall::assert('foobar', $function, $template, true));
	}
	
	/**
	 * Provide <code>assert</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>assert</code> method data.</p>
	 */
	public function provideAssertMethodData(): array
	{
		$data = [];
		foreach ($this->provideCompatibleMethodData() as $datum) {
			if ($datum[2]) {
				$data[] = [$datum[0], $datum[1]];
			}
		}
		return $data;
	}
	
	/**
	 * Test <code>assert</code> method expecting an <code>AssertionFailed</code> exception to be thrown.
	 * 
	 * @dataProvider provideAssertMethodDataForAssertionFailedException
	 * @testdox Call::assert('foobar', {$function}, {$template}) --> AssertionFailed exception
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param callable|array|string $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testAssertMethodAssertionFailedException($function, $template): void
	{
		$this->expectException(Exceptions\AssertionFailed::class);
		UCall::assert('foobar', $function, $template);
	}
	
	/**
	 * Test <code>assert</code> method with <var>$no_throw</var> set to <code>true</code>, 
	 * expecting boolean <code>false</code> to be returned.
	 * 
	 * @dataProvider provideAssertMethodDataForAssertionFailedException
	 * @testdox Call::assert('foobar', {$function}, {$template}, true) === false
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param callable|array|string $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testAssertMethodNoThrowFalse($function, $template): void
	{
		$this->assertFalse(UCall::assert('foobar', $function, $template, true));
	}
	
	/**
	 * Provide <code>assert</code> method data for an <code>AssertionFailed</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The provided <code>assert</code> method data for an <code>AssertionFailed</code> exception to be thrown.</p>
	 */
	public function provideAssertMethodDataForAssertionFailedException(): array
	{
		$data = [];
		foreach ($this->provideCompatibleMethodData() as $datum) {
			if (!$datum[2]) {
				$data[] = [$datum[0], $datum[1]];
			}
		}
		return $data;
	}
	
	/**
	 * Test <code>assert</code> method in a production environment.
	 * 
	 * @dataProvider provideAssertMethodDataForProductionEnvironment
	 * @testdox Call::assert('foobar', {$function}, {$template}) === void [production]
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param callable|array|string $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testAssertMethodInProductionEnvironment($function, $template): void
	{
		$environment = System::getEnvironment();
		try {
			System::setEnvironment('production');
			$this->assertNull(UCall::assert('foobar', $function, $template));
			$this->assertTrue(UCall::assert('foobar', $function, $template, true));
		} finally {
			System::setEnvironment($environment);
		}
	}
	
	/**
	 * Provide <code>assert</code> method data for a production environment.
	 * 
	 * @return array
	 * <p>The provided <code>assert</code> method data for a production environment.</p>
	 */
	public function provideAssertMethodDataForProductionEnvironment(): array
	{
		$data = [];
		foreach ($this->provideCompatibleMethodData() as $datum) {
			$data[] = [$datum[0], $datum[1]];
		}
		return $data;
	}
	
	/**
	 * Test <code>object</code> method.
	 * 
	 * @dataProvider provideObjectMethodData
	 * @testdox Call::object({$function}) === $expected
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param object|null $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testObjectMethod($function, ?object $expected): void
	{
		$this->assertSame($expected, UCall::object($function));
	}
	
	/**
	 * Provide <code>object</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>object</code> method data.</p>
	 */
	public function provideObjectMethodData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		$class_object = new CallTest_Class();
		$invokeable_object = new CallTest_InvokeableClass();
		$invokeable_object2 = new CallTest_InvokeableClass2();
		
		//return
		return [
			['strlen', null],
			[\Closure::fromCallable('strlen'), null],
			[function () {}, $this],
			[$invokeable_object, $invokeable_object],
			[$invokeable_object2, $invokeable_object2],
			[[$class, 'getString'], null],
			[[$class, 'getStaticString'], null],
			[[$class, 'getProtectedInteger'], null],
			[[$class, 'getProtectedStaticInteger'], null],
			[[$class, 'getPrivateBoolean'], null],
			[[$class, 'getPrivateStaticBoolean'], null],
			[[$class_object, 'getString'], $class_object],
			[[$class_object, 'getStaticString'], null],
			[[$class_object, 'getProtectedInteger'], $class_object],
			[[$class_object, 'getProtectedStaticInteger'], null],
			[[$class_object, 'getPrivateBoolean'], $class_object],
			[[$class_object, 'getPrivateStaticBoolean'], null],
			[\Closure::fromCallable([$class_object, 'getString']), $class_object],
			[\Closure::fromCallable([$class_object, 'getStaticString']), null],
			[[$class_abstract, 'getString'], null],
			[[$class_abstract, 'getStaticString'], null],
			[[$class_abstract, 'getProtectedInteger'], null],
			[[$class_abstract, 'getProtectedStaticInteger'], null],
			[[$interface, 'getString'], null],
			[[$interface, 'getStaticString'], null]
		];
	}
	
	/**
	 * Test <code>class</code> method.
	 * 
	 * @dataProvider provideClassMethodData
	 * @testdox Call::class({$function}, $short) === {$expected}
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param bool $short
	 * <p>The method <var>$short</var> parameter to test with.</p>
	 * @param string|null $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testClassMethod($function, bool $short, ?string $expected): void
	{
		$this->assertSame($expected, UCall::class($function, $short));
	}
	
	/**
	 * Provide <code>class</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>class</code> method data.</p>
	 */
	public function provideClassMethodData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		$class_object = new CallTest_Class();
		$invokeable_object = new CallTest_InvokeableClass();
		$invokeable_object2 = new CallTest_InvokeableClass2();
		
		//return
		return [
			['strlen', false, null],
			['strlen', true, null],
			[\Closure::fromCallable('strlen'), false, null],
			[\Closure::fromCallable('strlen'), true, null],
			[function () {}, false, static::class],
			[function () {}, true, 'CallTest'],
			[$invokeable_object, false, CallTest_InvokeableClass::class],
			[$invokeable_object, true, 'CallTest_InvokeableClass'],
			[$invokeable_object2, false, CallTest_InvokeableClass2::class],
			[$invokeable_object2, true, 'CallTest_InvokeableClass2'],
			[[$class, 'getString'], false, $class],
			[[$class, 'getString'], true, 'CallTest_Class'],
			[[$class, 'getStaticString'], false, $class],
			[[$class, 'getStaticString'], true, 'CallTest_Class'],
			[[$class, 'getProtectedInteger'], false, $class],
			[[$class, 'getProtectedInteger'], true, 'CallTest_Class'],
			[[$class, 'getProtectedStaticInteger'], false, $class],
			[[$class, 'getProtectedStaticInteger'], true, 'CallTest_Class'],
			[[$class, 'getPrivateBoolean'], false, $class],
			[[$class, 'getPrivateBoolean'], true, 'CallTest_Class'],
			[[$class, 'getPrivateStaticBoolean'], false, $class],
			[[$class, 'getPrivateStaticBoolean'], true, 'CallTest_Class'],
			[[$class_object, 'getString'], false, $class],
			[[$class_object, 'getString'], true, 'CallTest_Class'],
			[[$class_object, 'getStaticString'], false, $class],
			[[$class_object, 'getStaticString'], true, 'CallTest_Class'],
			[[$class_object, 'getProtectedInteger'], false, $class],
			[[$class_object, 'getProtectedInteger'], true, 'CallTest_Class'],
			[[$class_object, 'getProtectedStaticInteger'], false, $class],
			[[$class_object, 'getProtectedStaticInteger'], true, 'CallTest_Class'],
			[[$class_object, 'getPrivateBoolean'], false, $class],
			[[$class_object, 'getPrivateBoolean'], true, 'CallTest_Class'],
			[[$class_object, 'getPrivateStaticBoolean'], false, $class],
			[[$class_object, 'getPrivateStaticBoolean'], true, 'CallTest_Class'],
			[\Closure::fromCallable([$class_object, 'getString']), false, $class],
			[\Closure::fromCallable([$class_object, 'getString']), true, 'CallTest_Class'],
			[\Closure::fromCallable([$class_object, 'getStaticString']), false, $class],
			[\Closure::fromCallable([$class_object, 'getStaticString']), true, 'CallTest_Class'],
			[[$class_abstract, 'getString'], false, $class_abstract],
			[[$class_abstract, 'getString'], true, 'CallTest_AbstractClass'],
			[[$class_abstract, 'getStaticString'], false, $class_abstract],
			[[$class_abstract, 'getStaticString'], true, 'CallTest_AbstractClass'],
			[[$class_abstract, 'getProtectedInteger'], false, $class_abstract],
			[[$class_abstract, 'getProtectedInteger'], true, 'CallTest_AbstractClass'],
			[[$class_abstract, 'getProtectedStaticInteger'], false, $class_abstract],
			[[$class_abstract, 'getProtectedStaticInteger'], true, 'CallTest_AbstractClass'],
			[[$interface, 'getString'], false, $interface],
			[[$interface, 'getString'], true, 'CallTest_Interface'],
			[[$interface, 'getStaticString'], false, $interface],
			[[$interface, 'getStaticString'], true, 'CallTest_Interface']
		];
	}
}



/** Test case dummy invokeable class. */
class CallTest_InvokeableClass
{
	public function __invoke(): ?CallTest_Class {}
}



/** Test case dummy invokeable class 2. */
class CallTest_InvokeableClass2
{
	public const FOO_CONSTANT = 'bar2foo';
	
	public function __invoke(string $s_foo = self::FOO_CONSTANT): void {
		//condition
		if (strlen($s_foo) > 45) {
			$s_foo = substr($s_foo, 0, 45);
		}
	}
}



/** Test case dummy class. */
class CallTest_Class
{
	public const A_S = 'Aaa';
	public const B_ARRAY = ['foo' => false, 'bar' => null];
	protected const C_CONSTANT = 1200;
	private const D_ENABLE = true;
	
	public function getString(): string
	{
		return '';
	}
	
	public function setString(string $string): void {}
	
	public function doStuff(
		?float $fnumber, CallTest_AbstractClass $ac, ?CallTest_Class &$c, $options, callable $c_function,
		string $farboo = self::A_S, array $foob = self::B_ARRAY, int $cint = self::C_CONSTANT,
		bool &$enable = self::D_ENABLE, ?\stdClass $std = null, $flags = SORT_STRING, ...$p
	): ?CallTest_Interface
	{
		//iterate
		foreach ($foob as $foo) {
			$fnumber *= $foo;
		}
		
		//do something
		$farboo = "{$fnumber}{$cint}";
		if ($enable) {
			if (UCall::object($c_function) === null) {
				$enable = false;
			} elseif ($flags & SORT_STRING) {
				$std->barobj = $ac;
				$c = $ac;
			}
		}
		
		//return
		return new class implements CallTest_Interface
		{
			public function getString(): string {}
			public function setString(string $string): void {}
			public static function getStaticString(): string {}
			public static function setStaticString(string $string): void {}
		};
	}
	
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



/** Test case dummy class 2. */
class CallTest_Class2 extends CallTest_Class {}



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



/** Test case dummy interface class. */
class CallTest_InterfaceClass implements CallTest_Interface
{
	public function getString(): string {}
	public function setString(string $string): void {}
	public static function getStaticString(): string {}
	public static function setStaticString(string $string): void {}
}
