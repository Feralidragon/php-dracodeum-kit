<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Traits;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Traits;
use Dracodeum\Kit\Attributes\Property\{
	coercive,
	strict
};
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;
use Dracodeum\Kit\Prototypes\Type;
use Dracodeum\Kit\Primitives\Error;

/** @see \Dracodeum\Kit\Traits\PropertiesV2 */
class PropertiesTest extends TestCase
{
	//Public methods
	/**
	 * Test trait.
	 * 
	 * @return void
	 */
	public function testTrait(): void
	{
		//initialize
		$object = new PropertiesTest_Class('unreal', p2: 250);
		
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
}



/** Test case dummy class. */
class PropertiesTest_Class
{
	use Traits\PropertiesV2;
	
	#[strict]
	public string $p1;
	
	#[coercive]
	public int $p2 = 100;
	
	#[coercive(PropertiesTest_Type::class)]
	public $p3;
	
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
