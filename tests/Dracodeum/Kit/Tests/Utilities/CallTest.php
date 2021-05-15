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
use Closure;

/** @see \Dracodeum\Kit\Utilities\Call */
class CallTest extends TestCase
{
	//Public methods
	/**
	 * Test <code>validate</code> method.
	 * 
	 * @testdox Call::validate({$function}) === void
	 * @dataProvider provideValidateData
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @return void
	 */
	public function testValidate($function): void
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
	public function provideValidateData(): array
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
	 * @testdox Call::validate({$function}) --> InvalidFunction exception
	 * @dataProvider provideValidateData_Exception_InvalidFunction
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @return void
	 */
	public function testValidate_Exception_InvalidFunction($function): void
	{
		$this->expectException(Exceptions\InvalidFunction::class);
		try {
			UCall::validate($function);
		} catch (Exceptions\InvalidFunction $exception) {
			$this->assertSame($function, $exception->function);
			throw $exception;
		}
	}
	
	/**
	 * Test <code>validate</code> method with <var>$no_throw</var> set to boolean <code>true</code>, 
	 * expecting boolean <code>false</code> to be returned.
	 * 
	 * @testdox Call::validate({$function}, true) === false
	 * @dataProvider provideValidateData_Exception_InvalidFunction
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @return void
	 */
	public function testValidate_NoThrow_False($function): void
	{
		$this->assertFalse(UCall::validate($function, true));
	}
	
	/**
	 * Provide <code>validate</code> method data for an <code>InvalidFunction</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The provided <code>validate</code> method data for an <code>InvalidFunction</code> exception to be thrown.</p>
	 */
	public function provideValidateData_Exception_InvalidFunction(): array
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
	 * @testdox Call::reflection({$function}, $methodify) === $expected_class
	 * @dataProvider provideReflectionData
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param string $expected_class
	 * <p>The expected method return instance class.</p>
	 * @param bool $methodify [default = false]
	 * <p>The method <var>$methodify</var> parameter to test with.</p>
	 * @return void
	 */
	public function testReflection($function, string $expected_class, bool $methodify = false): void
	{
		foreach ([false, true] as $no_throw) {
			$this->assertInstanceOf($expected_class, UCall::reflection($function, $methodify, $no_throw));
		}
	}
	
