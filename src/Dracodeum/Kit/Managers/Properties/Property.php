<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\Properties;

use Dracodeum\Kit\Interfaces\Uncloneable as IUncloneable;
use Dracodeum\Kit\Managers\Properties as Manager;
use Dracodeum\Kit\Managers\Properties\Property\Exceptions;
use Dracodeum\Kit\Traits;
use Dracodeum\Kit\Utilities\{
	Call as UCall,
	Text as UText
};

class Property implements IUncloneable
{	
	//Traits
	use Traits\Evaluators;
	use Traits\Uncloneable;
	
	
	
	//Private constants
	/** Initialized flag. */
	private const FLAG_INITIALIZED = 0x001;
	
	/** Optional flag. */
	private const FLAG_OPTIONAL = 0x002;
	
	/** Automatic flag. */
	private const FLAG_AUTOMATIC = 0x004;
	
	/** Immutable flag. */
	private const FLAG_IMMUTABLE = 0x008;
	
	/** Value flag. */
	private const FLAG_VALUE = 0x010;
	
	/** Getter flag. */
	private const FLAG_GETTER = 0x020;
	
	/** Default value flag. */
	private const FLAG_DEFAULT_VALUE = 0x040;
	
	/** Default getter flag. */
	private const FLAG_DEFAULT_GETTER = 0x080;
	
	/** Lazy flag. */
	private const FLAG_LAZY = 0x100;
	
	/** Lazy value flag. */
	private const FLAG_LAZY_VALUE = 0x200;
	
	/** Auto-immutable flag. */
	private const FLAG_AUTOIMMUTABLE = self::FLAG_AUTOMATIC | self::FLAG_IMMUTABLE;
	
	/** Default flag. */
	private const FLAG_DEFAULT = self::FLAG_DEFAULT_VALUE | self::FLAG_DEFAULT_GETTER;
	
	/** Gettable flag. */
	private const FLAG_GETTABLE = self::FLAG_VALUE | self::FLAG_GETTER | self::FLAG_DEFAULT | self::FLAG_LAZY_VALUE;
	
	
	
	//Private properties
	/** @var \Dracodeum\Kit\Managers\Properties */
	private $manager;
	
	/** @var string */
	private $name;
	
	/** @var string|null */
	private $mode = null;
	
	/** @var mixed */
	private $value_getter = null;
	
	/** @var mixed */
	private $default_value_getter = null;
	
	/** @var \Closure|null */
	private $setter = null;
	
	/** @var int */
	private $flags = 0x00;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param \Dracodeum\Kit\Managers\Properties $manager
	 * <p>The manager instance to instantiate with.</p>
	 * @param string $name
	 * <p>The name to instantiate with.</p>
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
	 * @return \Dracodeum\Kit\Managers\Properties
	 * <p>The manager instance.</p>
	 */
	final public function getManager(): Manager
	{
		return $this->manager;
	}
	
	/**
	 * Get name.
	 * 
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
	 * @return bool
	 * <p>Boolean <code>true</code> if is initialized.</p>
	 */
	final public function isInitialized(): bool
	{
		return $this->flags & self::FLAG_INITIALIZED;
	}
	
	/**
	 * Initialize.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function initialize(): Property
	{
		$this->guardNonInitializedCall();
		$this->flags |= self::FLAG_INITIALIZED;
		return $this;
	}
	
	/**
	 * Reset.
	 * 
	 * This method may only be called after initialization.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function reset(): Property
	{
		$this->guardInitializedCall();
		if (!$this->hasGetter()) {
			$this->value_getter = null;
			$this->flags &= ~(self::FLAG_VALUE | self::FLAG_LAZY_VALUE);
			if (!$this->isGettable()) {
				$this->flags &= ~self::FLAG_INITIALIZED;
			}
		}
		return $this;
	}
	
	/**
	 * Check if is required.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is required.</p>
	 */
	final public function isRequired(): bool
	{
		return !$this->isOptional();
	}
	
	/**
	 * Check if is optional.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is optional.</p>
	 */
	final public function isOptional(): bool
	{
		if ($this->manager->isLazy() && $this->manager->isRequiredPropertyName($this->name)) {
			return false;
		}
		return ($this->flags & self::FLAG_OPTIONAL) || $this->hasDefault() || $this->hasGetter() || 
			($this->isAutomatic() && !$this->manager->isPersisted());
	}
	
