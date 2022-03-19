<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Managers\Properties;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Managers\PropertiesV2\Meta;
use Dracodeum\Kit\Managers\PropertiesV2\Meta\Entry;
use Dracodeum\Kit\Managers\PropertiesV2\Meta\Exceptions\{
	Defined as DefinedException,
	Undefined as UndefinedException,
	InvalidDefault as InvalidDefaultException
};
use Dracodeum\Kit\Components\Type;
use Dracodeum\Kit\Prototypes\Type as TypePrototype;
use Dracodeum\Kit\Primitives\Error;

/** @see \Dracodeum\Kit\Managers\PropertiesV2\Meta */
class MetaTest extends TestCase
{
	//Public methods
	/**
	 * Test class.
	 * 
	 * @testdox Class
	 * 
	 * @return void
	 */
	public function testClass(): void
	{
		$class = MetaTest_Class1::class;
		$meta = new Meta($class);
		$this->assertSame($class, $meta->getClass());
	}
	
	/**
	 * Test entry.
	 * 
	 * @testdox Entry
	 * 
	 * @return void
	 */
	public function testEntry(): void
	{
		//initialize
		$meta = new Meta(MetaTest_Class1::class);
		$type1 = Type::build(MetaTest_Type1::class);
		$type2 = Type::build(MetaTest_Type2::class);
		$name1 = 'foo';
		$name2 = 'bar';
		
		//assert (has [1])
		$this->assertFalse($meta->has($name1));
		$this->assertFalse($meta->has($name2));
		
		//assert (set [1])
		$this->assertSame($meta, $meta->set($name1, $type1, '123'));
		
		//assert (has [2])
		$this->assertTrue($meta->has($name1));
		$this->assertFalse($meta->has($name2));
		
		//assert (set [2])
		$this->assertSame($meta, $meta->set($name2, $type2, 'abc'));
		
		//assert (has [3])
		$this->assertTrue($meta->has($name1));
		$this->assertTrue($meta->has($name2));
		
		//assert (entries)
		$entry1 = $meta->get($name1);
		$entry2 = $meta->get($name2);
		$this->assertInstanceOf(Entry::class, $entry1);
		$this->assertInstanceOf(Entry::class, $entry2);
		
		//assert (types)
		$this->assertSame($type1, $entry1->type);
		$this->assertSame($type2, $entry2->type);
		
		//assert (defaults)
		$this->assertSame(123, $entry1->default);
		$this->assertSame('abc_', $entry2->default);
		
		//assert (process [1.a])
		$value1 = '456';
		$error1 = $meta->process($name1, $value1);
		$this->assertNull($error1);
		$this->assertSame(456, $value1);
		
		//assert (process [1.b])
		$value1 = 'def';
		$error1 = $meta->process($name1, $value1);
		$this->assertNull($error1);
		$this->assertSame('def', $value1);
		
		//assert (process [1.c])
		$value1 = 123.456;
		$error1 = $meta->process($name1, $value1);
		$this->assertInstanceOf(Error::class, $error1);
		$this->assertSame(123.456, $value1);
		
		//assert (process [2.a])
		$value2 = 'def';
		$error2 = $meta->process($name2, $value2);
		$this->assertNull($error2);
		$this->assertSame('def_', $value2);
		
		//assert (process [2.b])
		$value2 = 123;
		$error2 = $meta->process($name2, $value2);
		$this->assertInstanceOf(Error::class, $error2);
		$this->assertSame(123, $value2);
	}
	
	/**
	 * Test `get` method expecting an `Undefined` exception to be thrown.
	 * 
	 * @testdox Get Undefined exception
	 * 
	 * @return void
	 */
	public function testGet_UndefinedException(): void
	{
		//initialize
		$class = MetaTest_Class1::class;
		$meta = new Meta($class);
		$name = 'foo';
		
		//exception
		$this->expectException(UndefinedException::class);
		try {
			$meta->get($name);
		} catch (UndefinedException $exception) {
			$this->assertSame($class, $exception->class);
			$this->assertSame($name, $exception->name);
			throw $exception;
		}
	}
	
