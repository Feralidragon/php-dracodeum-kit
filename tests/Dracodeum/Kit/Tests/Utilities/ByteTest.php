<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Utilities;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Utilities\Byte as UByte;
use Dracodeum\Kit\Utilities\Byte\Exceptions;

/** @see \Dracodeum\Kit\Utilities\Byte */
class ByteTest extends TestCase
{
	//Public methods
	/**
	 * Test <code>hvalue</code> method.
	 * 
	 * @testdox Byte::hvalue($value, $options) === '$expected'
	 * @dataProvider provideHvalueData
	 * 
	 * @param int $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param \Dracodeum\Kit\Utilities\Byte\Options\Hvalue|array|null $options
	 * <p>The method <var>$options</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 */
	public function testHvalue(int $value, $options, string $expected): void
	{
		$this->assertSame($expected, UByte::hvalue($value, $options));
	}
	
	/**
	 * Test <code>mvalue</code> method.
	 * 
	 * @testdox Byte::mvalue('$value') === $expected
	 * @dataProvider provideMvalueData
	 * 
	 * @param string $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param int $expected
	 * <p>The expected method return value.</p>
	 */
	public function testMvalue(string $value, int $expected): void
	{
		foreach ([false, true] as $no_throw) {
			$this->assertSame($expected, UByte::mvalue($value, $no_throw));
		}
	}
	
	/**
	 * Test <code>mvalue</code> method expecting an <code>InvalidValue</code> exception to be thrown.
	 * 
	 * @testdox Byte::mvalue('$value') --> InvalidValue exception
	 * @dataProvider provideMvalueData_Exception_InvalidValue
	 * 
	 * @param string $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 */
	public function testMvalue_Exception_InvalidValue(string $value): void
	{
		$this->expectException(Exceptions\Mvalue\InvalidValue::class);
		try {
			UByte::mvalue($value);
		} catch (Exceptions\Mvalue\InvalidValue $exception) {
			$this->assertSame($value, $exception->value);
			throw $exception;
		}
	}
	
	/**
	 * Test <code>mvalue</code> method with <var>$no_throw</var> set to boolean <code>true</code>, 
	 * expecting <code>null</code> to be returned.
	 * 
	 * @testdox Byte::mvalue('$value', true) === null
	 * @dataProvider provideMvalueData_Exception_InvalidValue
	 * 
	 * @param string $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 */
	public function testMvalue_NoThrow_Null(string $value): void
	{
		$this->assertNull(UByte::mvalue($value, true));
	}
	
	/**
	 * Test <code>evaluateSize</code> method.
	 * 
	 * @testdox Byte::evaluateSize(&{$value} --> &{$expected_value}) === true
	 * @dataProvider provideSizeCoercionData
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param int $expected_value
	 * <p>The expected value derived from the given <var>$value</var> parameter.</p>
	 */
	public function testEvaluateSize($value, int $expected_value): void
	{
		foreach ([false, true] as $nullable) {
			$v = $value;
			$this->assertTrue(UByte::evaluateSize($v, $nullable));
			$this->assertSame($expected_value, $v);
		}
	}
	
	/**
	 * Test <code>coerceSize</code> method.
	 * 
	 * @testdox Byte::coerceSize({$value}) === $expected
	 * @dataProvider provideSizeCoercionData
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param int|null $expected
	 * <p>The expected method return value.</p>
	 */
	public function testCoerceSize($value, ?int $expected): void
	{
		foreach ([false, true] as $nullable) {
			$this->assertSame($expected, UByte::coerceSize($value, $nullable));
		}
	}
	
	/**
	 * Test <code>processSizeCoercion</code> method.
	 * 
	 * @testdox Byte::processSizeCoercion(&{$value} --> &{$expected_value}) === true
	 * @dataProvider provideSizeCoercionData
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param int $expected_value
	 * <p>The expected value derived from the given <var>$value</var> parameter.</p>
	 */
	public function testProcessSizeCoercion($value, int $expected_value): void
	{
		foreach ([false, true] as $nullable) {
			foreach ([false, true] as $no_throw) {
				$v = $value;
				$this->assertTrue(UByte::processSizeCoercion($v, $nullable, $no_throw));
				$this->assertSame($expected_value, $v);
			}
		}
	}
	
