<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Factories;

use Feralygon\Kit\Factory;
use Feralygon\Kit\Factory\Objects\Type;
use Feralygon\Kit\Components\Input\Factories\Component\Builders;
use Feralygon\Kit\Components\Input\Factories\Component\Builder\Interfaces as BuilderInterfaces;
use Feralygon\Kit\Components\Input\Components\Modifiers\{
	Constraint,
	Filter
};

/**
 * This factory is used to build component instances.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Components\Input\Factories\Component\Builder\Interfaces\Constraint
 * [builder interface, type = 'constraint']
 * @see \Feralygon\Kit\Components\Input\Factories\Component\Builder\Interfaces\Filter
 * [builder interface, type = 'filter']
 * @see \Feralygon\Kit\Components\Input\Factories\Component\Builders\Constraint
 * [builder, type = 'constraint']
 * @see \Feralygon\Kit\Components\Input\Factories\Component\Builders\Filter
 * [builder, type = 'filter']
 */
class Component extends Factory
{
	//Implemented protected static methods
	/** {@inheritdoc} */
	protected static function buildType(string $name) : ?Type
	{
		switch ($name) {
			case 'constraint':
				return static::createType(BuilderInterfaces\Constraint::class, Builders\Constraint::class);
			case 'filter':
				return static::createType(BuilderInterfaces\Filter::class, Builders\Filter::class);
		}
		return null;
	}
	
	
	
	//Public static methods
	/**
	 * Build constraint instance with a given prototype.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint|string $prototype
	 * <p>The prototype instance, class or name to build with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to build with, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Components\Input\Components\Modifiers\Constraint
	 * <p>The built constraint instance with the given prototype.</p>
	 */
	public static function constraint($prototype, array $properties = []) : Constraint
	{
		return static::build('constraint', $prototype, $properties);
	}
	
	/**
	 * Build filter instance with a given prototype.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filter|string $prototype
	 * <p>The prototype instance, class or name to build with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to build with, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Components\Input\Components\Modifiers\Filter
	 * <p>The built filter instance with the given prototype.</p>
	 */
	public static function filter($prototype, array $properties = []) : Filter
	{
		return static::build('filter', $prototype, $properties);
	}
}
