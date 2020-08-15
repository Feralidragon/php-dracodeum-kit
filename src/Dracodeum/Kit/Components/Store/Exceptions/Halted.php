<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Store\Exceptions;

use Dracodeum\Kit\Components\Store\Exception;
use Dracodeum\Kit\Structures\Uid;
use Dracodeum\Kit\Components\Store\Enumerations\Halt\Type as EHaltType;

/**
 * This exception is thrown from a store whenever execution is halted for a given resource.
 * 
 * @property-read \Dracodeum\Kit\Structures\Uid $uid [coercive]
 * <p>The UID instance.</p>
 * @property-read string $type [coercive = enumeration value]
 * <p>The type, as a value from the <code>Dracodeum\Kit\Components\Store\Enumerations\Halt\Type</code> enumeration.</p>
 * @see \Dracodeum\Kit\Components\Store\Enumerations\Halt\Type
 */
class Halted extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		//initialize
		$uid = $this->uid;
		$message = "Execution halted with {{type}} for resource";
		
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
		$message .= " in store {{component}} (with prototype {{prototype}}).";
		
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
		$this->addProperty('type')->setAsEnumerationValue(EHaltType::class);
	}
}
