<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit;

use Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyInitializer as IPropertyInitializer;
use Dracodeum\Kit\Managers\PropertiesV2\Property;
use Dracodeum\Kit\Traits;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;
use Dracodeum\Kit\Primitives\Text;
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
abstract class ExceptionV2 extends \Exception implements IPropertyInitializer
{
	//Traits
	use Traits\PropertiesV2;
	
	
	
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
		/** @var \Dracodeum\Kit\Primitives\Text $text */
		$text = UCall::guardExecution(Closure::fromCallable([$this, 'produceText']), [], [Text::class, 'coerce']);
		$text
			->setObject($this)
			->setStringifier(function (mixed $value, TextOptions $text_options): string {
				return UText::stringify($value, $text_options, [
					'quote_strings' => true, 'prepend_type' => is_bool($value), 'non_stringable' => true
				]);
			})
		;
		
		//parent
		parent::__construct($text->toString(['info_level' => EInfoLevel::INTERNAL]), $code, $previous);
	}
	
	
	
	//Abstract protected methods
	/**
	 * Produce text.
	 * 
	 * @return coercible<\Dracodeum\Kit\Primitives\Text>
	 * The produced text.
	 */
	abstract protected function produceText();
	
	
	
	//Implemented final public static methods (Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyInitializer)
	/** {@inheritdoc} */
	final public static function initializeProperty(Property $property): void
	{
		if ($property->getReflection()->isPublic() && $property->getMode()[0] === 'r') {
			$property->setMode('r+');
		}
	}
}
