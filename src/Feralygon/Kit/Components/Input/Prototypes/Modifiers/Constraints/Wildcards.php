<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This constraint prototype restricts a value to a set of allowed wildcard matches.
 * 
 * @since 1.0.0
 * @property string[] $wildcards
 * <p>The allowed wildcard matches to restrict a given value to.</p>
 * @property bool $insensitive [default = false]
 * <p>Match the given wildcards in a case-insensitive manner.</p>
 * @property bool $negate [default = false]
 * <p>Negate the restriction, so the given allowed wildcard matches act as disallowed wildcard matches instead.</p>
 */
class Wildcards extends Constraint implements IName, IInformation, IStringification, ISchemaData
{
	//Private properties
	/** @var string[] */
	private $wildcards;
	
	/** @var bool */
	private $insensitive = false;
	
	/** @var bool */
	private $negate = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		return UText::isAnyWildcardsMatch($value, $this->wildcards, $this->insensitive) !== $this->negate;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Name)
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'constraints.wildcards';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		//negate
		if ($this->negate) {
			//end-user
			if ($text_options->info_scope === EInfoScope::ENDUSER) {
				/** @tags end-user */
				return UText::plocalize(
					"Disallowed match", "Disallowed matches",
					count($this->wildcards), null, self::class, $text_options
				);
			}
			
			//non-end-user
			/** @tags non-end-user */
			return UText::plocalize(
				"Disallowed wildcard match", "Disallowed wildcard matches",
				count($this->wildcards), null, self::class, $text_options
			);
		}
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/** @tags end-user */
			return UText::plocalize(
				"Allowed match", "Allowed matches",
				count($this->wildcards), null, self::class, $text_options
			);
		}
		
		//non-end-user
		/** @tags non-end-user */
		return UText::plocalize(
			"Allowed wildcard match", "Allowed wildcard matches",
			count($this->wildcards), null, self::class, $text_options
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//message
		$message = '';
		if ($this->negate) {
			if ($text_options->info_scope === EInfoScope::ENDUSER) {
				/**
				 * @placeholder wildcards The list of disallowed wildcard matches.
				 * @tags end-user
				 * @example The following matches are not allowed: "*foo", "bar*" and "*abc*".
				 */
				$message = UText::plocalize(
					"The following match is not allowed: {{wildcards}}.",
					"The following matches are not allowed: {{wildcards}}.",
					count($this->wildcards), null, self::class, $text_options, [
						'parameters' => ['wildcards' => $this->getString($text_options)]
					]
				);
			} else {
				/**
				 * @placeholder wildcards The list of disallowed wildcard matches.
				 * @tags non-end-user
				 * @example The following wildcard matches are not allowed: "*foo", "bar*" and "*abc*".
				 */
				$message = UText::plocalize(
					"The following wildcard match is not allowed: {{wildcards}}.",
					"The following wildcard matches are not allowed: {{wildcards}}.",
					count($this->wildcards), null, self::class, $text_options, [
						'parameters' => ['wildcards' => $this->getString($text_options)]
					]
				);
			}
		} elseif ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder wildcards The list of allowed wildcard matches.
			 * @tags end-user
			 * @example Only the following matches are allowed: "*foo", "bar*" and "*abc*".
			 */
			$message = UText::plocalize(
				"Only the following match is allowed: {{wildcards}}.",
				"Only the following matches are allowed: {{wildcards}}.",
				count($this->wildcards), null, self::class, $text_options, [
					'parameters' => ['wildcards' => $this->getString($text_options)]
				]
			);
		} else {
			/**
			 * @placeholder wildcards The list of allowed wildcard matches.
			 * @tags non-end-user
			 * @example Only the following wildcard matches are allowed: "*foo", "bar*" and "*abc*".
			 */
			$message = UText::plocalize(
				"Only the following wildcard match is allowed: {{wildcards}}.",
				"Only the following wildcard matches are allowed: {{wildcards}}.",
				count($this->wildcards), null, self::class, $text_options, [
					'parameters' => ['wildcards' => $this->getString($text_options)]
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
				$message .= UText::localize(
					"All matches are case-insensitive.", self::class, $text_options
				);
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
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options): string
	{
		return UText::stringify($this->wildcards, $text_options, [
			'quote_strings' => true,
			'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND
		]);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'wildcards' => $this->wildcards,
			'insensitive' => $this->insensitive,
			'negate' => $this->negate
		];
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\RequiredPropertyNamesLoader)
	/** {@inheritdoc} */
	protected function loadRequiredPropertyNames(): void
	{
		$this->addRequiredPropertyNames(['wildcards']);
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'wildcards':
				return $this->createProperty()
					->setAsArray(function (&$key, &$value): bool {
						return UType::evaluateString($value);
					}, true, true)
					->bind(self::class)
				;
			case 'insensitive':
				//no break
			case 'negate':
				return $this->createProperty()->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
