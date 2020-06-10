<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Store\Exceptions;

use Dracodeum\Kit\Components\Store\Exception;
use Dracodeum\Kit\Components\Store\Structures\Uid;

/**
 * This exception is thrown from a store whenever a given resource scope is not found.
 * 
 * @property-read \Dracodeum\Kit\Components\Store\Structures\Uid $uid [coercive]
 * <p>The UID instance.</p>
 */
class ScopeNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		//initialize
		$uid = $this->get('uid');
		$message = "Resource";
		
		//name
		if ($uid->name !== null) {
			$message .= " {{uid.name}}";
		}
		
		//scope
		$message .= " scope";
		if ($uid->scope !== null) {
			$message .= " {{uid.scope}}";
		}
		
		//finalize
		$message .= " not found in store {{component}} (with prototype {{prototype}}).";
		
		//return
		return $message;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('uid')->setAsStructure(Uid::class);
	}
}
