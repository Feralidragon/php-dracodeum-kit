<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Environments;

use Feralygon\Kit\Root\System\Environment;
use Feralygon\Kit\Root\System;

/** @since 1.0.0 */
class Production extends Environment
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'production';
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
		System::setIniOption('display_errors', false);
		System::setErrorReportingFlags(E_ALL ^ E_NOTICE ^ E_STRICT ^ E_DEPRECATED);
	}
}
