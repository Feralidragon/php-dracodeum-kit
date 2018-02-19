<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Filters;

use Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Filter;
use Feralygon\Kit\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * Input uppercase filter modifier prototype class.
 * 
 * This filter prototype converts a value to uppercase.
 * 
 * @since 1.0.0
 * @property bool $unicode [default = false] <p>Convert as an Unicode value.</p>
 */
class Uppercase extends Filter implements IPrototypeProperties, IName, ISchemaData
{
	//Private properties
	/** @var bool */
	private $unicode = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function processValue(&$value) : bool
	{
		if (is_string($value)) {
			$value = UText::upper($value, $this->unicode);
			return true;
		}
		return false;
	}
	
	
	
	//Implemented public methods (prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'unicode':
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
	
	
	
	//Implemented public methods (input modifier prototype name interface)
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'filters.uppercase';
	}
	
	
	
	//Implemented public methods (input modifier prototype schema data interface)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'unicode' => $this->unicode
		];
	}
}
