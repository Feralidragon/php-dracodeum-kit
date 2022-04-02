<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitive;

use Dracodeum\Kit\Exception as KitException;
use Dracodeum\Kit\Primitive;
use Dracodeum\Kit\Utilities\Type as UType;

/**
 * @property-read \Dracodeum\Kit\Primitive|string $primitive
 * <p>The primitive instance or class.</p>
 */
abstract class Exception extends KitException
{
	//Abstract protected static methods
	/**
	 * Get primitive class.
	 * 
	 * @return string
	 * <p>The primitive class.</p>
	 */
	abstract protected static function getPrimitiveClass(): string;
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('primitive')->setAsObjectClass(
			UType::coerceClass($this->getPrimitiveClass(), Primitive::class)
		);
	}
}
