<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Traits;

/** This trait defines a method to get the base scope from an entity. */
trait BaseScope
{
	//Protected static methods
	/**
	 * Get base scope.
	 * 
	 * Placeholders may optionally be set as <samp>{{placeholder}}</samp>, 
	 * corresponding directly to properties in this entity, and must be exclusively composed by identifiers.<br>
	 * <br>
	 * Identifiers are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
	 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
	 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used with pointers to specific object properties or associative array values, 
	 * by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
	 * with no limit on the number of chained pointers.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the identifiers are interpreted as getter method calls, but they cannot be given any arguments.
	 * 
	 * @return string|null
	 * <p>The base scope or <code>null</code> if none is set.</p>
	 */
	protected static function getBaseScope(): ?string
	{
		return null;
	}
}
