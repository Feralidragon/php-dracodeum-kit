<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs\Text\Constraints;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Subtype as ISubtype,
	Information as IInformation,
	SchemaData as ISchemaData
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Enumerations\InfoScope as EInfoScope;
use Dracodeum\Kit\Utilities\{
	Base64 as UBase64,
	Text as UText,
	Type as UType
};

/**
 * This constraint prototype restricts a given text input value to a Base64 format.
 * 
 * @property-write bool|null $url_safe [writeonce] [transient] [coercive] [default = null]
 * <p>Allow or disallow the URL-safe format only, in which the plus signs (<samp>+</samp>) and slashes (<samp>/</samp>) 
 * are replaced by hyphens (<samp>-</samp>) and underscores (<samp>_</samp>) respectively, 
 * with the padding equal signs (<samp>=</samp>) removed, in order to be safely put in an URL.<br>
 * If not set, then any format is allowed.</p>
 */
class Base64 extends Constraint implements ISubtype, IInformation, ISchemaData
{
	//Protected properties
	/** @var bool|null */
	protected $url_safe = null;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'base64';
	}
	
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		return UType::evaluateString($value) && UBase64::encoded($value, $this->url_safe);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'text';
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return $this->url_safe === true
			? UText::localize("URL-safe Base64 format", self::class, $text_options)
			: UText::localize("Base64 format", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//non-url-safe
		if ($this->url_safe === false) {
			//end-user
			if ($text_options->info_scope === EInfoScope::ENDUSER) {
				/**
				 * @placeholder letters.a The lowercase "a" letter character.
				 * @placeholder letters.z The lowercase "z" letter character.
				 * @placeholder letters.A The uppercase "A" letter character.
				 * @placeholder letters.Z The uppercase "Z" letter character.
				 * @placeholder digits.num0 The numeric "0" digit character.
				 * @placeholder digits.num9 The numeric "9" digit character.
				 * @placeholder signs.plus The plus "+" sign character.
				 * @placeholder signs.equal The equal "=" sign character.
				 * @placeholder slash The slash "/" character.
				 * @tags end-user
				 * @example Only the Base64 format is allowed, \
				 * consisting only of alphanumeric (a-z, A-Z and 0-9), \
				 * plus sign (+) and slash (/) characters, optionally ending with equal signs (=), \
				 * as groups of 2 to 4 characters.
				 */
				return UText::localize(
					"Only the Base64 format is allowed, " . 
						"consisting only of alphanumeric ({{letters.a}}-{{letters.z}}, " . 
						"{{letters.A}}-{{letters.Z}} and {{digits.num0}}-{{digits.num9}}), " . 
						"plus sign ({{signs.plus}}) and slash ({{slash}}) characters, " . 
						"optionally ending with equal signs ({{signs.equal}}), as groups of 2 to 4 characters.",
					self::class, $text_options, [
						'parameters' => [
							'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
							'digits' => ['num0' => '0', 'num9' => '9'],
							'signs' => ['plus' => '+', 'equal' => '='],
							'slash' => '/'
						]
					]
				);
			}
			
			//non-end-user
			/**
			 * @placeholder letters.a The lowercase "a" letter character.
			 * @placeholder letters.z The lowercase "z" letter character.
			 * @placeholder letters.A The uppercase "A" letter character.
			 * @placeholder letters.Z The uppercase "Z" letter character.
			 * @placeholder digits.num0 The numeric "0" digit character.
			 * @placeholder digits.num9 The numeric "9" digit character.
			 * @placeholder signs.plus The plus "+" sign character.
			 * @placeholder signs.equal The equal "=" sign character.
			 * @placeholder slash The slash "/" character.
			 * @tags non-end-user
			 * @example Only the Base64 format is allowed, \
			 * consisting only of ASCII alphanumeric (a-z, A-Z and 0-9), \
			 * plus sign (+) and slash (/) characters, optionally padded with equal signs (=), \
			 * as groups of 2 to 4 characters.
			 */
			return UText::localize(
				"Only the Base64 format is allowed, " . 
					"consisting only of ASCII alphanumeric ({{letters.a}}-{{letters.z}}, " . 
					"{{letters.A}}-{{letters.Z}} and {{digits.num0}}-{{digits.num9}}), " . 
					"plus sign ({{signs.plus}}) and slash ({{slash}}) characters, " . 
					"optionally padded with equal signs ({{signs.equal}}), as groups of 2 to 4 characters.",
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
						'digits' => ['num0' => '0', 'num9' => '9'],
						'signs' => ['plus' => '+', 'equal' => '='],
						'slash' => '/'
					]
				]
			);
			
		//url-safe
		} elseif ($this->url_safe === true) {
			//end-user
			if ($text_options->info_scope === EInfoScope::ENDUSER) {
				/**
				 * @placeholder letters.a The lowercase "a" letter character.
				 * @placeholder letters.z The lowercase "z" letter character.
				 * @placeholder letters.A The uppercase "A" letter character.
				 * @placeholder letters.Z The uppercase "Z" letter character.
				 * @placeholder digits.num0 The numeric "0" digit character.
				 * @placeholder digits.num9 The numeric "9" digit character.
				 * @placeholder hyphen The hyphen "-" character.
				 * @placeholder underscore The underscore "_" character.
				 * @tags end-user
				 * @example Only the URL-safe Base64 format is allowed, \
				 * consisting only of alphanumeric (a-z, A-Z and 0-9), hyphen (-) and underscore (_) characters, \
				 * as groups of 2 to 4 characters.
				 */
				return UText::localize(
					"Only the URL-safe Base64 format is allowed, " . 
						"consisting only of alphanumeric ({{letters.a}}-{{letters.z}}, " . 
						"{{letters.A}}-{{letters.Z}} and {{digits.num0}}-{{digits.num9}}), " . 
						"hyphen ({{hyphen}}) and underscore ({{underscore}}) characters, " . 
						"as groups of 2 to 4 characters.",
					self::class, $text_options, [
						'parameters' => [
							'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
							'digits' => ['num0' => '0', 'num9' => '9'],
							'hyphen' => '-',
							'underscore' => '_'
						]
					]
				);
			}
			
			//non-end-user
			/**
			 * @placeholder letters.a The lowercase "a" letter character.
			 * @placeholder letters.z The lowercase "z" letter character.
			 * @placeholder letters.A The uppercase "A" letter character.
			 * @placeholder letters.Z The uppercase "Z" letter character.
			 * @placeholder digits.num0 The numeric "0" digit character.
			 * @placeholder digits.num9 The numeric "9" digit character.
			 * @placeholder hyphen The hyphen "-" character.
			 * @placeholder underscore The underscore "_" character.
			 * @tags non-end-user
			 * @example Only the URL-safe Base64 format is allowed, \
			 * consisting only of ASCII alphanumeric (a-z, A-Z and 0-9), hyphen (-) and underscore (_) characters, \
			 * as groups of 2 to 4 characters.
			 */
			return UText::localize(
				"Only the URL-safe Base64 format is allowed, " . 
					"consisting only of ASCII alphanumeric ({{letters.a}}-{{letters.z}}, " . 
					"{{letters.A}}-{{letters.Z}} and {{digits.num0}}-{{digits.num9}}), " . 
					"hyphen ({{hyphen}}) and underscore ({{underscore}}) characters, as groups of 2 to 4 characters.",
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
						'digits' => ['num0' => '0', 'num9' => '9'],
						'hyphen' => '-',
						'underscore' => '_'
					]
				]
			);
		}
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder letters.a The lowercase "a" letter character.
			 * @placeholder letters.z The lowercase "z" letter character.
			 * @placeholder letters.A The uppercase "A" letter character.
			 * @placeholder letters.Z The uppercase "Z" letter character.
			 * @placeholder digits.num0 The numeric "0" digit character.
			 * @placeholder digits.num9 The numeric "9" digit character.
			 * @placeholder signs.plus The plus "+" sign character.
			 * @placeholder signs.equal The equal "=" sign character.
			 * @placeholder slash The slash "/" character.
			 * @placeholder hyphen The hyphen "-" character.
			 * @placeholder underscore The underscore "_" character.
			 * @tags end-user
			 * @example Only the Base64 or URL-safe Base64 format is allowed, \
			 * consisting only of alphanumeric characters (a-z, A-Z and 0-9), \
			 * and also plus signs (+) and slashes (/), optionally ending with equal signs (=), in the case of Base64, \
			 * or hyphens (-) and underscores (_) respectively, without equal signs, in the case of URL-safe Base64, \
			 * as groups of 2 to 4 characters.
			 */
			return UText::localize(
				"Only the Base64 or URL-safe Base64 format is allowed, " . 
					"consisting only of alphanumeric characters ({{letters.a}}-{{letters.z}}, " . 
					"{{letters.A}}-{{letters.Z}} and {{digits.num0}}-{{digits.num9}}), " . 
					"and also plus signs ({{signs.plus}}) and slashes ({{slash}}), " . 
					"optionally ending with equal signs ({{signs.equal}}), in the case of Base64, " . 
					"or hyphens ({{hyphen}}) and underscores ({{underscore}}) respectively, " . 
					"without equal signs, in the case of URL-safe Base64, as groups of 2 to 4 characters.",
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
						'digits' => ['num0' => '0', 'num9' => '9'],
						'signs' => ['plus' => '+', 'equal' => '='],
						'slash' => '/',
						'hyphen' => '-',
						'underscore' => '_'
					]
				]
			);
		}
		
		//non-end-user
		/**
		 * @placeholder letters.a The lowercase "a" letter character.
		 * @placeholder letters.z The lowercase "z" letter character.
		 * @placeholder letters.A The uppercase "A" letter character.
		 * @placeholder letters.Z The uppercase "Z" letter character.
		 * @placeholder digits.num0 The numeric "0" digit character.
		 * @placeholder digits.num9 The numeric "9" digit character.
		 * @placeholder signs.plus The plus "+" sign character.
		 * @placeholder signs.equal The equal "=" sign character.
		 * @placeholder slash The slash "/" character.
		 * @placeholder hyphen The hyphen "-" character.
		 * @placeholder underscore The underscore "_" character.
		 * @tags non-end-user
		 * @example Only the Base64 or URL-safe Base64 format is allowed, \
		 * consisting only of ASCII alphanumeric characters (a-z, A-Z and 0-9), \
		 * and also plus signs (+) and slashes (/), optionally padded with equal signs (=), in the case of Base64, \
		 * or hyphens (-) and underscores (_) respectively, without any padding, in the case of URL-safe Base64, \
		 * as groups of 2 to 4 characters.
		 */
		return UText::localize(
			"Only the Base64 or URL-safe Base64 format is allowed, " . 
				"consisting only of ASCII alphanumeric characters ({{letters.a}}-{{letters.z}}, " . 
				"{{letters.A}}-{{letters.Z}} and {{digits.num0}}-{{digits.num9}}), " . 
				"and also plus signs ({{signs.plus}}) and slashes ({{slash}}), " . 
				"optionally padded with equal signs ({{signs.equal}}), in the case of Base64, " . 
				"or hyphens ({{hyphen}}) and underscores ({{underscore}}) respectively, " . 
				"without any padding, in the case of URL-safe Base64, as groups of 2 to 4 characters.",
			self::class, $text_options, [
				'parameters' => [
					'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
					'digits' => ['num0' => '0', 'num9' => '9'],
					'signs' => ['plus' => '+', 'equal' => '='],
					'slash' => '/',
					'hyphen' => '-',
					'underscore' => '_'
				]
			]
		);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'url_safe' => $this->url_safe
		];
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'url_safe':
				return $this->createProperty()->setMode('w--')->setAsBoolean(true)->bind(self::class);
		}
		return null;
	}
}