	/**
	 * Test <code>evaluateSize</code> method with a <code>null</code> value.
	 * 
	 * @testdox Byte::evaluateSize(&{null} --> &{null}, true) === true
	 */
	public function testEvaluateSize_Null(): void
	{
		$value = null;
		$this->assertTrue(UByte::evaluateSize($value, true));
		$this->assertNull($value);
	}
	
	/**
	 * Test <code>coerceSize</code> method with a <code>null</code> value.
	 * 
	 * @testdox Byte::coerceSize({null}, true) === null
	 */
	public function testCoerceSize_Null(): void
	{
		$this->assertNull(UByte::coerceSize(null, true));
	}
	
	/**
	 * Test <code>processSizeCoercion</code> method with a <code>null</code> value.
	 * 
	 * @testdox Byte::processSizeCoercion(&{null} --> &{null}, true) === true
	 */
	public function testProcessSizeCoercion_Null(): void
	{
		foreach ([false, true] as $no_throw) {
			$value = null;
			$this->assertTrue(UByte::processSizeCoercion($value, true, $no_throw));
			$this->assertNull($value);
		}
	}
	
	/**
	 * Test <code>evaluateSize</code> method expecting boolean <code>false</code> to be returned.
	 * 
	 * @testdox Byte::evaluateSize(&{$value}) === false
	 * @dataProvider provideSizeCoercionData_Exception_SizeCoercionFailed
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 */
	public function testEvaluateSize_False($value): void
	{
		foreach ([false, true] as $nullable) {
			$v = $value;
			$this->assertFalse(UByte::evaluateSize($v, $nullable));
			$this->assertSame($value, $v);
		}
	}
	
	/**
	 * Test <code>coerceSize</code> method expecting a <code>SizeCoercionFailed</code> exception to be thrown.
	 * 
	 * @testdox Byte::coerceSize({$value}) --> SizeCoercionFailed exception
	 * @dataProvider provideSizeCoercionData_Exception_SizeCoercionFailed
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 */
	public function testCoerceSize_Exception_SizeCoercionFailed($value): void
	{
		$this->expectException(Exceptions\SizeCoercionFailed::class);
		try {
			UByte::coerceSize($value);
		} catch (Exceptions\SizeCoercionFailed $exception) {
			$this->assertSame($value, $exception->getValue());
			throw $exception;
		}
	}
	
	/**
	 * Test <code>processSizeCoercion</code> method expecting a <code>SizeCoercionFailed</code> exception to be thrown.
	 * 
	 * @testdox Byte::processSizeCoercion(&{$value}) --> SizeCoercionFailed exception
	 * @dataProvider provideSizeCoercionData_Exception_SizeCoercionFailed
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 */
	public function testProcessSizeCoercion_Exception_SizeCoercionFailed($value): void
	{
		$v = $value;
		$this->expectException(Exceptions\SizeCoercionFailed::class);
		try {
			UByte::processSizeCoercion($v);
		} catch (Exceptions\SizeCoercionFailed $exception) {
			$this->assertSame($value, $v);
			$this->assertSame($value, $exception->getValue());
			throw $exception;
		}
	}
	
	/**
	 * Test <code>processSizeCoercion</code> method with <var>$no_throw</var> set to boolean <code>true</code>, 
	 * expecting boolean <code>false</code> to be returned.
	 * 
	 * @testdox Byte::processSizeCoercion(&{$value}, false|true, true) === false
	 * @dataProvider provideSizeCoercionData_Exception_SizeCoercionFailed
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 */
	public function testProcessSizeCoercion_NoThrow_False($value): void
	{
		foreach ([false, true] as $nullable) {
			$v = $value;
			$this->assertFalse(UByte::processSizeCoercion($v, $nullable, true));
			$this->assertSame($value, $v);
		}
	}
	
