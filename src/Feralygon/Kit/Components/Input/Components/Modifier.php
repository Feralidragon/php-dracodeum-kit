<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Components;

use Feralygon\Kit\Component;
use Feralygon\Kit\Components\Input\Components\Modifier\{
	Objects,
	Structures
};
use Feralygon\Kit\Prototypes\Input\Prototypes\Modifier as Prototype;
use Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces as PrototypeInterfaces;
use Feralygon\Kit\Options\Text as TextOptions;

/**
 * This component represents a modifier which alters the behavior of an input towards a given value, 
 * in the form of an additional constraint, filter or other.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Components\Input
 * @see \Feralygon\Kit\Prototypes\Input\Prototypes\Modifier
 */
abstract class Modifier extends Component
{
	//Private properties
	/** @var int */
	private $priority = 0;
	
	/** @var \Feralygon\Kit\Components\Input\Components\Modifier\Objects\Error|null */
	private $error = null;
	
	
	
	//Abstract protected methods
	/**
	 * Handle the evaluation of a given value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference]
	 * <p>The value to handle the evaluation of (validate and sanitize).</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value is evaluated as being valid.</p>
	 */
	abstract protected function handleValueEvaluation(&$value) : bool;
	
	/**
	 * Get default error message.
	 * 
	 * The returning error message is the one used by default when the value evaluation fails.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string
	 * <p>The default error message.</p>
	 */
	abstract protected function getDefaultErrorMessage(TextOptions $text_options) : string;
	
	
	
	//Abstract protected static methods
	/**
	 * Get base priority.
	 * 
	 * The returning priority determines the base order by which this modifier is applied.<br>
	 * Modifiers which end up with the same priority are grouped together and are all executed, 
	 * even if any one of them fails.
	 * 
	 * @since 1.0.0
	 * @return int
	 * <p>The base priority.</p>
	 */
	abstract protected static function getBasePriority() : int;
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getBasePrototypeClass() : string
	{
		return Prototype::class;
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Component\Traits\Initialization)
	/** {@inheritdoc} */
	protected function initialize() : void
	{
		//prototype
		$prototype = $this->getPrototype();
		
		//priority
		$this->priority = $this->getBasePriority();
		if ($prototype instanceof PrototypeInterfaces\Priority) {
			$this->priority += $prototype->getPriority();
		}
	}
	
	
	
	//Public methods
	/**
	 * Get name.
	 * 
	 * The returning name is a canonical string, which uniquely identifies this modifier within an input.
	 * 
	 * @since 1.0.0
	 * @return string|null
	 * <p>The name or <code>null</code> if none exists.</p>
	 */
	public function getName() : ?string
	{
		$prototype = $this->getPrototype();
		return $prototype instanceof PrototypeInterfaces\Name ? $prototype->getName() : null;
	}
	
	/**
	 * Get label.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The label or <code>null</code> if none exists.</p>
	 */
	public function getLabel($text_options = null) : ?string
	{
		$prototype = $this->getPrototype();
		return $prototype instanceof PrototypeInterfaces\Information
			? $prototype->getLabel(TextOptions::coerce($text_options))
			: null;
	}
	
	/**
	 * Get message.
	 * 
	 * The returning message is assertive relative to the expected value.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The message or <code>null</code> if none exists.</p>
	 */
	public function getMessage($text_options = null) : ?string
	{
		$prototype = $this->getPrototype();
		return $prototype instanceof PrototypeInterfaces\Information
			? $prototype->getMessage(TextOptions::coerce($text_options))
			: null;
	}
	
	/**
	 * Get string.
	 * 
	 * The returning string represents the full set of properties which defines how a value is evaluated.<br>
	 * If no such properties are set, <code>null</code> is returned instead.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The string or <code>null</code> if none exists.</p>
	 */
	public function getString($text_options = null) : ?string
	{
		$prototype = $this->getPrototype();
		return $prototype instanceof PrototypeInterfaces\Stringification
			? $prototype->getString(TextOptions::coerce($text_options))
			: null;
	}
	
	/**
	 * Get error message.
	 * 
	 * The returning error message is only set if a previous <code>evaluateValue</code> method call has failed.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The error message or <code>null</code> if no error is set.</p>
	 */
	public function getErrorMessage($text_options = null) : ?string
	{
		//initialize
		if (!isset($this->error)) {
			return null;
		}
		$text_options = TextOptions::coerce($text_options);
		
		//prototype
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\ErrorInformation) {
			return $prototype->getErrorMessage($text_options);
		} elseif ($prototype instanceof PrototypeInterfaces\Information) {
			return $prototype->getMessage($text_options);
		}
		
		//default
		return $this->getDefaultErrorMessage($text_options);
	}
	
	/**
	 * Unset error.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if an error was previously set.</p>
	 */
	public function unsetError() : bool
	{
		//initialize
		$set = isset($this->error);
		$this->error = null;
		
		//prototype
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\ErrorUnset) {
			$prototype->unsetError();
		}
		
		//return
		return $set;
	}
	
	/**
	 * Get schema instance.
	 * 
	 * The returning schema describes this modifier by using a structure.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Components\Input\Components\Modifier\Structures\Schema|null
	 * <p>The schema instance or <code>null</code> if none exists.</p>
	 */
	public function getSchema() : ?Structures\Schema
	{
		$name = $this->getName();
		if (isset($name)) {
			$prototype = $this->getPrototype();
			return new Structures\Schema([
				'name' => $name,
				'data' => $prototype instanceof PrototypeInterfaces\SchemaData ? $prototype->getSchemaData() : null
			], true);
		}
		return null;
	}
	
	
	
	//Final public methods
	/**
	 * Evaluate a given value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value is successfully evaluated.</p>
	 */
	final public function evaluateValue(&$value) : bool
	{
		$v = $value;
		if (!$this->handleValueEvaluation($v)) {
			$this->error = new Objects\Error($value);
			return false;
		}
		$value = $v;
		return true;
	}
	
	/**
	 * Get priority.
	 * 
	 * The returning priority determines the order by which this modifier is applied.<br>
	 * Modifiers with the same priority should be grouped together and be all executed, even if any one of them fails.
	 * 
	 * @since 1.0.0
	 * @return int
	 * <p>The priority.</p>
	 */
	final public function getPriority() : int
	{
		return $this->priority;
	}
}
