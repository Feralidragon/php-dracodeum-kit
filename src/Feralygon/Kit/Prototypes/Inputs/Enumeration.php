<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs;

use Feralygon\Kit\Prototypes\Input;
use Feralygon\Kit\Prototypes\Input\Interfaces\{
	Information as IInformation,
	ValueStringifier as IValueStringifier,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Enumeration as KitEnumeration;
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * This input prototype represents an enumeration element, as an integer, float or string.
 * 
 * Only the following types of values may be evaluated as an enumeration element:<br>
 * &nbsp; &#8226; &nbsp; an integer, float or string as the enumeration element value;<br>
 * &nbsp; &#8226; &nbsp; a string as the enumeration element name.
 * 
 * @property-write string $enumeration [writeonce] [strict = class]
 * <p>The enumeration class to use.</p>
 * @property-write int[]|float[]|string[] $values [writeonce] [coercive] [default = []]
 * <p>The enumeration element values to restrict a given value to.</p>
 * @property-write int[]|float[]|string[] $non_values [writeonce] [coercive] [default = []]
 * <p>The enumeration element values to restrict a given value from.</p>
 * @property-write bool $names_only [writeonce] [coercive] [default = false]
 * <p>Only allow enumeration element names to be set.</p>
 * @property-write bool $values_only [writeonce] [coercive] [default = false]
 * <p>Only allow enumeration element values to be set.</p>
 * @property-write bool $hide_names [writeonce] [coercive] [default = false]
 * <p>Hide enumeration element names in labels, descriptions and messages.</p>
 * @property-write bool $hide_values [writeonce] [coercive] [default = false]
 * <p>Hide enumeration element values in labels, descriptions and messages.</p>
 * @property-write bool $namify [writeonce] [coercive] [default = false]
 * <p>Set as an enumeration element name.</p>
 * @see \Feralygon\Kit\Enumeration
 */
class Enumeration extends Input implements IInformation, IValueStringifier, ISchemaData
{
	//Protected properties
	/** @var string */
	protected $enumeration;
	
	/** @var int[]|float[]|string[] */
	protected $values = [];
	
	/** @var int[]|float[]|string[] */
	protected $non_values = [];
	
	/** @var bool */
	protected $names_only = false;
	
	/** @var bool */
	protected $values_only = false;
	
	/** @var bool */
	protected $hide_names = false;
	
	/** @var bool */
	protected $hide_values = false;
	
	/** @var bool */
	protected $namify = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'enumeration';
	}
	
	/** {@inheritdoc} */
	public function isScalar(): bool
	{
		return true;
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value): bool
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
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options): string
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
				 * @description Enumeration element label.
				 * @placeholder label The enumeration element label.
				 * @placeholder name_value The enumeration element name and/or value.
				 * @example Not Modified: "NOT_MODIFIED" or 304
				 */
				$labels[] = UText::localize(
					"{{label}}: {{name_value}}",
					self::class, $text_options, [
						'parameters' => [
							'label' => $enumeration::getNameLabel($name, $text_options),
							'name_value' => UText::commify(
								$show_names && $show_values && $name !== $value
									? [$name, $value]
									: ($show_names ? [$name] : [$value])
								, $text_options, 'or', true
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
		 * @placeholder labels The enumeration element labels.
		 * @tags non-end-user
		 * @example Enumeration {
		 *    OK: "OK" or 200
		 *    Not Modified: "NOT_MODIFIED" or 304
		 *    Bad Request: "BAD_REQUEST" or 400
		 * }
		 */
		return UText::localize(
			"Enumeration {\n{{labels}}\n}",
			self::class, $text_options, ['parameters' => ['labels' => UText::indentate(implode("\n", $labels))]]
		);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options): string
	{
		//initialize
		$show_names = $this->canShowNames();
		$show_values = $this->canShowValues();
		
		//descriptions
		$names_descriptions = $this->getNamesDescriptions($text_options);
		if (empty($names_descriptions)) {
			return UText::localize("An enumeration element.", self::class, $text_options);
		}
		$merged_descriptions = UText::mbulletify($names_descriptions, $text_options, ['merge' => true]);
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder descriptions The enumeration element descriptions.
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
	
		//show names and values
		if ($show_names && $show_values) {
			/**
			 * @placeholder descriptions The enumeration element descriptions.
			 * @tags non-end-user
			 * @example An enumeration element name or value, as one of the following:
			 *  &#8226; OK (given as "OK" or 200): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as "NOT_MODIFIED" or 304): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as "BAD_REQUEST" or 400): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"An enumeration element name or value, as one of the following:\n{{descriptions}}",
				self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
			);
		}
		
		//show names
		if ($show_names) {
			/**
			 * @placeholder descriptions The enumeration element descriptions.
			 * @tags non-end-user
			 * @example An enumeration element name, as one of the following:
			 *  &#8226; OK (given as "OK"): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as "NOT_MODIFIED"): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as "BAD_REQUEST"): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"An enumeration element name, as one of the following:\n{{descriptions}}",
				self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
			);
		}
		
		//show values
		if ($show_values) {
			/**
			 * @placeholder descriptions The enumeration element descriptions.
			 * @tags non-end-user
			 * @example An enumeration element value, as one of the following:
			 *  &#8226; OK (given as 200): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as 304): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as 400): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"An enumeration element value, as one of the following:\n{{descriptions}}",
				self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
			);
		}
		
		//default
		/**
		 * @placeholder descriptions The enumeration element descriptions.
		 * @tags non-end-user
		 * @example An enumeration element, as one of the following:
		 *  &#8226; OK: success "OK" HTTP status code.
		 *  &#8226; Not Modified: redirection "Not Modified" HTTP status code.
		 *  &#8226; Bad Request: client error "Bad Request" HTTP status code.
		 */
		return UText::localize(
			"An enumeration element, as one of the following:\n{{descriptions}}",
			self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options): string
	{
		//initialize
		$show_names = $this->canShowNames();
		$show_values = $this->canShowValues();
		
		//descriptions
		$names_descriptions = $this->getNamesDescriptions($text_options);
		if (empty($names_descriptions)) {
			return UText::localize("Only an enumeration element is allowed.", self::class, $text_options);
		}
		$merged_descriptions = UText::mbulletify($names_descriptions, $text_options, ['merge' => true]);
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder descriptions The enumeration element descriptions.
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
	
		//show names and values
		if ($show_names && $show_values) {
			/**
			 * @placeholder descriptions The enumeration element descriptions.
			 * @tags non-end-user
			 * @example Only an enumeration element name or value is allowed, as follows:
			 *  &#8226; OK (given as "OK" or 200): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as "NOT_MODIFIED" or 304): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as "BAD_REQUEST" or 400): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"Only an enumeration element name or value is allowed, as follows:\n{{descriptions}}",
				self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
			);
		}
		
		//show names
		if ($show_names) {
			/**
			 * @placeholder descriptions The enumeration element descriptions.
			 * @tags non-end-user
			 * @example Only an enumeration element name is allowed, as follows:
			 *  &#8226; OK (given as "OK"): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as "NOT_MODIFIED"): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as "BAD_REQUEST"): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"Only an enumeration element name is allowed, as follows:\n{{descriptions}}",
				self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
			);
		}
		
		//show values
		if ($show_values) {
			/**
			 * @placeholder descriptions The enumeration element descriptions.
			 * @tags non-end-user
			 * @example Only an enumeration element value is allowed, as follows:
			 *  &#8226; OK (given as 200): success "OK" HTTP status code.
			 *  &#8226; Not Modified (given as 304): redirection "Not Modified" HTTP status code.
			 *  &#8226; Bad Request (given as 400): client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"Only an enumeration element value is allowed, as follows:\n{{descriptions}}",
				self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
			);
		}
		
		//default
		/**
		 * @placeholder descriptions The enumeration element descriptions.
		 * @tags non-end-user
		 * @example Only an enumeration element is allowed, as follows:
		 *  &#8226; OK: success "OK" HTTP status code.
		 *  &#8226; Not Modified: redirection "Not Modified" HTTP status code.
		 *  &#8226; Bad Request: client error "Bad Request" HTTP status code.
		 */
		return UText::localize(
			"Only an enumeration element is allowed, as follows:\n{{descriptions}}",
			self::class, $text_options, ['parameters' => ['descriptions' => $merged_descriptions]]
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\ValueStringifier)
	/** {@inheritdoc} */
	public function stringifyValue($value, TextOptions $text_options): string
	{
		$enumeration = $this->enumeration;
		return UText::stringify($enumeration::getName($value), $text_options, ['quote_strings' => true]);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\SchemaData)
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
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\RequiredPropertyNamesLoader)
	/** {@inheritdoc} */
	protected function loadRequiredPropertyNames(): void
	{
		$this->addRequiredPropertyName('enumeration');
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'enumeration':
				return $this->createProperty()
					->setMode('w-')
					->setAsStrictClass(KitEnumeration::class)
					->bind(self::class)
				;
			case 'values':
				//no break
			case 'non_values':
				return $this->createProperty()
					->setMode('w-')
					->setAsArray(function (&$key, &$value): bool {
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
				return $this->createProperty()->setMode('w-')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
	
	
	
	//Protected methods
	/**
	 * Check if names can be shown.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if names can be shown.</p>
	 */
	protected function canShowNames(): bool
	{
		return !$this->values_only && !$this->hide_names;
	}
	
	/**
	 * Check if values can be shown.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if values can be shown.</p>
	 */
	protected function canShowValues(): bool
	{
		return !$this->names_only && !$this->hide_values;
	}
	
	/**
	 * Get names values.
	 * 
	 * @return int[]|float[]|string[]
	 * <p>The names values, as <samp>name => value</samp> pairs.</p>
	 */
	protected function getNamesValues(): array
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
	 * Get names descriptions.
	 * 
	 * @param \Feralygon\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string[]
	 * <p>The names descriptions, as <samp>name => description</samp> pairs.</p>
	 */
	protected function getNamesDescriptions(TextOptions $text_options): array
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
				$description = UText::formatMessage($description, true);
				if ($show_names || $show_values) {
					/**
					 * @description Enumeration element description (with name and value).
					 * @placeholder label The enumeration element label.
					 * @placeholder name_value The enumeration element name and/or value.
					 * @placeholder description The enumeration element description.
					 * @example Not Modified (given as "NOT_MODIFIED" or 304): \
					 * redirection "Not Modified" HTTP status code.
					 */
					$descriptions[$name] = UText::localize(
						"{{label}} (given as {{name_value}}): {{description}}", 
						self::class, $text_options, [
							'parameters' => [
								'label' => $enumeration::getNameLabel($name, $text_options),
								'name_value' => UText::commify(
									$show_names && $show_values && $name !== $value
										? [$name, $value]
										: ($show_names ? [$name] : [$value])
									, $text_options, 'or', true
								),
								'description' => $description
							]
						]
					);
				} else {
					/**
					 * @description Enumeration element description.
					 * @placeholder label The enumeration element label.
					 * @placeholder description The enumeration element description.
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
				 * @description Enumeration element description (no description).
				 * @placeholder label The enumeration element label.
				 * @placeholder name_value The enumeration element name and/or value.
				 * @example Not Modified: given as "NOT_MODIFIED" or 304.
				 */
				$descriptions[$name] = UText::localize(
					"{{label}}: given as {{name_value}}.", 
					self::class, $text_options, [
						'parameters' => [
							'label' => $enumeration::getNameLabel($name, $text_options),
							'name_value' => UText::commify(
								$show_names && $show_values && $name !== $value
									? [$name, $value]
									: ($show_names ? [$name] : [$value])
								, $text_options, 'or', true
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