	/**
	 * Provide <code>reflection</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>reflection</code> method data.</p>
	 */
	public function provideReflectionData(): array
	{
		//initialize
		$c = new CallTest_Class();
		$ci = new CallTest_InvokeableClass();
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', \ReflectionFunction::class],
			['strlen', \ReflectionFunction::class, true],
			[function () {}, \ReflectionFunction::class],
			[function () {}, \ReflectionFunction::class, true],
			[$ci, \ReflectionMethod::class],
			[Closure::fromCallable($ci), \ReflectionFunction::class],
			[Closure::fromCallable($ci), \ReflectionMethod::class, true],
			["{$class}::getString", \ReflectionMethod::class],
			["{$class}->getString", \ReflectionMethod::class],
			[[$class, 'getString'], \ReflectionMethod::class],
			[[$c, 'getString'], \ReflectionMethod::class],
			[Closure::fromCallable([$c, 'getString']), \ReflectionFunction::class],
			[Closure::fromCallable([$c, 'getString']), \ReflectionMethod::class, true],
			["{$class}::getStaticString", \ReflectionMethod::class],
			["{$class}->getStaticString", \ReflectionMethod::class],
			[[$class, 'getStaticString'], \ReflectionMethod::class],
			[Closure::fromCallable([$class, 'getStaticString']), \ReflectionFunction::class],
			[Closure::fromCallable([$class, 'getStaticString']), \ReflectionMethod::class, true],
			[[$c, 'getStaticString'], \ReflectionMethod::class],
			[Closure::fromCallable([$c, 'getStaticString']), \ReflectionFunction::class],
			[Closure::fromCallable([$c, 'getStaticString']), \ReflectionMethod::class, true],
			["{$class}::getProtectedInteger", \ReflectionMethod::class],
			["{$class}->getProtectedInteger", \ReflectionMethod::class],
			[[$class, 'getProtectedInteger'], \ReflectionMethod::class],
			[[$c, 'getProtectedInteger'], \ReflectionMethod::class],
			[$c->getGetProtectedIntegerClosure(), \ReflectionFunction::class],
			[$c->getGetProtectedIntegerClosure(), \ReflectionMethod::class, true],
			["{$class}::getProtectedStaticInteger", \ReflectionMethod::class],
			["{$class}->getProtectedStaticInteger", \ReflectionMethod::class],
			[[$class, 'getProtectedStaticInteger'], \ReflectionMethod::class],
			[$class::getGetProtectedStaticIntegerClosure(), \ReflectionFunction::class],
			[$class::getGetProtectedStaticIntegerClosure(), \ReflectionMethod::class, true],
			[[$c, 'getProtectedStaticInteger'], \ReflectionMethod::class],
			[$c->getGetProtectedStaticIntegerClosure(), \ReflectionFunction::class],
			[$c->getGetProtectedStaticIntegerClosure(), \ReflectionMethod::class, true],
			["{$class}::getPrivateBoolean", \ReflectionMethod::class],
			["{$class}->getPrivateBoolean", \ReflectionMethod::class],
			[[$class, 'getPrivateBoolean'], \ReflectionMethod::class],
			[[$c, 'getPrivateBoolean'], \ReflectionMethod::class],
			[$c->getGetPrivateBooleanClosure(), \ReflectionFunction::class],
			[$c->getGetPrivateBooleanClosure(), \ReflectionMethod::class, true],
			["{$class}::getPrivateStaticBoolean", \ReflectionMethod::class],
			["{$class}->getPrivateStaticBoolean", \ReflectionMethod::class],
			[[$class, 'getPrivateStaticBoolean'], \ReflectionMethod::class],
			[$class::getGetPrivateStaticBooleanClosure(), \ReflectionFunction::class],
			[$class::getGetPrivateStaticBooleanClosure(), \ReflectionMethod::class, true],
			[[$c, 'getPrivateStaticBoolean'], \ReflectionMethod::class],
			[$c->getGetPrivateStaticBooleanClosure(), \ReflectionFunction::class],
			[$c->getGetPrivateStaticBooleanClosure(), \ReflectionMethod::class, true],
			["{$class_abstract}::getString", \ReflectionMethod::class],
			[[$class_abstract, 'getString'], \ReflectionMethod::class],
			["{$class_abstract}::getStaticString", \ReflectionMethod::class],
			[[$class_abstract, 'getStaticString'], \ReflectionMethod::class],
			["{$class_abstract}::getProtectedInteger", \ReflectionMethod::class],
			[[$class_abstract, 'getProtectedInteger'], \ReflectionMethod::class],
			["{$class_abstract}::getProtectedStaticInteger", \ReflectionMethod::class],
			[[$class_abstract, 'getProtectedStaticInteger'], \ReflectionMethod::class],
			["{$interface}::getString", \ReflectionMethod::class],
			[[$interface, 'getString'], \ReflectionMethod::class],
			["{$interface}::getStaticString", \ReflectionMethod::class],
			[[$interface, 'getStaticString'], \ReflectionMethod::class]
		];
	}
	
	/**
	 * Test <code>reflection</code> method expecting an <code>InvalidFunction</code> exception to be thrown.
	 * 
	 * @testdox Call::reflection({$function}) --> InvalidFunction exception
	 * @dataProvider provideValidateData_Exception_InvalidFunction
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @return void
	 */
	public function testReflection_Exception_InvalidFunction($function): void
	{
		$this->expectException(Exceptions\InvalidFunction::class);
		try {
			UCall::reflection($function);
		} catch (Exceptions\InvalidFunction $exception) {
			$this->assertSame($function, $exception->function);
			throw $exception;
		}
	}
	
	/**
	 * Test <code>reflection</code> method with <var>$no_throw</var> set to boolean <code>true</code>, 
	 * expecting <code>null</code> to be returned.
	 * 
	 * @testdox Call::reflection({$function}, no_throw: true) === false
	 * @dataProvider provideValidateData_Exception_InvalidFunction
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @return void
	 */
	public function testReflection_NoThrow_Null($function): void
	{
		$this->assertNull(UCall::reflection($function, no_throw: true));
	}
	
	/**
	 * Test <code>hash</code> method.
	 * 
	 * @testdox Call::hash({$function}, '$algorithm') === '$expected'
	 * @dataProvider provideHashData
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param string $algorithm
	 * <p>The method <var>$algorithm</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testHash($function, string $algorithm, string $expected): void
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
	public function provideHashData(): array
	{
		//initialize
		$c = new CallTest_Class();
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', 'MD5', '73d3a702db472629f27b06ac8f056476'],
			['strlen', 'SHA1', '6c19df52f4536474beeb594b4c186a34750bfbba'],
			[Closure::fromCallable('strlen'), 'MD5', '73d3a702db472629f27b06ac8f056476'],
			[Closure::fromCallable('strlen'), 'SHA1', '6c19df52f4536474beeb594b4c186a34750bfbba'],
			[function () {}, 'MD5', 'bac1ff6c096124a515b64b42d93c80f3'],
			[function () {}, 'SHA1', '339638c04736145b5d2b3c2cb5f6b855cca59282'],
			[new CallTest_InvokeableClass(), 'MD5', '06678054507a08aa3179b82ad631ef77'],
			[new CallTest_InvokeableClass(), 'SHA1', 'cbb1e245087d78bcf998a89555f3517ceb491114'],
			[Closure::fromCallable(new CallTest_InvokeableClass()), 'MD5', '06678054507a08aa3179b82ad631ef77'],
			[Closure::fromCallable(new CallTest_InvokeableClass()), 'SHA1', 'cbb1e245087d78bcf998a89555f3517ceb491114'],
			[[$c, 'getString'], 'MD5', 'bd30850066e2deb385eae54d1369edfb'],
			[[$c, 'getString'], 'SHA1', 'b9a57503eae0f1f314b0cdb601643ba0425831be'],
			[[$class, 'getString'], 'MD5', 'bd30850066e2deb385eae54d1369edfb'],
			[[$class, 'getString'], 'SHA1', 'b9a57503eae0f1f314b0cdb601643ba0425831be'],
			[[$class, 'getStaticString'], 'MD5', '606b220c861176152934f9382769c599'],
			[[$class, 'getStaticString'], 'SHA1', 'b159051679c6240c133cdef3c0580e94f7c82910'],
			[Closure::fromCallable([$c, 'getString']), 'MD5', 'bd30850066e2deb385eae54d1369edfb'],
			[Closure::fromCallable([$c, 'getString']), 'SHA1', 'b9a57503eae0f1f314b0cdb601643ba0425831be'],
			[Closure::fromCallable([$class, 'getStaticString']), 'MD5', '606b220c861176152934f9382769c599'],
			[Closure::fromCallable([$class, 'getStaticString']), 'SHA1', 'b159051679c6240c133cdef3c0580e94f7c82910'],
			[[$class, 'getProtectedInteger'], 'MD5', 'dd99454f0ed6bdf40cd53505eb95d82e'],
			[[$class, 'getProtectedInteger'], 'SHA1', '54a3af5a5a2e3834dc27b2330af720591f08bedd'],
			[[$class, 'getProtectedStaticInteger'], 'MD5', 'e133f75ff6daf5bcc1f49977f30b2539'],
			[[$class, 'getProtectedStaticInteger'], 'SHA1', '7eeb8eba694d404d4dd0ee373a440885e0656da9'],
			[$c->getGetProtectedIntegerClosure(), 'MD5', 'dd99454f0ed6bdf40cd53505eb95d82e'],
			[$c->getGetProtectedIntegerClosure(), 'SHA1', '54a3af5a5a2e3834dc27b2330af720591f08bedd'],
			[CallTest_Class::getGetProtectedStaticIntegerClosure(), 'MD5', 'e133f75ff6daf5bcc1f49977f30b2539'],
			[CallTest_Class::getGetProtectedStaticIntegerClosure(), 'SHA1', '7eeb8eba694d404d4dd0ee373a440885e0656da9'],
			[[$class, 'getPrivateBoolean'], 'MD5', '9a48386d5b2337a69be15ee81d72c001'],
			[[$class, 'getPrivateBoolean'], 'SHA1', 'd404611ae4b96908c71461a068754952d13a3879'],
			[[$class, 'getPrivateStaticBoolean'], 'MD5', '66dbcf40dcb6a4256ed221f7732a59d8'],
			[[$class, 'getPrivateStaticBoolean'], 'SHA1', '8b6eecb43fe5e566a15712f3e8a8976fe88aaf79'],
			[$c->getGetPrivateBooleanClosure(), 'MD5', '9a48386d5b2337a69be15ee81d72c001'],
			[$c->getGetPrivateBooleanClosure(), 'SHA1', 'd404611ae4b96908c71461a068754952d13a3879'],
			[CallTest_Class::getGetPrivateStaticBooleanClosure(), 'MD5', '66dbcf40dcb6a4256ed221f7732a59d8'],
			[CallTest_Class::getGetPrivateStaticBooleanClosure(), 'SHA1', '8b6eecb43fe5e566a15712f3e8a8976fe88aaf79'],
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
	 * @testdox Call::modifiers({$function}) === $expected
	 * @dataProvider provideModifiersData
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param string[] $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testModifiers($function, array $expected): void
	{
		$this->assertSame($expected, UCall::modifiers($function));
	}
	
	/**
	 * Provide <code>modifiers</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>modifiers</code> method data.</p>
	 */
	public function provideModifiersData(): array
	{
		//initialize
		$c = new CallTest_Class();
		$ci = new CallTest_InvokeableClass();
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', []],
			[function () {}, []],
			[$ci, ['public']],
			[Closure::fromCallable($ci), ['public']],
			[[$c, 'getString'], ['public']],
			[Closure::fromCallable([$c, 'getString']), ['public']],
			[[$class, 'getString'], ['public']],
			[[$class, 'getStaticString'], ['public', 'static']],
			[Closure::fromCallable([$class, 'getStaticString']), ['public', 'static']],
			[[$c, 'getProtectedInteger'], ['protected']],
			[$c->getGetProtectedIntegerClosure(), ['protected']],
			[[$class, 'getProtectedInteger'], ['protected']],
			[[$class, 'getProtectedStaticInteger'], ['protected', 'static']],
			[$class::getGetProtectedStaticIntegerClosure(), ['protected', 'static']],
			[[$c, 'getProtectedStaticInteger'], ['protected', 'static']],
			[$c->getGetProtectedStaticIntegerClosure(), ['protected', 'static']],
			[[$c, 'getPrivateBoolean'], ['private']],
			[$c->getGetPrivateBooleanClosure(), ['private']],
			[[$class, 'getPrivateBoolean'], ['private']],
			[[$class, 'getPrivateStaticBoolean'], ['private', 'static']],
			[$class::getGetPrivateStaticBooleanClosure(), ['private', 'static']],
			[[$c, 'getPrivateStaticBoolean'], ['private', 'static']],
			[$c->getGetPrivateStaticBooleanClosure(), ['private', 'static']],
			[[$c, 'getFinalString'], ['final', 'public']],
			[Closure::fromCallable([$c, 'getFinalString']), ['final', 'public']],
			[[$class, 'getFinalString'], ['final', 'public']],
			[[$class, 'getFinalStaticString'], ['final', 'public', 'static']],
			[[$c, 'getFinalStaticString'], ['final', 'public', 'static']],
			[Closure::fromCallable([$c, 'getFinalStaticString']), ['final', 'public', 'static']],
			[[$c, 'getFinalProtectedInteger'], ['final', 'protected']],
			[$c->getGetFinalProtectedIntegerClosure(), ['final', 'protected']],
			[[$class, 'getFinalProtectedInteger'], ['final', 'protected']],
			[[$class, 'getFinalProtectedStaticInteger'], ['final', 'protected', 'static']],
			[$class::getGetFinalProtectedStaticIntegerClosure(), ['final', 'protected', 'static']],
			[[$c, 'getFinalProtectedStaticInteger'], ['final', 'protected', 'static']],
			[$c->getGetFinalProtectedStaticIntegerClosure(), ['final', 'protected', 'static']],
			[[$class_abstract, 'getString'], ['abstract', 'public']],
			[[$class_abstract, 'getStaticString'], ['abstract', 'public', 'static']],
			[[$class_abstract, 'getProtectedInteger'], ['abstract', 'protected']],
			[[$class_abstract, 'getProtectedStaticInteger'], ['abstract', 'protected', 'static']],
			[[$class_abstract, 'getFinalString'], ['final', 'public']],
			[[$class_abstract, 'getFinalStaticString'], ['final', 'public', 'static']],
			[[$class_abstract, 'getFinalProtectedInteger'], ['final', 'protected']],
			[[$class_abstract, 'getFinalProtectedStaticInteger'], ['final', 'protected', 'static']],
			[[$interface, 'getString'], ['abstract', 'public']],
			[[$interface, 'getStaticString'], ['abstract', 'public', 'static']]
		];
	}
	
	/**
	 * Test <code>name</code> method.
	 * 
	 * @testdox Call::name({$function}, $full, $short) === {$expected}
	 * @dataProvider provideNameData
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
	public function testName($function, bool $full, bool $short, ?string $expected): void
	{
		$this->assertSame($expected, UCall::name($function, $full, $short));
	}
	
	/**
	 * Provide <code>name</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>name</code> method data.</p>
	 */
	public function provideNameData(): array
	{
		//initialize
		$c = new CallTest_Class();
		$ci = new CallTest_InvokeableClass();
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
			[Closure::fromCallable('strlen'), false, false, 'strlen'],
			[Closure::fromCallable('strlen'), false, true, 'strlen'],
			[Closure::fromCallable('strlen'), true, false, 'strlen'],
			[Closure::fromCallable('strlen'), true, true, 'strlen'],
			[function () {}, false, false, null],
			[function () {}, false, true, null],
			[function () {}, true, false, null],
			[function () {}, true, true, null],
			[$ci, false, false, '__invoke'],
			[$ci, false, true, '__invoke'],
			[$ci, true, false, "{$class_invokeable}::__invoke"],
			[$ci, true, true, 'CallTest_InvokeableClass::__invoke'],
			[Closure::fromCallable($ci), false, false, '__invoke'],
			[Closure::fromCallable($ci), false, true, '__invoke'],
			[Closure::fromCallable($ci), true, false, "{$class_invokeable}::__invoke"],
			[Closure::fromCallable($ci), true, true, 'CallTest_InvokeableClass::__invoke'],
			[[$c, 'getString'], false, false, 'getString'],
			[[$c, 'getString'], false, true, 'getString'],
			[[$c, 'getString'], true, false, "{$class}::getString"],
			[[$c, 'getString'], true, true, 'CallTest_Class::getString'],
			[Closure::fromCallable([$c, 'getString']), false, false, 'getString'],
			[Closure::fromCallable([$c, 'getString']), false, true, 'getString'],
			[Closure::fromCallable([$c, 'getString']), true, false, "{$class}::getString"],
			[Closure::fromCallable([$c, 'getString']), true, true, 'CallTest_Class::getString'],
			[[$class, 'getString'], false, false, 'getString'],
			[[$class, 'getString'], false, true, 'getString'],
			[[$class, 'getString'], true, false, "{$class}::getString"],
			[[$class, 'getString'], true, true, 'CallTest_Class::getString'],
			[[$class, 'getStaticString'], false, false, 'getStaticString'],
			[[$class, 'getStaticString'], false, true, 'getStaticString'],
			[[$class, 'getStaticString'], true, false, "{$class}::getStaticString"],
			[[$class, 'getStaticString'], true, true, 'CallTest_Class::getStaticString'],
			[Closure::fromCallable([$class, 'getStaticString']), false, false, 'getStaticString'],
			[Closure::fromCallable([$class, 'getStaticString']), false, true, 'getStaticString'],
			[Closure::fromCallable([$class, 'getStaticString']), true, false, "{$class}::getStaticString"],
			[Closure::fromCallable([$class, 'getStaticString']), true, true, 'CallTest_Class::getStaticString'],
			[[$class, 'getProtectedInteger'], false, false, 'getProtectedInteger'],
			[[$class, 'getProtectedInteger'], false, true, 'getProtectedInteger'],
			[[$class, 'getProtectedInteger'], true, false, "{$class}::getProtectedInteger"],
			[[$class, 'getProtectedInteger'], true, true, 'CallTest_Class::getProtectedInteger'],
			[$c->getGetProtectedIntegerClosure(), false, false, 'getProtectedInteger'],
			[$c->getGetProtectedIntegerClosure(), false, true, 'getProtectedInteger'],
			[$c->getGetProtectedIntegerClosure(), true, false, "{$class}::getProtectedInteger"],
			[$c->getGetProtectedIntegerClosure(), true, true, 'CallTest_Class::getProtectedInteger'],
			[[$class, 'getProtectedStaticInteger'], false, false, 'getProtectedStaticInteger'],
			[[$class, 'getProtectedStaticInteger'], false, true, 'getProtectedStaticInteger'],
			[[$class, 'getProtectedStaticInteger'], true, false, "{$class}::getProtectedStaticInteger"],
			[[$class, 'getProtectedStaticInteger'], true, true, 'CallTest_Class::getProtectedStaticInteger'],
			[$class::getGetProtectedStaticIntegerClosure(), false, false, 'getProtectedStaticInteger'],
			[$class::getGetProtectedStaticIntegerClosure(), false, true, 'getProtectedStaticInteger'],
			[$class::getGetProtectedStaticIntegerClosure(), true, false, "{$class}::getProtectedStaticInteger"],
			[$class::getGetProtectedStaticIntegerClosure(), true, true, 'CallTest_Class::getProtectedStaticInteger'],
			[[$class, 'getPrivateBoolean'], false, false, 'getPrivateBoolean'],
			[[$class, 'getPrivateBoolean'], false, true, 'getPrivateBoolean'],
			[[$class, 'getPrivateBoolean'], true, false, "{$class}::getPrivateBoolean"],
			[[$class, 'getPrivateBoolean'], true, true, 'CallTest_Class::getPrivateBoolean'],
			[$c->getGetPrivateBooleanClosure(), false, false, 'getPrivateBoolean'],
			[$c->getGetPrivateBooleanClosure(), false, true, 'getPrivateBoolean'],
			[$c->getGetPrivateBooleanClosure(), true, false, "{$class}::getPrivateBoolean"],
			[$c->getGetPrivateBooleanClosure(), true, true, 'CallTest_Class::getPrivateBoolean'],
			[[$class, 'getPrivateStaticBoolean'], false, false, 'getPrivateStaticBoolean'],
			[[$class, 'getPrivateStaticBoolean'], false, true, 'getPrivateStaticBoolean'],
			[[$class, 'getPrivateStaticBoolean'], true, false, "{$class}::getPrivateStaticBoolean"],
			[[$class, 'getPrivateStaticBoolean'], true, true, 'CallTest_Class::getPrivateStaticBoolean'],
			[$class::getGetPrivateStaticBooleanClosure(), false, false, 'getPrivateStaticBoolean'],
			[$class::getGetPrivateStaticBooleanClosure(), false, true, 'getPrivateStaticBoolean'],
			[$class::getGetPrivateStaticBooleanClosure(), true, false, "{$class}::getPrivateStaticBoolean"],
			[$class::getGetPrivateStaticBooleanClosure(), true, true, 'CallTest_Class::getPrivateStaticBoolean'],
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
	 * @testdox Call::parameters({$function}, $flags) === $expected
	 * @dataProvider provideParametersData
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param array $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testParameters($function, int $flags, array $expected): void
	{
		$this->assertSame($expected, UCall::parameters($function, $flags));
	}
	
	/**
	 * Provide <code>parameters</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>parameters</code> method data.</p>
	 */
	public function provideParametersData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', 0x00, ['string $string']],
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
				'int $cint = 1200', 'bool &$enable = true', '?stdClass $std = null', '$flags = 2', '...$p']],
			[function (CallTest_Class|string $a, int|\stdClass|null $b, bool|float|object|array $e = false) {}, 0x00,
				[$class . '|string $a', 'stdClass|int|null $b', 'object|array|float|bool $e = false']],
			[function (CallTest_Class|string $a, int|\stdClass|null $b, bool|float|object|array $e = false) {},
				UCall::PARAMETERS_TYPES_SHORT_NAMES,
				['CallTest_Class|string $a', 'stdClass|int|null $b', 'object|array|float|bool $e = false']],
			[function (CallTest_Class|string $a, int|\stdClass|null $b, bool|float|object|array $e = false) {},
				UCall::PARAMETERS_NAMESPACES_LEADING_SLASH,
				['\\' . $class . '|string $a', '\\stdClass|int|null $b', 'object|array|float|bool $e = false']],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				0x00, [$class_abstract . '|' . $interface . '|null $aci', '?' . $class . ' &$c', 'mixed $m = 123']],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				UCall::PARAMETERS_TYPES_SHORT_NAMES,
				['CallTest_AbstractClass|CallTest_Interface|null $aci' , '?CallTest_Class &$c', 'mixed $m = 123']],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				UCall::PARAMETERS_NAMESPACES_LEADING_SLASH,
				['\\' . $class_abstract . '|\\' . $interface . '|null $aci', '?\\' . $class . ' &$c',
				'mixed $m = 123']],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				UCall::PARAMETERS_NO_MIXED_TYPE,
				[$class_abstract . '|' . $interface . '|null $aci', '?' . $class . ' &$c', '$m = 123']],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				UCall::PARAMETERS_TYPES_SHORT_NAMES | UCall::PARAMETERS_NAMESPACES_LEADING_SLASH,
				['CallTest_AbstractClass|CallTest_Interface|null $aci' , '?CallTest_Class &$c', 'mixed $m = 123']],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				UCall::PARAMETERS_TYPES_SHORT_NAMES | UCall::PARAMETERS_NO_MIXED_TYPE,
				['CallTest_AbstractClass|CallTest_Interface|null $aci' , '?CallTest_Class &$c', '$m = 123']]
		];
	}
	
	/**
	 * Test <code>type</code> method.
	 * 
	 * @testdox Call::type({$function}, $flags) === '$expected'
	 * @dataProvider provideTypeData
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testType($function, int $flags, string $expected): void
	{
		$this->assertSame($expected, UCall::type($function, $flags));
	}
	
	/**
	 * Provide <code>type</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>type</code> method data.</p>
	 */
	public function provideTypeData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', 0x00, 'int'],
			[function () {}, 0x00, 'mixed'],
			[function () {}, UCall::TYPE_NO_MIXED, ''],
			[function (): mixed {}, 0x00, 'mixed'],
			[function (): mixed {}, UCall::TYPE_NO_MIXED, ''],
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
				'?CallTest_Interface'],
			[function (): CallTest_Class|string {}, 0x00, "{$class}|string"],
			[function (): CallTest_Class|string {}, UCall::TYPE_SHORT_NAME, 'CallTest_Class|string'],
			[function (): CallTest_Class|string {}, UCall::TYPE_NAMESPACE_LEADING_SLASH, "\\{$class}|string"],
			[function (): CallTest_Class|string {}, UCall::TYPE_SHORT_NAME | UCall::TYPE_NAMESPACE_LEADING_SLASH,
				'CallTest_Class|string'],
			[function (): int|\stdClass|null {}, 0x00, 'stdClass|int|null'],
			[function (): int|\stdClass|null {}, UCall::TYPE_NAMESPACE_LEADING_SLASH, '\\stdClass|int|null'],
			[function (): bool|float|object|array {}, 0x00, 'object|array|float|bool'],
			[function (): CallTest_AbstractClass|null|CallTest_Interface {}, 0x00, 
				"{$class_abstract}|{$interface}|null"],
			[function (): CallTest_AbstractClass|null|CallTest_Interface {}, UCall::TYPE_SHORT_NAME,
				'CallTest_AbstractClass|CallTest_Interface|null'],
			[function (): CallTest_AbstractClass|null|CallTest_Interface {}, UCall::TYPE_NAMESPACE_LEADING_SLASH,
				"\\{$class_abstract}|\\{$interface}|null"],
			[function (): CallTest_Class|null {}, 0x00, "?{$class}"],
			[function (): CallTest_Class|null {}, UCall::TYPE_SHORT_NAME, '?CallTest_Class'],
			[function (): CallTest_Class|null {}, UCall::TYPE_NAMESPACE_LEADING_SLASH, "?\\{$class}"]
		];
	}
	
	/**
	 * Test <code>header</code> method.
	 * 
	 * @testdox Call::header({$function}, $flags) === '$expected'
	 * @dataProvider provideHeaderData
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testHeader($function, int $flags, string $expected): void
	{
		$this->assertSame($expected, UCall::header($function, $flags));
	}
	
	/**
	 * Provide <code>header</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>header</code> method data.</p>
	 */
	public function provideHeaderData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', 0x00, 'function strlen(string $string): int'],
			[function () {}, 0x00, 'function (): mixed'],
			[function () {}, UCall::HEADER_NO_MIXED_TYPE, 'function ()'],
			[function (): mixed {}, 0x00, 'function (): mixed'],
			[function (): mixed {}, UCall::HEADER_NO_MIXED_TYPE, 'function ()'],
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
				'...$p): ?CallTest_Interface'],
			[function (): CallTest_Class|string {}, 0x00, "function (): {$class}|string"],
			[function (): CallTest_Class|string {}, UCall::HEADER_TYPES_SHORT_NAMES,
				'function (): CallTest_Class|string'],
			[function (): CallTest_Class|string {}, UCall::HEADER_NAMESPACES_LEADING_SLASH,
				"function (): \\{$class}|string"],
			[function (): CallTest_Class|string {},
				UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (): CallTest_Class|string'],
			[function (): int|\stdClass|null {}, 0x00, 'function (): stdClass|int|null'],
			[function (): int|\stdClass|null {}, UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (): \\stdClass|int|null'],
			[function (): bool|float|object|array {}, 0x00, 'function (): object|array|float|bool'],
			[function (): CallTest_AbstractClass|null|CallTest_Interface {}, 0x00,
				"function (): {$class_abstract}|{$interface}|null"],
			[function (): CallTest_AbstractClass|null|CallTest_Interface {}, UCall::HEADER_TYPES_SHORT_NAMES,
				'function (): CallTest_AbstractClass|CallTest_Interface|null'],
			[function (): CallTest_AbstractClass|null|CallTest_Interface {}, UCall::HEADER_NAMESPACES_LEADING_SLASH,
				"function (): \\{$class_abstract}|\\{$interface}|null"],
			[function (): CallTest_Class|null {}, 0x00, "function (): ?{$class}"],
			[function (): CallTest_Class|null {}, UCall::HEADER_TYPES_SHORT_NAMES, 'function (): ?CallTest_Class'],
			[function (): CallTest_Class|null {}, UCall::HEADER_NAMESPACES_LEADING_SLASH, "function (): ?\\{$class}"],
			[function (CallTest_Class|string $a, int|\stdClass|null $b, bool|float|object|array $e = false): void {}, 
				0x00,
				'function (' . $class . '|string $a, stdClass|int|null $b, object|array|float|bool $e = false): void'],
			[function (CallTest_Class|string $a, int|\stdClass|null $b, bool|float|object|array $e = false): void {},
				UCall::HEADER_TYPES_SHORT_NAMES,
				'function (CallTest_Class|string $a, stdClass|int|null $b, object|array|float|bool $e = false): void'],
			[function (CallTest_Class|string $a, int|\stdClass|null $b, bool|float|object|array $e = false): void {},
				UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (\\' . $class . '|string $a, \\stdClass|int|null $b, object|array|float|bool $e = false): ' . 
				'void'],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				0x00, 
				'function (' . $class_abstract . '|' . $interface . '|null $aci, ?' . $class . ' &$c, ' . 
				'mixed $m = 123): mixed'],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				UCall::HEADER_TYPES_SHORT_NAMES,
				'function (CallTest_AbstractClass|CallTest_Interface|null $aci, ?CallTest_Class &$c, ' . 
				'mixed $m = 123): mixed'],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (\\' . $class_abstract . '|\\' . $interface . '|null $aci, ?\\' . $class . ' &$c, ' . 
				'mixed $m = 123): mixed'],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				UCall::HEADER_NO_MIXED_TYPE,
				'function (' . $class_abstract . '|' . $interface . '|null $aci, ?' . $class . ' &$c, $m = 123)'],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NAMESPACES_LEADING_SLASH,
				'function (CallTest_AbstractClass|CallTest_Interface|null $aci, ?CallTest_Class &$c, ' . 
				'mixed $m = 123): mixed'],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				UCall::HEADER_TYPES_SHORT_NAMES | UCall::HEADER_NO_MIXED_TYPE,
				'function (CallTest_AbstractClass|CallTest_Interface|null $aci, ?CallTest_Class &$c, $m = 123)']
		];
	}
	
	/**
	 * Test <code>body</code> method.
	 * 
	 * @testdox Call::body({$function}) === '$expected'
	 * @dataProvider provideBodyData
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testBody($function, string $expected): void
	{
		$this->assertSame($expected, UCall::body($function));
	}
	
	/**
	 * Provide <code>body</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>body</code> method data.</p>
	 */
	public function provideBodyData(): array
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
	 * @testdox Call::source({$function}, $flags) === '$expected'
	 * @dataProvider provideSourceData
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testSource($function, int $flags, string $expected): void
	{
		$this->assertSame($expected, UCall::source($function, $flags));
	}
	
	/**
	 * Provide <code>source</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>source</code> method data.</p>
	 */
	public function provideSourceData(): array
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
			['strlen', 0x00, 'function strlen(string $string): int {}'],
			[function () {}, 0x00, 'function (): mixed {}'],
			[function () {}, UCall::SOURCE_NO_MIXED_TYPE, 'function () {}'],
			[function (): mixed {}, 0x00, 'function (): mixed {}'],
			[function (): mixed {}, UCall::SOURCE_NO_MIXED_TYPE, 'function () {}'],
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
				'...$p): ?CallTest_Interface' . $dostuff_body],
			[function (): CallTest_Class|string {}, 0x00, "function (): {$class}|string {}"],
			[function (): CallTest_Class|string {}, UCall::SOURCE_TYPES_SHORT_NAMES,
				'function (): CallTest_Class|string {}'],
			[function (): CallTest_Class|string {}, UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				"function (): \\{$class}|string {}"],
			[function (): CallTest_Class|string {},
				UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (): CallTest_Class|string {}'],
			[function (): int|\stdClass|null {}, 0x00, 'function (): stdClass|int|null {}'],
			[function (): int|\stdClass|null {}, UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (): \\stdClass|int|null {}'],
			[function (): bool|float|object|array {}, 0x00, 'function (): object|array|float|bool {}'],
			[function (): CallTest_AbstractClass|null|CallTest_Interface {}, 0x00,
				"function (): {$class_abstract}|{$interface}|null {}"],
			[function (): CallTest_AbstractClass|null|CallTest_Interface {}, UCall::SOURCE_TYPES_SHORT_NAMES,
				'function (): CallTest_AbstractClass|CallTest_Interface|null {}'],
			[function (): CallTest_AbstractClass|null|CallTest_Interface {}, UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				"function (): \\{$class_abstract}|\\{$interface}|null {}"],
			[function (): CallTest_Class|null {}, 0x00, "function (): ?{$class} {}"],
			[function (): CallTest_Class|null {}, UCall::SOURCE_TYPES_SHORT_NAMES, 'function (): ?CallTest_Class {}'],
			[function (): CallTest_Class|null {}, UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				"function (): ?\\{$class} {}"],
			[function (CallTest_Class|string $a, int|\stdClass|null $b, bool|float|object|array $e = false): void {}, 
				0x00,
				'function (' . $class . '|string $a, stdClass|int|null $b, object|array|float|bool $e = false): ' . 
				'void {}'],
			[function (CallTest_Class|string $a, int|\stdClass|null $b, bool|float|object|array $e = false): void {},
				UCall::SOURCE_TYPES_SHORT_NAMES,
				'function (CallTest_Class|string $a, stdClass|int|null $b, object|array|float|bool $e = false): ' . 
				'void {}'],
			[function (CallTest_Class|string $a, int|\stdClass|null $b, bool|float|object|array $e = false): void {},
				UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (\\' . $class . '|string $a, \\stdClass|int|null $b, object|array|float|bool $e = false): ' . 
				'void {}'],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				0x00, 
				'function (' . $class_abstract . '|' . $interface . '|null $aci, ?' . $class . ' &$c, ' . 
				'mixed $m = 123): mixed {}'],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				UCall::SOURCE_TYPES_SHORT_NAMES,
				'function (CallTest_AbstractClass|CallTest_Interface|null $aci, ?CallTest_Class &$c, ' . 
				'mixed $m = 123): mixed {}'],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (\\' . $class_abstract . '|\\' . $interface . '|null $aci, ?\\' . $class . ' &$c, ' . 
				'mixed $m = 123): mixed {}'],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				UCall::SOURCE_NO_MIXED_TYPE,
				'function (' . $class_abstract . '|' . $interface . '|null $aci, ?' . $class . ' &$c, $m = 123) {}'],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NAMESPACES_LEADING_SLASH,
				'function (CallTest_AbstractClass|CallTest_Interface|null $aci, ?CallTest_Class &$c, ' . 
				'mixed $m = 123): mixed {}'],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				UCall::SOURCE_TYPES_SHORT_NAMES | UCall::SOURCE_NO_MIXED_TYPE,
				'function (CallTest_AbstractClass|CallTest_Interface|null $aci, ?CallTest_Class &$c, $m = 123) {}']
		];
	}
	
	/**
	 * Test <code>signature</code> method.
	 * 
	 * @testdox Call::signature({$function}) === '$expected'
	 * @dataProvider provideSignatureData
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testSignature($function, string $expected): void
	{
		$this->assertSame($expected, UCall::signature($function));
	}
	
	/**
	 * Provide <code>signature</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>signature</code> method data.</p>
	 */
	public function provideSignatureData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', '( string ): int'],
			[function () {}, '(): mixed'],
			[function (): mixed {}, '(): mixed'],
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
				'[, &bool [, ?stdClass [, mixed [, ...mixed ]]]]]]]): ?' . $interface],
			[function (): CallTest_Class|string {}, "(): {$class}|string"],
			[function (): int|\stdClass|null {}, '(): stdClass|int|null'],
			[function (): bool|float|object|array {}, '(): object|array|float|bool'],
			[function (): CallTest_AbstractClass|null|CallTest_Interface {}, "(): {$class_abstract}|{$interface}|null"],
			[function (): CallTest_Class|null {}, "(): ?{$class}"],
			[function (CallTest_Class|string $a, int|\stdClass|null $b, bool|float|object|array $e = false): void {}, 
				'( ' . $class . '|string , stdClass|int|null [, object|array|float|bool ]): void'],
			[function (CallTest_AbstractClass|null|CallTest_Interface $aci, CallTest_Class|null &$c, mixed $m = 123) {},
				'( ' . $class_abstract . '|' . $interface . '|null , &?' . $class . ' [, mixed ]): mixed']
		];
	}
	
	/**
	 * Test <code>compatible</code> method.
	 * 
	 * @testdox Call::compatible({$function}, {$template}) === $expected
	 * @dataProvider provideCompatibleData
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param callable|array|string $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @param bool $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testCompatible($function, $template, bool $expected): void
	{
		$this->assertSame($expected, UCall::compatible($function, $template));
	}
	
	/**
	 * Provide <code>compatible</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>compatible</code> method data.</p>
	 */
	public function provideCompatibleData(): array
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
			['strlen', function ($string) {}, false],
			['strlen', function (string $string) {}, true],
			['strlen', function ($string): int {}, false],
			['strlen', function (string $string): int {}, true],
			[function ($string) {}, 'strlen', false],
			[function (string $string) {}, 'strlen', false],
			[function ($string): int {}, 'strlen', true],
			[function (string $string): int {}, 'strlen', true],
			[function () {}, function () {}, true],
			[function () {}, function (): mixed {}, true],
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
			[function (): mixed {}, function () {}, true],
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
			[function ($m) {}, function ($m) {}, true],
			[function ($m) {}, function (mixed $m) {}, true],
			[function (mixed $m) {}, function ($m) {}, true],
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
			[[$interface, 'setStaticString'], function (string $s = ''): void {}, false],
			[function (): bool|float|object|array {}, function (): void {}, true],
			[function (): void {}, function (): bool|float|object|array {}, false],
			[function (): bool|float|object|array {}, function (): bool|float|object|array {}, true],
			[function (): bool|float|object|array {}, function (): bool|float|object {}, false],
			[function (): bool|float|object {}, function (): bool|float|object|array {}, true],
			[function (): bool|float|object|array {}, function (): mixed {}, true],
			[function (): mixed {}, function (): bool|float|object|array {}, false],
			[function (): CallTest_Class {}, function (): CallTest_Class|string {}, true],
			[function (): string {}, function (): CallTest_Class|string {}, true],
			[function (): CallTest_Class|string {}, function (): CallTest_Class {}, false],
			[function (): CallTest_Class|string {}, function (): string {}, false],
			[function (): CallTest_Class2|string {}, function (): CallTest_Class|string {}, true],
			[function (): CallTest_Class|string {}, function (): CallTest_Class2|string {}, false],
			[function (): CallTest_Class|string {}, function (): CallTest_Class|string|null {}, true],
			[function (): CallTest_Class|string|null {}, function (): CallTest_Class|string {}, false],
			[function (): CallTest_Class|CallTest_Interface {}, function (): object {}, true],
			[function (): object {}, function (): CallTest_Class|CallTest_Interface {}, false],
			[function (): CallTest_Class {}, function (): CallTest_Class|CallTest_Interface {}, true],
			[function (): CallTest_Interface {}, function (): CallTest_Class|CallTest_Interface {}, true],
			[function (): CallTest_Class|CallTest_Interface {}, function (): CallTest_Class {}, false],
			[function (): CallTest_Class|CallTest_Interface {}, function (): CallTest_Interface {}, false],
			[function (): ?int {}, function (): int|float|null {}, true],
			[function (): int|float|null {}, function (): ?int {}, false],
			[function (): int|float {}, function (): ?int {}, false],
			[function (): ?int {}, function (): int|float {}, false],
			[function (bool|float|object|array $a) {}, function (bool|float|object|array $a) {}, true],
			[function (bool|float|object|array $a) {}, function (bool|float|object $a) {}, true],
			[function (bool|float|object $a) {}, function (bool|float|object|array $a) {}, false],
			[function (mixed $a) {}, function (bool|float|object|array $a) {}, true],
			[function (bool|float|object|array $a) {}, function (mixed $a) {}, false],
			[function (CallTest_Class|string $a) {}, function (CallTest_Class $a) {}, true],
			[function (CallTest_Class|string $a) {}, function (string $a) {}, true],
			[function (CallTest_Class $a) {}, function (CallTest_Class|string $a) {}, false],
			[function (string $a) {}, function (CallTest_Class|string $a) {}, false],
			[function (CallTest_Class|string $a) {}, function (CallTest_Class2|string $a) {}, true],
			[function (CallTest_Class2|string $a) {}, function (CallTest_Class|string $a) {}, false],
			[function (CallTest_Class|string|null $a) {}, function (CallTest_Class|string $a) {}, true],
			[function (CallTest_Class|string $a) {}, function (CallTest_Class|string|null $a) {}, false],
			[function (object $a) {}, function (CallTest_Class|CallTest_Interface $a) {}, true],
			[function (CallTest_Class|CallTest_Interface $a) {}, function (object $a) {}, false],
			[function (CallTest_Class|CallTest_Interface $a) {}, function (CallTest_Class $a) {}, true],
			[function (CallTest_Class|CallTest_Interface $a) {}, function (CallTest_Interface $a) {}, true],
			[function (CallTest_Class $a) {}, function (CallTest_Class|CallTest_Interface $a) {}, false],
			[function (CallTest_Interface $a) {}, function (CallTest_Class|CallTest_Interface $a) {}, false],
			[function (int|float|null $a) {}, function (?int $a) {}, true],
			[function (?int $a) {}, function (int|float|null $a) {}, false],
			[function (int|float $a) {}, function (?int $a) {}, false],
			[function (?int $a) {}, function (int|float $a) {}, false]
		];
	}
	
	/**
	 * Test <code>assert</code> method.
	 * 
	 * @testdox Call::assert('foobar', {$function}, {$template}) === void
	 * @dataProvider provideAssertData
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param callable|array|string $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testAssert($function, $template): void
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
	public function provideAssertData(): array
	{
		$data = [];
		foreach ($this->provideCompatibleData() as $i => $datum) {
			if ($datum[2]) {
				$data[$i] = [$datum[0], $datum[1]];
			}
		}
		return $data;
	}
	
	/**
	 * Test <code>assert</code> method expecting an <code>AssertionFailed</code> exception to be thrown.
	 * 
	 * @testdox Call::assert('foobar', {$function}, {$template}) --> AssertionFailed exception
	 * @dataProvider provideAssertData_Exception_AssertionFailed
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param callable|array|string $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testAssert_Exception_AssertionFailed($function, $template): void
	{
		$this->expectException(Exceptions\AssertionFailed::class);
		try {
			UCall::assert('foobar', $function, $template);
		} catch (Exceptions\AssertionFailed $exception) {
			$this->assertSame('foobar', $exception->name);
			$this->assertSame($function, $exception->function);
			$this->assertSame($template, $exception->template);
			$this->assertSame($this, $exception->source_object_class);
			$this->assertSame('testAssert_Exception_AssertionFailed', $exception->source_function_name);
			throw $exception;
		}
	}
	
	/**
	 * Test <code>assert</code> method with <var>$no_throw</var> set to boolean <code>true</code>, 
	 * expecting boolean <code>false</code> to be returned.
	 * 
	 * @testdox Call::assert('foobar', {$function}, {$template}, true) === false
	 * @dataProvider provideAssertData_Exception_AssertionFailed
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param callable|array|string $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testAssert_NoThrow_False($function, $template): void
	{
		$this->assertFalse(UCall::assert('foobar', $function, $template, true));
	}
	
	/**
	 * Provide <code>assert</code> method data for an <code>AssertionFailed</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The provided <code>assert</code> method data for an <code>AssertionFailed</code> exception to be thrown.</p>
	 */
	public function provideAssertData_Exception_AssertionFailed(): array
	{
		$data = [];
		foreach ($this->provideCompatibleData() as $i => $datum) {
			if (!$datum[2]) {
				$data[$i] = [$datum[0], $datum[1]];
			}
		}
		return $data;
	}
	
	/**
	 * Test <code>assert</code> method in a production environment.
	 * 
	 * @testdox Call::assert('foobar', {$function}, {$template}) === void [production]
	 * @dataProvider provideAssertData_Environment_Production
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param callable|array|string $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testAssert_Environment_Production($function, $template): void
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
	public function provideAssertData_Environment_Production(): array
	{
		$data = [];
		foreach ($this->provideCompatibleData() as $i => $datum) {
			$data[$i] = [$datum[0], $datum[1]];
		}
		return $data;
	}
	
	/**
	 * Test <code>object</code> method.
	 * 
	 * @testdox Call::object({$function}) === $expected
	 * @dataProvider provideObjectData
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param object|null $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testObject($function, ?object $expected): void
	{
		$this->assertSame($expected, UCall::object($function));
	}
	
	/**
	 * Provide <code>object</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>object</code> method data.</p>
	 */
	public function provideObjectData(): array
	{
		//initialize
		$c = new CallTest_Class();
		$ci = new CallTest_InvokeableClass();
		$ci2 = new CallTest_InvokeableClass2();
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', null],
			[Closure::fromCallable('strlen'), null],
			[function () {}, $this],
			[$ci, $ci],
			[$ci2, $ci2],
			[Closure::fromCallable($ci), $ci],
			[Closure::fromCallable($ci2), $ci2],
			[[$class, 'getString'], null],
			[[$class, 'getStaticString'], null],
			[[$class, 'getProtectedInteger'], null],
			[[$class, 'getProtectedStaticInteger'], null],
			[[$class, 'getPrivateBoolean'], null],
			[[$class, 'getPrivateStaticBoolean'], null],
			[[$c, 'getString'], $c],
			[[$c, 'getStaticString'], null],
			[[$c, 'getProtectedInteger'], $c],
			[[$c, 'getProtectedStaticInteger'], null],
			[[$c, 'getPrivateBoolean'], $c],
			[[$c, 'getPrivateStaticBoolean'], null],
			[Closure::fromCallable([$c, 'getString']), $c],
			[Closure::fromCallable([$c, 'getStaticString']), null],
			[$c->getGetProtectedIntegerClosure(), $c],
			[$c->getGetPrivateBooleanClosure(), $c],
			[$c->getGetProtectedStaticIntegerClosure(), null],
			[$c->getGetPrivateStaticBooleanClosure(), null],
			[$class::getGetProtectedStaticIntegerClosure(), null],
			[$class::getGetPrivateStaticBooleanClosure(), null],
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
	 * @testdox Call::class({$function}, $short) === {$expected}
	 * @dataProvider provideClassData
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param bool $short
	 * <p>The method <var>$short</var> parameter to test with.</p>
	 * @param string|null $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testClass($function, bool $short, ?string $expected): void
	{
		$this->assertSame($expected, UCall::class($function, $short));
	}
	
	/**
	 * Provide <code>class</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>class</code> method data.</p>
	 */
	public function provideClassData(): array
	{
		//initialize
		$c = new CallTest_Class();
		$ci = new CallTest_InvokeableClass();
		$ci2 = new CallTest_InvokeableClass2();
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', false, null],
			['strlen', true, null],
			[Closure::fromCallable('strlen'), false, null],
			[Closure::fromCallable('strlen'), true, null],
			[function () {}, false, static::class],
			[function () {}, true, 'CallTest'],
			[$ci, false, CallTest_InvokeableClass::class],
			[$ci, true, 'CallTest_InvokeableClass'],
			[$ci2, false, CallTest_InvokeableClass2::class],
			[$ci2, true, 'CallTest_InvokeableClass2'],
			[Closure::fromCallable($ci), false, CallTest_InvokeableClass::class],
			[Closure::fromCallable($ci), true, 'CallTest_InvokeableClass'],
			[Closure::fromCallable($ci2), false, CallTest_InvokeableClass2::class],
			[Closure::fromCallable($ci2), true, 'CallTest_InvokeableClass2'],
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
			[[$c, 'getString'], false, $class],
			[[$c, 'getString'], true, 'CallTest_Class'],
			[[$c, 'getStaticString'], false, $class],
			[[$c, 'getStaticString'], true, 'CallTest_Class'],
			[[$c, 'getProtectedInteger'], false, $class],
			[[$c, 'getProtectedInteger'], true, 'CallTest_Class'],
			[[$c, 'getProtectedStaticInteger'], false, $class],
			[[$c, 'getProtectedStaticInteger'], true, 'CallTest_Class'],
			[[$c, 'getPrivateBoolean'], false, $class],
			[[$c, 'getPrivateBoolean'], true, 'CallTest_Class'],
			[[$c, 'getPrivateStaticBoolean'], false, $class],
			[[$c, 'getPrivateStaticBoolean'], true, 'CallTest_Class'],
			[Closure::fromCallable([$c, 'getString']), false, $class],
			[Closure::fromCallable([$c, 'getString']), true, 'CallTest_Class'],
			[Closure::fromCallable([$c, 'getStaticString']), false, $class],
			[Closure::fromCallable([$c, 'getStaticString']), true, 'CallTest_Class'],
			[$c->getGetProtectedIntegerClosure(), false, $class],
			[$c->getGetProtectedIntegerClosure(), true, 'CallTest_Class'],
			[$c->getGetProtectedStaticIntegerClosure(), false, $class],
			[$c->getGetProtectedStaticIntegerClosure(), true, 'CallTest_Class'],
			[$class::getGetProtectedStaticIntegerClosure(), false, $class],
			[$class::getGetProtectedStaticIntegerClosure(), true, 'CallTest_Class'],
			[$c->getGetPrivateBooleanClosure(), false, $class],
			[$c->getGetPrivateBooleanClosure(), true, 'CallTest_Class'],
			[$c->getGetPrivateStaticBooleanClosure(), false, $class],
			[$c->getGetPrivateStaticBooleanClosure(), true, 'CallTest_Class'],
			[$class::getGetPrivateStaticBooleanClosure(), false, $class],
			[$class::getGetPrivateStaticBooleanClosure(), true, 'CallTest_Class'],
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
	
	/**
	 * Test <code>extension</code> method.
	 * 
	 * @testdox Call::extension({$function}) === {$expected}
	 * @dataProvider provideExtensionData
	 * 
	 * @param callable|array|string $function
	 * <p>The method <var>$function</var> parameter to test with.</p>
	 * @param string|null $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testExtension($function, ?string $expected): void
	{
		$this->assertSame($expected, UCall::extension($function));
	}
	
	/**
	 * Provide <code>extension</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>extension</code> method data.</p>
	 */
	public function provideExtensionData(): array
	{
		//initialize
		$class = CallTest_Class::class;
		$class_abstract = CallTest_AbstractClass::class;
		$interface = CallTest_Interface::class;
		
		//return
		return [
			['strlen', 'Core'],
			['mb_strlen', 'mbstring'],
			['json_encode', 'json'],
			[['ReflectionFunction', 'getName'], 'Reflection'],
			[[UCall::reflection('strlen'), 'getName'], 'Reflection'],
			[Closure::fromCallable('strlen'), 'Core'],
			[Closure::fromCallable('mb_strlen'), 'mbstring'],
			[Closure::fromCallable('json_encode'), 'json'],
			[Closure::fromCallable([UCall::reflection('strlen'), 'getName']), 'Reflection'],
			[function () {}, null],
			[new CallTest_InvokeableClass(), null],
			[[$class, 'getString'], null],
			[[$class, 'getStaticString'], null],
			[[$class, 'getProtectedInteger'], null],
			[[$class, 'getProtectedStaticInteger'], null],
			[[$class, 'getPrivateBoolean'], null],
			[[$class, 'getPrivateStaticBoolean'], null],
			[[$class_abstract, 'getString'], null],
			[[$class_abstract, 'getStaticString'], null],
			[[$class_abstract, 'getProtectedInteger'], null],
			[[$class_abstract, 'getProtectedStaticInteger'], null],
			[[$interface, 'getString'], null],
			[[$interface, 'getStaticString'], null]
		];
	}
	
	/**
	 * Test <code>evaluate</code> method.
	 * 
	 * @testdox Call::evaluate(&{$value}, {$template}) === true
	 * @dataProvider provideCoercionData
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param callable|array|string|null $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testEvaluate($value, $template): void
	{
		foreach ([false, true] as $nullable) {
			foreach ([false, true] as $assertive) {
				$v = $value;
				$this->assertTrue(UCall::evaluate($v, $template, $nullable, $assertive));
				$this->assertInstanceOf(Closure::class, $v);
			}
		}
	}
	
	/**
	 * Test <code>coerce</code> method.
	 * 
	 * @testdox Call::coerce({$value}, {$template}) === Closure
	 * @dataProvider provideCoercionData
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param callable|array|string|null $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testCoerce($value, $template): void
	{
		foreach ([false, true] as $nullable) {
			foreach ([false, true] as $assertive) {
				$this->assertInstanceOf(Closure::class, UCall::coerce($value, $template, $nullable, $assertive));
			}
		}
	}
	
	/**
	 * Test <code>processCoercion</code> method.
	 * 
	 * @testdox Call::processCoercion(&{$value}, {$template}) === true
	 * @dataProvider provideCoercionData
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param callable|array|string|null $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testProcessCoercion($value, $template): void
	{
		foreach ([false, true] as $nullable) {
			foreach ([false, true] as $assertive) {
				foreach ([false, true] as $no_throw) {
					$v = $value;
					$this->assertTrue(UCall::processCoercion($v, $template, $nullable, $assertive, $no_throw));
					$this->assertInstanceOf(Closure::class, $v);
				}
			}
		}
	}
	
	/**
	 * Provide coercion method data.
	 * 
	 * @return array
	 * <p>The provided coercion method data.</p>
	 */
	public function provideCoercionData(): array
	{
		return [
			['strlen', null],
			['strlen', 'strlen'],
			['strlen', function (string $str): int {}],
			[function () {}, null],
			[function () {}, function () {}],
			[function (int $i) {}, function (int $ii) {}],
			[function (int $i): bool {}, function (int $ii) {}],
			[function (int $i): bool {}, function (int $ii): ?bool {}],
			[function (int $i = 0): bool {}, function (int $ii): bool {}],
			[function (CallTest_Class $c): CallTest_Interface {},
				function (CallTest_Class2 $c): ?CallTest_Interface {}],
			[function (CallTest_Class $c): CallTest_InterfaceClass {},
				function (CallTest_Class2 $c): CallTest_Interface {}],
			[new CallTest_InvokeableClass(), null],
			[new CallTest_InvokeableClass(), function () {}],
			[new CallTest_InvokeableClass(), function (): ?CallTest_Class {}],
			[new CallTest_InvokeableClass2(), null],
			[new CallTest_InvokeableClass2(), function (string $s): void {}],
			[new CallTest_InvokeableClass2(), function (string $s = ''): void {}],
			[[new CallTest_Class(), 'getString'], null],
			[[new CallTest_Class(), 'getString'], function () {}],
			[[new CallTest_Class(), 'getString'], function (): string {}],
			[[new CallTest_Class(), 'setString'], null],
			[[new CallTest_Class(), 'setString'], function (string $s): void {}],
			[CallTest_Class::class . '::getStaticString', null],
			[CallTest_Class::class . '::getStaticString', function () {}],
			[CallTest_Class::class . '::getStaticString', function (): string {}],
			[CallTest_Class::class . '::setStaticString', null],
			[CallTest_Class::class . '::setStaticString', function (string $s): void {}],
			[[new CallTest_Class(), 'getStaticString'], null],
			[[new CallTest_Class(), 'getStaticString'], function () {}],
			[[new CallTest_Class(), 'getStaticString'], function (): string {}],
			[[new CallTest_Class(), 'setStaticString'], null],
			[[new CallTest_Class(), 'setStaticString'], function (string $s): void {}],
			[function (): bool|float|object|array {}, function (): void {}],
			[function (): bool|float|object|array {}, function (): bool|float|object|array {}],
			[function (): bool|float|object {}, function (): bool|float|object|array {}],
			[function (): bool|float|object|array {}, function (): mixed {}],
			[function (): CallTest_Class {}, function (): CallTest_Class|string {}],
			[function (): string {}, function (): CallTest_Class|string {}],
			[function (): CallTest_Class2|string {}, function (): CallTest_Class|string {}],
			[function (): CallTest_Class|string {}, function (): CallTest_Class|string|null {}],
			[function (): CallTest_Class|CallTest_Interface {}, function (): object {}],
			[function (): CallTest_Class {}, function (): CallTest_Class|CallTest_Interface {}],
			[function (): CallTest_Interface {}, function (): CallTest_Class|CallTest_Interface {}],
			[function (): ?int {}, function (): int|float|null {}],
			[function (bool|float|object|array $a) {}, function (bool|float|object|array $a) {}],
			[function (bool|float|object|array $a) {}, function (bool|float|object $a) {}],
			[function (mixed $a) {}, function (bool|float|object|array $a) {}],
			[function (CallTest_Class|string $a) {}, function (CallTest_Class $a) {}],
			[function (CallTest_Class|string $a) {}, function (string $a) {}],
			[function (CallTest_Class|string $a) {}, function (CallTest_Class2|string $a) {}],
			[function (CallTest_Class|string|null $a) {}, function (CallTest_Class|string $a) {}],
			[function (object $a) {}, function (CallTest_Class|CallTest_Interface $a) {}],
			[function (CallTest_Class|CallTest_Interface $a) {}, function (CallTest_Class $a) {}],
			[function (CallTest_Class|CallTest_Interface $a) {}, function (CallTest_Interface $a) {}],
			[function (int|float|null $a) {}, function (?int $a) {}]
		];
	}
	
	/**
	 * Test <code>evaluate</code> method with a <code>null</code> value.
	 * 
	 * @testdox Call::evaluate(&{null} --> &{null}, null, true) === true
	 * 
	 * @return void
	 */
	public function testEvaluate_Null(): void
	{
		foreach ([false, true] as $assertive) {
			$value = null;
			$this->assertTrue(UCall::evaluate($value, null, true, $assertive));
			$this->assertNull($value);
		}
	}
	
	/**
	 * Test <code>coerce</code> method with a <code>null</code> value.
	 * 
	 * @testdox Call::coerce({null}, null, true) === null
	 * 
	 * @return void
	 */
	public function testCoerce_Null(): void
	{
		foreach ([false, true] as $assertive) {
			$this->assertNull(UCall::coerce(null, null, true, $assertive));
		}
	}
	
	/**
	 * Test <code>processCoercion</code> method with a <code>null</code> value.
	 * 
	 * @testdox Call::processCoercion(&{null} --> &{null}, null, true) === true
	 * 
	 * @return void
	 */
	public function testProcessCoercion_Null(): void
	{
		foreach ([false, true] as $assertive) {
			foreach ([false, true] as $no_throw) {
				$value = null;
				$this->assertTrue(UCall::processCoercion($value, null, true, $assertive, $no_throw));
				$this->assertNull($value);
			}
		}
	}
	
	/**
	 * Test <code>evaluate</code> method expecting boolean <code>false</code> to be returned.
	 * 
	 * @testdox Call::evaluate(&{$value}, {$template}) === false
	 * @dataProvider provideCoercionData_Exception_CoercionFailed
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param callable|array|string|null $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testEvaluate_False($value, $template): void
	{
		foreach ([false, true] as $nullable) {
			$v = $value;
			$this->assertFalse(UCall::evaluate($v, $template, $nullable));
			$this->assertSame($value, $v);
		}
	}
	
	/**
	 * Test <code>coerce</code> method expecting a <code>CoercionFailed</code> exception to be thrown.
	 * 
	 * @testdox Call::coerce({$value}, {$template}) --> CoercionFailed exception
	 * @dataProvider provideCoercionData_Exception_CoercionFailed
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param callable|array|string|null $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testCoerce_Exception_CoercionFailed($value, $template): void
	{
		$this->expectException(Exceptions\CoercionFailed::class);
		try {
			UCall::coerce($value, $template);
		} catch (Exceptions\CoercionFailed $exception) {
			$this->assertSame($value, $exception->getValue());
			throw $exception;
		}
	}
	
	/**
	 * Test <code>processCoercion</code> method expecting a <code>CoercionFailed</code> exception to be thrown.
	 * 
	 * @testdox Call::processCoercion(&{$value}, {$template}) --> CoercionFailed exception
	 * @dataProvider provideCoercionData_Exception_CoercionFailed
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param callable|array|string|null $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testProcessCoercion_Exception_CoercionFailed($value, $template): void
	{
		$v = $value;
		$this->expectException(Exceptions\CoercionFailed::class);
		try {
			UCall::processCoercion($v, $template);
		} catch (Exceptions\CoercionFailed $exception) {
			$this->assertSame($value, $v);
			$this->assertSame($value, $exception->getValue());
			throw $exception;
		}
	}
	
	/**
	 * Test <code>processCoercion</code> method with <var>$no_throw</var> set to boolean <code>true</code>, 
	 * expecting boolean <code>false</code> to be returned.
	 * 
	 * @testdox Call::processCoercion(&{$value}, {$template}, false|true, false, true) === false
	 * @dataProvider provideCoercionData_Exception_CoercionFailed
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param callable|array|string|null $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testProcessCoercion_NoThrow_False($value, $template): void
	{
		foreach ([false, true] as $nullable) {
			$v = $value;
			$this->assertFalse(UCall::processCoercion($v, $template, $nullable, false, true));
			$this->assertSame($value, $v);
		}
	}
	
	/**
	 * Provide coercion method data for a <code>CoercionFailed</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The provided coercion method data for a <code>CoercionFailed</code> exception to be thrown.</p>
	 */
	public function provideCoercionData_Exception_CoercionFailed(): array
	{
		return [
			[false, null],
			[true, null],
			[1, null],
			[0.123, null],
			['', null],
			['non_existent_function', null],
			['strlen', 'str_repeat'],
			['strlen', function ($str, $str2) {}],
			[CallTest_Class::class . '::getString', null],
			[CallTest_Class::class . '::getStaticString', function (): bool {}],
			[CallTest_Class::class . '::getStaticString', function (string $s): string {}],
			[CallTest_Class::class . '::setStaticString', function (): void {}],
			[CallTest_Class::class . '::getProtectedInteger', null],
			[CallTest_Class::class . '::getProtectedStaticInteger', null],
			[CallTest_Class::class . '::getPrivateBoolean', null],
			[CallTest_Class::class . '::getPrivateStaticBoolean', null],
			[CallTest_AbstractClass::class . '::getString', null],
			[CallTest_AbstractClass::class . '::getStaticString', null],
			[CallTest_AbstractClass::class . '::getProtectedInteger', null],
			[CallTest_AbstractClass::class . '::getProtectedStaticInteger', null],
			[CallTest_Interface::class . '::getString', null],
			[CallTest_Interface::class . '::getStaticString', null],
			[function (): void {}, function () {}],
			[function (int $i) {}, function (bool $b) {}],
			[function (int $i) {}, function (int $i): bool {}],
			[function (int $i): ?bool {}, function (int $i): bool {}],
			[function (int $i): bool {}, function (int $i = 0): bool {}],
			[function (CallTest_Class2 $c): ?CallTest_Interface {},
				function (CallTest_Class $c): CallTest_Interface {}],
			[function (CallTest_Class2 $c): CallTest_Interface {},
				function (CallTest_Class $c): CallTest_InterfaceClass {}],
			[[], null],
			[new CallTest_InvokeableClass(), function ($s) {}],
			[new CallTest_InvokeableClass(), function (): CallTest_Class {}],
			[new CallTest_InvokeableClass2(), function (int $i): void {}],
			[new CallTest_InvokeableClass2(), function (string $s) {}],
			[[new CallTest_Class(), 'getString'], function (): bool {}],
			[[new CallTest_Class(), 'getString'], function (string $s): string {}],
			[[new CallTest_Class(), 'setString'], function (): void {}],
			[[new CallTest_Class(), 'getStaticString'], function (): bool {}],
			[[new CallTest_Class(), 'getStaticString'], function (string $s): string {}],
			[[new CallTest_Class(), 'setStaticString'], function (): void {}],
			[[CallTest_Class::class, 'getString'], null],
			[[CallTest_Class::class, 'getProtectedInteger'], null],
			[[CallTest_Class::class, 'getProtectedStaticInteger'], null],
			[[CallTest_Class::class, 'getPrivateBoolean'], null],
			[[CallTest_Class::class, 'getPrivateStaticBoolean'], null],
			[[CallTest_AbstractClass::class, 'getString'], null],
			[[CallTest_AbstractClass::class, 'getStaticString'], null],
			[[CallTest_AbstractClass::class, 'getProtectedInteger'], null],
			[[CallTest_AbstractClass::class, 'getProtectedStaticInteger'], null],
			[[CallTest_Interface::class, 'getString'], null],
			[[CallTest_Interface::class, 'getStaticString'], null],
			[new \stdClass(), null],
			[fopen(__FILE__, 'r'), null],
			[function (): void {}, function (): bool|float|object|array {}],
			[function (): bool|float|object|array {}, function (): bool|float|object {}],
			[function (): mixed {}, function (): bool|float|object|array {}],
			[function (): CallTest_Class|string {}, function (): CallTest_Class {}],
			[function (): CallTest_Class|string {}, function (): string {}],
			[function (): CallTest_Class|string {}, function (): CallTest_Class2|string {}],
			[function (): CallTest_Class|string|null {}, function (): CallTest_Class|string {}],
			[function (): object {}, function (): CallTest_Class|CallTest_Interface {}],
			[function (): CallTest_Class|CallTest_Interface {}, function (): CallTest_Class {}],
			[function (): CallTest_Class|CallTest_Interface {}, function (): CallTest_Interface {}],
			[function (): int|float|null {}, function (): ?int {}],
			[function (): int|float {}, function (): ?int {}],
			[function (): ?int {}, function (): int|float {}],
			[function (bool|float|object $a) {}, function (bool|float|object|array $a) {}],
			[function (bool|float|object|array $a) {}, function (mixed $a) {}],
			[function (CallTest_Class $a) {}, function (CallTest_Class|string $a) {}],
			[function (string $a) {}, function (CallTest_Class|string $a) {}],
			[function (CallTest_Class2|string $a) {}, function (CallTest_Class|string $a) {}],
			[function (CallTest_Class|string $a) {}, function (CallTest_Class|string|null $a) {}],
			[function (CallTest_Class|CallTest_Interface $a) {}, function (object $a) {}],
			[function (CallTest_Class $a) {}, function (CallTest_Class|CallTest_Interface $a) {}],
			[function (CallTest_Interface $a) {}, function (CallTest_Class|CallTest_Interface $a) {}],
			[function (?int $a) {}, function (int|float|null $a) {}],
			[function (int|float $a) {}, function (?int $a) {}],
			[function (?int $a) {}, function (int|float $a) {}]
		];
	}
	
	/**
	 * Test <code>evaluate</code> method with a <code>null</code> value, 
	 * expecting boolean <code>false</code> to be returned.
	 * 
	 * @testdox Call::evaluate(&{null} --> &{null}) === false
	 * 
	 * @return void
	 */
	public function testEvaluate_Null_False(): void
	{
		$value = null;
		$this->assertFalse(UCall::evaluate($value));
		$this->assertNull($value);
	}
	
	/**
	 * Test <code>coerce</code> method with a <code>null</code> value, 
	 * expecting a <code>CoercionFailed</code> exception to be thrown.
	 * 
	 * @testdox Call::coerce({null}) --> CoercionFailed exception
	 * 
	 * @return void
	 */
	public function testCoerce_Null_Exception_CoercionFailed(): void
	{
		$this->expectException(Exceptions\CoercionFailed::class);
		try {
			UCall::coerce(null);
		} catch (Exceptions\CoercionFailed $exception) {
			$this->assertNull($exception->getValue());
			throw $exception;
		}
	}
	
	/**
	 * Test <code>processCoercion</code> method with a <code>null</code> value, 
	 * expecting a <code>CoercionFailed</code> exception to be thrown.
	 * 
	 * @testdox Call::processCoercion(&{null}) --> CoercionFailed exception
	 * 
	 * @return void
	 */
	public function testProcessCoercion_Null_Exception_CoercionFailed(): void
	{
		$value = null;
		$this->expectException(Exceptions\CoercionFailed::class);
		try {
			UCall::processCoercion($value);
		} catch (Exceptions\CoercionFailed $exception) {
			$this->assertNull($value);
			$this->assertNull($exception->getValue());
			throw $exception;
		}
	}
	
	/**
	 * Test <code>processCoercion</code> method with a <code>null</code> value, 
	 * with <var>$no_throw</var> set to boolean <code>true</code>, expecting boolean <code>false</code> to be returned.
	 * 
	 * @testdox Call::processCoercion(&{null}, null, false, false, true) === false
	 * 
	 * @return void
	 */
	public function testProcessCoercion_Null_NoThrow_False(): void
	{
		$value = null;
		$this->assertFalse(UCall::processCoercion($value, null, false, false, true));
		$this->assertNull($value);
	}
	
	/**
	 * Test <code>evaluate</code> method with <var>$assertive</var> set to boolean <code>true</code>.
	 * 
	 * @testdox Call::evaluate(&{$value}, {$template}, false|true, true) === false|true
	 * @dataProvider provideCoercionData_Assertive
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param callable|array|string|null $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testEvaluate_Assertive($value, $template): void
	{
		//production
		$environment = System::getEnvironment();
		try {
			System::setEnvironment('production');
			foreach ([false, true] as $nullable) {
				$v = $value;
				$this->assertTrue(UCall::evaluate($v, $template, $nullable, true));
				$this->assertInstanceOf(Closure::class, $v);
			}
		} finally {
			System::setEnvironment($environment);
		}
		
		//debug
		foreach ([false, true] as $nullable) {
			$v = $value;
			$this->assertFalse(UCall::evaluate($v, $template, $nullable, true));
			$this->assertSame($value, $v);
		}
	}
	
	/**
	 * Test <code>coerce</code> method with <var>$assertive</var> set to boolean <code>true</code>.
	 * 
	 * @testdox Call::coerce({$value}, {$template}, false|true, true) === Closure or --> CoercionFailed exception
	 * @dataProvider provideCoercionData_Assertive
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param callable|array|string|null $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testCoerce_Assertive($value, $template): void
	{
		//production
		$environment = System::getEnvironment();
		try {
			System::setEnvironment('production');
			foreach ([false, true] as $nullable) {
				$this->assertInstanceOf(Closure::class, UCall::coerce($value, $template, $nullable, true));
			}
		} finally {
			System::setEnvironment($environment);
		}
		
		//debug
		$this->expectException(Exceptions\CoercionFailed::class);
		try {
			UCall::coerce($value, $template, false, true);
		} catch (Exceptions\CoercionFailed $exception) {
			$this->assertSame($value, $exception->getValue());
			throw $exception;
		}
	}
	
	/**
	 * Test <code>processCoercion</code> method with <var>$assertive</var> set to boolean <code>true</code>.
	 * 
	 * @testdox Call::processCoercion(&{$value}, {$template}, false|true, true, false|true) === false|true or --> CoercionFailed exception
	 * @dataProvider provideCoercionData_Assertive
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param callable|array|string|null $template
	 * <p>The method <var>$template</var> parameter to test with.</p>
	 * @return void
	 */
	public function testProcessCoercion_Assertive($value, $template): void
	{
		//production
		$environment = System::getEnvironment();
		try {
			System::setEnvironment('production');
			foreach ([false, true] as $nullable) {
				foreach ([false, true] as $no_throw) {
					$v = $value;
					$this->assertTrue(UCall::processCoercion($v, $template, $nullable, true, $no_throw));
					$this->assertInstanceOf(Closure::class, $v);
				}
			}
		} finally {
			System::setEnvironment($environment);
		}
		
		//debug (no throw)
		foreach ([false, true] as $nullable) {
			$v = $value;
			$this->assertFalse(UCall::processCoercion($v, $template, $nullable, true, true));
			$this->assertSame($value, $v);
		}
		
		//debug (exception)
		$v = $value;
		$this->expectException(Exceptions\CoercionFailed::class);
		try {
			UCall::processCoercion($v, $template, false, true);
		} catch (Exceptions\CoercionFailed $exception) {
			$this->assertSame($value, $v);
			$this->assertSame($value, $exception->getValue());
			throw $exception;
		}
	}
	
	/**
	 * Provide coercion method data with <var>$assertive</var> set to boolean <code>true</code>.
	 * 
	 * @return array
	 * <p>The provided coercion method data with <var>$assertive</var> set to boolean <code>true</code>.</p>
	 */
	public function provideCoercionData_Assertive(): array
	{
		$data = $this->provideCoercionData_Exception_CoercionFailed();
		return array_filter($data, fn (array $datum): bool => $datum[1] !== null);
	}
	
	/**
	 * Test <code>stackPreviousClass</code> method.
	 * 
	 * @testdox Call::stackPreviousClass()
	 * 
	 * @return void
	 */
	public function testStackPreviousClass(): void
	{
		//initialize
		$a = new CallTest_StackClassA(new CallTest_StackClassB(new CallTest_StackClassC()));
		
		//assert
		$this->assertSame(static::class, $a->getStackPreviousClassA());
		$this->assertSame(static::class, $a->getStaticStackPreviousClassA());
		$this->assertSame(static::class, CallTest_StackClassA::getStaticStackPreviousClassA());
		$this->assertSame(CallTest_StackClassA::class, $a->getStackPreviousClassB());
		$this->assertSame(CallTest_StackClassA::class, $a->getStaticStackPreviousClassB());
		$this->assertSame(CallTest_StackClassA::class, CallTest_StackClassA::getStaticStackPreviousClassB());
		$this->assertSame(CallTest_StackClassB::class, $a->getStackPreviousClassC());
		$this->assertSame(CallTest_StackClassB::class, $a->getStaticStackPreviousClassC());
		$this->assertSame(CallTest_StackClassB::class, CallTest_StackClassA::getStaticStackPreviousClassC());
		$this->assertSame(CallTest_StackClassA::class, $a->getStackPreviousClassC(1));
		$this->assertSame(CallTest_StackClassA::class, $a->getStaticStackPreviousClassC(1));
		$this->assertSame(CallTest_StackClassA::class, CallTest_StackClassA::getStaticStackPreviousClassC(1));
		$this->assertSame(static::class, $a->getStackPreviousClassC(2));
		$this->assertSame(static::class, $a->getStaticStackPreviousClassC(2));
		$this->assertSame(static::class, CallTest_StackClassA::getStaticStackPreviousClassC(2));
		$this->assertSame(static::class, $a->getStackPreviousClassB(1));
		$this->assertSame(static::class, $a->getStaticStackPreviousClassB(1));
		$this->assertSame(static::class, CallTest_StackClassA::getStaticStackPreviousClassB(1));
		$this->assertNull($a->getStackPreviousClassC(10000));
		$this->assertNull($a->getStaticStackPreviousClassC(10000));
		$this->assertNull(CallTest_StackClassA::getStaticStackPreviousClassC(10000));
	}
	
	/**
	 * Test <code>stackPreviousClasses</code> method.
	 * 
	 * @testdox Call::stackPreviousClasses()
	 * 
	 * @return void
	 */
	public function testStackPreviousClasses(): void
	{
		//initialize
		$a = new CallTest_StackClassA(new CallTest_StackClassB(new CallTest_StackClassC()));
		$class_a = CallTest_StackClassA::class;
		$class_b = CallTest_StackClassB::class;
		
		//assert
		$this->assertGreaterThan(1, count($a->getStackPreviousClassesA()));
		$this->assertSame([static::class], array_slice($a->getStackPreviousClassesA(), 0, 1));
		$this->assertGreaterThan(1, count($a->getStaticStackPreviousClassesA()));
		$this->assertSame([static::class], array_slice($a->getStaticStackPreviousClassesA(), 0, 1));
		$this->assertGreaterThan(1, count(CallTest_StackClassA::getStaticStackPreviousClassesA()));
		$this->assertSame([static::class], array_slice(CallTest_StackClassA::getStaticStackPreviousClassesA(), 0, 1));
		$this->assertGreaterThan(2, count($a->getStackPreviousClassesB()));
		$this->assertSame([$class_a, static::class], array_slice($a->getStackPreviousClassesB(), 0, 2));
		$this->assertGreaterThan(2, count($a->getStaticStackPreviousClassesB()));
		$this->assertSame([$class_a, static::class], array_slice($a->getStaticStackPreviousClassesB(), 0, 2));
		$this->assertGreaterThan(2, count(CallTest_StackClassA::getStaticStackPreviousClassesB()));
		$this->assertSame(
			[$class_a, static::class], array_slice(CallTest_StackClassA::getStaticStackPreviousClassesB(), 0, 2)
		);
		$this->assertGreaterThan(3, count($a->getStackPreviousClassesC()));
		$this->assertSame([$class_b, $class_a, static::class], array_slice($a->getStackPreviousClassesC(), 0, 3));
		$this->assertGreaterThan(3, count($a->getStaticStackPreviousClassesC()));
		$this->assertSame([$class_b, $class_a, static::class], array_slice($a->getStaticStackPreviousClassesC(), 0, 3));
		$this->assertGreaterThan(3, count(CallTest_StackClassA::getStaticStackPreviousClassesC()));
		$this->assertSame(
			[$class_b, $class_a, static::class],
			array_slice(CallTest_StackClassA::getStaticStackPreviousClassesC(), 0, 3)
		);
		$this->assertGreaterThan(0, count($a->getStackPreviousClassesA(1)));
		$this->assertGreaterThan(0, count($a->getStaticStackPreviousClassesA(1)));
		$this->assertGreaterThan(0, count(CallTest_StackClassA::getStaticStackPreviousClassesA(1)));
		$this->assertGreaterThan(1, count($a->getStackPreviousClassesB(1)));
		$this->assertSame([static::class], array_slice($a->getStackPreviousClassesB(1), 0, 1));
		$this->assertGreaterThan(1, count($a->getStaticStackPreviousClassesB(1)));
		$this->assertSame([static::class], array_slice($a->getStaticStackPreviousClassesB(1), 0, 1));
		$this->assertGreaterThan(1, count(CallTest_StackClassA::getStaticStackPreviousClassesB(1)));
		$this->assertSame([static::class], array_slice(CallTest_StackClassA::getStaticStackPreviousClassesB(1), 0, 1));
		$this->assertGreaterThan(2, count($a->getStackPreviousClassesC(1)));
		$this->assertSame([$class_a, static::class], array_slice($a->getStackPreviousClassesC(1), 0, 2));
		$this->assertGreaterThan(2, count($a->getStaticStackPreviousClassesC(1)));
		$this->assertSame([$class_a, static::class], array_slice($a->getStaticStackPreviousClassesC(1), 0, 2));
		$this->assertGreaterThan(2, count(CallTest_StackClassA::getStaticStackPreviousClassesC(1)));
		$this->assertSame(
			[$class_a, static::class], array_slice(CallTest_StackClassA::getStaticStackPreviousClassesC(1), 0, 2)
		);
		$this->assertGreaterThan(1, count($a->getStackPreviousClassesC(2)));
		$this->assertSame([static::class], array_slice($a->getStackPreviousClassesC(2), 0, 1));
		$this->assertGreaterThan(1, count($a->getStaticStackPreviousClassesC(2)));
		$this->assertSame([static::class], array_slice($a->getStaticStackPreviousClassesC(2), 0, 1));
		$this->assertGreaterThan(1, count(CallTest_StackClassA::getStaticStackPreviousClassesC(2)));
		$this->assertSame([static::class], array_slice(CallTest_StackClassA::getStaticStackPreviousClassesC(2), 0, 1));
		$this->assertSame([static::class], $a->getStackPreviousClassesA(0, 1));
		$this->assertSame([static::class], $a->getStaticStackPreviousClassesA(0, 1));
		$this->assertSame([static::class], CallTest_StackClassA::getStaticStackPreviousClassesA(0, 1));
		$this->assertSame([$class_a], $a->getStackPreviousClassesB(0, 1));
		$this->assertSame([$class_a], $a->getStaticStackPreviousClassesB(0, 1));
		$this->assertSame([$class_a], CallTest_StackClassA::getStaticStackPreviousClassesB(0, 1));
		$this->assertSame([$class_b], $a->getStackPreviousClassesC(0, 1));
		$this->assertSame([$class_b], $a->getStaticStackPreviousClassesC(0, 1));
		$this->assertSame([$class_b], CallTest_StackClassA::getStaticStackPreviousClassesC(0, 1));
		$this->assertSame([$class_b, $class_a], $a->getStackPreviousClassesC(0, 2));
		$this->assertSame([$class_b, $class_a], $a->getStaticStackPreviousClassesC(0, 2));
		$this->assertSame([$class_b, $class_a], CallTest_StackClassA::getStaticStackPreviousClassesC(0, 2));
		$this->assertSame([$class_b, $class_a, static::class], $a->getStackPreviousClassesC(0, 3));
		$this->assertSame([$class_b, $class_a, static::class], $a->getStaticStackPreviousClassesC(0, 3));
		$this->assertSame(
			[$class_b, $class_a, static::class], CallTest_StackClassA::getStaticStackPreviousClassesC(0, 3)
		);
		$this->assertSame([$class_a, static::class], $a->getStackPreviousClassesC(1, 2));
		$this->assertSame([$class_a, static::class], $a->getStaticStackPreviousClassesC(1, 2));
		$this->assertSame([$class_a, static::class], CallTest_StackClassA::getStaticStackPreviousClassesC(1, 2));
		$this->assertSame([static::class], $a->getStackPreviousClassesC(2, 1));
		$this->assertSame([static::class], $a->getStaticStackPreviousClassesC(2, 1));
		$this->assertSame([static::class], CallTest_StackClassA::getStaticStackPreviousClassesC(2, 1));
		$this->assertSame([], $a->getStackPreviousClassesC(10000));
		$this->assertSame([], $a->getStaticStackPreviousClassesC(10000));
		$this->assertSame([], CallTest_StackClassA::getStaticStackPreviousClassesC(10000));
	}
	
	/**
	 * Test <code>stackPreviousObject</code> method.
	 * 
	 * @testdox Call::stackPreviousObject()
	 * 
	 * @return void
	 */
	public function testStackPreviousObject(): void
	{
		//initialize
		$b = new CallTest_StackClassB(new CallTest_StackClassC());
		$a = new CallTest_StackClassA($b);
		
		//assert
		$this->assertSame($this, $a->getStackPreviousObjectA());
		$this->assertSame($this, $a->getStaticStackPreviousObjectA());
		$this->assertSame($this, CallTest_StackClassA::getStaticStackPreviousObjectA());
		$this->assertSame($a, $a->getStackPreviousObjectB());
		$this->assertNull($a->getStaticStackPreviousObjectB());
		$this->assertNull(CallTest_StackClassA::getStaticStackPreviousObjectB());
		$this->assertSame($b, $a->getStackPreviousObjectC());
		$this->assertNull($b->getStaticStackPreviousObjectC());
		$this->assertNull(CallTest_StackClassA::getStaticStackPreviousObjectC());
		$this->assertSame($a, $a->getStackPreviousObjectC(1));
		$this->assertNull($a->getStaticStackPreviousObjectC(1));
		$this->assertNull(CallTest_StackClassA::getStaticStackPreviousObjectC(1));
		$this->assertSame($this, $a->getStackPreviousObjectC(2));
		$this->assertSame($this, $a->getStaticStackPreviousObjectC(2));
		$this->assertSame($this, CallTest_StackClassA::getStaticStackPreviousObjectC(2));
		$this->assertSame($this, $a->getStackPreviousObjectB(1));
		$this->assertSame($this, $a->getStaticStackPreviousObjectB(1));
		$this->assertSame($this, CallTest_StackClassA::getStaticStackPreviousObjectB(1));
		$this->assertNull($a->getStackPreviousObjectC(10000));
		$this->assertNull($a->getStaticStackPreviousObjectC(10000));
		$this->assertNull(CallTest_StackClassA::getStaticStackPreviousObjectC(10000));
	}
	
	/**
	 * Test <code>stackPreviousObjects</code> method.
	 * 
	 * @testdox Call::stackPreviousObjects()
	 * 
	 * @return void
	 */
	public function testStackPreviousObjects(): void
	{
		//initialize
		$b = new CallTest_StackClassB(new CallTest_StackClassC());
		$a = new CallTest_StackClassA($b);
		
		//assert
		$this->assertGreaterThan(1, count($a->getStackPreviousObjectsA()));
		$this->assertSame([$this], array_slice($a->getStackPreviousObjectsA(), 0, 1));
		$this->assertGreaterThan(1, count($a->getStaticStackPreviousObjectsA()));
		$this->assertSame([$this], array_slice($a->getStaticStackPreviousObjectsA(), 0, 1));
		$this->assertGreaterThan(1, count(CallTest_StackClassA::getStaticStackPreviousObjectsA()));
		$this->assertSame([$this], array_slice(CallTest_StackClassA::getStaticStackPreviousObjectsA(), 0, 1));
		$this->assertGreaterThan(2, count($a->getStackPreviousObjectsB()));
		$this->assertSame([$a, $this], array_slice($a->getStackPreviousObjectsB(), 0, 2));
		$this->assertGreaterThan(2, count($a->getStaticStackPreviousObjectsB()));
		$this->assertSame([null, $this], array_slice($a->getStaticStackPreviousObjectsB(), 0, 2));
		$this->assertGreaterThan(2, count(CallTest_StackClassA::getStaticStackPreviousObjectsB()));
		$this->assertSame([null, $this], array_slice(CallTest_StackClassA::getStaticStackPreviousObjectsB(), 0, 2));
		$this->assertGreaterThan(3, count($a->getStackPreviousObjectsC()));
		$this->assertSame([$b, $a, $this], array_slice($a->getStackPreviousObjectsC(), 0, 3));
		$this->assertGreaterThan(3, count($a->getStaticStackPreviousObjectsC()));
		$this->assertSame([null, null, $this], array_slice($a->getStaticStackPreviousObjectsC(), 0, 3));
		$this->assertGreaterThan(3, count(CallTest_StackClassA::getStaticStackPreviousObjectsC()));
		$this->assertSame(
			[null, null, $this], array_slice(CallTest_StackClassA::getStaticStackPreviousObjectsC(), 0, 3)
		);
		$this->assertGreaterThan(0, count($a->getStackPreviousObjectsA(1)));
		$this->assertGreaterThan(0, count($a->getStaticStackPreviousObjectsA(1)));
		$this->assertGreaterThan(0, count(CallTest_StackClassA::getStaticStackPreviousObjectsA(1)));
		$this->assertGreaterThan(1, count($a->getStackPreviousObjectsB(1)));
		$this->assertSame([$this], array_slice($a->getStackPreviousObjectsB(1), 0, 1));
		$this->assertGreaterThan(1, count($a->getStaticStackPreviousObjectsB(1)));
		$this->assertSame([$this], array_slice($a->getStaticStackPreviousObjectsB(1), 0, 1));
		$this->assertGreaterThan(1, count(CallTest_StackClassA::getStaticStackPreviousObjectsB(1)));
		$this->assertSame([$this], array_slice(CallTest_StackClassA::getStaticStackPreviousObjectsB(1), 0, 1));
		$this->assertGreaterThan(2, count($a->getStackPreviousObjectsC(1)));
		$this->assertSame([$a, $this], array_slice($a->getStackPreviousObjectsC(1), 0, 2));
		$this->assertGreaterThan(2, count($a->getStaticStackPreviousObjectsC(1)));
		$this->assertSame([null, $this], array_slice($a->getStaticStackPreviousObjectsC(1), 0, 2));
		$this->assertGreaterThan(2, count(CallTest_StackClassA::getStaticStackPreviousObjectsC(1)));
		$this->assertSame([null, $this], array_slice(CallTest_StackClassA::getStaticStackPreviousObjectsC(1), 0, 2));
		$this->assertGreaterThan(1, count($a->getStackPreviousObjectsC(2)));
		$this->assertSame([$this], array_slice($a->getStackPreviousObjectsC(2), 0, 1));
		$this->assertGreaterThan(1, count($a->getStaticStackPreviousObjectsC(2)));
		$this->assertSame([$this], array_slice($a->getStaticStackPreviousObjectsC(2), 0, 1));
		$this->assertGreaterThan(1, count(CallTest_StackClassA::getStaticStackPreviousObjectsC(2)));
		$this->assertSame([$this], array_slice(CallTest_StackClassA::getStaticStackPreviousObjectsC(2), 0, 1));
		$this->assertSame([$this], $a->getStackPreviousObjectsA(0, 1));
		$this->assertSame([$this], $a->getStaticStackPreviousObjectsA(0, 1));
		$this->assertSame([$this], CallTest_StackClassA::getStaticStackPreviousObjectsA(0, 1));
		$this->assertSame([$a], $a->getStackPreviousObjectsB(0, 1));
		$this->assertSame([null], $a->getStaticStackPreviousObjectsB(0, 1));
		$this->assertSame([null], CallTest_StackClassA::getStaticStackPreviousObjectsB(0, 1));
		$this->assertSame([$b], $a->getStackPreviousObjectsC(0, 1));
		$this->assertSame([null], $a->getStaticStackPreviousObjectsC(0, 1));
		$this->assertSame([null], CallTest_StackClassA::getStaticStackPreviousObjectsC(0, 1));
		$this->assertSame([$b, $a], $a->getStackPreviousObjectsC(0, 2));
		$this->assertSame([null, null], $a->getStaticStackPreviousObjectsC(0, 2));
		$this->assertSame([null, null], CallTest_StackClassA::getStaticStackPreviousObjectsC(0, 2));
		$this->assertSame([$b, $a, $this], $a->getStackPreviousObjectsC(0, 3));
		$this->assertSame([null, null, $this], $a->getStaticStackPreviousObjectsC(0, 3));
		$this->assertSame([null, null, $this], CallTest_StackClassA::getStaticStackPreviousObjectsC(0, 3));
		$this->assertSame([$a, $this], $a->getStackPreviousObjectsC(1, 2));
		$this->assertSame([null, $this], $a->getStaticStackPreviousObjectsC(1, 2));
		$this->assertSame([null, $this], CallTest_StackClassA::getStaticStackPreviousObjectsC(1, 2));
		$this->assertSame([$this], $a->getStackPreviousObjectsC(2, 1));
		$this->assertSame([$this], $a->getStaticStackPreviousObjectsC(2, 1));
		$this->assertSame([$this], CallTest_StackClassA::getStaticStackPreviousObjectsC(2, 1));
		$this->assertSame([], $a->getStackPreviousObjectsC(10000));
		$this->assertSame([], $a->getStaticStackPreviousObjectsC(10000));
		$this->assertSame([], CallTest_StackClassA::getStaticStackPreviousObjectsC(10000));
	}
	
	/**
	 * Test <code>stackPreviousObjectClass</code> method.
	 * 
	 * @testdox Call::stackPreviousObjectClass()
	 * 
	 * @return void
	 */
	public function testStackPreviousObjectClass(): void
	{
		//initialize
		$b = new CallTest_StackClassB(new CallTest_StackClassC());
		$a = new CallTest_StackClassA($b);
		
		//assert
		$this->assertSame($this, $a->getStackPreviousObjectClassA());
		$this->assertSame($this, $a->getStaticStackPreviousObjectClassA());
		$this->assertSame($this, CallTest_StackClassA::getStaticStackPreviousObjectClassA());
		$this->assertSame($a, $a->getStackPreviousObjectClassB());
		$this->assertSame(CallTest_StackClassA::class, $a->getStaticStackPreviousObjectClassB());
		$this->assertSame(CallTest_StackClassA::class, CallTest_StackClassA::getStaticStackPreviousObjectClassB());
		$this->assertSame($b, $a->getStackPreviousObjectClassC());
		$this->assertSame(CallTest_StackClassB::class, $b->getStaticStackPreviousObjectClassC());
		$this->assertSame(CallTest_StackClassB::class, CallTest_StackClassA::getStaticStackPreviousObjectClassC());
		$this->assertSame($a, $a->getStackPreviousObjectClassC(1));
		$this->assertSame(CallTest_StackClassA::class, $a->getStaticStackPreviousObjectClassC(1));
		$this->assertSame(CallTest_StackClassA::class, CallTest_StackClassA::getStaticStackPreviousObjectClassC(1));
		$this->assertSame($this, $a->getStackPreviousObjectClassC(2));
		$this->assertSame($this, $a->getStaticStackPreviousObjectClassC(2));
		$this->assertSame($this, CallTest_StackClassA::getStaticStackPreviousObjectClassC(2));
		$this->assertSame($this, $a->getStackPreviousObjectClassB(1));
		$this->assertSame($this, $a->getStaticStackPreviousObjectClassB(1));
		$this->assertSame($this, CallTest_StackClassA::getStaticStackPreviousObjectClassB(1));
		$this->assertNull($a->getStackPreviousObjectClassC(10000));
		$this->assertNull($a->getStaticStackPreviousObjectClassC(10000));
		$this->assertNull(CallTest_StackClassA::getStaticStackPreviousObjectClassC(10000));
	}
	
	/**
	 * Test <code>stackPreviousObjectsClasses</code> method.
	 * 
	 * @testdox Call::stackPreviousObjectsClasses()
	 * 
	 * @return void
	 */
	public function testStackPreviousObjectsClasses(): void
	{
		//initialize
		$b = new CallTest_StackClassB(new CallTest_StackClassC());
		$a = new CallTest_StackClassA($b);
		$class_a = CallTest_StackClassA::class;
		$class_b = CallTest_StackClassB::class;
		
		//assert
		$this->assertGreaterThan(1, count($a->getStackPreviousObjectsClassesA()));
		$this->assertSame([$this], array_slice($a->getStackPreviousObjectsClassesA(), 0, 1));
		$this->assertGreaterThan(1, count($a->getStaticStackPreviousObjectsClassesA()));
		$this->assertSame([$this], array_slice($a->getStaticStackPreviousObjectsClassesA(), 0, 1));
		$this->assertGreaterThan(1, count(CallTest_StackClassA::getStaticStackPreviousObjectsClassesA()));
		$this->assertSame([$this], array_slice(CallTest_StackClassA::getStaticStackPreviousObjectsClassesA(), 0, 1));
		$this->assertGreaterThan(2, count($a->getStackPreviousObjectsClassesB()));
		$this->assertSame([$a, $this], array_slice($a->getStackPreviousObjectsClassesB(), 0, 2));
		$this->assertGreaterThan(2, count($a->getStaticStackPreviousObjectsClassesB()));
		$this->assertSame([$class_a, $this], array_slice($a->getStaticStackPreviousObjectsClassesB(), 0, 2));
		$this->assertGreaterThan(2, count(CallTest_StackClassA::getStaticStackPreviousObjectsClassesB()));
		$this->assertSame(
			[$class_a, $this], array_slice(CallTest_StackClassA::getStaticStackPreviousObjectsClassesB(), 0, 2)
		);
		$this->assertGreaterThan(3, count($a->getStackPreviousObjectsClassesC()));
		$this->assertSame([$b, $a, $this], array_slice($a->getStackPreviousObjectsClassesC(), 0, 3));
		$this->assertGreaterThan(3, count($a->getStaticStackPreviousObjectsClassesC()));
		$this->assertSame([$class_b, $class_a, $this], array_slice($a->getStaticStackPreviousObjectsClassesC(), 0, 3));
		$this->assertGreaterThan(3, count(CallTest_StackClassA::getStaticStackPreviousObjectsClassesC()));
		$this->assertSame(
			[$class_b, $class_a, $this],
			array_slice(CallTest_StackClassA::getStaticStackPreviousObjectsClassesC(), 0, 3)
		);
		$this->assertGreaterThan(0, count($a->getStackPreviousObjectsClassesA(1)));
		$this->assertGreaterThan(0, count($a->getStaticStackPreviousObjectsClassesA(1)));
		$this->assertGreaterThan(0, count(CallTest_StackClassA::getStaticStackPreviousObjectsClassesA(1)));
		$this->assertGreaterThan(1, count($a->getStackPreviousObjectsClassesB(1)));
		$this->assertSame([$this], array_slice($a->getStackPreviousObjectsClassesB(1), 0, 1));
		$this->assertGreaterThan(1, count($a->getStaticStackPreviousObjectsClassesB(1)));
		$this->assertSame([$this], array_slice($a->getStaticStackPreviousObjectsClassesB(1), 0, 1));
		$this->assertGreaterThan(1, count(CallTest_StackClassA::getStaticStackPreviousObjectsClassesB(1)));
		$this->assertSame([$this], array_slice(CallTest_StackClassA::getStaticStackPreviousObjectsClassesB(1), 0, 1));
		$this->assertGreaterThan(2, count($a->getStackPreviousObjectsClassesC(1)));
		$this->assertSame([$a, $this], array_slice($a->getStackPreviousObjectsClassesC(1), 0, 2));
		$this->assertGreaterThan(2, count($a->getStaticStackPreviousObjectsClassesC(1)));
		$this->assertSame([$class_a, $this], array_slice($a->getStaticStackPreviousObjectsClassesC(1), 0, 2));
		$this->assertGreaterThan(2, count(CallTest_StackClassA::getStaticStackPreviousObjectsClassesC(1)));
		$this->assertSame(
			[$class_a, $this], array_slice(CallTest_StackClassA::getStaticStackPreviousObjectsClassesC(1), 0, 2)
		);
		$this->assertGreaterThan(1, count($a->getStackPreviousObjectsClassesC(2)));
		$this->assertSame([$this], array_slice($a->getStackPreviousObjectsClassesC(2), 0, 1));
		$this->assertGreaterThan(1, count($a->getStaticStackPreviousObjectsClassesC(2)));
		$this->assertSame([$this], array_slice($a->getStaticStackPreviousObjectsClassesC(2), 0, 1));
		$this->assertGreaterThan(1, count(CallTest_StackClassA::getStaticStackPreviousObjectsClassesC(2)));
		$this->assertSame([$this], array_slice(CallTest_StackClassA::getStaticStackPreviousObjectsClassesC(2), 0, 1));
		$this->assertSame([$this], $a->getStackPreviousObjectsClassesA(0, 1));
		$this->assertSame([$this], $a->getStaticStackPreviousObjectsClassesA(0, 1));
		$this->assertSame([$this], CallTest_StackClassA::getStaticStackPreviousObjectsClassesA(0, 1));
		$this->assertSame([$a], $a->getStackPreviousObjectsClassesB(0, 1));
		$this->assertSame([$class_a], $a->getStaticStackPreviousObjectsClassesB(0, 1));
		$this->assertSame([$class_a], CallTest_StackClassA::getStaticStackPreviousObjectsClassesB(0, 1));
		$this->assertSame([$b], $a->getStackPreviousObjectsClassesC(0, 1));
		$this->assertSame([$class_b], $a->getStaticStackPreviousObjectsClassesC(0, 1));
		$this->assertSame([$class_b], CallTest_StackClassA::getStaticStackPreviousObjectsClassesC(0, 1));
		$this->assertSame([$b, $a], $a->getStackPreviousObjectsClassesC(0, 2));
		$this->assertSame([$class_b, $class_a], $a->getStaticStackPreviousObjectsClassesC(0, 2));
		$this->assertSame([$class_b, $class_a], CallTest_StackClassA::getStaticStackPreviousObjectsClassesC(0, 2));
		$this->assertSame([$b, $a, $this], $a->getStackPreviousObjectsClassesC(0, 3));
		$this->assertSame([$class_b, $class_a, $this], $a->getStaticStackPreviousObjectsClassesC(0, 3));
		$this->assertSame(
			[$class_b, $class_a, $this], CallTest_StackClassA::getStaticStackPreviousObjectsClassesC(0, 3)
		);
		$this->assertSame([$a, $this], $a->getStackPreviousObjectsClassesC(1, 2));
		$this->assertSame([$class_a, $this], $a->getStaticStackPreviousObjectsClassesC(1, 2));
		$this->assertSame([$class_a, $this], CallTest_StackClassA::getStaticStackPreviousObjectsClassesC(1, 2));
		$this->assertSame([$this], $a->getStackPreviousObjectsClassesC(2, 1));
		$this->assertSame([$this], $a->getStaticStackPreviousObjectsClassesC(2, 1));
		$this->assertSame([$this], CallTest_StackClassA::getStaticStackPreviousObjectsClassesC(2, 1));
		$this->assertSame([], $a->getStackPreviousObjectsClassesC(10000));
		$this->assertSame([], $a->getStaticStackPreviousObjectsClassesC(10000));
		$this->assertSame([], CallTest_StackClassA::getStaticStackPreviousObjectsClassesC(10000));
	}
	
	/**
	 * Test <code>stackPreviousName</code> method.
	 *
	 * @testdox Call::stackPreviousName()
	 *
	 * @return void
	 */
	public function testStackPreviousName(): void
	{
		//initialize
		$a = new CallTest_StackClassA(new CallTest_StackClassB(new CallTest_StackClassC()));
		
		//assert
		$this->assertSame('testStackPreviousName', $a->getStackPreviousNameA());
		$this->assertSame(static::class . '::testStackPreviousName', $a->getStackPreviousNameA(true));
		$this->assertSame('CallTest::testStackPreviousName', $a->getStackPreviousNameA(true, true));
		$this->assertSame('testStackPreviousName', $a->getStaticStackPreviousNameA());
		$this->assertSame(static::class . '::testStackPreviousName', $a->getStaticStackPreviousNameA(true));
		$this->assertSame('CallTest::testStackPreviousName', $a->getStaticStackPreviousNameA(true, true));
		$this->assertSame('testStackPreviousName', CallTest_StackClassA::getStaticStackPreviousNameA());
		$this->assertSame(
			static::class . '::testStackPreviousName', CallTest_StackClassA::getStaticStackPreviousNameA(true)
		);
		$this->assertSame(
			'CallTest::testStackPreviousName', CallTest_StackClassA::getStaticStackPreviousNameA(true, true)
		);
		$this->assertSame('getStackPreviousNameBA', $a->getStackPreviousNameBA());
		$this->assertSame(CallTest_StackClassA::class . '::getStackPreviousNameBA', $a->getStackPreviousNameBA(true));
		$this->assertSame('CallTest_StackClassA::getStackPreviousNameBA', $a->getStackPreviousNameBA(true, true));
		$this->assertSame('getStaticStackPreviousNameBA', $a->getStaticStackPreviousNameBA());
		$this->assertSame(
			CallTest_StackClassA::class . '::getStaticStackPreviousNameBA', $a->getStaticStackPreviousNameBA(true)
		);
		$this->assertSame(
			'CallTest_StackClassA::getStaticStackPreviousNameBA', $a->getStaticStackPreviousNameBA(true, true)
		);
		$this->assertSame('getStaticStackPreviousNameBA', CallTest_StackClassA::getStaticStackPreviousNameBA());
		$this->assertSame(
			CallTest_StackClassA::class . '::getStaticStackPreviousNameBA',
			CallTest_StackClassA::getStaticStackPreviousNameBA(true)
		);
		$this->assertSame(
			'CallTest_StackClassA::getStaticStackPreviousNameBA',
			CallTest_StackClassA::getStaticStackPreviousNameBA(true, true)
		);
		$this->assertSame('getStackPreviousNameCB', $a->getStackPreviousNameCA());
		$this->assertSame(CallTest_StackClassB::class . '::getStackPreviousNameCB', $a->getStackPreviousNameCA(true));
		$this->assertSame('CallTest_StackClassB::getStackPreviousNameCB', $a->getStackPreviousNameCA(true, true));
		$this->assertSame('getStaticStackPreviousNameCB', $a->getStaticStackPreviousNameCA());
		$this->assertSame(
			CallTest_StackClassB::class . '::getStaticStackPreviousNameCB', $a->getStaticStackPreviousNameCA(true)
		);
		$this->assertSame(
			'CallTest_StackClassB::getStaticStackPreviousNameCB', $a->getStaticStackPreviousNameCA(true, true)
		);
		$this->assertSame('getStaticStackPreviousNameCB', CallTest_StackClassA::getStaticStackPreviousNameCA());
		$this->assertSame(
			CallTest_StackClassB::class . '::getStaticStackPreviousNameCB',
			CallTest_StackClassA::getStaticStackPreviousNameCA(true)
		);
		$this->assertSame(
			'CallTest_StackClassB::getStaticStackPreviousNameCB',
			CallTest_StackClassA::getStaticStackPreviousNameCA(true, true)
		);
		$this->assertSame('getStackPreviousNameCA', $a->getStackPreviousNameCA(false, false, 1));
		$this->assertSame(
			CallTest_StackClassA::class . '::getStackPreviousNameCA', $a->getStackPreviousNameCA(true, false, 1)
		);
		$this->assertSame('CallTest_StackClassA::getStackPreviousNameCA', $a->getStackPreviousNameCA(true, true, 1));
		$this->assertSame('getStaticStackPreviousNameCA', $a->getStaticStackPreviousNameCA(false, false, 1));
		$this->assertSame(
			CallTest_StackClassA::class . '::getStaticStackPreviousNameCA',
			$a->getStaticStackPreviousNameCA(true, false, 1)
		);
		$this->assertSame(
			'CallTest_StackClassA::getStaticStackPreviousNameCA', $a->getStaticStackPreviousNameCA(true, true, 1)
		);
		$this->assertSame(
			'getStaticStackPreviousNameCA', CallTest_StackClassA::getStaticStackPreviousNameCA(false, false, 1)
		);
		$this->assertSame(
			CallTest_StackClassA::class . '::getStaticStackPreviousNameCA',
			CallTest_StackClassA::getStaticStackPreviousNameCA(true, false, 1)
		);
		$this->assertSame(
			'CallTest_StackClassA::getStaticStackPreviousNameCA',
			CallTest_StackClassA::getStaticStackPreviousNameCA(true, true, 1)
		);
		$this->assertSame('testStackPreviousName', $a->getStackPreviousNameCA(false, false, 2));
		$this->assertSame(static::class . '::testStackPreviousName', $a->getStackPreviousNameCA(true, false, 2));
		$this->assertSame('CallTest::testStackPreviousName', $a->getStackPreviousNameCA(true, true, 2));
		$this->assertSame('testStackPreviousName', $a->getStaticStackPreviousNameCA(false, false, 2));
		$this->assertSame(static::class . '::testStackPreviousName', $a->getStaticStackPreviousNameCA(true, false, 2));
		$this->assertSame('CallTest::testStackPreviousName', $a->getStaticStackPreviousNameCA(true, true, 2));
		$this->assertSame('testStackPreviousName', CallTest_StackClassA::getStaticStackPreviousNameCA(false, false, 2));
		$this->assertSame(
			static::class . '::testStackPreviousName',
			CallTest_StackClassA::getStaticStackPreviousNameCA(true, false, 2)
		);
		$this->assertSame(
			'CallTest::testStackPreviousName', CallTest_StackClassA::getStaticStackPreviousNameCA(true, true, 2)
		);
		$this->assertSame('testStackPreviousName', $a->getStackPreviousNameBA(false, false, 1));
		$this->assertSame(static::class . '::testStackPreviousName', $a->getStackPreviousNameBA(true, false, 1));
		$this->assertSame('CallTest::testStackPreviousName', $a->getStackPreviousNameBA(true, true, 1));
		$this->assertSame('testStackPreviousName', $a->getStaticStackPreviousNameBA(false, false, 1));
		$this->assertSame(static::class . '::testStackPreviousName', $a->getStaticStackPreviousNameBA(true, false, 1));
		$this->assertSame('CallTest::testStackPreviousName', $a->getStaticStackPreviousNameBA(true, true, 1));
		$this->assertSame('testStackPreviousName', CallTest_StackClassA::getStaticStackPreviousNameBA(false, false, 1));
		$this->assertSame(
			static::class . '::testStackPreviousName',
			CallTest_StackClassA::getStaticStackPreviousNameBA(true, false, 1)
		);
		$this->assertSame(
			'CallTest::testStackPreviousName', CallTest_StackClassA::getStaticStackPreviousNameBA(true, true, 1)
		);
		$this->assertNull($a->getStackPreviousNameCA(false, false, 10000));
		$this->assertNull($a->getStaticStackPreviousNameCA(false, false, 10000));
		$this->assertNull(CallTest_StackClassA::getStaticStackPreviousNameCA(false, false, 10000));
		
		//assert anonymous
		$f1 = function () use ($a): ?string {
			return $a->getStackPreviousNameA();
		};
		$f2 = function () use ($a): ?string {
			return $a->getStaticStackPreviousNameA();
		};
		$f3 = function (): ?string {
			return CallTest_StackClassA::getStaticStackPreviousNameA();
		};
		$this->assertNull($f1());
		$this->assertNull($f2());
		$this->assertNull($f3());
	}
	
	/**
	 * Test <code>stackPreviousNames</code> method.
	 *
	 * @testdox Call::stackPreviousNames()
	 *
	 * @return void
	 */
	public function testStackPreviousNames(): void
	{
		//initialize
		$a = new CallTest_StackClassA(new CallTest_StackClassB(new CallTest_StackClassC()));
		
		//assert
		$this->assertGreaterThan(1, count($a->getStackPreviousNamesA()));
		$this->assertSame(['testStackPreviousNames'], array_slice($a->getStackPreviousNamesA(), 0, 1));
		$this->assertSame(
			[static::class . '::testStackPreviousNames'], array_slice($a->getStackPreviousNamesA(true), 0, 1)
		);
		$this->assertSame(
			['CallTest::testStackPreviousNames'], array_slice($a->getStackPreviousNamesA(true, true), 0, 1)
		);
		$this->assertGreaterThan(1, count($a->getStaticStackPreviousNamesA()));
		$this->assertSame(['testStackPreviousNames'], array_slice($a->getStaticStackPreviousNamesA(), 0, 1));
		$this->assertSame(
			[static::class . '::testStackPreviousNames'], array_slice($a->getStaticStackPreviousNamesA(true), 0, 1)
		);
		$this->assertSame(
			['CallTest::testStackPreviousNames'], array_slice($a->getStaticStackPreviousNamesA(true, true), 0, 1)
		);
		$this->assertGreaterThan(1, count(CallTest_StackClassA::getStaticStackPreviousNamesA()));
		$this->assertSame(
			['testStackPreviousNames'], array_slice(CallTest_StackClassA::getStaticStackPreviousNamesA(), 0, 1)
		);
		$this->assertSame(
			[static::class . '::testStackPreviousNames'],
			array_slice(CallTest_StackClassA::getStaticStackPreviousNamesA(true), 0, 1)
		);
		$this->assertSame(
			['CallTest::testStackPreviousNames'],
			array_slice(CallTest_StackClassA::getStaticStackPreviousNamesA(true, true), 0, 1)
		);
		$this->assertGreaterThan(2, count($a->getStackPreviousNamesBA()));
		$this->assertSame(
			['getStackPreviousNamesBA', 'testStackPreviousNames'], array_slice($a->getStackPreviousNamesBA(), 0, 2)
		);
		$this->assertSame(
			[CallTest_StackClassA::class . '::getStackPreviousNamesBA', static::class . '::testStackPreviousNames'],
			array_slice($a->getStackPreviousNamesBA(true), 0, 2)
		);
		$this->assertSame(
			['CallTest_StackClassA::getStackPreviousNamesBA', 'CallTest::testStackPreviousNames'],
			array_slice($a->getStackPreviousNamesBA(true, true), 0, 2)
		);
		$this->assertGreaterThan(2, count($a->getStaticStackPreviousNamesBA()));
		$this->assertSame(
			['getStaticStackPreviousNamesBA', 'testStackPreviousNames'],
			array_slice($a->getStaticStackPreviousNamesBA(), 0, 2)
		);
		$this->assertSame([
				CallTest_StackClassA::class . '::getStaticStackPreviousNamesBA',
				static::class . '::testStackPreviousNames'
			], array_slice($a->getStaticStackPreviousNamesBA(true), 0, 2)
		);
		$this->assertSame(
			['CallTest_StackClassA::getStaticStackPreviousNamesBA', 'CallTest::testStackPreviousNames'],
			array_slice($a->getStaticStackPreviousNamesBA(true, true), 0, 2)
		);
		$this->assertGreaterThan(2, count(CallTest_StackClassA::getStaticStackPreviousNamesBA()));
		$this->assertSame(
			['getStaticStackPreviousNamesBA', 'testStackPreviousNames'],
			array_slice(CallTest_StackClassA::getStaticStackPreviousNamesBA(), 0, 2)
		);
		$this->assertSame([
				CallTest_StackClassA::class . '::getStaticStackPreviousNamesBA',
				static::class . '::testStackPreviousNames'
			], array_slice(CallTest_StackClassA::getStaticStackPreviousNamesBA(true), 0, 2)
		);
		$this->assertSame(
			['CallTest_StackClassA::getStaticStackPreviousNamesBA', 'CallTest::testStackPreviousNames'],
			array_slice(CallTest_StackClassA::getStaticStackPreviousNamesBA(true, true), 0, 2)
		);
		$this->assertGreaterThan(3, count($a->getStackPreviousNamesCA()));
		$this->assertSame(
			['getStackPreviousNamesCB', 'getStackPreviousNamesCA', 'testStackPreviousNames'],
			array_slice($a->getStackPreviousNamesCA(), 0, 3)
		);
		$this->assertSame([
				CallTest_StackClassB::class . '::getStackPreviousNamesCB',
				CallTest_StackClassA::class . '::getStackPreviousNamesCA',
				static::class . '::testStackPreviousNames'
			], array_slice($a->getStackPreviousNamesCA(true), 0, 3)
		);
		$this->assertSame([
				'CallTest_StackClassB::getStackPreviousNamesCB',
				'CallTest_StackClassA::getStackPreviousNamesCA',
				'CallTest::testStackPreviousNames'
			], array_slice($a->getStackPreviousNamesCA(true, true), 0, 3)
		);
		$this->assertGreaterThan(3, count($a->getStaticStackPreviousNamesCA()));
		$this->assertSame(
			['getStaticStackPreviousNamesCB', 'getStaticStackPreviousNamesCA', 'testStackPreviousNames'],
			array_slice($a->getStaticStackPreviousNamesCA(), 0, 3)
		);
		$this->assertSame([
				CallTest_StackClassB::class . '::getStaticStackPreviousNamesCB',
				CallTest_StackClassA::class . '::getStaticStackPreviousNamesCA',
				static::class . '::testStackPreviousNames'
			], array_slice($a->getStaticStackPreviousNamesCA(true), 0, 3)
		);
		$this->assertSame([
				'CallTest_StackClassB::getStaticStackPreviousNamesCB',
				'CallTest_StackClassA::getStaticStackPreviousNamesCA',
				'CallTest::testStackPreviousNames'
			], array_slice($a->getStaticStackPreviousNamesCA(true, true), 0, 3)
		);
		$this->assertGreaterThan(3, count(CallTest_StackClassA::getStaticStackPreviousNamesCA()));
		$this->assertSame(
			['getStaticStackPreviousNamesCB', 'getStaticStackPreviousNamesCA', 'testStackPreviousNames'],
			array_slice(CallTest_StackClassA::getStaticStackPreviousNamesCA(), 0, 3)
		);
		$this->assertSame([
				CallTest_StackClassB::class . '::getStaticStackPreviousNamesCB',
				CallTest_StackClassA::class . '::getStaticStackPreviousNamesCA',
				static::class . '::testStackPreviousNames'
			], array_slice(CallTest_StackClassA::getStaticStackPreviousNamesCA(true), 0, 3)
		);
		$this->assertSame([
				'CallTest_StackClassB::getStaticStackPreviousNamesCB',
				'CallTest_StackClassA::getStaticStackPreviousNamesCA',
				'CallTest::testStackPreviousNames'
			], array_slice(CallTest_StackClassA::getStaticStackPreviousNamesCA(true, true), 0, 3)
		);
		$this->assertGreaterThan(0, count($a->getStackPreviousNamesA(false, false, 1)));
		$this->assertGreaterThan(0, count($a->getStaticStackPreviousNamesA(false, false, 1)));
		$this->assertGreaterThan(0, count(CallTest_StackClassA::getStaticStackPreviousNamesA(false, false, 1)));
		$this->assertGreaterThan(1, count($a->getStackPreviousNamesBA(false, false, 1)));
		$this->assertSame(['testStackPreviousNames'], array_slice($a->getStackPreviousNamesBA(false, false, 1), 0, 1));
		$this->assertSame(
			[static::class . '::testStackPreviousNames'], array_slice($a->getStackPreviousNamesBA(true, false, 1), 0, 1)
		);
		$this->assertSame(
			['CallTest::testStackPreviousNames'], array_slice($a->getStackPreviousNamesBA(true, true, 1), 0, 1)
		);
		$this->assertGreaterThan(1, count($a->getStaticStackPreviousNamesBA(false, false, 1)));
		$this->assertSame(
			['testStackPreviousNames'], array_slice($a->getStaticStackPreviousNamesBA(false, false, 1), 0, 1)
		);
		$this->assertSame(
			[static::class . '::testStackPreviousNames'],
			array_slice($a->getStaticStackPreviousNamesBA(true, false, 1), 0, 1)
		);
		$this->assertSame(
			['CallTest::testStackPreviousNames'], array_slice($a->getStaticStackPreviousNamesBA(true, true, 1), 0, 1)
		);
		$this->assertGreaterThan(1, count(CallTest_StackClassA::getStaticStackPreviousNamesBA(false, false, 1)));
		$this->assertSame(
			['testStackPreviousNames'],
			array_slice(CallTest_StackClassA::getStaticStackPreviousNamesBA(false, false, 1), 0, 1)
		);
		$this->assertSame(
			[static::class . '::testStackPreviousNames'],
			array_slice(CallTest_StackClassA::getStaticStackPreviousNamesBA(true, false, 1), 0, 1)
		);
		$this->assertSame(
			['CallTest::testStackPreviousNames'],
			array_slice(CallTest_StackClassA::getStaticStackPreviousNamesBA(true, true, 1), 0, 1)
		);
		$this->assertGreaterThan(2, count($a->getStackPreviousNamesCA(false, false, 1)));
		$this->assertSame(
			['getStackPreviousNamesCA', 'testStackPreviousNames'],
			array_slice($a->getStackPreviousNamesCA(false, false, 1), 0, 2)
		);
		$this->assertSame(
			[CallTest_StackClassA::class . '::getStackPreviousNamesCA', static::class . '::testStackPreviousNames'],
			array_slice($a->getStackPreviousNamesCA(true, false, 1), 0, 2)
		);
		$this->assertSame(
			['CallTest_StackClassA::getStackPreviousNamesCA', 'CallTest::testStackPreviousNames'],
			array_slice($a->getStackPreviousNamesCA(true, true, 1), 0, 2)
		);
		$this->assertGreaterThan(2, count($a->getStaticStackPreviousNamesCA(false, false, 1)));
		$this->assertSame(
			['getStaticStackPreviousNamesCA', 'testStackPreviousNames'],
			array_slice($a->getStaticStackPreviousNamesCA(false, false, 1), 0, 2)
		);
		$this->assertSame([
				CallTest_StackClassA::class . '::getStaticStackPreviousNamesCA',
				static::class . '::testStackPreviousNames'
			], array_slice($a->getStaticStackPreviousNamesCA(true, false, 1), 0, 2)
		);
		$this->assertSame(
			['CallTest_StackClassA::getStaticStackPreviousNamesCA', 'CallTest::testStackPreviousNames'],
			array_slice($a->getStaticStackPreviousNamesCA(true, true, 1), 0, 2)
		);
		$this->assertGreaterThan(2, count(CallTest_StackClassA::getStaticStackPreviousNamesCA(false, false, 1)));
		$this->assertSame(
			['getStaticStackPreviousNamesCA', 'testStackPreviousNames'],
			array_slice(CallTest_StackClassA::getStaticStackPreviousNamesCA(false, false, 1), 0, 2)
		);
		$this->assertSame([
				CallTest_StackClassA::class . '::getStaticStackPreviousNamesCA',
				static::class . '::testStackPreviousNames'
			], array_slice(CallTest_StackClassA::getStaticStackPreviousNamesCA(true, false, 1), 0, 2)
		);
		$this->assertSame(
			['CallTest_StackClassA::getStaticStackPreviousNamesCA', 'CallTest::testStackPreviousNames'],
			array_slice(CallTest_StackClassA::getStaticStackPreviousNamesCA(true, true, 1), 0, 2)
		);
		$this->assertGreaterThan(1, count($a->getStackPreviousNamesCA(false, false, 2)));
		$this->assertSame(['testStackPreviousNames'], array_slice($a->getStackPreviousNamesCA(false, false, 2), 0, 1));
		$this->assertSame(
			[static::class . '::testStackPreviousNames'], array_slice($a->getStackPreviousNamesCA(true, false, 2), 0, 1)
		);
		$this->assertSame(
			['CallTest::testStackPreviousNames'], array_slice($a->getStackPreviousNamesCA(true, true, 2), 0, 1)
		);
		$this->assertGreaterThan(1, count($a->getStaticStackPreviousNamesCA(false, false, 2)));
		$this->assertSame(
			['testStackPreviousNames'], array_slice($a->getStaticStackPreviousNamesCA(false, false, 2), 0, 1)
		);
		$this->assertSame(
			[static::class . '::testStackPreviousNames'],
			array_slice($a->getStaticStackPreviousNamesCA(true, false, 2), 0, 1)
		);
		$this->assertSame(
			['CallTest::testStackPreviousNames'], array_slice($a->getStaticStackPreviousNamesCA(true, true, 2), 0, 1)
		);
		
		$this->assertGreaterThan(1, count(CallTest_StackClassA::getStaticStackPreviousNamesCA(false, false, 2)));
		$this->assertSame(
			['testStackPreviousNames'],
			array_slice(CallTest_StackClassA::getStaticStackPreviousNamesCA(false, false, 2), 0, 1)
		);
		$this->assertSame(
			[static::class . '::testStackPreviousNames'],
			array_slice(CallTest_StackClassA::getStaticStackPreviousNamesCA(true, false, 2), 0, 1)
		);
		$this->assertSame(
			['CallTest::testStackPreviousNames'],
			array_slice(CallTest_StackClassA::getStaticStackPreviousNamesCA(true, true, 2), 0, 1)
		);
		$this->assertSame(['testStackPreviousNames'], $a->getStackPreviousNamesA(false, false, 0, 1));
		$this->assertSame([static::class . '::testStackPreviousNames'], $a->getStackPreviousNamesA(true, false, 0, 1));
		$this->assertSame(['CallTest::testStackPreviousNames'], $a->getStackPreviousNamesA(true, true, 0, 1));
		$this->assertSame(['testStackPreviousNames'], $a->getStaticStackPreviousNamesA(false, false, 0, 1));
		$this->assertSame(
			[static::class . '::testStackPreviousNames'], $a->getStaticStackPreviousNamesA(true, false, 0, 1)
		);
		$this->assertSame(['CallTest::testStackPreviousNames'], $a->getStaticStackPreviousNamesA(true, true, 0, 1));
		$this->assertSame(
			['testStackPreviousNames'], CallTest_StackClassA::getStaticStackPreviousNamesA(false, false, 0, 1)
		);
		$this->assertSame(
			[static::class . '::testStackPreviousNames'],
			CallTest_StackClassA::getStaticStackPreviousNamesA(true, false, 0, 1)
		);
		$this->assertSame(
			['CallTest::testStackPreviousNames'], CallTest_StackClassA::getStaticStackPreviousNamesA(true, true, 0, 1)
		);
		$this->assertSame(['getStackPreviousNamesBA'], $a->getStackPreviousNamesBA(false, false, 0, 1));
		$this->assertSame(
			[CallTest_StackClassA::class . '::getStackPreviousNamesBA'], $a->getStackPreviousNamesBA(true, false, 0, 1)
		);
		$this->assertSame(
			['CallTest_StackClassA::getStackPreviousNamesBA'], $a->getStackPreviousNamesBA(true, true, 0, 1)
		);
		$this->assertSame(['getStaticStackPreviousNamesBA'], $a->getStaticStackPreviousNamesBA(false, false, 0, 1));
		$this->assertSame(
			[CallTest_StackClassA::class . '::getStaticStackPreviousNamesBA'],
			$a->getStaticStackPreviousNamesBA(true, false, 0, 1)
		);
		$this->assertSame(
			['CallTest_StackClassA::getStaticStackPreviousNamesBA'], $a->getStaticStackPreviousNamesBA(true, true, 0, 1)
		);
		$this->assertSame(
			['getStaticStackPreviousNamesBA'], CallTest_StackClassA::getStaticStackPreviousNamesBA(false, false, 0, 1)
		);
		$this->assertSame(
			[CallTest_StackClassA::class . '::getStaticStackPreviousNamesBA'],
			CallTest_StackClassA::getStaticStackPreviousNamesBA(true, false, 0, 1)
		);
		$this->assertSame(
			['CallTest_StackClassA::getStaticStackPreviousNamesBA'],
			CallTest_StackClassA::getStaticStackPreviousNamesBA(true, true, 0, 1)
		);
		$this->assertSame(['getStackPreviousNamesCB'], $a->getStackPreviousNamesCA(false, false, 0, 1));
		$this->assertSame(
			[CallTest_StackClassB::class . '::getStackPreviousNamesCB'], $a->getStackPreviousNamesCA(true, false, 0, 1)
		);
		$this->assertSame(
			['CallTest_StackClassB::getStackPreviousNamesCB'], $a->getStackPreviousNamesCA(true, true, 0, 1)
		);
		$this->assertSame(['getStaticStackPreviousNamesCB'], $a->getStaticStackPreviousNamesCA(false, false, 0, 1));
		$this->assertSame(
			[CallTest_StackClassB::class . '::getStaticStackPreviousNamesCB'],
			$a->getStaticStackPreviousNamesCA(true, false, 0, 1)
		);
		$this->assertSame(
			['CallTest_StackClassB::getStaticStackPreviousNamesCB'], $a->getStaticStackPreviousNamesCA(true, true, 0, 1)
		);
		$this->assertSame(
			['getStaticStackPreviousNamesCB'], CallTest_StackClassA::getStaticStackPreviousNamesCA(false, false, 0, 1)
		);
		$this->assertSame(
			[CallTest_StackClassB::class . '::getStaticStackPreviousNamesCB'],
			CallTest_StackClassA::getStaticStackPreviousNamesCA(true, false, 0, 1)
		);
		$this->assertSame(
			['CallTest_StackClassB::getStaticStackPreviousNamesCB'],
			CallTest_StackClassA::getStaticStackPreviousNamesCA(true, true, 0, 1)
		);
		$this->assertSame(
			['getStackPreviousNamesCB', 'getStackPreviousNamesCA'], $a->getStackPreviousNamesCA(false, false, 0, 2)
		);
		$this->assertSame([
				CallTest_StackClassB::class . '::getStackPreviousNamesCB',
				CallTest_StackClassA::class . '::getStackPreviousNamesCA'
			], $a->getStackPreviousNamesCA(true, false, 0, 2)
		);
		$this->assertSame(
			['CallTest_StackClassB::getStackPreviousNamesCB', 'CallTest_StackClassA::getStackPreviousNamesCA'],
			$a->getStackPreviousNamesCA(true, true, 0, 2)
		);
		$this->assertSame(
			['getStaticStackPreviousNamesCB', 'getStaticStackPreviousNamesCA'],
			$a->getStaticStackPreviousNamesCA(false, false, 0, 2)
		);
		$this->assertSame([
				CallTest_StackClassB::class . '::getStaticStackPreviousNamesCB',
				CallTest_StackClassA::class . '::getStaticStackPreviousNamesCA'
			], $a->getStaticStackPreviousNamesCA(true, false, 0, 2)
		);
		$this->assertSame([
				'CallTest_StackClassB::getStaticStackPreviousNamesCB',
				'CallTest_StackClassA::getStaticStackPreviousNamesCA'
			], $a->getStaticStackPreviousNamesCA(true, true, 0, 2)
		);
		$this->assertSame(
			['getStaticStackPreviousNamesCB', 'getStaticStackPreviousNamesCA'],
			CallTest_StackClassA::getStaticStackPreviousNamesCA(false, false, 0, 2)
		);
		$this->assertSame([
				CallTest_StackClassB::class . '::getStaticStackPreviousNamesCB',
				CallTest_StackClassA::class . '::getStaticStackPreviousNamesCA'
			], CallTest_StackClassA::getStaticStackPreviousNamesCA(true, false, 0, 2)
		);
		$this->assertSame([
				'CallTest_StackClassB::getStaticStackPreviousNamesCB',
				'CallTest_StackClassA::getStaticStackPreviousNamesCA'
			], CallTest_StackClassA::getStaticStackPreviousNamesCA(true, true, 0, 2)
		);
		$this->assertSame(
			['getStackPreviousNamesCB', 'getStackPreviousNamesCA', 'testStackPreviousNames'],
			$a->getStackPreviousNamesCA(false, false, 0, 3)
		);
		$this->assertSame([
				CallTest_StackClassB::class . '::getStackPreviousNamesCB',
				CallTest_StackClassA::class . '::getStackPreviousNamesCA',
				static::class . '::testStackPreviousNames'
			], $a->getStackPreviousNamesCA(true, false, 0, 3)
		);
		$this->assertSame([
				'CallTest_StackClassB::getStackPreviousNamesCB',
				'CallTest_StackClassA::getStackPreviousNamesCA',
				'CallTest::testStackPreviousNames'
			], $a->getStackPreviousNamesCA(true, true, 0, 3)
		);
		$this->assertSame(
			['getStaticStackPreviousNamesCB', 'getStaticStackPreviousNamesCA', 'testStackPreviousNames'],
			$a->getStaticStackPreviousNamesCA(false, false, 0, 3)
		);
		$this->assertSame([
				CallTest_StackClassB::class . '::getStaticStackPreviousNamesCB',
				CallTest_StackClassA::class . '::getStaticStackPreviousNamesCA',
				static::class . '::testStackPreviousNames'
			], $a->getStaticStackPreviousNamesCA(true, false, 0, 3)
		);
		$this->assertSame([
				'CallTest_StackClassB::getStaticStackPreviousNamesCB',
				'CallTest_StackClassA::getStaticStackPreviousNamesCA',
				'CallTest::testStackPreviousNames'
			], $a->getStaticStackPreviousNamesCA(true, true, 0, 3)
		);
		$this->assertSame(
			['getStaticStackPreviousNamesCB', 'getStaticStackPreviousNamesCA', 'testStackPreviousNames'],
			CallTest_StackClassA::getStaticStackPreviousNamesCA(false, false, 0, 3)
		);
		$this->assertSame([
				CallTest_StackClassB::class . '::getStaticStackPreviousNamesCB',
				CallTest_StackClassA::class . '::getStaticStackPreviousNamesCA',
				static::class . '::testStackPreviousNames'
			], CallTest_StackClassA::getStaticStackPreviousNamesCA(true, false, 0, 3)
		);
		$this->assertSame([
				'CallTest_StackClassB::getStaticStackPreviousNamesCB',
				'CallTest_StackClassA::getStaticStackPreviousNamesCA',
				'CallTest::testStackPreviousNames'
			], CallTest_StackClassA::getStaticStackPreviousNamesCA(true, true, 0, 3)
		);
		$this->assertSame(
			['getStackPreviousNamesCA', 'testStackPreviousNames'], $a->getStackPreviousNamesCA(false, false, 1, 2)
		);
		$this->assertSame(
			[CallTest_StackClassA::class . '::getStackPreviousNamesCA', static::class . '::testStackPreviousNames'],
			$a->getStackPreviousNamesCA(true, false, 1, 2)
		);
		$this->assertSame(
			['CallTest_StackClassA::getStackPreviousNamesCA', 'CallTest::testStackPreviousNames'],
			$a->getStackPreviousNamesCA(true, true, 1, 2)
		);
		$this->assertSame(
			['getStaticStackPreviousNamesCA', 'testStackPreviousNames'],
			$a->getStaticStackPreviousNamesCA(false, false, 1, 2)
		);
		$this->assertSame([
				CallTest_StackClassA::class . '::getStaticStackPreviousNamesCA',
				static::class . '::testStackPreviousNames'
			], $a->getStaticStackPreviousNamesCA(true, false, 1, 2)
		);
		$this->assertSame([
				'CallTest_StackClassA::getStaticStackPreviousNamesCA',
				'CallTest::testStackPreviousNames'
			], $a->getStaticStackPreviousNamesCA(true, true, 1, 2)
		);
		$this->assertSame(
			['getStaticStackPreviousNamesCA', 'testStackPreviousNames'],
			CallTest_StackClassA::getStaticStackPreviousNamesCA(false, false, 1, 2)
		);
		$this->assertSame([
				CallTest_StackClassA::class . '::getStaticStackPreviousNamesCA',
				static::class . '::testStackPreviousNames'
			], CallTest_StackClassA::getStaticStackPreviousNamesCA(true, false, 1, 2)
		);
		$this->assertSame(
			['CallTest_StackClassA::getStaticStackPreviousNamesCA', 'CallTest::testStackPreviousNames'],
			CallTest_StackClassA::getStaticStackPreviousNamesCA(true, true, 1, 2)
		);
		$this->assertSame(['testStackPreviousNames'], $a->getStackPreviousNamesCA(false, false, 2, 1));
		$this->assertSame(
			[static::class . '::testStackPreviousNames'], $a->getStackPreviousNamesCA(true, false, 2, 1)
		);
		$this->assertSame(['CallTest::testStackPreviousNames'], $a->getStackPreviousNamesCA(true, true, 2, 1));
		$this->assertSame(['testStackPreviousNames'], $a->getStaticStackPreviousNamesCA(false, false, 2, 1));
		$this->assertSame(
			[static::class . '::testStackPreviousNames'], $a->getStaticStackPreviousNamesCA(true, false, 2, 1)
		);
		$this->assertSame(['CallTest::testStackPreviousNames'], $a->getStaticStackPreviousNamesCA(true, true, 2, 1));
		$this->assertSame(
			['testStackPreviousNames'], CallTest_StackClassA::getStaticStackPreviousNamesCA(false, false, 2, 1)
		);
		$this->assertSame(
			[static::class . '::testStackPreviousNames'],
			CallTest_StackClassA::getStaticStackPreviousNamesCA(true, false, 2, 1)
		);
		$this->assertSame(
			['CallTest::testStackPreviousNames'], CallTest_StackClassA::getStaticStackPreviousNamesCA(true, true, 2, 1)
		);
		$this->assertSame([], $a->getStackPreviousNamesCA(false, false, 10000));
		$this->assertSame([], $a->getStackPreviousNamesCA(true, false, 10000));
		$this->assertSame([], $a->getStackPreviousNamesCA(true, true, 10000));
		$this->assertSame([], $a->getStaticStackPreviousNamesCA(false, false, 10000));
		$this->assertSame([], $a->getStaticStackPreviousNamesCA(true, false, 10000));
		$this->assertSame([], $a->getStaticStackPreviousNamesCA(true, true, 10000));
		$this->assertSame([], CallTest_StackClassA::getStaticStackPreviousNamesCA(false, false, 10000));
		$this->assertSame([], CallTest_StackClassA::getStaticStackPreviousNamesCA(true, false, 10000));
		$this->assertSame([], CallTest_StackClassA::getStaticStackPreviousNamesCA(true, true, 10000));
	}
	
	/**
	 * Test <code>halt</code> method.
	 * 
	 * @testdox Call::halt(...)
	 * 
	 * @return void
	 */
	public function testHalt(): void
	{
		//exception 1
		try {
			UCall::halt();
			$this->fail("Expected NotAllowed exception not thrown.");
		} catch (Exceptions\Halt\NotAllowed $exception) {
			$this->assertSame('testHalt', $exception->function_name);
			$this->assertSame($this, $exception->object_class);
			$this->assertNull($exception->error_message);
			$this->assertNull($exception->hint_message);
		}
		
		//exception 2
		try {
			UCall::halt([
				'error_message' => "Potatoes are not for sale.",
				'hint_message' => "Try some eggs.",
				'function_name' => 'barFoo',
				'object_class' => CallTest_Class::class
			]);
			$this->fail("Expected NotAllowed exception not thrown.");
		} catch (Exceptions\Halt\NotAllowed $exception) {
			$this->assertSame('barFoo', $exception->function_name);
			$this->assertSame(CallTest_Class::class, $exception->object_class);
			$this->assertSame("Potatoes are not for sale.", $exception->error_message);
			$this->assertSame("Try some eggs.", $exception->hint_message);
		}
		
		//exception 3
		try {
			UCall::halt([
				'error_message' => "{{product1}} are not for sale.",
				'hint_message' => "Try some {{product2}}.",
				'function_name' => 'foo2Bar',
				'stack_offset' => 1,
				'parameters' => ['product1' => 'Bananas', 'product2' => 'peaches']
			]);
			$this->fail("Expected NotAllowed exception not thrown.");
		} catch (Exceptions\Halt\NotAllowed $exception) {
			$this->assertSame('foo2Bar', $exception->function_name);
			$this->assertSame(UCall::stackPreviousObjectClass(), $exception->object_class);
			$this->assertSame("\"Bananas\" are not for sale.", $exception->error_message);
			$this->assertSame("Try some \"peaches\".", $exception->hint_message);
		}
		
		//exception 4
		try {
			UCall::halt([
				'error_message' => "There is only {{count1}} {{product1}}.",
				'error_message_plural' => "There are only {{count1}} {{product1}}.",
				'error_message_number_placeholder' => 'count1',
				'hint_message' => "Try this {{count2}} {{product2}}.",
				'hint_message_plural' => "Try these {{count2}} {{product2}}.",
				'hint_message_number_placeholder' => 'count2',
				'function_name' => 'fBar',
				'error_message_number' => 7,
				'hint_message_number' => 2,
				'stack_offset' => 2,
				'parameters' => ['product1' => 'bananas', 'product2' => 'peaches'],
				'stringifier' => function (string $placeholder, $value): ?string {
					return is_int($value) ? (string)$value : "[{$value}]";
				}
			]);
			$this->fail("Expected NotAllowed exception not thrown.");
		} catch (Exceptions\Halt\NotAllowed $exception) {
			$this->assertSame('fBar', $exception->function_name);
			$this->assertSame(UCall::stackPreviousObjectClass(1), $exception->object_class);
			$this->assertSame("There are only 7 [bananas].", $exception->error_message);
			$this->assertSame("Try these 2 [peaches].", $exception->hint_message);
		}
	}
	
	/**
	 * Test <code>guard</code> method.
	 * 
	 * @testdox Call::guard(...)
	 * 
	 * @return void
	 */
	public function testGuard(): void
	{
		//void
		$this->assertNull(UCall::guard(true));
		
		//exception 1
		try {
			UCall::guard(false);
			$this->fail("Expected NotAllowed exception not thrown.");
		} catch (Exceptions\Halt\NotAllowed $exception) {
			$this->assertSame('testGuard', $exception->function_name);
			$this->assertSame($this, $exception->object_class);
			$this->assertNull($exception->error_message);
			$this->assertNull($exception->hint_message);
		}
		
		//exception 2
		try {
			UCall::guard(false, [
				'error_message' => "Potatoes are not for sale.",
				'hint_message' => "Try some eggs.",
				'function_name' => 'barFoo',
				'object_class' => CallTest_Class::class
			]);
			$this->fail("Expected NotAllowed exception not thrown.");
		} catch (Exceptions\Halt\NotAllowed $exception) {
			$this->assertSame('barFoo', $exception->function_name);
			$this->assertSame(CallTest_Class::class, $exception->object_class);
			$this->assertSame("Potatoes are not for sale.", $exception->error_message);
			$this->assertSame("Try some eggs.", $exception->hint_message);
		}
		
		//exception 3
		try {
			UCall::guard(false, [
				'error_message' => "{{product1}} are not for sale.",
				'hint_message' => "Try some {{product2}}.",
				'function_name' => 'foo2Bar',
				'stack_offset' => 1,
				'parameters' => ['product1' => 'Bananas', 'product2' => 'peaches']
			]);
			$this->fail("Expected NotAllowed exception not thrown.");
		} catch (Exceptions\Halt\NotAllowed $exception) {
			$this->assertSame('foo2Bar', $exception->function_name);
			$this->assertSame(UCall::stackPreviousObjectClass(), $exception->object_class);
			$this->assertSame("\"Bananas\" are not for sale.", $exception->error_message);
			$this->assertSame("Try some \"peaches\".", $exception->hint_message);
		}
		
		//exception 4
		try {
			UCall::guard(false, [
				'error_message' => "There is only {{count1}} {{product1}}.",
				'error_message_plural' => "There are only {{count1}} {{product1}}.",
				'error_message_number_placeholder' => 'count1',
				'hint_message' => "Try this {{count2}} {{product2}}.",
				'hint_message_plural' => "Try these {{count2}} {{product2}}.",
				'hint_message_number_placeholder' => 'count2',
				'function_name' => 'fBar',
				'error_message_number' => 7,
				'hint_message_number' => 2,
				'stack_offset' => 2,
				'parameters' => ['product1' => 'bananas', 'product2' => 'peaches'],
				'stringifier' => function (string $placeholder, $value): ?string {
					return is_int($value) ? (string)$value : "[{$value}]";
				}
			]);
			$this->fail("Expected NotAllowed exception not thrown.");
		} catch (Exceptions\Halt\NotAllowed $exception) {
			$this->assertSame('fBar', $exception->function_name);
			$this->assertSame(UCall::stackPreviousObjectClass(1), $exception->object_class);
			$this->assertSame("There are only 7 [bananas].", $exception->error_message);
			$this->assertSame("Try these 2 [peaches].", $exception->hint_message);
		}
		
		//exception 5
		try {
			UCall::guard(false, function () {
				return [
					'error_message' => "There is only {{count}} {{product1}}.",
					'error_message_plural' => "There are only {{count}} {{product1}}.",
					'error_message_number_placeholder' => 'count',
					'hint_message' => "Try this {{count}} {{product2}}.",
					'hint_message_plural' => "Try these {{count}} {{product2}}.",
					'hint_message_number_placeholder' => 'count',
					'error_message_number' => 1,
					'hint_message_number' => 1,
					'parameters' => ['product1' => 'banana', 'product2' => 'peach'],
					'string_options' => [
						'prepend_type' => true
					]
				];
			});
			$this->fail("Expected NotAllowed exception not thrown.");
		} catch (Exceptions\Halt\NotAllowed $exception) {
			$this->assertSame('testGuard', $exception->function_name);
			$this->assertSame($this, $exception->object_class);
			$this->assertSame("There is only (integer)1 (string)\"banana\".", $exception->error_message);
			$this->assertSame("Try this (integer)1 (string)\"peach\".", $exception->hint_message);
		}
	}
	
	/**
	 * Test <code>haltParameter</code> method.
	 * 
	 * @testdox Call::haltParameter(...)
	 * 
	 * @return void
	 */
	public function testHaltParameter(): void
	{
		//exception 1
		try {
			UCall::haltParameter('foobar', null);
			$this->fail("Expected ParameterNotAllowed exception not thrown.");
		} catch (Exceptions\Halt\ParameterNotAllowed $exception) {
			$this->assertSame('testHaltParameter', $exception->function_name);
			$this->assertSame($this, $exception->object_class);
			$this->assertNull($exception->error_message);
			$this->assertNull($exception->hint_message);
		}
		
		//exception 2
		try {
			UCall::haltParameter('foobar', null, [
				'error_message' => "Potatoes are not for sale.",
				'hint_message' => "Try some eggs.",
				'function_name' => 'barFoo',
				'object_class' => CallTest_Class::class
			]);
			$this->fail("Expected ParameterNotAllowed exception not thrown.");
		} catch (Exceptions\Halt\ParameterNotAllowed $exception) {
			$this->assertSame('barFoo', $exception->function_name);
			$this->assertSame(CallTest_Class::class, $exception->object_class);
			$this->assertSame("Potatoes are not for sale.", $exception->error_message);
			$this->assertSame("Try some eggs.", $exception->hint_message);
		}
		
		//exception 3
		try {
			UCall::haltParameter('foobar', null, [
				'error_message' => "{{product1}} are not for sale.",
				'hint_message' => "Try some {{product2}}.",
				'function_name' => 'foo2Bar',
				'stack_offset' => 1,
				'parameters' => ['product1' => 'Bananas', 'product2' => 'peaches']
			]);
			$this->fail("Expected ParameterNotAllowed exception not thrown.");
		} catch (Exceptions\Halt\ParameterNotAllowed $exception) {
			$this->assertSame('foo2Bar', $exception->function_name);
			$this->assertSame(UCall::stackPreviousObjectClass(), $exception->object_class);
			$this->assertSame("\"Bananas\" are not for sale.", $exception->error_message);
			$this->assertSame("Try some \"peaches\".", $exception->hint_message);
		}
		
		//exception 4
		try {
			UCall::haltParameter('foobar', null, [
				'error_message' => "There is only {{count1}} {{product1}}.",
				'error_message_plural' => "There are only {{count1}} {{product1}}.",
				'error_message_number_placeholder' => 'count1',
				'hint_message' => "Try this {{count2}} {{product2}}.",
				'hint_message_plural' => "Try these {{count2}} {{product2}}.",
				'hint_message_number_placeholder' => 'count2',
				'function_name' => 'fBar',
				'error_message_number' => 7,
				'hint_message_number' => 2,
				'stack_offset' => 2,
				'parameters' => ['product1' => 'bananas', 'product2' => 'peaches'],
				'stringifier' => function (string $placeholder, $value): ?string {
					return is_int($value) ? (string)$value : "[{$value}]";
				}
			]);
			$this->fail("Expected ParameterNotAllowed exception not thrown.");
		} catch (Exceptions\Halt\ParameterNotAllowed $exception) {
			$this->assertSame('fBar', $exception->function_name);
			$this->assertSame(UCall::stackPreviousObjectClass(1), $exception->object_class);
			$this->assertSame("There are only 7 [bananas].", $exception->error_message);
			$this->assertSame("Try these 2 [peaches].", $exception->hint_message);
		}
	}
	
	/**
	 * Test <code>guardParameter</code> method.
	 * 
	 * @testdox Call::guardParameter(...)
	 * 
	 * @return void
	 */
	public function testGuardParameter(): void
	{
		//void
		$this->assertNull(UCall::guardParameter('foobar', null, true));
		
		//exception 1
		try {
			UCall::guardParameter('foobar', null, false);
			$this->fail("Expected ParameterNotAllowed exception not thrown.");
		} catch (Exceptions\Halt\ParameterNotAllowed $exception) {
			$this->assertSame('testGuardParameter', $exception->function_name);
			$this->assertSame($this, $exception->object_class);
			$this->assertNull($exception->error_message);
			$this->assertNull($exception->hint_message);
		}
		
		//exception 2
		try {
			UCall::guardParameter('foobar', null, false, [
				'error_message' => "Potatoes are not for sale.",
				'hint_message' => "Try some eggs.",
				'function_name' => 'barFoo',
				'object_class' => CallTest_Class::class
			]);
			$this->fail("Expected ParameterNotAllowed exception not thrown.");
		} catch (Exceptions\Halt\ParameterNotAllowed $exception) {
			$this->assertSame('barFoo', $exception->function_name);
			$this->assertSame(CallTest_Class::class, $exception->object_class);
			$this->assertSame("Potatoes are not for sale.", $exception->error_message);
			$this->assertSame("Try some eggs.", $exception->hint_message);
		}
		
		//exception 3
		try {
			UCall::guardParameter('foobar', null, false, [
				'error_message' => "{{product1}} are not for sale.",
				'hint_message' => "Try some {{product2}}.",
				'function_name' => 'foo2Bar',
				'stack_offset' => 1,
				'parameters' => ['product1' => 'Bananas', 'product2' => 'peaches']
			]);
			$this->fail("Expected ParameterNotAllowed exception not thrown.");
		} catch (Exceptions\Halt\ParameterNotAllowed $exception) {
			$this->assertSame('foo2Bar', $exception->function_name);
			$this->assertSame(UCall::stackPreviousObjectClass(), $exception->object_class);
			$this->assertSame("\"Bananas\" are not for sale.", $exception->error_message);
			$this->assertSame("Try some \"peaches\".", $exception->hint_message);
		}
		
		//exception 4
		try {
			UCall::guardParameter('foobar', null, false, [
				'error_message' => "There is only {{count1}} {{product1}}.",
				'error_message_plural' => "There are only {{count1}} {{product1}}.",
				'error_message_number_placeholder' => 'count1',
				'hint_message' => "Try this {{count2}} {{product2}}.",
				'hint_message_plural' => "Try these {{count2}} {{product2}}.",
				'hint_message_number_placeholder' => 'count2',
				'function_name' => 'fBar',
				'error_message_number' => 7,
				'hint_message_number' => 2,
				'stack_offset' => 2,
				'parameters' => ['product1' => 'bananas', 'product2' => 'peaches'],
				'stringifier' => function (string $placeholder, $value): ?string {
					return is_int($value) ? (string)$value : "[{$value}]";
				}
			]);
			$this->fail("Expected ParameterNotAllowed exception not thrown.");
		} catch (Exceptions\Halt\ParameterNotAllowed $exception) {
			$this->assertSame('fBar', $exception->function_name);
			$this->assertSame(UCall::stackPreviousObjectClass(1), $exception->object_class);
			$this->assertSame("There are only 7 [bananas].", $exception->error_message);
			$this->assertSame("Try these 2 [peaches].", $exception->hint_message);
		}
		
		//exception 5
		try {
			UCall::guardParameter('foobar', null, false, function () {
				return [
					'error_message' => "There is only {{count}} {{product1}}.",
					'error_message_plural' => "There are only {{count}} {{product1}}.",
					'error_message_number_placeholder' => 'count',
					'hint_message' => "Try this {{count}} {{product2}}.",
					'hint_message_plural' => "Try these {{count}} {{product2}}.",
					'hint_message_number_placeholder' => 'count',
					'error_message_number' => 1,
					'hint_message_number' => 1,
					'parameters' => ['product1' => 'banana', 'product2' => 'peach'],
					'string_options' => [
						'prepend_type' => true
					]
				];
			});
			$this->fail("Expected ParameterNotAllowed exception not thrown.");
		} catch (Exceptions\Halt\ParameterNotAllowed $exception) {
			$this->assertSame('testGuardParameter', $exception->function_name);
			$this->assertSame($this, $exception->object_class);
			$this->assertSame("There is only (integer)1 (string)\"banana\".", $exception->error_message);
			$this->assertSame("Try this (integer)1 (string)\"peach\".", $exception->hint_message);
		}
	}
	
	/**
	 * Test <code>haltInternal</code> method.
	 * 
	 * @testdox Call::haltInternal(...)
	 * 
	 * @return void
	 */
	public function testHaltInternal(): void
	{
		//exception 1
		try {
			UCall::haltInternal();
			$this->fail("Expected InternalError exception not thrown.");
		} catch (Exceptions\Halt\InternalError $exception) {
			$this->assertSame('testHaltInternal', $exception->function_name);
			$this->assertSame($this, $exception->object_class);
			$this->assertNull($exception->error_message);
			$this->assertNull($exception->hint_message);
		}
		
		//exception 2
		try {
			UCall::haltInternal([
				'error_message' => "Potatoes are not for sale.",
				'hint_message' => "Try some eggs.",
				'function_name' => 'barFoo',
				'object_class' => CallTest_Class::class
			]);
			$this->fail("Expected InternalError exception not thrown.");
		} catch (Exceptions\Halt\InternalError $exception) {
			$this->assertSame('barFoo', $exception->function_name);
			$this->assertSame(CallTest_Class::class, $exception->object_class);
			$this->assertSame("Potatoes are not for sale.", $exception->error_message);
			$this->assertSame("Try some eggs.", $exception->hint_message);
		}
		
		//exception 3
		try {
			UCall::haltInternal([
				'error_message' => "{{product1}} are not for sale.",
				'hint_message' => "Try some {{product2}}.",
				'function_name' => 'foo2Bar',
				'stack_offset' => 1,
				'parameters' => ['product1' => 'Bananas', 'product2' => 'peaches']
			]);
			$this->fail("Expected InternalError exception not thrown.");
		} catch (Exceptions\Halt\InternalError $exception) {
			$this->assertSame('foo2Bar', $exception->function_name);
			$this->assertSame(UCall::stackPreviousObjectClass(), $exception->object_class);
			$this->assertSame("\"Bananas\" are not for sale.", $exception->error_message);
			$this->assertSame("Try some \"peaches\".", $exception->hint_message);
		}
		
		//exception 4
		try {
			UCall::haltInternal([
				'error_message' => "There is only {{count1}} {{product1}}.",
				'error_message_plural' => "There are only {{count1}} {{product1}}.",
				'error_message_number_placeholder' => 'count1',
				'hint_message' => "Try this {{count2}} {{product2}}.",
				'hint_message_plural' => "Try these {{count2}} {{product2}}.",
				'hint_message_number_placeholder' => 'count2',
				'function_name' => 'fBar',
				'error_message_number' => 7,
				'hint_message_number' => 2,
				'stack_offset' => 2,
				'parameters' => ['product1' => 'bananas', 'product2' => 'peaches'],
				'stringifier' => function (string $placeholder, $value): ?string {
					return is_int($value) ? (string)$value : "[{$value}]";
				}
			]);
			$this->fail("Expected InternalError exception not thrown.");
		} catch (Exceptions\Halt\InternalError $exception) {
			$this->assertSame('fBar', $exception->function_name);
			$this->assertSame(UCall::stackPreviousObjectClass(1), $exception->object_class);
			$this->assertSame("There are only 7 [bananas].", $exception->error_message);
			$this->assertSame("Try these 2 [peaches].", $exception->hint_message);
		}
	}
	
	/**
	 * Test <code>guardInternal</code> method.
	 * 
	 * @testdox Call::guardInternal(...)
	 * 
	 * @return void
	 */
	public function testGuardInternal(): void
	{
		//void
		$this->assertNull(UCall::guardInternal(true));
		
		//exception 1
		try {
			UCall::guardInternal(false);
			$this->fail("Expected InternalError exception not thrown.");
		} catch (Exceptions\Halt\InternalError $exception) {
			$this->assertSame('testGuardInternal', $exception->function_name);
			$this->assertSame($this, $exception->object_class);
			$this->assertNull($exception->error_message);
			$this->assertNull($exception->hint_message);
		}
		
		//exception 2
		try {
			UCall::guardInternal(false, [
				'error_message' => "Potatoes are not for sale.",
				'hint_message' => "Try some eggs.",
				'function_name' => 'barFoo',
				'object_class' => CallTest_Class::class
			]);
			$this->fail("Expected InternalError exception not thrown.");
		} catch (Exceptions\Halt\InternalError $exception) {
			$this->assertSame('barFoo', $exception->function_name);
			$this->assertSame(CallTest_Class::class, $exception->object_class);
			$this->assertSame("Potatoes are not for sale.", $exception->error_message);
			$this->assertSame("Try some eggs.", $exception->hint_message);
		}
		
		//exception 3
		try {
			UCall::guardInternal(false, [
				'error_message' => "{{product1}} are not for sale.",
				'hint_message' => "Try some {{product2}}.",
				'function_name' => 'foo2Bar',
				'stack_offset' => 1,
				'parameters' => ['product1' => 'Bananas', 'product2' => 'peaches']
			]);
			$this->fail("Expected InternalError exception not thrown.");
		} catch (Exceptions\Halt\InternalError $exception) {
			$this->assertSame('foo2Bar', $exception->function_name);
			$this->assertSame(UCall::stackPreviousObjectClass(), $exception->object_class);
			$this->assertSame("\"Bananas\" are not for sale.", $exception->error_message);
			$this->assertSame("Try some \"peaches\".", $exception->hint_message);
		}
		
		//exception 4
		try {
			UCall::guardInternal(false, [
				'error_message' => "There is only {{count1}} {{product1}}.",
				'error_message_plural' => "There are only {{count1}} {{product1}}.",
				'error_message_number_placeholder' => 'count1',
				'hint_message' => "Try this {{count2}} {{product2}}.",
				'hint_message_plural' => "Try these {{count2}} {{product2}}.",
				'hint_message_number_placeholder' => 'count2',
				'function_name' => 'fBar',
				'error_message_number' => 7,
				'hint_message_number' => 2,
				'stack_offset' => 2,
				'parameters' => ['product1' => 'bananas', 'product2' => 'peaches'],
				'stringifier' => function (string $placeholder, $value): ?string {
					return is_int($value) ? (string)$value : "[{$value}]";
				}
			]);
			$this->fail("Expected InternalError exception not thrown.");
		} catch (Exceptions\Halt\InternalError $exception) {
			$this->assertSame('fBar', $exception->function_name);
			$this->assertSame(UCall::stackPreviousObjectClass(1), $exception->object_class);
			$this->assertSame("There are only 7 [bananas].", $exception->error_message);
			$this->assertSame("Try these 2 [peaches].", $exception->hint_message);
		}
		
		//exception 5
		try {
			UCall::guardInternal(false, function () {
				return [
					'error_message' => "There is only {{count}} {{product1}}.",
					'error_message_plural' => "There are only {{count}} {{product1}}.",
					'error_message_number_placeholder' => 'count',
					'hint_message' => "Try this {{count}} {{product2}}.",
					'hint_message_plural' => "Try these {{count}} {{product2}}.",
					'hint_message_number_placeholder' => 'count',
					'error_message_number' => 1,
					'hint_message_number' => 1,
					'parameters' => ['product1' => 'banana', 'product2' => 'peach'],
					'string_options' => [
						'prepend_type' => true
					]
				];
			});
			$this->fail("Expected InternalError exception not thrown.");
		} catch (Exceptions\Halt\InternalError $exception) {
			$this->assertSame('testGuardInternal', $exception->function_name);
			$this->assertSame($this, $exception->object_class);
			$this->assertSame("There is only (integer)1 (string)\"banana\".", $exception->error_message);
			$this->assertSame("Try this (integer)1 (string)\"peach\".", $exception->hint_message);
		}
	}
	
	/**
	 * Test <code>haltExecution</code> method.
	 * 
	 * @testdox Call::haltExecution(...)
	 * 
	 * @return void
	 */
	public function testHaltExecution(): void
	{
		//initialize
		$functions = [
			function () {},
			[CallTest_HaltClass::class, 'getBar']
		];
		$extra_options = [
			['value' => '_bar_'],
			['exception' => new \Exception("Something went very wrong!")]
		];
		
		//exception 1
		foreach ($functions as $function) {
			foreach ($extra_options as $e_options) {
				try {
					UCall::haltExecution($function, $e_options);
					$this->fail("Expected ReturnError exception not thrown.");
				} catch (Exceptions\Halt\ReturnError $exception) {
					if (isset($e_options['value'])) {
						$this->assertNull($exception->error_message);
						$this->assertInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
					} else {
						$this->assertSame("Something went very wrong!", $exception->error_message);
						$this->assertNotInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
					}
					$this->assertSame('testHaltExecution', $exception->function_name);
					$this->assertSame($this, $exception->object_class);
					$this->assertNull($exception->hint_message);
					$this->assertSame($e_options['value'] ?? null, $exception->value);
					$this->assertSame(
						is_array($function) ? CallTest_HaltClass::class . '::getBar' : null,
						$exception->exec_function_full_name
					);
				}
			}
		}
		
		//exception 2
		foreach ($functions as $function) {
			foreach ($extra_options as $e_options) {
				try {
					UCall::haltExecution($function, $e_options + [
						'error_message' => "Potatoes are not for sale.",
						'hint_message' => "Try some eggs.",
						'function_name' => 'barFoo',
						'object_class' => CallTest_Class::class
					]);
					$this->fail("Expected ReturnError exception not thrown.");
				} catch (Exceptions\Halt\ReturnError $exception) {
					if (isset($e_options['value'])) {
						$this->assertInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
					} else {
						$this->assertNotInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
					}
					$this->assertSame('barFoo', $exception->function_name);
					$this->assertSame(CallTest_Class::class, $exception->object_class);
					$this->assertSame("Potatoes are not for sale.", $exception->error_message);
					$this->assertSame("Try some eggs.", $exception->hint_message);
					$this->assertSame($e_options['value'] ?? null, $exception->value);
					$this->assertSame(
						is_array($function) ? CallTest_HaltClass::class . '::getBar' : null,
						$exception->exec_function_full_name
					);
				}
			}
		}
		
		//exception 3
		foreach ($functions as $function) {
			foreach ($extra_options as $e_options) {
				try {
					UCall::haltExecution($function, $e_options + [
						'error_message' => "{{product1}} are not for sale.",
						'hint_message' => "Try some {{product2}}.",
						'function_name' => 'foo2Bar',
						'stack_offset' => 1,
						'parameters' => ['product1' => 'Bananas', 'product2' => 'peaches']
					]);
					$this->fail("Expected ReturnError exception not thrown.");
				} catch (Exceptions\Halt\ReturnError $exception) {
					if (isset($e_options['value'])) {
						$this->assertInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
					} else {
						$this->assertNotInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
					}
					$this->assertSame('foo2Bar', $exception->function_name);
					$this->assertSame(UCall::stackPreviousObjectClass(), $exception->object_class);
					$this->assertSame("\"Bananas\" are not for sale.", $exception->error_message);
					$this->assertSame("Try some \"peaches\".", $exception->hint_message);
					$this->assertSame($e_options['value'] ?? null, $exception->value);
					$this->assertSame(
						is_array($function) ? CallTest_HaltClass::class . '::getBar' : null,
						$exception->exec_function_full_name
					);
				}
			}
		}
		
		//exception 4
		foreach ($functions as $function) {
			foreach ($extra_options as $e_options) {
				try {
					UCall::haltExecution($function, $e_options + [
						'error_message' => "There is only {{count1}} {{product1}}.",
						'error_message_plural' => "There are only {{count1}} {{product1}}.",
						'error_message_number_placeholder' => 'count1',
						'hint_message' => "Try this {{count2}} {{product2}}.",
						'hint_message_plural' => "Try these {{count2}} {{product2}}.",
						'hint_message_number_placeholder' => 'count2',
						'function_name' => 'fBar',
						'error_message_number' => 7,
						'hint_message_number' => 2,
						'stack_offset' => 2,
						'parameters' => ['product1' => 'bananas', 'product2' => 'peaches'],
						'stringifier' => function (string $placeholder, $value): ?string {
							return is_int($value) ? (string)$value : "[{$value}]";
						}
					]);
					$this->fail("Expected ReturnError exception not thrown.");
				} catch (Exceptions\Halt\ReturnError $exception) {
					if (isset($e_options['value'])) {
						$this->assertInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
					} else {
						$this->assertNotInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
					}
					$this->assertSame('fBar', $exception->function_name);
					$this->assertSame(UCall::stackPreviousObjectClass(1), $exception->object_class);
					$this->assertSame("There are only 7 [bananas].", $exception->error_message);
					$this->assertSame("Try these 2 [peaches].", $exception->hint_message);
					$this->assertSame($e_options['value'] ?? null, $exception->value);
					$this->assertSame(
						is_array($function) ? CallTest_HaltClass::class . '::getBar' : null,
						$exception->exec_function_full_name
					);
				}
			}
		}
	}
	
	/**
	 * Test <code>guardExecution</code> method.
	 * 
	 * @testdox Call::guardExecution(...)
	 * 
	 * @return void
	 */
	public function testGuardExecution(): void
	{
		//initialize
		$functions = [
			function (string $a, int $b = 10) {
				$this->assertSame('_foo_', $a);
				$this->assertSame(173, $b);
				return '_bar_';
			},
			[CallTest_GuardClass::class, 'getBar']
		];
		$parameters = ['_foo_', 173];
		$callback_true = function (&$value): bool {
			$this->assertSame('_bar_', $value);
			$value = 'foo555';
			return true;
		};
		$callback_false = function (&$value): bool {
			$this->assertSame('_bar_', $value);
			$value = 'foo555';
			return false;
		};
		$callback_exception = function (&$value): bool {
			$this->assertSame('_bar_', $value);
			$value = 'foo555';
			throw new \Exception();
		};
		
		//success
		foreach ($functions as $function) {
			$this->assertSame('foo555', UCall::guardExecution($function, $parameters, $callback_true));
		}
		
		//exception 1
		foreach ($functions as $function) {
			foreach ([$callback_false, $callback_exception] as $callback) {
				try {
					UCall::guardExecution($function, $parameters, $callback);
					$this->fail("Expected ReturnError exception not thrown.");
				} catch (Exceptions\Halt\ReturnError $exception) {
					$this->assertSame('testGuardExecution', $exception->function_name);
					$this->assertSame($this, $exception->object_class);
					if ($callback === $callback_false) {
						$this->assertInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
						$this->assertNull($exception->error_message);
					} else {
						$this->assertNotInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
						$this->assertNotNull($exception->error_message);
					}
					$this->assertNull($exception->hint_message);
					$this->assertSame('_bar_', $exception->value);
					$this->assertSame(
						is_array($function) ? CallTest_GuardClass::class . '::getBar' : null,
						$exception->exec_function_full_name
					);
				}
			}
		}
		
		//exception 2
		foreach ($functions as $function) {
			foreach ([$callback_false, $callback_exception] as $callback) {
				try {
					UCall::guardExecution($function, $parameters, $callback, [
						'error_message' => "Potatoes are not for sale.",
						'hint_message' => "Try some eggs.",
						'function_name' => 'barFoo',
						'object_class' => CallTest_Class::class
					]);
					$this->fail("Expected ReturnError exception not thrown.");
				} catch (Exceptions\Halt\ReturnError $exception) {
					if ($callback === $callback_false) {
						$this->assertInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
					} else {
						$this->assertNotInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
					}
					$this->assertSame('barFoo', $exception->function_name);
					$this->assertSame(CallTest_Class::class, $exception->object_class);
					$this->assertSame("Potatoes are not for sale.", $exception->error_message);
					$this->assertSame("Try some eggs.", $exception->hint_message);
					$this->assertSame('_bar_', $exception->value);
					$this->assertSame(
						is_array($function) ? CallTest_GuardClass::class . '::getBar' : null,
						$exception->exec_function_full_name
					);
				}
			}
		}
		
		//exception 3
		foreach ($functions as $function) {
			foreach ([$callback_false, $callback_exception] as $callback) {
				try {
					UCall::guardExecution($function, $parameters, $callback, [
						'error_message' => "{{product1}} are not for sale.",
						'hint_message' => "Try some {{product2}}.",
						'function_name' => 'foo2Bar',
						'stack_offset' => 1,
						'parameters' => ['product1' => 'Bananas', 'product2' => 'peaches']
					]);
					$this->fail("Expected ReturnError exception not thrown.");
				} catch (Exceptions\Halt\ReturnError $exception) {
					if ($callback === $callback_false) {
						$this->assertInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
					} else {
						$this->assertNotInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
					}
					$this->assertSame('foo2Bar', $exception->function_name);
					$this->assertSame(UCall::stackPreviousObjectClass(), $exception->object_class);
					$this->assertSame("\"Bananas\" are not for sale.", $exception->error_message);
					$this->assertSame("Try some \"peaches\".", $exception->hint_message);
					$this->assertSame('_bar_', $exception->value);
					$this->assertSame(
						is_array($function) ? CallTest_GuardClass::class . '::getBar' : null,
						$exception->exec_function_full_name
					);
				}
			}
		}
		
		//exception 4
		foreach ($functions as $function) {
			foreach ([$callback_false, $callback_exception] as $callback) {
				try {
					UCall::guardExecution($function, $parameters, $callback, [
						'error_message' => "There is only {{count1}} {{product1}}.",
						'error_message_plural' => "There are only {{count1}} {{product1}}.",
						'error_message_number_placeholder' => 'count1',
						'hint_message' => "Try this {{count2}} {{product2}}.",
						'hint_message_plural' => "Try these {{count2}} {{product2}}.",
						'hint_message_number_placeholder' => 'count2',
						'function_name' => 'fBar',
						'error_message_number' => 7,
						'hint_message_number' => 2,
						'stack_offset' => 2,
						'parameters' => ['product1' => 'bananas', 'product2' => 'peaches'],
						'stringifier' => function (string $placeholder, $value): ?string {
							return is_int($value) ? (string)$value : "[{$value}]";
						}
					]);
					$this->fail("Expected ReturnError exception not thrown.");
				} catch (Exceptions\Halt\ReturnError $exception) {
					if ($callback === $callback_false) {
						$this->assertInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
					} else {
						$this->assertNotInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
					}
					$this->assertSame('fBar', $exception->function_name);
					$this->assertSame(UCall::stackPreviousObjectClass(1), $exception->object_class);
					$this->assertSame("There are only 7 [bananas].", $exception->error_message);
					$this->assertSame("Try these 2 [peaches].", $exception->hint_message);
					$this->assertSame('_bar_', $exception->value);
					$this->assertSame(
						is_array($function) ? CallTest_GuardClass::class . '::getBar' : null,
						$exception->exec_function_full_name
					);
				}
			}
		}
		
		//exception 5
		foreach ($functions as $function) {
			foreach ([$callback_false, $callback_exception] as $callback) {
				try {
					UCall::guardExecution($function, $parameters, $callback, function () {
						return [
							'error_message' => "There is only {{count}} {{product1}}.",
							'error_message_plural' => "There are only {{count}} {{product1}}.",
							'error_message_number_placeholder' => 'count',
							'hint_message' => "Try this {{count}} {{product2}}.",
							'hint_message_plural' => "Try these {{count}} {{product2}}.",
							'hint_message_number_placeholder' => 'count',
							'error_message_number' => 1,
							'hint_message_number' => 1,
							'parameters' => ['product1' => 'banana', 'product2' => 'peach'],
							'string_options' => [
								'prepend_type' => true
							]
						];
					});
					$this->fail("Expected ReturnError exception not thrown.");
				} catch (Exceptions\Halt\ReturnError $exception) {
					if ($callback === $callback_false) {
						$this->assertInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
					} else {
						$this->assertNotInstanceOf(Exceptions\Halt\ReturnNotAllowed::class, $exception);
					}
					$this->assertSame('testGuardExecution', $exception->function_name);
					$this->assertSame($this, $exception->object_class);
					$this->assertSame("There is only (integer)1 (string)\"banana\".", $exception->error_message);
					$this->assertSame("Try this (integer)1 (string)\"peach\".", $exception->hint_message);
					$this->assertSame('_bar_', $exception->value);
					$this->assertSame(
						is_array($function) ? CallTest_GuardClass::class . '::getBar' : null,
						$exception->exec_function_full_name
					);
				}
			}
		}
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
	
	public function getGetFinalProtectedIntegerClosure(): Closure
	{
		return Closure::fromCallable([$this, 'getFinalProtectedInteger']);
	}
	
	public function getSetFinalProtectedIntegerClosure(): Closure
	{
		return Closure::fromCallable([$this, 'setFinalProtectedInteger']);
	}
	
	public static function getGetFinalProtectedStaticIntegerClosure(): Closure
	{
		return Closure::fromCallable([self::class, 'getFinalProtectedStaticInteger']);
	}
	
	public static function getSetFinalProtectedStaticIntegerClosure(): Closure
	{
		return Closure::fromCallable([self::class, 'setFinalProtectedStaticInteger']);
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



/** Test case dummy stack class A. */
class CallTest_StackClassA
{
	private CallTest_StackClassB $b;
	
	public function __construct(CallTest_StackClassB $b)
	{
		$this->b = $b;
	}
	
	public function getStackPreviousClassA(int $offset = 0): ?string
	{
		return UCall::stackPreviousClass($offset);
	}
	
	public function getStackPreviousClassB(int $offset = 0): ?string
	{
		return $this->b->getStackPreviousClassB($offset);
	}
	
	public function getStackPreviousClassC(int $offset = 0): ?string
	{
		return $this->b->getStackPreviousClassC($offset);
	}
	
	public function getStackPreviousClassesA(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousClasses($offset, $limit);
	}
	
	public function getStackPreviousClassesB(int $offset = 0, ?int $limit = null): array
	{
		return $this->b->getStackPreviousClassesB($offset, $limit);
	}
	
	public function getStackPreviousClassesC(int $offset = 0, ?int $limit = null): array
	{
		return $this->b->getStackPreviousClassesC($offset, $limit);
	}
	
	public function getStackPreviousObjectA(int $offset = 0): ?object
	{
		return UCall::stackPreviousObject($offset);
	}
	
	public function getStackPreviousObjectB(int $offset = 0): ?object
	{
		return $this->b->getStackPreviousObjectB($offset);
	}
	
	public function getStackPreviousObjectC(int $offset = 0): ?object
	{
		return $this->b->getStackPreviousObjectC($offset);
	}
	
	public function getStackPreviousObjectsA(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousObjects($offset, $limit);
	}
	
	public function getStackPreviousObjectsB(int $offset = 0, ?int $limit = null): array
	{
		return $this->b->getStackPreviousObjectsB($offset, $limit);
	}
	
	public function getStackPreviousObjectsC(int $offset = 0, ?int $limit = null): array
	{
		return $this->b->getStackPreviousObjectsC($offset, $limit);
	}
	
	public function getStackPreviousObjectClassA(int $offset = 0)
	{
		return UCall::stackPreviousObjectClass($offset);
	}
	
	public function getStackPreviousObjectClassB(int $offset = 0)
	{
		return $this->b->getStackPreviousObjectClassB($offset);
	}
	
	public function getStackPreviousObjectClassC(int $offset = 0)
	{
		return $this->b->getStackPreviousObjectClassC($offset);
	}
	
	public function getStackPreviousObjectsClassesA(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousObjectsClasses($offset, $limit);
	}
	
	public function getStackPreviousObjectsClassesB(int $offset = 0, ?int $limit = null): array
	{
		return $this->b->getStackPreviousObjectsClassesB($offset, $limit);
	}
	
	public function getStackPreviousObjectsClassesC(int $offset = 0, ?int $limit = null): array
	{
		return $this->b->getStackPreviousObjectsClassesC($offset, $limit);
	}
	
	public function getStackPreviousNameA(bool $full = false, bool $short = false, int $offset = 0): ?string
	{
		return UCall::stackPreviousName($full, $short, $offset);
	}
	
	public function getStackPreviousNameBA(bool $full = false, bool $short = false, int $offset = 0): ?string
	{
		return $this->b->getStackPreviousNameB($full, $short, $offset);
	}
	
	public function getStackPreviousNameCA(bool $full = false, bool $short = false, int $offset = 0): ?string
	{
		return $this->b->getStackPreviousNameCB($full, $short, $offset);
	}
	
	public function getStackPreviousNamesA(
		bool $full = false, bool $short = false, int $offset = 0, ?int $limit = null
	): array
	{
		return UCall::stackPreviousNames($full, $short, $offset, $limit);
	}
	
	public function getStackPreviousNamesBA(
		bool $full = false, bool $short = false, int $offset = 0, ?int $limit = null
	): array
	{
		return $this->b->getStackPreviousNamesB($full, $short, $offset, $limit);
	}
	
	public function getStackPreviousNamesCA(
		bool $full = false, bool $short = false, int $offset = 0, ?int $limit = null
	): array
	{
		return $this->b->getStackPreviousNamesCB($full, $short, $offset, $limit);
	}
	
	public static function getStaticStackPreviousClassA(int $offset = 0): ?string
	{
		return UCall::stackPreviousClass($offset);
	}
	
	public static function getStaticStackPreviousClassB(int $offset = 0): ?string
	{
		return CallTest_StackClassB::getStaticStackPreviousClassB($offset);
	}
	
	public static function getStaticStackPreviousClassC(int $offset = 0): ?string
	{
		return CallTest_StackClassB::getStaticStackPreviousClassC($offset);
	}
	
	public static function getStaticStackPreviousClassesA(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousClasses($offset, $limit);
	}
	
	public static function getStaticStackPreviousClassesB(int $offset = 0, ?int $limit = null): array
	{
		return CallTest_StackClassB::getStaticStackPreviousClassesB($offset, $limit);
	}
	
	public static function getStaticStackPreviousClassesC(int $offset = 0, ?int $limit = null): array
	{
		return CallTest_StackClassB::getStaticStackPreviousClassesC($offset, $limit);
	}
	
	public static function getStaticStackPreviousObjectA(int $offset = 0): ?object
	{
		return UCall::stackPreviousObject($offset);
	}
	
	public static function getStaticStackPreviousObjectB(int $offset = 0): ?object
	{
		return CallTest_StackClassB::getStaticStackPreviousObjectB($offset);
	}
	
	public static function getStaticStackPreviousObjectC(int $offset = 0): ?object
	{
		return CallTest_StackClassB::getStaticStackPreviousObjectC($offset);
	}
	
	public static function getStaticStackPreviousObjectsA(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousObjects($offset, $limit);
	}
	
	public static function getStaticStackPreviousObjectsB(int $offset = 0, ?int $limit = null): array
	{
		return CallTest_StackClassB::getStaticStackPreviousObjectsB($offset, $limit);
	}
	
	public static function getStaticStackPreviousObjectsC(int $offset = 0, ?int $limit = null): array
	{
		return CallTest_StackClassB::getStaticStackPreviousObjectsC($offset, $limit);
	}
	
	public static function getStaticStackPreviousObjectClassA(int $offset = 0)
	{
		return UCall::stackPreviousObjectClass($offset);
	}
	
	public static function getStaticStackPreviousObjectClassB(int $offset = 0)
	{
		return CallTest_StackClassB::getStaticStackPreviousObjectClassB($offset);
	}
	
	public static function getStaticStackPreviousObjectClassC(int $offset = 0)
	{
		return CallTest_StackClassB::getStaticStackPreviousObjectClassC($offset);
	}
	
	public static function getStaticStackPreviousObjectsClassesA(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousObjectsClasses($offset, $limit);
	}
	
	public static function getStaticStackPreviousObjectsClassesB(int $offset = 0, ?int $limit = null): array
	{
		return CallTest_StackClassB::getStaticStackPreviousObjectsClassesB($offset, $limit);
	}
	
	public static function getStaticStackPreviousObjectsClassesC(int $offset = 0, ?int $limit = null): array
	{
		return CallTest_StackClassB::getStaticStackPreviousObjectsClassesC($offset, $limit);
	}
	
	public static function getStaticStackPreviousNameA(
		bool $full = false, bool $short = false, int $offset = 0
	): ?string
	{
		return UCall::stackPreviousName($full, $short, $offset);
	}
	
	public static function getStaticStackPreviousNameBA(
		bool $full = false, bool $short = false, int $offset = 0
	): ?string
	{
		return CallTest_StackClassB::getStaticStackPreviousNameB($full, $short, $offset);
	}
	
	public static function getStaticStackPreviousNameCA(
		bool $full = false, bool $short = false, int $offset = 0
	): ?string
	{
		return CallTest_StackClassB::getStaticStackPreviousNameCB($full, $short, $offset);
	}
	
	public static function getStaticStackPreviousNamesA(
		bool $full = false, bool $short = false, int $offset = 0, ?int $limit = null
	): array
	{
		return UCall::stackPreviousNames($full, $short, $offset, $limit);
	}
	
	public static function getStaticStackPreviousNamesBA(
		bool $full = false, bool $short = false, int $offset = 0, ?int $limit = null
	): array
	{
		return CallTest_StackClassB::getStaticStackPreviousNamesB($full, $short, $offset, $limit);
	}
	
	public static function getStaticStackPreviousNamesCA(
		bool $full = false, bool $short = false, int $offset = 0, ?int $limit = null
	): array
	{
		return CallTest_StackClassB::getStaticStackPreviousNamesCB($full, $short, $offset, $limit);
	}
}



/** Test case dummy stack class B. */
class CallTest_StackClassB
{
	private CallTest_StackClassC $c;
	
	public function __construct(CallTest_StackClassC $c)
	{
		$this->c = $c;
	}
	
	public function getStackPreviousClassB(int $offset = 0): ?string
	{
		return UCall::stackPreviousClass($offset);
	}
	
	public function getStackPreviousClassC(int $offset = 0): ?string
	{
		return $this->c->getStackPreviousClassC($offset);
	}
	
	public function getStackPreviousClassesB(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousClasses($offset, $limit);
	}
	
	public function getStackPreviousClassesC(int $offset = 0, ?int $limit = null): array
	{
		return $this->c->getStackPreviousClassesC($offset, $limit);
	}
	
	public function getStackPreviousObjectB(int $offset = 0): ?object
	{
		return UCall::stackPreviousObject($offset);
	}
	
	public function getStackPreviousObjectC(int $offset = 0): ?object
	{
		return $this->c->getStackPreviousObjectC($offset);
	}
	
	public function getStackPreviousObjectsB(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousObjects($offset, $limit);
	}
	
	public function getStackPreviousObjectsC(int $offset = 0, ?int $limit = null): array
	{
		return $this->c->getStackPreviousObjectsC($offset, $limit);
	}
	
	public function getStackPreviousObjectClassB(int $offset = 0)
	{
		return UCall::stackPreviousObjectClass($offset);
	}
	
	public function getStackPreviousObjectClassC(int $offset = 0)
	{
		return $this->c->getStackPreviousObjectClassC($offset);
	}
	
	public function getStackPreviousObjectsClassesB(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousObjectsClasses($offset, $limit);
	}
	
	public function getStackPreviousObjectsClassesC(int $offset = 0, ?int $limit = null): array
	{
		return $this->c->getStackPreviousObjectsClassesC($offset, $limit);
	}
	
	public function getStackPreviousNameB(bool $full = false, bool $short = false, int $offset = 0): ?string
	{
		return UCall::stackPreviousName($full, $short, $offset);
	}
	
	public function getStackPreviousNameCB(bool $full = false, bool $short = false, int $offset = 0): ?string
	{
		return $this->c->getStackPreviousNameC($full, $short, $offset);
	}
	
	public function getStackPreviousNamesB(
		bool $full = false, bool $short = false, int $offset = 0, ?int $limit = null
	): array
	{
		return UCall::stackPreviousNames($full, $short, $offset, $limit);
	}
	
	public function getStackPreviousNamesCB(
		bool $full = false, bool $short = false, int $offset = 0, ?int $limit = null
	): array
	{
		return $this->c->getStackPreviousNamesC($full, $short, $offset, $limit);
	}
	
	public static function getStaticStackPreviousClassB(int $offset = 0): ?string
	{
		return UCall::stackPreviousClass($offset);
	}
	
	public static function getStaticStackPreviousClassC(int $offset = 0): ?string
	{
		return CallTest_StackClassC::getStaticStackPreviousClassC($offset);
	}
	
	public static function getStaticStackPreviousClassesB(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousClasses($offset, $limit);
	}
	
	public static function getStaticStackPreviousClassesC(int $offset = 0, ?int $limit = null): array
	{
		return CallTest_StackClassC::getStaticStackPreviousClassesC($offset, $limit);
	}
	
	public static function getStaticStackPreviousObjectB(int $offset = 0): ?object
	{
		return UCall::stackPreviousObject($offset);
	}
	
	public static function getStaticStackPreviousObjectC(int $offset = 0): ?object
	{
		return CallTest_StackClassC::getStaticStackPreviousObjectC($offset);
	}
	
	public static function getStaticStackPreviousObjectsB(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousObjects($offset, $limit);
	}
	
	public static function getStaticStackPreviousObjectsC(int $offset = 0, ?int $limit = null): array
	{
		return CallTest_StackClassC::getStaticStackPreviousObjectsC($offset, $limit);
	}
	
	public static function getStaticStackPreviousObjectClassB(int $offset = 0)
	{
		return UCall::stackPreviousObjectClass($offset);
	}
	
	public static function getStaticStackPreviousObjectClassC(int $offset = 0)
	{
		return CallTest_StackClassC::getStaticStackPreviousObjectClassC($offset);
	}
	
	public static function getStaticStackPreviousObjectsClassesB(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousObjectsClasses($offset, $limit);
	}
	
	public static function getStaticStackPreviousObjectsClassesC(int $offset = 0, ?int $limit = null): array
	{
		return CallTest_StackClassC::getStaticStackPreviousObjectsClassesC($offset, $limit);
	}
	
	public static function getStaticStackPreviousNameB(
		bool $full = false, bool $short = false, int $offset = 0
	): ?string
	{
		return UCall::stackPreviousName($full, $short, $offset);
	}
	
	public static function getStaticStackPreviousNameCB(
		bool $full = false, bool $short = false, int $offset = 0
	): ?string
	{
		return CallTest_StackClassC::getStaticStackPreviousNameC($full, $short, $offset);
	}
	
	public static function getStaticStackPreviousNamesB(
		bool $full = false, bool $short = false, int $offset = 0, ?int $limit = null
	): array
	{
		return UCall::stackPreviousNames($full, $short, $offset, $limit);
	}
	
	public static function getStaticStackPreviousNamesCB(
		bool $full = false, bool $short = false, int $offset = 0, ?int $limit = null
	): array
	{
		return CallTest_StackClassC::getStaticStackPreviousNamesC($full, $short, $offset, $limit);
	}
}



/** Test case dummy stack class C. */
class CallTest_StackClassC
{	
	public function getStackPreviousClassC(int $offset = 0): ?string
	{
		return UCall::stackPreviousClass($offset);
	}
	
	public function getStackPreviousClassesC(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousClasses($offset, $limit);
	}
	
	public function getStackPreviousObjectC(int $offset = 0): ?object
	{
		return UCall::stackPreviousObject($offset);
	}
	
	public function getStackPreviousObjectsC(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousObjects($offset, $limit);
	}
	
	public function getStackPreviousObjectClassC(int $offset = 0)
	{
		return UCall::stackPreviousObjectClass($offset);
	}
	
	public function getStackPreviousObjectsClassesC(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousObjectsClasses($offset, $limit);
	}
	
	public function getStackPreviousNameC(bool $full = false, bool $short = false, int $offset = 0): ?string
	{
		return UCall::stackPreviousName($full, $short, $offset);
	}
	
	public function getStackPreviousNamesC(
		bool $full = false, bool $short = false, int $offset = 0, ?int $limit = null
	): array
	{
		return UCall::stackPreviousNames($full, $short, $offset, $limit);
	}
	
	public static function getStaticStackPreviousClassC(int $offset = 0): ?string
	{
		return UCall::stackPreviousClass($offset);
	}
	
	public static function getStaticStackPreviousClassesC(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousClasses($offset, $limit);
	}
	
	public static function getStaticStackPreviousObjectC(int $offset = 0): ?object
	{
		return UCall::stackPreviousObject($offset);
	}
	
	public static function getStaticStackPreviousObjectsC(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousObjects($offset, $limit);
	}
	
	public static function getStaticStackPreviousObjectClassC(int $offset = 0)
	{
		return UCall::stackPreviousObjectClass($offset);
	}
	
	public static function getStaticStackPreviousObjectsClassesC(int $offset = 0, ?int $limit = null): array
	{
		return UCall::stackPreviousObjectsClasses($offset, $limit);
	}
	
	public static function getStaticStackPreviousNameC(
		bool $full = false, bool $short = false, int $offset = 0
	): ?string
	{
		return UCall::stackPreviousName($full, $short, $offset);
	}
	
	public static function getStaticStackPreviousNamesC(
		bool $full = false, bool $short = false, int $offset = 0, ?int $limit = null
	): array
	{
		return UCall::stackPreviousNames($full, $short, $offset, $limit);
	}
}



/** Test case dummy halt class. */
class CallTest_HaltClass
{
	public static function getBar(string $a, int $b) {}
}



/** Test case dummy guard class. */
class CallTest_GuardClass
{
	public static function getBar(string $a, int $b)
	{
		return '_bar_';
	}
}