	/**
	 * Set as optional.
	 * 
	 * Even without being explicitly set as optional, a property is considered so if it has a default value 
	 * or a getter function set, or if it is automatic and not yet persisted.<br>
	 * <br>
	 * This method may only be called before initialization, of both the property and the manager.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsOptional(): Property
	{
		$this->guardNonInitializedCall(true);
		$this->flags |= self::FLAG_OPTIONAL;
		return $this;
	}
	
	/**
	 * Get mode.
	 * 
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
	 * @param string $mode
	 * <p>The mode to set, which must be one the following:<br>
	 * &nbsp; &#8226; &nbsp; <samp>r</samp> : Allow this property to be only strictly read from, 
	 * so that it cannot be given during initialization (strict read-only).<br>
	 * &nbsp; &#8226; &nbsp; <samp>r+</samp> : Allow this property to be only read from (read-only), 
	 * although it may still be given during initialization.<br>
	 * &nbsp; &#8226; &nbsp; <samp>rw</samp> : Allow this property to be both read from and written to (read-write).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w</samp> : Allow this property to be only written to (write-only).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w-</samp> : Allow this property to be only written to, 
	 * but only once during initialization (write-once).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w--</samp> : Allow this property to be only written to, 
	 * but only once during initialization (write-once), and drop it immediately after initialization (transient).<br>
	 * <br>
	 * NOTE: The allowed modes may be more restricted depending on the global mode set in the manager:<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>r</samp> or <samp>r+</samp>, 
	 * only <samp>r</samp>, <samp>r+</samp> and <samp>rw</samp> are allowed;<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>rw</samp>, all modes are allowed;<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>w</samp>, <samp>w-</samp> or <samp>w--</samp>, 
	 * only <samp>rw</samp>, <samp>w</samp>, <samp>w-</samp> and <samp>w--</samp> are allowed.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setMode(string $mode): Property
	{
		//maps
		static $maps = [
			'r' => ['r' => 'r', 'r+' => 'r', 'rw' => 'r'],
			'r+' => ['r' => 'r', 'r+' => 'r+', 'rw' => 'r+'],
			'rw' => ['r' => 'r', 'r+' => 'r+', 'rw' => 'rw', 'w' => 'w', 'w-' => 'w-', 'w--' => 'w--'],
			'w' => ['rw' => 'w', 'w' => 'w', 'w-' => 'w-', 'w--' => 'w--'],
			'w-' => ['rw' => 'w-', 'w' => 'w-', 'w-' => 'w-', 'w--' => 'w--'],
			'w--' => ['rw' => 'w--', 'w' => 'w--', 'w-' => 'w--', 'w--' => 'w--']
		];
		
		//manager
		$manager_mode = $this->manager->getMode();
		if (!isset($maps[$manager_mode])) {
			UCall::haltInternal([
				'error_message' => "Invalid manager mode {{mode}} in {{manager}} with owner {{manager.getOwner()}}.",
				'parameters' => ['mode' => $manager_mode, 'manager' => $this->manager]
			]);
		}
		
		//set
		$map = $maps[$manager_mode];
		if (!isset($map[$mode])) {
			UCall::haltParameter('mode', $mode, [
				'hint_message' => "Only the following modes are allowed for property {{property.getName()}} " . 
					"in manager with owner {{property.getManager().getOwner()}}: {{modes}}.",
				'parameters' => ['property' => $this, 'modes' => array_keys($map)],
				'string_options' => ['non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND]
			]);
		}
		$this->mode = $map[$mode];
		
		//return
		return $this;
	}
	
	/**
	 * Check if is gettable.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is gettable.</p>
	 */
	final public function isGettable(): bool
	{
		return $this->flags & self::FLAG_GETTABLE;
	}
	
	/**
	 * Check if is settable.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is settable.</p>
	 */
	final public function isSettable(): bool
	{
		return $this->hasSetter() || !$this->hasGetter();
	}
	
	/**
	 * Check if is automatic.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is automatic.</p>
	 */
	final public function isAutomatic(): bool
	{
		return $this->flags & self::FLAG_AUTOMATIC;
	}
	
