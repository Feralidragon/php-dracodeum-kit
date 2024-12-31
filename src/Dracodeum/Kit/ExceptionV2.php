<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit;

use Exception as PhpException;
use Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyBooter as IPropertyBooter;
use Dracodeum\Kit\Managers\PropertiesV2\Property;
use Dracodeum\Kit\Traits;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Enums\Info\Level as EInfoLevel;
use Dracodeum\Kit\Primitives\{
	Text,
	Error
};
use Dracodeum\Kit\Utilities\{
	Call as UCall,
	Text as UText
};
use Closure;

/**
 * This class is the base to be extended from when creating a throwable exception, 
 * as it extends the PHP `Exception` class to provide extra functionality, namely:
 * - extended properties;
 * - text primitive for message definition.
 * 
 * All publicly readable properties are set as read-only after instantiation.
 * 
 * @see https://php.net/manual/en/class.exception.php
 */
abstract class ExceptionV2 extends PhpException implements IPropertyBooter
{
	//Traits
	use Traits\PropertiesV2;
	
	
	
	//Private properties
	private Text $text;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param mixed $properties
	 * The properties to instantiate with.
	 */
	final public function __construct(...$properties)
	{
		//initialize
		$code = $properties['code'] ?? 0;
		$previous = $properties['previous'] ?? null;
		unset($properties['code'], $properties['previous']);
		
		//properties
		$this->initializePropertiesManager($properties);
		
		//text
		$this->text = UCall::guardExecution(Closure::fromCallable([$this, 'produceText']), [], [Text::class, 'coerce']);
		$this->text->setObject($this)->setStringifier($this->getStringifier());
		
		//parent
		parent::__construct($this->text->toString(['info_level' => EInfoLevel::INTERNAL->value]), $code, $previous);
	}
	
	
	
	//Abstract protected methods
	/**
	 * Produce text.
	 * 
	 * @return coercible<\Dracodeum\Kit\Primitives\Text>
	 * The produced text.
	 */
	abstract protected function produceText();
	
	
	
	//Implemented final public static methods (Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyBooter)
	/** {@inheritdoc} */
	final public static function bootProperty(Property $property): void
	{
		if ($property->getReflection()->isPublic() && $property->getMode()[0] === 'r') {
			$property->setMode('r+');
		}
	}
	
	
	
	//Final public methods
	/**
	 * Get text instance.
	 * 
	 * @return \Dracodeum\Kit\Primitives\Text
	 * The text instance.
	 */
	final public function getText(): Text
	{
		return $this->text;
	}
	
	/**
	 * Cast this exception to an error instance.
	 * 
	 * @return \Dracodeum\Kit\Primitives\Error
	 * An error instance cast from this exception.
	 */
	final public function toError(): Error
	{
		return Error::build($this::class, $this->text)
			->setThrowable($this)
			->setData($this->getPropertiesManager()->mget(scope_class: $this::class), EInfoLevel::INTERNAL);
	}
	
	
	
	//Final protected methods
	/**
	 * Get stringifier.
	 * 
	 * @return callable
	 * The stringifier.
	 */
	final protected function getStringifier(): callable
	{
		return function (mixed $value, TextOptions $text_options): string {
			return UText::stringify($value, $text_options, [
				'quote_strings' => true, 'prepend_type' => is_bool($value), 'non_stringable' => true
			]);
		};
	}
}
