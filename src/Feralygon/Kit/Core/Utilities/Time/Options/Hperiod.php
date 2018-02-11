<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Time\Options;

use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Traits\LazyProperties\Objects\Property;
use Feralygon\Kit\Core\Utilities\{
	Time as UTime,
	Type as UType
};

/**
 * Core time utility hperiod method options class.
 * 
 * @since 1.0.0
 * @property bool $short [default = false] <p>Return the short form of the human-readable period.</p>
 * @property int|null $precision [default = null] <p>The rounding precision, 
 * in other words, the maximum number of decimal places to keep.<br>
 * If not set, a specific precision is automatically chosen and used, 
 * otherwise it must always be greater than or equal to <code>0</code>.</p>
 * @property int|null $limit [default = null] <p>The limit on the number of multiples returned.<br>
 * If not set, a specific limit is automatically chosen and used, 
 * otherwise it must always be greater than <code>0</code>.</p>
 * @property string|float|null $min_multiple [default = null] <p>The minimum multiple to use, 
 * which can be defined by:<br>
 * &nbsp; &#8226; &nbsp; a number of seconds, such as: <code>1</code>, <code>60</code>, <code>3600</code>, ...<br>
 * &nbsp; &#8226; &nbsp; a symbol, such as: <samp>s</samp>, <samp>min</samp>, <samp>h</samp>, ... <br>
 * &nbsp; &#8226; &nbsp; a label, such as: <samp>second</samp>, <samp>minute</samp>, <samp>hour</samp>, ...<br>
 * <br>
 * If not set, a specific multiple is automatically chosen and used.</p>
 * @property string|float|null $max_multiple [default = null] <p>The maximum multiple to use, 
 * which can be defined by:<br>
 * &nbsp; &#8226; &nbsp; a number of seconds, such as: <code>1</code>, <code>60</code>, <code>3600</code>, ...<br>
 * &nbsp; &#8226; &nbsp; a symbol, such as: <samp>s</samp>, <samp>min</samp>, <samp>h</samp>, ... <br>
 * &nbsp; &#8226; &nbsp; a label, such as: <samp>second</samp>, <samp>minute</samp>, <samp>hour</samp>, ...<br>
 * <br>
 * If not set, the highest multiple supported is used.</p>
 * @see \Feralygon\Kit\Core\Utilities\Time
 */
class Hperiod extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'short':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
			case 'precision':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return !isset($value) || (UType::evaluateInteger($value) && $value >= 0);
					})
					->setDefaultValue(null)
				;
			case 'limit':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return !isset($value) || (UType::evaluateInteger($value) && $value > 0);
					})
					->setDefaultValue(null)
				;
			case 'min_multiple':
				//no break
			case 'max_multiple':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UTime::evaluateMultiple($value, true);
					})
					->setDefaultValue(null)
				;
		}
		return null;
	}
}
