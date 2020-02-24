<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes;

use Dracodeum\Kit\Prototype;

/**
 * @see \Dracodeum\Kit\Components\Store
 * @see \Dracodeum\Kit\Prototypes\Store\Interfaces\Checker
 * @see \Dracodeum\Kit\Prototypes\Store\Interfaces\Returner
 * @see \Dracodeum\Kit\Prototypes\Store\Interfaces\Inserter
 * @see \Dracodeum\Kit\Prototypes\Store\Interfaces\Updater
 * @see \Dracodeum\Kit\Prototypes\Store\Interfaces\Deleter
 * @see \Dracodeum\Kit\Prototypes\Store\Interfaces\UidScopePlaceholderValueString
 */
abstract class Store extends Prototype {}
