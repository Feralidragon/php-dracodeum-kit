<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraints;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Enumerations\InfoScope as EInfoScope;
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This constraint prototype restricts a given input value to a set of allowed wildcard matches.
 * 
 * @property-write string[] $values [writeonce] [transient] [coercive]
 * <p>The allowed wildcard match values to restrict a given input value to.</p>
 * @property-write bool $insensitive [writeonce] [transient] [coercive] [default = false]
 * <p>Match the given wildcard match values in a case-insensitive manner.</p>
 * @property-write bool $negate [writeonce] [transient] [coercive] [default = false]
 * <p>Negate the restriction condition, 
 * so the given allowed wildcard match values behave as disallowed wildcard match values instead.</p>
 */
class Wildcards extends Constraint implements IInformation, IStringification, ISchemaData
{
	//Protected properties
	/** @var string[] */
	protected $values;
	
	/** @var bool */
	protected $insensitive = false;
	
	/** @var bool */
	protected $negate = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'wildcards';
	}
	
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		return UType::evaluateString($value) && 
			UText::anyWildcardsMatch($value, $this->values, $this->insensitive) !== $this->negate;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		//label
		$label = '';
		if ($this->negate) {
			if ($text_options->info_scope === EInfoScope::ENDUSER) {
				/** @tags end-user */
				$label = UText::plocalize(
					"Disallowed match", "Disallowed matches",
					count($this->values), null, self::class, $text_options
				);
			} else {
				/** @tags non-end-user */
				$label = UText::plocalize(
					"Disallowed wildcard match", "Disallowed wildcard matches",
					count($this->values), null, self::class, $text_options
				);
			}
		} elseif ($text_options->info_scope === EInfoScope::ENDUSER) {
			/** @tags end-user */
			$label = UText::plocalize(
				"Allowed match", "Allowed matches",
				count($this->values), null, self::class, $text_options
			);
		} else {
			/** @tags non-end-user */
			$label = UText::plocalize(
				"Allowed wildcard match", "Allowed wildcard matches",
				count($this->values), null, self::class, $text_options
			);
		}
		
		//insensitive
		if ($this->insensitive) {
			/**
			 * @placeholder label The label.
			 * @example Allowed match (case-insensitive)
			 */
			$label = UText::localize(
				"{{label}} (case-insensitive)", self::class, $text_options, ['parameters' => ['label' => $label]]
			);
		}
		
		//return
		return $label;
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//message
		$message = '';
		if ($this->negate) {
			if ($text_options->info_scope === EInfoScope::ENDUSER) {
				/**
				 * @placeholder values The list of disallowed wildcard match values.
				 * @tags end-user
				 * @example The following matches are not allowed: "*foo", "bar*" and "*abc*".
				 */
				$message = UText::plocalize(
					"The following match is not allowed: {{values}}.",
					"The following matches are not allowed: {{values}}.",
					count($this->values), null, self::class, $text_options, [
						'parameters' => ['values' => $this->getString($text_options)]
					]
				);
			} else {
				/**
				 * @placeholder values The list of disallowed wildcard match values.
				 * @tags non-end-user
				 * @example The following wildcard matches are not allowed: "*foo", "bar*" and "*abc*".
				 */
				$message = UText::plocalize(
					"The following wildcard match is not allowed: {{values}}.",
					"The following wildcard matches are not allowed: {{values}}.",
					count($this->values), null, self::class, $text_options, [
						'parameters' => ['values' => $this->getString($text_options)]
					]
				);
			}
		} elseif ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder values The list of allowed wildcard match values.
			 * @tags end-user
			 * @example Only the following matches are allowed: "*foo", "bar*" or "*abc*".
			 */
			$message = UText::plocalize(
				"Only the following match is allowed: {{values}}.",
				"Only the following matches are allowed: {{values}}.",
				count($this->values), null, self::class, $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		} else {
			/**
			 * @placeholder values The list of allowed wildcard match values.
			 * @tags non-end-user
			 * @example Only the following wildcard matches are allowed: "*foo", "bar*" or "*abc*".
			 */
			$message = UText::plocalize(
				"Only the following wildcard match is allowed: {{values}}.",
				"Only the following wildcard matches are allowed: {{values}}.",
				count($this->values), null, self::class, $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		
		//additional details
		$message .= "\n";
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder wildcard The wildcard "*" character.
			 * @tags end-user
			 * @example The character "*" matches any characters.
			 */
			$message .= UText::localize(
				"The character {{wildcard}} matches any characters.",
				self::class, $text_options, [
					'parameters' => ['wildcard' => '*'],
					'string_options' => ['quote_strings' => true]
				]
			);
		} else {
			/**
			 * @placeholder wildcard The wildcard "*" character.
			 * @tags non-end-user
			 * @example The wildcard character "*" matches any number and type of characters.
			 */
			$message .= UText::localize(
				"The wildcard character {{wildcard}} matches any number and type of characters.",
				self::class, $text_options, [
					'parameters' => ['wildcard' => '*'],
					'string_options' => ['quote_strings' => true]
				]
			);
		}
		
		//insensitive
		if ($this->insensitive) {
			$message .= "\n";
			if ($text_options->info_scope === EInfoScope::ENDUSER) {
				/** @tags end-user */
				$message .= UText::localize("All matches are case-insensitive.", self::class, $text_options);
			} else {
				/** @tags non-end-user */
				$message .= UText::localize(
					"All wildcard matches are performed in a case-insensitive manner.", self::class, $text_options
				);
			}
		}
		
		//return
		return $message;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options): string
	{
		return UText::commify($this->values, $text_options, $this->negate ? 'and' : 'or', true);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'values' => $this->values,
			'insensitive' => $this->insensitive,
			'negate' => $this->negate
		];
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertiesInitializer)
	/** {@inheritdoc} */
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyName('values');
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'values':
				return $this->createProperty()
					->setMode('w--')
					->setAsArray(function (&$key, &$value): bool {
						return UType::evaluateString($value);
					}, true, true)
					->bind(self::class)
				;
			case 'insensitive':
				//no break
			case 'negate':
				return $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
