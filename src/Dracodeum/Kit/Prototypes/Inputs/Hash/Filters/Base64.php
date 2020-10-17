<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs\Hash\Filters;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Filter;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Subtype as ISubtype,
	SchemaData as ISchemaData
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\Base64 as UBase64;

/**
 * This filter prototype converts a given hash input value in hexadecimal notation into a Base64 encoded string.
 * 
 * @property-write bool $url_safe [writeonce] [transient] [default = false]
 * <p>Use URL-safe encoding, in which the plus signs (+) and slashes (/) are replaced 
 * by hyphens (-) and underscores (_) respectively, with the padding equal signs (=) removed, 
 * in order to be safely put in a URL.</p>
 */
class Base64 extends Filter implements ISubtype, ISchemaData
{
	//Protected properties
	/** @var bool */
	protected $url_safe = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'base64';
	}
	
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
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'hash';
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'url_safe' => $this->url_safe
		];
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'url_safe':
				return $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
