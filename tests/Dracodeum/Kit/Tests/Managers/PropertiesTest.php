<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Managers;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Property\{
	mode,
	coercive,
	strict,
	lazy
};
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;
use Dracodeum\Kit\Managers\PropertiesV2\Property;
use Dracodeum\Kit\Managers\PropertiesV2\Exceptions\{
	Missing as MissingException,
	Undefined as UndefinedException,
	Inaccessible as InaccessibleException,
	Unreadable as UnreadableException,
	Unwriteable as UnwriteableException,
	Uninitialized as UninitializedException,
	Invalid as InvalidException
};
use Dracodeum\Kit\Utilities\Call\Exceptions\Halt\NotAllowed as CallNotAllowedException;
use stdClass;
use Closure;

/** @see \Dracodeum\Kit\Managers\PropertiesV2 */
class PropertiesTest extends TestCase
{
	//Public methods
	/**
	 * Test instantiation.
	 * 
	 * @testdox Instantiation
	 * 
	 * @return void
	 */
	public function testInstantiation(): void
	{
		//initialize
		Manager::clearCache();
		$owner = new stdClass();
		$manager = new Manager($owner);
		
		//assert
		$this->assertSame($owner, $manager->getOwner());
		$this->assertSame([], $manager->getProperties());
		$this->assertFalse($manager->isInitialized());
	}
	
