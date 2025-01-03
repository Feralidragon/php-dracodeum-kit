<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Time\Options;

use Dracodeum\Kit\Options;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\Time as UTime;

/**
 * Time utility <code>hperiod</code> method options.
 * 
 * @property bool $short [default = false]
 * <p>Return the short form of the human-readable period.</p>
 * @property int|null $precision [default = null]
 * <p>The rounding precision to use, in other words, the maximum number of decimal places to keep.<br>
 * If not set, then a specific precision is automatically chosen and used.</p>
 * @property int|null $limit [default = null]
 * <p>The limit to use on the number of multiples returned.<br>
 * If not set, then a specific limit is automatically chosen and used.</p>
 * @property int|float|null $min_multiple [default = null]
 * <p>The minimum multiple to use, which can be defined by:<br>
 * &nbsp; &#8226; &nbsp; a number of seconds, such as: <code>1</code>, <code>60</code>, <code>3600</code>, ...<br>
 * &nbsp; &#8226; &nbsp; a symbol, such as: <samp>s</samp>, <samp>min</samp>, <samp>h</samp>, ... <br>
 * &nbsp; &#8226; &nbsp; a label, such as: <samp>second</samp>, <samp>minute</samp>, <samp>hour</samp>, ...<br>
 * <br>
 * If not set, then a specific multiple is automatically chosen and used.</p>
 * @property int|float|null $max_multiple [default = null]
 * <p>The maximum multiple to use, which can be defined by:<br>
 * &nbsp; &#8226; &nbsp; a number of seconds, such as: <code>1</code>, <code>60</code>, <code>3600</code>, ...<br>
 * &nbsp; &#8226; &nbsp; a symbol, such as: <samp>s</samp>, <samp>min</samp>, <samp>h</samp>, ... <br>
 * &nbsp; &#8226; &nbsp; a label, such as: <samp>second</samp>, <samp>minute</samp>, <samp>hour</samp>, ...<br>
 * <br>
 * If not set, then the highest multiple supported is used.</p>
 */
class Hperiod extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'short':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
			case 'precision':
				return $this->createProperty()->setAsInteger(true, null, true)->setDefaultValue(null);
			case 'limit':
				return $this->createProperty()
					->setAsInteger(true, null, true)
					->addEvaluator(function (&$value): bool {
						return !isset($value) || $value > 0;
					})
					->setDefaultValue(null)
				;
			case 'min_multiple':
				//no break
			case 'max_multiple':
				return $this->createProperty()
					->addEvaluator(function (&$value): bool {
						return UTime::evaluateMultiple($value, true);
					})
					->setDefaultValue(null)
				;
		}
		return null;
	}
}
