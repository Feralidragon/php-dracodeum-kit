<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs;

use Feralygon\Kit\Core\Prototypes\Input;
use Feralygon\Kit\Core\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Core\Prototypes\Input\Interfaces\{
	Information as IInformation,
	ValueStringification as IValueStringification,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Core\Enumeration as CoreEnumeration;
use Feralygon\Kit\Core\Traits\LazyProperties\Objects\Property;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Core\Utilities\Text as UText;

/**
 * Core enumeration input prototype class.
 * 
 * This input prototype represents an enumerated element, as an integer, float or string, 
 * for which only the following types of values may be evaluated as such:<br>
 * &nbsp; &#8226; &nbsp; an integer, float or string as the enumerated element value;<br>
 * &nbsp; &#8226; &nbsp; a string as the enumerated element name.
 * 
 * @since 1.0.0
 * @property-read string $enumeration <p>The enumeration class to use.</p>
 * @property-read int[]|float[]|string[] $values [default = []] <p>The enumerated element values to restrict to.</p>
 * @property-read int[]|float[]|string[] $non_values [default = []] 
 * <p>The enumerated element values to restrict from.</p>
 * @property-read bool $names_only [default = false] <p>Only allow enumerated element names to be set.</p>
 * @property-read bool $values_only [default = false] <p>Only allow enumerated element values to be set.</p>
 * @property-read bool $hide_names [default = false] <p>Hide enumerated element names in labels, 
 * descriptions and messages.</p>
 * @property-read bool $hide_values [default = false] <p>Hide enumerated element values in labels, 
 * descriptions and messages.</p>
 * @property-read bool $namify [default = false] <p>Set as an enumerated element name.</p>
 * @see \Feralygon\Kit\Core\Enumeration
 */
class Enumeration extends Input implements IPrototypeProperties, IInformation, IValueStringification, ISchemaData
{
	//Private properties
	/** @var string */
	private $enumeration;
	
	/** @var int[]|float[]|string[] */
	private $values = [];
	
	/** @var int[]|float[]|string[] */
	private $non_values = [];
	
	/** @var bool */
	private $names_only = false;
	
	/** @var bool */
	private $values_only = false;
	
	/** @var bool */
	private $hide_names = false;
	
	/** @var bool */
	private $hide_values = false;
	
	/** @var bool */
	private $namify = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'enumeration';
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value) : bool
	{
		//check
		if (!is_int($value) && !is_float($value) && !is_string($value)) {
			return false;
		}
		
		//enumeration
		$enumeration = $this->enumeration;
		if (!$this->values_only && is_string($value) && $enumeration::hasName($value)) {
			$value = $enumeration::getNameValue($value);
		} elseif (!$this->names_only && $enumeration::hasValue($value)) {
			$value = $enumeration::getValue($value);
		} else {
			return false;
		}
		
		//values
		if (!empty($this->values) && !in_array($value, $this->values, true)) {
			return false;
		} elseif (!empty($this->non_values) && in_array($value, $this->non_values, true)) {
			return false;
		}
		
		//namify
		if ($this->namify) {
			$value = $enumeration::getValueName($value);
		}
		
		//return
		return true;
	}
	
	
	
	//Implemented public methods (core prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'enumeration':
				return $this->createProperty()
					->setMode('r+')
					->setAsStrictClass(CoreEnumeration::class)
					->bind(self::class)
				;
			case 'values':
				//no break
			case 'non_values':
				return $this->createProperty()
					->setMode('r+')
					->setAsArray(function (&$key, &$value) : bool {
						return is_int($value) || is_float($value) || is_string($value);
					}, true)
					->bind(self::class)
				;
			case 'names_only':
				//no break
			case 'values_only':
				//no break
			case 'hide_names':
				//no break
			case 'hide_values':
				//no break
			case 'namify':
				return $this->createProperty()->setMode('r+')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
	
	
	
	//Implemented public static methods (core prototype properties interface)
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['enumeration'];
	}
	
	
	
	//Implemented public methods (core input prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//initialize
		$labels = [];
		$enumeration = $this->enumeration;
		$show_names = $this->canShowNames();
		$show_values = $this->canShowValues();
		
		//labels
		foreach ($this->getNamesValues() as $name => $value) {
			if ($show_names || $show_values) {
				/**
				 * @description Enumerated element label.
				 * @placeholder label The enumerated element label.
				 * @placeholder name_value The enumerated element name and value.
				 * @example Not Modified: "NOT_MODIFIED" or 304
				 */
				$labels[] = UText::localize(
					"{{label}}: {{name_value}}",
					self::class, $text_options, [
						'parameters' => [
							'label' => $enumeration::getNameLabel($name, $text_options),
							'name_value' => UText::stringify(
								$show_names && $show_values && $name !== $value
									? [$name, $value]
									: ($show_names ? $name : $value),
								$text_options, [
									'quote_strings' => true,
									'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_OR
								]
							)
						]
					]
				);
			} else {
				$labels[] = $enumeration::getNameLabel($name, $text_options);
			}
		}
		
		//no labels
		if (empty($labels)) {
			return UText::localize("Enumeration", self::class, $text_options);
		}
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			return implode("\n", $labels);
		}
		
		//non-end-user
		/**
		 * @placeholder labels The enumerated element labels.
		 * @tags non-end-user
		 * @example Enumeration {
		 *    OK: "OK" or 200
		 *    Not Modified: "NOT_MODIFIED" or 304
		 *    Bad Request: "BAD_REQUEST" or 400
		 * }
		 */
		return UText::localize(
			"Enumeration {\n{{labels}}\n}",
			self::class, $text_options, [
				'parameters' => ['labels' => UText::indentate(implode("\n", $labels), 3, ' ')]
			]
		);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//initialize
		$show_names = $this->canShowNames();
		$show_values = $this->canShowValues();
		
		//descriptions
		$names_descriptions = $this->getNamesDescriptions($text_options);
		if (empty($names_descriptions)) {
			return UText::localize("An enumerated element.", self::class, $text_options);
		}
		$merged_descriptions = UText::mbulletify($names_descriptions, $text_options, ['merge' => true]);
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder descriptions The enumerated element descriptions.
			 * @tags end-user
			 * @example One of the following:
			 *  &#8226; OK (given as "OK" or 200): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as "NOT_MODIFIED" or 304): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as "BAD_REQUEST" or 400): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"One of the following:\n{{descriptions}}",
				self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
			);
		}
	
		//non-end-user
		if ($show_names && $show_values) {
			/**
			 * @placeholder descriptions The enumerated element descriptions.
			 * @tags non-end-user
			 * @example An enumerated element name or value, as one of the following:
			 *  &#8226; OK (given as "OK" or 200): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as "NOT_MODIFIED" or 304): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as "BAD_REQUEST" or 400): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"An enumerated element name or value, as one of the following:\n{{descriptions}}",
				self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
			);
		} elseif ($show_names) {
			/**
			 * @placeholder descriptions The enumerated element descriptions.
			 * @tags non-end-user
			 * @example An enumerated element name, as one of the following:
			 *  &#8226; OK (given as "OK"): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as "NOT_MODIFIED"): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as "BAD_REQUEST"): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"An enumerated element name, as one of the following:\n{{descriptions}}",
				self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
			);
		} elseif ($show_values) {
			/**
			 * @placeholder descriptions The enumerated element descriptions.
			 * @tags non-end-user
			 * @example An enumerated element value, as one of the following:
			 *  &#8226; OK (given as 200): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as 304): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as 400): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"An enumerated element value, as one of the following:\n{{descriptions}}",
				self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
			);
		}
		/**
		 * @placeholder descriptions The enumerated element descriptions.
		 * @tags non-end-user
		 * @example An enumerated element, as one of the following:
		 *  &#8226; OK: success "OK" HTTP status code.
		 *  &#8226; Not Modified: redirection "Not Modified" HTTP status code.
		 *  &#8226; Bad Request: client error "Bad Request" HTTP status code.
		 */
		return UText::localize(
			"An enumerated element, as one of the following:\n{{descriptions}}",
			self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//initialize
		$show_names = $this->canShowNames();
		$show_values = $this->canShowValues();
		
		//descriptions
		$names_descriptions = $this->getNamesDescriptions($text_options);
		if (empty($names_descriptions)) {
			return UText::localize("Only an enumerated element is allowed.", self::class, $text_options);
		}
		$merged_descriptions = UText::mbulletify($names_descriptions, $text_options, ['merge' => true]);
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder descriptions The enumerated element descriptions.
			 * @tags end-user
			 * @example Only the following is allowed:
			 *  &#8226; OK (given as "OK" or 200): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as "NOT_MODIFIED" or 304): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as "BAD_REQUEST" or 400): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"Only the following is allowed:\n{{descriptions}}",
				self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
			);
		}
	
		//non-end-user
		if ($show_names && $show_values) {
			/**
			 * @placeholder descriptions The enumerated element descriptions.
			 * @tags non-end-user
			 * @example Only an enumerated element name or value is allowed, as follows:
			 *  &#8226; OK (given as "OK" or 200): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as "NOT_MODIFIED" or 304): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as "BAD_REQUEST" or 400): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"Only an enumerated element name or value is allowed, as follows:\n{{descriptions}}",
				self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
			);
		} elseif ($show_names) {
			/**
			 * @placeholder descriptions The enumerated element descriptions.
			 * @tags non-end-user
			 * @example Only an enumerated element name is allowed, as follows:
			 *  &#8226; OK (given as "OK"): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as "NOT_MODIFIED"): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as "BAD_REQUEST"): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"Only an enumerated element name is allowed, as follows:\n{{descriptions}}",
				self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
			);
		} elseif ($show_values) {
			/**
			 * @placeholder descriptions The enumerated element descriptions.
			 * @tags non-end-user
			 * @example Only an enumerated element value is allowed, as follows:
			 *  &#8226; OK (given as 200): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as 304): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as 400): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"Only an enumerated element value is allowed, as follows:\n{{descriptions}}",
				self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
			);
		}
		/**
		 * @placeholder descriptions The enumerated element descriptions.
		 * @tags non-end-user
		 * @example Only an enumerated element is allowed, as follows:
		 *  &#8226; OK: success "OK" HTTP status code.
		 *  &#8226; Not Modified: redirection "Not Modified" HTTP status code.
		 *  &#8226; Bad Request: client error "Bad Request" HTTP status code.
		 */
		return UText::localize(
			"Only an enumerated element is allowed, as follows:\n{{descriptions}}",
			self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
		);
	}
	
	
	
	//Implemented public methods (core input prototype value stringification interface)
	/** {@inheritdoc} */
	public function stringifyValue($value, TextOptions $text_options) : string
	{
		$enumeration = $this->enumeration;
		return UText::stringify($enumeration::getName($value), $text_options, ['quote_strings' => true]);
	}
	
	
	
	//Implemented public methods (core input prototype schema data interface)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		$show_names = $this->canShowNames();
		$show_values = $this->canShowValues();
		if ($show_names && $show_values) {
			$elements = [];
			foreach ($this->getNamesValues() as $name => $value) {
				$elements[] = ['name' => $name, 'value' => $value];
			}
			return ['elements' => $elements];
		} elseif ($show_names) {
			return ['names' => array_keys($this->getNamesValues())];
		} elseif ($show_values) {
			return ['values' => array_values($this->getNamesValues())];
		}
		return null;
	}
	
	
	
	//Protected methods
	/**
	 * Check if names can be shown.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if names can be shown.</p>
	 */
	protected function canShowNames() : bool
	{
		return !$this->values_only && !$this->hide_names;
	}
	
	/**
	 * Check if values can be shown.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if values can be shown.</p>
	 */
	protected function canShowValues() : bool
	{
		return !$this->names_only && !$this->hide_values;
	}
	
	/**
	 * Get enumerated element names values.
	 * 
	 * @since 1.0.0
	 * @return int[]|float[]|string[] <p>The enumerated element names values, as <samp>name => value</samp> pairs.</p>
	 */
	protected function getNamesValues() : array
	{
		//initialize
		$enumeration = $this->enumeration;
		$names_values = $enumeration::getNamesValues();
		
		//values
		if (!empty($this->values)) {
			$names_map = [];
			foreach ($this->values as $value) {
				$names_map[$enumeration::getValueName($value)] = true;
			}
			$names_values = array_intersect_key($names_values, $names_map);
			unset($names_map);
		}
		
		//non-values
		if (!empty($this->non_values)) {
			$non_names_map = [];
			foreach ($this->non_values as $non_value) {
				$non_names_map[$enumeration::getValueName($non_value)] = true;
			}
			$names_values = array_diff_key($names_values, $non_names_map);
			unset($non_names_map);
		}
		
		//return
		return $names_values;
	}
	
	/**
	 * Get enumerated element names descriptions.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Options\Text $text_options <p>The text options instance to use.</p>
	 * @return string[] <p>The enumerated element names descriptions, as <samp>name => description</samp> pairs.</p>
	 */
	protected function getNamesDescriptions(TextOptions $text_options) : array
	{
		//initialize
		$descriptions = [];
		$enumeration = $this->enumeration;
		$show_names = $this->canShowNames();
		$show_values = $this->canShowValues();
		
		//descriptions
		foreach ($this->getNamesValues() as $name => $value) {
			$description = $enumeration::getNameDescription($name, $text_options);
			if (isset($description)) {
				$description = UText::uncapitalize($description, true);
				if ($show_names || $show_values) {
					/**
					 * @description Enumerated element description (with name and value).
					 * @placeholder label The enumerated element label.
					 * @placeholder name_value The enumerated element name and value.
					 * @placeholder description The enumerated element description.
					 * @example Not Modified (given as "NOT_MODIFIED" or 304): \
					 * redirection "Not Modified" HTTP status code.
					 */
					$descriptions[$name] = UText::localize(
						"{{label}} (given as {{name_value}}): {{description}}", 
						self::class, $text_options, [
							'parameters' => [
								'label' => $enumeration::getNameLabel($name, $text_options),
								'name_value' => UText::stringify(
									$show_names && $show_values && $name !== $value
										? [$name, $value]
										: ($show_names ? $name : $value),
									$text_options, [
										'quote_strings' => true,
										'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_OR
									]
								),
								'description' => $description
							]
						]
					);
				} else {
					/**
					 * @description Enumerated element description.
					 * @placeholder label The enumerated element label.
					 * @placeholder description The enumerated element description.
					 * @example Not Modified: redirection "Not Modified" HTTP status code.
					 */
					$descriptions[$name] = UText::localize(
						"{{label}}: {{description}}", 
						self::class, $text_options, [
							'parameters' => [
								'label' => $enumeration::getNameLabel($name, $text_options),
								'description' => $description
							]
						]
					);
				}
			} else {
				/**
				 * @description Enumerated element description (no description).
				 * @placeholder label The enumerated element label.
				 * @placeholder name_value The enumerated element name and value.
				 * @example Not Modified: given as "NOT_MODIFIED" or 304.
				 */
				$descriptions[$name] = UText::localize(
					"{{label}}: given as {{name_value}}.", 
					self::class, $text_options, [
						'parameters' => [
							'label' => $enumeration::getNameLabel($name, $text_options),
							'name_value' => UText::stringify(
								$show_names && $show_values && $name !== $value
									? [$name, $value]
									: ($show_names ? $name : $value),
								$text_options, [
									'quote_strings' => true,
									'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_OR
								]
							)
						]
					]
				);
			}
		}
		
		//return
		return $descriptions;
	}
}
