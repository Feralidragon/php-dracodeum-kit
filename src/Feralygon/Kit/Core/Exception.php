<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core;

use Feralygon\Kit\Core\Interfaces\Arrayable as IArrayable;
use Feralygon\Kit\Core\Exception\Options;
use Feralygon\Kit\Core\Utilities\Text as UText;

/**
 * Core exception class.
 * 
 * This class is the base to be extended from when creating a throwable exception, 
 * and it extends the PHP core <code>Exception</code> class to provide extra functionality.<br>
 * It also provides the means to define custom read-only properties and a default message.
 * 
 * @since 1.0.0
 * @see https://php.net/manual/en/class.exception.php
 */
abstract class Exception extends \Exception implements \ArrayAccess, IArrayable
{
	//Traits
	use Traits\Properties\ArrayableAccess;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param array $properties [default = []] <p>The properties, as <samp>name => value</samp> pairs.</p>
	 * @param \Feralygon\Kit\Core\Exception\Options\Construct|array|null $options [default = null] 
	 * <p>Additional options, as an instance or <samp>name => value</samp> pairs.</p>
	 */
	final public function __construct(array $properties = [], $options = null)
	{
		//initialize
		$options = Options\Construct::coerce($options);
		$this->initializeProperties($properties, \Closure::fromCallable([$this, 'loadProperties']), 'r');
		
		//message
		$message = $options->message ?? $this->getDefaultMessage();
		$placeholders = UText::getPlaceholders($message);
		if (!empty($placeholders)) {
			//parameters
			$parameters = [];
			foreach ($placeholders as $placeholder) {
				$name = strtok($placeholder, '.');
				$parameters[$name] = $this->get($name);
			}
			
			//message
			$message = UText::fill($message, $parameters, null, [
				'stringifier' => function (string $placeholder, $value) use ($options) : ?string {
					$string = null;
					if (isset($options->stringifier)) {
						$string = ($options->stringifier)($placeholder, $value);
					}
					return $string ?? $this->getPlaceholderValueString($placeholder, $value);
				}
			]);
		}
		
		//parent
		parent::__construct($message, $options->code, $options->previous);
	}
	
	
	
	//Abstract public methods
	/**
	 * Get default message.
	 * 
	 * If set, placeholders must be exclusively composed by identifiers, set as <samp>{{placeholder}}</samp>.<br>
	 * Identifiers are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
	 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
	 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used with pointers to specific object properties or associative array values, 
	 * within the set properties, by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
	 * with no limit on the number of chained pointers.<br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * the identifiers are interpreted as getter method calls, but they cannot be given any custom parameters.
	 * 
	 * @since 1.0.0
	 * @return string <p>The default message.</p>
	 */
	abstract public function getDefaultMessage() : string;
	
	
	
	//Abstract protected methods
	/**
	 * Load properties.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	abstract protected function loadProperties() : void;
	
	
	
	//Protected methods
	/**
	 * Get string from a given placeholder value.
	 * 
	 * @since 1.0.0
	 * @param string $placeholder <p>The placeholder to get from.</p>
	 * @param mixed $value <p>The value to get from.</p>
	 * @return string <p>The string from the given placeholder value.</p>
	 */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		return UText::stringify($value, null, ['quote_strings' => true, 'prepend_type' => is_bool($value)]);
	}
}
