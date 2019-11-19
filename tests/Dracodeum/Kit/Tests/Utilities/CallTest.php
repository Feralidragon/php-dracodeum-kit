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
			[[$class_abstract, 'getFinalStaticString'], 0x00, 'final public static function getFinalStaticString(): string'],
			[[$class_abstract, 'setFinalStaticString'], 0x00,
				'final public static function setFinalStaticString(string $string): void'],
			[[$class_abstract, 'getFinalProtectedInteger'], 0x00, 'final protected function getFinalProtectedInteger(): int'],
			[[$class_abstract, 'setFinalProtectedInteger'], 0x00,
				'final protected function setFinalProtectedInteger(int $integer): void'],
			[[$class_abstract, 'getFinalProtectedStaticInteger'], 0x00,
				'final protected static function getFinalProtectedStaticInteger(): int'],
			[[$class_abstract, 'setFinalProtectedStaticInteger'], 0x00,
				'final protected static function setFinalProtectedStaticInteger(int $integer): void'],
			[[$class_abstract, 'getFinalPrivateBoolean'], 0x00, 'final private function getFinalPrivateBoolean(): bool'],
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
	
	public function __invoke(string $s_foo = self::FOO_CONSTANT): void {}
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
	): ?CallTest_Interface {}
	
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
