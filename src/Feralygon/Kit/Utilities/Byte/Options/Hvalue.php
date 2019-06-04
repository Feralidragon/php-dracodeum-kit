<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Byte\Options;

use Feralygon\Kit\Options;
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Utilities\Byte as UByte;

/**
 * Byte utility <code>hvalue</code> method options.
 * 
 * @since 1.0.0
 * @property bool $long [coercive] [default = false]
 * <p>Return the long form of the human-readable value.</p>
 * @property int|null $precision [coercive] [default = null]
 * <p>The rounding precision to use, in other words, the maximum number of decimal places to keep.<br>
 * If not set, then a specific precision is automatically chosen and used, 
 * otherwise it must always be greater than or equal to <code>0</code>.</p>
 * @property int|null $min_multiple [coercive = byte multiple] [default = null]
 * <p>The minimum multiple to use, which can be defined by:<br>
 * &nbsp; &#8226; &nbsp; a number of bytes, 
 * such as: <code>1</code>, <code>1000</code>, <code>1000000</code>, ...<br>
 * &nbsp; &#8226; &nbsp; a symbol, 
 * such as: <samp>B</samp>, <samp>KB</samp> or <samp>K</samp>, <samp>MB</samp> or <samp>M</samp>, ... <br>
 * &nbsp; &#8226; &nbsp; a label, 
 * such as: <samp>byte</samp>, <samp>kilobyte</samp>, <samp>megabyte</samp>, ...<br>
 * <br>
 * If not set, then the lowest multiple supported is used.</p>
 * @property int|null $max_multiple [coercive = byte multiple] [default = null]
 * <p>The maximum multiple to use, which can be defined by:<br>
 * &nbsp; &#8226; &nbsp; a number of bytes, 
 * such as: <code>1</code>, <code>1000</code>, <code>1000000</code>, ...<br>
 * &nbsp; &#8226; &nbsp; a symbol, 
 * such as: <samp>B</samp>, <samp>KB</samp> or <samp>K</samp>, <samp>MB</samp> or <samp>M</samp>, ... <br>
 * &nbsp; &#8226; &nbsp; a label, 
 * such as: <samp>byte</samp>, <samp>kilobyte</samp>, <samp>megabyte</samp>, ...<br>
 * <br>
 * If not set, then the highest multiple supported is used.</p>
 * @see \Feralygon\Kit\Utilities\Byte
 */
class Hvalue extends Options
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
						return UByte::evaluateMultiple($value, true);
					})
					->setDefaultValue(null)
				;
		}
		return null;
	}
}
