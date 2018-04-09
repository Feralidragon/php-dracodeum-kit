<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Priority as IPriority,
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This constraint prototype restricts a value to a range of lengths.
 * 
 * @since 1.0.0
 * @property int $min_length
 * <p>The minimum length to restrict a given value to.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 * @property int $max_length
 * <p>The maximum length to restrict a given value to.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 * @property bool $unicode [default = false]
 * <p>Check a given value as Unicode.</p>
 */
class LengthRange extends Constraint implements IName, IPriority, IInformation, IStringification, ISchemaData
{
	//Private properties
	/** @var int */
	private $min_length;
	
	/** @var int */
	private $max_length;
	
	/** @var bool */
	private $unicode = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value) : bool
	{
		$length = UText::length($value, $this->unicode);
		return $length >= $this->min_length && $length <= $this->max_length;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\Name)
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'constraints.length_range';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\Priority)
	/** {@inheritdoc} */
	public function getPriority() : int
	{
		return 250;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		return UText::localize("Allowed lengths range", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		/**
		 * @placeholder min_length The minimum allowed length.
		 * @placeholder max_length The maximum allowed length.
		 * @example Only between 5 and 10 characters are allowed.
		 */
		return UText::plocalize(
			"Only between {{min_length}} and {{max_length}} character is allowed.",
			"Only between {{min_length}} and {{max_length}} characters are allowed.",
			$this->max_length, 'max_length', self::class, $text_options, [
				'parameters' => ['min_length' => $this->min_length]
			]
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		/**
		 * @placeholder min_length The minimum allowed length.
		 * @placeholder max_length The maximum allowed length.
		 * @example 5 to 10
		 */
		return UText::localize(
			"{{min_length}} to {{max_length}}",
			self::class, $text_options, [
				'parameters' => ['min_length' => $this->min_length, 'max_length' => $this->max_length]
			]
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'minimum' => [
				'length' => $this->min_length
			],
			'maximum' => [
				'length' => $this->max_length
			],
			'unicode' => $this->unicode
		];
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\RequiredPropertyNames)
	/** {@inheritdoc} */
	protected function loadRequiredPropertyNames() : void
	{
		$this->addRequiredPropertyNames(['min_length', 'max_length']);
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\Properties)
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'min_length':
				//no break
			case 'max_length':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateInteger($value) && $value >= 0;
					})
					->bind(self::class)
				;
			case 'unicode':
				return $this->createProperty()->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
