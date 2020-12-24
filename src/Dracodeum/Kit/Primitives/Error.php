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
 * definition usually bound to the given name.<br>
 * <br>
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
	 * @param string|null $name [default = null]
	 * <p>The name to instantiate with.</p>
	 * @param coercible:text|null $text [default = null]
	 * <p>The text to instantiate with.</p>
	 * @param mixed $data [default = null]
	 * <p>The data to instantiate with.</p>
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
	 * <p>Boolean <code>true</code> if has name.</p>
	 */
	final public function hasName(): bool
	{
		return $this->name !== null;
	}
	
	/**
	 * Get name.
	 * 
	 * @return string|null
	 * <p>The name or <code>null</code> if none is set.</p>
	 */
	final public function getName(): ?string
	{
		return $this->name;
	}
	
	/**
	 * Set name.
	 * 
	 * @param string $name
	 * <p>The name to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
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
	 * <p>Boolean <code>true</code> if has text.</p>
	 */
	final public function hasText(): bool
	{
		return $this->text !== null;
	}
	
	/**
	 * Get text instance.
	 * 
	 * @return \Dracodeum\Kit\Primitives\Text|null
	 * <p>The text instance or <code>null</code> if none is set.</p>
	 */
	final public function getText(): ?Text
	{
		return $this->text;
	}
	
	/**
	 * Set text.
	 * 
	 * @param coercible:text $text
	 * <p>The text to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
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
	 * <p>Boolean <code>true</code> if has data.</p>
	 */
	final public function hasData(): bool
	{
		return $this->data !== null;
	}
	
	/**
	 * Get data.
	 * 
	 * @return mixed
	 * <p>The data.</p>
	 */
	final public function getData(): mixed
	{
		return $this->data;
	}
	
	/**
	 * Set data.
	 * 
	 * @param mixed $data
	 * <p>The data to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
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
	 * @param string|null $name [default = null]
	 * <p>The name to build with.</p>
	 * @param coercible:text|null $text [default = null]
	 * <p>The text to build with.</p>
	 * @param mixed $data [default = null]
	 * <p>The data to build with.</p>
	 * @return static
	 * <p>The built instance.</p>
	 */
	final public static function build(?string $name = null, $text = null, mixed $data = null)
	{
		return new static($name, $text, $data);
	}
}
