<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs;

use Dracodeum\Kit\Prototypes\Input;
use Dracodeum\Kit\Prototypes\Input\Interfaces\{
	Information as IInformation,
	SchemaData as ISchemaData,
	ConstraintProducer as IConstraintProducer,
	FilterProducer as IFilterProducer
};
use Dracodeum\Kit\Prototypes\Inputs\Text\{
	Constraints,
	Filters
};
use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\{
	Constraints as InputConstraints,
	Filters as InputFilters
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Components\Input\Options\Info as InfoOptions;
use Dracodeum\Kit\Enumerations\{
	InfoScope as EInfoScope,
	TextCase as ETextCase
};
use Dracodeum\Kit\Root\Locale;
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This input prototype represents a text or string.
 * 
 * Only an integer, float or string may be evaluated as a text or string.
 * 
 * @property-write bool $unicode [writeonce] [transient] [coercive] [default = false]
 * <p>Set as Unicode text.</p>
 * @property-write bool $trim [writeonce] [transient] [coercive] [default = false]
 * <p>Trim the given text or string from whitespace.</p>
 * @see https://en.wikipedia.org/wiki/Plain_text
 * @see https://en.wikipedia.org/wiki/String_(computer_science)
 * @see \Dracodeum\Kit\Prototypes\Inputs\Text\Constraints\Values
 * [constraint, name = 'values' or 'non_values']
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraints\Wildcards
 * [constraint, name = 'wildcards' or 'non_wildcards']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Text\Constraints\NonEmpty
 * [constraint, name = 'non_empty']
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraints\Length
 * [constraint, name = 'length']
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraints\MinLength
 * [constraint, name = 'min_length']
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraints\MaxLength
 * [constraint, name = 'max_length']
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraints\LengthRange
 * [constraint, name = 'length_range']
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraints\Lowercase
 * [constraint, name = 'lowercase']
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraints\Uppercase
 * [constraint, name = 'uppercase']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Text\Constraints\Alphabetical
 * [constraint, name = 'alphabetical' or 'alphabetic' or 'lower_alphabetical' or 'lower_alphabetic' 
 * or 'upper_alphabetical' or 'upper_alphabetic']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Text\Constraints\Numerical
 * [constraint, name = 'numerical' or 'numeric']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Text\Constraints\Alphanumerical
 * [constraint, name = 'alphanumerical' or 'alphanumeric' or 'lower_alphanumerical' or 'lower_alphanumeric'
 * or 'upper_alphanumerical' or 'upper_alphanumeric']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Text\Constraints\Identifier
 * [constraint, name = 'identifier' or 'lower_identifier' or 'upper_identifier']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Text\Constraints\Hexadecimal
 * [constraint, name = 'hexadecimal']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Text\Constraints\Base64
 * [constraint, name = 'base64']
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Filters\Lowercase
 * [filter, name = 'lowercase']
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Filters\Uppercase
 * [filter, name = 'uppercase']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Text\Filters\Truncate
 * [filter, name = 'truncate']
 */
class Text extends Input implements IInformation, ISchemaData, IConstraintProducer, IFilterProducer
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
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\Information)
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
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'unicode' => $this->unicode,
			'trim' => $this->trim
		];
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\ConstraintProducer)
	/** {@inheritdoc} */
	public function produceConstraint(string $name, array $properties)
	{
		switch ($name) {
			case 'values':
				return $this->createConstraint(Constraints\Values::class, $properties + ['unicode' => $this->unicode]);
			case 'non_values':
				return $this->createConstraint(
					Constraints\Values::class, ['negate' => true] + $properties + ['unicode' => $this->unicode]
				);
			case 'wildcards':
				return InputConstraints\Wildcards::class;
			case 'non_wildcards':
				return $this->createConstraint(InputConstraints\Wildcards::class, ['negate' => true] + $properties);
			case 'non_empty':
				return Constraints\NonEmpty::class;
			case 'length':
				return $this->createConstraint(
					InputConstraints\Length::class, $properties + ['unicode' => $this->unicode]
				);
			case 'min_length':
				return $this->createConstraint(
					InputConstraints\MinLength::class, $properties + ['unicode' => $this->unicode]
				);
			case 'max_length':
				return $this->createConstraint(
					InputConstraints\MaxLength::class, $properties + ['unicode' => $this->unicode]
				);
			case 'length_range':
				return $this->createConstraint(
					InputConstraints\LengthRange::class, $properties + ['unicode' => $this->unicode]
				);
			case 'lowercase':
				return $this->createConstraint(
					InputConstraints\Lowercase::class, $properties + ['unicode' => $this->unicode]
				);
			case 'uppercase':
				return $this->createConstraint(
					InputConstraints\Uppercase::class, $properties + ['unicode' => $this->unicode]
				);
			case 'alphabetical':
				//no break
			case 'alphabetic':
				return $this->createConstraint(
					Constraints\Alphabetical::class, $properties + ['unicode' => $this->unicode]
				);
			case 'lower_alphabetical':
				//no break
			case 'lower_alphabetic':
				return $this->createConstraint(
					Constraints\Alphabetical::class,
					['case' => ETextCase::LOWER] + $properties + ['unicode' => $this->unicode]
				);
			case 'upper_alphabetical':
				//no break
			case 'upper_alphabetic':
				return $this->createConstraint(
					Constraints\Alphabetical::class,
					['case' => ETextCase::UPPER] + $properties + ['unicode' => $this->unicode]
				);
			case 'numerical':
				//no break
			case 'numeric':
				return $this->createConstraint(
					Constraints\Numerical::class, $properties + ['unicode' => $this->unicode]
				);
			case 'alphanumerical':
				//no break
			case 'alphanumeric':
				return $this->createConstraint(
					Constraints\Alphanumerical::class, $properties + ['unicode' => $this->unicode]
				);
			case 'lower_alphanumerical':
				//no break
			case 'lower_alphanumeric':
				return $this->createConstraint(
					Constraints\Alphanumerical::class,
					['case' => ETextCase::LOWER] + $properties + ['unicode' => $this->unicode]
				);
			case 'upper_alphanumerical':
				//no break
			case 'upper_alphanumeric':
				return $this->createConstraint(
					Constraints\Alphanumerical::class,
					['case' => ETextCase::UPPER] + $properties + ['unicode' => $this->unicode]
				);
			case 'identifier':
				return Constraints\Identifier::class;
			case 'lower_identifier':
				return $this->createConstraint(
					Constraints\Identifier::class, ['case' => ETextCase::LOWER] + $properties
				);
			case 'upper_identifier':
				return $this->createConstraint(
					Constraints\Identifier::class, ['case' => ETextCase::UPPER] + $properties
				);
			case 'hexadecimal':
				return Constraints\Hexadecimal::class;
			case 'base64':
				return Constraints\Base64::class;
		}
		return null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\FilterProducer)
	/** {@inheritdoc} */
	public function produceFilter(string $name, array $properties)
	{
		switch ($name) {
			case 'lowercase':
				return $this->createFilter(InputFilters\Lowercase::class, $properties + ['unicode' => $this->unicode]);
			case 'uppercase':
				return $this->createFilter(InputFilters\Uppercase::class, $properties + ['unicode' => $this->unicode]);
			case 'truncate':
				return $this->createFilter(Filters\Truncate::class, $properties + ['unicode' => $this->unicode]);
		}
		return null;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'unicode':
				//no break
			case 'trim':
				return $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
