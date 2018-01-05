<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core;

use Feralygon\Kit\Core\Exception\Options;
use Feralygon\Kit\Core\Utilities\Text as UText;

/**
 * Core exception class.
 * 
 * This class is the base to be extended from when creating a throwable exception, 
 * and it extends the PHP core <code>Exception</code> class to provide extra functionality.<br>
 * This extension of the PHP core <code>Exception</code> class provides the means to define custom read-only properties and a default message.
 * 
 * @since 1.0.0
 * @see https://php.net/manual/en/class.exception.php
 */
abstract class Exception extends \Exception implements \ArrayAccess
{
	//Traits
	use Traits\PropertiesArrayAccess;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param array $properties [default = []] <p>The properties, as <code>name => value</code> pairs.</p>
	 * @param \Feralygon\Kit\Core\Exception\Options\Construct|array|null $options [default = null] <p>Additional options, as an instance or <code>name => value</code> pairs.</p>
	 */
	final public function __construct(array $properties = [], $options = null)
	{
		//initialize
		$options = Options\Construct::load($options);
		$this->initializeProperties($properties, \Closure::fromCallable([$this, 'evaluateProperty']), $this->getRequiredPropertyNames(), 'r');
		
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
	 * If set, placeholders must be exclusively composed by identifiers, set as <code>{{placeholder}}</code>.<br>
	 * Identifiers are defined as words which must start with a letter (<code>a-z</code> and <code>A-Z</code>) 
	 * or underscore (<code>_</code>), and may only contain letters (<code>a-z</code> and <code>A-Z</code>), 
	 * digits (<code>0-9</code>) and underscores (<code>_</code>).<br>
	 * <br>
	 * They may also be used with pointers to specific object properties or associative array values, within the set properties, by using a dot between identifiers, 
	 * such as <code>{{object.property}}</code>, with no limit on the number of chained pointers.<br>
	 * If suffixed with opening and closing parenthesis, such as <code>{{object.method()}}</code>, the identifiers are interpreted as getter method calls, 
	 * but they cannot be given any custom parameters.
	 * 
	 * @since 1.0.0
	 * @return string <p>The default message.</p>
	 */
	abstract public function getDefaultMessage() : string;
	
	
	
	//Abstract public static methods
	/**
	 * Get required property names.
	 * 
	 * All the required properties returned here must be given during instantiation.
	 * 
	 * @since 1.0.0
	 * @return string[] <p>The required property names.</p>
	 */
	abstract public static function getRequiredPropertyNames() : array;
	
	
	
	//Abstract protected methods
	/**
	 * Evaluate a given property value for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to evaluate for.</p>
	 * @param mixed $value [reference] <p>The property value to evaluate (validate and sanitize).</p>
	 * @return bool|null <p>Boolean <samp>true</samp> if the property with the given name and value exists and is valid,
	 * boolean <samp>false</samp> if it exists but is not valid, or <samp>null</samp> if it does not exist.</p>
	 */
	abstract protected function evaluateProperty(string $name, &$value) : ?bool;
	
	
	
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
		return UText::stringify($value);
	}
}
