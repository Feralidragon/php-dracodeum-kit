<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components;

use Feralygon\Kit\Component;
use Feralygon\Kit\Prototypes\Input\Subcontracts\{
	ConstraintCreator as IPrototypeConstraintCreator,
	FilterCreator as IPrototypeFilterCreator
};
use Feralygon\Kit\Components\Input\{
	Components,
	Exceptions,
	Error,
	Options,
	Structures
};
use Feralygon\Kit\Factories\Component as Factory;
use Feralygon\Kit\Components\Input\Factories\Component as FComponent;
use Feralygon\Kit\Prototypes\{
	Input as Prototype,
	Inputs as Prototypes
};
use Feralygon\Kit\Prototypes\Input\Interfaces as PrototypeInterfaces;
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Prototype as ComponentPrototype;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Utilities\{
	Call as UCall,
	Text as UText
};

/**
 * This component represents an input which can check, sanitize and hold a value.
 * 
 * @since 1.0.0
 * @property-read bool $nullable [default = false]
 * <p>Allow a <code>null</code> value to be set.</p>
 * @property-write \Feralygon\Kit\Components\Input\Components\Modifier[]|string[] $modifiers [once] [default = []]
 * <p>The modifiers to add, as any combination of the following:<br>
 * &nbsp; &#8226; &nbsp; instances, classes or names;<br>
 * &nbsp; &#8226; &nbsp; <samp>class => properties</samp> or <samp>name => properties</samp> pairs, 
 * with the properties being given as <samp>name => value</samp> pairs.</p>
 * @see \Feralygon\Kit\Prototypes\Input
 * @see \Feralygon\Kit\Prototypes\Inputs\Boolean
 * [prototype, name = 'boolean' or 'bool']
 * @see \Feralygon\Kit\Prototypes\Inputs\Number
 * [prototype, name = 'number']
 * @see \Feralygon\Kit\Prototypes\Inputs\Numbers\Float64
 * [prototype, name = 'float64' or 'float' or 'double']
 * @see \Feralygon\Kit\Prototypes\Inputs\Numbers\Integer
 * [prototype, name = 'integer' or 'int' or 'uint' or 'int64' or 'uint63' or 'int32' or 'uint32' or 'uint31' or 
 * 'int16' or 'uint16' or 'uint15' or 'int8' or 'uint8' or 'uint7']
 * @see \Feralygon\Kit\Prototypes\Inputs\Numbers\Size
 * [prototype, name = 'size']
 * @see \Feralygon\Kit\Prototypes\Inputs\Enumeration
 * [prototype, name = 'enumeration' or 'enum']
 * @see \Feralygon\Kit\Prototypes\Inputs\Text
 * [prototype, name = 'text' or 'string']
 * @see \Feralygon\Kit\Prototypes\Inputs\Hashes\Crc32
 * [prototype, name = 'crc32']
 * @see \Feralygon\Kit\Prototypes\Inputs\Hashes\Md5
 * [prototype, name = 'md5']
 * @see \Feralygon\Kit\Prototypes\Inputs\Hashes\Sha1
 * [prototype, name = 'sha1']
 * @see \Feralygon\Kit\Prototypes\Inputs\Hashes\Sha256
 * [prototype, name = 'sha256']
 * @see \Feralygon\Kit\Prototypes\Inputs\Hashes\Sha512
 * [prototype, name = 'sha512']
 * @see \Feralygon\Kit\Prototypes\Inputs\Date
 * [prototype, name = 'date']
 * @see \Feralygon\Kit\Prototypes\Inputs\Time
 * [prototype, name = 'time']
 * @see \Feralygon\Kit\Prototypes\Inputs\DateTime
 * [prototype, name = 'datetime' or 'timestamp']
 */
class Input extends Component implements IPrototypeConstraintCreator, IPrototypeFilterCreator
{
	//Private properties
	/** @var bool */
	private $initialized = false;
	
	/** @var mixed */
	private $value = null;
	
	/** @var bool */
	private $nullable = false;
	
	/** @var \Feralygon\Kit\Components\Input\Error|null */
	private $error = null;
	
	/** @var \Feralygon\Kit\Components\Input\Components\Modifier[] */
	private $modifiers_tree = [];
	
