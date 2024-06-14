<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitives;

use Dracodeum\Kit\Primitive;
use Dracodeum\Kit\Enums\Info\Level as EInfoLevel;
use Exception;

/**
 * This primitive represents an error.
 * 
 * This is a simple object which represents and stores error information, such as a name to identify the error, 
 * text as an error message to the user or/and developer, an `Exception` instance that originated it, as well as data 
 * as additional information, with a structure definition usually bound to the given name.
 * 
 * Unlike an exception, this object cannot be thrown and therefore does not break out from the current call stack, 
 * and is meant to be used to represent an error which can be directly returned to any user, including the end-user, 
 * thus any related sensitive system information in the given text and data must be set with the `INTERNAL` info level, 
 * or not set at all.
 */
final class Error extends Primitive
{
	//Private properties
	private ?string $name = null;
	private ?Text $text = null;
	private ?Exception $exception = null;
	
	/** @var array<int,mixed> */
	private array $data = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param string|null $name
	 * The name to instantiate with.
	 * 
	 * @param coercible<\Dracodeum\Kit\Primitives\Text>|null $text
	 * The text to instantiate with.
	 */
	final public function __construct(?string $name = null, $text = null)
	{
		//name
		if ($name !== null) {
			$this->setName($name);
		}
		
		//text
		if ($text !== null) {
			$this->setText($text);
		}
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
	 * @param coercible<\Dracodeum\Kit\Primitives\Text> $text
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
	 * Check if has exception.
	 * 
	 * @return bool
	 * Boolean `true` if has exception.
	 */
	final public function hasException(): bool
	{
		return $this->exception !== null;
	}
	
	/**
	 * Get exception instance.
	 * 
	 * @return \Exception|null
	 * The exception instance, or `null` if none is set.
	 */
	final public function getException(): ?Exception
	{
		return $this->exception;
	}
	
	/**
	 * Set exception instance.
	 * 
	 * @param \Exception $exception
	 * The exception instance to set.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setException(Exception $exception)
	{
		$this->exception = $exception;
		return $this;
	}
	
	/**
	 * Check if has data.
	 * 
	 * @param \Dracodeum\Kit\Enums\Info\Level $info_level
	 * The info level to check for.
	 * 
	 * @return bool
	 * Boolean `true` if has data.
	 */
	final public function hasData(EInfoLevel $info_level = EInfoLevel::ENDUSER): bool
	{
		return $this->getData($info_level) !== null;
	}
	
	/**
	 * Get data.
	 * 
	 * @param \Dracodeum\Kit\Enums\Info\Level $info_level
	 * The info level to get for.
	 * 
	 * @return mixed
	 * The data.
	 */
	final public function getData(EInfoLevel $info_level = EInfoLevel::ENDUSER): mixed
	{
		return $this->data[$info_level->value] ?? null;
	}
	
	/**
	 * Set data.
	 * 
	 * @param mixed $data
	 * The data to set.
	 * 
	 * @param \Dracodeum\Kit\Enums\Info\Level $info_level
	 * The info level to set with.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setData(mixed $data, EInfoLevel $info_level = EInfoLevel::ENDUSER)
	{
		$this->data[$info_level->value] = $data;
		return $this;
	}
	
	
	
	//Final public static methods
	/**
	 * Build instance.
	 * 
	 * @param string|null $name
	 * The name to build with.
	 * 
	 * @param coercible<\Dracodeum\Kit\Primitives\Text>|null $text
	 * The text to build with.
	 * 
	 * @return static
	 * The built instance.
	 */
	final public static function build(?string $name = null, $text = null): static
	{
		return new static($name, $text);
	}
}
