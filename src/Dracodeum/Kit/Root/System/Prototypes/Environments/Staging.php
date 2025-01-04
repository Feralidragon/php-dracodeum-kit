<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\System\Prototypes\Environments;

use Dracodeum\Kit\Root\System\Prototypes\Environment;
use Dracodeum\Kit\Root\System\Enumerations\DumpVerbosityLevel as EDumpVerbosityLevel;
use Dracodeum\Kit\Root\System;

/** This environment prototype sets the system for staging (post-development and pre-production). */
class Staging extends Environment
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'staging';
	}
	
	/** {@inheritdoc} */
	public function isDebug(): bool
	{
		return false;
	}
	
	/** {@inheritdoc} */
	public function getDumpVerbosityLevel(): int
	{
		return EDumpVerbosityLevel::LOW;
	}
	
	/** {@inheritdoc} */
	public function apply(): void
	{
		System::setIniOption('display_errors', true);
		System::setErrorReportingFlags(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
	}
}