	/** @var \Closure[] */
	private $value_evaluators_tree = [];
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getBasePrototypeClass(): string
	{
		return Prototype::class;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Subcontracts\ConstraintCreator)
	/** {@inheritdoc} */
	public function createConstraint($prototype, array $properties = []): Components\Modifiers\Constraint
	{
		return FComponent::constraint($prototype, $properties);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Subcontracts\FilterCreator)
	/** {@inheritdoc} */
	public function createFilter($prototype, array $properties = []): Components\Modifiers\Filter
	{
		return FComponent::filter($prototype, $properties);
	}
	
	
	
	//Implemented protected static methods (Feralygon\Kit\Component\Traits\DefaultBuilder)
	/** {@inheritdoc} */
	protected static function getDefaultBuilder(): ?callable
	{
		return [Factory::class, 'input'];
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Component\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'nullable':
				return $this->createProperty()->setMode('r+')->setAsBoolean()->bind(self::class);
			case 'modifiers':
				return $this->createProperty()
					->setMode('w-')
					->setAsArray(function (&$key, &$value): bool {
						if (is_string($key) && is_array($value)) {
							$this->addModifier($key, $value);
							return true;
						} elseif (is_int($key) && (is_string($value) || is_object($value))) {
							$this->addModifier($value);
							return true;
						}
						return false;
					})
				;
		}
		return null;
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Component\Traits\PrototypeBuilder)
	/** {@inheritdoc} */
	protected function buildPrototype(string $name, array $properties = []): ?ComponentPrototype
	{
		switch ($name) {
			case 'boolean':
				//no break
			case 'bool':
				return new Prototypes\Boolean($properties);
			case 'number':
				return new Prototypes\Number($properties);
			case 'float64':
				//no break
			case 'float':
				//no break
			case 'double':
				return new Prototypes\Numbers\Float64($properties);
			case 'integer':
				//no break
			case 'int':
				return new Prototypes\Numbers\Integer($properties);
			case 'uint':
				return new Prototypes\Numbers\Integer(['unsigned' => true] + $properties);
			case 'int64':
				return new Prototypes\Numbers\Integer(['bits' => 64] + $properties);
			case 'uint63':
				return new Prototypes\Numbers\Integer(['unsigned' => true, 'bits' => 63] + $properties);
			case 'int32':
				return new Prototypes\Numbers\Integer(['bits' => 32] + $properties);
			case 'uint32':
				return new Prototypes\Numbers\Integer(['unsigned' => true, 'bits' => 32] + $properties);
			case 'uint31':
				return new Prototypes\Numbers\Integer(['unsigned' => true, 'bits' => 31] + $properties);
			case 'int16':
				return new Prototypes\Numbers\Integer(['bits' => 16] + $properties);
			case 'uint16':
				return new Prototypes\Numbers\Integer(['unsigned' => true, 'bits' => 16] + $properties);
			case 'uint15':
				return new Prototypes\Numbers\Integer(['unsigned' => true, 'bits' => 15] + $properties);
			case 'int8':
				return new Prototypes\Numbers\Integer(['bits' => 8] + $properties);
			case 'uint8':
				return new Prototypes\Numbers\Integer(['unsigned' => true, 'bits' => 8] + $properties);
			case 'uint7':
				return new Prototypes\Numbers\Integer(['unsigned' => true, 'bits' => 7] + $properties);
			case 'size':
				return new Prototypes\Numbers\Size($properties);
			case 'enumeration':
				//no break
			case 'enum':
				return new Prototypes\Enumeration($properties);
			case 'text':
				//no break
			case 'string':
				return new Prototypes\Text($properties);
			case 'crc32':
				return new Prototypes\Hashes\Crc32($properties);
			case 'md5':
				return new Prototypes\Hashes\Md5($properties);
			case 'sha1':
				return new Prototypes\Hashes\Sha1($properties);
			case 'sha256':
				return new Prototypes\Hashes\Sha256($properties);
			case 'sha512':
				return new Prototypes\Hashes\Sha512($properties);
			case 'date':
				return new Prototypes\Date($properties);
			case 'time':
				return new Prototypes\Time($properties);
			case 'datetime':
				//no break
			case 'timestamp':
				return new Prototypes\DateTime($properties);
		}
		return null;
	}
	
	
	
	//Public methods
	/**
	 * Get name.
	 * 
	 * The returning name is a canonical string, which uniquely identifies this input.
	 * 
	 * @since 1.0.0
	 * @return string
	 * <p>The name.</p>
	 */
	public function getName(): string
	{
		return $this->getPrototype()->getName();
	}
	
	/**
	 * Get value string.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The value string.</p>
	 */
	public function getValueString($text_options = null): string
	{
		$value = $this->getValue();
		$text_options = TextOptions::coerce($text_options);
		$prototype = $this->getPrototype();
		return isset($value) && $prototype instanceof PrototypeInterfaces\ValueStringifier
			? $prototype->stringifyValue($value, $text_options)
			: UText::stringify($value, $text_options, ['quote_strings' => true]);
	}
	
	/**
	 * Get label.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @param \Feralygon\Kit\Components\Input\Options\Info|array|null $info_options [default = null]
	 * <p>The info options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The label.</p>
	 */
	public function getLabel($text_options = null, $info_options = null): string
	{
		//label
		$text_options = TextOptions::coerce($text_options);
		$info_options = Options\Info::coerce($info_options);
		$prototype = $this->getPrototype();
		$label = $prototype instanceof PrototypeInterfaces\Information
			? $prototype->getLabel($text_options, $info_options)
			: UText::unslugify($this->getName(), UText::UNSLUG_CAPITALIZE_FIRST);
		
		//null
		if ($this->nullable && !$info_options->exclude_null) {
			$null_label = $this->getDefaultNullLabel($text_options);
			if (isset($null_label)) {
				$label = UText::stringify(
					[$label, UText::uncapitalize($null_label, true)],
					$text_options,
					['non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_OR]
				);
			}
		}
		
		//modifiers
		if (!$info_options->exclude_modifiers) {
			$modifier_labels = $this->getModifierLabels($text_options);
			if (!empty($modifier_labels)) {
				$label .= "\n\n" . implode("\n", $modifier_labels);
			}
		}
		
		//return
		return $label;
	}
	
	/**
	 * Get default null label.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The default null label or <code>null</code> if none is set.</p>
	 */
	public function getDefaultNullLabel($text_options = null): ?string
	{
		$text_options = TextOptions::coerce($text_options);
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			/** @tags non-end-user */
			return UText::localize("Null", self::class, $text_options);
		}
		return null;
	}
	
	/**
	 * Get description.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @param \Feralygon\Kit\Components\Input\Options\Info|array|null $info_options [default = null]
	 * <p>The info options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The description or <code>null</code> if none is set.</p>
	 */
	public function getDescription($text_options = null, $info_options = null): ?string
	{
		//description
		$text_options = TextOptions::coerce($text_options);
		$info_options = Options\Info::coerce($info_options);
		$prototype = $this->getPrototype();
		$description = $prototype instanceof PrototypeInterfaces\Information
			? $prototype->getDescription($text_options, $info_options)
			: null;
		
		//additional information
		if (isset($description)) {
			//null
			if ($this->nullable && !$info_options->exclude_null) {
				$null_description = $this->getDefaultNullDescription($text_options);
				if (isset($null_description)) {
					$description .= "\n{$null_description}";
				}
				unset($null_description);
			}
			
			//modifiers
			if (!$info_options->exclude_modifiers) {
				$modifier_messages = $this->getModifierMessages($text_options);
				if (!empty($modifier_messages)) {
					$description .= "\n\n" . implode("\n", $modifier_messages);
				}
			}
		}
		
		//return
		return $description;
	}
	
	/**
	 * Get default null description.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The default null description or <code>null</code> if none is set.</p>
	 */
	public function getDefaultNullDescription($text_options = null): ?string
	{
		$text_options = TextOptions::coerce($text_options);
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/** @tags end-user */
			return UText::localize("Alternatively, it may also be empty.", self::class, $text_options);
		}
		/** @tags non-end-user */
		return UText::localize("Alternatively, it may also be null.", self::class, $text_options);
	}
	
	/**
	 * Get message.
	 * 
	 * The returning message is assertive relative to the expected value.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @param \Feralygon\Kit\Components\Input\Options\Info|array|null $info_options [default = null]
	 * <p>The info options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The message or <code>null</code> if none is set.</p>
	 */
	public function getMessage($text_options = null, $info_options = null): ?string
	{
		//message
		$text_options = TextOptions::coerce($text_options);
		$info_options = Options\Info::coerce($info_options);
		$prototype = $this->getPrototype();
		$message = $prototype instanceof PrototypeInterfaces\Information
			? $prototype->getMessage($text_options, $info_options)
			: null;
		
		//additional information
		if (isset($message)) {
			//null
			if ($this->nullable && !$info_options->exclude_null) {
				$null_message = $this->getDefaultNullMessage($text_options);
				if (isset($null_message)) {
					$message .= "\n{$null_message}";
				}
				unset($null_message);
			}
			
			//modifiers
			if (!$info_options->exclude_modifiers) {
				$modifier_messages = $this->getModifierMessages($text_options);
				if (!empty($modifier_messages)) {
					$message .= "\n\n" . implode("\n", $modifier_messages);
				}
			}
		}
		
		//return
		return $message;
	}
	
	/**
	 * Get default null message.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The default null message or <code>null</code> if none is set.</p>
	 */
	public function getDefaultNullMessage($text_options = null): ?string
	{
		$text_options = TextOptions::coerce($text_options);
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/** @tags end-user */
			return UText::localize("Alternatively, an empty value may also be given.", self::class, $text_options);
		}
		/** @tags non-end-user */
		return UText::localize("Alternatively, a null value may also be given.", self::class, $text_options);
	}
	
	/**
	 * Get error message.
	 * 
	 * The returning error message is only set if a previous <code>setValue</code> method call has failed.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
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
		
		//messages
		$messages = [];
		foreach ($this->error->getMessengers() as $messenger) {
			$message = $messenger($text_options);
			if (isset($message)) {
				$messages[] = $message;
			}
		}
		if (!empty($messages)) {
			return implode("\n", array_unique($messages, SORT_STRING));
		}
		
		//prototype
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\ErrorMessage) {
			return $prototype->getErrorMessage($text_options);
		} elseif ($prototype instanceof PrototypeInterfaces\Information) {
			return $prototype->getMessage($text_options, new Options\Info());
		}
		
		//default
		return $this->getDefaultErrorMessage($text_options);
	}
	
	/**
	 * Get default error message.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The default error message.</p>
	 */
	public function getDefaultErrorMessage($text_options = null): string
	{
		return UText::localize("The given value is invalid.", self::class, $text_options);
	}
	
	/**
	 * Unset error.
	 * 
	 * @since 1.0.0
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	public function unsetError(): Input
	{
		//modifiers
		foreach ($this->modifiers_tree as $modifiers) {
			foreach ($modifiers as $modifier) {
				$modifier->unsetError();
			}
		}
		
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
	 * The returning schema describes this input by using a structure.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Components\Input\Structures\Schema
	 * <p>The schema instance.</p>
	 */
	public function getSchema(): Structures\Schema
	{
		$prototype = $this->getPrototype();
		return new Structures\Schema([
			'name' => $this->getName(),
			'nullable' => $this->nullable,
			'data' => $prototype instanceof PrototypeInterfaces\SchemaData ? $prototype->getSchemaData() : null,
			'modifiers' => $this->getModifierSchemas()
		], true);
	}
	
	
	
	//Final public methods
	/**
	 * Check if is initialized.
	 * 
	 * An input is only considered to have been initialized after a value has been set.
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
		UCall::guard($this->initialized, function () {
			//error message
			$error_message = $this->getErrorMessage();
			if (isset($error_message)) {
				$error_message = "No value set due to the following error: " . UText::uncapitalize($error_message);
			}
			
			//return
			return [
				'error_message' => $error_message,
				'hint_message' => "This method may only be called after initialization."
			];
		});
		return $this->value;
	}
	
	/**
	 * Set value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value
	 * <p>The value to set.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Components\Input\Exceptions\InvalidValue
	 * @return $this|bool
	 * <p>This instance, for chaining purposes.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then boolean <code>true</code> is returned if the value was successfully set, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public function setValue($value, bool $no_throw = false)
	{
		//initialize
		$this->unsetError();
		$prototype = $this->getPrototype();
		
		//evaluate
		$v = $value;
		if ((!isset($v) && !$this->nullable) || (isset($v) && !$prototype->evaluateValue($v))) {
			$this->error = new Error($value);
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\InvalidValue([
				'component' => $this,
				'prototype' => $prototype,
				'value' => $value,
				'error_message' => $this->getErrorMessage()
			]);
		}
		$value = $v;
		unset($v);
		
		//additional evaluations
		if (isset($value)) {
			//value evaluators (before modifiers)
			if (!$this->evaluateValueWithValueEvaluators($value, true)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\InvalidValue([
					'component' => $this,
					'prototype' => $prototype,
					'value' => $value,
					'error_message' => $this->getErrorMessage()
				]);
			}
			
			//modifiers
			foreach ($this->modifiers_tree as $modifiers) {
				//evaluate
				$v = $value;
				$error_messengers = [];
				foreach ($modifiers as $modifier) {
					if (!$modifier->evaluateValue($v)) {
						$error_messengers[] = function (TextOptions $text_options) use ($modifier): ?string {
							return $modifier->getErrorMessage($text_options);
						};
					}
				}
				
				//error
				if (!empty($error_messengers)) {
					$this->error = new Error($value, $error_messengers);
					if ($no_throw) {
						return false;
					}
					throw new Exceptions\InvalidValue([
						'component' => $this,
						'prototype' => $prototype,
						'value' => $value,
						'error_message' => $this->getErrorMessage()
					]);
				}
				
				//finish
				$value = $v;
				unset($v);
			}
			
			//value evaluators (after modifiers)
			if (!$this->evaluateValueWithValueEvaluators($value, false)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\InvalidValue([
					'component' => $this,
					'prototype' => $prototype,
					'value' => $value,
					'error_message' => $this->getErrorMessage()
				]);
			}
		}
		
		//set
		$this->value = $value;
		$this->initialized = true;
		
		//return
		return $no_throw ? true : $this;
	}
	
	/**
	 * Unset value.
	 * 
	 * By unsetting the value, the input reverts back to an uninitialized state.
	 * 
	 * @since 1.0.0
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unsetValue(): Input
	{
		$this->value = null;
		$this->initialized = false;
		return $this;
	}
	
	/**
	 * Check if is nullable.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if is nullable.</p>
	 */
	final public function isNullable(): bool
	{
		return $this->nullable;
	}
	
	/**
	 * Get modifier instances.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Components\Input\Components\Modifier[]
	 * <p>The modifier instances.</p>
	 */
	final public function getModifiers(): array
	{
		$instances = [];
		foreach ($this->modifiers_tree as $modifiers) {
			foreach ($modifiers as $modifier) {
				$instances[] = $modifier;
			}
		}
		return $instances;
	}
	
	/**
	 * Get modifier labels.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string[]
	 * <p>The modifier labels.</p>
	 */
	final public function getModifierLabels($text_options = null): array
	{
		$labels = [];
		$text_options = TextOptions::coerce($text_options);
		foreach ($this->getModifiers() as $modifier) {
			$label = $modifier->getLabel($text_options);
			if (isset($label)) {
				$string = $modifier->getString($text_options);
				if (isset($string)) {
					/**
					 * @description Modifier label with string.
					 * @placeholder label The modifier label.
					 * @placeholder string The modifier string.
					 * @example Allowed values: "a", "b" and "c"
					 */
					$labels[] = UText::localize(
						"{{label}}: {{string}}",
						self::class, $text_options, ['parameters' => ['label' => $label, 'string' => $string]]
					);
				} else {
					$labels[] = $label;
				}
			}
		}
		return $labels;
	}
	
	/**
	 * Get modifier messages.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string[]
	 * <p>The modifier messages.</p>
	 */
	final public function getModifierMessages($text_options = null): array
	{
		$messages = [];
		$text_options = TextOptions::coerce($text_options);
		foreach ($this->getModifiers() as $modifier) {
			$message = $modifier->getMessage($text_options);
			if (isset($message)) {
				$messages[] = $message;
			}
		}
		return $messages;
	}
	
	/**
	 * Add modifier.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Components\Input\Components\Modifier|string $modifier
	 * <p>The modifier component instance or name to add.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to add with if a component name is given, as <samp>name => value</samp> pairs.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addModifier($modifier, array $properties = []): Input
	{
		//guard
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization."
		]);
		
		//coerce
		$prototype = $this->getPrototype();
		$modifier = Components\Modifier::coerce(
			$modifier, $properties, null,
			$prototype instanceof PrototypeInterfaces\ModifierBuilder ? [$prototype, 'buildModifier'] : null
		);
		
		//add
		$priority = $modifier->getPriority();
		$is_new_priority = empty($this->modifiers_tree[$priority]);
		$this->modifiers_tree[$priority][] = $modifier;
		if ($is_new_priority) {
			krsort($this->modifiers_tree, SORT_NUMERIC);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Add value evaluator function.
	 * 
	 * @since 1.0.0
	 * @param callable $evaluator
	 * <p>The function to evaluate a given value with.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (&$value, ?callable &$error_messenger): bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The value to evaluate (validate and sanitize).<br>
	 * &nbsp; &#8226; &nbsp; <code><b>callable|null $error_messenger</b> [reference output]</code><br>
	 * &nbsp; &nbsp; &nbsp; The set error messenger function to get the error message from, 
	 * when the given value is not successfully evaluated, with the following characteristics:<br>
	 * &nbsp; &nbsp; &#8594; signature: 
	 * <code>function (\Feralygon\Kit\Options\Text $text_options): ?string</code><br>
	 * &nbsp; &nbsp; &#8594; parameters:<br>
	 * &nbsp; &nbsp; &nbsp; &nbsp; &#9656; <code>Feralygon\Kit\Options\Text $text_options</code><br>
	 * &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; The text options instance to use.<br>
	 * &nbsp; &nbsp; &#8594; return: <code>string|null</code><br>
	 * &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; The error message or <code>null</code> if no error is set.<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given value is successfully evaluated.</p>
	 * @param int $priority [default = 0]
	 * <p>The priority to execute the given evaluator function with.</p>
	 * @param bool $before_modifiers [default = false]
	 * <p>Execute the given evaluator function before the modifiers.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addValueEvaluator(
		callable $evaluator, int $priority = 0, bool $before_modifiers = false
	): Input
	{
		UCall::assert('evaluator', $evaluator, function (&$value, ?callable &$error_messenger): bool {});
		$is_new_priority = empty($this->value_evaluators_tree[$before_modifiers][$priority]);
		$this->value_evaluators_tree[$before_modifiers][$priority][] = \Closure::fromCallable($evaluator);
		if ($is_new_priority) {
			krsort($this->value_evaluators_tree[$before_modifiers], SORT_NUMERIC);
		}
		return $this;
	}
	
	/**
	 * Check if has an error.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if has an error.</p>
	 */
	final public function hasError(): bool
	{
		return isset($this->error);
	}
	
	/**
	 * Get modifier schema instances.
	 * 
	 * The returning schemas describe the modifiers from this input by using structures.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Components\Input\Components\Modifier\Structures\Schema[]
	 * <p>The modifier schema instances.</p>
	 */
	final public function getModifierSchemas(): array
	{
		$schemas = [];
		foreach ($this->getModifiers() as $modifier) {
			if ($modifier->hasSchema()) {
				$schemas[] = $modifier->getSchema();
			}
		}
		return $schemas;
	}
	
	
	
	//Final public static methods
	/**
	 * Evaluate a given value with a given prototype.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param \Feralygon\Kit\Prototypes\Input|string $prototype
	 * <p>The prototype instance, class or name to evaluate with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to evaluate with, as <samp>name => value</samp> pairs.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated with the given prototype.</p>
	 */
	final public static function evaluateValue(&$value, $prototype, array $properties = []): bool
	{
		try {
			$value = static::coerceValue($value, $prototype, $properties);
		} catch (Exceptions\ValueCoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value with a given prototype.
	 * 
	 * @since 1.0.0
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param \Feralygon\Kit\Prototypes\Input|string $prototype
	 * <p>The prototype instance, class or name to coerce with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to coerce with, as <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Components\Input\Exceptions\ValueCoercionFailed
	 * @return mixed
	 * <p>The given value coerced with the given prototype.</p>
	 */
	final public static function coerceValue($value, $prototype, array $properties = [])
	{
		$input = new static($prototype, $properties);
		if (!$input->setValue($value, true)) {
			throw new Exceptions\ValueCoercionFailed([
				'value' => $value,
				'component' => $input,
				'prototype' => $input->getPrototype(),
				'error_message' => $input->getErrorMessage()
			]);
		}
		return $input->getValue();
	}
	
	
	
	//Final private methods
	/**
	 * Evaluate a given value with value evaluators.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $before_modifiers [default = false]
	 * <p>Execute the evaluator functions set to be executed before the modifiers.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated.</p>
	 */
	final private function evaluateValueWithValueEvaluators(&$value, bool $before_modifiers = false): bool
	{
		//check
		if (empty($this->value_evaluators_tree[$before_modifiers])) {
			return true;
		}
		
		//iterate
		foreach ($this->value_evaluators_tree[$before_modifiers] as $evaluators) {
			//evaluate
			$v = $value;
			$valid = true;
			$error_messengers = [];
			foreach ($evaluators as $evaluator) {
				$error_messenger = null;
				if (!$evaluator($v, $error_messenger)) {
					$valid = false;
					if (isset($error_messenger)) {
						$error_messengers[] = $error_messenger;
					}
				}
			}
			
			//error
			if (!$valid) {
				if (empty($error_messengers)) {
					$error_messengers[] = function (TextOptions $text_options): ?string {
						return $this->getDefaultErrorMessage($text_options);
					};
				}
				$this->error = new Error($value, $error_messengers);
				return false;
			}
			
			//finish
			$value = $v;
			unset($v);
		}
		
		//return
		return true;
	}
}