	/**
	 * Test pre-initialization `get` method expecting a `NotAllowed` exception to be thrown.
	 * 
	 * @testdox Pre-initialization "get" method NotAllowed exception
	 * 
	 * @return void
	 */
	public function testPreInitialization_Get_CallNotAllowedException(): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new stdClass());
		$this->assertFalse($manager->isInitialized());
		
		//exception
		$this->expectException(CallNotAllowedException::class);
		try {
			$manager->get('foo');
		} catch (CallNotAllowedException $exception) {
			$this->assertSame('get', $exception->function_name);
			$this->assertSame($manager, $exception->object_class);
			throw $exception;
		}
	}
	
	/**
	 * Test pre-initialization `mget` method expecting a `NotAllowed` exception to be thrown.
	 * 
	 * @testdox Pre-initialization "mget" method NotAllowed exception
	 * 
	 * @return void
	 */
	public function testPreInitialization_Mget_CallNotAllowedException(): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new stdClass());
		$this->assertFalse($manager->isInitialized());
		
		//exception
		$this->expectException(CallNotAllowedException::class);
		try {
			$manager->mget();
		} catch (CallNotAllowedException $exception) {
			$this->assertSame('mget', $exception->function_name);
			$this->assertSame($manager, $exception->object_class);
			throw $exception;
		}
	}
	
	/**
	 * Test pre-initialization `set` method expecting a `NotAllowed` exception to be thrown.
	 * 
	 * @testdox Pre-initialization "set" method NotAllowed exception
	 * 
	 * @return void
	 */
	public function testPreInitialization_Set_CallNotAllowedException(): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new stdClass());
		$this->assertFalse($manager->isInitialized());
		
		//exception
		$this->expectException(CallNotAllowedException::class);
		try {
			$manager->set('foo', 'bar');
		} catch (CallNotAllowedException $exception) {
			$this->assertSame('set', $exception->function_name);
			$this->assertSame($manager, $exception->object_class);
			throw $exception;
		}
	}
	
	/**
	 * Test pre-initialization `mset` method expecting a `NotAllowed` exception to be thrown.
	 * 
	 * @testdox Pre-initialization "mset" method NotAllowed exception
	 * 
	 * @return void
	 */
	public function testPreInitialization_Mset_CallNotAllowedException(): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new stdClass());
		$this->assertFalse($manager->isInitialized());
		
		//exception
		$this->expectException(CallNotAllowedException::class);
		try {
			$manager->mset(['foo' => 'bar']);
		} catch (CallNotAllowedException $exception) {
			$this->assertSame('mset', $exception->function_name);
			$this->assertSame($manager, $exception->object_class);
			throw $exception;
		}
	}
	
	/**
	 * Test pre-initialization `unset` method expecting a `NotAllowed` exception to be thrown.
	 * 
	 * @testdox Pre-initialization "unset" method NotAllowed exception
	 * 
	 * @return void
	 */
	public function testPreInitialization_Unset_CallNotAllowedException(): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new stdClass());
		$this->assertFalse($manager->isInitialized());
		
		//exception
		$this->expectException(CallNotAllowedException::class);
		try {
			$manager->unset('foo');
		} catch (CallNotAllowedException $exception) {
			$this->assertSame('unset', $exception->function_name);
			$this->assertSame($manager, $exception->object_class);
			throw $exception;
		}
	}
	
	/**
	 * Test pre-initialization `munset` method expecting a `NotAllowed` exception to be thrown.
	 * 
	 * @testdox Pre-initialization "munset" method NotAllowed exception
	 * 
	 * @return void
	 */
	public function testPreInitialization_Munset_CallNotAllowedException(): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new stdClass());
		$this->assertFalse($manager->isInitialized());
		
		//exception
		$this->expectException(CallNotAllowedException::class);
		try {
			$manager->munset(['foo', 'bar']);
		} catch (CallNotAllowedException $exception) {
			$this->assertSame('munset', $exception->function_name);
			$this->assertSame($manager, $exception->object_class);
			throw $exception;
		}
	}
	
	/**
	 * Test initialization.
	 * 
	 * @testdox Initialization
	 * 
	 * @return void
	 */
	public function testInitialization(): void
	{
		//initialize
		Manager::clearCache();
		$owner = new stdClass();
		$manager = new Manager($owner);
		$manager->initialize();
		
		//assert
		$this->assertSame($owner, $manager->getOwner());
		$this->assertSame([], $manager->getProperties());
		$this->assertTrue($manager->isInitialized());
	}
	
	/**
	 * Test post-initialization expecting a `NotAllowed` exception to be thrown.
	 * 
	 * @testdox Post-initialization NotAllowed exception
	 * 
	 * @return void
	 */
	public function testPostInitialization_CallNotAllowedException(): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new stdClass());
		$this->assertFalse($manager->isInitialized());
		$manager->initialize();
		$this->assertTrue($manager->isInitialized());
		
		//exception
		$this->expectException(CallNotAllowedException::class);
		try {
			$manager->initialize();
		} catch (CallNotAllowedException $exception) {
			$this->assertSame('initialize', $exception->function_name);
			$this->assertSame($manager, $exception->object_class);
			throw $exception;
		}
	}
	
	/**
	 * Test properties (class 1).
	 * 
	 * @testdox Properties (class 1)
	 * 
	 * @return void
	 */
	public function testProperties_Class1(): void
	{
		//initialize
		Manager::clearCache();
		$class1 = PropertiesTest_Class1::class;
		$manager = new Manager(new $class1());
		
		//inexistent
		$this->assertFalse($manager->hasProperty('p'));
		$this->assertNull($manager->getProperty('p'));
		
		//properties
		$properties = $manager->getProperties();
		$this->assertSame($properties, (new Manager(new $class1()))->getProperties());
		$this->assertSame(array_keys($properties), [
			'p0', 'p1', 'p6', 'p7', 'p8', 'p9', 'p10', 'p11', 'p12', 'p13', 'p14', 'p15', 'p16', 'p17', 'p18', 'p19',
			'p20', 'p21', 'p22', 'p23', 'c1p0', 'c1p1'
		]);
		foreach ($properties as $name => $property) {
			$this->assertInstanceOf(Property::class, $property);
			$this->assertTrue($manager->hasProperty($name));
			$check_function = Closure::fromCallable([$this, 'check' . strtoupper($name)]);
			$check_function($property);
		}
	}
	
	/**
	 * Test properties (class 2).
	 * 
	 * @testdox Properties (class 2)
	 * 
	 * @return void
	 */
	public function testProperties_Class2(): void
	{
		//initialize
		Manager::clearCache();
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		$manager = new Manager(new $class2());
		
		//inexistent
		$this->assertFalse($manager->hasProperty('p'));
		$this->assertNull($manager->getProperty('p'));
		
		//properties
		$properties = $manager->getProperties();
		foreach ((new Manager(new $class1()))->getProperties() as $name => $c1_property) {
			$this->assertSame($properties[$name], $c1_property);
		}
		$this->assertSame($properties, (new Manager(new $class2()))->getProperties());
		$this->assertSame(array_keys($properties), [
			'p0', 'c2p0', 'c2p3', 'p1', 'p6', 'p7', 'p8', 'p9', 'p10', 'p11', 'p12', 'p13', 'p14', 'p15', 'p16', 'p17',
			'p18', 'p19', 'p20', 'p21', 'p22', 'p23', 'c1p0', 'c1p1', 'c2p1', 'c2p2', 'c2p4'
		]);
		foreach ($properties as $name => $property) {
			$this->assertInstanceOf(Property::class, $property);
			$this->assertTrue($manager->hasProperty($name));
			$check_function = Closure::fromCallable([$this, 'check' . strtoupper($name)]);
			$check_function($property);
		}
	}
	
	/**
	 * Test values initialization.
	 * 
	 * @testdox Values initialization
	 * @dataProvider provideValuesInitializationData
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string|null $scope_class
	 * The scope class to test with.
	 * 
	 * @param array $values
	 * The values to test with.
	 * 
	 * @param array $expected_values
	 * The expected initialized values.
	 * 
	 * @return void
	 */
	public function testValuesInitialization(
		string $class, ?string $scope_class, array $values, array $expected_values
	): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize($values, $scope_class);
		
		//assert
		$this->assertSame($expected_values, $manager->mget(scope_class: $class));
	}
	
	/**
	 * Provide values initialization data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideValuesInitializationData(): array
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			[
				$class1, null, [
					'p0' => 123
				], [
					'p0' => 123,
					'p6' => null,
					'p7' => null,
					'p8' => 0,
					'p9' => 1.0,
					'p11' => 1,
					'p12' => 1,
					'p13' => 1,
					'p14' => 1,
					'p15' => 1,
					'p16' => 1,
					'p17' => 1,
					'p18' => 1,
					'p19' => 1200,
					'p20' => '420',
					'p21' => null,
					'p22' => '100',
					'p23' => 1,
					'c1p0' => 'foo',
					'c1p1' => ''
				]
			], [
				$class1, null, [
					123,
					'p6' => true,
					'p8' => '-98',
					'p12' => ['a'],
					'p13' => 333,
					'p14' => 8.75,
					'p16' => 'FOO',
					'p17' => false,
					'p18' => true,
					'p19' => '-749',
					'p20' => 'fooBar',
					'p21' => '7k',
					'p22' => '75.80',
					'p23' => '930',
					'c1p0' => 56.72
				], [
					'p0' => 123,
					'p6' => true,
					'p7' => null,
					'p8' => -98,
					'p9' => 1.0,
					'p11' => 1,
					'p12' => ['a'],
					'p13' => 333,
					'p14' => 8.75,
					'p15' => 1,
					'p16' => 'FOO',
					'p17' => false,
					'p18' => true,
					'p19' => -749,
					'p20' => 'fooBar',
					'p21' => 7000,
					'p22' => '75.80',
					'p23' => 930,
					'c1p0' => '56.72',
					'c1p1' => ''
				]
			], [
				$class1, $stdclass, [
					'p0' => 123
				], [
					'p0' => 123,
					'p6' => null,
					'p7' => null,
					'p8' => 0,
					'p9' => 1.0,
					'p11' => 1,
					'p12' => 1,
					'p13' => 1,
					'p14' => 1,
					'p15' => 1,
					'p16' => 1,
					'p17' => 1,
					'p18' => 1,
					'p19' => 1200,
					'p20' => '420',
					'p21' => null,
					'p22' => '100',
					'p23' => 1,
					'c1p0' => 'foo',
					'c1p1' => ''
				]
			], [
				$class1, $stdclass, [
					123,
					'p6' => true,
					'p8' => '-98',
					'p12' => ['a'],
					'p13' => 333,
					'p14' => 8.75,
					'p16' => 'FOO',
					'p17' => false,
					'p18' => true,
					'p19' => '-749',
					'p20' => 'fooBar',
					'p21' => '7k',
					'p22' => '75.80',
					'p23' => '930',
					'c1p0' => 56.72
				], [
					'p0' => 123,
					'p6' => true,
					'p7' => null,
					'p8' => -98,
					'p9' => 1.0,
					'p11' => 1,
					'p12' => ['a'],
					'p13' => 333,
					'p14' => 8.75,
					'p15' => 1,
					'p16' => 'FOO',
					'p17' => false,
					'p18' => true,
					'p19' => -749,
					'p20' => 'fooBar',
					'p21' => 7000,
					'p22' => '75.80',
					'p23' => 930,
					'c1p0' => '56.72',
					'c1p1' => ''
				]
			], [
				$class1, $class1, [
					'p0' => 123,
					'p1' => 456
				], [
					'p0' => 123,
					'p1' => 456,
					'p6' => null,
					'p7' => null,
					'p8' => 0,
					'p9' => 1.0,
					'p11' => 1,
					'p12' => 1,
					'p13' => 1,
					'p14' => 1,
					'p15' => 1,
					'p16' => 1,
					'p17' => 1,
					'p18' => 1,
					'p19' => 1200,
					'p20' => '420',
					'p21' => null,
					'p22' => '100',
					'p23' => 1,
					'c1p0' => 'foo',
					'c1p1' => ''
				]
			], [
				$class1, $class1, [
					123,
					'p1' => 456,
					'p6' => true,
					'p7' => [],
					'p8' => '-98',
					'p9' => '-2.5',
					'p10' => '__T__',
					'p11' => '__A__',
					'p12' => ['a'],
					'p13' => 333,
					'p14' => 8.75,
					'p15' => 0x0f,
					'p16' => 'FOO',
					'p17' => false,
					'p18' => true,
					'p19' => '-749',
					'p20' => 'fooBar',
					'p21' => '7k',
					'p22' => '75.80',
					'p23' => '930',
					'c1p0' => 56.72,
					'c1p1' => 234
				], [
					'p0' => 123,
					'p1' => 456,
					'p6' => true,
					'p7' => [],
					'p8' => -98,
					'p9' => -2.5,
					'p10' => '__T__',
					'p11' => '__A__',
					'p12' => ['a'],
					'p13' => 333,
					'p14' => 8.75,
					'p15' => 0x0f,
					'p16' => 'FOO',
					'p17' => false,
					'p18' => true,
					'p19' => -749,
					'p20' => 'fooBar',
					'p21' => 7000,
					'p22' => '75.80',
					'p23' => 930,
					'c1p0' => '56.72',
					'c1p1' => '234'
				]
			], [
				$class1, $class2, [
					'p0' => 123,
					'p1' => 456
				], [
					'p0' => 123,
					'p1' => 456,
					'p6' => null,
					'p7' => null,
					'p8' => 0,
					'p9' => 1.0,
					'p11' => 1,
					'p12' => 1,
					'p13' => 1,
					'p14' => 1,
					'p15' => 1,
					'p16' => 1,
					'p17' => 1,
					'p18' => 1,
					'p19' => 1200,
					'p20' => '420',
					'p21' => null,
					'p22' => '100',
					'p23' => 1,
					'c1p0' => 'foo',
					'c1p1' => ''
				]
			], [
				$class1, $class2, [
					123,
					'p1' => 456,
					'p6' => true,
					'p7' => [],
					'p8' => '-98',
					'p9' => '-2.5',
					'p10' => '__T__',
					'p11' => '__A__',
					'p12' => ['a'],
					'p13' => 333,
					'p14' => 8.75,
					'p16' => 'FOO',
					'p17' => false,
					'p18' => true,
					'p19' => '-749',
					'p20' => 'fooBar',
					'p21' => '7k',
					'p22' => '75.80',
					'p23' => '930',
					'c1p0' => 56.72,
					'c1p1' => 234
				], [
					'p0' => 123,
					'p1' => 456,
					'p6' => true,
					'p7' => [],
					'p8' => -98,
					'p9' => -2.5,
					'p10' => '__T__',
					'p11' => '__A__',
					'p12' => ['a'],
					'p13' => 333,
					'p14' => 8.75,
					'p15' => 1,
					'p16' => 'FOO',
					'p17' => false,
					'p18' => true,
					'p19' => -749,
					'p20' => 'fooBar',
					'p21' => 7000,
					'p22' => '75.80',
					'p23' => 930,
					'c1p0' => '56.72',
					'c1p1' => '234'
				]
			], [
				$class2, null, [
					'p0' => 123,
					'c2p0' => '4.35M',
					'c2p3' => 2
				], [
					'p0' => 123,
					'c2p0' => 4350000,
					'c2p3' => '2',
					'p6' => null,
					'p7' => null,
					'p8' => 0,
					'p9' => 1.0,
					'p11' => 1,
					'p12' => 1,
					'p13' => 1,
					'p14' => 1,
					'p15' => 1,
					'p16' => 1,
					'p19' => 1200,
					'p20' => '420',
					'p21' => null,
					'p22' => '100',
					'p23' => 1,
					'c1p0' => 'foo',
					'c2p1' => false,
					'c2p2' => 75.5,
					'c2p4' => []
				]
			], [
				$class2, null, [
					123, '4.35M', 2,
					'p6' => true,
					'p8' => '-98',
					'p12' => ['a'],
					'p13' => 333,
					'p14' => 8.75,
					'p16' => 'FOO',
					'p17' => false,
					'p18' => true,
					'p19' => '-749',
					'p20' => 'fooBar',
					'p21' => '7k',
					'p22' => '75.80',
					'p23' => '930',
					'c1p0' => 56.72,
					'c2p2' => '975'
					
				], [
					'p0' => 123,
					'c2p0' => 4350000,
					'c2p3' => '2',
					'p6' => true,
					'p7' => null,
					'p8' => -98,
					'p9' => 1.0,
					'p11' => 1,
					'p12' => ['a'],
					'p13' => 333,
					'p14' => 8.75,
					'p15' => 1,
					'p16' => 'FOO',
					'p19' => -749,
					'p20' => 'fooBar',
					'p21' => 7000,
					'p22' => '75.80',
					'p23' => 930,
					'c1p0' => '56.72',
					'c2p1' => false,
					'c2p2' => 975,
					'c2p4' => []
				]
			], [
				$class2, $stdclass, [
					'p0' => 123,
					'c2p0' => '4.35M',
					'c2p3' => 2
				], [
					'p0' => 123,
					'c2p0' => 4350000,
					'c2p3' => '2',
					'p6' => null,
					'p7' => null,
					'p8' => 0,
					'p9' => 1.0,
					'p11' => 1,
					'p12' => 1,
					'p13' => 1,
					'p14' => 1,
					'p15' => 1,
					'p16' => 1,
					'p19' => 1200,
					'p20' => '420',
					'p21' => null,
					'p22' => '100',
					'p23' => 1,
					'c1p0' => 'foo',
					'c2p1' => false,
					'c2p2' => 75.5,
					'c2p4' => []
				]
			], [
				$class2, $stdclass, [
					123, '4.35M', 2,
					'p6' => true,
					'p8' => '-98',
					'p12' => ['a'],
					'p13' => 333,
					'p14' => 8.75,
					'p16' => 'FOO',
					'p17' => false,
					'p18' => true,
					'p19' => '-749',
					'p20' => 'fooBar',
					'p21' => '7k',
					'p22' => '75.80',
					'p23' => '930',
					'c1p0' => 56.72,
					'c2p2' => '975'
					
				], [
					'p0' => 123,
					'c2p0' => 4350000,
					'c2p3' => '2',
					'p6' => true,
					'p7' => null,
					'p8' => -98,
					'p9' => 1.0,
					'p11' => 1,
					'p12' => ['a'],
					'p13' => 333,
					'p14' => 8.75,
					'p15' => 1,
					'p16' => 'FOO',
					'p19' => -749,
					'p20' => 'fooBar',
					'p21' => 7000,
					'p22' => '75.80',
					'p23' => 930,
					'c1p0' => '56.72',
					'c2p1' => false,
					'c2p2' => 975,
					'c2p4' => []
				]
			], [
				$class2, $class1, [
					'p0' => 123,
					'c2p0' => '4.35M',
					'c2p3' => 2,
					'p1' => 456
				], [
					'p0' => 123,
					'c2p0' => 4350000,
					'c2p3' => '2',
					'p1' => 456,
					'p6' => null,
					'p7' => null,
					'p8' => 0,
					'p9' => 1.0,
					'p11' => 1,
					'p12' => 1,
					'p13' => 1,
					'p14' => 1,
					'p15' => 1,
					'p16' => 1,
					'p19' => 1200,
					'p20' => '420',
					'p21' => null,
					'p22' => '100',
					'p23' => 1,
					'c1p0' => 'foo',
					'c2p1' => false,
					'c2p2' => 75.5,
					'c2p4' => []
				]
			], [
				$class2, $class1, [
					123, '4.35M', 2,
					'p1' => 456,
					'p6' => true,
					'p7' => [],
					'p8' => '-98',
					'p9' => '-2.5',
					'p10' => '__T__',
					'p11' => '__A__',
					'p12' => ['a'],
					'p13' => 333,
					'p14' => 8.75,
					'p15' => 0x0f,
					'p16' => 'FOO',
					'p17' => false,
					'p18' => true,
					'p19' => '-749',
					'p20' => 'fooBar',
					'p21' => '7k',
					'p22' => '75.80',
					'p23' => '930',
					'c1p0' => 56.72,
					'c1p1' => 234,
					'c2p2' => '975'
				], [
					'p0' => 123,
					'c2p0' => 4350000,
					'c2p3' => '2',
					'p1' => 456,
					'p6' => true,
					'p7' => [],
					'p8' => -98,
					'p9' => -2.5,
					'p10' => '__T__',
					'p11' => '__A__',
					'p12' => ['a'],
					'p13' => 333,
					'p14' => 8.75,
					'p15' => 0x0f,
					'p16' => 'FOO',
					'p19' => -749,
					'p20' => 'fooBar',
					'p21' => 7000,
					'p22' => '75.80',
					'p23' => 930,
					'c1p0' => '56.72',
					'c2p1' => false,
					'c2p2' => 975,
					'c2p4' => []
				]
			], [
				$class2, $class2, [
					'p0' => 123,
					'c2p0' => '4.35M',
					'c2p3' => 2,
					'p1' => 456
				], [
					'p0' => 123,
					'c2p0' => 4350000,
					'c2p3' => '2',
					'p1' => 456,
					'p6' => null,
					'p7' => null,
					'p8' => 0,
					'p9' => 1.0,
					'p11' => 1,
					'p12' => 1,
					'p13' => 1,
					'p14' => 1,
					'p15' => 1,
					'p16' => 1,
					'p19' => 1200,
					'p20' => '420',
					'p21' => null,
					'p22' => '100',
					'p23' => 1,
					'c1p0' => 'foo',
					'c2p1' => false,
					'c2p2' => 75.5,
					'c2p4' => []
				]
			], [
				$class2, $class2, [
					123, '4.35M', 2,
					'p1' => 456,
					'p6' => true,
					'p7' => [],
					'p8' => '-98',
					'p9' => '-2.5',
					'p10' => '__T__',
					'p11' => '__A__',
					'p12' => ['a'],
					'p13' => 333,
					'p14' => 8.75,
					'p16' => 'FOO',
					'p17' => false,
					'p18' => true,
					'p19' => '-749',
					'p20' => 'fooBar',
					'p21' => '7k',
					'p22' => '75.80',
					'p23' => '930',
					'c1p0' => 56.72,
					'c1p1' => 234,
					'c2p1' => 1,
					'c2p2' => '975',
					'c2p4' => ['foo', 'bar']
				], [
					'p0' => 123,
					'c2p0' => 4350000,
					'c2p3' => '2',
					'p1' => 456,
					'p6' => true,
					'p7' => [],
					'p8' => -98,
					'p9' => -2.5,
					'p10' => '__T__',
					'p11' => '__A__',
					'p12' => ['a'],
					'p13' => 333,
					'p14' => 8.75,
					'p15' => 1,
					'p16' => 'FOO',
					'p19' => -749,
					'p20' => 'fooBar',
					'p21' => 7000,
					'p22' => '75.80',
					'p23' => 930,
					'c1p0' => '56.72',
					'c2p1' => true,
					'c2p2' => 975,
					'c2p4' => ['foo', 'bar']
				]
			]
		];
	}
	
	/**
	 * Test values initialization expecting a `Missing` exception to be thrown.
	 * 
	 * @testdox Values initialization Missing exception
	 * @dataProvider provideValuesInitializationData_MissingException
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param array $values
	 * The values to test with.
	 * 
	 * @param string[] $expected_names
	 * The expected exception value names.
	 * 
	 * @return void
	 */
	public function testValuesInitialization_MissingException(string $class, array $values, array $expected_names): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		
		//exception
		$this->expectException(MissingException::class);
		try {
			$manager->initialize($values);
		} catch (MissingException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame($expected_names, $exception->names);
			throw $exception;
		}
	}
	
	/**
	 * Provide values initialization data for a `Missing` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideValuesInitializationData_MissingException(): array
	{
		//initialize
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			[$class1, [], ['p0']],
			[$class2, [], ['p0', 'c2p0', 'c2p3']],
			[$class2, [123], ['c2p0', 'c2p3']],
			[$class2, [123, '4.35M'], ['c2p3']],
			[$class2, ['c2p0' => '4.35M', 'c2p3' => 2], ['p0']],
			[$class2, ['c2p0' => '4.35M'], ['p0', 'c2p3']],
			[$class2, ['c2p3' => 2], ['p0', 'c2p0']]
		];
	}
	
	/**
	 * Test values initialization expecting an `Undefined` exception to be thrown.
	 * 
	 * @testdox Values initialization Undefined exception
	 * @dataProvider provideValuesInitializationData_UndefinedException
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param array $values
	 * The values to test with.
	 * 
	 * @param string[] $expected_names
	 * The expected exception value names.
	 * 
	 * @return void
	 */
	public function testValuesInitialization_UndefinedException(
		string $class, array $values, array $expected_names
	): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		
		//exception
		$this->expectException(UndefinedException::class);
		try {
			$manager->initialize($values);
		} catch (UndefinedException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame($expected_names, $exception->names);
			throw $exception;
		}
	}
	
	/**
	 * Provide values initialization data for an `Undefined` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideValuesInitializationData_UndefinedException(): array
	{
		//initialize
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			[$class1, [123, 'p' => 1], ['p']],
			[$class1, [123, 'p' => 1, 'p2' => 2, 'p6' => 3], ['p', 'p2']],
			[$class1, [123, 'p' => 1, 'p2' => 2, 'p6' => 3, 'p3' => 4], ['p', 'p2', 'p3']],
			[$class2, [123, 'p' => 1, '4.35M', 2], ['p']],
			[$class2, [123, 'p' => 1, 'p2' => 2, 'p6' => 3, '4.35M', 2], ['p', 'p2']],
			[$class2, [123, 'p' => 1, 'p2' => 2, 'p6' => 3, 'p3' => 4, '4.35M', 2], ['p', 'p2', 'p3']],
			[$class2, [123, 'p' => 1, 'p2' => 2, 'p6' => 3, 'p3' => 4, '4.35M', 2, 'c2p5' => 9],
				['p', 'p2', 'p3', 'c2p5']]
		];
	}
	
	/**
	 * Test values initialization expecting an `Inaccessible` exception to be thrown.
	 * 
	 * @testdox Values initialization Inaccessible exception
	 * @dataProvider provideValuesInitializationData_InaccessibleException
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string|null $scope_class
	 * The scope class to test with.
	 * 
	 * @param array $values
	 * The values to test with.
	 * 
	 * @param string[] $expected_names
	 * The expected exception value names.
	 * 
	 * @return void
	 */
	public function testValuesInitialization_InaccessibleException(
		string $class, ?string $scope_class, array $values, array $expected_names
	): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		
		//exception
		$this->expectException(InaccessibleException::class);
		try {
			$manager->initialize($values, $scope_class);
		} catch (InaccessibleException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame($expected_names, $exception->names);
			$this->assertSame($scope_class, $exception->scope_class);
			throw $exception;
		}
	}
	
	/**
	 * Provide values initialization data for an `Inaccessible` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideValuesInitializationData_InaccessibleException(): array
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			[$class1, null, [123, 'p1' => 1], ['p1']],
			[$class1, null, [123, 'p1' => 1, 'p6' => 2, 'p7' => 3], ['p1', 'p7']],
			[$class1, null, [123, 'p1' => 1, 'p6' => 2, 'p7' => 3, 'p9' => 6], ['p1', 'p7', 'p9']],
			[$class1, $stdclass, [123, 'p1' => 1], ['p1']],
			[$class1, $stdclass, [123, 'p1' => 1, 'p6' => 2, 'p7' => 3], ['p1', 'p7']],
			[$class1, $stdclass, [123, 'p1' => 1, 'p6' => 2, 'p7' => 3, 'p9' => 6], ['p1', 'p7', 'p9']],
			[$class2, null, [123, 'p1' => 1, '4.35M', 2], ['p1']],
			[$class2, null, [123, 'p1' => 1, 'p6' => 2, 'p7' => 3, '4.35M', 2], ['p1', 'p7']],
			[$class2, null, [123, 'p1' => 1, 'p6' => 2, 'p7' => 3, 'p9' => 6, '4.35M', 2], ['p1', 'p7', 'p9']],
			[$class2, null, [123, 'p1' => 1, 'p6' => 2, 'p7' => 3, 'p9' => 6, '4.35M', 2, 'c2p1' => 1, 'c2p2' => 0],
				['p1', 'p7', 'p9', 'c2p1']],
			[$class2, $stdclass, [123, 'p1' => 1, '4.35M', 2], ['p1']],
			[$class2, $stdclass, [123, 'p1' => 1, 'p6' => 2, 'p7' => 3, '4.35M', 2], ['p1', 'p7']],
			[$class2, $stdclass, [123, 'p1' => 1, 'p6' => 2, 'p7' => 3, 'p9' => 6, '4.35M', 2], ['p1', 'p7', 'p9']],
			[$class2, $stdclass,
				[123, 'p1' => 1, 'p6' => 2, 'p7' => 3, 'p9' => 6, '4.35M', 2, 'c2p1' => 1, 'c2p2' => 0],
				['p1', 'p7', 'p9', 'c2p1']],
			[$class2, $class1, [123, 'p1' => 1, 'p6' => 2, 'p7' => 3, 'p9' => 6, '4.35M', 2, 'c2p1' => 1, 'c2p2' => 0],
				['c2p1']]
		];
	}
	
	/**
	 * Test values initialization expecting an `Unwriteable` exception to be thrown.
	 * 
	 * @testdox Values initialization Unwriteable exception
	 * @dataProvider provideValuesInitializationData_UnwriteableException
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string|null $scope_class
	 * The scope class to test with.
	 * 
	 * @param array $values
	 * The values to test with.
	 * 
	 * @param string[] $expected_names
	 * The expected exception value names.
	 * 
	 * @return void
	 */
	public function testValuesInitialization_UnwriteableException(
		string $class, ?string $scope_class, array $values, array $expected_names
	): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		
		//exception
		$this->expectException(UnwriteableException::class);
		try {
			$manager->initialize($values, $scope_class);
		} catch (UnwriteableException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame($expected_names, $exception->names);
			$this->assertSame($scope_class, $exception->scope_class);
			throw $exception;
		}
	}
	
	/**
	 * Provide values initialization data for an `Unwriteable` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideValuesInitializationData_UnwriteableException(): array
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		$class3 = PropertiesTest_Class3::class;
		
		//return
		return [
			[$class1, null, [123, 'p10' => 1], ['p10']],
			[$class1, null, [123, 'p10' => 1, 'p12' => 4, 'p15' => 7], ['p10', 'p15']],
			[$class1, null, [123, 'p10' => 1, 'p12' => 4, 'p15' => 7, 'p11' => 0], ['p10', 'p15', 'p11']],
			[$class1, $stdclass, [123, 'p10' => 1], ['p10']],
			[$class1, $stdclass, [123, 'p10' => 1, 'p12' => 4, 'p15' => 7], ['p10', 'p15']],
			[$class1, $stdclass, [123, 'p10' => 1, 'p12' => 4, 'p15' => 7, 'p11' => 0], ['p10', 'p15', 'p11']],
			[$class1, $class2, [123, 'p10' => 1, 'p12' => 4, 'p15' => 7], ['p15']],
			[$class1, $class2, [123, 'p10' => 1, 'p12' => 4, 'p15' => 7, 'p11' => 0], ['p15']],
			[$class2, null, [123, 'p10' => 1, '4.35M', 2], ['p10']],
			[$class2, null, [123, 'p10' => 1, 'p12' => 4, 'p15' => 7, '4.35M', 2], ['p10', 'p15']],
			[$class2, null, [123, 'p10' => 1, 'p12' => 4, 'p15' => 7, 'p11' => 0, '4.35M', 2], ['p10', 'p15', 'p11']],
			[$class2, $stdclass, [123, 'p10' => 1, '4.35M', 2], ['p10']],
			[$class2, $stdclass, [123, 'p10' => 1, 'p12' => 4, 'p15' => 7, '4.35M', 2], ['p10', 'p15']],
			[$class2, $stdclass, [123, 'p10' => 1, 'p12' => 4, 'p15' => 7, 'p11' => 0, '4.35M', 2],
				['p10', 'p15', 'p11']],
			[$class3, $class2, [123, 'p10' => 1, 'p12' => 4, 'p15' => 7, 'p11' => 0, '4.35M', 2, 'c3p0' => 9],
				['p15', 'c3p0']]
		];
	}
	
	/**
	 * Test values initialization expecting an `Invalid` exception to be thrown.
	 * 
	 * @testdox Values initialization Invalid exception
	 * @dataProvider provideValuesInitializationData_InvalidException
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param array $values
	 * The values to test with.
	 * 
	 * @param string[] $expected_names
	 * The expected exception value names.
	 * 
	 * @return void
	 */
	public function testValuesInitialization_InvalidException(string $class, array $values, array $expected_names): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		
		//exception
		$this->expectException(InvalidException::class);
		try {
			$manager->initialize($values);
		} catch (InvalidException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame($expected_names, array_keys($exception->values));
			$this->assertSame($expected_names, array_keys($exception->errors));
			throw $exception;
		}
	}
	
	/**
	 * Provide values initialization data for an `Invalid` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideValuesInitializationData_InvalidException(): array
	{
		//initialize
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			[$class1, [123, 'p8' => 'foo'], ['p8']],
			[$class1, [123, 'p8' => 'foo', 'p12' => 1, 'p19' => true], ['p8', 'p19']],
			[$class1, [123, 'p8' => 'foo', 'p12' => 1, 'p19' => true, 'p20' => []], ['p8', 'p19', 'p20']],
			[$class2, [123, 'p8' => 'foo', '4.35M', 2], ['p8']],
			[$class2, [123, 'p8' => 'foo', 'p12' => 1, 'p19' => true, '4.35M', 2], ['p8', 'p19']],
			[$class2, [123, 'p8' => 'foo', 'p12' => 1, 'p19' => true, 'p20' => [], '4.35M', 2], ['p8', 'p19', 'p20']],
			[$class2, [123, 'p8' => 'foo', 'p12' => 1, 'p19' => true, 'p20' => [], '4.35M', 2, 'c2p2' => false],
				['p8', 'p19', 'p20', 'c2p2']],
			[$class2, [123, 'p8' => 'foo', 'a', 2], ['p8', 'c2p0']],
			[$class2, [123, 'p8' => 'foo', 'p12' => 1, 'p19' => true, 'a', 2], ['p8', 'p19', 'c2p0']],
			[$class2, [123, 'p8' => 'foo', 'p12' => 1, 'p19' => true, 'p20' => [], 'a', 2],
				['p8', 'p19', 'p20', 'c2p0']],
			[$class2, [123, 'p8' => 'foo', 'p12' => 1, 'p19' => true, 'p20' => [], 'a', 2, 'c2p2' => false],
				['p8', 'p19', 'p20', 'c2p0', 'c2p2']],
		];
	}
	
	/**
	 * Test `has` method.
	 * 
	 * @testdox Has
	 * @dataProvider provideHasData
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string|null $scope_class
	 * The scope class to test with.
	 * 
	 * @param bool $expected
	 * The expected returning value.
	 * 
	 * @return void
	 */
	public function testHas(string $name, string $class, ?string $scope_class, bool $expected): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		
		//assert
		$this->assertSame($expected, $manager->has($name, $scope_class));
	}
	
	/**
	 * Provide `has` method data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideHasData(): array
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			['p', $class1, null, false],
			['p', $class1, $stdclass, false],
			['p', $class1, $class1, false],
			['p', $class1, $class2, false],
			['p', $class2, null, false],
			['p', $class2, $stdclass, false],
			['p', $class2, $class1, false],
			['p', $class2, $class2, false],
			['p0', $class1, null, true],
			['p0', $class1, $stdclass, true],
			['p0', $class1, $class1, true],
			['p0', $class1, $class2, true],
			['p0', $class2, null, true],
			['p0', $class2, $stdclass, true],
			['p0', $class2, $class1, true],
			['p0', $class2, $class2, true],
			['p1', $class1, null, false],
			['p1', $class1, $stdclass, false],
			['p1', $class1, $class1, true],
			['p1', $class1, $class2, true],
			['p1', $class2, null, false],
			['p1', $class2, $stdclass, false],
			['p1', $class2, $class1, true],
			['p1', $class2, $class2, true],
			['p2', $class1, null, false],
			['p2', $class1, $stdclass, false],
			['p2', $class1, $class1, false],
			['p2', $class1, $class2, false],
			['p2', $class2, null, false],
			['p2', $class2, $stdclass, false],
			['p2', $class2, $class1, false],
			['p2', $class2, $class2, false],
			['p3', $class1, null, false],
			['p3', $class1, $stdclass, false],
			['p3', $class1, $class1, false],
			['p3', $class1, $class2, false],
			['p3', $class2, null, false],
			['p3', $class2, $stdclass, false],
			['p3', $class2, $class1, false],
			['p3', $class2, $class2, false],
			['p4', $class1, null, false],
			['p4', $class1, $stdclass, false],
			['p4', $class1, $class1, false],
			['p4', $class1, $class2, false],
			['p4', $class2, null, false],
			['p4', $class2, $stdclass, false],
			['p4', $class2, $class1, false],
			['p4', $class2, $class2, false],
			['p5', $class1, null, false],
			['p5', $class1, $stdclass, false],
			['p5', $class1, $class1, false],
			['p5', $class1, $class2, false],
			['p5', $class2, null, false],
			['p5', $class2, $stdclass, false],
			['p5', $class2, $class1, false],
			['p5', $class2, $class2, false],
			['p6', $class1, null, true],
			['p6', $class1, $stdclass, true],
			['p6', $class1, $class1, true],
			['p6', $class1, $class2, true],
			['p6', $class2, null, true],
			['p6', $class2, $stdclass, true],
			['p6', $class2, $class1, true],
			['p6', $class2, $class2, true],
			['p7', $class1, null, false],
			['p7', $class1, $stdclass, false],
			['p7', $class1, $class1, true],
			['p7', $class1, $class2, true],
			['p7', $class2, null, false],
			['p7', $class2, $stdclass, false],
			['p7', $class2, $class1, true],
			['p7', $class2, $class2, true],
			['p8', $class1, null, true],
			['p8', $class1, $stdclass, true],
			['p8', $class1, $class1, true],
			['p8', $class1, $class2, true],
			['p8', $class2, null, true],
			['p8', $class2, $stdclass, true],
			['p8', $class2, $class1, true],
			['p8', $class2, $class2, true],
			['p9', $class1, null, false],
			['p9', $class1, $stdclass, false],
			['p9', $class1, $class1, true],
			['p9', $class1, $class2, true],
			['p9', $class2, null, false],
			['p9', $class2, $stdclass, false],
			['p9', $class2, $class1, true],
			['p9', $class2, $class2, true],
			['p10', $class1, null, true],
			['p10', $class1, $stdclass, true],
			['p10', $class1, $class1, true],
			['p10', $class1, $class2, true],
			['p10', $class2, null, true],
			['p10', $class2, $stdclass, true],
			['p10', $class2, $class1, true],
			['p10', $class2, $class2, true],
			['p11', $class1, null, true],
			['p11', $class1, $stdclass, true],
			['p11', $class1, $class1, true],
			['p11', $class1, $class2, true],
			['p11', $class2, null, true],
			['p11', $class2, $stdclass, true],
			['p11', $class2, $class1, true],
			['p11', $class2, $class2, true],
			['p12', $class1, null, true],
			['p12', $class1, $stdclass, true],
			['p12', $class1, $class1, true],
			['p12', $class1, $class2, true],
			['p12', $class2, null, true],
			['p12', $class2, $stdclass, true],
			['p12', $class2, $class1, true],
			['p12', $class2, $class2, true],
			['p13', $class1, null, false],
			['p13', $class1, $stdclass, false],
			['p13', $class1, $class1, true],
			['p13', $class1, $class2, true],
			['p13', $class2, null, false],
			['p13', $class2, $stdclass, false],
			['p13', $class2, $class1, true],
			['p13', $class2, $class2, true],
			['p14', $class1, null, false],
			['p14', $class1, $stdclass, false],
			['p14', $class1, $class1, true],
			['p14', $class1, $class2, true],
			['p14', $class2, null, false],
			['p14', $class2, $stdclass, false],
			['p14', $class2, $class1, true],
			['p14', $class2, $class2, true],
			['p15', $class1, null, true],
			['p15', $class1, $stdclass, true],
			['p15', $class1, $class1, true],
			['p15', $class1, $class2, true],
			['p15', $class2, null, true],
			['p15', $class2, $stdclass, true],
			['p15', $class2, $class1, true],
			['p15', $class2, $class2, true],
			['p16', $class1, null, true],
			['p16', $class1, $stdclass, true],
			['p16', $class1, $class1, true],
			['p16', $class1, $class2, true],
			['p16', $class2, null, true],
			['p16', $class2, $stdclass, true],
			['p16', $class2, $class1, true],
			['p16', $class2, $class2, true],
			['p17', $class1, null, false],
			['p17', $class1, $stdclass, false],
			['p17', $class1, $class1, true],
			['p17', $class1, $class2, false],
			['p17', $class2, null, false],
			['p17', $class2, $stdclass, false],
			['p17', $class2, $class1, true],
			['p17', $class2, $class2, false],
			['p18', $class1, null, false],
			['p18', $class1, $stdclass, false],
			['p18', $class1, $class1, true],
			['p18', $class1, $class2, false],
			['p18', $class2, null, false],
			['p18', $class2, $stdclass, false],
			['p18', $class2, $class1, true],
			['p18', $class2, $class2, false],
			['c1p0', $class1, null, true],
			['c1p0', $class1, $stdclass, true],
			['c1p0', $class1, $class1, true],
			['c1p0', $class1, $class2, true],
			['c1p0', $class2, null, true],
			['c1p0', $class2, $stdclass, true],
			['c1p0', $class2, $class1, true],
			['c1p0', $class2, $class2, true],
			['c1p1', $class1, null, false],
			['c1p1', $class1, $stdclass, false],
			['c1p1', $class1, $class1, true],
			['c1p1', $class1, $class2, false],
			['c1p1', $class2, null, false],
			['c1p1', $class2, $stdclass, false],
			['c1p1', $class2, $class1, true],
			['c1p1', $class2, $class2, false],
			['c2p0', $class1, null, false],
			['c2p0', $class1, $stdclass, false],
			['c2p0', $class1, $class1, false],
			['c2p0', $class1, $class2, false],
			['c2p0', $class2, null, false],
			['c2p0', $class2, $stdclass, false],
			['c2p0', $class2, $class1, false],
			['c2p0', $class2, $class2, true],
			['c2p1', $class1, null, false],
			['c2p1', $class1, $stdclass, false],
			['c2p1', $class1, $class1, false],
			['c2p1', $class1, $class2, false],
			['c2p1', $class2, null, false],
			['c2p1', $class2, $stdclass, false],
			['c2p1', $class2, $class1, false],
			['c2p1', $class2, $class2, true],
			['c2p2', $class1, null, false],
			['c2p2', $class1, $stdclass, false],
			['c2p2', $class1, $class1, false],
			['c2p2', $class1, $class2, false],
			['c2p2', $class2, null, true],
			['c2p2', $class2, $stdclass, true],
			['c2p2', $class2, $class1, true],
			['c2p2', $class2, $class2, true],
			['c2p3', $class1, null, false],
			['c2p3', $class1, $stdclass, false],
			['c2p3', $class1, $class1, false],
			['c2p3', $class1, $class2, false],
			['c2p3', $class2, null, true],
			['c2p3', $class2, $stdclass, true],
			['c2p3', $class2, $class1, true],
			['c2p3', $class2, $class2, true],
			['c2p4', $class1, null, false],
			['c2p4', $class1, $stdclass, false],
			['c2p4', $class1, $class1, false],
			['c2p4', $class1, $class2, false],
			['c2p4', $class2, null, false],
			['c2p4', $class2, $stdclass, false],
			['c2p4', $class2, $class1, false],
			['c2p4', $class2, $class2, true],
			['c2p5', $class1, null, false],
			['c2p5', $class1, $stdclass, false],
			['c2p5', $class1, $class1, false],
			['c2p5', $class1, $class2, false],
			['c2p5', $class2, null, false],
			['c2p5', $class2, $stdclass, false],
			['c2p5', $class2, $class1, false],
			['c2p5', $class2, $class2, false]
		];
	}
	
	/**
	 * Test `isset` method.
	 * 
	 * @testdox Isset
	 * @dataProvider provideIssetData
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string|null $scope_class
	 * The scope class to test with.
	 * 
	 * @param bool $expected
	 * The expected returning value.
	 * 
	 * @return void
	 */
	public function testIsset(string $name, string $class, ?string $scope_class, bool $expected): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize(match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', 2]
		});
		
		//assert
		$this->assertSame($expected, $manager->isset($name, $scope_class));
	}
	
	/**
	 * Provide `isset` method data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideIssetData(): array
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			['p', $class1, null, false],
			['p', $class1, $stdclass, false],
			['p', $class1, $class1, false],
			['p', $class1, $class2, false],
			['p', $class2, null, false],
			['p', $class2, $stdclass, false],
			['p', $class2, $class1, false],
			['p', $class2, $class2, false],
			['p0', $class1, null, true],
			['p0', $class1, $stdclass, true],
			['p0', $class1, $class1, true],
			['p0', $class1, $class2, true],
			['p0', $class2, null, true],
			['p0', $class2, $stdclass, true],
			['p0', $class2, $class1, true],
			['p0', $class2, $class2, true],
			['p1', $class1, null, false],
			['p1', $class1, $stdclass, false],
			['p1', $class1, $class1, false],
			['p1', $class1, $class2, false],
			['p1', $class2, null, false],
			['p1', $class2, $stdclass, false],
			['p1', $class2, $class1, false],
			['p1', $class2, $class2, false],
			['p2', $class1, null, false],
			['p2', $class1, $stdclass, false],
			['p2', $class1, $class1, false],
			['p2', $class1, $class2, false],
			['p2', $class2, null, false],
			['p2', $class2, $stdclass, false],
			['p2', $class2, $class1, false],
			['p2', $class2, $class2, false],
			['p3', $class1, null, false],
			['p3', $class1, $stdclass, false],
			['p3', $class1, $class1, false],
			['p3', $class1, $class2, false],
			['p3', $class2, null, false],
			['p3', $class2, $stdclass, false],
			['p3', $class2, $class1, false],
			['p3', $class2, $class2, false],
			['p4', $class1, null, false],
			['p4', $class1, $stdclass, false],
			['p4', $class1, $class1, false],
			['p4', $class1, $class2, false],
			['p4', $class2, null, false],
			['p4', $class2, $stdclass, false],
			['p4', $class2, $class1, false],
			['p4', $class2, $class2, false],
			['p5', $class1, null, false],
			['p5', $class1, $stdclass, false],
			['p5', $class1, $class1, false],
			['p5', $class1, $class2, false],
			['p5', $class2, null, false],
			['p5', $class2, $stdclass, false],
			['p5', $class2, $class1, false],
			['p5', $class2, $class2, false],
			['p6', $class1, null, false],
			['p6', $class1, $stdclass, false],
			['p6', $class1, $class1, false],
			['p6', $class1, $class2, false],
			['p6', $class2, null, false],
			['p6', $class2, $stdclass, false],
			['p6', $class2, $class1, false],
			['p6', $class2, $class2, false],
			['p7', $class1, null, false],
			['p7', $class1, $stdclass, false],
			['p7', $class1, $class1, false],
			['p7', $class1, $class2, false],
			['p7', $class2, null, false],
			['p7', $class2, $stdclass, false],
			['p7', $class2, $class1, false],
			['p7', $class2, $class2, false],
			['p8', $class1, null, true],
			['p8', $class1, $stdclass, true],
			['p8', $class1, $class1, true],
			['p8', $class1, $class2, true],
			['p8', $class2, null, true],
			['p8', $class2, $stdclass, true],
			['p8', $class2, $class1, true],
			['p8', $class2, $class2, true],
			['p9', $class1, null, false],
			['p9', $class1, $stdclass, false],
			['p9', $class1, $class1, true],
			['p9', $class1, $class2, true],
			['p9', $class2, null, false],
			['p9', $class2, $stdclass, false],
			['p9', $class2, $class1, true],
			['p9', $class2, $class2, true],
			['p10', $class1, null, false],
			['p10', $class1, $stdclass, false],
			['p10', $class1, $class1, false],
			['p10', $class1, $class2, false],
			['p10', $class2, null, false],
			['p10', $class2, $stdclass, false],
			['p10', $class2, $class1, false],
			['p10', $class2, $class2, false],
			['p11', $class1, null, true],
			['p11', $class1, $stdclass, true],
			['p11', $class1, $class1, true],
			['p11', $class1, $class2, true],
			['p11', $class2, null, true],
			['p11', $class2, $stdclass, true],
			['p11', $class2, $class1, true],
			['p11', $class2, $class2, true],
			['p12', $class1, null, true],
			['p12', $class1, $stdclass, true],
			['p12', $class1, $class1, true],
			['p12', $class1, $class2, true],
			['p12', $class2, null, true],
			['p12', $class2, $stdclass, true],
			['p12', $class2, $class1, true],
			['p12', $class2, $class2, true],
			['p13', $class1, null, false],
			['p13', $class1, $stdclass, false],
			['p13', $class1, $class1, true],
			['p13', $class1, $class2, true],
			['p13', $class2, null, false],
			['p13', $class2, $stdclass, false],
			['p13', $class2, $class1, true],
			['p13', $class2, $class2, true],
			['p14', $class1, null, false],
			['p14', $class1, $stdclass, false],
			['p14', $class1, $class1, true],
			['p14', $class1, $class2, true],
			['p14', $class2, null, false],
			['p14', $class2, $stdclass, false],
			['p14', $class2, $class1, true],
			['p14', $class2, $class2, true],
			['p15', $class1, null, true],
			['p15', $class1, $stdclass, true],
			['p15', $class1, $class1, true],
			['p15', $class1, $class2, true],
			['p15', $class2, null, true],
			['p15', $class2, $stdclass, true],
			['p15', $class2, $class1, true],
			['p15', $class2, $class2, true],
			['p16', $class1, null, true],
			['p16', $class1, $stdclass, true],
			['p16', $class1, $class1, true],
			['p16', $class1, $class2, true],
			['p16', $class2, null, true],
			['p16', $class2, $stdclass, true],
			['p16', $class2, $class1, true],
			['p16', $class2, $class2, true],
			['p17', $class1, null, false],
			['p17', $class1, $stdclass, false],
			['p17', $class1, $class1, true],
			['p17', $class1, $class2, false],
			['p17', $class2, null, false],
			['p17', $class2, $stdclass, false],
			['p17', $class2, $class1, true],
			['p17', $class2, $class2, false],
			['p18', $class1, null, false],
			['p18', $class1, $stdclass, false],
			['p18', $class1, $class1, true],
			['p18', $class1, $class2, false],
			['p18', $class2, null, false],
			['p18', $class2, $stdclass, false],
			['p18', $class2, $class1, true],
			['p18', $class2, $class2, false],
			['p19', $class1, null, true],
			['p19', $class1, $stdclass, true],
			['p19', $class1, $class1, true],
			['p19', $class1, $class2, true],
			['p19', $class2, null, true],
			['p19', $class2, $stdclass, true],
			['p19', $class2, $class1, true],
			['p19', $class2, $class2, true],
			['p20', $class1, null, true],
			['p20', $class1, $stdclass, true],
			['p20', $class1, $class1, true],
			['p20', $class1, $class2, true],
			['p20', $class2, null, true],
			['p20', $class2, $stdclass, true],
			['p20', $class2, $class1, true],
			['p20', $class2, $class2, true],
			['p21', $class1, null, false],
			['p21', $class1, $stdclass, false],
			['p21', $class1, $class1, false],
			['p21', $class1, $class2, false],
			['p21', $class2, null, false],
			['p21', $class2, $stdclass, false],
			['p21', $class2, $class1, false],
			['p21', $class2, $class2, false],
			['c1p0', $class1, null, true],
			['c1p0', $class1, $stdclass, true],
			['c1p0', $class1, $class1, true],
			['c1p0', $class1, $class2, true],
			['c1p0', $class2, null, true],
			['c1p0', $class2, $stdclass, true],
			['c1p0', $class2, $class1, true],
			['c1p0', $class2, $class2, true],
			['c1p1', $class1, null, false],
			['c1p1', $class1, $stdclass, false],
			['c1p1', $class1, $class1, true],
			['c1p1', $class1, $class2, false],
			['c1p1', $class2, null, false],
			['c1p1', $class2, $stdclass, false],
			['c1p1', $class2, $class1, true],
			['c1p1', $class2, $class2, false],
			['c2p0', $class1, null, false],
			['c2p0', $class1, $stdclass, false],
			['c2p0', $class1, $class1, false],
			['c2p0', $class1, $class2, false],
			['c2p0', $class2, null, false],
			['c2p0', $class2, $stdclass, false],
			['c2p0', $class2, $class1, false],
			['c2p0', $class2, $class2, true],
			['c2p1', $class1, null, false],
			['c2p1', $class1, $stdclass, false],
			['c2p1', $class1, $class1, false],
			['c2p1', $class1, $class2, false],
			['c2p1', $class2, null, false],
			['c2p1', $class2, $stdclass, false],
			['c2p1', $class2, $class1, false],
			['c2p1', $class2, $class2, true],
			['c2p2', $class1, null, false],
			['c2p2', $class1, $stdclass, false],
			['c2p2', $class1, $class1, false],
			['c2p2', $class1, $class2, false],
			['c2p2', $class2, null, true],
			['c2p2', $class2, $stdclass, true],
			['c2p2', $class2, $class1, true],
			['c2p2', $class2, $class2, true],
			['c2p3', $class1, null, false],
			['c2p3', $class1, $stdclass, false],
			['c2p3', $class1, $class1, false],
			['c2p3', $class1, $class2, false],
			['c2p3', $class2, null, true],
			['c2p3', $class2, $stdclass, true],
			['c2p3', $class2, $class1, true],
			['c2p3', $class2, $class2, true],
			['c2p4', $class1, null, false],
			['c2p4', $class1, $stdclass, false],
			['c2p4', $class1, $class1, false],
			['c2p4', $class1, $class2, false],
			['c2p4', $class2, null, false],
			['c2p4', $class2, $stdclass, false],
			['c2p4', $class2, $class1, false],
			['c2p4', $class2, $class2, true],
			['c2p5', $class1, null, false],
			['c2p5', $class1, $stdclass, false],
			['c2p5', $class1, $class1, false],
			['c2p5', $class1, $class2, false],
			['c2p5', $class2, null, false],
			['c2p5', $class2, $stdclass, false],
			['c2p5', $class2, $class1, false],
			['c2p5', $class2, $class2, false]
		];
	}
	
	/**
	 * Test `get` method.
	 * 
	 * @testdox Get
	 * @dataProvider provideGetData
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string|null $scope_class
	 * The scope class to test with.
	 * 
	 * @param mixed $expected
	 * The expected returning value.
	 * 
	 * @return void
	 */
	public function testGet(string $name, string $class, ?string $scope_class, mixed $expected): void
	{
		//values
		$values = match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', 2]
		} + [
			'p1' => 456,
			'p10' => '__T__',
			'p21' => '7k',
			'p23' => '930'
		];
		
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize($values, $class);
		
		//assert
		$this->assertSame($expected, $manager->get($name, $scope_class));
	}
	
	/**
	 * Provide `get` method data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideGetData(): array
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			['p0', $class1, null, 123],
			['p0', $class1, $stdclass, 123],
			['p0', $class1, $class1, 123],
			['p0', $class1, $class2, 123],
			['p0', $class2, null, 123],
			['p0', $class2, $stdclass, 123],
			['p0', $class2, $class1, 123],
			['p0', $class2, $class2, 123],
			['p1', $class1, $class1, 456],
			['p1', $class1, $class2, 456],
			['p1', $class2, $class1, 456],
			['p1', $class2, $class2, 456],
			['p6', $class1, null, null],
			['p6', $class1, $stdclass, null],
			['p6', $class1, $class1, null],
			['p6', $class1, $class2, null],
			['p6', $class2, null, null],
			['p6', $class2, $stdclass, null],
			['p6', $class2, $class1, null],
			['p6', $class2, $class2, null],
			['p7', $class1, $class1, null],
			['p7', $class1, $class2, null],
			['p7', $class2, $class1, null],
			['p7', $class2, $class2, null],
			['p8', $class1, null, 0],
			['p8', $class1, $stdclass, 0],
			['p8', $class1, $class1, 0],
			['p8', $class1, $class2, 0],
			['p8', $class2, null, 0],
			['p8', $class2, $stdclass, 0],
			['p8', $class2, $class1, 0],
			['p8', $class2, $class2, 0],
			['p9', $class1, $class1, 1.0],
			['p9', $class1, $class2, 1.0],
			['p9', $class2, $class1, 1.0],
			['p9', $class2, $class2, 1.0],
			['p10', $class1, null, '__T__'],
			['p10', $class1, $stdclass, '__T__'],
			['p10', $class1, $class1, '__T__'],
			['p10', $class1, $class2, '__T__'],
			['p10', $class2, null, '__T__'],
			['p10', $class2, $stdclass, '__T__'],
			['p10', $class2, $class1, '__T__'],
			['p10', $class2, $class2, '__T__'],
			['p11', $class1, null, 1],
			['p11', $class1, $stdclass, 1],
			['p11', $class1, $class1, 1],
			['p11', $class1, $class2, 1],
			['p11', $class2, null, 1],
			['p11', $class2, $stdclass, 1],
			['p11', $class2, $class1, 1],
			['p11', $class2, $class2, 1],
			['p12', $class1, null, 1],
			['p12', $class1, $stdclass, 1],
			['p12', $class1, $class1, 1],
			['p12', $class1, $class2, 1],
			['p12', $class2, null, 1],
			['p12', $class2, $stdclass, 1],
			['p12', $class2, $class1, 1],
			['p12', $class2, $class2, 1],
			['p13', $class1, $class1, 1],
			['p13', $class1, $class2, 1],
			['p13', $class2, $class1, 1],
			['p13', $class2, $class2, 1],
			['p14', $class1, $class1, 1],
			['p14', $class1, $class2, 1],
			['p14', $class2, $class1, 1],
			['p14', $class2, $class2, 1],
			['p15', $class1, null, 1],
			['p15', $class1, $stdclass, 1],
			['p15', $class1, $class1, 1],
			['p15', $class1, $class2, 1],
			['p15', $class2, null, 1],
			['p15', $class2, $stdclass, 1],
			['p15', $class2, $class1, 1],
			['p15', $class2, $class2, 1],
			['p16', $class1, null, 1],
			['p16', $class1, $stdclass, 1],
			['p16', $class1, $class1, 1],
			['p16', $class1, $class2, 1],
			['p16', $class2, null, 1],
			['p16', $class2, $stdclass, 1],
			['p16', $class2, $class1, 1],
			['p16', $class2, $class2, 1],
			['p17', $class1, $class1, 1],
			['p17', $class2, $class1, 1],
			['p18', $class1, $class1, 1],
			['p18', $class2, $class1, 1],
			['p19', $class1, null, 1200],
			['p19', $class1, $stdclass, 1200],
			['p19', $class1, $class1, 1200],
			['p19', $class1, $class2, 1200],
			['p19', $class2, null, 1200],
			['p19', $class2, $stdclass, 1200],
			['p19', $class2, $class1, 1200],
			['p19', $class2, $class2, 1200],
			['p20', $class1, null, '420'],
			['p20', $class1, $stdclass, '420'],
			['p20', $class1, $class1, '420'],
			['p20', $class1, $class2, '420'],
			['p20', $class2, null, '420'],
			['p20', $class2, $stdclass, '420'],
			['p20', $class2, $class1, '420'],
			['p20', $class2, $class2, '420'],
			['p21', $class1, null, 7000],
			['p21', $class1, $stdclass, 7000],
			['p21', $class1, $class1, 7000],
			['p21', $class1, $class2, 7000],
			['p21', $class2, null, 7000],
			['p21', $class2, $stdclass, 7000],
			['p21', $class2, $class1, 7000],
			['p21', $class2, $class2, 7000],
			['p23', $class1, null, 930],
			['p23', $class1, $stdclass, 930],
			['p23', $class1, $class1, 930],
			['p23', $class1, $class2, 930],
			['p23', $class2, null, 930],
			['p23', $class2, $stdclass, 930],
			['p23', $class2, $class1, 930],
			['p23', $class2, $class2, 930],
			['c1p0', $class1, null, 'foo'],
			['c1p0', $class1, $stdclass, 'foo'],
			['c1p0', $class1, $class1, 'foo'],
			['c1p0', $class1, $class2, 'foo'],
			['c1p0', $class2, null, 'foo'],
			['c1p0', $class2, $stdclass, 'foo'],
			['c1p0', $class2, $class1, 'foo'],
			['c1p0', $class2, $class2, 'foo'],
			['c1p1', $class1, $class1, ''],
			['c1p1', $class2, $class1, ''],
			['c2p0', $class2, $class2, 4350000],
			['c2p1', $class2, $class2, false],
			['c2p2', $class2, null, 75.5],
			['c2p2', $class2, $stdclass, 75.5],
			['c2p2', $class2, $class1, 75.5],
			['c2p2', $class2, $class2, 75.5],
			['c2p3', $class2, null, '2'],
			['c2p3', $class2, $stdclass, '2'],
			['c2p3', $class2, $class1, '2'],
			['c2p3', $class2, $class2, '2'],
			['c2p4', $class2, $class2, []]
		];
	}
	
	/**
	 * Test `get` method expecting an `Undefined` exception to be thrown.
	 * 
	 * @testdox Get Undefined exception
	 * @dataProvider provideGetData_UndefinedException
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @return void
	 */
	public function testGet_UndefinedException(string $name, string $class): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize(match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', 2]
		});
		
		//exception
		$this->expectException(UndefinedException::class);
		try {
			$manager->get($name);
		} catch (UndefinedException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame([$name], $exception->names);
			throw $exception;
		}
	}
	
	/**
	 * Provide `get` method data for an `Undefined` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideGetData_UndefinedException(): array
	{
		//initialize
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			['p', $class1],
			['p2', $class1],
			['p3', $class1],
			['p4', $class1],
			['p5', $class1],
			['c2p0', $class1],
			['p', $class2],
			['p2', $class2],
			['p3', $class2],
			['p4', $class2],
			['p5', $class2],
			['c2p5', $class2]
		];
	}
	
	/**
	 * Test `get` method expecting an `Inaccessible` exception to be thrown.
	 * 
	 * @testdox Get Inaccessible exception
	 * @dataProvider provideGetData_InaccessibleException
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string|null $scope_class
	 * The scope class to test with.
	 * 
	 * @return void
	 */
	public function testGet_InaccessibleException(string $name, string $class, ?string $scope_class): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize(match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', 2]
		});
		
		//exception
		$this->expectException(InaccessibleException::class);
		try {
			$manager->get($name, $scope_class);
		} catch (InaccessibleException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame([$name], $exception->names);
			$this->assertSame($scope_class, $exception->scope_class);
			throw $exception;
		}
	}
	
	/**
	 * Provide `get` method data for an `Inaccessible` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideGetData_InaccessibleException(): array
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			['p1', $class1, null],
			['p1', $class1, $stdclass],
			['p1', $class2, null],
			['p1', $class2, $stdclass],
			['p7', $class1, null],
			['p7', $class1, $stdclass],
			['p7', $class2, null],
			['p7', $class2, $stdclass],
			['p9', $class1, null],
			['p9', $class1, $stdclass],
			['p9', $class2, null],
			['p9', $class2, $stdclass],
			['c2p1', $class2, null],
			['c2p1', $class2, $stdclass]
		];
	}
	
	/**
	 * Test `get` method expecting an `Unreadable` exception to be thrown.
	 * 
	 * @testdox Get Unreadable exception
	 * @dataProvider provideGetData_UnreadableException
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string|null $scope_class
	 * The scope class to test with.
	 * 
	 * @return void
	 */
	public function testGet_UnreadableException(string $name, string $class, ?string $scope_class): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize(match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', 2]
		});
		
		//exception
		$this->expectException(UnreadableException::class);
		try {
			$manager->get($name, $scope_class);
		} catch (UnreadableException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame([$name], $exception->names);
			$this->assertSame($scope_class, $exception->scope_class);
			throw $exception;
		}
	}
	
	/**
	 * Provide `get` method data for an `Unreadable` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideGetData_UnreadableException(): array
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			['p13', $class1, null],
			['p13', $class1, $stdclass],
			['p13', $class2, null],
			['p13', $class2, $stdclass],
			['p14', $class1, null],
			['p14', $class1, $stdclass],
			['p14', $class2, null],
			['p14', $class2, $stdclass],
			['p17', $class1, null],
			['p17', $class1, $stdclass],
			['p17', $class1, $class2],
			['p17', $class2, null],
			['p17', $class2, $stdclass],
			['p17', $class2, $class2],
			['p18', $class1, null],
			['p18', $class1, $stdclass],
			['p18', $class1, $class2],
			['p18', $class2, null],
			['p18', $class2, $stdclass],
			['p18', $class2, $class2],
			['c1p1', $class1, $class2],
			['c1p1', $class2, $class2],
			['c2p0', $class2, null],
			['c2p0', $class2, $stdclass],
			['c2p0', $class2, $class1]
		];
	}
	
	/**
	 * Test `get` method expecting an `Uninitialized` exception to be thrown.
	 * 
	 * @testdox Get Uninitialized exception
	 * @dataProvider provideGetData_UninitializedException
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @return void
	 */
	public function testGet_UninitializedException(string $name, string $class): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize(match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', 2]
		});
		
		//exception
		$this->expectException(UninitializedException::class);
		try {
			$manager->get($name, $class);
		} catch (UninitializedException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame([$name], $exception->names);
			throw $exception;
		}
	}
	
	/**
	 * Provide `get` method data for an `Uninitialized` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideGetData_UninitializedException(): array
	{
		//initialize
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			['p1', $class1],
			['p1', $class2],
			['p10', $class1],
			['p10', $class2]
		];
	}
	
	/**
	 * Test `get` method expecting an `Invalid` exception to be thrown.
	 * 
	 * @testdox Get Invalid exception
	 * @dataProvider provideGetData_InvalidException
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @return void
	 */
	public function testGet_InvalidException(string $name, string $class): void
	{
		//values
		$values = match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', []]
		} + [
			'p23' => 'foo',
			'c1p1' => new stdClass()
		];
		
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize($values, $class);
		
		//exception
		$this->expectException(InvalidException::class);
		try {
			$manager->get($name, $class);
		} catch (InvalidException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame([$name], array_keys($exception->values));
			$this->assertSame([$name], array_keys($exception->errors));
			throw $exception;
		}
	}
	
	/**
	 * Provide `get` method data for an `Invalid` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideGetData_InvalidException(): array
	{
		//initialize
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			['p23', $class1],
			['p23', $class2],
			['c1p1', $class1],
			['c2p3', $class2]
		];
	}
	
	/**
	 * Test `mget` method.
	 * 
	 * @testdox Mget
	 * @dataProvider provideMgetData
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string|null $scope_class
	 * The scope class to test with.
	 * 
	 * @param string[]|null $names
	 * The names to test with.
	 * 
	 * @param array $expected
	 * The expected returning values.
	 * 
	 * @return void
	 */
	public function testMget(string $class, ?string $scope_class, ?array $names, array $expected): void
	{
		//values
		$values = match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', 2]
		} + [
			'p1' => 456,
			'p10' => '__T__',
			'p21' => '7k',
			'p23' => '930'
		];
		
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize($values, $class);
		
		//assert
		$this->assertSame($expected, $manager->mget($names, $scope_class));
	}
	
	/**
	 * Provide `mget` method data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideMgetData(): array
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			[$class1, null, null, [
				'p0' => 123,
				'p6' => null,
				'p8' => 0,
				'p10' => '__T__',
				'p11' => 1,
				'p12' => 1,
				'p15' => 1,
				'p16' => 1,
				'p19' => 1200,
				'p20' => '420',
				'p21' => 7000,
				'p22' => '100',
				'p23' => 930,
				'c1p0' => 'foo'
			]],
			[$class1, $stdclass, null, [
				'p0' => 123,
				'p6' => null,
				'p8' => 0,
				'p10' => '__T__',
				'p11' => 1,
				'p12' => 1,
				'p15' => 1,
				'p16' => 1,
				'p19' => 1200,
				'p20' => '420',
				'p21' => 7000,
				'p22' => '100',
				'p23' => 930,
				'c1p0' => 'foo'
			]],
			[$class1, $class1, null, [
				'p0' => 123,
				'p1' => 456,
				'p6' => null,
				'p7' => null,
				'p8' => 0,
				'p9' => 1.0,
				'p10' => '__T__',
				'p11' => 1,
				'p12' => 1,
				'p13' => 1,
				'p14' => 1,
				'p15' => 1,
				'p16' => 1,
				'p17' => 1,
				'p18' => 1,
				'p19' => 1200,
				'p20' => '420',
				'p21' => 7000,
				'p22' => '100',
				'p23' => 930,
				'c1p0' => 'foo',
				'c1p1' => ''
			]],
			[$class1, $class2, null, [
				'p0' => 123,
				'p1' => 456,
				'p6' => null,
				'p7' => null,
				'p8' => 0,
				'p9' => 1.0,
				'p10' => '__T__',
				'p11' => 1,
				'p12' => 1,
				'p13' => 1,
				'p14' => 1,
				'p15' => 1,
				'p16' => 1,
				'p19' => 1200,
				'p20' => '420',
				'p21' => 7000,
				'p22' => '100',
				'p23' => 930,
				'c1p0' => 'foo'
			]],
			[$class2, null, null, [
				'p0' => 123,
				'c2p3' => '2',
				'p6' => null,
				'p8' => 0,
				'p10' => '__T__',
				'p11' => 1,
				'p12' => 1,
				'p15' => 1,
				'p16' => 1,
				'p19' => 1200,
				'p20' => '420',
				'p21' => 7000,
				'p22' => '100',
				'p23' => 930,
				'c1p0' => 'foo',
				'c2p2' => 75.5
			]],
			[$class2, $stdclass, null, [
				'p0' => 123,
				'c2p3' => '2',
				'p6' => null,
				'p8' => 0,
				'p10' => '__T__',
				'p11' => 1,
				'p12' => 1,
				'p15' => 1,
				'p16' => 1,
				'p19' => 1200,
				'p20' => '420',
				'p21' => 7000,
				'p22' => '100',
				'p23' => 930,
				'c1p0' => 'foo',
				'c2p2' => 75.5
			]],
			[$class2, $class1, null, [
				'p0' => 123,
				'c2p3' => '2',
				'p1' => 456,
				'p6' => null,
				'p7' => null,
				'p8' => 0,
				'p9' => 1.0,
				'p10' => '__T__',
				'p11' => 1,
				'p12' => 1,
				'p13' => 1,
				'p14' => 1,
				'p15' => 1,
				'p16' => 1,
				'p17' => 1,
				'p18' => 1,
				'p19' => 1200,
				'p20' => '420',
				'p21' => 7000,
				'p22' => '100',
				'p23' => 930,
				'c1p0' => 'foo',
				'c1p1' => '',
				'c2p2' => 75.5
			]],
			[$class2, $class2, null, [
				'p0' => 123,
				'c2p0' => 4350000,
				'c2p3' => '2',
				'p1' => 456,
				'p6' => null,
				'p7' => null,
				'p8' => 0,
				'p9' => 1.0,
				'p10' => '__T__',
				'p11' => 1,
				'p12' => 1,
				'p13' => 1,
				'p14' => 1,
				'p15' => 1,
				'p16' => 1,
				'p19' => 1200,
				'p20' => '420',
				'p21' => 7000,
				'p22' => '100',
				'p23' => 930,
				'c1p0' => 'foo',
				'c2p1' => false,
				'c2p2' => 75.5,
				'c2p4' => []
			]],
			[$class1, null, ['p6', 'p10', 'c1p0', 'p8', 'p21', 'p0'], [
				'p6' => null,
				'p10' => '__T__',
				'c1p0' => 'foo',
				'p8' => 0,
				'p21' => 7000,
				'p0' => 123
			]],
			[$class1, $stdclass, ['p6', 'p10', 'c1p0', 'p8', 'p21', 'p0'], [
				'p6' => null,
				'p10' => '__T__',
				'c1p0' => 'foo',
				'p8' => 0,
				'p21' => 7000,
				'p0' => 123
			]],
			[$class1, $class1, ['p6', 'p18', 'p10', 'c1p0', 'p1', 'p8', 'p21', 'c1p1', 'p0'], [
				'p6' => null,
				'p18' => 1,
				'p10' => '__T__',
				'c1p0' => 'foo',
				'p1' => 456,
				'p8' => 0,
				'p21' => 7000,
				'c1p1' => '',
				'p0' => 123
			]],
			[$class1, $class2, ['p6', 'p14', 'p10', 'c1p0', 'p1', 'p8', 'p21', 'p0'], [
				'p6' => null,
				'p14' => 1,
				'p10' => '__T__',
				'c1p0' => 'foo',
				'p1' => 456,
				'p8' => 0,
				'p21' => 7000,
				'p0' => 123
			]],
			[$class2, null, ['c2p2', 'p6', 'p10', 'c1p0', 'c2p3', 'p8', 'p21', 'p0'], [
				'c2p2' => 75.5,
				'p6' => null,
				'p10' => '__T__',
				'c1p0' => 'foo',
				'c2p3' => '2',
				'p8' => 0,
				'p21' => 7000,
				'p0' => 123
			]],
			[$class2, $stdclass, ['c2p2', 'p6', 'p10', 'c1p0', 'c2p3', 'p8', 'p21', 'p0'], [
				'c2p2' => 75.5,
				'p6' => null,
				'p10' => '__T__',
				'c1p0' => 'foo',
				'c2p3' => '2',
				'p8' => 0,
				'p21' => 7000,
				'p0' => 123
			]],
			[$class2, $class1, ['c2p2', 'p6', 'p18', 'p10', 'c1p0', 'c2p3', 'p1', 'p8', 'p21', 'c1p1', 'p0'], [
				'c2p2' => 75.5,
				'p6' => null,
				'p18' => 1,
				'p10' => '__T__',
				'c1p0' => 'foo',
				'c2p3' => '2',
				'p1' => 456,
				'p8' => 0,
				'p21' => 7000,
				'c1p1' => '',
				'p0' => 123
			]],
			[$class2, $class2, ['c2p2', 'p6', 'p14', 'c2p4', 'p10', 'c1p0', 'c2p3', 'p1', 'p8', 'p21', 'p0'], [
				'c2p2' => 75.5,
				'p6' => null,
				'p14' => 1,
				'c2p4' => [],
				'p10' => '__T__',
				'c1p0' => 'foo',
				'c2p3' => '2',
				'p1' => 456,
				'p8' => 0,
				'p21' => 7000,
				'p0' => 123
			]],
			[
				$class1, null, [
					'c1p0', 'p23', 'p22', 'p21', 'p20', 'p19', 'p16', 'p15', 'p12', 'p11', 'p10', 'p8', 'p6', 'p0'
				], [
					'c1p0' => 'foo',
					'p23' => 930,
					'p22' => '100',
					'p21' => 7000,
					'p20' => '420',
					'p19' => 1200,
					'p16' => 1,
					'p15' => 1,
					'p12' => 1,
					'p11' => 1,
					'p10' => '__T__',
					'p8' => 0,
					'p6' => null,
					'p0' => 123
				]
			],
			[
				$class1, $stdclass, [
					'c1p0', 'p23', 'p22', 'p21', 'p20', 'p19', 'p16', 'p15', 'p12', 'p11', 'p10', 'p8', 'p6', 'p0'
				], [
					'c1p0' => 'foo',
					'p23' => 930,
					'p22' => '100',
					'p21' => 7000,
					'p20' => '420',
					'p19' => 1200,
					'p16' => 1,
					'p15' => 1,
					'p12' => 1,
					'p11' => 1,
					'p10' => '__T__',
					'p8' => 0,
					'p6' => null,
					'p0' => 123
				]
			],
			[
				$class1, $class1, [
					'c1p1', 'c1p0', 'p23', 'p22', 'p21', 'p20', 'p19', 'p18', 'p17', 'p16', 'p15', 'p14', 'p13', 'p12',
					'p11', 'p10', 'p9', 'p8', 'p7', 'p6', 'p1', 'p0'
				], [
					'c1p1' => '',
					'c1p0' => 'foo',
					'p23' => 930,
					'p22' => '100',
					'p21' => 7000,
					'p20' => '420',
					'p19' => 1200,
					'p18' => 1,
					'p17' => 1,
					'p16' => 1,
					'p15' => 1,
					'p14' => 1,
					'p13' => 1,
					'p12' => 1,
					'p11' => 1,
					'p10' => '__T__',
					'p9' => 1.0,
					'p8' => 0,
					'p7' => null,
					'p6' => null,
					'p1' => 456,
					'p0' => 123
				]
			],
			[
				$class1, $class2, [
					'c1p0', 'p23', 'p22', 'p21', 'p20', 'p19', 'p16', 'p15', 'p14', 'p13', 'p12', 'p11', 'p10', 'p9',
					'p8', 'p7', 'p6', 'p1', 'p0'
				], [
					'c1p0' => 'foo',
					'p23' => 930,
					'p22' => '100',
					'p21' => 7000,
					'p20' => '420',
					'p19' => 1200,
					'p16' => 1,
					'p15' => 1,
					'p14' => 1,
					'p13' => 1,
					'p12' => 1,
					'p11' => 1,
					'p10' => '__T__',
					'p9' => 1.0,
					'p8' => 0,
					'p7' => null,
					'p6' => null,
					'p1' => 456,
					'p0' => 123
				]
			],
			[
				$class2, null, [
					'c2p2', 'c1p0', 'p23', 'p22', 'p21', 'p20', 'p19', 'p16', 'p15', 'p12', 'p11', 'p10', 'p8', 'p6',
					'c2p3', 'p0'
				], [
					'c2p2' => 75.5,
					'c1p0' => 'foo',
					'p23' => 930,
					'p22' => '100',
					'p21' => 7000,
					'p20' => '420',
					'p19' => 1200,
					'p16' => 1,
					'p15' => 1,
					'p12' => 1,
					'p11' => 1,
					'p10' => '__T__',
					'p8' => 0,
					'p6' => null,
					'c2p3' => '2',
					'p0' => 123
				]
			],
			[
				$class2, $stdclass, [
					'c2p2', 'c1p0', 'p23', 'p22', 'p21', 'p20', 'p19', 'p16', 'p15', 'p12', 'p11', 'p10', 'p8', 'p6',
					'c2p3', 'p0'
				], [
					'c2p2' => 75.5,
					'c1p0' => 'foo',
					'p23' => 930,
					'p22' => '100',
					'p21' => 7000,
					'p20' => '420',
					'p19' => 1200,
					'p16' => 1,
					'p15' => 1,
					'p12' => 1,
					'p11' => 1,
					'p10' => '__T__',
					'p8' => 0,
					'p6' => null,
					'c2p3' => '2',
					'p0' => 123
				]
			],
			[
				$class2, $class1, [
					'c2p2', 'c1p1', 'c1p0', 'p23', 'p22', 'p21', 'p20', 'p19', 'p18', 'p17', 'p16', 'p15', 'p14', 'p13',
					'p12', 'p11', 'p10', 'p9', 'p8', 'p7', 'p6', 'p1', 'c2p3', 'p0'
				], [
					'c2p2' => 75.5,
					'c1p1' => '',
					'c1p0' => 'foo',
					'p23' => 930,
					'p22' => '100',
					'p21' => 7000,
					'p20' => '420',
					'p19' => 1200,
					'p18' => 1,
					'p17' => 1,
					'p16' => 1,
					'p15' => 1,
					'p14' => 1,
					'p13' => 1,
					'p12' => 1,
					'p11' => 1,
					'p10' => '__T__',
					'p9' => 1.0,
					'p8' => 0,
					'p7' => null,
					'p6' => null,
					'p1' => 456,
					'c2p3' => '2',
					'p0' => 123
				]
			],
			[
				$class2, $class2, [
					'c2p4', 'c2p2', 'c2p1', 'c1p0', 'p23', 'p22', 'p21', 'p20', 'p19', 'p16', 'p15', 'p14', 'p13',
					'p12', 'p11', 'p10', 'p9', 'p8', 'p7', 'p6', 'p1', 'c2p3', 'c2p0', 'p0'
				], [
					'c2p4' => [],
					'c2p2' => 75.5,
					'c2p1' => false,
					'c1p0' => 'foo',
					'p23' => 930,
					'p22' => '100',
					'p21' => 7000,
					'p20' => '420',
					'p19' => 1200,
					'p16' => 1,
					'p15' => 1,
					'p14' => 1,
					'p13' => 1,
					'p12' => 1,
					'p11' => 1,
					'p10' => '__T__',
					'p9' => 1.0,
					'p8' => 0,
					'p7' => null,
					'p6' => null,
					'p1' => 456,
					'c2p3' => '2',
					'c2p0' => 4350000,
					'p0' => 123
				]
			]
		];
	}
	
	/**
	 * Test `mget` method expecting an `Undefined` exception to be thrown.
	 * 
	 * @testdox Mget Undefined exception
	 * @dataProvider provideMgetData_UndefinedException
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string[] $names
	 * The names to test with.
	 * 
	 * @param string[] $expected_names
	 * The expected exception names.
	 * 
	 * @return void
	 */
	public function testMget_UndefinedException(string $class, array $names, array $expected_names): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize(match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', 2]
		});
		
		//exception
		$this->expectException(UndefinedException::class);
		try {
			$manager->mget($names);
		} catch (UndefinedException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame($expected_names, $exception->names);
			throw $exception;
		}
	}
	
	/**
	 * Provide `mget` method data for an `Undefined` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideMgetData_UndefinedException(): array
	{
		//initialize
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			[$class1, ['p'], ['p']],
			[$class1, ['p', 'p0'], ['p']],
			[$class1, ['p2', 'p3'], ['p2', 'p3']],
			[$class1, ['p0', 'p2', 'p6', 'p3'], ['p2', 'p3']],
			[$class1, ['p4', 'p5', 'c2p0'], ['p4', 'p5', 'c2p0']],
			[$class1, ['p0', 'p4', 'p5', 'p6', 'p11', 'c2p0'], ['p4', 'p5', 'c2p0']],
			[$class2, ['p', 'p2', 'p3', 'p4', 'p5', 'c2p5'], ['p', 'p2', 'p3', 'p4', 'p5', 'c2p5']],
			[$class2, ['p', 'p0', 'p2', 'p3', 'p4', 'p5', 'p6', 'c2p2', 'c2p5'], ['p', 'p2', 'p3', 'p4', 'p5', 'c2p5']]
		];
	}
	
	/**
	 * Test `mget` method expecting an `Inaccessible` exception to be thrown.
	 * 
	 * @testdox Mget Inaccessible exception
	 * @dataProvider provideMgetData_InaccessibleException
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string|null $scope_class
	 * The scope class to test with.
	 * 
	 * @param string[] $names
	 * The names to test with.
	 * 
	 * @param string[] $expected_names
	 * The expected exception names.
	 * 
	 * @return void
	 */
	public function testMget_InaccessibleException(
		string $class, ?string $scope_class, array $names, array $expected_names
	): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize(match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', 2]
		});
		
		//exception
		$this->expectException(InaccessibleException::class);
		try {
			$manager->mget($names, $scope_class);
		} catch (InaccessibleException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame($expected_names, $exception->names);
			$this->assertSame($scope_class, $exception->scope_class);
			throw $exception;
		}
	}
	
	/**
	 * Provide `mget` method data for an `Inaccessible` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideMgetData_InaccessibleException(): array
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			[$class1, null, ['p1'], ['p1']],
			[$class1, $stdclass, ['p1'], ['p1']],
			[$class1, null, ['p0', 'p1'], ['p1']],
			[$class1, $stdclass, ['p0', 'p1'], ['p1']],
			[$class1, null, ['p7', 'p9'], ['p7', 'p9']],
			[$class1, $stdclass, ['p7', 'p9'], ['p7', 'p9']],
			[$class1, null, ['p0', 'p7', 'p6', 'p9'], ['p7', 'p9']],
			[$class1, $stdclass, ['p0', 'p7', 'p6', 'p9'], ['p7', 'p9']],
			[$class2, null, ['p1', 'p7', 'p9', 'c2p1'], ['p1', 'p7', 'p9', 'c2p1']],
			[$class2, $stdclass, ['p1', 'p7', 'p9', 'c2p1'], ['p1', 'p7', 'p9', 'c2p1']],
			[$class2, null, ['p1', 'p0', 'p7', 'p6', 'p9', 'c2p1', 'c2p2'], ['p1', 'p7', 'p9', 'c2p1']],
			[$class2, $stdclass, ['p1', 'p0', 'p7', 'p6', 'p9', 'c2p1', 'c2p2'], ['p1', 'p7', 'p9', 'c2p1']]
		];
	}
	
	/**
	 * Test `mget` method expecting an `Unreadable` exception to be thrown.
	 * 
	 * @testdox Mget Unreadable exception
	 * @dataProvider provideMgetData_UnreadableException
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string|null $scope_class
	 * The scope class to test with.
	 * 
	 * @param string[] $names
	 * The names to test with.
	 * 
	 * @param string[] $expected_names
	 * The expected exception names.
	 * 
	 * @return void
	 */
	public function testMget_UnreadableException(
		string $class, ?string $scope_class, array $names, array $expected_names
	): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize(match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', 2]
		});
		
		//exception
		$this->expectException(UnreadableException::class);
		try {
			$manager->mget($names, $scope_class);
		} catch (UnreadableException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame($expected_names, $exception->names);
			$this->assertSame($scope_class, $exception->scope_class);
			throw $exception;
		}
	}
	
	/**
	 * Provide `mget` method data for an `Unreadable` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideMgetData_UnreadableException(): array
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			[$class1, null, ['p13'], ['p13']],
			[$class1, $stdclass, ['p13'], ['p13']],
			[$class1, null, ['p0', 'p13'], ['p13']],
			[$class1, $stdclass, ['p0', 'p13'], ['p13']],
			[$class1, null, ['p14', 'p17', 'p18'], ['p14', 'p17', 'p18']],
			[$class1, $stdclass, ['p14', 'p17', 'p18'], ['p14', 'p17', 'p18']],
			[$class1, null, ['p0', 'p14', 'p17', 'p6', 'p18'], ['p14', 'p17', 'p18']],
			[$class1, $stdclass, ['p0', 'p14', 'p17', 'p6', 'p18'], ['p14', 'p17', 'p18']],
			[$class1, $class2, ['p17', 'p18', 'c1p1'], ['p17', 'p18', 'c1p1']],
			[$class1, $class2, ['p17', 'p0', 'p6', 'p18', 'c1p0', 'c1p1'], ['p17', 'p18', 'c1p1']],
			[$class2, null, ['p13', 'p14', 'p17', 'p18', 'c2p0'], ['p13', 'p14', 'p17', 'p18', 'c2p0']],
			[$class2, $stdclass, ['p13', 'p14', 'p17', 'p18', 'c2p0'], ['p13', 'p14', 'p17', 'p18', 'c2p0']],
			[$class2, null, ['p13', 'p0', 'p14', 'p17', 'p6', 'p18', 'c2p0', 'c2p2'],
				['p13', 'p14', 'p17', 'p18', 'c2p0']],
			[$class2, $stdclass, ['p13', 'p0', 'p14', 'p17', 'p6', 'p18', 'c2p0', 'c2p2'],
				['p13', 'p14', 'p17', 'p18', 'c2p0']],
			[$class2, $class2, ['p17', 'p18', 'c1p1'], ['p17', 'p18', 'c1p1']],
			[$class2, $class2, ['p0', 'p17', 'p18', 'p6', 'c1p1', 'c2p2'], ['p17', 'p18', 'c1p1']],
			[$class2, $class1, ['c2p0'], ['c2p0']],
			[$class2, $class1, ['p0', 'c2p0', 'c2p2'], ['c2p0']]
		];
	}
	
	/**
	 * Test `mget` method expecting an `Uninitialized` exception to be thrown.
	 * 
	 * @testdox Mget Uninitialized exception
	 * @dataProvider provideMgetData_UninitializedException
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string[] $names
	 * The names to test with.
	 * 
	 * @param string[] $expected_names
	 * The expected exception names.
	 * 
	 * @return void
	 */
	public function testMget_UninitializedException(string $class, array $names, array $expected_names): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize(match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', 2]
		});
		
		//exception
		$this->expectException(UninitializedException::class);
		try {
			$manager->mget($names, $class);
		} catch (UninitializedException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame($expected_names, $exception->names);
			throw $exception;
		}
	}
	
	/**
	 * Provide `mget` method data for an `Uninitialized` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideMgetData_UninitializedException(): array
	{
		//initialize
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			[$class1, ['p1'], ['p1']],
			[$class2, ['p1'], ['p1']],
			[$class1, ['p1', 'p0'], ['p1']],
			[$class2, ['p1', 'p0'], ['p1']],
			[$class1, ['p1', 'p10'], ['p1', 'p10']],
			[$class2, ['p1', 'p10'], ['p1', 'p10']],
			[$class1, ['p0', 'p1', 'p6', 'p10'], ['p1', 'p10']],
			[$class2, ['p0', 'p1', 'p6', 'p10', 'c2p2'], ['p1', 'p10']]
		];
	}
	
	/**
	 * Test `mget` method expecting an `Invalid` exception to be thrown.
	 * 
	 * @testdox Mget Invalid exception
	 * @dataProvider provideMgetData_InvalidException
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string[] $names
	 * The names to test with.
	 * 
	 * @param string[] $expected_names
	 * The expected exception names.
	 * 
	 * @return void
	 */
	public function testMget_InvalidException(string $class, array $names, array $expected_names): void
	{
		//values
		$values = match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', []]
		} + [
			'p23' => 'foo',
			'c1p1' => new stdClass()
		];
		
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize($values, $class);
		
		//exception
		$this->expectException(InvalidException::class);
		try {
			$manager->mget($names, $class);
		} catch (InvalidException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame($expected_names, array_keys($exception->values));
			$this->assertSame($expected_names, array_keys($exception->errors));
			throw $exception;
		}
	}
	
	/**
	 * Provide `mget` method data for an `Invalid` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideMgetData_InvalidException(): array
	{
		//initialize
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			[$class1, ['p23'], ['p23']],
			[$class2, ['p23'], ['p23']],
			[$class1, ['p0', 'p23'], ['p23']],
			[$class2, ['p0', 'p23'], ['p23']],
			[$class1, ['p23', 'c1p1'], ['p23', 'c1p1']],
			[$class2, ['p23', 'c2p3'], ['p23', 'c2p3']],
			[$class1, ['p0', 'p23', 'p6', 'c1p1'], ['p23', 'c1p1']],
			[$class2, ['p0', 'p23', 'p6', 'c2p2', 'c2p3'], ['p23', 'c2p3']]
		];
	}
	
	/**
	 * Test `set` method.
	 * 
	 * @testdox Set
	 * @dataProvider provideSetData
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param mixed $expected
	 * The expected value.
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string|null $scope_class
	 * The scope class to test with.
	 * 
	 * @return void
	 */
	public function testSet(string $name, mixed $value, mixed $expected, string $class, ?string $scope_class): void
	{
		//initialize
		Manager::clearCache();
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		$manager = new Manager(new $class());
		$manager->initialize(match ($class) {
			$class1 => [123],
			$class2 => [123, '4.35M', 2]
		});
		
		//assert
		$this->assertSame($manager, $manager->set($name, $value, $scope_class));
		$this->assertSame($expected, ($manager->mget(null, $class1) + $manager->mget(null, $class2))[$name]);
	}
	
	/**
	 * Provide `set` method data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideSetData(): array
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			['p0', 321, 321, $class1, null],
			['p0', 321, 321, $class1, $stdclass],
			['p0', 321, 321, $class1, $class1],
			['p0', 321, 321, $class1, $class2],
			['p0', 321, 321, $class2, null],
			['p0', 321, 321, $class2, $stdclass],
			['p0', 321, 321, $class2, $class1],
			['p0', 321, 321, $class2, $class2],
			['p1', 456, 456, $class1, $class1],
			['p1', 456, 456, $class1, $class2],
			['p1', 456, 456, $class2, $class1],
			['p1', 456, 456, $class2, $class2],
			['p6', true, true, $class1, null],
			['p6', true, true, $class1, $stdclass],
			['p6', true, true, $class1, $class1],
			['p6', true, true, $class1, $class2],
			['p6', true, true, $class2, null],
			['p6', true, true, $class2, $stdclass],
			['p6', true, true, $class2, $class1],
			['p6', true, true, $class2, $class2],
			['p7', [], [], $class1, $class1],
			['p7', [], [], $class1, $class2],
			['p7', [], [], $class2, $class1],
			['p7', [], [], $class2, $class2],
			['p8', '-98', -98, $class1, null],
			['p8', '-98', -98, $class1, $stdclass],
			['p8', '-98', -98, $class1, $class1],
			['p8', '-98', -98, $class1, $class2],
			['p8', '-98', -98, $class2, null],
			['p8', '-98', -98, $class2, $stdclass],
			['p8', '-98', -98, $class2, $class1],
			['p8', '-98', -98, $class2, $class2],
			['p9', '-2.5', -2.5, $class1, $class1],
			['p9', '-2.5', -2.5, $class1, $class2],
			['p9', '-2.5', -2.5, $class2, $class1],
			['p9', '-2.5', -2.5, $class2, $class2],
			['p10', '__T__', '__T__', $class1, $class1],
			['p10', '__T__', '__T__', $class1, $class2],
			['p10', '__T__', '__T__', $class2, $class1],
			['p10', '__T__', '__T__', $class2, $class2],
			['p11', '__A__', '__A__', $class1, $class1],
			['p11', '__A__', '__A__', $class1, $class2],
			['p11', '__A__', '__A__', $class2, $class1],
			['p11', '__A__', '__A__', $class2, $class2],
			['p12', ['a'], ['a'], $class1, $class1],
			['p12', ['a'], ['a'], $class1, $class2],
			['p12', ['a'], ['a'], $class2, $class1],
			['p12', ['a'], ['a'], $class2, $class2],
			['p13', 333, 333, $class1, null],
			['p13', 333, 333, $class1, $stdclass],
			['p13', 333, 333, $class1, $class1],
			['p13', 333, 333, $class1, $class2],
			['p13', 333, 333, $class2, null],
			['p13', 333, 333, $class2, $stdclass],
			['p13', 333, 333, $class2, $class1],
			['p13', 333, 333, $class2, $class2],
			['p14', 8.75, 8.75, $class1, $class1],
			['p14', 8.75, 8.75, $class1, $class2],
			['p14', 8.75, 8.75, $class2, $class1],
			['p14', 8.75, 8.75, $class2, $class2],
			['p15', 0x0f, 0x0f, $class1, $class1],
			['p15', 0x0f, 0x0f, $class2, $class1],
			['p16', 'FOO', 'FOO', $class1, $class1],
			['p16', 'FOO', 'FOO', $class2, $class1],
			['p17', false, false, $class1, null],
			['p17', false, false, $class1, $stdclass],
			['p17', false, false, $class1, $class1],
			['p17', false, false, $class1, $class2],
			['p17', false, false, $class2, null],
			['p17', false, false, $class2, $stdclass],
			['p17', false, false, $class2, $class1],
			['p17', false, false, $class2, $class2],
			['p18', true, true, $class1, $class1],
			['p18', true, true, $class2, $class1],
			['p19', '-749', -749, $class1, null],
			['p19', '-749', -749, $class1, $stdclass],
			['p19', '-749', -749, $class1, $class1],
			['p19', '-749', -749, $class1, $class2],
			['p19', '-749', -749, $class2, null],
			['p19', '-749', -749, $class2, $stdclass],
			['p19', '-749', -749, $class2, $class1],
			['p19', '-749', -749, $class2, $class2],
			['p20', 'fooBar', 'fooBar', $class1, null],
			['p20', 'fooBar', 'fooBar', $class1, $stdclass],
			['p20', 'fooBar', 'fooBar', $class1, $class1],
			['p20', 'fooBar', 'fooBar', $class1, $class2],
			['p20', 'fooBar', 'fooBar', $class2, null],
			['p20', 'fooBar', 'fooBar', $class2, $stdclass],
			['p20', 'fooBar', 'fooBar', $class2, $class1],
			['p20', 'fooBar', 'fooBar', $class2, $class2],
			['p21', '7k', 7000, $class1, null],
			['p21', '7k', 7000, $class1, $stdclass],
			['p21', '7k', 7000, $class1, $class1],
			['p21', '7k', 7000, $class1, $class2],
			['p21', '7k', 7000, $class2, null],
			['p21', '7k', 7000, $class2, $stdclass],
			['p21', '7k', 7000, $class2, $class1],
			['p21', '7k', 7000, $class2, $class2],
			['p22', '75.80', '75.80', $class1, null],
			['p22', '75.80', '75.80', $class1, $stdclass],
			['p22', '75.80', '75.80', $class1, $class1],
			['p22', '75.80', '75.80', $class1, $class2],
			['p22', '75.80', '75.80', $class2, null],
			['p22', '75.80', '75.80', $class2, $stdclass],
			['p22', '75.80', '75.80', $class2, $class1],
			['p22', '75.80', '75.80', $class2, $class2],
			['p23', '930', 930, $class1, null],
			['p23', '930', 930, $class1, $stdclass],
			['p23', '930', 930, $class1, $class1],
			['p23', '930', 930, $class1, $class2],
			['p23', '930', 930, $class2, null],
			['p23', '930', 930, $class2, $stdclass],
			['p23', '930', 930, $class2, $class1],
			['p23', '930', 930, $class2, $class2],
			['c1p0', 56.72, '56.72', $class1, $class1],
			['c1p0', 56.72, '56.72', $class1, $class2],
			['c1p0', 56.72, '56.72', $class2, $class1],
			['c1p0', 56.72, '56.72', $class2, $class2],
			['c1p1', 234, '234', $class1, $class1],
			['c1p1', 234, '234', $class2, $class1],
			['c2p0', '-4.12k', -4120, $class2, $class2],
			['c2p1', 1, true, $class2, $class2],
			['c2p2', '975', 975, $class2, $class2],
			['c2p3', null, null, $class2, null],
			['c2p3', null, null, $class2, $stdclass],
			['c2p3', null, null, $class2, $class1],
			['c2p3', null, null, $class2, $class2],
			['c2p4', ['foo', 'bar'], ['foo', 'bar'], $class2, $class2]
		];
	}
	
	/**
	 * Test `set` method expecting an `Undefined` exception to be thrown.
	 * 
	 * @testdox Set Undefined exception
	 * @dataProvider provideSetData_UndefinedException
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @return void
	 */
	public function testSet_UndefinedException(string $name, string $class): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize(match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', 2]
		});
		
		//exception
		$this->expectException(UndefinedException::class);
		try {
			$manager->set($name, 1);
		} catch (UndefinedException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame([$name], $exception->names);
			throw $exception;
		}
	}
	
	/**
	 * Provide `set` method data for an `Undefined` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideSetData_UndefinedException(): array
	{
		//initialize
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			['p', $class1],
			['p2', $class1],
			['p3', $class1],
			['p4', $class1],
			['p5', $class1],
			['c2p0', $class1],
			['p', $class2],
			['p2', $class2],
			['p3', $class2],
			['p4', $class2],
			['p5', $class2],
			['c2p5', $class2]
		];
	}
	
	/**
	 * Test `set` method expecting an `Inaccessible` exception to be thrown.
	 * 
	 * @testdox Set Inaccessible exception
	 * @dataProvider provideSetData_InaccessibleException
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string|null $scope_class
	 * The scope class to test with.
	 * 
	 * @return void
	 */
	public function testSet_InaccessibleException(string $name, string $class, ?string $scope_class): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize(match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', 2]
		});
		
		//exception
		$this->expectException(InaccessibleException::class);
		try {
			$manager->set($name, 1, $scope_class);
		} catch (InaccessibleException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame([$name], $exception->names);
			$this->assertSame($scope_class, $exception->scope_class);
			throw $exception;
		}
	}
	
	/**
	 * Provide `set` method data for an `Inaccessible` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideSetData_InaccessibleException(): array
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			['p1', $class1, null],
			['p1', $class1, $stdclass],
			['p1', $class2, null],
			['p1', $class2, $stdclass],
			['p7', $class1, null],
			['p7', $class1, $stdclass],
			['p7', $class2, null],
			['p7', $class2, $stdclass],
			['p9', $class1, null],
			['p9', $class1, $stdclass],
			['p9', $class2, null],
			['p9', $class2, $stdclass],
			['c2p1', $class2, null],
			['c2p1', $class2, $stdclass]
		];
	}
	
	/**
	 * Test `set` method expecting an `Unwriteable` exception to be thrown.
	 * 
	 * @testdox Set Unwriteable exception
	 * @dataProvider provideSetData_UnwriteableException
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @param string|null $scope_class
	 * The scope class to test with.
	 * 
	 * @return void
	 */
	public function testSet_UnwriteableException(string $name, string $class, ?string $scope_class): void
	{
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize(match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', 2]
		});
		
		//exception
		$this->expectException(UnwriteableException::class);
		try {
			$manager->set($name, 1, $scope_class);
		} catch (UnwriteableException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame([$name], $exception->names);
			$this->assertSame($scope_class, $exception->scope_class);
			throw $exception;
		}
	}
	
	/**
	 * Provide `set` method data for an `Unwriteable` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideSetData_UnwriteableException(): array
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		$class3 = PropertiesTest_Class3::class;
		
		//return
		return [
			['p10', $class1, null],
			['p10', $class1, $stdclass],
			['p10', $class2, null],
			['p10', $class2, $stdclass],
			['p11', $class1, null],
			['p11', $class1, $stdclass],
			['p11', $class2, null],
			['p11', $class2, $stdclass],
			['p12', $class1, null],
			['p12', $class1, $stdclass],
			['p12', $class2, null],
			['p12', $class2, $stdclass],
			['p14', $class1, null],
			['p14', $class1, $stdclass],
			['p14', $class2, null],
			['p14', $class2, $stdclass],
			['p15', $class1, null],
			['p15', $class1, $stdclass],
			['p15', $class1, $class2],
			['p15', $class2, null],
			['p15', $class2, $stdclass],
			['p15', $class2, $class2],
			['p16', $class1, null],
			['p16', $class1, $stdclass],
			['p16', $class1, $class2],
			['p16', $class2, null],
			['p16', $class2, $stdclass],
			['p16', $class2, $class2],
			['p18', $class1, null],
			['p18', $class1, $stdclass],
			['p18', $class1, $class2],
			['p18', $class2, null],
			['p18', $class2, $stdclass],
			['p18', $class2, $class2],
			['c1p0', $class1, null],
			['c1p0', $class1, $stdclass],
			['c1p0', $class2, null],
			['c1p0', $class2, $stdclass],
			['c1p1', $class1, $class2],
			['c1p1', $class2, $class2],
			['c2p0', $class2, null],
			['c2p0', $class2, $stdclass],
			['c2p0', $class2, $class1],
			['c2p1', $class2, $class3],
			['c2p2', $class2, null],
			['c2p2', $class2, $stdclass],
			['c2p2', $class2, $class1],
			['c2p2', $class2, $class3]
		];
	}
	
	/**
	 * Test `set` method expecting an `Invalid` exception to be thrown.
	 * 
	 * @testdox Set Invalid exception
	 * @dataProvider provideSetData_InvalidException
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param string $class
	 * The class to test with.
	 * 
	 * @return void
	 */
	public function testSet_InvalidException(string $name, mixed $value, string $class): void
	{		
		//initialize
		Manager::clearCache();
		$manager = new Manager(new $class());
		$manager->initialize(match ($class) {
			PropertiesTest_Class1::class => [123],
			PropertiesTest_Class2::class => [123, '4.35M', []]
		});
		
		//exception
		$this->expectException(InvalidException::class);
		try {
			$manager->set($name, $value, $class);
		} catch (InvalidException $exception) {
			$this->assertSame($manager, $exception->manager);
			$this->assertSame([$name => $value], $exception->values);
			$this->assertSame([$name], array_keys($exception->errors));
			throw $exception;
		}
	}
	
	/**
	 * Provide `set` method data for an `Invalid` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideSetData_InvalidException(): array
	{
		//initialize
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//return
		return [
			['p8', 'foo', $class1],
			['p8', 'foo', $class2],
			['p9', '1bar', $class1],
			['p9', '1bar', $class2],
			['p19', true, $class1],
			['p19', true, $class2],
			['p20', [], $class1],
			['p20', [], $class2],
			['p21', 1.1, $class1],
			['p21', 1.1, $class2],
			['c2p0', 'a', $class2],
			['c2p2', false, $class2],
			['c2p4', [1, 2], $class2]
		];
	}
	
	
	
	//Protected methods
	/**
	 * Check property `p0`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP0(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p0', $property->getReflection()->getName());
		$this->assertTrue($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertFalse($property->hasDefaultValue());
		$this->assertNull($property->getDefaultValue());
		$this->assertSame('rw', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertTrue($property->isWriteable());
		$this->assertTrue($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p1`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP1(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p1', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertFalse($property->isAccessible());
		$this->assertFalse($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertFalse($property->hasDefaultValue());
		$this->assertNull($property->getDefaultValue());
		$this->assertSame('rw', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertTrue($property->isWriteable());
		$this->assertTrue($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p6`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP6(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p6', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertNull($property->getDefaultValue());
		$this->assertSame('rw', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertTrue($property->isWriteable());
		$this->assertTrue($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p7`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP7(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p7', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertFalse($property->isAccessible());
		$this->assertFalse($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertNull($property->getDefaultValue());
		$this->assertSame('rw', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertTrue($property->isWriteable());
		$this->assertTrue($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p8`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP8(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p8', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame(0, $property->getDefaultValue());
		$this->assertSame('rw', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertTrue($property->isWriteable());
		$this->assertTrue($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p9`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP9(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p9', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertFalse($property->isAccessible());
		$this->assertFalse($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame(1.0, $property->getDefaultValue());
		$this->assertSame('rw', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertTrue($property->isWriteable());
		$this->assertTrue($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p10`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP10(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p10', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertFalse($property->hasDefaultValue());
		$this->assertNull($property->getDefaultValue());
		$this->assertSame('r', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertFalse($property->isWriteable());
		$this->assertFalse($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertFalse($property->isWriteable(initializing: true));
		$this->assertFalse($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p11`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP11(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p11', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame(1, $property->getDefaultValue());
		$this->assertSame('r', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertFalse($property->isWriteable());
		$this->assertFalse($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertFalse($property->isWriteable(initializing: true));
		$this->assertFalse($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p12`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP12(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p12', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame(1, $property->getDefaultValue());
		$this->assertSame('r+', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertFalse($property->isWriteable());
		$this->assertFalse($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p13`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP13(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p13', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame(1, $property->getDefaultValue());
		$this->assertSame('w', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertFalse($property->isReadable());
		$this->assertFalse($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertTrue($property->isWriteable());
		$this->assertTrue($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p14`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP14(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p14', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame(1, $property->getDefaultValue());
		$this->assertSame('w-', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertFalse($property->isReadable());
		$this->assertFalse($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertFalse($property->isWriteable());
		$this->assertFalse($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p15`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP15(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p15', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame(1, $property->getDefaultValue());
		$this->assertSame('r', $property->getMode());
		$this->assertTrue($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertFalse($property->isWriteable());
		$this->assertFalse($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertFalse($property->isWriteable($class2));
		$this->assertFalse($property->isWriteable(initializing: true));
		$this->assertFalse($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertFalse($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p16`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP16(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p16', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame(1, $property->getDefaultValue());
		$this->assertSame('r+', $property->getMode());
		$this->assertTrue($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertFalse($property->isWriteable());
		$this->assertFalse($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertFalse($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p17`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP17(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p17', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame(1, $property->getDefaultValue());
		$this->assertSame('w', $property->getMode());
		$this->assertTrue($property->areSubclassesAffectedByMode());
		$this->assertFalse($property->isReadable());
		$this->assertFalse($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertFalse($property->isReadable($class2));
		$this->assertTrue($property->isWriteable());
		$this->assertTrue($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p18`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP18(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p18', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame(1, $property->getDefaultValue());
		$this->assertSame('w-', $property->getMode());
		$this->assertTrue($property->areSubclassesAffectedByMode());
		$this->assertFalse($property->isReadable());
		$this->assertFalse($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertFalse($property->isReadable($class2));
		$this->assertFalse($property->isWriteable());
		$this->assertFalse($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertFalse($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p19`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP19(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p19', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame('1.2k', $property->getDefaultValue());
		$this->assertSame('rw', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertTrue($property->isWriteable());
		$this->assertTrue($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertTrue($property->hasType());
		$this->assertNotNull($property->getType());
		$this->assertFalse($property->getType()->nullable);
		$this->assertFalse($property->getType()->strict);
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p20`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP20(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p20', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame(420, $property->getDefaultValue());
		$this->assertSame('rw', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertTrue($property->isWriteable());
		$this->assertTrue($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertTrue($property->hasType());
		$this->assertNotNull($property->getType());
		$this->assertTrue($property->getType()->nullable);
		$this->assertFalse($property->getType()->strict);
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p21`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP21(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p21', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertNull($property->getDefaultValue());
		$this->assertSame('rw', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertTrue($property->isWriteable());
		$this->assertTrue($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertTrue($property->hasType());
		$this->assertNotNull($property->getType());
		$this->assertTrue($property->getType()->nullable);
		$this->assertFalse($property->getType()->strict);
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p22`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP22(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p22', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame('100', $property->getDefaultValue());
		$this->assertSame('rw', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertTrue($property->isWriteable());
		$this->assertTrue($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `p23`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkP23(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('p23', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame(1, $property->getDefaultValue());
		$this->assertSame('rw', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertTrue($property->isWriteable());
		$this->assertTrue($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertTrue($property->isLazy());
	}
	
	/**
	 * Check property `c1p0`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkC1P0(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('c1p0', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame('foo', $property->getDefaultValue());
		$this->assertSame('r+', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertFalse($property->isWriteable());
		$this->assertFalse($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertTrue($property->hasType());
		$this->assertNotNull($property->getType());
		$this->assertFalse($property->getType()->nullable);
		$this->assertFalse($property->getType()->strict);
		$this->assertTrue($property->isLazy());
	}
	
	/**
	 * Check property `c1p1`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkC1P1(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('c1p1', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertFalse($property->isAccessible());
		$this->assertFalse($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame('', $property->getDefaultValue());
		$this->assertSame('w-', $property->getMode());
		$this->assertTrue($property->areSubclassesAffectedByMode());
		$this->assertFalse($property->isReadable());
		$this->assertFalse($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertFalse($property->isReadable($class2));
		$this->assertFalse($property->isWriteable());
		$this->assertFalse($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertFalse($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertTrue($property->isLazy());
	}
	
	/**
	 * Check property `c2p0`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkC2P0(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('c2p0', $property->getReflection()->getName());
		$this->assertTrue($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertFalse($property->hasDefaultValue());
		$this->assertNull($property->getDefaultValue());
		$this->assertSame('w-', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertFalse($property->isReadable());
		$this->assertFalse($property->isReadable($stdclass));
		$this->assertFalse($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertFalse($property->isWriteable());
		$this->assertFalse($property->isWriteable($stdclass));
		$this->assertFalse($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertTrue($property->hasType());
		$this->assertNotNull($property->getType());
		$this->assertFalse($property->getType()->nullable);
		$this->assertFalse($property->getType()->strict);
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `c2p1`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkC2P1(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('c2p1', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertFalse($property->isAccessible());
		$this->assertFalse($property->isAccessible($stdclass));
		$this->assertFalse($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame(false, $property->getDefaultValue());
		$this->assertSame('r', $property->getMode());
		$this->assertTrue($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertFalse($property->isWriteable());
		$this->assertFalse($property->isWriteable($stdclass));
		$this->assertFalse($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertFalse($property->isWriteable(initializing: true));
		$this->assertFalse($property->isWriteable($stdclass, true));
		$this->assertFalse($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertFalse($property->hasType());
		$this->assertNull($property->getType());
		$this->assertTrue($property->isLazy());
	}
	
	/**
	 * Check property `c2p2`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkC2P2(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('c2p2', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame(75.5, $property->getDefaultValue());
		$this->assertSame('r+', $property->getMode());
		$this->assertTrue($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertFalse($property->isWriteable());
		$this->assertFalse($property->isWriteable($stdclass));
		$this->assertFalse($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertTrue($property->hasType());
		$this->assertNotNull($property->getType());
		$this->assertFalse($property->getType()->nullable);
		$this->assertFalse($property->getType()->strict);
		$this->assertFalse($property->isLazy());
	}
	
	/**
	 * Check property `c2p3`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkC2P3(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('c2p3', $property->getReflection()->getName());
		$this->assertTrue($property->isRequired());
		$this->assertTrue($property->isAccessible());
		$this->assertTrue($property->isAccessible($stdclass));
		$this->assertTrue($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertFalse($property->hasDefaultValue());
		$this->assertNull($property->getDefaultValue());
		$this->assertSame('rw', $property->getMode());
		$this->assertFalse($property->areSubclassesAffectedByMode());
		$this->assertTrue($property->isReadable());
		$this->assertTrue($property->isReadable($stdclass));
		$this->assertTrue($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertTrue($property->isWriteable());
		$this->assertTrue($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertTrue($property->hasType());
		$this->assertNotNull($property->getType());
		$this->assertTrue($property->getType()->nullable);
		$this->assertFalse($property->getType()->strict);
		$this->assertTrue($property->isLazy());
	}
	
	/**
	 * Check property `c2p4`.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to check.
	 * 
	 * @return void
	 */
	protected function checkC2P4(Property $property): void
	{
		//initialize
		$stdclass = stdClass::class;
		$class1 = PropertiesTest_Class1::class;
		$class2 = PropertiesTest_Class2::class;
		
		//assert
		$this->assertSame('c2p4', $property->getReflection()->getName());
		$this->assertFalse($property->isRequired());
		$this->assertFalse($property->isAccessible());
		$this->assertFalse($property->isAccessible($stdclass));
		$this->assertFalse($property->isAccessible($class1));
		$this->assertTrue($property->isAccessible($class2));
		$this->assertTrue($property->hasDefaultValue());
		$this->assertSame([], $property->getDefaultValue());
		$this->assertSame('w', $property->getMode());
		$this->assertTrue($property->areSubclassesAffectedByMode());
		$this->assertFalse($property->isReadable());
		$this->assertFalse($property->isReadable($stdclass));
		$this->assertFalse($property->isReadable($class1));
		$this->assertTrue($property->isReadable($class2));
		$this->assertTrue($property->isWriteable());
		$this->assertTrue($property->isWriteable($stdclass));
		$this->assertTrue($property->isWriteable($class1));
		$this->assertTrue($property->isWriteable($class2));
		$this->assertTrue($property->isWriteable(initializing: true));
		$this->assertTrue($property->isWriteable($stdclass, true));
		$this->assertTrue($property->isWriteable($class1, true));
		$this->assertTrue($property->isWriteable($class2, true));
		$this->assertTrue($property->hasType());
		$this->assertNotNull($property->getType());
		$this->assertFalse($property->getType()->nullable);
		$this->assertTrue($property->getType()->strict);
		$this->assertFalse($property->isLazy());
	}
}



