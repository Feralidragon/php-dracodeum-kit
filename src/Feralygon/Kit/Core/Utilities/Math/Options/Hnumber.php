<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Math\Options;

use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Utilities\{
	Math as UMath,
	Type as UType
};

/**
 * Core math utility hnumber method options class.
 * 
 * @since 1.0.0
 * @property bool $long [default = false] <p>Return the long form of the human-readable number.</p>
 * @property int|null $precision [default = null] <p>The rounding precision, in other words, the maximum number of decimal places to keep.<br>
 * If not set, a specific precision is automatically chosen and used, otherwise it must always be greater than or equal to <code>0</code>.</p>
 * @property string|int|null $min_multiple [default = null] <p>The minimum multiple to use, which can be defined by:<br>
 * &nbsp; &#8226; &nbsp; a number, such as: <code>1</code>, <code>1000</code>, <code>1000000</code>, ...<br>
 * &nbsp; &#8226; &nbsp; a symbol, such as: <samp>K</samp>, <samp>M</samp>, <samp>B</samp>, ... <br>
 * &nbsp; &#8226; &nbsp; a label, such as: <samp>thousand</samp>, <samp>million</samp>, <samp>billion</samp>, ...<br>
 * <br>
 * If not set, the lowest multiple supported is used.</p>
 * @property string|int|null $max_multiple [default = null] <p>The maximum multiple to use, which can be defined by:<br>
 * &nbsp; &#8226; &nbsp; a number, such as: <code>1</code>, <code>1000</code>, <code>1000000</code>, ...<br>
 * &nbsp; &#8226; &nbsp; a symbol, such as: <samp>K</samp>, <samp>M</samp>, <samp>B</samp>, ... <br>
 * &nbsp; &#8226; &nbsp; a label, such as: <samp>thousand</samp>, <samp>million</samp>, <samp>billion</samp>, ...<br>
 * <br>
 * If not set, the highest multiple supported is used.</p>
 * @see \Feralygon\Kit\Core\Utilities\Math
 */
class Hnumber extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'long':
				$value = $value ?? false;
				return UType::evaluateBoolean($value);
			case 'precision':
				return !isset($value) || (UType::evaluateInteger($value) && $value >= 0);
			case 'min_multiple':
				//no break
			case 'max_multiple':
				return UMath::evaluateMultiple($value, true);
		}
		return null;
	}
}
