<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs;

use Feralygon\Kit\Prototypes\Input;
use Feralygon\Kit\Prototypes\Input\Interfaces\{
	Information as IInformation,
	SchemaData as ISchemaData,
	ModifierBuilder as IModifierBuilder
};
use Feralygon\Kit\Components\Input\Components\Modifier;
use Feralygon\Kit\Prototypes\Inputs\Text\{
	Constraints,
	Filters
};
use Feralygon\Kit\Components\Input\Prototypes\Modifiers\{
	Constraints as InputConstraints,
	Filters as InputFilters
};
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Root\Locale;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This input prototype represents a text or string.
 * 
 * Only an integer, float or string may be evaluated as a text or string.
 * 
 * @since 1.0.0
 * @property-write bool $unicode [writeonce] [coercive] [default = false]
 * <p>Set as Unicode text.</p>
 * @property-write bool $trim [writeonce] [coercive] [default = false]
 * <p>Trim the given text or string from whitespace.</p>
 * @see https://en.wikipedia.org/wiki/Plain_text
 * @see https://en.wikipedia.org/wiki/String_(computer_science)
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Constraints\Values
 * [modifier, name = 'constraints.values' or 'values' or 'constraints.non_values' or 'non_values']
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraints\Wildcards
 * [modifier, name = 'constraints.wildcards' or 'wildcards' or 'constraints.non_wildcards' or 'non_wildcards']
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Constraints\NonEmpty
 * [modifier, name = 'constraints.non_empty' or 'non_empty']
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraints\Length
 * [modifier, name = 'constraints.length' or 'length']
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraints\MinLength
 * [modifier, name = 'constraints.min_length' or 'min_length']
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraints\MaxLength
 * [modifier, name = 'constraints.max_length' or 'max_length']
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraints\LengthRange
 * [modifier, name = 'constraints.length_range' or 'length_range']
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraints\Lowercase
 * [modifier, name = 'constraints.lowercase' or 'lowercase']
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraints\Uppercase
 * [modifier, name = 'constraints.uppercase' or 'uppercase']
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Constraints\Alphabetical
 * [modifier, name = 'constraints.alphabetical' or 'alphabetical' or 'alphabetic']
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Constraints\Numerical
 * [modifier, name = 'constraints.numerical' or 'numerical' or 'numeric']
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Constraints\Alphanumerical
 * [modifier, name = 'constraints.alphanumerical' or 'alphanumerical' or 'alphanumeric']
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Constraints\Identifier
 * [modifier, name = 'constraints.identifier' or 'identifier']
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Constraints\Hexadecimal
 * [modifier, name = 'constraints.hexadecimal' or 'hexadecimal']
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Constraints\Base64
 * [modifier, name = 'constraints.base64' or 'base64']
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filters\Lowercase
 * [modifier, name = 'filters.lowercase' or 'lower']
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filters\Uppercase
 * [modifier, name = 'filters.uppercase' or 'upper']
 * @see \Feralygon\Kit\Prototypes\Inputs\Text\Filters\Truncate
 * [modifier, name = 'filters.truncate' or 'truncate']
 */
class Text extends Input implements IInformation, ISchemaData, IModifierBuilder
{
	//Protected properties
	/** @var bool */
	protected $unicode = false;
	
	/** @var bool */
	protected $trim = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'text';
	}
	
	/** {@inheritdoc} */
	public function isScalar(): bool
	{
		return true;
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value): bool
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
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options): string
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
	public function getDescription(TextOptions $text_options, InfoOptions $info_options): string
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
	public function getMessage(TextOptions $text_options, InfoOptions $info_options): string
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
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'unicode' => $this->unicode,
			'trim' => $this->trim
		];
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\ModifierBuilder)
	/** {@inheritdoc} */
	public function buildModifier(string $name, array $properties): ?Modifier
	{
		switch ($name) {
			//constraints
			case 'constraints.values':
				//no break
			case 'values':
				return $this->createConstraint(Constraints\Values::class, $properties + ['unicode' => $this->unicode]);
			case 'constraints.non_values':
				//no break
			case 'non_values':
				return $this->createConstraint(
					Constraints\Values::class, ['negate' => true] + $properties + ['unicode' => $this->unicode]
				);
			case 'constraints.wildcards':
				//no break
			case 'wildcards':
				return $this->createConstraint(InputConstraints\Wildcards::class, $properties);
			case 'constraints.non_wildcards':
				//no break
			case 'non_wildcards':
				return $this->createConstraint(InputConstraints\Wildcards::class, ['negate' => true] + $properties);
			case 'constraints.non_empty':
				//no break
			case 'non_empty':
				return $this->createConstraint(Constraints\NonEmpty::class, $properties);
			case 'constraints.length':
				//no break
			case 'length':
				return $this->createConstraint(
					InputConstraints\Length::class, $properties + ['unicode' => $this->unicode]
				);
			case 'constraints.min_length':
				//no break
			case 'min_length':
				return $this->createConstraint(
					InputConstraints\MinLength::class, $properties + ['unicode' => $this->unicode]
				);
			case 'constraints.max_length':
				//no break
			case 'max_length':
				return $this->createConstraint(
					InputConstraints\MaxLength::class, $properties + ['unicode' => $this->unicode]
				);
			case 'constraints.length_range':
				//no break
			case 'length_range':
				return $this->createConstraint(
					InputConstraints\LengthRange::class, $properties + ['unicode' => $this->unicode]
				);
			case 'constraints.lowercase':
				//no break
			case 'lowercase':
				return $this->createConstraint(
					InputConstraints\Lowercase::class, $properties + ['unicode' => $this->unicode]
				);
			case 'constraints.uppercase':
				//no break
			case 'uppercase':
				return $this->createConstraint(
					InputConstraints\Uppercase::class, $properties + ['unicode' => $this->unicode]
				);
			case 'constraints.alphabetical':
				//no break
			case 'alphabetical':
				//no break
			case 'alphabetic':
				return $this->createConstraint(
					Constraints\Alphabetical::class, $properties + ['unicode' => $this->unicode]
				);
			case 'constraints.numerical':
				//no break
			case 'numerical':
				//no break
			case 'numeric':
				return $this->createConstraint(
					Constraints\Numerical::class, $properties + ['unicode' => $this->unicode]
				);
			case 'constraints.alphanumerical':
				//no break
			case 'alphanumerical':
				//no break
			case 'alphanumeric':
				return $this->createConstraint(
					Constraints\Alphanumerical::class, $properties + ['unicode' => $this->unicode]
				);
			case 'constraints.identifier':
				//no break
			case 'identifier':
				return $this->createConstraint(Constraints\Identifier::class, $properties);
			case 'constraints.hexadecimal':
				//no break
			case 'hexadecimal':
				return $this->createConstraint(Constraints\Hexadecimal::class, $properties);
			case 'constraints.base64':
				//no break
			case 'base64':
				return $this->createConstraint(Constraints\Base64::class, $properties);
			
			//filters
			case 'filters.lowercase':
				//no break
			case 'lower':
				return $this->createFilter(InputFilters\Lowercase::class, $properties + ['unicode' => $this->unicode]);
			case 'filters.uppercase':
				//no break
			case 'upper':
				return $this->createFilter(InputFilters\Uppercase::class, $properties + ['unicode' => $this->unicode]);
			case 'filters.truncate':
				//no break
			case 'truncate':
				return $this->createFilter(Filters\Truncate::class, $properties + ['unicode' => $this->unicode]);
		}
		return null;
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'unicode':
				//no break
			case 'trim':
				return $this->createProperty()->setMode('w-')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
