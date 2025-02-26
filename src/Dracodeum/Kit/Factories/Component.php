<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Factories;

use Dracodeum\Kit\Factory;
use Dracodeum\Kit\Factory\Type;
use Dracodeum\Kit\Factories\Component\Builders;
use Dracodeum\Kit\Factories\Component\Builder\Interfaces as BuilderInterfaces;
use Dracodeum\Kit\Components\{
	Input,
	Logger,
	Store
};

class Component extends Factory
{
	//Implemented protected static methods
	/** {@inheritdoc} */
	protected static function buildType(string $name): ?Type
	{
		switch ($name) {
			case 'input':
				return static::createType(BuilderInterfaces\Input::class, Builders\Input::class);
			case 'logger':
				return static::createType(BuilderInterfaces\Logger::class, Builders\Logger::class);
			case 'store':
				return static::createType(BuilderInterfaces\Store::class, Builders\Store::class);
		}
		return null;
	}
	
	
	
	//Public static methods
	/**
	 * Build input instance with a given prototype.
	 * 
	 * @param \Dracodeum\Kit\Prototypes\Input|string $prototype
	 * <p>The prototype instance, class or name to build with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to build with, as a set of <samp>name => value</samp> pairs, 
	 * if a prototype class or name is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Dracodeum\Kit\Components\Input
	 * <p>The built input instance with the given prototype.</p>
	 */
	public static function input($prototype, array $properties = []): Input
	{
		return static::build('input', $prototype, $properties);
	}
	
	/**
	 * Build logger instance with a given prototype.
	 *
	 * @param \Dracodeum\Kit\Prototypes\Logger|string $prototype
	 * <p>The prototype instance, class or name to build with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to build with, as a set of <samp>name => value</samp> pairs, 
	 * if a prototype class or name is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>),
	 * in the same order as how these properties were first declared.</p>
	 * @return \Dracodeum\Kit\Components\Logger
	 * <p>The built logger instance with the given prototype.</p>
	 */
	public static function logger($prototype, array $properties = []): Logger
	{
		return static::build('logger', $prototype, $properties);
	}
	
	/**
	 * Build store instance with a given prototype.
	 * 
	 * @param \Dracodeum\Kit\Prototypes\Store|string $prototype
	 * <p>The prototype instance, class or name to build with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to build with, as a set of <samp>name => value</samp> pairs, 
	 * if a prototype class or name is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Dracodeum\Kit\Components\Store
	 * <p>The built store instance with the given prototype.</p>
	 */
	public static function store($prototype, array $properties = []): Store
	{
		return static::build('store', $prototype, $properties);
	}
}
