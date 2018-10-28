<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties;

use Feralygon\Kit\Managers\Properties as Manager;
use Feralygon\Kit\Managers\Properties\Property\Exceptions;
use Feralygon\Kit\Traits;
use Feralygon\Kit\Utilities\{
	Call as UCall,
	Text as UText
};

/**
 * @since 1.0.0
 * @see \Feralygon\Kit\Managers\Properties
 */
class Property
{	
	//Traits
	use Traits\Evaluators;
	
	
	
	//Private properties
	/** @var \Feralygon\Kit\Managers\Properties */
	private $manager;
	
	/** @var string */
	private $name;
	
	/** @var bool */
	private $initialized = false;
	
	/** @var bool */
	private $required = false;
	
	/** @var string|null */
	private $mode = null;
	
	/** @var mixed */
	private $value = null;
	
	/** @var \Closure|null */
	private $default_getter = null;
	
	/** @var \Closure|null */
	private $getter = null;
	
	/** @var \Closure|null */
	private $setter = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Managers\Properties $manager
	 * <p>The manager instance.</p>
	 * @param string $name
	 * <p>The name.</p>
	 */
	final public function __construct(Manager $manager, string $name)
	{
		$this->manager = $manager;
		$this->name = $name;
	}
	
	
	
	//Final public methods
	/**
	 * Get manager instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Managers\Properties
	 * <p>The manager instance.</p>
	 */
	final public function getManager(): Manager
	{
		return $this->manager;
	}
	
	/**
	 * Get name.
	 * 
	 * @since 1.0.0
	 * @return string
	 * <p>The name.</p>
	 */
	final public function getName(): string
	{
		return $this->name;
	}
	
	/**
	 * Check if is initialized.
	 * 
	 * A property becomes implicitly initialized once a value is set.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if is initialized.</p>
	 */
	final public function isInitialized(): bool
	{
		return $this->initialized;
	}
	
	/**
	 * Check if is required.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if is required.</p>
	 */
	final public function isRequired(): bool
	{
		return $this->required || !isset($this->default_getter) || 
			($this->manager->isLazy() && $this->manager->isRequiredPropertyName($this->name));
	}
	
	/**
	 * Initialize.
	 * 
	 * @since 1.0.0
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function initialize(): Property
	{
		UCall::guard(!$this->initialized, [
			'error_message' => "Property {{property.getName()}} already initialized " . 
				"in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		$this->setValue($this->getDefaultValue());
		return $this;
	}
	
	/**
	 * Set as required.
	 * 
	 * Even without being explicitly set as required, a property is considered so if it has no default value set.<br>
	 * <br>
	 * This method may only be called before initialization, of both the property and the manager, 
	 * with lazy-loading disabled and only if the mode is not set to strict read-only.
	 * 
	 * @since 1.0.0
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsRequired(): Property
	{
		//guard
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		UCall::guard(!$this->manager->isLazy(), [
			'hint_message' => "In order to set property {{property.getName()}} as required " . 
				"in manager with owner {{property.getManager().getOwner()}}, " . 
				"please use the manager {{method}} method instead, as lazy-loading is enabled.",
			'parameters' => ['property' => $this, 'method' => 'addRequiredPropertyNames']
		]);
		UCall::guard(!$this->manager->isInitialized(), [
			'hint_message' => "This method may only be called before the manager initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		UCall::guard($this->getMode() !== 'r', [
			'hint_message' => "Property {{property.getName()}} is strictly read-only " . 
				"in manager with owner {{property.getManager().getOwner()}} and thus cannot be set as required.",
			'parameters' => ['property' => $this]
		]);
		
		//set
		$this->required = true;
		
		//return
		return $this;
	}
	
	/**
	 * Get mode.
	 * 
	 * @since 1.0.0
	 * @return string
	 * <p>The mode.</p>
	 */
	final public function getMode(): string
	{
		return $this->mode ?? $this->manager->getMode();
	}
	