	/**
	 * Test <code>evaluateSize</code> method with a <code>null</code> value, 
	 * expecting boolean <code>false</code> to be returned.
	 * 
	 * @testdox Byte::evaluateSize(&{null} --> &{null}) === false
	 */
	public function testEvaluateSize_Null_False(): void
	{
		$value = null;
		$this->assertFalse(UByte::evaluateSize($value));
		$this->assertNull($value);
	}
	
	/**
	 * Test <code>coerceSize</code> method with a <code>null</code> value, 
	 * expecting a <code>SizeCoercionFailed</code> exception to be thrown.
	 * 
	 * @testdox Byte::coerceSize({null}) --> SizeCoercionFailed exception
	 */
	public function testCoerceSize_Null_Exception_SizeCoercionFailed(): void
	{
		$this->expectException(Exceptions\SizeCoercionFailed::class);
		try {
			UByte::coerceSize(null);
		} catch (Exceptions\SizeCoercionFailed $exception) {
			$this->assertNull($exception->getValue());
			throw $exception;
		}
	}
	
	/**
	 * Test <code>processSizeCoercion</code> method with a <code>null</code> value, 
	 * expecting a <code>SizeCoercionFailed</code> exception to be thrown.
	 * 
	 * @testdox Byte::processSizeCoercion(&{null}) --> SizeCoercionFailed exception
	 */
	public function testProcessSizeCoercion_Null_Exception_SizeCoercionFailed(): void
	{
		$value = null;
		$this->expectException(Exceptions\SizeCoercionFailed::class);
		try {
			UByte::processSizeCoercion($value);
		} catch (Exceptions\SizeCoercionFailed $exception) {
			$this->assertNull($value);
			$this->assertNull($exception->getValue());
			throw $exception;
		}
	}
	
	/**
	 * Test <code>processSizeCoercion</code> method with a <code>null</code> value, 
	 * with <var>$no_throw</var> set to boolean <code>true</code>, expecting boolean <code>false</code> to be returned.
	 * 
	 * @testdox Byte::processSizeCoercion(&{null}, false, true) === false
	 */
	public function testProcessSizeCoercion_Null_NoThrow_False(): void
	{
		$value = null;
		$this->assertFalse(UByte::processSizeCoercion($value, false, true));
		$this->assertNull($value);
	}
	
	/**
	 * Test <code>evaluateMultiple</code> method.
	 * 
	 * @testdox Byte::evaluateMultiple(&{$value} --> &{$expected_value}) === true
	 * @dataProvider provideMultipleCoercionData
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param int $expected_value
	 * <p>The expected value derived from the given <var>$value</var> parameter.</p>
	 */
	public function testEvaluateMultiple($value, int $expected_value): void
	{
		foreach ([false, true] as $nullable) {
			$v = $value;
			$this->assertTrue(UByte::evaluateMultiple($v, $nullable));
			$this->assertSame($expected_value, $v);
		}
	}
	
	/**
	 * Test <code>coerceMultiple</code> method.
	 * 
	 * @testdox Byte::coerceMultiple({$value}) === $expected
	 * @dataProvider provideMultipleCoercionData
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param int|null $expected
	 * <p>The expected method return value.</p>
	 */
	public function testCoerceMultiple($value, ?int $expected): void
	{
		foreach ([false, true] as $nullable) {
			$this->assertSame($expected, UByte::coerceMultiple($value, $nullable));
		}
	}
	
	/**
	 * Test <code>processMultipleCoercion</code> method.
	 * 
	 * @testdox Byte::processMultipleCoercion(&{$value} --> &{$expected_value}) === true
	 * @dataProvider provideMultipleCoercionData
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param int $expected_value
	 * <p>The expected value derived from the given <var>$value</var> parameter.</p>
	 */
	public function testProcessMultipleCoercion($value, int $expected_value): void
	{
		foreach ([false, true] as $nullable) {
			foreach ([false, true] as $no_throw) {
				$v = $value;
				$this->assertTrue(UByte::processMultipleCoercion($v, $nullable, $no_throw));
				$this->assertSame($expected_value, $v);
			}
		}
	}
	
