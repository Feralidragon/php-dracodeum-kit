<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Traits;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Traits;
use Dracodeum\Kit\Attributes\Property\{
	Coercive,
	Strict
};
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;
use Dracodeum\Kit\Managers\PropertiesV2\Property;
use Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Inaccessible as InaccessibleException;
use Dracodeum\Kit\Prototypes\Type;
use Dracodeum\Kit\Primitives\Error;

/** @covers \Dracodeum\Kit\Traits\PropertiesV2 */
class PropertiesTest extends TestCase
{
	//Public methods
	/**
	 * Test public.
	 * 
	 * @testdox Public
	 */
	public function testPublic(): void
	{
		//initialize
		$object = new PropertiesTest_Class1('unreal', p2: 250);
		
		//assert (manager)
		$this->assertInstanceOf(Manager::class, $object->getManager());
		$this->assertInstanceOf(Manager::class, $object->getPManager());
		$this->assertSame($object->getManager(), $object->getPManager());
		
		//assert (p0)
		$this->assertFalse(isset($object->p0));
		
		//assert (p1)
		$this->assertTrue(isset($object->p1));
		$this->assertSame('unreal', $object->p1);
		$object->p1 = '__A__';
		$this->assertTrue(isset($object->p1));
		$this->assertSame('__A__', $object->p1);
		
		//assert (p2)
		$this->assertTrue(isset($object->p2));
		$this->assertSame(250, $object->p2);
		$object->p2 = '1e3';
		$this->assertTrue(isset($object->p2));
		$this->assertSame(1000, $object->p2);
		unset($object->p2);
		$this->assertTrue(isset($object->p2));
		$this->assertSame(100, $object->p2);
		
		//assert (p3)
		$this->assertFalse(isset($object->p3));
		$this->assertNull($object->p3);
		$object->p3 = 15;
		$this->assertTrue(isset($object->p3));
		$this->assertSame(16, $object->p3);
		$object->p3 = '15';
		$this->assertTrue(isset($object->p3));
		$this->assertSame('15_', $object->p3);
		unset($object->p3);
		$this->assertFalse(isset($object->p3));
		$this->assertNull($object->p3);
	}
	
	/**
	 * Test protected.
	 * 
	 * @testdox Protected
	 */
	public function testProtected(): void
	{
		//initialize
		$object = new PropertiesTest_Class2('unreal', p2: 250);
		
		//assert (manager)
		$this->assertInstanceOf(Manager::class, $object->getManager());
		$this->assertInstanceOf(Manager::class, $object->getPManager());
		$this->assertSame($object->getManager(), $object->getPManager());
		
		//assert (p4)
		$this->assertFalse(isset($object->p4));
		$this->assertFalse($object->issetP4());
		$this->assertNull($object->getP4());
		$object->setP4('__B__');
		$this->assertFalse(isset($object->p4));
		$this->assertTrue($object->issetP4());
		$this->assertSame('__B__', $object->getP4());
		$object->unsetP4();
		$this->assertFalse(isset($object->p4));
		$this->assertFalse($object->issetP4());
		$this->assertNull($object->getP4());
	}
	
	/**
	 * Test protected `get` expecting an `Inaccessible` exception to be thrown.
	 * 
	 * @testdox Protected get Inaccessible exception
	 */
	public function testProtected_Get_InaccessibleException(): void
	{
		//initialize
		$object = new PropertiesTest_Class2('unreal', p2: 250);
		
		//exception
		$this->expectException(InaccessibleException::class);
		try {
			$object->p4;
		} catch (InaccessibleException $exception) {
			$this->assertSame(['p4'], $exception->names);
			$this->assertSame(self::class, $exception->scope_class);
			throw $exception;
		}
	}
	
	/**
	 * Test protected `set` expecting an `Inaccessible` exception to be thrown.
	 * 
	 * @testdox Protected set Inaccessible exception
	 */
	public function testProtected_Set_InaccessibleException(): void
	{
		//initialize
		$object = new PropertiesTest_Class2('unreal', p2: 250);
		
		//exception
		$this->expectException(InaccessibleException::class);
		try {
			$object->p4 = '__B__';
		} catch (InaccessibleException $exception) {
			$this->assertSame(['p4'], $exception->names);
			$this->assertSame(self::class, $exception->scope_class);
			throw $exception;
		}
	}
	
	/**
	 * Test protected `unset` expecting an `Inaccessible` exception to be thrown.
	 * 
	 * @testdox Protected unset Inaccessible exception
	 */
	public function testProtected_Unset_InaccessibleException(): void
	{
		//initialize
		$object = new PropertiesTest_Class2('unreal', p2: 250);
		
		//exception
		$this->expectException(InaccessibleException::class);
		try {
			unset($object->p4);
		} catch (InaccessibleException $exception) {
			$this->assertSame(['p4'], $exception->names);
			$this->assertSame(self::class, $exception->scope_class);
			throw $exception;
		}
	}
	
	/**
	 * Test `properties` method.
	 * 
	 * @testdox self::properties()
	 * @dataProvider providePropertiesData
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string[] $expected_names
	 * The expected returning property names.
	 */
	public function testProperties(string $class, array $expected_names): void
	{
		//initialize
		$properties = $class::getProperties();
		
		//assert
		$this->assertSame($expected_names, array_keys($properties));
		foreach ($properties as $property) {
			$this->assertInstanceOf(Property::class, $property);
		}
	}
	
	
	
	//Public static methods
	/**
	 * Provide `properties` method data.
	 * 
	 * @return array
	 * The data.
	 */
	public static function providePropertiesData(): array
	{
		return [
			[PropertiesTest_Class1::class, ['p1', 'p2', 'p3', 'p4']],
			[PropertiesTest_Class2::class, ['p1', 'p2', 'p3', 'p4', 'p5']]
		];
	}
}



/** Test case dummy class 0. */
class PropertiesTest_Class0
{
	public string $p0;
}



/** Test case dummy class 1. */
class PropertiesTest_Class1 extends PropertiesTest_Class0
{
	use Traits\PropertiesV2;
	
	#[Strict]
	public string $p1;
	
	#[Coercive]
	public int $p2 = 100;
	
	#[Coercive(PropertiesTest_Type::class)]
	public $p3;
	
	protected $p4;
	
	private $manager;
	
	public function __construct(...$values)
	{
		$this->manager = $this->initializePropertiesManager($values);
	}
	
	public function getManager()
	{
		return $this->manager;
	}
	
	public function getPManager()
	{
		return $this->getPropertiesManager();
	}
	
	public static function getProperties()
	{
		return static::properties();
	}
}



/** Test case dummy class 2. */
class PropertiesTest_Class2 extends PropertiesTest_Class1
{
	public string $p5 = '';
	
	public function issetP4(): bool
	{
		return isset($this->p4);
	}
	
	public function getP4(): mixed
	{
		return $this->p4;
	}
	
	public function setP4(mixed $value): void
	{
		$this->p4 = $value;
	}
	
	public function unsetP4(): void
	{
		unset($this->p4);
	}
}



/** Test case type class. */
class PropertiesTest_Type extends Type
{
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		if (is_int($value)) {
			$value++;
		} elseif (is_string($value)) {
			$value .= '_';
		}
		return null;
	}
}
