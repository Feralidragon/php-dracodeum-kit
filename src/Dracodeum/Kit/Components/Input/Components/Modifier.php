<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Components;

use Dracodeum\Kit\Component;
use Dracodeum\Kit\Components\Input\Components\Modifier\{
	Error,
	Structures
};
use Dracodeum\Kit\Components\Input\Prototypes\Modifier as Prototype;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces as PrototypeInterfaces;
use Dracodeum\Kit\Traits\DebugInfo\Info as DebugInfo;
use Dracodeum\Kit\Options\Text as TextOptions;

/**
 * This component represents a modifier which alters the behavior of an input towards a given value, 
 * in the form of an additional constraint, filter or other.
 * 
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifier
 */
abstract class Modifier extends Component
{
	//Private properties
	/** @var int */
	private $priority = 0;
	
	/** @var \Dracodeum\Kit\Components\Input\Components\Modifier\Error|null */
	private $error = null;
	
	
	
	//Abstract public methods
	/**
	 * Get type.
	 * 
	 * The returning type is a canonical string.
	 * 
	 * @return string
	 * <p>The type.</p>
	 */
	abstract public function getType(): string;
	
	
	
	//Abstract protected methods
	/**
	 * Handle the evaluation of a given value.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to handle the evaluation of (validate and sanitize).</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was evaluated as being valid.</p>
	 */
	abstract protected function handleValueEvaluation(&$value): bool;
	
	/**
	 * Get default error message.
	 * 
	 * The returning error message is the one used by default when the value evaluation fails.
	 * 
	 * @param \Dracodeum\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string
	 * <p>The default error message.</p>
	 */
	abstract protected function getDefaultErrorMessage(TextOptions $text_options): string;
	
	
	
	//Abstract protected static methods
	/**
	 * Get base priority.
	 * 
	 * The returning priority determines the base order by which this modifier is applied.<br>
	 * Modifiers which end up with the same priority are grouped together and are all executed, 
	 * even if any one of them fails.
	 * 
	 * @return int
	 * <p>The base priority.</p>
	 */
	abstract protected static function getBasePriority(): int;
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getBasePrototypeClass(): string
	{
		return Prototype::class;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Component\Traits\Initializer)
	/** {@inheritdoc} */
	protected function initialize(): void
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
	 * The returning name is a canonical string which identifies this modifier within an input.
	 * 
	 * @return string
	 * <p>The name.</p>
	 */
	public function getName(): string
	{
		return $this->getPrototype()->getName();
	}
	
	/**
	 * Get subtype.
	 * 
	 * The returning subtype is a canonical string.
	 * 
	 * @return string|null
	 * <p>The subtype or <code>null</code> if none is set.</p>
	 */
	public function getSubtype(): ?string
	{
		$prototype = $this->getPrototype();
		return $prototype instanceof PrototypeInterfaces\Subtype ? $prototype->getSubtype() : null;
	}
	
	/**
	 * Get label.
	 * 
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The label or <code>null</code> if none is set.</p>
	 */
	public function getLabel($text_options = null): ?string
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
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The message or <code>null</code> if none is set.</p>
	 */
	public function getMessage($text_options = null): ?string
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
	 * If no such properties are set, then <code>null</code> is returned instead.
	 * 
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The string or <code>null</code> if none is set.</p>
	 */
	public function getString($text_options = null): ?string
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
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The error message or <code>null</code> if no error is set.</p>
	 */
	public function getErrorMessage($text_options = null): ?string
	{
		//initialize
		if (!isset($this->error)) {
			return null;
		}
		$text_options = TextOptions::coerce($text_options);
		
		//prototype
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\ErrorMessage) {
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
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	public function unsetError(): Modifier
	{
		//prototype
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\ErrorUnset) {
			$prototype->unsetError();
		}
		
		//unset
		$this->error = null;
		
		//return
		return $this;
	}
	
	/**
	 * Get schema instance.
	 * 
	 * The returning schema describes this modifier by using a structure.
	 * 
	 * @return \Dracodeum\Kit\Components\Input\Components\Modifier\Structures\Schema
	 * <p>The schema instance.</p>
	 */
	public function getSchema(): Structures\Schema
	{
		$prototype = $this->getPrototype();
		return Structures\Schema::build([
			'name' => $this->getName(),
			'type' => $this->getType(),
			'subtype' => $this->getSubtype(),
			'data' => $prototype instanceof PrototypeInterfaces\SchemaData ? $prototype->getSchemaData() : null
		])->setAsReadonly(true);
	}
	
	
	
	//Final public methods
	/**
	 * Evaluate a given value.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated.</p>
	 */
	final public function evaluateValue(&$value): bool
	{
		$v = $value;
		if (!$this->handleValueEvaluation($v)) {
			$this->error = new Error($value);
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
	 * @return int
	 * <p>The priority.</p>
	 */
	final public function getPriority(): int
	{
		return $this->priority;
	}
	
	
	
	//Overridden public methods
	/** {@inheritdoc} */
	public function processDebugInfo(DebugInfo $info): void
	{
		//parent
		parent::processDebugInfo($info);
		
		//hidden properties
		if (!isset($this->error)) {
			$info->hideObjectProperty('error', self::class);
		}
	}
}
