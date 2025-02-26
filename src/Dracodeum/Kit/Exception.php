<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit;

use Dracodeum\Kit\Interfaces\{
	DebugInfo as IDebugInfo,
	Properties as IProperties,
	Arrayable as IArrayable
};
use Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor as IDebugInfoProcessor;
use Dracodeum\Kit\Traits as KTraits;
use Dracodeum\Kit\Exception\{
	Options,
	Traits
};
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This class is the base to be extended from when creating a throwable exception, 
 * and it extends the PHP <code>Exception</code> class to provide extra functionality.
 * 
 * It also provides the means to define custom read-only properties and a default message.
 * 
 * @see https://php.net/manual/en/class.exception.php
 * @see \Dracodeum\Kit\Exception\Traits\PropertiesInitializer
 * @see \Dracodeum\Kit\Exception\Traits\PropertiesLoader
 */
abstract class Exception extends \Exception implements IDebugInfo, IDebugInfoProcessor, IProperties, IArrayable
{
	//Traits
	use KTraits\DebugInfo;
	use KTraits\DebugInfo\PropertiesDumpProcessor;
	use KTraits\Properties;
	use KTraits\Properties\Arrayable;
	use Traits\PropertiesInitializer;
	use Traits\PropertiesLoader;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param array $properties [default = []]
	 * <p>The properties to instantiate with, as a set of <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @param \Dracodeum\Kit\Exception\Options\Construct|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 */
	final public function __construct(array $properties = [], $options = null)
	{
		//initialize
		$options = Options\Construct::coerce($options);
		$this->initializePropertiesManager(
			\Closure::fromCallable([$this, 'loadProperties']), $properties, 'r+',
			\Closure::fromCallable([$this, 'initializeProperties'])
		);
		
		//message
		$message = $options->message ?? $this->getDefaultMessage();
		
		//parameters
		$parameters = [];
		foreach (UText::placeholders($message, true) as $name) {
			$parameters[$name] = $this->get($name);
		}
		if (!empty($parameters)) {
			$message = UText::fill($message, $parameters, null, [
				'stringifier' => function (string $placeholder, $value) use ($options): ?string {
					$string = null;
					if (isset($options->stringifier)) {
						$string = ($options->stringifier)($placeholder, $value);
					}
					return $string ?? $this->getPlaceholderValueString($placeholder, $value);
				}
			]);
		}
		
		//parent
		parent::__construct($message, $options->code ?? 0, $options->previous);
	}
	
	
	
	//Abstract public methods
	/**
	 * Get default message.
	 * 
	 * Placeholders may optionally be set as <samp>{{placeholder}}</samp>, 
	 * corresponding directly to properties in this exception, and must be exclusively composed of identifiers.<br>
	 * <br>
	 * Identifiers are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
	 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
	 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used with pointers to specific object properties or associative array values, 
	 * by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
	 * with no limit on the number of chained pointers.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the identifiers are interpreted as getter method calls, but they cannot be given any arguments.
	 * 
	 * @return string
	 * <p>The default message.</p>
	 */
	abstract public function getDefaultMessage(): string;
	
	
	
	//Protected methods
	/**
	 * Get string for a given placeholder from a given value.
	 * 
	 * @param string $placeholder
	 * <p>The placeholder to get for.</p>
	 * @param mixed $value
	 * <p>The value to get from.</p>
	 * @return string
	 * <p>The string for the given placeholder from the given value.</p>
	 */
	protected function getPlaceholderValueString(string $placeholder, $value): string
	{
		return UText::stringify($value, null, [
			'quote_strings' => true, 'prepend_type' => is_bool($value), 'non_stringable' => true
		]);
	}
}
