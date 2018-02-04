<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Environments;

use Feralygon\Kit\Root\System\Environment;
use Feralygon\Kit\Root\System;

/**
 * Root system staging environment class.
 * 
 * @since 1.0.0
 */
class Staging extends Environment
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'staging';
	}
	
	/** {@inheritdoc} */
	public function isDebug() : bool
	{
		return false;
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function initialize() : void
	{
		System::setIniOption('display_errors', true);
		System::setErrorReportingFlags(E_ALL ^ E_NOTICE ^ E_STRICT ^ E_DEPRECATED);
	}
}