	/**
	 * Test <code>evaluateMultiple</code> method with a <code>null</code> value.
	 * 
	 * @testdox Byte::evaluateMultiple(&{null} --> &{null}, true) === true
	 */
	public function testEvaluateMultiple_Null(): void
	{
		$value = null;
		$this->assertTrue(UByte::evaluateMultiple($value, true));
		$this->assertNull($value);
	}
	
	/**
	 * Test <code>coerceMultiple</code> method with a <code>null</code> value.
	 * 
	 * @testdox Byte::coerceMultiple({null}, true) === null
	 */
	public function testCoerceMultiple_Null(): void
	{
		$this->assertNull(UByte::coerceMultiple(null, true));
	}
	
	/**
	 * Test <code>processMultipleCoercion</code> method with a <code>null</code> value.
	 * 
	 * @testdox Byte::processMultipleCoercion(&{null} --> &{null}, true) === true
	 */
	public function testProcessMultipleCoercion_Null(): void
	{
		foreach ([false, true] as $no_throw) {
			$value = null;
			$this->assertTrue(UByte::processMultipleCoercion($value, true, $no_throw));
			$this->assertNull($value);
		}
	}
	
	/**
	 * Test <code>evaluateMultiple</code> method expecting boolean <code>false</code> to be returned.
	 * 
	 * @testdox Byte::evaluateMultiple(&{$value}) === false
	 * @dataProvider provideMultipleCoercionData_Exception_MultipleCoercionFailed
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 */
	public function testEvaluateMultiple_False($value): void
	{
		foreach ([false, true] as $nullable) {
			$v = $value;
			$this->assertFalse(UByte::evaluateMultiple($v, $nullable));
			$this->assertSame($value, $v);
		}
	}
	
	/**
	 * Test <code>coerceMultiple</code> method expecting a <code>MultipleCoercionFailed</code> exception to be thrown.
	 * 
	 * @testdox Byte::coerceMultiple({$value}) --> MultipleCoercionFailed exception
	 * @dataProvider provideMultipleCoercionData_Exception_MultipleCoercionFailed
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 */
	public function testCoerceMultiple_Exception_MultipleCoercionFailed($value): void
	{
		$this->expectException(Exceptions\MultipleCoercionFailed::class);
		try {
			UByte::coerceMultiple($value);
		} catch (Exceptions\MultipleCoercionFailed $exception) {
			$this->assertSame($value, $exception->getValue());
			throw $exception;
		}
	}
	
	/**
	 * Test <code>processMultipleCoercion</code> method expecting a <code>MultipleCoercionFailed</code> exception to be 
	 * thrown.
	 * 
	 * @testdox Byte::processMultipleCoercion(&{$value}) --> MultipleCoercionFailed exception
	 * @dataProvider provideMultipleCoercionData_Exception_MultipleCoercionFailed
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 */
	public function testProcessMultipleCoercion_Exception_MultipleCoercionFailed($value): void
	{
		$v = $value;
		$this->expectException(Exceptions\MultipleCoercionFailed::class);
		try {
			UByte::processMultipleCoercion($v);
		} catch (Exceptions\MultipleCoercionFailed $exception) {
			$this->assertSame($value, $v);
			$this->assertSame($value, $exception->getValue());
			throw $exception;
		}
	}
	
	/**
	 * Test <code>processMultipleCoercion</code> method with <var>$no_throw</var> set to boolean <code>true</code>, 
	 * expecting boolean <code>false</code> to be returned.
	 * 
	 * @testdox Byte::processMultipleCoercion(&{$value}, false|true, true) === false
	 * @dataProvider provideMultipleCoercionData_Exception_MultipleCoercionFailed
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 */
	public function testProcessMultipleCoercion_NoThrow_False($value): void
	{
		foreach ([false, true] as $nullable) {
			$v = $value;
			$this->assertFalse(UByte::processMultipleCoercion($v, $nullable, true));
			$this->assertSame($value, $v);
		}
	}
	
