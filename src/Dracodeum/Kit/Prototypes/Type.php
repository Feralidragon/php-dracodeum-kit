<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes;

use Dracodeum\Kit\Prototype;
use Dracodeum\Kit\Prototype\Interfaces\Contract as IContract;
use Dracodeum\Kit\Prototypes\Type\Contract;
use Dracodeum\Kit\Primitives\Error;

/** @see \Dracodeum\Kit\Components\Type */
abstract class Type extends Prototype implements IContract
{
	//Abstract public methods
	/**
	 * Process a given value.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to process.</p>
	 * @return \Dracodeum\Kit\Primitives\Error|null
	 * <p>An error instance if the given value failed to be processed or <code>null</code> if otherwise.</p>
	 */
	abstract public function process(mixed &$value): ?Error;
	
	
	
	//Implemented public static methods (Dracodeum\Kit\Prototype\Interfaces\Contract)
	/** {@inheritdoc} */
	public static function getContract(): string
	{
		return Contract::class;
	}
	
	
	
	//Final protected methods
	/**
	 * Get context.
	 * 
	 * @return enum:value(Dracodeum\Kit\Components\Type\Enumerations\Context)
	 * <p>The context.</p>
	 */
	final protected function getContext()
	{
		return $this->contractCall('getContext');
	}
}
