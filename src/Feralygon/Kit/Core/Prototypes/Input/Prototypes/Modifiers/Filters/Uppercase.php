<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Filters;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Filter;
use Feralygon\Kit\Core\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property;
use Feralygon\Kit\Core\Utilities\Text as UText;

/**
 * Core input uppercase filter modifier prototype class.
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
	
	
	
	//Implemented public methods (core prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'unicode':
				return $this->createProperty()->bind($name, self::class)->setAsBoolean();
		}
		return null;
	}
	
	
	
	//Implemented public static methods (core prototype properties interface)
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return [];
	}
	
	
	
	//Implemented public methods (core input modifier prototype name interface)
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'filters.uppercase';
	}
	
	
	
	//Implemented public methods (core input modifier prototype schema data interface)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'unicode' => $this->unicode
		];
	}
}
