<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Store\Exceptions;

use Dracodeum\Kit\Components\Store\Exception;
use Dracodeum\Kit\Components\Store\Structures\Uid;

/**
 * This exception is thrown from a store whenever a resource conflicts with an existing one.
 * 
 * @property-read \Dracodeum\Kit\Components\Store\Structures\Uid $uid [coercive]
 * <p>The UID instance.</p>
 */
class Conflict extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		$uid = $this->get('uid');
		if ($uid->id !== null && $uid->scope !== null) {
			return "Resource {{uid.name}} with ID {{uid.id}} and scope {{uid.scope}} " . 
				"conflicts with an existing one in store {{component}} (with prototype {{prototype}}).";
		} elseif ($uid->scope !== null) {
			return "Resource {{uid.name}} with scope {{uid.scope}} " . 
				"conflicts with an existing one in store {{component}} (with prototype {{prototype}}).";
		} elseif ($uid->id !== null) {
			return "Resource {{uid.name}} with ID {{uid.id}} " . 
				"conflicts with an existing one in store {{component}} (with prototype {{prototype}}).";
		}
		return "Resource {{uid.name}} " . 
				"conflicts with an existing one in store {{component}} (with prototype {{prototype}}).";
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