	/**
	 * Set as automatic.
	 * 
	 * By setting this property as automatic, no value is allowed to be set while this property has not yet been 
	 * persisted at least once, as the value of this property is to be automatically generated during the first 
	 * persistence.<br>
	 * <br>
	 * This method may only be called before initialization, of both the property and the manager.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsAutomatic(): Property
	{
		$this->guardNonInitializedCall(true);
		$this->flags |= self::FLAG_AUTOMATIC;
		return $this;
	}
	
	/**
	 * Check if is immutable.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is immutable.</p>
	 */
	final public function isImmutable(): bool
	{
		return $this->flags & self::FLAG_IMMUTABLE;
	}
	
	/**
	 * Set as immutable.
	 * 
	 * By setting this property as immutable, no value is allowed to be set after this property has already been 
	 * persisted at least once, as the value of this property is only meant to be set before the first data 
	 * persistence.<br>
	 * <br>
	 * This method may only be called before initialization, of both the property and the manager.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsImmutable(): Property
	{
		$this->guardNonInitializedCall(true);
		$this->flags |= self::FLAG_IMMUTABLE;
		return $this;
	}
	
	/**
	 * Check if is auto-immutable (both automatic and immutable).
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is auto-immutable (both automatic and immutable).</p>
	 */
	final public function isAutoImmutable(): bool
	{
		return $this->isAutomatic() && $this->isImmutable();
	}
	
	/**
	 * Set as auto-immutable (both automatic and immutable).
	 * 
	 * By setting this property as auto-immutable (both automatic and immutable), no value is ever allowed to be set, 
	 * as the value of this property is to be automatically generated once during the first persistence and then remain 
	 * as an immutable read-only value.<br>
	 * <br>
	 * This method may only be called before initialization, of both the property and the manager.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsAutoImmutable(): Property
	{
		$this->guardNonInitializedCall(true);
		$this->flags |= self::FLAG_AUTOIMMUTABLE;
		return $this;
	}
	
	/**
	 * Check if is lazy.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is lazy.</p>
	 */
	final public function isLazy(): bool
	{
		return $this->flags & self::FLAG_LAZY;
	}
	
	/**
	 * Set as lazy.
	 * 
	 * By setting this property as lazy, whenever a new value is set, no value evaluation is performed immediately, 
	 * being only performed later when the new value is first retrieved.<br>
	 * <br>
	 * This method may only be called before initialization, of both the property and the manager, 
	 * and only if a getter function has not been set.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsLazy(): Property
	{
		$this->guardNonInitializedCall(true);
		$this->guardGetterNotSetCall();
		$this->flags |= self::FLAG_LAZY;
		return $this;
	}
	
	/**
	 * Check if has lazy value.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if has lazy value.</p>
	 */
	final public function hasLazyValue(): bool
	{
		return $this->flags & self::FLAG_LAZY_VALUE;
	}
	
	/**
	 * Get value.
	 * 
	 * This method may only be called after initialization.
	 * 
	 * @param bool $lazy [default = false]
	 * <p>Get the lazily set value without evaluating it, if currently set as such.</p>
	 * @return mixed
	 * <p>The value.</p>
	 */
	final public function getValue(bool $lazy = false)
	{
		//guard
		$this->guardInitializedCall();
		
		//lazy
		if ($this->flags & self::FLAG_LAZY_VALUE) {
			if ($lazy) {
				return $this->value_getter;
			}
			$this->setValue($this->value_getter, true);
		}
		
		//get
		if ($this->flags & self::FLAG_VALUE) {
			return $this->value_getter;
		} elseif ($this->flags & self::FLAG_GETTER) {
			$value = ($this->value_getter)();
			if (!$this->evaluateValue($value)) {
				UCall::haltInternal([
					'error_message' => "Invalid getter value {{value}} for property {{property.getName()}} " . 
						"in manager with owner {{property.getManager().getOwner()}}.",
					'parameters' => ['property' => $this, 'value' => $value]
				]);
			}
			return $value;
		}
		return $this->getDefaultValue();
	}
	
	/**
	 * Evaluate value.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated.</p>
	 */
	final public function evaluateValue(&$value): bool
	{
		return $this->getEvaluatorsManager()->evaluate($value);
	}
	