/** Test case dummy class 1. */
class PropertiesTest_Class1
{
	public mixed $p0;
	
	protected mixed $p1;
	
	private mixed $p2;
	
	public static $p3;
	
	protected static $p4;
	
	private static $p5;
	
	public $p6;
	
	protected $p7;
	
	public int $p8 = 0;
	
	protected float $p9 = 1.0;
	
	#[mode('r')]
	public mixed $p10;
	
	#[mode('r')]
	public $p11 = 1;
	
	#[mode('r+')]
	public $p12 = 1;
	
	#[mode('w')]
	public $p13 = 1;
	
	#[mode('w-')]
	public $p14 = 1;
	
	#[mode('r', true)]
	public $p15 = 1;
	
	#[mode('r+', true)]
	public $p16 = 1;
	
	#[mode('w', true)]
	public $p17 = 1;
	
	#[mode('w-', true)]
	public $p18 = 1;
	
	#[coercive('int')]
	public $p19 = '1.2k';
	
	#[coercive('?string')]
	public $p20 = 420;
	
	#[coercive]
	public ?int $p21 = null;
	
	public $p22 = '100'; //alternate type in real-time between int and float
	
	#[lazy]
	public int $p23 = 1;
	
	#[mode('r+'), coercive, lazy]
	public string $c1p0 = 'foo';
	
	#[lazy, mode('w-', true)]
	protected string $c1p1 = '';
}



/** Test case dummy class 2. */
class PropertiesTest_Class2 extends PropertiesTest_Class1
{
	#[mode('w-'), coercive]
	public int $c2p0;
	
	#[lazy, mode('r', true)]
	protected bool $c2p1 = false;
	
	#[mode('r+', true), coercive]
	public int|float $c2p2 = 75.5;
	
	#[coercive, lazy]
	public int|string|null $c2p3;
	
	#[strict('string[]'), mode('w', true)]
	protected array $c2p4 = [];
	
	private string $c2p5;
}



/** Test case dummy class 3. */
class PropertiesTest_Class3 extends PropertiesTest_Class2
{
	#[mode('r')]
	public $c3p0;
}