	/**
	 * Test `set` method expecting a `Defined` exception to be thrown.
	 * 
	 * @testdox Set Defined exception
	 * 
	 * @return void
	 */
	public function testSet_DefinedException(): void
	{
		//initialize
		$class = MetaTest_Class1::class;
		$meta = new Meta($class);
		$type = Type::build(MetaTest_Type1::class);
		$name = 'foo';
		$meta->set($name, $type, 123);
		
		//exception
		$this->expectException(DefinedException::class);
		try {
			$meta->set($name, $type, 123);
		} catch (DefinedException $exception) {
			$this->assertSame($class, $exception->class);
			$this->assertSame($name, $exception->name);
			throw $exception;
		}
	}
	
	/**
	 * Test `set` method expecting an `InvalidDefault` exception to be thrown.
	 * 
	 * @testdox Set InvalidDefault exception
	 * 
	 * @return void
	 */
	public function testSet_InvalidDefaultException(): void
	{
		//initialize
		$class = MetaTest_Class1::class;
		$meta = new Meta($class);
		$type = Type::build(MetaTest_Type1::class);
		$name = 'foo';
		$default = 123.456;
		
		//exception
		$this->expectException(InvalidDefaultException::class);
		try {
			$meta->set($name, $type, $default);
		} catch (InvalidDefaultException $exception) {
			$this->assertSame($class, $exception->class);
			$this->assertSame($name, $exception->name);
			$this->assertSame($default, $exception->value);
			$this->assertInstanceOf(Error::class, $exception->error);
			throw $exception;
		}
	}
	
	/**
	 * Test `process` method expecting an `Undefined` exception to be thrown.
	 * 
	 * @testdox Process Undefined exception
	 * 
	 * @return void
	 */
	public function testProcess_UndefinedException(): void
	{
		//initialize
		$class = MetaTest_Class1::class;
		$meta = new Meta($class);
		$name = 'foo';
		$value = $v = 123;
		
		//exception
		$this->expectException(UndefinedException::class);
		try {
			$meta->process($name, $value);
		} catch (UndefinedException $exception) {
			$this->assertSame($class, $exception->class);
			$this->assertSame($name, $exception->name);
			$this->assertSame($v, $value);
			throw $exception;
		}
	}
	
	/**
	 * Test clone.
	 * 
	 * @testdox Clone
	 * 
	 * @return void
	 */
	public function testClone(): void
	{
		//initialize
		$class1 = MetaTest_Class1::class;
		$class2 = MetaTest_Class2::class;
		$meta1 = new Meta($class1);
		$name1 = 'foo';
		$name2 = 'bar';
		$name3 = 'def';
		$meta1->set($name1, Type::build(MetaTest_Type1::class), '123');
		$meta1->set($name2, Type::build(MetaTest_Type2::class), 'abc');
		$meta2 = $meta1->clone($class2);
		
		//assert
		$this->assertNotSame($meta1, $meta2);
		$this->assertSame($class1, $meta1->getClass());
		$this->assertSame($class2, $meta2->getClass());
		$this->assertTrue($meta1->has($name1));
		$this->assertTrue($meta1->has($name2));
		$this->assertFalse($meta1->has($name3));
		$this->assertSame($meta1->has($name1), $meta2->has($name1));
		$this->assertSame($meta1->has($name2), $meta2->has($name2));
		$this->assertSame($meta1->has($name3), $meta2->has($name3));
		$this->assertSame($meta1->get($name1), $meta2->get($name1));
		$this->assertSame($meta1->get($name2), $meta2->get($name2));
	}
}



/** Test case dummy class 1. */
class MetaTest_Class1 {}



/** Test case dummy class 2. */
class MetaTest_Class2 {}



/** Test case type class 1. */
class MetaTest_Type1 extends TypePrototype
{
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		if (!is_string($value) && !is_int($value)) {
			return Error::build();
		} elseif (is_string($value) && is_numeric($value)) {
			$value = (int)$value;
		}
		return null;
	}
}



/** Test case type class 2. */
class MetaTest_Type2 extends TypePrototype
{
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		if (!is_string($value)) {
			return Error::build();
		}
		$value .= '_';
		return null;
	}
}
