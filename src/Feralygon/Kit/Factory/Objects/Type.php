<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory\Objects;

use Feralygon\Kit\Factory\Builder;
use Feralygon\Kit\Utilities\Type as UType;

/**
 * Factory type object class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Factory
 */
final class Type
{
	//Private properties	
	/** @var string */
	private $name;
	
	/** @var \Feralygon\Kit\Factory\Builder */
	private $builder;
	
	/** @var string|null */
	private $class = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The name.</p>
	 * @param \Feralygon\Kit\Factory\Builder|string $builder <p>The builder instance or class.</p>
	 * @param string|null $class [default = null] <p>The class.<br>
	 * Any object built by this type must be or extend from the same class as the one given here.<br>
	 * If no class is set, then any built object is assumed to be valid.</p>
	 */
	final public function __construct(string $name, $builder, ?string $class = null)
	{
		$this->name = $name;
		$this->setBuilder($builder);
		if (isset($class)) {
			$this->class = UType::coerceClass($class);
		}
	}
	
	
	
	//Final public methods
	/**
	 * Get name.
	 * 
	 * @since 1.0.0
	 * @return string <p>The name.</p>
	 */
	final public function getName() : string
	{
		return $this->name;
	}
	
	/**
	 * Get builder instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Factory\Builder <p>The builder instance.</p>
	 */
	final public function getBuilder() : Builder
	{
		return $this->builder;
	}
	
	/**
	 * Set builder.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Factory\Builder|string $builder <p>The builder instance or class to set.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setBuilder($builder) : Type
	{
		$this->builder = UType::coerceObject($builder, Builder::class);
		return $this;
	}
	
	/**
	 * Check if has class.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if has class.</p>
	 */
	final public function hasClass() : bool
	{
		return isset($this->class);
	}
	
	/**
	 * Get class.
	 * 
	 * @since 1.0.0
	 * @return string|null <p>The class or <code>null</code> if none is set.</p>
	 */
	final public function getClass() : ?string
	{
		return $this->class;
	}
}
