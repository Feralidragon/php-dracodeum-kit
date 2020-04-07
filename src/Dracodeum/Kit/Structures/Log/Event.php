<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Structures\Log;

use Dracodeum\Kit\Structure;
use Dracodeum\Kit\Enumerations\DateTime\Format as EDateTimeFormat;
use Dracodeum\Kit\Enumerations\Log\Level as ELogLevel;
use Dracodeum\Kit\Primitives\Vector;

/**
 * Log event structure.
 * 
 * @property-read string $id [coercive]
 * <p>The ID, which uniquely identifies this event.<br>
 * It cannot be empty.</p>
 * @property-read string $timestamp [coercive = datetime] [default = 'now']
 * <p>The timestamp, as a string using the ISO 8601 format in UTC with microseconds.</p>
 * @property int $level [coercive = enumeration value]
 * <p>The severity level, as a value from the <code>Dracodeum\Kit\Enumerations\Log\Level</code> enumeration.</p>
 * @property string $message [coercive]
 * <p>The message.<br>
 * It cannot be empty.</p>
 * @property-read string|null $host [coercive]
 * <p>The host, as the hostname or IP address where this event was generated from.<br>
 * If set, then it cannot be empty.</p>
 * @property-read string $origin [coercive]
 * <p>The origin, as the originally used entry point to execute the application which generated this event, 
 * such as <samp>POST http://myservice.com/myresource</samp> when the origin was an HTTP request for example.<br>
 * It cannot be empty.</p>
 * @property-read string|null $session [coercive] [default = null]
 * <p>The session UUID (Universally Unique Identifier), as a string which uniquely identifies a single session instance 
 * of the application, representing a group of one or more runtimes.<br>
 * If set, then it cannot be empty.</p>
 * @property-read string $runtime [coercive]
 * <p>The runtime UUID (Universally Unique Identifier), as a randomly generated string which uniquely identifies 
 * a single runtime instance of the application.<br>
 * It cannot be empty.</p>
 * @property-read string|null $class [coercive] [default = null]
 * <p>The class name, which identifies the class which generated this event.<br>
 * If set, then it cannot be empty.</p>
 * @property-read string|null $function [coercive] [default = null]
 * <p>The function name, which identifies the function which generated this event.<br>
 * If set, then it cannot be empty.</p>
 * @property string|null $name [coercive] [default = null]
 * <p>The name.<br>
 * If set, then it cannot be empty.</p>
 * @property string|null $tag [coercive] [default = null]
 * <p>The tag.<br>
 * If set, then it cannot be empty.</p>
 * @property mixed $data [default = null]
 * <p>The data.</p>
 * @property \Dracodeum\Kit\Primitives\Vector $tags [coercive] [default = \Dracodeum\Kit\Primitives\Vector::build()]
 * <p>The tags vector instance, with each value coerced into a string.<br>
 * The values cannot be empty.</p>
 * @see https://en.wikipedia.org/wiki/ISO_8601
 * @see \Dracodeum\Kit\Enumerations\Log\Level
 */
class Event extends Structure
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('id')->setMode('r+')->setAsString(true);
		$this->addProperty('timestamp')
			->setMode('r+')
			->setAsDateTime(EDateTimeFormat::ISO8601_UTC_MICRO, true)
			->setDefaultValue('now')
		;
		$this->addProperty('level')->setAsEnumerationValue(ELogLevel::class);
		$this->addProperty('message')->setAsString(true);
		$this->addProperty('host')->setMode('r+')->setAsString(true, true);
		$this->addProperty('origin')->setMode('r+')->setAsString(true);
		$this->addProperty('session')->setMode('r+')->setAsString(true, true)->setDefaultValue(null);
		$this->addProperty('runtime')->setMode('r+')->setAsString(true);
		$this->addProperty('class')->setMode('r+')->setAsString(true, true)->setDefaultValue(null);
		$this->addProperty('function')->setMode('r+')->setAsString(true, true)->setDefaultValue(null);
		$this->addProperty('name')->setAsString(true, true)->setDefaultValue(null);
		$this->addProperty('tag')->setAsString(true, true)->setDefaultValue(null);
		$this->addProperty('data')->setDefaultValue(null);
		$this->addProperty('tags')->setAsVector(Vector::build()->setAsString(true))->setDefaultValue([]);
	}
}
