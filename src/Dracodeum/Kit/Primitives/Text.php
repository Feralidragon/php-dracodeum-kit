<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitives;

use Dracodeum\Kit\Primitive;
use Dracodeum\Kit\Interfaces\{
	Stringable as IStringable,
	StringInstantiable as IStringInstantiable,
	Cloneable as ICloneable
};
use JsonSerializable as IJsonSerializable;
use Dracodeum\Kit\Traits;
use Dracodeum\Kit\Primitives\Text\Exceptions;
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\{
	Call as UCall,
	Text as UText,
	Type as UType
};

/**
 * This primitive represents a text.
 * 
 * This is a simple object which represents and stores a string or set of strings, each to be shown to an end-user or 
 * developer, or to be logged internally, depending on the requested and assigned info level of each string.<br>
 * <br>
 * Each string may also be dynamic and parameterized, such as:<br>
 * &nbsp; &#8226; &nbsp; having placeholders to be replaced with parameters;<br>
 * &nbsp; &#8226; &nbsp; having a plural form;<br>
 * &nbsp; &#8226; &nbsp; supporting localization, to be translated to different languages.
 */
final class Text extends Primitive implements IStringable, IStringInstantiable, ICloneable, IJsonSerializable
{
	//Traits
	use Traits\Stringable;
	use Traits\Cloneable;
	
	
	
	//Private properties
	/** @var string[] */
	private array $strings = [];
	
	/** @var string[] */
	private array $plural_strings = [];
	
	private float $plural_number = 1.0;
	
	private ?string $plural_number_placeholder = null;
	
	private array $parameters = [];
	
