<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Information as IInformation,
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
 * This constraint prototype restricts a text or string to an identifier format.
 * 
 * @since 1.0.0
 * @property bool $extended [default = false]
 * <p>Allow an extended format, where dots may be used as delimiters between words to represent pointers.</p>
 * @see \Feralygon\Kit\Prototypes\Inputs\Text
 */
class Identifier extends Constraint implements IName, IInformation, ISchemaData
{
	//Private properties
	/** @var bool */
	private $extended = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		return UType::evaluateString($value) && UText::isIdentifier($value, $this->extended);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Name)
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'constraints.identifier';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return $this->extended
			? UText::localize("Extended identifier format", self::class, $text_options)
			: UText::localize("Identifier format", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//entries
		$entries = [];
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			//first
			/**
			 * @description Format description first entry.
			 * @placeholder letters.a The lowercase "a" letter character.
			 * @placeholder letters.z The lowercase "z" letter character.
			 * @placeholder letters.A The uppercase "A" letter character.
			 * @placeholder letters.Z The uppercase "Z" letter character.
			 * @placeholder underscore The underscore "_" character.
			 * @tags end-user
			 * @example must start with a letter (a to z and A to Z) or underscore (_)
			 */
			$entries[] = UText::localize(
				"must start with a letter ({{letters.a}} to {{letters.z}} and {{letters.A}} to {{letters.Z}}) " . 
					"or underscore ({{underscore}})", 
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
						'underscore' => '_'
					]
				]
			);
			
			//second
			/**
			 * @description Format description second entry.
			 * @placeholder letters.a The lowercase "a" letter character.
			 * @placeholder letters.z The lowercase "z" letter character.
			 * @placeholder letters.A The uppercase "A" letter character.
			 * @placeholder letters.Z The uppercase "Z" letter character.
			 * @placeholder digits.num0 The numeric "0" digit character.
			 * @placeholder digits.num9 The numeric "9" digit character.
			 * @placeholder underscore The underscore "_" character.
			 * @tags end-user
			 * @example must be only composed by letters (a to z and A to Z), digits (0 to 9) and underscores (_)
			 */
			$entries[] = UText::localize(
				"must be only composed by letters ({{letters.a}} to {{letters.z}} " . 
					"and {{letters.A}} to {{letters.Z}}), digits ({{digits.num0}} to {{digits.num9}}) " . 
					"and underscores ({{underscore}})",
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
						'digits' => ['num0' => '0', 'num9' => '9'],
						'underscore' => '_'
					]
				]
			);
		} else {
			//first
			/**
			 * @description Format description first entry.
			 * @placeholder letters.a The lowercase "a" letter character.
			 * @placeholder letters.z The lowercase "z" letter character.
			 * @placeholder letters.A The uppercase "A" letter character.
			 * @placeholder letters.Z The uppercase "Z" letter character.
			 * @placeholder underscore The underscore "_" character.
			 * @tags non-end-user
			 * @example must start with an ASCII letter (a-z and A-Z) or underscore (_)
			 */
			$entries[] = UText::localize(
				"must start with an ASCII letter ({{letters.a}}-{{letters.z}} and {{letters.A}}-{{letters.Z}}) " . 
					"or underscore ({{underscore}})",
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
						'underscore' => '_'
					]
				]
			);
			
			//second
			/**
			 * @description Format description second entry.
			 * @placeholder letters.a The lowercase "a" letter character.
			 * @placeholder letters.z The lowercase "z" letter character.
			 * @placeholder letters.A The uppercase "A" letter character.
			 * @placeholder letters.Z The uppercase "Z" letter character.
			 * @placeholder digits.num0 The numeric "0" digit character.
			 * @placeholder digits.num9 The numeric "9" digit character.
			 * @placeholder underscore The underscore "_" character.
			 * @tags non-end-user
			 * @example must be exclusively composed by ASCII letters (a-z and A-Z), digits (0-9) and underscores (_)
			 */
			$entries[] = UText::localize(
				"must be exclusively composed by ASCII letters ({{letters.a}}-{{letters.z}} " . 
					"and {{letters.A}}-{{letters.Z}}), digits ({{digits.num0}}-{{digits.num9}}) " . 
					"and underscores ({{underscore}})",
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
						'digits' => ['num0' => '0', 'num9' => '9'],
						'underscore' => '_'
					]
				]
			);
		}
		
		//extended entry
		if ($this->extended) {
			if ($text_options->info_scope === EInfoScope::ENDUSER) {
				/**
				 * @description Format description third entry.
				 * @placeholder dot The dot "." character.
				 * @tags end-user
				 * @example dots (.) may also be used as delimiters between words
				 */
				$entries[] = UText::localize(
					"dots ({{dot}}) may also be used as delimiters between words",
					self::class, $text_options, ['parameters' => ['dot' => '.']]
				);
			} else {
				/**
				 * @description Format description third entry.
				 * @placeholder dot The dot "." character.
				 * @tags non-end-user
				 * @example dots (.) may also be used as delimiters between words to represent pointers
				 */
				$entries[] = UText::localize(
					"dots ({{dot}}) may also be used as delimiters between words to represent pointers",
					self::class, $text_options, ['parameters' => ['dot' => '.']]
				);
			}
		}
		
		//message
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder entries The format description entries.
			 * @tags end-user
			 * @example Only the following format is allowed:
			 *  &#8226; must start with a letter (a to z and A to Z) or underscore (_);
			 *  &#8226; must be only composed by letters (a to z and A to Z), digits (0 to 9) and underscores (_);
			 *  &#8226; dots (.) may also be used as delimiters between words.
			 */
			return UText::localize(
				"Only the following format is allowed:\n{{entries}}",
				self::class, $text_options, [
					'parameters' => [
						'entries' => UText::mbulletify($entries, $text_options, ['merge' => true, 'punctuate' => true])
					]
				]
			);
		}
		/**
		 * @placeholder entries The format description entries.
		 * @tags non-end-user
		 * @example Only an identifier with the following format is allowed:
		 *  &#8226; must start with an ASCII letter (a-z and A-Z) or underscore (_);
		 *  &#8226; must be exclusively composed by ASCII letters (a-z and A-Z), digits (0-9) and underscores (_);
		 *  &#8226; dots (.) may also be used as delimiters between words to represent pointers.
		 */
		return UText::localize(
			"Only an identifier with the following format is allowed:\n{{entries}}",
			self::class, $text_options, [
				'parameters' => [
					'entries' => UText::mbulletify($entries, $text_options, ['merge' => true, 'punctuate' => true])
				]
			]
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'extended' => $this->extended
		];
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'extended':
				return $this->createProperty()->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
