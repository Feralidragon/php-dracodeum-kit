<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Prototypes\Environments;

use Feralygon\Kit\Root\System\Prototypes\Environment;
use Feralygon\Kit\Root\System\Enumerations\DumpVerbosityLevel as EDumpVerbosityLevel;
use Feralygon\Kit\Root\System;

/** This environment prototype sets the system for internal debugging. */
class Debug extends Environment
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'debug';
	}
	
	/** {@inheritdoc} */
	public function isDebug(): bool
	{
		return true;
	}
	
	/** {@inheritdoc} */
	public function getDumpVerbosityLevel(): int
	{
		return EDumpVerbosityLevel::HIGH;
	}
	
	/** {@inheritdoc} */
	public function apply(): void
	{
		System::setIniOption('display_errors', true);
		System::setErrorReportingFlags(E_ALL);
	}
}
