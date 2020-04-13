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
	
	/** Required flag. */
	private const FLAG_REQUIRED = 0x002;
	
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
		UCall::guard(!$this->isInitialized(), [
			'error_message' => "Property {{property.getName()}} already initialized " . 
				"in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		$this->flags |= self::FLAG_INITIALIZED;
		return $this;
	}
	
	/**
	 * Uninitialize.
	 * 
	 * This method may only be called after initialization.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function uninitialize(): Property
	{
		//guard
		UCall::guard($this->isInitialized(), [
			'error_message' => "Property {{property.getName()}} has not been initialized yet " . 
				"in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//unset
		if (!($this->flags & self::FLAG_GETTER)) {
			$this->value_getter = null;
			$this->flags &= ~(self::FLAG_VALUE | self::FLAG_LAZY_VALUE);
		}
		
		//finalize
		$this->flags &= ~self::FLAG_INITIALIZED;
		
		//return
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
		if (!$this->manager->isPersisted() && $this->isAutomatic()) {
			return false;
		} elseif (
			($this->flags & self::FLAG_REQUIRED) || 
			($this->manager->isLazy() && $this->manager->isRequiredPropertyName($this->name))
		) {
			return true;
		} elseif ($this->flags & self::FLAG_GETTER) {
			return false;
		}
		return !$this->hasDefault();
	}
	
	/**
	 * Set as required.
	 * 
	 * Even without being explicitly set as required, a property is considered so if it has no default value set.<br>
	 * <br>
	 * This method may only be called before initialization, of both the property and the manager, 
	 * with lazy-loading disabled and only if the mode is not set to strict read-only.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsRequired(): Property
	{
		//guard
		UCall::guard(!$this->isInitialized(), [
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
		$this->flags |= self::FLAG_REQUIRED;
		
		//return
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
				$map = ['rw' => 'w', 'w' => 'w', 'w-' => 'w-', 'w--' => 'w--'];
				break;
			case 'w-':
				$map = ['rw' => 'w-', 'w' => 'w-', 'w-' => 'w-', 'w--' => 'w--'];
				break;
			case 'w--':
				$map = ['rw' => 'w--', 'w' => 'w--', 'w-' => 'w--', 'w--' => 'w--'];
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
	 * persisted at least once, as the value of this property is meant to be automatically generated during the first 
	 * data persistence.<br>
	 * <br>
	 * This method may only be called before initialization, of both the property and the manager.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsAutomatic(): Property
	{
		//guard
		UCall::guard(!$this->isInitialized(), [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		UCall::guard(!$this->manager->isInitialized(), [
			'hint_message' => "This method may only be called before the manager initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//set
		$this->flags |= self::FLAG_AUTOMATIC;
		
		//return
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
		//guard
		UCall::guard(!$this->isInitialized(), [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		UCall::guard(!$this->manager->isInitialized(), [
			'hint_message' => "This method may only be called before the manager initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//set
		$this->flags |= self::FLAG_IMMUTABLE;
		
		//return
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
		return ($this->flags & self::FLAG_AUTOMATIC) && ($this->flags & self::FLAG_IMMUTABLE);
	}
	
	/**
	 * Set as auto-immutable (both automatic and immutable).
	 * 
	 * By setting this property as auto-immutable (both automatic and immutable), no value is ever allowed to be set, 
	 * as the value of this property is meant to be automatically generated once during the first data persistence and 
	 * then remain as an immutable read-only value.<br>
	 * <br>
	 * This method may only be called before initialization, of both the property and the manager.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsAutoImmutable(): Property
	{
		//guard
		UCall::guard(!$this->isInitialized(), [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		UCall::guard(!$this->manager->isInitialized(), [
			'hint_message' => "This method may only be called before the manager initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//set
		$this->flags |= self::FLAG_AUTOIMMUTABLE;
		
		//return
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
	 * being only performed later on when the new value is first retrieved.<br>
	 * <br>
	 * This method may only be called before initialization, of both the property and the manager, 
	 * and only if a getter function has not been set.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsLazy(): Property
	{
		//guard
		UCall::guard(!$this->isInitialized(), [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		UCall::guard(!$this->manager->isInitialized(), [
			'hint_message' => "This method may only be called before the manager initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		UCall::guard(!($this->flags & self::FLAG_GETTER), [
			'hint_message' => "This method may only be called if a getter function has not been set, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//set
		$this->flags |= self::FLAG_LAZY;
		
		//return
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
		UCall::guard($this->isInitialized(), [
			'hint_message' => "This method may only be called after initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
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
			//get
			$value = ($this->value_getter)();
			
			//evaluate
			UCall::guardInternal($this->getEvaluatorsManager()->evaluate($value), [
				'error_message' => "Invalid getter value {{value}} for property {{property.getName()}} " . 
					"in manager with owner {{property.getManager().getOwner()}}.",
				'parameters' => ['property' => $this, 'value' => $value]
			]);
			
			//return
			return $value;
		}
		return $this->getDefaultValue();
	}
	
	/**
	 * Set value.
	 * 
	 * This method may only be called during or after the manager initialization.
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
		UCall::guard($this->manager->isInitialized() || $this->manager->isInitializing(), [
			'hint_message' => "This method may only be called during or after the manager initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//set
		if (!$force && $this->isLazy()) {
			$this->value_getter = $value;
			$this->flags |= self::FLAG_LAZY_VALUE;
			$this->flags &= ~self::FLAG_VALUE;
		} else {
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
				//guard
				UCall::guard(!($this->flags & self::FLAG_GETTER), [
					'error_message' => "Cannot set value in getter property {{property.getName()}} " . 
						"in manager with owner {{property.getManager().getOwner()}}.",
					'parameters' => ['property' => $this]
				]);
				
				//set
				$this->value_getter = $value;
				$this->flags |= self::FLAG_VALUE;
			}
			$this->flags &= ~self::FLAG_LAZY_VALUE;
		}
		
		//initialized
		$this->lockEvaluators([
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
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
		return $this->hasDefault() && !($this->flags & (self::FLAG_VALUE | self::FLAG_GETTER));
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
	 * @param mixed $value
	 * <p>The default value to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setDefaultValue($value): Property
	{
		//guard
		UCall::guard(!$this->isInitialized(), [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//set
		$this->default_value_getter = $value;
		$this->flags |= self::FLAG_DEFAULT_VALUE;
		$this->flags &= ~self::FLAG_DEFAULT_GETTER;
		
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
		//guard
		UCall::guard(!$this->isInitialized(), [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		UCall::assert('getter', $getter, function () {});
		
		//set
		$this->default_value_getter = \Closure::fromCallable($getter);
		$this->flags |= self::FLAG_DEFAULT_GETTER;
		$this->flags &= ~self::FLAG_DEFAULT_VALUE;
		
		//return
		return $this;
	}
	
	/**
	 * Unset value.
	 * 
	 * This method may only be called after initialization.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unsetValue(): Property
	{
		//guard
		UCall::guard($this->isInitialized(), [
			'hint_message' => "This method may only be called after initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		UCall::guard(!($this->flags & self::FLAG_GETTER), [
			'error_message' => "Cannot unset value in getter property {{property.getName()}} " . 
				"in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//unset
		$this->value_getter = null;
		$this->flags &= ~(self::FLAG_VALUE | self::FLAG_LAZY_VALUE);
		
		//return
		return $this;
	}
	
	/**
	 * Set getter function.
	 * 
	 * By setting a getter function, the value will always be got using that function.<br>
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
		UCall::guard(!$this->isInitialized(), [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		UCall::guard(!$this->isLazy(), [
			'hint_message' => "This method may only be called if this property has not been set as lazy, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//set
		UCall::assert('getter', $getter, function () {});
		$this->value_getter = \Closure::fromCallable($getter);
		$this->flags |= self::FLAG_GETTER;
		
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
		UCall::guard(!$this->isInitialized(), [
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
	 * All restrictions set in this property still apply however, thus attempts at accessing and modifying it 
	 * may still fail accordingly.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
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
		UCall::guard(!$this->isInitialized(), [
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
