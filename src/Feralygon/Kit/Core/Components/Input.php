<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components;

use Feralygon\Kit\Core\Component;
use Feralygon\Kit\Core\Components\Input\{
	Components,
	Exceptions,
	Objects,
	Options,
	Structures
};
use Feralygon\Kit\Core\Prototypes\{
	Input as Prototype,
	Inputs as Prototypes
};
use Feralygon\Kit\Core\Prototypes\Input\Interfaces as PrototypeInterfaces;
use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property;
use Feralygon\Kit\Core\Prototype as ComponentPrototype;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Core\Utilities\{
	Call as UCall,
	Data as UData,
	Text as UText,
	Type as UType
};

/**
 * Core input component class.
 * 
 * This component represents an input which can check, sanitize and hold a value.
 * 
 * @since 1.0.0
 * @property-read bool $nullable [default = false] <p>Allow a <code>null</code> value to be set.</p>
 * @property-write \Feralygon\Kit\Core\Components\Input\Components\Modifier[]|string[] $modifiers [once] [default = []] <p>The modifiers to add, as any combination of the following:<br>
 * &nbsp; &#8226; &nbsp; instances, classes or names;<br>
 * &nbsp; &#8226; &nbsp; <samp>class => properties</samp> or <samp>name => properties</samp> pairs, with the properties being given as <samp>name => value</samp> pairs.</p>
 * @see \Feralygon\Kit\Core\Prototypes\Input
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Boolean [prototype, name = 'boolean' or 'bool']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Hashes\Crc32 [prototype, name = 'crc32']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Hashes\Md5 [prototype, name = 'md5']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Hashes\Sha1 [prototype, name = 'sha1']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Hashes\Sha256 [prototype, name = 'sha256']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Hashes\Sha512 [prototype, name = 'sha512']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Number [prototype, name = 'number']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Numbers\Float64 [prototype, name = 'float64' or 'float' or 'double']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Numbers\Integer [prototype, name = 'integer' or 'int' or 'uint' or 'int64' or 'uint63' or 'int32' or 'uint32' or 'uint31' or 'int16' or 'uint16' or 'uint15' or 'int8' or 'uint8' or 'uint7']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Enumeration [prototype, name = 'enumeration' or 'enum']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Text [prototype, name = 'text' or 'string']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Date [prototype, name = 'date']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Time [prototype, name = 'time']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\DateTime [prototype, name = 'datetime' or 'timestamp']
 */
class Input extends Component
{
	//Private properties
	/** @var bool */
	private $initialized = false;
	
	/** @var mixed */
	private $value = null;
	
	/** @var bool */
	private $nullable = false;
	
	/** @var \Feralygon\Kit\Core\Components\Input\Objects\Error|null */
	private $error = null;
	
	/** @var \Feralygon\Kit\Core\Components\Input\Components\Modifier[] */
	private $modifiers_tree = [];
	
	/** @var \Closure[] */
	private $value_evaluators_tree = [];
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getPrototypeBaseClass() : string
	{
		return Prototype::class;
	}
	
	
	
	//Implemented protected methods (core component prototype initialization trait)
	protected function initializePrototype(ComponentPrototype $prototype) : void
	{
		$prototype
			->bind('createConstraint', [$this, 'createConstraint'])
			->bind('createFilter', [$this, 'createFilter'])
		;
	}
	
	
	
