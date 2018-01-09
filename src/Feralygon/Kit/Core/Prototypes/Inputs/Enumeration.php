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
	SpecificationData as ISpecificationData
};
use Feralygon\Kit\Core\Enumeration as CoreEnumeration;
use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core enumeration input prototype class.
 * 
 * This input prototype represents an enumerated element, as an integer, float or string, for which only the following types of values may be evaluated as such:<br>
 * &nbsp; &#8226; &nbsp; an integer, float or string as the enumerated element value;<br>
 * &nbsp; &#8226; &nbsp; a string as the enumerated element name.
 * 
 * @since 1.0.0
 * @property-read string $enumeration <p>The enumeration class to use.</p>
 * @property-read int[]|float[]|string[] $values [default = []] <p>The enumerated element values to restrict to.</p>
 * @property-read int[]|float[]|string[] $non_values [default = []] <p>The enumerated element values to restrict from.</p>
 * @property-read bool $names_only [default = false] <p>Only allow enumerated element names to be set.</p>
 * @property-read bool $values_only [default = false] <p>Only allow enumerated element values to be set.</p>
 * @property-read bool $hide_names [default = false] <p>Hide enumerated element names in labels, descriptions and messages.</p>
 * @property-read bool $hide_values [default = false] <p>Hide enumerated element values in labels, descriptions and messages.</p>
 * @property-read bool $namify [default = false] <p>Set as an enumerated element name.</p>
 * @see \Feralygon\Kit\Core\Enumeration
 */
