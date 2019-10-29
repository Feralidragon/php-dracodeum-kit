<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Math\Options;

use Dracodeum\Kit\Options;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\Math as UMath;

/**
 * Math utility <code>hnumber</code> method options.
 * 
 * @property bool $long [coercive] [default = false]
 * <p>Return the long form of the human-readable number.</p>
 * @property int|null $precision [coercive] [default = null]
 * <p>The rounding precision to use, in other words, the maximum number of decimal places to keep.<br>
 * If not set, then a specific precision is automatically chosen and used, 
 * otherwise it must always be greater than or equal to <code>0</code>.</p>
 * @property int|null $min_multiple [coercive = math multiple] [default = null]
 * <p>The minimum multiple to use, which can be defined by:<br>
 * &nbsp; &#8226; &nbsp; a number, such as: <code>1</code>, <code>1000</code>, <code>1000000</code>, ...<br>
 * &nbsp; &#8226; &nbsp; a symbol, such as: <samp>K</samp>, <samp>M</samp>, <samp>B</samp>, ... <br>
 * &nbsp; &#8226; &nbsp; a label, such as: <samp>thousand</samp>, <samp>million</samp>, <samp>billion</samp>, ...<br>
 * <br>
 * If not set, then the lowest multiple supported is used.</p>
 * @property int|null $max_multiple [coercive = math multiple] [default = null]
 * <p>The maximum multiple to use, which can be defined by:<br>
 * &nbsp; &#8226; &nbsp; a number, such as: <code>1</code>, <code>1000</code>, <code>1000000</code>, ...<br>
 * &nbsp; &#8226; &nbsp; a symbol, such as: <samp>K</samp>, <samp>M</samp>, <samp>B</samp>, ... <br>
 * &nbsp; &#8226; &nbsp; a label, such as: <samp>thousand</samp>, <samp>million</samp>, <samp>billion</samp>, ...<br>
 * <br>
 * If not set, then the highest multiple supported is used.</p>
 */
class Hnumber extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'long':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
			case 'precision':
				return $this->createProperty()->setAsInteger(true, null, true)->setDefaultValue(null);
			case 'min_multiple':
				//no break
			case 'max_multiple':
				return $this->createProperty()
					->addEvaluator(function (&$value): bool {
						return UMath::evaluateMultiple($value, true);
					})
					->setDefaultValue(null)
				;
		}
		return null;
	}
}
