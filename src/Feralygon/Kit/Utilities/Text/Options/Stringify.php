<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Options;

use Feralygon\Kit\Options;
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * Text utility <code>stringify</code> method options.
 * 
 * @since 1.0.0
 * @property bool $prepend_type [default = false]
 * <p>Prepend the type of the given value in the returning string.<br>
 * When prepending the type, a slightly different string may be generated for the given value, 
 * when compared with the resulting string otherwise.<br>
 * <br>
 * NOTE: This property is entirely ignored if the used text info scope is of an end-user 
 * or ignored for non-associative arrays only if <var>$non_assoc_mode</var> below is set.</p>
 * @property bool $quote_strings [default = false]
 * <p>Add quotation marks to string value types in the returning string.<br>
 * Even if not enabled, if <var>$prepend_type</var> above is set to boolean <code>true</code>, 
 * quotation marks may still be added anyway.</p>
 * @property bool $non_stringifiable [default = false]
 * <p>If the given value is an object which implements either the <code>__toString</code> method or 
 * the <code>Feralygon\Kit\Interfaces\Stringifiable</code> interface, 
 * then do not attempt to stringify it through any of them.</p>
 * @property string|null $non_assoc_mode [default = null]
 * <p>The text utility <code>Feralygon\Kit\Utilities\Text</code> class non-associative array 
 * stringification mode to use, which can be any of the following:<br>
 * <br>
 * &nbsp; &#8226; &nbsp; <code>STRING_NONASSOC_MODE_COMMA_LIST</code> : 
 * Convert non-associative arrays into comma-separated lists.<br><br>
 * &nbsp; &#8226; &nbsp; <code>STRING_NONASSOC_MODE_COMMA_LIST_AND</code> : 
 * Convert non-associative arrays into comma-separated lists, 
 * with an "and" conjunction for the last two elements.<br><br>
 * &nbsp; &#8226; &nbsp; <code>STRING_NONASSOC_MODE_COMMA_LIST_OR</code> : 
 * Convert non-associative arrays into comma-separated lists, 
 * with an "or" conjunction for the last two elements.<br><br>
 * &nbsp; &#8226; &nbsp; <code>STRING_NONASSOC_MODE_COMMA_LIST_NOR</code> : 
 * Convert non-associative arrays into comma-separated lists, 
 * with a "nor" conjunction for the last two elements.<br><br>
 * <br>
 * NOTE: If the used text info scope is of an end-user, 
 * then the mode applied is <code>STRING_NONASSOC_MODE_COMMA_LIST</code> by default.</p>
 * @property bool $no_throw [default = false]
 * <p>Do not throw an exception.</p>
 * @see https://php.net/manual/en/language.oop5.magic.php#object.tostring
 * @see \Feralygon\Kit\Utilities\Text
 * @see \Feralygon\Kit\Interfaces\Stringifiable
 */
class Stringify extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'prepend_type':
				//no break
			case 'quote_strings':
				//no break
			case 'non_stringifiable':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
			case 'non_assoc_mode':
				return $this->createProperty()
					->setAsString(true, true)
					->addEvaluator(function (&$value): bool {
						return !isset($value) || in_array($value, [
							UText::STRING_NONASSOC_MODE_COMMA_LIST,
							UText::STRING_NONASSOC_MODE_COMMA_LIST_AND,
							UText::STRING_NONASSOC_MODE_COMMA_LIST_OR,
							UText::STRING_NONASSOC_MODE_COMMA_LIST_NOR
						], true);
					})
					->setDefaultValue(null)
				;
			case 'no_throw':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
		}
		return null;
	}
}
