<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Input\Prototypes;

use Feralygon\Kit\Core\Prototype;

/**
 * Core input modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Components\Input\Components\Modifier
 * @see \Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier\Interfaces\Name
 * @see \Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier\Interfaces\Error
 * @see \Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier\Interfaces\Priority
 * @see \Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier\Interfaces\Information
 * @see \Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier\Interfaces\Stringification
 * @see \Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier\Interfaces\ErrorInformation
 */
abstract class Modifier extends Prototype {}
