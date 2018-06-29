<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Prototypes\Environments;

use Feralygon\Kit\Root\System\Prototypes\Environment;
use Feralygon\Kit\Root\System;

/**
 * This environment prototype sets the system for development and debugging.
 * 
 * @since 1.0.0
 */
class Development extends Environment
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'development';
	}
	
	/** {@inheritdoc} */
	public function isDebug(): bool
	{
		return true;
	}
	
	/** {@inheritdoc} */
	public function apply(): void
	{
		System::setIniOption('display_errors', true);
		System::setErrorReportingFlags(E_ALL);
	}
}