	/** @var callable[] */
	private array $placeholders_stringifiers = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param string $string [default = '']
	 * <p>The string to instantiate with.<br>
	 * <br>
	 * Placeholders may optionally be set in the given string as <samp>{{placeholder}}</samp> to be replaced by a 
	 * corresponding set of parameters, and they must be exclusively composed of identifiers, which are defined as 
	 * words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) or underscore (<samp>_</samp>), 
	 * and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), digits (<samp>0-9</samp>) and 
	 * underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also contain pointers to specific object properties or associative array values from the given set of 
	 * parameters by using a dot between identifiers, such as <samp>{{object.property}}</samp>, with no limit on the 
	 * number of pointers chained.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the given pointers are interpreted as getter method calls, but they cannot be given any arguments.</p>
	 * @param coercible:enum(Dracodeum\Kit\Enumerations\InfoLevel) $info_level [default = ENDUSER]
	 * <p>The info level to instantiate with.</p>
	 */
	final public function __construct(string $string = '', $info_level = EInfoLevel::ENDUSER)
	{
		$this->setString($string, $info_level);
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Stringable)
	/** {@inheritdoc} */
	final public function toString(?TextOptions $text_options = null): string
	{
		//initialize
		$info_levels = $text_options !== null
			? array_reverse(range(EInfoLevel::ENDUSER, $text_options->info_level))
			: EInfoLevel::getValues();
		
		//string
		$string = '';
		$plural_string = null;
		foreach ($info_levels as $info_level) {
			if (isset($this->strings[$info_level])) {
				$string = $this->strings[$info_level];
				$plural_string = $this->plural_strings[$info_level] ?? null;
				break;
			}
		}
		
		//process
		if (UText::hasPlaceholders($string)) {
			//initialize
			$fill_text_options = TextOptions::coerce($text_options);
			$fill_stringifier = function (string $placeholder, $value) use ($fill_text_options): ?string {
				return isset($this->placeholders_stringifiers[$placeholder])
					? ($this->placeholders_stringifiers[$placeholder])($value, $fill_text_options)
					: null;
			};
			
			//fill
			$string = $plural_string !== null
				? UText::pfill(
					$string, $plural_string, $this->plural_number, $this->plural_number_placeholder, $this->parameters,
					$fill_text_options, ['stringifier' => $fill_stringifier]
				)
				: UText::fill($string, $this->parameters, $fill_text_options, ['stringifier' => $fill_stringifier]);
			
			//finalize
			unset($fill_text_options, $fill_stringifier);
			
		} elseif ($plural_string !== null && abs($this->plural_number) !== 1.0) {
			$string = $plural_string;
		}
		
		//return
		return $string;
	}
	
	
	
	//Implemented final public static methods (Dracodeum\Kit\Interfaces\StringInstantiable)
	/** {@inheritdoc} */
	final public static function fromString(string $string): object
	{
		return self::build($string);
	}
	
	
	
	//Implemented final public methods (JsonSerializable)
	/** {@inheritdoc} */
	final public function jsonSerialize(): mixed
	{
		return $this->toString();
	}
	
	
	
	//Final public methods
	/**
	 * Set string.
	 * 
	 * @param string $string
	 * <p>The string to set.<br>
	 * <br>
	 * Placeholders may optionally be set in the given string as <samp>{{placeholder}}</samp> to be replaced by a 
	 * corresponding set of parameters, and they must be exclusively composed of identifiers, which are defined as 
	 * words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) or underscore (<samp>_</samp>), 
	 * and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), digits (<samp>0-9</samp>) and 
	 * underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also contain pointers to specific object properties or associative array values from the given set of 
	 * parameters by using a dot between identifiers, such as <samp>{{object.property}}</samp>, with no limit on the 
	 * number of pointers chained.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the given pointers are interpreted as getter method calls, but they cannot be given any arguments.</p>
	 * @param coercible:enum(Dracodeum\Kit\Enumerations\InfoLevel) $info_level [default = ENDUSER]
	 * <p>The info level to set with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setString(string $string, $info_level = EInfoLevel::ENDUSER)
	{
		$this->strings[EInfoLevel::coerceValue($info_level)] = $string;
		return $this;
	}
	
	/**
	 * Set plural string.
	 * 
	 * @param string $string
	 * <p>The string to set.<br>
	 * <br>
	 * Placeholders may optionally be set in the given string as <samp>{{placeholder}}</samp> to be replaced by a 
	 * corresponding set of parameters, and they must be exclusively composed of identifiers, which are defined as 
	 * words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) or underscore (<samp>_</samp>), 
	 * and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), digits (<samp>0-9</samp>) and 
	 * underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also contain pointers to specific object properties or associative array values from the given set of 
	 * parameters by using a dot between identifiers, such as <samp>{{object.property}}</samp>, with no limit on the 
	 * number of pointers chained.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the given pointers are interpreted as getter method calls, but they cannot be given any arguments.</p>
	 * @param coercible:enum(Dracodeum\Kit\Enumerations\InfoLevel) $info_level [default = ENDUSER]
	 * <p>The info level to set with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setPluralString(string $string, $info_level = EInfoLevel::ENDUSER)
	{
		$this->plural_strings[EInfoLevel::coerceValue($info_level)] = $string;
		return $this;
	}
	
	/**
	 * Set plural number.
	 * 
	 * @param float $number
	 * <p>The number to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setPluralNumber(float $number)
	{
		$this->plural_number = $number;
		return $this;
	}
	
	/**
	 * Set plural number placeholder.
	 * 
	 * @param string $placeholder
	 * <p>The placeholder to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setPluralNumberPlaceholder(string $placeholder)
	{
		$this->plural_number_placeholder = $placeholder;
		return $this;
	}
	
	/**
	 * Set parameter.
	 * 
	 * @param string $name
	 * <p>The name to set with.</p>
	 * @param mixed $value
	 * <p>The value to set with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setParameter(string $name, mixed $value)
	{
		$this->parameters[$name] = $value;
		return $this;
	}
	
	/**
	 * Set parameters.
	 * 
	 * @param array $parameters
	 * <p>The parameters to set, as a set of <samp>name => value</samp> pairs.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setParameters(array $parameters)
	{
		foreach ($parameters as $name => $value) {
			$this->parameters[$name] = $value;
		}
		return $this;
	}
	
	/**
	 * Set placeholder stringifier.
	 * 
	 * @param string $placeholder
	 * <p>The placeholder to set for.</p>
	 * @param callable $stringifier
	 * <p>The function to use to stringify a given value.<br>
	 * It must be compatible with the following signature:<br>
	 * <br>
	 * <code>function (mixed $value, \Dracodeum\Kit\Options\Text $text_options): string</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The value to stringify.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>\Dracodeum\Kit\Options\Text $text_options</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The text options instance to use.<br>
	 * <br>
	 * Return: <code><b>string</b></code><br>
	 * The stringified value.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setPlaceholderStringifier(string $placeholder, callable $stringifier)
	{
		UCall::assert('stringifier', $stringifier, function (mixed $value, TextOptions $text_options): string {});
		$this->placeholders_stringifiers[$placeholder] = $stringifier;
		return $this;
	}
	
	/**
	 * Set as localized.
	 * 
	 * @param string|null $context [default = null]
	 * <p>The context to set with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsLocalized(?string $context = null)
	{
		//TODO
		
		return $this;
	}
	
	
	
	//Final public static methods
	/**
	 * Build instance.
	 * 
	 * @param string $string [default = '']
	 * <p>The string to build with.<br>
	 * <br>
	 * Placeholders may optionally be set in the given string as <samp>{{placeholder}}</samp> to be replaced by a 
	 * corresponding set of parameters, and they must be exclusively composed of identifiers, which are defined as 
	 * words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) or underscore (<samp>_</samp>), 
	 * and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), digits (<samp>0-9</samp>) and 
	 * underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also contain pointers to specific object properties or associative array values from the given set of 
	 * parameters by using a dot between identifiers, such as <samp>{{object.property}}</samp>, with no limit on the 
	 * number of pointers chained.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the given pointers are interpreted as getter method calls, but they cannot be given any arguments.</p>
	 * @param coercible:enum(Dracodeum\Kit\Enumerations\InfoLevel) $info_level [default = ENDUSER]
	 * <p>The info level to build with.</p>
	 * @return static
	 * <p>The built instance.</p>
	 */
	final public static function build(string $string = '', $info_level = EInfoLevel::ENDUSER)
	{
		return new static($string, $info_level);
	}
	
	/**
	 * Coerce a given value into an instance.
	 * 
	 * //TODO: add @see towards Type prototype
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to coerce.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to coerce with, as a set of <samp>name => value</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Primitives\Text\Exceptions\CoercionFailed
	 * @return void|bool
	 * <p>If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then boolean <code>true</code> is returned if the given value was successfully coerced into an instance, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public static function coerce(mixed &$value, array $properties = [], bool $no_throw = false)
	{
		//TODO: move this code to a Type prototype
		
		//coerce
		if (!($value instanceof self)) {
			if (UType::evaluateString($value)) {
				$value = self::build($value);
			} elseif ($no_throw) {
				return false;
			} else {
				throw new Exceptions\CoercionFailed([self::class, $value]);
			}
		}
		
		//finalize
		if ($no_throw) {
			return true;
		}
	}
}