	/**
	 * Set mode.
	 * 
	 * @since 1.0.0
	 * @param string $mode
	 * <p>The access mode to set, which must be one the following:<br>
	 * &nbsp; &#8226; &nbsp; <samp>r</samp> : Allow this property to be only strictly read from, 
	 * so that it cannot be given during initialization (strict read-only).<br>
	 * &nbsp; &#8226; &nbsp; <samp>r+</samp> : Allow this property to be only read from (read-only), 
	 * although it may still be given during initialization.<br>
	 * &nbsp; &#8226; &nbsp; <samp>rw</samp> : Allow this property to be both read from and written to (read-write).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w</samp> : Allow this property to be only written to (write-only).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w-</samp> : Allow this property to be only written to, 
	 * but only once during initialization (write-once).<br>
	 * <br>
	 * NOTE: The allowed modes may be more restricted depending on the global mode set in the manager:<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>r</samp> or <samp>r+</samp>, 
	 * only <samp>r</samp>, <samp>r+</samp> and <samp>rw</samp> are allowed;<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>rw</samp>, all modes are allowed;<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>w</samp> or <samp>w-</samp>, 
	 * only <samp>rw</samp>, <samp>w</samp> and <samp>w-</samp> are allowed.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setMode(string $mode): Property
	{
		//map
		$map = [];
		switch ($this->manager->getMode()) {
			case 'r':
				$map = ['r' => 'r', 'r+' => 'r', 'rw' => 'r'];
				break;
			case 'r+':
				$map = ['r' => 'r', 'r+' => 'r+', 'rw' => 'r+'];
				break;
			case 'rw':
				$map = array_combine(Manager::MODES, Manager::MODES);
				break;
			case 'w':
				$map = ['rw' => 'w', 'w' => 'w', 'w-' => 'w-'];
				break;
			case 'w-':
				$map = ['rw' => 'w-', 'w' => 'w-', 'w-' => 'w-'];
				break;
		}
		
		//set
		UCall::guardParameter('mode', $mode, isset($map[$mode]), function () use ($map) {
			return [
				'hint_message' => "Only the following mode is allowed for property {{property.getName()}} " . 
					"in manager with owner {{property.getManager().getOwner()}}: {{modes}}.",
				'hint_message_plural' => "Only the following modes are allowed for property {{property.getName()}} " . 
					"in manager with owner {{property.getManager().getOwner()}}: {{modes}}.",
				'hint_message_number' => count($map),
				'parameters' => ['property' => $this, 'modes' => array_keys($map)],
				'string_options' => ['non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND]
			];
		});
		$this->mode = $map[$mode];
		
		//return
		return $this;
	}
	
	/**
	 * Get value.
	 * 
	 * This method may only be called after initialization.
	 * 
	 * @since 1.0.0
	 * @return mixed
	 * <p>The value.</p>
	 */
	final public function getValue()
	{
		UCall::guard($this->initialized, [
			'hint_message' => "This method may only be called after initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		return isset($this->getter) ? ($this->getter)() : $this->value;
	}
	
	/**
	 * Set value.
	 * 
	 * This method may only be called during or after the manager initialization.
	 * 
	 * @since 1.0.0
	 * @param mixed $value
	 * <p>The value to set.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Managers\Properties\Property\Exceptions\InvalidValue
	 * @return $this|bool
	 * <p>This instance, for chaining purposes.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then boolean <code>true</code> is returned if the value was successfully set, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public function setValue($value, bool $no_throw = false)
	{
		//guard
		UCall::guard($this->manager->isInitialized() || $this->manager->isInitializing(), [
			'hint_message' => "This method may only be called during or after the manager initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//evaluate
		if (!$this->getEvaluatorsManager()->evaluate($value)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\InvalidValue([$this, $value]);
		}
		
		//set
		if (isset($this->setter)) {
			($this->setter)($value);
		} else {
			$this->value = $value;
		}
		
		//initialized
		$this->lockEvaluators([
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		$this->initialized = true;
		
		//return
		return $no_throw ? true : $this;
	}
	
	/**
	 * Check if has default value.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if has default value.</p>
	 */
	final public function hasDefaultValue(): bool
	{
		return isset($this->default_getter);
	}
	
	/**
	 * Get default value.
	 * 
	 * @since 1.0.0
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Managers\Properties\Property\Exceptions\DefaultValueNotSet
	 * @return mixed
	 * <p>The default value.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then <code>null</code> may also be returned if none is set.</p>
	 */
	final public function getDefaultValue(bool $no_throw = false)
	{
		//check
		if (!isset($this->default_getter)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\DefaultValueNotSet([$this]);
		}
		
		//value
		$value = ($this->default_getter)();
		
		//evaluate
		UCall::guardInternal($this->getEvaluatorsManager()->evaluate($value), [
			'error_message' => "Invalid default value {{value}} for property {{property.getName()}} " . 
				"in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this, 'value' => $value]
		]);
		
		//return
		return $value;
	}
	
	/**
	 * Set default value.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param mixed $value
	 * <p>The default value to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setDefaultValue($value): Property
	{
		//guard
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//set
		$this->default_getter = function () use ($value) {
			return $value;
		};
		
		//return
		return $this;
	}
	
	/**
	 * Set default getter function.
	 * 
	 * By setting a default getter function, the default value will always be got using that function.<br>
	 * It is only called after all properties have been initialized through the manager.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param callable $getter
	 * <p>The default getter function to set.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ()</code><br>
	 * <br>
	 * Return: <code><b>mixed</b></code><br>
	 * The default value.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setDefaultGetter(callable $getter): Property
	{
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		UCall::assert('default_getter', $getter, function () {});
		$this->default_getter = \Closure::fromCallable($getter);
		return $this;
	}
	
	/**
	 * Reset value.
	 * 
	 * This method may only be called after initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Managers\Properties\Property\Exceptions\DefaultValueNotSet
	 * @throws \Feralygon\Kit\Managers\Properties\Property\Exceptions\InvalidValue
	 * @return $this|bool
	 * <p>This instance, for chaining purposes.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then boolean <code>true</code> is returned if the value was successfully reset, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public function resetValue(bool $no_throw = false)
	{
		//guard
		UCall::guard($this->initialized, [
			'hint_message' => "This method may only be called after initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//reset
		$reset = false;
		try {
			$reset = $this->setValue($this->getDefaultValue(), $no_throw);
		} catch (Exceptions\DefaultValueNotSet $exception) {
			if (!$no_throw) {
				throw $exception;
			}
		}
		
		//return
		return $no_throw ? $reset : $this;
	}
	
	/**
	 * Set getter function.
	 * 
	 * By setting a getter function, the value will always be got using that function.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param callable $getter
	 * <p>The getter function to set.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ()</code><br>
	 * <br>
	 * Return: <code><b>mixed</b></code><br>
	 * The value.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setGetter(callable $getter): Property
	{
		//guard
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//set
		UCall::assert('getter', $getter, function () {});
		$this->getter = \Closure::fromCallable($getter);
		
		//default
		if (!isset($this->default_getter)) {
			$this->default_getter = $this->getter;
		}
		
		//return
		return $this;
	}
	
	/**
	 * Set setter function.
	 * 
	 * By setting a setter function, the value will always be set using that function.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param callable $setter
	 * <p>The setter function to set.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ($value): void</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The value to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setSetter(callable $setter): Property
	{
		//guard
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//set
		UCall::assert('setter', $setter, function ($value): void {});
		$this->setter = \Closure::fromCallable($setter);
		
		//return
		return $this;
	}
	
	/**
	 * Bind to an existing property from the manager owner object.
	 * 
	 * By binding to an existing property, getter and setter functions are automatically set for that property, 
	 * using the given class scope, so it can be accessed and modified directly from outside.<br>
	 * All restrictions set in this property still apply however, therefore attempts at accessing and modifying it 
	 * may still fail accordingly.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param string|null $class [default = null]
	 * <p>The class scope to use.<br>
	 * If not set, then the manager owner object of this instance is used.</p>
	 * @param string|null $name [default = null]
	 * <p>The name to bind to.<br>
	 * If not set, then the name set in this instance is used.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function bind(?string $class = null, ?string $name = null): Property
	{
		//guard
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//initialize
		$owner = $this->manager->getOwner();
		if (!isset($class)) {
			$class = get_class($owner);
		}
		if (!isset($name)) {
			$name = $this->name;
		}
		
		//bind
		$this->setGetter(\Closure::bind(function () use ($name) {
			return $this->$name;
		}, $owner, $class));
		$this->setSetter(\Closure::bind(function ($value) use ($name): void {
			$this->$name = $value;
		}, $owner, $class));
		
		//return
		return $this;
	}
}