	/**
	 * Test <code>evaluateMultiple</code> method with a <code>null</code> value, 
	 * expecting boolean <code>false</code> to be returned.
	 * 
	 * @testdox Byte::evaluateMultiple(&{null} --> &{null}) === false
	 */
	public function testEvaluateMultiple_Null_False(): void
	{
		$value = null;
		$this->assertFalse(UByte::evaluateMultiple($value));
		$this->assertNull($value);
	}
	
	/**
	 * Test <code>coerceMultiple</code> method with a <code>null</code> value, 
	 * expecting a <code>MultipleCoercionFailed</code> exception to be thrown.
	 * 
	 * @testdox Byte::coerceMultiple({null}) --> MultipleCoercionFailed exception
	 */
	public function testCoerceMultiple_Null_Exception_MultipleCoercionFailed(): void
	{
		$this->expectException(Exceptions\MultipleCoercionFailed::class);
		try {
			UByte::coerceMultiple(null);
		} catch (Exceptions\MultipleCoercionFailed $exception) {
			$this->assertNull($exception->getValue());
			throw $exception;
		}
	}
	
	/**
	 * Test <code>processMultipleCoercion</code> method with a <code>null</code> value, 
	 * expecting a <code>MultipleCoercionFailed</code> exception to be thrown.
	 * 
	 * @testdox Byte::processMultipleCoercion(&{null}) --> MultipleCoercionFailed exception
	 */
	public function testProcessMultipleCoercion_Null_Exception_MultipleCoercionFailed(): void
	{
		$value = null;
		$this->expectException(Exceptions\MultipleCoercionFailed::class);
		try {
			UByte::processMultipleCoercion($value);
		} catch (Exceptions\MultipleCoercionFailed $exception) {
			$this->assertNull($value);
			$this->assertNull($exception->getValue());
			throw $exception;
		}
	}
	
	/**
	 * Test <code>processMultipleCoercion</code> method with a <code>null</code> value, 
	 * with <var>$no_throw</var> set to boolean <code>true</code>, expecting boolean <code>false</code> to be returned.
	 * 
	 * @testdox Byte::processMultipleCoercion(&{null}, false, true) === false
	 */
	public function testProcessMultipleCoercion_Null_NoThrow_False(): void
	{
		$value = null;
		$this->assertFalse(UByte::processMultipleCoercion($value, false, true));
		$this->assertNull($value);
	}
	
	/**
	 * Test flag methods.
	 * 
	 * @testdox Flags
	 */
	public function testFlags(): void
	{
		//initialize
		$value = 0x0;
		$flag1 = 0x1;
		$flag2 = 0x2;
		$flag3 = 0x4;
		
		//assert
		$this->assertFalse(UByte::hasFlag($value, $flag1));
		$this->assertFalse(UByte::hasFlag($value, $flag2));
		$this->assertFalse(UByte::hasFlag($value, $flag3));
		
		//set (1)
		UByte::setFlag($value, $flag1);
		$this->assertTrue(UByte::hasFlag($value, $flag1));
		$this->assertFalse(UByte::hasFlag($value, $flag2));
		$this->assertFalse(UByte::hasFlag($value, $flag3));
		
		//set (2)
		UByte::setFlag($value, $flag3);
		$this->assertTrue(UByte::hasFlag($value, $flag1));
		$this->assertFalse(UByte::hasFlag($value, $flag2));
		$this->assertTrue(UByte::hasFlag($value, $flag3));
		
		//set (3)
		UByte::setFlag($value, $flag2);
		$this->assertTrue(UByte::hasFlag($value, $flag1));
		$this->assertTrue(UByte::hasFlag($value, $flag2));
		$this->assertTrue(UByte::hasFlag($value, $flag3));
		
		//unset (1)
		UByte::unsetFlag($value, $flag1);
		$this->assertFalse(UByte::hasFlag($value, $flag1));
		$this->assertTrue(UByte::hasFlag($value, $flag2));
		$this->assertTrue(UByte::hasFlag($value, $flag3));
		
		//unset (2)
		UByte::unsetFlag($value, $flag3);
		$this->assertFalse(UByte::hasFlag($value, $flag1));
		$this->assertTrue(UByte::hasFlag($value, $flag2));
		$this->assertFalse(UByte::hasFlag($value, $flag3));
		
		//unset (3)
		UByte::unsetFlag($value, $flag2);
		$this->assertFalse(UByte::hasFlag($value, $flag1));
		$this->assertFalse(UByte::hasFlag($value, $flag2));
		$this->assertFalse(UByte::hasFlag($value, $flag3));
		
		//update (1)
		UByte::updateFlag($value, $flag1, true);
		$this->assertTrue(UByte::hasFlag($value, $flag1));
		$this->assertFalse(UByte::hasFlag($value, $flag2));
		$this->assertFalse(UByte::hasFlag($value, $flag3));
		
		//update (2)
		UByte::updateFlag($value, $flag3, true);
		$this->assertTrue(UByte::hasFlag($value, $flag1));
		$this->assertFalse(UByte::hasFlag($value, $flag2));
		$this->assertTrue(UByte::hasFlag($value, $flag3));
		
		//update (3)
		UByte::updateFlag($value, $flag1, false);
		$this->assertFalse(UByte::hasFlag($value, $flag1));
		$this->assertFalse(UByte::hasFlag($value, $flag2));
		$this->assertTrue(UByte::hasFlag($value, $flag3));
		
		//update (4)
		UByte::updateFlag($value, $flag3, false);
		$this->assertFalse(UByte::hasFlag($value, $flag1));
		$this->assertFalse(UByte::hasFlag($value, $flag2));
		$this->assertFalse(UByte::hasFlag($value, $flag3));
	}
	
	
	
