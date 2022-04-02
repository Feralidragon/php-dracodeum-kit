<?php

/**
 * Require or include this file in order to use this package.
 * 
 * @version 1.0.0
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

use Dracodeum\Kit\Root\Loader;



//prevent the loader from being loaded again
if (class_exists(Loader::class, false)) {
	return false;
}

//constants
define('DRACODEUM_KIT_DIRECTORY', __DIR__ . '/src');

//required classes
require_once DRACODEUM_KIT_DIRECTORY . '/Dracodeum/Kit/Root/Loader.php';
require_once DRACODEUM_KIT_DIRECTORY . '/Dracodeum/Kit/Root/Loader/Package.php';

//set package
Loader::setPackage('dracodeum', 'kit', DRACODEUM_KIT_DIRECTORY);

//return
return true;
