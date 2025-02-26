<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Structures\Log;

use Dracodeum\Kit\Structure;
use Dracodeum\Kit\Enumerations\DateTime\Format as EDateTimeFormat;
use Dracodeum\Kit\Enumerations\Log\Level as ELevel;
use Dracodeum\Kit\Primitives\Vector;
use Dracodeum\Kit\Root\{
	Log,
	Remote,
	Runtime,
	System
};

/**
 * This structure represents a log event.
 * 
 * @property-read string $id [default = auto]
 * <p>The ID, which uniquely identifies this event.</p>
 * @property-read string $timestamp [default = 'now']
 * <p>The timestamp, as a string using the ISO 8601 format in UTC with microseconds.</p>
 * @property int $level
 * <p>The level, as a value from the <code>Dracodeum\Kit\Enumerations\Log\Level</code> enumeration.</p>
 * @property string $message
 * <p>The message.</p>
 * @property string|null $ip_address [default = auto]
 * <p>The remote IP address from which this event was generated.</p>
 * @property string|null $agent [default = auto]
 * <p>The remote agent from which this event was generated.</p>
 * @property string $origin [default = auto]
 * <p>The origin, as the originally used entry point to execute the application which generated this event, 
 * such as <samp>POST http://myservice.com/myresource</samp> when the origin was an HTTP request for example.</p>
 * @property string|null $user [default = null]
 * <p>The user from whom this event was generated.</p>
 * @property string|null $session [default = null]
 * <p>The session UUID (Universally Unique Identifier), as a string which uniquely identifies a single session instance 
 * of the application, representing a group of one or more runtimes.</p>
 * @property-read string|null $host [default = auto]
 * <p>The host, as the hostname or IP address where this event was generated from.</p>
 * @property-read string $runtime [default = auto]
 * <p>The runtime UUID (Universally Unique Identifier), as a randomly generated string which uniquely identifies 
 * a single runtime instance of the application.</p>
 * @property-read object|null $object [default = null]
 * <p>The object which generated this event.</p>
 * @property-read string|null $class [default = null]
 * <p>The class which generated this event.</p>
 * @property-read string|null $function [default = null]
 * <p>The name of the function which generated this event.</p>
 * @property string|null $name [default = null]
 * <p>The name.</p>
 * @property mixed $data [default = null]
 * <p>The data.</p>
 * @property \Dracodeum\Kit\Primitives\Vector $tags [default = \Dracodeum\Kit\Primitives\Vector::build()]
 * <p>The tags vector instance.</p>
 * @see https://en.wikipedia.org/wiki/ISO_8601
 * @see \Dracodeum\Kit\Enumerations\Log\Level
 */
class Event extends Structure
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('id')->setMode('r+')->setAsString(true)->setDefaultGetter([Log::class, 'generateEventId']);
		$this->addProperty('timestamp')
			->setMode('r+')
			->setAsDateTime(EDateTimeFormat::ISO8601_UTC_MICRO, true)
			->setDefaultValue('now')
		;
		$this->addProperty('level')->setAsEnumerationValue(ELevel::class);
		$this->addProperty('message')->setAsString(true);
		$this->addProperty('ip_address')->setAsString(true, true)->setDefaultGetter(function () {
			return Remote::getIpAddress(true);
		});
		$this->addProperty('agent')->setAsString(true, true)->setDefaultGetter(function () {
			return Remote::getAgent(true);
		});
		$this->addProperty('origin')->setAsString(true)->setDefaultGetter([Runtime::class, 'getOrigin']);
		$this->addProperty('user')->setAsString(true, true)->setDefaultValue(null);
		$this->addProperty('session')->setAsString(true, true)->setDefaultValue(null);
		$this->addProperty('host')->setMode('r+')->setAsString(true, true)->setDefaultGetter(function () {
			return System::getHost(true);
		});
		$this->addProperty('runtime')->setMode('r+')->setAsString(true)->setDefaultGetter([Runtime::class, 'getUuid']);
		$this->addProperty('object')->setMode('r+')->setAsStrictObject(null, true)->setDefaultValue(null);
		$this->addProperty('class')->setMode('r+')->setAsClass(null, true)->setDefaultValue(null);
		$this->addProperty('function')->setMode('r+')->setAsString(true, true)->setDefaultValue(null);
		$this->addProperty('name')->setAsString(true, true)->setDefaultValue(null);
		$this->addProperty('data')->setDefaultValue(null);
		$this->addProperty('tags')->setAsVector(Vector::build()->setAsString(true))->setDefaultValue([]);
	}
}