	//Public static methods
	/**
	 * Provide <code>hvalue</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>hvalue</code> method data.</p>
	 */
	public static function provideHvalueData(): array
	{
		return [
			[0, null, '0 B'],
			[0, ['long' => true], '0 bytes'],
			[1, null, '1 B'],
			[1, ['long' => true], '1 byte'],
			[5, null, '5 B'],
			[5, ['long' => true], '5 bytes'],
			[1000, null, '1 kB'],
			[1000, ['long' => true], '1 kilobyte'],
			[5000, null, '5 kB'],
			[5000, ['long' => true], '5 kilobytes'],
			[1000000, null, '1 MB'],
			[1000000, ['long' => true], '1 megabyte'],
			[5000000, null, '5 MB'],
			[5000000, ['long' => true], '5 megabytes'],
			[1000000000, null, '1 GB'],
			[1000000000, ['long' => true], '1 gigabyte'],
			[5000000000, null, '5 GB'],
			[5000000000, ['long' => true], '5 gigabytes'],
			[1000000000000, null, '1 TB'],
			[1000000000000, ['long' => true], '1 terabyte'],
			[5000000000000, null, '5 TB'],
			[5000000000000, ['long' => true], '5 terabytes'],
			[1000000000000000, null, '1 PB'],
			[1000000000000000, ['long' => true], '1 petabyte'],
			[5000000000000000, null, '5 PB'],
			[5000000000000000, ['long' => true], '5 petabytes'],
			[1000000000000000000, null, '1 EB'],
			[1000000000000000000, ['long' => true], '1 exabyte'],
			[5000000000000000000, null, '5 EB'],
			[5000000000000000000, ['long' => true], '5 exabytes'],
			[39714, null, '39.71 kB'],
			[39714, ['long' => true], '39.71 kilobytes'],
			[39714, ['precision' => 3], '39.714 kB'],
			[39714, ['long' => true, 'precision' => 3], '39.714 kilobytes'],
			[39714, ['precision' => 1], '39.7 kB'],
			[39714, ['long' => true, 'precision' => 1], '39.7 kilobytes'],
			[39714, ['precision' => 0], '40 kB'],
			[39714, ['long' => true, 'precision' => 0], '40 kilobytes'],
			[39714, ['min_multiple' => 'MB'], '0.04 MB'],
			[39714, ['long' => true, 'min_multiple' => 'MB'], '0.04 megabytes'],
			[39714, ['precision' => 3, 'min_multiple' => 'MB'], '0.04 MB'],
			[39714, ['long' => true, 'precision' => 3, 'min_multiple' => 'MB'], '0.04 megabytes'],
			[39714, ['precision' => 5, 'min_multiple' => 'MB'], '0.03971 MB'],
			[39714, ['long' => true, 'precision' => 5, 'min_multiple' => 'MB'], '0.03971 megabytes'],
			[39714, ['max_multiple' => 'B'], '39714 B'],
			[39714, ['long' => true, 'max_multiple' => 'B'], '39714 bytes'],
			[-39714, null, '-39.71 kB'],
			[-39714, ['long' => true], '-39.71 kilobytes'],
			[-39714, ['precision' => 0], '-40 kB'],
			[-39714, ['long' => true, 'precision' => 0], '-40 kilobytes']
		];
	}
	
