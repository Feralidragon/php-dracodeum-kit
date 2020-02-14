<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes;

use Dracodeum\Kit\Prototype;

/**
 * @see \Dracodeum\Kit\Components\Provider
 * @see \Dracodeum\Kit\Prototypes\Provider\Interfaces\Checker
 * @see \Dracodeum\Kit\Prototypes\Provider\Interfaces\Returner
 * @see \Dracodeum\Kit\Prototypes\Provider\Interfaces\Inserter
 * @see \Dracodeum\Kit\Prototypes\Provider\Interfaces\Updater
 * @see \Dracodeum\Kit\Prototypes\Provider\Interfaces\Deleter
 */
abstract class Provider extends Prototype {}
