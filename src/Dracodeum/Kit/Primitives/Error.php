<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitives;

use Dracodeum\Kit\Primitive;
use Dracodeum\Kit\Interfaces\Cloneable as ICloneable;
use JsonSerializable as IJsonSerializable;
use Dracodeum\Kit\Traits;

/**
 * This primitive represents an error.
 * 
 * This is a simple object which represents and stores error information, such as a name to identify the error, 
 * text as an error message to the user, as well as data as additional structural information, with a structure 
 * definition usually bound to the given name.
 * 
 * Unlike an exception, this object cannot be thrown and therefore does not break out from the current stack, 
 * and is meant to be used to represent an error which can be directly returned to any user, including the end-user, 
 * therefore it must be assumed that both the given name and data may be returned and become publicly visible, 
 * thus any related sensitive system information in the given text must be set with the internal info level, 
 * or not set at all.
 */
final class Error extends Primitive implements ICloneable, IJsonSerializable
{
	//Traits
	use Traits\Cloneable;
	
	
	
	//Private properties
	private ?string $name = null;
	
	private ?Text $text = null;
	
	private mixed $data = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param string|null $name
	 * The name to instantiate with.
	 * 
	 * @param coercible:text|null $text
	 * The text to instantiate with.
	 * 
	 * @param mixed $data
	 * The data to instantiate with.
	 */
	final public function __construct(?string $name = null, $text = null, mixed $data = null)
	{
		//name
		if ($name !== null) {
			$this->setName($name);
		}
		
		//text
		if ($text !== null) {
			$this->setText($text);
		}
		
		//data
		if ($data !== null) {
			$this->setData($data);
		}
	}
	
	
	
	//Implemented final public methods (JsonSerializable)
	/** {@inheritdoc} */
	final public function jsonSerialize(): mixed
	{
		return [
			'name' => $this->name,
			'text' => $this->text,
			'data' => $this->data
		];
	}
	
	
	
	//Final public methods
	/**
	 * Check if has name.
	 * 
	 * @return bool
	 * Boolean `true` if has name.
	 */
	final public function hasName(): bool
	{
		return $this->name !== null;
	}
	
	/**
	 * Get name.
	 * 
	 * @return string|null
	 * The name, or `null` if none is set.
	 */
	final public function getName(): ?string
	{
		return $this->name;
	}
	
	/**
	 * Set name.
	 * 
	 * @param string $name
	 * The name to set.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setName(string $name)
	{
		$this->name = $name;
		return $this;
	}
	
	/**
	 * Check if has text.
	 * 
	 * @return bool
	 * Boolean `true` if has text.
	 */
	final public function hasText(): bool
	{
		return $this->text !== null;
	}
	
	/**
	 * Get text instance.
	 * 
	 * @return \Dracodeum\Kit\Primitives\Text|null
	 * The text instance, or `null` if none is set.
	 */
	final public function getText(): ?Text
	{
		return $this->text;
	}
	
	/**
	 * Set text.
	 * 
	 * @param coercible:text $text
	 * The text to set.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setText($text)
	{
		Text::coerce($text);
		$this->text = $text;
		return $this;
	}
	
	/**
	 * Check if has data.
	 * 
	 * @return bool
	 * Boolean `true` if has data.
	 */
	final public function hasData(): bool
	{
		return $this->data !== null;
	}
	
	/**
	 * Get data.
	 * 
	 * @return mixed
	 * The data.
	 */
	final public function getData(): mixed
	{
		return $this->data;
	}
	
	/**
	 * Set data.
	 * 
	 * @param mixed $data
	 * The data to set.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setData(mixed $data)
	{
		$this->data = $data;
		return $this;
	}
	
	
	
	//Final public static methods
	/**
	 * Build instance.
	 * 
	 * @param string|null $name
	 * The name to build with.
	 * 
	 * @param coercible:text|null $text
	 * The text to build with.
	 * 
	 * @param mixed $data
	 * The data to build with.
	 * 
	 * @return static
	 * The built instance.
	 */
	final public static function build(?string $name = null, $text = null, mixed $data = null)
	{
		return new static($name, $text, $data);
	}
}
