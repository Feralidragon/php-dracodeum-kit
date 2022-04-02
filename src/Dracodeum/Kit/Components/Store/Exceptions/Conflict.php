<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Store\Exceptions;

use Dracodeum\Kit\Components\Store\Exception;
use Dracodeum\Kit\Structures\Uid;

/**
 * @property-read \Dracodeum\Kit\Structures\Uid $uid
 * The UID instance.
 */
class Conflict extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		//initialize
		$uid = $this->uid;
		$message = "Resource";
		
		//name
		if ($uid->name !== null) {
			$message .= " {{uid.name}}";
		}
		
		//id and scope
		if ($uid->id !== null && $uid->scope !== null) {
			$message .= " with ID {{uid.id}} and scope {{uid.scope}}";
		} elseif ($uid->scope !== null) {
			$message .= " with scope {{uid.scope}}";
		} elseif ($uid->id !== null) {
			$message .= " with ID {{uid.id}}";
		}
		
		//finalize
		$message .= " conflicts with an existing one in store {{component}} (with prototype {{prototype}}).";
		
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
