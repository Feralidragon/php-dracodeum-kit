<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Class;

use Dracodeum\Kit\Managers\PropertiesV2\Meta;

/** This interface defines a method to initialize a meta instance from a class attribute. */
interface MetaInitializer
{
	//Public methods
	/**
	 * Initialize a given meta instance.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Meta $meta
	 * The meta instance to initialize.
	 * 
	 * @return void
	 */
	public function initializeMeta(Meta $meta): void;
}
