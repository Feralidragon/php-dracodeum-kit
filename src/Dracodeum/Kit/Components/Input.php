<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components;

use Dracodeum\Kit\Component;
use Dracodeum\Kit\Prototypes\Input\Subcontracts\{
	ConstraintCreator as IPrototypeConstraintCreator,
	FilterCreator as IPrototypeFilterCreator
};
use Dracodeum\Kit\Components\Input\{
	Components,
	Exceptions,
	Error,
	Options,
	Structures
};
use Dracodeum\Kit\Factories\Component as Factory;
use Dracodeum\Kit\Components\Input\Factories\Component as FComponent;
use Dracodeum\Kit\Prototypes\{
	Input as Prototype,
	Inputs as Prototypes
};
use Dracodeum\Kit\Prototypes\Input\Interfaces as PrototypeInterfaces;
use Dracodeum\Kit\Traits\DebugInfo\Info as DebugInfo;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Enumerations\InfoScope as EInfoScope;
use Dracodeum\Kit\Utilities\{
	Call as UCall,
	Text as UText
};

/**
 * This component represents an input which checks, sanitizes and holds a value.
 * 
 * If a prototype is given as a name prefixed with a question mark character (<samp>?</samp>), 
 * then that character is stripped from the given name and the input is set as nullable.
 * 
 * @property-write bool $nullable [writeonce] [transient] [coercive] [default = false]
 * <p>Allow a <code>null</code> value to be set.</p>
 * @property-write \Dracodeum\Kit\Components\Input\Components\Modifiers\Constraint[]|\Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraint[]|string[] $constraints [writeonce] [transient] [coercive] [default = []]
 * <p>The constraints to add, as any combination of the following:<br>
 * &nbsp; &#8226; &nbsp; instances, classes or names;<br>
 * &nbsp; &#8226; &nbsp; <samp>class => properties</samp> or <samp>name => properties</samp> pairs, 
 * with the properties being given as a set of <samp>name => value</samp> pairs.</p>
 * @property-write \Dracodeum\Kit\Components\Input\Components\Modifiers\Filter[]|\Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Filter[]|string[] $filters [writeonce] [transient] [coercive] [default = []]
 * <p>The filters to add, as any combination of the following:<br>
 * &nbsp; &#8226; &nbsp; instances, classes or names;<br>
 * &nbsp; &#8226; &nbsp; <samp>class => properties</samp> or <samp>name => properties</samp> pairs, 
 * with the properties being given as a set of <samp>name => value</samp> pairs.</p>
 * @property-write \Dracodeum\Kit\Components\Input\Components\Modifier[]|string[] $modifiers [writeonce] [transient] [coercive] [default = []]
 * <p>The modifiers to add, as any combination of the following:<br>
 * &nbsp; &#8226; &nbsp; instances, classes or names;<br>
 * &nbsp; &#8226; &nbsp; <samp>class => properties</samp> or <samp>name => properties</samp> pairs, 
 * with the properties being given as a set of <samp>name => value</samp> pairs.</p>
 * @see \Dracodeum\Kit\Prototypes\Input
 * @see \Dracodeum\Kit\Prototypes\Inputs\Boolean
 * [prototype, name = 'boolean' or 'bool']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Number
 * [prototype, name = 'number']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Numbers\Float64
 * [prototype, name = 'float64' or 'float' or 'double']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Numbers\Integer
 * [prototype, name = 'integer' or 'int' or 'uint' or 'int64' or 'uint63' or 'int32' or 'uint32' or 'uint31' or 
 * 'int16' or 'uint16' or 'uint15' or 'int8' or 'uint8' or 'uint7']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Numbers\Size
 * [prototype, name = 'size']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Enumeration
 * [prototype, name = 'enumeration' or 'enum']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Text
 * [prototype, name = 'text' or 'string']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Hash
 * [prototype, name = 'hash' or 'crc32' or 'md5' or 'sha1' or 'sha256' or 'sha512']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Date
 * [prototype, name = 'date']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Time
 * [prototype, name = 'time']
 * @see \Dracodeum\Kit\Prototypes\Inputs\DateTime
 * [prototype, name = 'datetime' or 'timestamp']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Vector
 * [prototype, name = 'vector' or 'array' or 'list']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Dictionary
 * [prototype, name = 'dictionary' or 'dict']
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
	
	/** @var \Dracodeum\Kit\Components\Input\Error|null */
	private $error = null;
	
	/** @var \Dracodeum\Kit\Components\Input\Components\Modifier[] */
	private $modifiers_tree = [];
	
	/** @var \Closure[] */
	private $value_evaluators_tree = [];
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getBasePrototypeClass(): string
	{
		return Prototype::class;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Subcontracts\ConstraintCreator)
	/** {@inheritdoc} */
	public function createConstraint($prototype, array $properties = []): Components\Modifiers\Constraint
	{
		return FComponent::constraint($prototype, $properties);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Subcontracts\FilterCreator)
	/** {@inheritdoc} */
	public function createFilter($prototype, array $properties = []): Components\Modifiers\Filter
	{
		return FComponent::filter($prototype, $properties);
	}
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Component\Traits\DefaultBuilder)
	/** {@inheritdoc} */
	protected static function getDefaultBuilder(): ?callable
	{
		return [Factory::class, 'input'];
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Component\Traits\PreInitializer)
	/** {@inheritdoc} */
	protected function preInitialize(&$prototype, array &$properties): void
	{
		if (is_string($prototype) && isset($prototype[0]) && $prototype[0] === '?') {
			$prototype = substr($prototype, 1);
			$properties['nullable'] = true;
		}
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Component\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'nullable':
				return $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class);
			case 'constraints':
				return $this->createProperty()
					->setMode('w--')
					->setAsArray(function (&$key, &$value): bool {
						if (is_string($key) && is_array($value)) {
							$this->addConstraint($key, $value);
							return true;
						} elseif (is_int($key) && (is_string($value) || is_object($value))) {
							$this->addConstraint($value);
							return true;
						}
						return false;
					})
				;
			case 'filters':
				return $this->createProperty()
					->setMode('w--')
					->setAsArray(function (&$key, &$value): bool {
						if (is_string($key) && is_array($value)) {
							$this->addFilter($key, $value);
							return true;
						} elseif (is_int($key) && (is_string($value) || is_object($value))) {
							$this->addFilter($value);
							return true;
						}
						return false;
					})
				;
			case 'modifiers':
				return $this->createProperty()
					->setMode('w--')
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
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Component\Traits\PrototypeProducer)
	/** {@inheritdoc} */
	protected function producePrototype(string $name, array $properties)
	{
		switch ($name) {
			case 'boolean':
				//no break
			case 'bool':
				return Prototypes\Boolean::class;
			case 'number':
				return Prototypes\Number::class;
			case 'float64':
				//no break
			case 'float':
				//no break
			case 'double':
				return Prototypes\Numbers\Float64::class;
			case 'integer':
				//no break
			case 'int':
				return Prototypes\Numbers\Integer::class;
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
				return Prototypes\Numbers\Size::class;
			case 'enumeration':
				//no break
			case 'enum':
				return Prototypes\Enumeration::class;
			case 'text':
				//no break
			case 'string':
				return Prototypes\Text::class;
			case 'hash':
				return Prototypes\Hash::class;
			case 'crc32':
				return new Prototypes\Hash(['bits' => 32, 'label' => "CRC32"] + $properties);
			case 'md5':
				return new Prototypes\Hash(['bits' => 128, 'label' => "MD5"] + $properties);
			case 'sha1':
				return new Prototypes\Hash(['bits' => 160, 'label' => "SHA-1"] + $properties);
			case 'sha256':
				return new Prototypes\Hash(['bits' => 256, 'label' => "SHA-256"] + $properties);
			case 'sha512':
				return new Prototypes\Hash(['bits' => 512, 'label' => "SHA-512"] + $properties);
			case 'date':
				return Prototypes\Date::class;
			case 'time':
				return Prototypes\Time::class;
			case 'datetime':
				//no break
			case 'timestamp':
				return Prototypes\DateTime::class;
			case 'vector':
				//no break
			case 'array':
				//no break
			case 'list':
				return Prototypes\Vector::class;
			case 'dictionary':
				//no break
			case 'dict':
				return Prototypes\Dictionary::class;
		}
		return null;
	}
	
	
	
	//Public methods
	/**
	 * Get name.
	 * 
	 * The returning name is a canonical string which identifies this input.
	 * 
	 * @return string
	 * <p>The name.</p>
	 */
	public function getName(): string
	{
		return $this->getPrototype()->getName();
	}
	
	/**
	 * Check if is scalar.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is scalar.</p>
	 */
	public function isScalar(): bool
	{
		return $this->getPrototype()->isScalar();
	}
	
	/**
	 * Get value string.
	 * 
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
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
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Components\Input\Options\Info|array|null $info_options [default = null]
	 * <p>The info options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
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
			$label = UText::commify(
				[$label, UText::uncapitalize($this->getDefaultNullLabel($text_options), true)],
				$text_options, 'or'
			);
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
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The default null label.</p>
	 */
	public function getDefaultNullLabel($text_options = null): string
	{
		//initialize
		$text_options = TextOptions::coerce($text_options);
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/** @tags end-user */
			return UText::localize("None", self::class, $text_options);
		}
		
		//non-end-user
		/** @tags non-end-user */
		return UText::localize("Null", self::class, $text_options);
	}
	
	/**
	 * Get description.
	 * 
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Components\Input\Options\Info|array|null $info_options [default = null]
	 * <p>The info options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
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
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The default null description or <code>null</code> if none is set.</p>
	 */
	public function getDefaultNullDescription($text_options = null): ?string
	{
		//initialize
		$text_options = TextOptions::coerce($text_options);
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/** @tags end-user */
			return UText::localize("Alternatively, it may also be none.", self::class, $text_options);
		}
		
		//non-end-user
		/** @tags non-end-user */
		return UText::localize("Alternatively, it may also be null.", self::class, $text_options);
	}
	
	/**
	 * Get message.
	 * 
	 * The returning message is assertive relative to the expected value.
	 * 
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Components\Input\Options\Info|array|null $info_options [default = null]
	 * <p>The info options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
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
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The default null message or <code>null</code> if none is set.</p>
	 */
	public function getDefaultNullMessage($text_options = null): ?string
	{
		//initialize
		$text_options = TextOptions::coerce($text_options);
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/** @tags end-user */
			return UText::localize("Alternatively, no value may also be given.", self::class, $text_options);
		}
		
		//non-end-user
		/** @tags non-end-user */
		return UText::localize("Alternatively, a null value may also be given.", self::class, $text_options);
	}
	
	/**
	 * Get error message.
	 * 
	 * The returning error message is only set if a previous <code>setValue</code> method call has failed.
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
		
		//error message
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\ErrorMessage) {
			$message = $prototype->getErrorMessage($text_options);
			if (isset($message)) {
				return $message;
			}
		}
		
		//message
		if ($prototype instanceof PrototypeInterfaces\Information) {
			return $prototype->getMessage($text_options, Options\Info::build());
		}
		
		//default
		return $this->getDefaultErrorMessage($text_options);
	}
	
	/**
	 * Get default error message.
	 * 
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
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
	 * @return \Dracodeum\Kit\Components\Input\Structures\Schema
	 * <p>The schema instance.</p>
	 */
	public function getSchema(): Structures\Schema
	{
		$prototype = $this->getPrototype();
		return Structures\Schema::build([
			'name' => $this->getName(),
			'nullable' => $this->nullable,
			'data' => $prototype instanceof PrototypeInterfaces\SchemaData ? $prototype->getSchemaData() : null,
			'modifiers' => $this->getModifierSchemas()
		])->setAsReadonly(true);
	}
	
	
	
	//Final public methods
	/**
	 * Check if is initialized.
	 * 
	 * An input is only considered to have been initialized after a value has been set.
	 * 
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
	 * @return mixed
	 * <p>The value.</p>
	 */
	final public function getValue()
	{
		UCall::guard($this->initialized, function () {
			//error message
			$error_message = $this->getErrorMessage();
			if (isset($error_message)) {
				$error_message = "No value set due to the following error: " . UText::formatMessage($error_message);
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
	 * @param mixed $value
	 * <p>The value to set.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Components\Input\Exceptions\InvalidValue
	 * @return $this|bool
	 * <p>This instance, for chaining purposes.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
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
			throw new Exceptions\InvalidValue([$this, $prototype, $value, 'error_message' => $this->getErrorMessage()]);
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
					$this, $prototype, $value, 'error_message' => $this->getErrorMessage()
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
						$this, $prototype, $value, 'error_message' => $this->getErrorMessage()
					]);
				}
				
				//finalize
				$value = $v;
				unset($v);
			}
			
			//value evaluators (after modifiers)
			if (!$this->evaluateValueWithValueEvaluators($value, false)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\InvalidValue([
					$this, $prototype, $value, 'error_message' => $this->getErrorMessage()
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
	 * @return bool
	 * <p>Boolean <code>true</code> if is nullable.</p>
	 */
	final public function isNullable(): bool
	{
		return $this->nullable;
	}
	
	/**
	 * Check if has modifiers.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if has modifiers.</p>
	 */
	final public function hasModifiers(): bool
	{
		return !empty($this->modifiers_tree);
	}
	
	/**
	 * Get modifier instances.
	 * 
	 * @return \Dracodeum\Kit\Components\Input\Components\Modifier[]
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
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
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
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
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
	 * @param \Dracodeum\Kit\Components\Input\Components\Modifier|string $modifier
	 * <p>The modifier component instance or name to add.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to add with, as a set of <samp>name => value</samp> pairs, if a component name is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
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
	 * Add constraint.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @param \Dracodeum\Kit\Components\Input\Components\Modifiers\Constraint|\Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraint|string $constraint
	 * <p>The constraint component instance or name, or prototype instance, class or name, to add.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to add with, as a set of <samp>name => value</samp> pairs, 
	 * if a component name, or a prototype class or name, is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addConstraint($constraint, array $properties = []): Input
	{
		//guard
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization."
		]);
		
		//add
		$prototype = $this->getPrototype();
		$this->addModifier(
			Components\Modifiers\Constraint::produce(
				$constraint, $properties,
				$prototype instanceof PrototypeInterfaces\ConstraintProducer ? [$prototype, 'produceConstraint'] : null,
				[$this, 'createConstraint']
			)
		);
		
		//return
		return $this;
	}
	
	/**
	 * Add filter.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @param \Dracodeum\Kit\Components\Input\Components\Modifiers\Filter|\Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Filter|string $filter
	 * <p>The filter component instance or name, or prototype instance, class or name, to add.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to add with, as a set of <samp>name => value</samp> pairs, 
	 * if a component name, or a prototype class or name, is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addFilter($filter, array $properties = []): Input
	{
		//guard
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization."
		]);
		
		//add
		$prototype = $this->getPrototype();
		$this->addModifier(
			Components\Modifiers\Filter::produce(
				$filter, $properties,
				$prototype instanceof PrototypeInterfaces\FilterProducer ? [$prototype, 'produceFilter'] : null,
				[$this, 'createFilter']
			)
		);
		
		//return
		return $this;
	}
	
	/**
	 * Add value evaluator function.
	 * 
	 * @param callable $evaluator
	 * <p>The function to evaluate a given value with.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (&$value, ?callable &$error_messenger): bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The value to evaluate (validate and sanitize).<br>
	 * &nbsp; &#8226; &nbsp; <code><b>callable|null $error_messenger</b> [reference output]</code><br>
	 * &nbsp; &nbsp; &nbsp; The set error messenger function to get the error message from, 
	 * when the given value is not successfully evaluated, with the following characteristics:<br>
	 * &nbsp; &nbsp; &#8594; signature: 
	 * <code>function (\Dracodeum\Kit\Options\Text $text_options): ?string</code><br>
	 * &nbsp; &nbsp; &#8594; parameters:<br>
	 * &nbsp; &nbsp; &nbsp; &nbsp; &#9656; <code>Dracodeum\Kit\Options\Text $text_options</code><br>
	 * &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; The text options instance to use.<br>
	 * &nbsp; &nbsp; &#8594; return: <code>string|null</code><br>
	 * &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; The error message or <code>null</code> if no error is set.<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given value was successfully evaluated.</p>
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
	 * @return \Dracodeum\Kit\Components\Input\Components\Modifier\Structures\Schema[]
	 * <p>The modifier schema instances.</p>
	 */
	final public function getModifierSchemas(): array
	{
		$schemas = [];
		foreach ($this->getModifiers() as $modifier) {
			$schemas[] = $modifier->getSchema();
		}
		return $schemas;
	}
	
	
	
	//Final public static methods
	/**
	 * Evaluate a given value with a given prototype.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param \Dracodeum\Kit\Prototypes\Input|string $prototype
	 * <p>The prototype instance, class or name to evaluate with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to evaluate with, as a set of <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated with the given prototype.</p>
	 */
	final public static function evaluateValue(&$value, $prototype, array $properties = []): bool
	{
		return self::processValueCoercion($value, $prototype, $properties, true);
	}
	
	/**
	 * Coerce a given value with a given prototype.
	 * 
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param \Dracodeum\Kit\Prototypes\Input|string $prototype
	 * <p>The prototype instance, class or name to coerce with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to coerce with, as a set of <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @throws \Dracodeum\Kit\Components\Input\Exceptions\ValueCoercionFailed
	 * @return mixed
	 * <p>The given value coerced with the given prototype.</p>
	 */
	final public static function coerceValue($value, $prototype, array $properties = [])
	{
		self::processValueCoercion($value, $prototype, $properties);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value with a given prototype.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param \Dracodeum\Kit\Prototypes\Input|string $prototype
	 * <p>The prototype instance, class or name to coerce with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to coerce with, as a set of <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Components\Input\Exceptions\ValueCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced with the given prototype.</p>
	 */
	final public static function processValueCoercion(
		&$value, $prototype, array $properties = [], bool $no_throw = false
	): bool
	{
		$component = static::build($prototype, $properties);
		if (!$component->setValue($value, true)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\ValueCoercionFailed([
				$component, $component->getPrototype(), $value, $component->getErrorMessage()
			]);
		}
		$value = $component->getValue();
		return true;
	}
	
	
	
	//Overridden public methods
	/** {@inheritdoc} */
	public function processDebugInfo(DebugInfo $info): void
	{
		//parent
		parent::processDebugInfo($info);
		
		//modifiers
		$modifiers = $this->getModifiers();
		if (!empty($modifiers)) {
			$info->set('@modifiers', $modifiers);
		}
		
		//hidden properties
		$info
			->hideObjectProperty('modifiers_tree', self::class)
			->hideObjectProperty('value_evaluators_tree', self::class)
		;
		if (!isset($this->error)) {
			$info->hideObjectProperty('error', self::class);
		}
	}
	
	
	
	//Private methods
	/**
	 * Evaluate a given value with value evaluators.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $before_modifiers [default = false]
	 * <p>Execute the evaluator functions set to be executed before the modifiers.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated.</p>
	 */
	private function evaluateValueWithValueEvaluators(&$value, bool $before_modifiers = false): bool
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
			
			//finalize
			$value = $v;
			unset($v);
		}
		
		//return
		return true;
	}
}