	/**
	 * Provide <code>mvalue</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>mvalue</code> method data.</p>
	 */
	public static function provideMvalueData(): array
	{
		return [
			['0', 0],
			['0 B', 0],
			['1', 1],
			['1B', 1],
			['1 B', 1],
			['1 byte', 1],
			['5', 5],
			['5B', 5],
			['5 B', 5],
			['5 bytes', 5],
			['1k', 1000],
			['1 kB', 1000],
			['1 kilobyte', 1000],
			['5k', 5000],
			['5 kB', 5000],
			['5 kilobytes', 5000],
			['1M', 1000000],
			['1 MB', 1000000],
			['1 megabyte', 1000000],
			['5M', 5000000],
			['5 MB', 5000000],
			['5 megabytes', 5000000],
			['1G', 1000000000],
			['1 GB', 1000000000],
			['1 gigabyte', 1000000000],
			['5G', 5000000000],
			['5 GB', 5000000000],
			['5 gigabytes', 5000000000],
			['1T', 1000000000000],
			['1 TB', 1000000000000],
			['1 terabyte', 1000000000000],
			['5T', 5000000000000],
			['5 TB', 5000000000000],
			['5 terabytes', 5000000000000],
			['1P', 1000000000000000],
			['1 PB', 1000000000000000],
			['1 petabyte', 1000000000000000],
			['5P', 5000000000000000],
			['5 PB', 5000000000000000],
			['5 petabytes', 5000000000000000],
			['1E', 1000000000000000000],
			['1 EB', 1000000000000000000],
			['1 exabyte', 1000000000000000000],
			['5E', 5000000000000000000],
			['5 EB', 5000000000000000000],
			['5 exabytes', 5000000000000000000],
			['39.7 kB', 39700],
			['39.7 kilobytes', 39700],
			['39.71 MB', 39710000],
			['39.71 megabytes', 39710000],
			['39.714 GB', 39714000000],
			['39.714 gigabytes', 39714000000],
			['0.039714 MB', 39714],
			['0.039714 megabytes', 39714],
			['+39.714 kB', 39714],
			['+39.714 kilobytes', 39714],
			['-39.714 kB', -39714],
			['-39.714 kilobytes', -39714],
			['-39,714 kB', -39714],
			['-39,714 kilobytes', -39714]
		];
	}
	
	/**
	 * Provide <code>mvalue</code> method data for an <code>InvalidValue</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The provided <code>mvalue</code> method data for an <code>InvalidValue</code> exception to be thrown.</p>
	 */
	public static function provideMvalueData_Exception_InvalidValue(): array
	{
		return [
			[''],
			['.'],
			['3.1'],
			['abc'],
			['1m'],
			['1 mB'],
			['5 foobytes'],
			['--5 bytes'],
			['5_bytes'],
			['bytes 5'],
			['5.5 bytes'],
			['123.4567 kB']
		];
	}
	
