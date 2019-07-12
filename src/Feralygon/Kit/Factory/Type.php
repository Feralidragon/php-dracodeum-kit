<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory;

use Feralygon\Kit\Utilities\{
	Call as UCall,
	Type as UType
};

/**
 * @since 1.0.0
 * @see \Feralygon\Kit\Factory
 */
final class Type
{
	//Private properties
	/** @var string */
	private $name;
	
	/** @var string */
	private $builder_interface;
	
	/** @var \Feralygon\Kit\Factory\Builder */
	private $builder;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to instantiate with.</p>
	 * @param string $builder_interface
	 * <p>The builder interface to instantiate with.<br>
	 * It must define a <code>build</code> method, which must return an object or <code>null</code>.</p>
	 * @param \Feralygon\Kit\Factory\Builder|string $builder
	 * <p>The builder instance or class to instantiate with.<br>
	 * It must implement the builder interface given above as <var>$builder_interface</var>.</p>
	 */
	final public function __construct(string $name, string $builder_interface, $builder)
	{
		//name
		$this->name = $name;
		
		//builder interface
		$builder_interface = UType::interface($builder_interface);
		UCall::guardParameter('builder_interface', $builder_interface, method_exists($builder_interface, 'build'), [
			'hint_message' => "Only an interface which defines a \"build\" method is allowed."
		]);
		$this->builder_interface = $builder_interface;
		
		//builder
		$this->setBuilder($builder);
	}
	
	
	
	//Final public methods
	/**
	 * Get name.
	 * 
	 * @since 1.0.0
	 * @return string
	 * <p>The name.</p>
	 */
	final public function getName(): string
	{
		return $this->name;
	}
	
	/**
	 * Get builder interface.
	 * 
	 * @since 1.0.0
	 * @return string
	 * <p>The builder interface.</p>
	 */
	final public function getBuilderInterface(): string
	{
		return $this->builder_interface;
	}
	
	/**
	 * Get builder instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Factory\Builder
	 * <p>The builder instance.</p>
	 */
	final public function getBuilder(): Builder
	{
		return $this->builder;
	}
	
	/**
	 * Set builder.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Factory\Builder|string $builder
	 * <p>The builder instance or class to set.<br>
	 * It must implement the builder interface set in this type.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setBuilder($builder): Type
	{
		$builder = UType::coerceObject($builder, Builder::class);
		UCall::guardParameter('builder', $builder, UType::implements($builder, $this->builder_interface), [
			'hint_message' => "Only a builder instance or class which implements the {{interface}} interface " . 
				"is allowed for type {{type.getName()}}.",
			'parameters' => ['interface' => $this->builder_interface, 'type' => $this]
		]);
		$this->builder = $builder;
		return $this;
	}
}
