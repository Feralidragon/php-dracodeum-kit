<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Types;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Types\TResource as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use stdClass;

/** @see \Dracodeum\Kit\Prototypes\Types\TResource */
class TResourceTest extends TestCase
{
	//Public methods
	/**
	 * Test process.
	 * 
	 * @testdox Process
	 * @dataProvider provideProcessData
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param mixed $expected
	 * The expected processed value.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess(mixed $value, mixed $expected, array $properties = []): void
	{
		$this->assertNull(Component::build(Prototype::class, $properties)->process($value));
		$this->assertSame($expected, $value);
	}
	
	/**
	 * Test process (error).
	 * 
	 * @testdox Process (error)
	 * @dataProvider provideProcessData_Error
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess_Error(mixed $value, array $properties = []): void
	{
		$this->assertInstanceOf(Error::class, Component::build(Prototype::class, $properties)->process($value));
	}
	
	/**
	 * Test `Textifier` interface.
	 * 
	 * @testdox Textifier interface
	 * 
	 * @see \Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier
	 */
	public function testTextifierInterface(): void
	{
		$text = Component::build(Prototype::class)->textify(fopen(__FILE__, 'r'));
		$this->assertInstanceOf(Text::class, $text);
		$this->assertMatchesRegularExpression('/^resource<stream>#\d+$/', $text->toString());
	}
	
	
	
	//Public static methods
	/**
	 * Provide process data.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData(): array
	{
		//initialize
		$resource1 = fopen(__FILE__, 'r');
		$resource2 = fopen(__FILE__, 'r');
		fclose($resource2);
		
		//return
		return [
			[$resource1, $resource1],
			[$resource2, $resource2],
			[$resource1, $resource1, ['type' => 'stream']]
		];
	}
	
	/**
	 * Provide process data (error).
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData_Error(): array
	{
		return [
			[null],
			[false],
			[true],
			[1],
			[1.1],
			[''],
			[' '],
			['123'],
			['foo'],
			[[]],
			[new stdClass],
			[fopen(__FILE__, 'r'), ['type' => 'curl']]
		];
	}
}