	/**
	 * Set value.
	 * 
	 * This method may only be called during or after the manager initialization, and only if the property is not a 
	 * getter (getter function set without a setter function).
	 * 
	 * @param mixed $value
	 * <p>The value to set.</p>
	 * @param bool $force [default = false]
	 * <p>Force the given value to be fully evaluated and set, even if this property is set as lazy.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Managers\Properties\Property\Exceptions\InvalidValue
	 * @return $this|bool
	 * <p>This instance, for chaining purposes.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then boolean <code>true</code> is returned if the value was successfully set, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public function setValue($value, bool $force = false, bool $no_throw = false)
	{
		//guard
		if (!$this->manager->isInitialized() && !$this->manager->isInitializing()) {
			UCall::halt([
				'hint_message' => "This method may only be called during or after the manager initialization, " . 
					"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
				'parameters' => ['property' => $this]
			]);
		} elseif ($this->hasGetter() && !$this->hasSetter()) {
			UCall::halt([
				'error_message' => "Cannot set value in getter property {{property.getName()}} " . 
					"in manager with owner {{property.getManager().getOwner()}}.",
				'parameters' => ['property' => $this]
			]);
		}
		
		//set
		if ($this->isLazy() && !$force) {
			$this->value_getter = $value;
			$this->flags |= self::FLAG_LAZY_VALUE;
			$this->flags &= ~self::FLAG_VALUE;
		} else {
			//evaluate
			if (!$this->evaluateValue($value)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\InvalidValue([$this, $value]);
			}
			
			//set
			if ($this->setter !== null) {
				($this->setter)($value);
			} else {
				$this->value_getter = $value;
				$this->flags |= self::FLAG_VALUE;
			}
			$this->flags &= ~self::FLAG_LAZY_VALUE;
		}
		
		//evaluators
		$this->lockEvaluators([
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//initialized
		$this->flags |= self::FLAG_INITIALIZED;
		
		//return
		return $no_throw ? true : $this;
	}
	
	/**
	 * Check if has default.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if has default.</p>
	 */
	final public function hasDefault(): bool
	{
		return $this->flags & self::FLAG_DEFAULT;
	}
	
	/**
	 * Check if is defaulted.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is defaulted.</p>
	 */
	final public function isDefaulted(): bool
	{
		return $this->hasDefault() && !($this->flags & (self::FLAG_VALUE | self::FLAG_GETTER | self::FLAG_LAZY_VALUE));
	}
	
	/**
	 * Get default value.
	 * 
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Managers\Properties\Property\Exceptions\DefaultValueNotSet
	 * @return mixed
	 * <p>The default value.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> may also be returned if none is set.</p>
	 */
	final public function getDefaultValue(bool $no_throw = false)
	{
		//check
		if (!$this->hasDefault()) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\DefaultValueNotSet([$this]);
		}
		
		//value
		$value = null;
		if ($this->flags & self::FLAG_DEFAULT_VALUE) {
			$value = $this->default_value_getter;
		} elseif ($this->flags & self::FLAG_DEFAULT_GETTER) {
			$value = ($this->default_value_getter)();
		}
		
		//evaluate
		if (!$this->evaluateValue($value)) {
			UCall::haltInternal([
				'error_message' => "Invalid default value {{value}} for property {{property.getName()}} " . 
					"in manager with owner {{property.getManager().getOwner()}}.",
				'parameters' => ['property' => $this, 'value' => $value]
			]);
		}
		
