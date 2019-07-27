<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Prototypes;

use Feralygon\Kit\Prototype;

/**
 * @see \Feralygon\Kit\Components\Input\Components\Modifier
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Name
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Priority
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\ErrorUnset
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\ErrorMessage
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData
 */
abstract class Modifier extends Prototype {}