	//Implemented public static methods (core component properties trait)
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return [];
	}
	
	
	
	//Implemented protected methods (core component properties trait)
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'nullable':
				return $this->createProperty()
					->setMode('r')
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateBoolean($value);
					})
					->setGetter(function () : bool {
						return $this->nullable;
					})
					->setSetter(function (bool $nullable) : void {
						$this->nullable = $nullable;
					})
				;
			case 'modifiers':
				return $this->createProperty()
					->setMode('w-')
					->setEvaluator(function (&$value) : bool {
						return UData::evaluate($value, function (&$key, &$value) : bool {
							if (is_string($key) && is_array($value)) {
								$this->addModifier($key, $value);
								return true;
							} elseif (is_int($key) && (is_string($value) || is_object($value))) {
								$this->addModifier($value);
								return true;
							}
							return false;
						});
					})
				;
		}
		return null;
	}
	
	
	
	//Implemented protected methods (core component prototypes trait)
	/** {@inheritdoc} */
	protected function buildPrototype(string $name, array $properties = []) : ?ComponentPrototype
	{
		switch ($name) {
			case 'boolean':
				//no break
			case 'bool':
				return new Prototypes\Boolean($properties);
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
			case 'enumeration':
				//no break
			case 'enum':
				return new Prototypes\Enumeration($properties);
			case 'text':
				//no break
			case 'string':
				return new Prototypes\Text($properties);
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
	 * @return string <p>The name.</p>
	 */
	public function getName() : string
	{
		return $this->getPrototype()->getName();
	}
	
	/**
	 * Get value string.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string <p>The value string.</p>
	 */
	public function getValueString($text_options = null) : string
	{
		$value = $this->getValue();
		$text_options = TextOptions::coerce($text_options);
		$prototype = $this->getPrototype();
		return isset($value) && $prototype instanceof PrototypeInterfaces\ValueStringification ? $prototype->stringifyValue($value, $text_options) : UText::stringify($value, $text_options);
	}
	
	/**
	 * Get label.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @param \Feralygon\Kit\Core\Components\Input\Options\Info|array|null $info_options [default = null] <p>The info options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string <p>The label.</p>
	 */
	public function getLabel($text_options = null, $info_options = null) : string
	{
		//label
		$text_options = TextOptions::coerce($text_options);
		$info_options = Options\Info::coerce($info_options);
		$prototype = $this->getPrototype();
		$label = $prototype instanceof PrototypeInterfaces\Information ? $prototype->getLabel($text_options, $info_options) : UText::unslugify($this->getName(), UText::UNSLUG_CAPITALIZE_FIRST);
		
		//null
		if ($this->nullable && !$info_options->exclude_null) {
			$null_label = $this->getDefaultNullLabel($text_options);
			if (isset($null_label)) {
				$label = UText::stringify([$label, UText::uncapitalize($null_label, true)], $text_options, ['non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_OR]);
			}
		}
		
		//modifiers
		if (!$info_options->exclude_modifiers) {
			$modifiers = $this->getModifiers();
			if (!empty($modifiers)) {
				$modifier_labels = [];
				foreach ($modifiers as $modifier) {
					$modifier_label = $modifier->getLabel($text_options);
					if (isset($modifier_label)) {
						$modifier_string = $modifier->getString($text_options);
						if (isset($modifier_string)) {
							/**
							 * @description Modifier label with string.
							 * @placeholder label The modifier label.
							 * @placeholder string The modifier string.
							 * @example Allowed values: "a", "b" and "c"
							 */
							$modifier_labels[] = UText::localize("{{label}}: {{string}}", self::class, $text_options, [
								'parameters' => ['label' => $modifier_label, 'string' => $modifier_string]
							]);
						} else {
							$modifier_labels[] = $modifier_label;
						}
					}
				}
				if (!empty($modifier_labels)) {
					$label .= "\n\n" . implode("\n", $modifier_labels);
				}
				unset($modifier_labels, $modifier_label);
			}
		}
		
		//return
		return $label;
	}
	
	/**
	 * Get default null label.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null <p>The default null label or <code>null</code> if none exists.</p>
	 */
	public function getDefaultNullLabel($text_options = null) : ?string
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
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @param \Feralygon\Kit\Core\Components\Input\Options\Info|array|null $info_options [default = null] <p>The info options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null <p>The description or <code>null</code> if none exists.</p>
	 */
	public function getDescription($text_options = null, $info_options = null) : ?string
	{
		//description
		$text_options = TextOptions::coerce($text_options);
		$info_options = Options\Info::coerce($info_options);
		$prototype = $this->getPrototype();
		$description = $prototype instanceof PrototypeInterfaces\Information ? $prototype->getDescription($text_options, $info_options) : null;
		
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
				$modifiers = $this->getModifiers();
				if (!empty($modifiers)) {
					$modifier_messages = [];
					foreach ($modifiers as $modifier) {
						$modifier_message = $modifier->getMessage($text_options);
						if (isset($modifier_message)) {
							$modifier_messages[] = $modifier_message;
						}
					}
					if (!empty($modifier_messages)) {
						$description .= "\n\n" . implode("\n", $modifier_messages);
					}
					unset($modifier_messages, $modifier_message);
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
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null <p>The default null description or <code>null</code> if none exists.</p>
	 */
	public function getDefaultNullDescription($text_options = null) : ?string
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
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @param \Feralygon\Kit\Core\Components\Input\Options\Info|array|null $info_options [default = null] <p>The info options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null <p>The message or <code>null</code> if none exists.</p>
	 */
	public function getMessage($text_options = null, $info_options = null) : ?string
	{
		//message
		$text_options = TextOptions::coerce($text_options);
		$info_options = Options\Info::coerce($info_options);
		$prototype = $this->getPrototype();
		$message = $prototype instanceof PrototypeInterfaces\Information ? $prototype->getMessage($text_options, $info_options) : null;
		
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
				$modifiers = $this->getModifiers();
				if (!empty($modifiers)) {
					$modifier_messages = [];
					foreach ($modifiers as $modifier) {
						$modifier_message = $modifier->getMessage($text_options);
						if (isset($modifier_message)) {
							$modifier_messages[] = $modifier_message;
						}
					}
					if (!empty($modifier_messages)) {
						$message .= "\n\n" . implode("\n", $modifier_messages);
					}
					unset($modifier_messages, $modifier_message);
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
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null <p>The default null message or <code>null</code> if none exists.</p>
	 */
	public function getDefaultNullMessage($text_options = null) : ?string
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
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null <p>The error message or <code>null</code> if no error is set.</p>
	 */
	public function getErrorMessage($text_options = null) : ?string
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
		if ($prototype instanceof PrototypeInterfaces\ErrorInformation) {
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
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string <p>The default error message.</p>
	 */
	public function getDefaultErrorMessage($text_options = null) : string
	{
		return UText::localize("The given value is invalid.", self::class, $text_options);
	}
	
	/**
	 * Unset error.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if an error was previously set.</p>
	 */
	public function unsetError() : bool
	{
		//initialize
		$set = isset($this->error);
		$this->error = null;
		
		//modifiers
		foreach ($this->modifiers_tree as $modifiers) {
			foreach ($modifiers as $modifier) {
				$modifier->unsetError();
			}
		}
		
		//prototype
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Error) {
			$prototype->unsetError();
		}
		
		//return
		return $set;
	}
	
	/**
	 * Create a constraint instance.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraint|string $prototype <p>The constraint prototype instance, class or name.</p>
	 * @param array $prototype_properties [default = []] <p>The constraint prototype properties, as <samp>name => value</samp> pairs.</p>
	 * @param array $properties [default = []] <p>The constraint properties, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Core\Components\Input\Components\Modifiers\Constraint <p>The created constraint instance.</p>
	 */
	public function createConstraint($prototype, array $prototype_properties = [], array $properties = []) : Components\Modifiers\Constraint
	{
		return new Components\Modifiers\Constraint($prototype, $prototype_properties, $properties);
	}
	
	/**
	 * Create a filter instance.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Filter|string $prototype <p>The filter prototype instance, class or name.</p>
	 * @param array $prototype_properties [default = []] <p>The filter prototype properties, as <samp>name => value</samp> pairs.</p>
	 * @param array $properties [default = []] <p>The filter properties, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Core\Components\Input\Components\Modifiers\Filter <p>The created filter instance.</p>
	 */
	public function createFilter($prototype, array $prototype_properties = [], array $properties = []) : Components\Modifiers\Filter
	{
		return new Components\Modifiers\Filter($prototype, $prototype_properties, $properties);
	}
	
	/**
	 * Get schema instance.
	 * 
	 * The returning schema describes this input by using a structure.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Core\Components\Input\Structures\Schema <p>The schema instance.</p>
	 */
	public function getSchema() : ?Structures\Schema
	{
		$prototype = $this->getPrototype();
		return new Structures\Schema([
			'name' => $this->getName(),
			'data' => $prototype instanceof PrototypeInterfaces\SchemaData ? $prototype->getSchemaData() : null,
			'modifiers' => $this->getModifierSchemas()
		]);
	}
	
	
	
	//Final public methods
	/**
	 * Check if is initialized.
	 * 
	 * An input is only considered to have been initialized after a value has been set.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if is initialized.</p>
	 */
	final public function isInitialized() : bool
	{
		return $this->initialized;
	}
	
	/**
	 * Get value.
	 * 
	 * @since 1.0.0
	 * @throws \Feralygon\Kit\Core\Components\Input\Exceptions\NotInitialized
	 * @return mixed <p>The value.</p>
	 */
	final public function getValue()
	{
		if (!$this->initialized) {
			throw new Exceptions\NotInitialized(['component' => $this, 'prototype' => $this->getPrototype(), 'error_message' => $this->getErrorMessage()]);
		}
		return $this->value;
	}
	
	/**
	 * Set value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to set.</p>
	 * @return bool <p>Boolean <code>true</code> if the value was set successfully.</p>
	 */
	final public function setValue($value) : bool
	{
		//initialize
		$this->unsetError();
		
		//evaluate
		$v = $value;
		if ((!isset($v) && !$this->nullable) || (isset($v) && !$this->getPrototype()->evaluateValue($v))) {
			$this->error = new Objects\Error($value);
			return false;
		}
		$value = $v;
		unset($v);
		
		//additional evaluations
		if (isset($value)) {
			//value evaluators (before modifiers)
			if (!$this->evaluateValueWithValueEvaluators($value, true)) {
				return false;
			}
			
			//modifiers
			foreach ($this->modifiers_tree as $modifiers) {
				//evaluate
				$v = $value;
				$error_messengers = [];
				foreach ($modifiers as $modifier) {
					if (!$modifier->evaluateValue($v)) {
						$error_messengers[] = function (TextOptions $text_options) use ($modifier) : ?string {
							return $modifier->getErrorMessage($text_options);
						};
					}
				}
				
				//error
				if (!empty($error_messengers)) {
					$this->error = new Objects\Error($value, $error_messengers);
					return false;
				}
				
				//finish
				$value = $v;
				unset($v);
			}
			
			//value evaluators (after modifiers)
			if (!$this->evaluateValueWithValueEvaluators($value, false)) {
				return false;
			}
		}
		
		//set
		$this->value = $value;
		$this->initialized = true;
		
		//return
		return true;
	}
	
	/**
	 * Unset value.
	 * 
	 * By unsetting the value, the input reverts back to an uninitialized state.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if a value was previously set.</p>
	 */
	final public function unsetValue() : bool
	{
		if (!$this->initialized) {
			return false;
		}
		$this->value = null;
		$this->initialized = false;
		return true;
	}
	
	/**
	 * Check if is nullable.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if is nullable.</p>
	 */
	final public function isNullable() : bool
	{
		return $this->nullable;
	}
	
	/**
	 * Get modifier instances.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Core\Components\Input\Components\Modifier[] <p>The modifier instances.</p>
	 */
	final public function getModifiers() : array
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
	 * Add modifier.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Components\Input\Components\Modifier|string $modifier <p>The modifier instance or name to add.</p>
	 * @param array $prototype_properties [default = []] <p>The modifier prototype properties to use, as <samp>name => value</samp> pairs.</p>
	 * @param array $properties [default = []] <p>The modifier properties to use, as <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Core\Components\Input\Exceptions\ModifierNameNotFound
	 * @throws \Feralygon\Kit\Core\Components\Input\Exceptions\InvalidModifier
	 * @throws \Feralygon\Kit\Core\Components\Input\Exceptions\ModifierPropertiesNotAllowed
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function addModifier($modifier, array $prototype_properties = [], array $properties = []) : Input
	{
		//validate and build
		$prototype = $this->getPrototype();
		if (is_string($modifier)) {
			$instance = $prototype instanceof PrototypeInterfaces\Modifiers ? $prototype->buildModifier($modifier, $prototype_properties, $properties) : null;
			if (isset($instance)) {
				$modifier = $instance;
			} else {
				throw new Exceptions\ModifierNameNotFound(['name' => $modifier, 'component' => $this, 'prototype' => $prototype]);
			}
		} elseif (!is_object($modifier) || !UType::isA($modifier, Components\Modifier::class)) {
			throw new Exceptions\InvalidModifier(['modifier' => $modifier, 'component' => $this, 'prototype' => $prototype]);
		} elseif (!empty($prototype_properties) || !empty($properties)) {
			throw new Exceptions\ModifierPropertiesNotAllowed(['component' => $this, 'prototype' => $prototype]);
		}
		
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
	 * Add constraint.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Components\Input\Components\Modifiers\Constraint|\Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraint|string $constraint <p>The constraint component instance, prototype instance, class or name to add.</p>
	 * @param array $prototype_properties [default = []] <p>The constraint prototype properties to use, as <samp>name => value</samp> pairs.</p>
	 * @param array $properties [default = []] <p>The constraint properties to use, as <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Core\Components\Input\Exceptions\ConstraintNameNotFound
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function addConstraint($constraint, array $prototype_properties = [], array $properties = []) : Input
	{
		//modifier
		$prototype = $this->getPrototype();
		if (is_string($constraint) && $prototype instanceof PrototypeInterfaces\Modifiers) {
			$modifier = $prototype->buildModifier($constraint, $prototype_properties, $properties);
			if (isset($modifier)) {
				$constraint = $modifier;
			} elseif (!class_exists($constraint)) {
				throw new Exceptions\ConstraintNameNotFound(['name' => $constraint, 'component' => $this, 'prototype' => $prototype]);
			}
		}
		
		//builder
		$builder = function ($prototype, array $prototype_properties, array $properties) : Component {
			return $this->createConstraint($prototype, $prototype_properties, $properties);
		};
		
		//add
		return $this->addModifier(Components\Modifiers\Constraint::coerce($constraint, $prototype_properties, $properties, $builder));
	}
	
	/**
	 * Add filter.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Components\Input\Components\Modifiers\Filter|\Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Filter|string $filter <p>The filter component instance, prototype instance, class or name to add.</p>
	 * @param array $prototype_properties [default = []] <p>The filter prototype properties to use, as <samp>name => value</samp> pairs.</p>
	 * @param array $properties [default = []] <p>The filter properties to use, as <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Core\Components\Input\Exceptions\FilterNameNotFound
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function addFilter($filter, array $prototype_properties = [], array $properties = []) : Input
	{
		//modifier
		$prototype = $this->getPrototype();
		if (is_string($filter) && $prototype instanceof PrototypeInterfaces\Modifiers) {
			$modifier = $prototype->buildModifier($filter, $prototype_properties, $properties);
			if (isset($modifier)) {
				$filter = $modifier;
			} elseif (!class_exists($filter)) {
				throw new Exceptions\FilterNameNotFound(['name' => $filter, 'component' => $this, 'prototype' => $prototype]);
			}
		}
		
		//builder
		$builder = function ($prototype, array $prototype_properties, array $properties) : Component {
			return $this->createFilter($prototype, $prototype_properties, $properties);
		};
		
		//add
		return $this->addModifier(Components\Modifiers\Filter::coerce($filter, $prototype_properties, $properties, $builder));
	}
	
	/**
	 * Add value evaluator function.
	 * 
	 * @since 1.0.0
	 * @param callable $evaluator <p>The function to evaluate a given value with.<br>
	 * The expected function signature is represented as:<br><br>
	 * <code>function (&$value, ?callable &$error_messenger) : bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code> : The value to evaluate (validate and sanitize).<br>
	 * &nbsp; &#8226; &nbsp; <code><b>callable|null $error_messenger</b> [reference output]</code> : The set error messenger function to retrieve the error message from, 
	 * when the given value is not valid, with the following characteristics:<br>
	 * &nbsp; &nbsp; &#8594; signature: <code>function (\Feralygon\Kit\Core\Options\Text $text_options) : ?string</code><br>
	 * &nbsp; &nbsp; &#8594; parameters:<br>
	 * &nbsp; &nbsp; &nbsp; &nbsp; &#9656; <code>\Feralygon\Kit\Core\Options\Text $text_options</code> : The text options instance to use.<br>
	 * &nbsp; &nbsp; &#8594; return: <code>string|null</code><br>
	 * &nbsp; &nbsp; &nbsp; &nbsp; The error message or <code>null</code> if no error is set.<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given value is valid.
	 * </p>
	 * @param int $priority [default = 0] <p>The priority to execute the given evaluator function with.</p>
	 * @param bool $before_modifiers [default = false] <p>Execute the given evaluator function before the modifiers.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function addValueEvaluator(callable $evaluator, int $priority = 0, bool $before_modifiers = false) : Input
	{
		UCall::assertSignature('evaluator', $evaluator, function (&$value, ?callable &$error_messenger) : bool {}, true);
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
	 * @return bool <p>Boolean <code>true</code> if has an error.</p>
	 */
	final public function hasError() : bool
	{
		return isset($this->error);
	}
	
	/**
	 * Get modifier schema instances.
	 * 
	 * The returning schemas describe the modifiers from this input by using structures.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Core\Components\Input\Components\Modifier\Structures\Schema[] <p>The modifier schema instances.</p>
	 */
	final public function getModifierSchemas() : array
	{
		$schemas = [];
		foreach ($this->getModifiers() as $modifier) {
			$schema = $modifier->getSchema();
			if (isset($schema)) {
				$schemas[] = $schema;
			}
		}
		return $schemas;
	}
	
	
	
	//Final public static methods
	/**
	 * Evaluate a given value with a given prototype.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param \Feralygon\Kit\Core\Prototypes\Input|string $prototype <p>The prototype instance, class or name to evaluate with.</p>
	 * @param array $prototype_properties [default = []] <p>The prototype properties to use, as <samp>name => value</samp> pairs.</p>
	 * @param array $properties [default = []] <p>The properties to use, as <samp>name => value</samp> pairs.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated with the given prototype.</p>
	 */
	final public static function evaluateValue(&$value, $prototype, array $prototype_properties = [], array $properties = []) : bool
	{
		try {
			$value = static::coerceValue($value, $prototype, $prototype_properties, $properties);
		} catch (Exceptions\ValueCoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value with a given prototype.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to coerce (validate and sanitize).</p>
	 * @param \Feralygon\Kit\Core\Prototypes\Input|string $prototype <p>The prototype instance, class or name to coerce with.</p>
	 * @param array $prototype_properties [default = []] <p>The prototype properties to use, as <samp>name => value</samp> pairs.</p>
	 * @param array $properties [default = []] <p>The properties to use, as <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Core\Components\Input\Exceptions\ValueCoercionFailed
	 * @return mixed <p>The given value coerced with the given prototype.</p>
	 */
	final public static function coerceValue($value, $prototype, array $prototype_properties = [], array $properties = [])
	{
		$input = new static($prototype, $prototype_properties, $properties);
		if (!$input->setValue($value)) {
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
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $before_modifiers [default = false] <p>Execute the evaluator functions set to be executed before the modifiers.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated.</p>
	 */
	final private function evaluateValueWithValueEvaluators(&$value, bool $before_modifiers = false) : bool
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
					$error_messengers[] = function (TextOptions $text_options) : ?string {
						return $this->getDefaultErrorMessage($text_options);
					};
				}
				$this->error = new Objects\Error($value, $error_messengers);
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