		//return
		return $value;
	}
	
	/**
	 * Set default value.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @param mixed $value
	 * <p>The default value to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setDefaultValue($value): Property
	{
		$this->guardNonInitializedCall();
		$this->default_value_getter = $value;
		$this->flags |= self::FLAG_DEFAULT_VALUE;
		$this->flags &= ~self::FLAG_DEFAULT_GETTER;
		return $this;
	}
	
	/**
	 * Set default getter function.
	 * 
	 * By setting a default getter function, the default value will always be retrieved using that function.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
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
		$this->guardNonInitializedCall();
		UCall::assert('getter', $getter, function () {});
		$this->default_value_getter = \Closure::fromCallable($getter);
		$this->flags |= self::FLAG_DEFAULT_GETTER;
		$this->flags &= ~self::FLAG_DEFAULT_VALUE;
		return $this;
	}
	
	/**
	 * Unset value.
	 * 
	 * This method may only be called after initialization, and only if a getter function has not been set.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unsetValue(): Property
	{
		$this->guardInitializedCall();
		$this->guardGetterNotSetCall();
		$this->value_getter = null;
		$this->flags &= ~(self::FLAG_VALUE | self::FLAG_LAZY_VALUE);
		return $this;
	}
	
	/**
	 * Check if has getter function.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if has getter function.</p>
	 */
	final public function hasGetter(): bool
	{
		return $this->flags & self::FLAG_GETTER;
	}
	
	/**
	 * Set getter function.
	 * 
	 * By setting a getter function, the value will always be retrieved using that function.<br>
	 * <br>
	 * This method may only be called before initialization, and only if this property has not been set as lazy.
	 * 
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
		$this->guardNonInitializedCall();
		if ($this->isLazy()) {
			UCall::halt([
				'hint_message' => "This method may only be called if this property has not been set as lazy, " . 
					"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
				'parameters' => ['property' => $this]
			]);
		}
		
		//set
		UCall::assert('getter', $getter, function () {});
		$this->value_getter = \Closure::fromCallable($getter);
		$this->flags |= self::FLAG_GETTER;
		
		//return
		return $this;
	}
	
	/**
	 * Check if has setter function.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if has setter function.</p>
	 */
	final public function hasSetter(): bool
	{
		return $this->setter !== null;
	}
	
	/**
	 * Set setter function.
	 * 
	 * By setting a setter function, the value will always be set using that function.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @param callable $setter
	 * <p>The setter function to set.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ($value): void</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The value to set.<br>
	 * <br>
	 * Return: <code><b>void</b></code></p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setSetter(callable $setter): Property
	{
		$this->guardNonInitializedCall();
		UCall::assert('setter', $setter, function ($value): void {});
		$this->setter = \Closure::fromCallable($setter);
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
	 * @param string|null $class [default = null]
	 * <p>The class scope to use.<br>
	 * If not set, then the class of the manager owner object of this instance is used.</p>
	 * @param string|null $name [default = null]
	 * <p>The name to bind to.<br>
	 * If not set, then the name set in this instance is used.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function bind(?string $class = null, ?string $name = null): Property
	{
		//guard
		$this->guardNonInitializedCall();
		
		//initialize
		$owner = $this->manager->getOwner();
		if ($class === null) {
			$class = get_class($owner);
		}
		if ($name === null) {
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
	
	
	
	//Final protected methods
	/**
	 * Guard the current function or method in the stack so it may only be called if this instance is not initialized.
	 * 
	 * @param bool $full [default = false]
	 * <p>Perform a full initialization check.</p>
	 * @return void
	 */
	final protected function guardNonInitializedCall(bool $full = false): void
	{
		if ($this->isInitialized()) {
			UCall::halt([
				'hint_message' => "This method may only be called before initialization, " . 
					"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
				'parameters' => ['property' => $this],
				'stack_offset' => 1
			]);
		} elseif ($full && $this->manager->isInitialized()) {
			UCall::halt([
				'hint_message' => "This method may only be called before the manager initialization, " . 
					"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
				'parameters' => ['property' => $this],
				'stack_offset' => 1
			]);
		}
	}
	
	/**
	 * Guard the current function or method in the stack so it may only be called if this instance is initialized.
	 * 
	 * @return void
	 */
	final protected function guardInitializedCall(): void
	{
		if (!$this->isInitialized()) {
			UCall::halt([
				'hint_message' => "This method may only be called after initialization, " . 
					"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
				'parameters' => ['property' => $this],
				'stack_offset' => 1
			]);
		}
	}
	
	/**
	 * Guard the current function or method in the stack so it may only be called if no getter function is set.
	 * 
	 * @return void
	 */
	final protected function guardGetterNotSetCall(): void
	{
		if ($this->hasGetter()) {
			UCall::halt([
				'hint_message' => "This method may only be called if a getter function has not been set, " . 
					"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
				'parameters' => ['property' => $this],
				'stack_offset' => 1
			]);
		}
	}
}
