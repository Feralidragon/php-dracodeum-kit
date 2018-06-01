<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Memoization;

/**
 * @since 1.0.0
 * @internal
 * @see \Feralygon\Kit\Managers\Memoization
 */
final class Entry
{
	//Public properties
	/** @var string */
	public $class;
	
	/** @var string */
	public $selector;
	
	/** @var string */
	public $namespace;
	
	/** @var string */
	public $key;
	
	/** @var mixed */
	public $value;
	
	/** @var int */
	public $index = 0;
	
	/** @var int|null */
	public $expire = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param string $class
	 * <p>The class.</p>
	 * @param string $selector
	 * <p>The selector.</p>
	 * @param string $namespace
	 * <p>The namespace.</p>
	 * @param string $key
	 * <p>The key.</p>
	 * @param mixed $value
	 * <p>The value.</p>
	 */
	final public function __construct(string $class, string $selector, string $namespace, string $key, $value)
	{
		$this->class = $class;
		$this->selector = $selector;
		$this->namespace = $namespace;
		$this->key = $key;
		$this->value = $value;
	}
}
