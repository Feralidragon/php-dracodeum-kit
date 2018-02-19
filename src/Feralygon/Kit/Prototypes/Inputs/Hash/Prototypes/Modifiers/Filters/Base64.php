<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Hash\Prototypes\Modifiers\Filters;

use Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Filter;
use Feralygon\Kit\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;
use Feralygon\Kit\Utilities\Base64 as UBase64;

/**
 * Hash input Base64 filter modifier prototype class.
 * 
 * This filter prototype converts a hash string in hexadecimal notation into a Base64 encoded string.
 * 
 * @since 1.0.0
 * @property bool $url_safe [default = false] <p>Use URL-safe encoding, 
 * in which the plus signs (+) and slashes (/) get replaced by hyphens (-) and underscores (_) respectively, 
 * as well as the padding equal signs (=) removed, in order to be safely put in an URL.</p>
 * @see \Feralygon\Kit\Prototypes\Inputs\Hash
 */
class Base64 extends Filter implements IPrototypeProperties
{
	//Private properties
	/** @var bool */
	private $url_safe = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function processValue(&$value) : bool
	{
		if (is_string($value)) {
			$value = hex2bin($value);
			if ($value !== false) {
				$value = UBase64::encode($value, $this->url_safe);
				return true;
			}
		}
		return false;
	}
	
	
	
	//Implemented public methods (prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'url_safe':
				return $this->createProperty()->setAsBoolean()->bind(self::class);
		}
		return null;
	}
	
	
	
	//Implemented public static methods (prototype properties interface)
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return [];
	}
}
