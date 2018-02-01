<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Core\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Priority as IPriority,
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core input minimum length constraint modifier prototype class.
 * 
 * This constraint prototype restricts a value to a minimum length.
 * 
 * @since 1.0.0
 * @property int $length <p>The minimum length to restrict to.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 * @property bool $unicode [default = false] <p>Check as an Unicode value.</p>
 */
class MinLength extends Constraint
implements IPrototypeProperties, IName, IPriority, IInformation, IStringification, ISchemaData
{
	//Private properties
	/** @var int */
	private $length;
	
	/** @var bool */
	private $unicode = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value) : bool
	{
		return UText::length($value, $this->unicode) >= $this->length;
	}
	
	
	
	//Implemented public methods (core prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'length':
				return $this->createProperty()
					->bind($name, self::class)
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateInteger($value) && $value >= 0;
					})
				;
			case 'unicode':
				return $this->createProperty()->bind($name, self::class)->setAsBoolean();
		}
		return null;
	}
	
	
	
	//Implemented public static methods (core prototype properties interface)
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['length'];
	}
	
	
	
	//Implemented public methods (core input modifier prototype name interface)
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'constraints.min_length';
	}
	
	
	
	//Implemented public methods (core input modifier prototype priority interface)
	/** {@inheritdoc} */
	public function getPriority() : int
	{
		return 250;
	}
	
	
	
	//Implemented public methods (core input modifier prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		return UText::localize("Minimum allowed length", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		/**
		 * @placeholder length The minimum allowed length.
		 * @example Only a minimum of 10 characters are allowed.
		 */
		return UText::plocalize(
			"Only a minimum of {{length}} character is allowed.",
			"Only a minimum of {{length}} characters are allowed.",
			$this->length, 'length', self::class, $text_options
		);
	}
	
	
	
	//Implemented public methods (core input modifier prototype stringification interface)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		return UText::stringify($this->length, $text_options);
	}
	
	
	
	//Implemented public methods (core input modifier prototype schema data interface)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'length' => $this->length,
			'unicode' => $this->unicode
		];
	}
}
