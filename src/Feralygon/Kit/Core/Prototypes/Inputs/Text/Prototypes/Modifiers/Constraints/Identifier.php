<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Core\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Information as IInformation,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Core\Utilities\Text as UText;

/**
 * Core text input identifier constraint modifier prototype class.
 * 
 * This constraint prototype restricts a text or string to an identifier format.
 * 
 * @since 1.0.0
 * @property bool $extended [default = false] <p>Allow an extended format, 
 * where dots may be used as delimiters between words to represent pointers.</p>
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Text
 */
class Identifier extends Constraint implements IPrototypeProperties, IName, IInformation, ISchemaData
{
	//Private properties	
	/** @var bool */
	private $extended = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value) : bool
	{
		return UText::isIdentifier($value, $this->extended);
	}
	
	
	
	//Implemented public methods (core prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'extended':
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
		return 'constraints.identifier';
	}
	
	
	
	//Implemented public methods (core input modifier prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		return $this->extended
			? UText::localize("Extended identifier format", self::class, $text_options)
			: UText::localize("Identifier format", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
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
	
	
	
	//Implemented public methods (core input modifier prototype schema data interface)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'extended' => $this->extended
		];
	}
}