class Enumeration extends Input implements IPrototypeProperties, IInformation, ISpecificationData
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
		if ((!empty($this->values) && !in_array($value, $this->values, true)) || (!empty($this->non_values) && in_array($value, $this->non_values, true))) {
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
					->setMode('r')
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateClass($value, CoreEnumeration::class);
					})
					->setGetter(function () : string {
						return $this->enumeration;
					})
					->setSetter(function (string $enumeration) : void {
						$this->enumeration = $enumeration;
					})
				;
			case 'values':
				return $this->createProperty()
					->setMode('r')
					->setEvaluator(function (&$value) : bool {
						if (is_array($value)) {
							foreach ($value as $v) {
								if (!is_int($v) && !is_float($v) && !is_string($v)) {
									return false;
								}
							}
							return true;
						}
						return false;
					})
					->setGetter(function () : array {
						return $this->values;
					})
					->setSetter(function (array $values) : void {
						$this->values = $values;
					})
				;
			case 'non_values':
				return $this->createProperty()
					->setMode('r')
					->setEvaluator(function (&$value) : bool {
						if (is_array($value)) {
							foreach ($value as $v) {
								if (!is_int($v) && !is_float($v) && !is_string($v)) {
									return false;
								}
							}
							return true;
						}
						return false;
					})
					->setGetter(function () : array {
						return $this->non_values;
					})
					->setSetter(function (array $non_values) : void {
						$this->non_values = $non_values;
					})
				;
			case 'names_only':
				return $this->createProperty()
					->setMode('r')
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateBoolean($value);
					})
					->setGetter(function () : bool {
						return $this->names_only;
					})
					->setSetter(function (bool $names_only) : void {
						$this->names_only = $names_only;
					})
				;
			case 'values_only':
				return $this->createProperty()
					->setMode('r')
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateBoolean($value);
					})
					->setGetter(function () : bool {
						return $this->values_only;
					})
					->setSetter(function (bool $values_only) : void {
						$this->values_only = $values_only;
					})
				;
			case 'hide_names':
				return $this->createProperty()
					->setMode('r')
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateBoolean($value);
					})
					->setGetter(function () : bool {
						return $this->hide_names;
					})
					->setSetter(function (bool $hide_names) : void {
						$this->hide_names = $hide_names;
					})
				;
			case 'hide_values':
				return $this->createProperty()
					->setMode('r')
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateBoolean($value);
					})
					->setGetter(function () : bool {
						return $this->hide_values;
					})
					->setSetter(function (bool $hide_values) : void {
						$this->hide_values = $hide_values;
					})
				;
			case 'namify':
				return $this->createProperty()
					->setMode('r')
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateBoolean($value);
					})
					->setGetter(function () : bool {
						return $this->namify;
					})
					->setSetter(function (bool $namify) : void {
						$this->namify = $namify;
					})
				;
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
				 * @description Core enumeration input prototype enumerated element label.
				 * @placeholder label The enumerated element label.
				 * @placeholder name_value The enumerated element name and value.
				 * @tags core prototype input enumeration label
				 * @example Not Modified: "NOT_MODIFIED" or 304
				 */
				$labels[] = UText::localize("{{label}}: {{name_value}}", 'core.prototypes.inputs.enumeration', $text_options, [
					'parameters' => [
						'label' => $enumeration::getNameLabel($name, $text_options),
						'name_value' => $show_names && $show_values && $name !== $value ? [$name, $value] : ($show_names ? [$name] : [$value])
					],
					'string_flags' => UText::STRING_NONASSOC_CONJUNCTION_OR
				]);
			} else {
				$labels[] = $enumeration::getNameLabel($name, $text_options);
			}
		}
		
		//no labels
		if (empty($labels)) {
			/**
			 * @description Core enumeration input prototype label (no element labels).
			 * @tags core prototype input enumeration label
			 */
			return UText::localize("Enumeration", 'core.prototypes.inputs.enumeration', $text_options);
		}
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			return implode("\n", $labels);
		}
		
		//non-end-user
		/**
		 * @description Core enumeration input prototype label.
		 * @placeholder labels The enumerated element labels.
		 * @tags core prototype input enumeration label non-end-user
		 * @example Enumeration {
		 *    OK: "OK" or 200
		 *    Not Modified: "NOT_MODIFIED" or 304
		 *    Bad Request: "BAD_REQUEST" or 400
		 * }
		 */
		return UText::localize("Enumeration {\n{{labels}}\n}", 'core.prototypes.inputs.enumeration', $text_options, [
			'parameters' => ['labels' => UText::indentate(implode("\n", $labels), 3, ' ')]
		]);
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
			/**
			 * @description Core enumeration input prototype description (no element descriptions).
			 * @tags core prototype input enumeration description
			 */
			return UText::localize("An enumerated element.", 'core.prototypes.inputs.enumeration', $text_options);
		}
		$merged_descriptions = UText::mbulletify($names_descriptions, $text_options, ['merge' => true]);
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @description Core enumeration input prototype description (end-user).
			 * @placeholder descriptions The enumerated element descriptions.
			 * @tags core prototype input enumeration description end-user
			 * @example One of the following:
			 *  &#8226; OK (given as "OK" or 200): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as "NOT_MODIFIED" or 304): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as "BAD_REQUEST" or 400): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"One of the following:\n{{descriptions}}",
				'core.prototypes.inputs.enumeration', $text_options, [
					'parameters' => ['descriptions' => $merged_descriptions]
				]
			);
		}
	
		//non-end-user
		if ($show_names && $show_values) {
			/**
			 * @description Core enumeration input prototype description (with names and values).
			 * @placeholder descriptions The enumerated element descriptions.
			 * @tags core prototype input enumeration description non-end-user
			 * @example An enumerated element name or value, as one of the following:
			 *  &#8226; OK (given as "OK" or 200): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as "NOT_MODIFIED" or 304): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as "BAD_REQUEST" or 400): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"An enumerated element name or value, as one of the following:\n{{descriptions}}",
				'core.prototypes.inputs.enumeration', $text_options, [
					'parameters' => ['descriptions' => $merged_descriptions]
				]
			);
		} elseif ($show_names) {
			/**
			 * @description Core enumeration input prototype description (with names only).
			 * @placeholder descriptions The enumerated element descriptions.
			 * @tags core prototype input enumeration description non-end-user
			 * @example An enumerated element name, as one of the following:
			 *  &#8226; OK (given as "OK"): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as "NOT_MODIFIED"): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as "BAD_REQUEST"): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"An enumerated element name, as one of the following:\n{{descriptions}}",
				'core.prototypes.inputs.enumeration', $text_options, [
					'parameters' => ['descriptions' => $merged_descriptions]
				]
			);
		} elseif ($show_values) {
			/**
			 * @description Core enumeration input prototype description (with values only).
			 * @placeholder descriptions The enumerated element descriptions.
			 * @tags core prototype input enumeration description non-end-user
			 * @example An enumerated element value, as one of the following:
			 *  &#8226; OK (given as 200): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as 304): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as 400): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"An enumerated element value, as one of the following:\n{{descriptions}}",
				'core.prototypes.inputs.enumeration', $text_options, [
					'parameters' => ['descriptions' => $merged_descriptions]
				]
			);
		}
		/**
		 * @description Core enumeration input prototype description.
		 * @placeholder descriptions The enumerated element descriptions.
		 * @tags core prototype input enumeration description non-end-user
		 * @example An enumerated element, as one of the following:
		 *  &#8226; OK: success "OK" HTTP status code.
		 *  &#8226; Not Modified: redirection "Not Modified" HTTP status code.
		 *  &#8226; Bad Request: client error "Bad Request" HTTP status code.
		 */
		return UText::localize(
			"An enumerated element, as one of the following:\n{{descriptions}}",
			'core.prototypes.inputs.enumeration', $text_options, [
				'parameters' => ['descriptions' => $merged_descriptions]
			]
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
			/**
			 * @description Core enumeration input prototype message (no element descriptions).
			 * @tags core prototype input enumeration message
			 */
			return UText::localize("Only enumerated elements are allowed.", 'core.prototypes.inputs.enumeration', $text_options);
		}
		$merged_descriptions = UText::mbulletify($names_descriptions, $text_options, ['merge' => true]);
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @description Core enumeration input prototype message (end-user).
			 * @placeholder descriptions The enumerated element descriptions.
			 * @tags core prototype input enumeration message end-user
			 * @example Only the following is allowed:
			 *  &#8226; OK (given as "OK" or 200): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as "NOT_MODIFIED" or 304): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as "BAD_REQUEST" or 400): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"Only the following is allowed:\n{{descriptions}}",
				'core.prototypes.inputs.enumeration', $text_options, [
					'parameters' => ['descriptions' => $merged_descriptions]
				]
			);
		}
	
		//non-end-user
		if ($show_names && $show_values) {
			/**
			 * @description Core enumeration input prototype message (with names and values).
			 * @placeholder descriptions The enumerated element descriptions.
			 * @tags core prototype input enumeration message non-end-user
			 * @example Only enumerated element names or values are allowed, as follows:
			 *  &#8226; OK (given as "OK" or 200): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as "NOT_MODIFIED" or 304): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as "BAD_REQUEST" or 400): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"Only enumerated element names or values are allowed, as follows:\n{{descriptions}}",
				'core.prototypes.inputs.enumeration', $text_options, [
					'parameters' => ['descriptions' => $merged_descriptions]
				]
			);
		} elseif ($show_names) {
			/**
			 * @description Core enumeration input prototype message (with names only).
			 * @placeholder descriptions The enumerated element descriptions.
			 * @tags core prototype input enumeration message non-end-user
			 * @example Only enumerated element names are allowed, as follows:
			 *  &#8226; OK (given as "OK"): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as "NOT_MODIFIED"): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as "BAD_REQUEST"): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"Only enumerated element names are allowed, as follows:\n{{descriptions}}",
				'core.prototypes.inputs.enumeration', $text_options, [
					'parameters' => ['descriptions' => $merged_descriptions]
				]
			);
		} elseif ($show_values) {
			/**
			 * @description Core enumeration input prototype message (with values only).
			 * @placeholder descriptions The enumerated element descriptions.
			 * @tags core prototype input enumeration message non-end-user
			 * @example Only enumerated element values are allowed, as follows:
			 *  &#8226; OK (given as 200): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as 304): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as 400): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"Only enumerated element values are allowed, as follows:\n{{descriptions}}",
				'core.prototypes.inputs.enumeration', $text_options, [
					'parameters' => ['descriptions' => $merged_descriptions]
				]
			);
		}
		/**
		 * @description Core enumeration input prototype message.
		 * @placeholder descriptions The enumerated element descriptions.
		 * @tags core prototype input enumeration message non-end-user
		 * @example Only enumerated elements are allowed, as follows:
		 *  &#8226; OK: success "OK" HTTP status code.
		 *  &#8226; Not Modified: redirection "Not Modified" HTTP status code.
		 *  &#8226; Bad Request: client error "Bad Request" HTTP status code.
		 */
		return UText::localize(
			"Only enumerated elements are allowed, as follows:\n{{descriptions}}",
			'core.prototypes.inputs.enumeration', $text_options, [
				'parameters' => ['descriptions' => $merged_descriptions]
			]
		);
	}
	
	
	
	//Implemented public methods (core input prototype specification data interface)
	/** {@inheritdoc} */
	public function getSpecificationData()
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
	 * @return bool <p>Boolean <samp>true</samp> if names can be shown.</p>
	 */
	protected function canShowNames() : bool
	{
		return !$this->values_only && !$this->hide_names;
	}
	
	/**
	 * Check if values can be shown.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <samp>true</samp> if values can be shown.</p>
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
					 * @description Core enumeration input prototype enumerated element description (with name and value).
					 * @placeholder label The enumerated element label.
					 * @placeholder name_value The enumerated element name and value.
					 * @placeholder description The enumerated element description.
					 * @tags core prototype input enumeration description
					 * @example Not Modified (given as "NOT_MODIFIED" or 304): redirection "Not Modified" HTTP status code.
					 */
					$descriptions[$name] = UText::localize(
						"{{label}} (given as {{name_value}}): {{description}}", 
						'core.prototypes.inputs.enumeration', $text_options, [
							'parameters' => [
								'label' => $enumeration::getNameLabel($name, $text_options),
								'name_value' => $show_names && $show_values && $name !== $value ? [$name, $value] : ($show_names ? [$name] : [$value]),
								'description' => $description
							],
							'string_flags' => UText::STRING_NONASSOC_CONJUNCTION_OR
						]
					);
				} else {
					/**
					 * @description Core enumeration input prototype enumerated element description.
					 * @placeholder label The enumerated element label.
					 * @placeholder description The enumerated element description.
					 * @tags core prototype input enumeration description
					 * @example Not Modified: redirection "Not Modified" HTTP status code.
					 */
					$descriptions[$name] = UText::localize("{{label}}: {{description}}", 'core.prototypes.inputs.enumeration', $text_options, [
						'parameters' => [
							'label' => $enumeration::getNameLabel($name, $text_options),
							'description' => $description
						]
					]);
				}
			} else {
				/**
				 * @description Core enumeration input prototype enumerated element description (no description).
				 * @placeholder label The enumerated element label.
				 * @placeholder name_value The enumerated element name and value.
				 * @tags core prototype input enumeration description
				 * @example Not Modified: given as "NOT_MODIFIED" or 304.
				 */
				$descriptions[$name] = UText::localize(
					"{{label}}: given as {{name_value}}.", 
					'core.prototypes.inputs.enumeration', $text_options, [
						'parameters' => [
							'label' => $enumeration::getNameLabel($name, $text_options),
							'name_value' => $show_names && $show_values && $name !== $value ? [$name, $value] : ($show_names ? [$name] : [$value])
						],
						'string_flags' => UText::STRING_NONASSOC_CONJUNCTION_OR
					]
				);
			}
		}
		
		//return
		return $descriptions;
	}
}
