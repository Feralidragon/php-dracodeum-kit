<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs;

use Feralygon\Kit\Prototypes\Input;
use Feralygon\Kit\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Prototypes\Input\Interfaces\{
	Information as IInformation,
	SchemaData as ISchemaData,
	Modifiers as IModifiers
};
use Feralygon\Kit\Components\Input\Components\Modifier;
use Feralygon\Kit\Prototypes\Inputs\Text\Prototypes\Modifiers\{
	Constraints,
	Filters
};
use Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\{
	Constraints as InputConstraints,
	Filters as InputFilters
};
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Root\Locale;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Text input prototype class.
 * 
 * This input prototype represents a text or string, 
 * for which only integers, floats and strings may be evaluated as such.
 * 
 * @since 1.0.0
 * @property-read bool $unicode [default = false] <p>Set as Unicode text.</p>
 * @property-read bool $trim [default = false] <p>Trim the given text or string from whitespace.</p>
 * @see https://en.wikipedia.org/wiki/Plain_text
 * @see https://en.wikipedia.org/wiki/String_(computer_science)
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints\Values 
 * [modifier, name = 'constraints.values' or 'constraints.non_values']
 * @see \Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraints\Wildcards 
 * [modifier, name = 'constraints.wildcards' or 'constraints.non_wildcards']
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints\NonEmpty 
 * [modifier, name = 'constraints.non_empty']
 * @see \Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraints\Length 
 * [modifier, name = 'constraints.length']
 * @see \Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraints\MinLength 
 * [modifier, name = 'constraints.min_length']
 * @see \Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraints\MaxLength 
 * [modifier, name = 'constraints.max_length']
 * @see \Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraints\LengthRange 
 * [modifier, name = 'constraints.length_range']
 * @see \Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraints\Lowercase 
 * [modifier, name = 'constraints.lowercase']
 * @see \Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraints\Uppercase 
 * [modifier, name = 'constraints.uppercase']
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints\Alphabetical 
 * [modifier, name = 'constraints.alphabetical']
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints\Numerical 
 * [modifier, name = 'constraints.numerical']
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints\Alphanumerical 
 * [modifier, name = 'constraints.alphanumerical']
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints\Identifier 
 * [modifier, name = 'constraints.identifier']
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints\Hexadecimal 
 * [modifier, name = 'constraints.hexadecimal']
 * @see \Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Filters\Lowercase 
 * [modifier, name = 'filters.lowercase']
 * @see \Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Filters\Uppercase 
 * [modifier, name = 'filters.uppercase']
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Prototypes\Modifiers\Filters\Truncate 
 * [modifier, name = 'filters.truncate']
 */
class Text extends Input implements IPrototypeProperties, IInformation, ISchemaData, IModifiers
{
	//Private properties
	/** @var bool */
	private $unicode = false;
	
