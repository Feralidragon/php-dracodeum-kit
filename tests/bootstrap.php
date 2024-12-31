<?php

//autoload
require_once __DIR__ . '/../vendor/autoload.php';

//use
use Dracodeum\Kit\Root\System;

//framework
System::setAsFramework();

//environment
System::setEnvironment('debug');
