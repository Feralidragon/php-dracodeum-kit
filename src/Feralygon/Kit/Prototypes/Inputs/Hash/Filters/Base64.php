<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Hash\Filters;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filter;
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Utilities\Base64 as UBase64;

/**
 * This filter prototype converts a hash in hexadecimal notation into a Base64 encoded string.
 * 
 * @property-write bool $url_safe [writeonce] [coercive] [default = false]
 * <p>Use URL-safe encoding, in which the plus signs (+) and slashes (/) are replaced 
 * by hyphens (-) and underscores (_) respectively, with the padding equal signs (=) removed, 
 * in order to be safely put in an URL.</p>
 * @see \Feralygon\Kit\Prototypes\Inputs\Hash
 */
class Base64 extends Filter
{
	//Protected properties
	/** @var bool */
	protected $url_safe = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function processValue(&$value): bool
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
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'url_safe':
				return $this->createProperty()->setMode('w-')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
