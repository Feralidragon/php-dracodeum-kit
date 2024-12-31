<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Options;

use Dracodeum\Kit\Exception as KException;
use Dracodeum\Kit\Options;

/**
 * @property-read \Dracodeum\Kit\Options|string $options
 * <p>The options instance or class.</p>
 */
abstract class Exception extends KException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('options')->setAsObjectClass(Options::class);
	}
}
