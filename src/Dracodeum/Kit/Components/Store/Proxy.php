<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Store;

use Dracodeum\Kit\Proxy as KitProxy;
use Dracodeum\Kit\Prototypes\Store\Contract as IContract;
use Dracodeum\Kit\Components\Store as Component;
use Dracodeum\Kit\Components\Store\Structures\Uid;

class Proxy extends KitProxy implements IContract
{
	//Implemented protected static methods
	/** {@inheritdoc} */
	protected static function getOwnerBaseClass(): string
	{
		return Component::class;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Store\Contract)
	/** {@inheritdoc} */
	public function halt(Uid $uid, string $type): void
	{
		$this->call('halt', $uid, $type);
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Proxy\Traits\Initializer)
	/** {@inheritdoc} */
	protected function initialize(): void
	{
		$this->bind('halt');
	}
}
