<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factories\Component\Builder\Interfaces;

use Feralygon\Kit\Components\Input as Component;

/**
 * This interface defines a method to build an input component instance.
 * 
 * @since 1.0.0
 */
interface Input
{
	//Public methods
	/**
	 * Build instance with a given prototype.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Prototypes\Input|array|string $prototype <p>The prototype to build with, 
	 * which may be given in one of the following types or formats:<br>
	 * &nbsp; &#8226; &nbsp; an instance, class or name;<br>
	 * &nbsp; &#8226; &nbsp; a <samp>class, properties</samp> array, 
	 * with the properties given as <samp>name => value</samp> pairs 
	 * (example: <samp>[Prototype::class, ['name1' => 'value1', 'name2' => 'value2']]</samp>);<br>
	 * &nbsp; &#8226; &nbsp; a <samp>name, properties</samp> array, 
	 * with the properties given as <samp>name => value</samp> pairs 
	 * (example: <samp>['proto_name', ['name1' => 'value1', 'name2' => 'value2']]</samp>);<br>
	 * &nbsp; &#8226; &nbsp; a set of properties, as <samp>name => value</samp> pairs.
	 * </p>
	 * @param array $properties [default = []] <p>The properties to build with, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Components\Input <p>The built instance with the given prototype.</p>
	 */
	public function build($prototype, array $properties = []) : Component;
}