	/** @var bool */
	private $trim = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'text';
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value) : bool
	{
		//evaluate
		if (!UType::evaluateString($value)) {
			return false;
		}
		
		//unicode
		if ($this->unicode) {
			$encoding = mb_detect_encoding($value);
			$locale_encoding = Locale::getEncoding();
			if ($encoding !== false) {
				$value = mb_convert_encoding($value, $locale_encoding, $encoding);
			} elseif ($locale_encoding === 'UTF-8') {
				$value = utf8_encode($value);
			}
		}
		
		//trim
		if ($this->trim) {
			$value = trim($value);
		}
		
		//return
		return true;
	}
	
	
	
	//Implemented public methods (prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'unicode':
				//no break
			case 'trim':
				return $this->createProperty()->setMode('r+')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
	
	
	
	//Implemented public static methods (prototype properties interface)
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return [];
	}
	
	
	
	//Implemented public methods (input prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/** @tags technical */
			return UText::localize("String", self::class, $text_options);
		}
		
		//non-technical
		/** @tags non-technical */
		return UText::localize("Text", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/** @tags technical */
			return UText::localize("A string of characters.", self::class, $text_options);
		}
		
		//non-technical
		/** @tags non-technical */
		return UText::localize("A text.", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/** @tags technical */
			return UText::localize("Only a string of characters is allowed.", self::class, $text_options);
		}
		
		//non-technical
		/** @tags non-technical */
		return UText::localize("Only text is allowed.", self::class, $text_options);
	}
	
	
	
	//Implemented public methods (input prototype schema data interface)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'unicode' => $this->unicode,
			'trim' => $this->trim
		];
	}
	
	
	
	//Implemented public methods (input prototype modifiers interface)
	/** {@inheritdoc} */
	public function buildModifier(string $name, array $prototype_properties = [], array $properties = []) : ?Modifier
	{
		switch ($name) {
			//constraints
			case 'constraints.values':
				return $this->createConstraint(Constraints\Values::class, $prototype_properties, $properties);
			case 'constraints.non_values':
				return $this->createConstraint(
					Constraints\Values::class, ['negate' => true] + $prototype_properties, $properties
				);
			case 'constraints.wildcards':
				return $this->createConstraint(InputConstraints\Wildcards::class, $prototype_properties, $properties);
			case 'constraints.non_wildcards':
				return $this->createConstraint(
					InputConstraints\Wildcards::class, ['negate' => true] + $prototype_properties, $properties
				);
			case 'constraints.non_empty':
				return $this->createConstraint(Constraints\NonEmpty::class, $prototype_properties, $properties);
			case 'constraints.length':
				return $this->createConstraint(
					InputConstraints\Length::class,
					$prototype_properties + ['unicode' => $this->unicode],
					$properties
				);
			case 'constraints.min_length':
				return $this->createConstraint(
					InputConstraints\MinLength::class,
					$prototype_properties + ['unicode' => $this->unicode],
					$properties
				);
			case 'constraints.max_length':
				return $this->createConstraint(
					InputConstraints\MaxLength::class,
					$prototype_properties + ['unicode' => $this->unicode],
					$properties
				);
			case 'constraints.length_range':
				return $this->createConstraint(
					InputConstraints\LengthRange::class,
					$prototype_properties + ['unicode' => $this->unicode],
					$properties
				);
			case 'constraints.lowercase':
				return $this->createConstraint(
					InputConstraints\Lowercase::class,
					$prototype_properties + ['unicode' => $this->unicode],
					$properties
				);
			case 'constraints.uppercase':
				return $this->createConstraint(
					InputConstraints\Uppercase::class,
					$prototype_properties + ['unicode' => $this->unicode],
					$properties
				);
			case 'constraints.alphabetical':
				return $this->createConstraint(
					Constraints\Alphabetical::class,
					$prototype_properties + ['unicode' => $this->unicode],
					$properties
				);
			case 'constraints.numerical':
				return $this->createConstraint(
					Constraints\Numerical::class, $prototype_properties + ['unicode' => $this->unicode], $properties
				);
			case 'constraints.alphanumerical':
				return $this->createConstraint(
					Constraints\Alphanumerical::class,
					$prototype_properties + ['unicode' => $this->unicode],
					$properties
				);
			case 'constraints.identifier':
				return $this->createConstraint(Constraints\Identifier::class, $prototype_properties, $properties);
			case 'constraints.hexadecimal':
				return $this->createConstraint(Constraints\Hexadecimal::class, $prototype_properties, $properties);
			
			//filters
			case 'filters.lowercase':
				return $this->createFilter(
					InputFilters\Lowercase::class, $prototype_properties + ['unicode' => $this->unicode], $properties
				);
			case 'filters.uppercase':
				return $this->createFilter(
					InputFilters\Uppercase::class, $prototype_properties + ['unicode' => $this->unicode], $properties
				);
			case 'filters.truncate':
				return $this->createFilter(
					Filters\Truncate::class, $prototype_properties + ['unicode' => $this->unicode], $properties
				);
		}
		return null;
	}
}