	/**
	 * Provide size coercion method data.
	 * 
	 * @return array
	 * <p>The provided size coercion method data.</p>
	 */
	public static function provideSizeCoercionData(): array
	{
		return [
			[0, 0],
			[123000 , 123000],
			[-123000 , -123000],
			[0.0 , 0],
			[123000.0 , 123000],
			[-123000.0 , -123000],
			['0' , 0],
			['123000', 123000],
			['-123000', -123000],
			['123e3' , 123000],
			['123E3' , 123000],
			['-123e3' , -123000],
			['0360170', 123000],
			['0x1e078', 123000],
			['0x1E078', 123000],
			['123k', 123000],
			['123 thousand', 123000],
			['-123k', -123000],
			['-123 thousand', -123000],
			['123 M', 123000000],
			['123 million', 123000000],
			['123 B', 123000000000],
			['123 G', 123000000000],
			['123 billion', 123000000000],
			['123kB', 123000],
			['123 kilobytes', 123000],
			['-123kB', -123000],
			['-123 kilobytes', -123000],
			['123 MB', 123000000],
			['123 megabytes', 123000000],
			['123 GB', 123000000000],
			['123 gigabytes', 123000000000]
		];
	}
	
	/**
	 * Provide size coercion method data for a <code>SizeCoercionFailed</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The provided size coercion method data for a <code>SizeCoercionFailed</code> exception to be thrown.</p>
	 */
	public static function provideSizeCoercionData_Exception_SizeCoercionFailed(): array
	{
		return [
			[false],
			[true],
			[0.123],
			[''],
			['.'],
			['3.1'],
			['abc'],
			['1m'],
			['1 mB'],
			['5 foobytes'],
			['--5 bytes'],
			['5_bytes'],
			['bytes 5'],
			['5.5 bytes'],
			['123.4567 kB'],
			[[]],
			[new \stdClass],
			[fopen(__FILE__, 'r')]
		];
	}
	
	/**
	 * Provide multiple coercion method data.
	 * 
	 * @return array
	 * <p>The provided multiple coercion method data.</p>
	 */
	public static function provideMultipleCoercionData(): array
	{
		return [
			[1, 1],
			['1', 1],
			['', 1],
			['B', 1],
			['byte', 1],
			['bytes', 1],
			[1000, 1000],
			['1000', 1000],
			['k', 1000],
			['kB', 1000],
			['kilobyte', 1000],
			['kilobytes', 1000],
			[1000000, 1000000],
			['1000000', 1000000],
			['M', 1000000],
			['MB', 1000000],
			['megabyte', 1000000],
			['megabytes', 1000000],
			[1000000000, 1000000000],
			['1000000000', 1000000000],
			['G', 1000000000],
			['GB', 1000000000],
			['gigabyte', 1000000000],
			['gigabytes', 1000000000],
			[1000000000000, 1000000000000],
			['1000000000000', 1000000000000],
			['T', 1000000000000],
			['TB', 1000000000000],
			['terabyte', 1000000000000],
			['terabytes', 1000000000000],
			[1000000000000000, 1000000000000000],
			['1000000000000000', 1000000000000000],
			['P', 1000000000000000],
			['PB', 1000000000000000],
			['petabyte', 1000000000000000],
			['petabytes', 1000000000000000],
			[1000000000000000000, 1000000000000000000],
			['1000000000000000000', 1000000000000000000],
			['E', 1000000000000000000],
			['EB', 1000000000000000000],
			['exabyte', 1000000000000000000],
			['exabytes', 1000000000000000000]
		];
	}
	
	/**
	 * Provide multiple coercion method data for a <code>MultipleCoercionFailed</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The provided multiple coercion method data for a <code>MultipleCoercionFailed</code> exception to be 
	 * thrown.</p>
	 */
	public static function provideMultipleCoercionData_Exception_MultipleCoercionFailed(): array
	{
		return [
			[false],
			[true],
			[0],
			[100],
			[-1000],
			[0.123],
			[1000.1],
			[' '],
			['.'],
			['K'],
			['Bk'],
			['kb'],
			['bit'],
			['0001'],
			['0x0001'],
			['foobyte'],
			['Kilobyte'],
			[[]],
			[new \stdClass],
			[fopen(__FILE__, 'r')]
		];
	}
}
