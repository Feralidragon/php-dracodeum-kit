<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities;

use Dracodeum\Kit\{
	Traits,
	Utility
};
use Dracodeum\Kit\Utilities\Call\{
	Options,
	Exceptions
};
use Dracodeum\Kit\Root\System;

/**
 * This utility implements a set of methods used to get information from existing PHP functions, 
 * methods and callables.
 * 
 * @see https://php.net/manual/en/language.functions.php
 * @see https://php.net/manual/en/language.types.callable.php
 */
final class Call extends Utility
{
	//Traits
	use Traits\Memoization;
	
	
	
	//Public constants
	/** Parameters constants values (flag). */
	public const PARAMETERS_CONSTANTS_VALUES = 0x01;
	
	/** Parameters types short names (flag). */
	public const PARAMETERS_TYPES_SHORT_NAMES = 0x02;
	
	/** Parameters namespaces leading slash (flag). */
	public const PARAMETERS_NAMESPACES_LEADING_SLASH = 0x04;
	
	/** Parameters no mixed type (flag). */
	public const PARAMETERS_NO_MIXED_TYPE = 0x08;
	
	/** Type no mixed (flag). */
	public const TYPE_NO_MIXED = 0x01;
	
	/** Type short name (flag). */
	public const TYPE_SHORT_NAME = 0x02;
	
	/** Type namespace leading slash (flag). */
	public const TYPE_NAMESPACE_LEADING_SLASH = 0x04;
	
	/** Header constants values (flag). */
	public const HEADER_CONSTANTS_VALUES = 0x01;
	
	/** Header types short names (flag). */
	public const HEADER_TYPES_SHORT_NAMES = 0x02;
	
	/** Header namespaces leading slash (flag). */
	public const HEADER_NAMESPACES_LEADING_SLASH = 0x04;
	
	/** Header no mixed type (flag). */
	public const HEADER_NO_MIXED_TYPE = 0x08;
	
	/** Source constants values (flag). */
	public const SOURCE_CONSTANTS_VALUES = 0x01;
	
	/** Source types short names (flag). */
	public const SOURCE_TYPES_SHORT_NAMES = 0x02;
	
	/** Source namespaces leading slash (flag). */
	public const SOURCE_NAMESPACES_LEADING_SLASH = 0x04;
	
	/** Source no mixed type (flag). */
	public const SOURCE_NO_MIXED_TYPE = 0x08;
	
	
	
	//Final public static methods
	/**
	 * Validate a given function reference.
	 * 
	 * Only the following types and formats are considered valid functions:<br>
	 * &nbsp; &#8226; &nbsp; a callable;<br>
	 * &nbsp; &#8226; &nbsp; an array with exactly 2 elements, with the first element being an interface name, 
	 * class name or instance, and the second element being a method name, such as <code>['Class', 'method']</code>;<br>
	 * &nbsp; &#8226; &nbsp; a string as a function or method name, with a method being composed of an interface or 
	 * class name and method name, delimited by <samp>::</samp> or <samp>-&gt;</samp>, 
	 * such as <samp>Class::method</samp>.<br>
	 * <br>
	 * All types of methods are considered valid, regardless of their visibility, including protected and private ones.
	 * 
	 * @param callable|array|string $function
	 * <p>The function to validate.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Call\Exceptions\InvalidFunction
	 * @return void|bool
	 * <p>If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then boolean <code>true</code> is returned if the validation succeeded, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public static function validate($function, bool $no_throw = false)
	{
		//callable
		if (is_callable($function)) {
			if ($no_throw) {
				return true;
			}
			return;
		}
		
		//array
		if (
			is_array($function) && count($function) === 2 && isset($function[0]) && isset($function[1]) && (
				is_object($function[0]) || (is_string($function[0]) && Type::exists($function[0]))
			) && is_string($function[1]) && method_exists($function[0], $function[1])
		) {
			if ($no_throw) {
				return true;
			}
			return;
		}
		
		//string
		if (is_string($function)) {
			$f = explode('::', str_replace('->', '::', $function));
			if (
				(count($f) === 1 && function_exists($f[0])) || 
				(count($f) === 2 && Type::exists($f[0]) && method_exists($f[0], $f[1]))
			) {
				if ($no_throw) {
					return true;
				}
				return;
			}
		}
		
		//exception
		if ($no_throw) {
			return false;
		}
		throw new Exceptions\InvalidFunction([$function]);
	}
	
	/**
	 * Get a new reflection instance from a given function.
	 * 
	 * The returning reflection instance depends on the type of function given.<br>
	 * In the case of a class or instance method, a reflection instance of the <code>ReflectionMethod</code> class is 
	 * returned.<br>
	 * If, however, it's any other type of function, such as a global, local or anonymous function, 
	 * a reflection instance of the <code>ReflectionFunction</code> class is returned instead.
	 * 
	 * @see https://php.net/manual/en/class.reflectionfunction.php
	 * @see https://php.net/manual/en/class.reflectionmethod.php
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @param bool $methodify [default = false]
	 * <p>Coerce into a <code>ReflectionMethod</code> instance, 
	 * if the given function is a closure which represents a method.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Call\Exceptions\InvalidFunction
	 * @return \ReflectionFunction|\ReflectionMethod|null
	 * <p>A new reflection instance from the given function.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be retrieved.</p>
	 */
	final public static function reflection(
		$function, bool $methodify = false, bool $no_throw = false
	): ?\ReflectionFunctionAbstract
	{
		//validate
		$valid = self::validate($function, $no_throw);
		if ($no_throw && !$valid) {
			return null;
		}
		
		//method (object)
		if (is_object($function) && !($function instanceof \Closure)) {
			return (new \ReflectionClass($function))->getMethod('__invoke');
		}
		
		//method (array)
		if (is_array($function)) {
			return (new \ReflectionClass($function[0]))->getMethod($function[1]);
		}
		
		//method (string)
		if (is_string($function)) {
			$function = str_replace('->', '::', $function);
			if (strpos($function, '::') !== false) {
				return new \ReflectionMethod($function);
			}
		}
		
		//function
		$reflection = new \ReflectionFunction($function);
		
		//methodify
		if ($methodify) {
			//object or class
			$object_class = $reflection->getClosureThis();
			if ($object_class === null) {
				$reflection_scope_class = $reflection->getClosureScopeClass();
				if ($reflection_scope_class !== null) {
					$object_class = $reflection_scope_class->getName();
				}
			}
			
			//finalize
			$name = $reflection->getName();
			if ($object_class !== null && !preg_match('/\{closure\}$/', $name)) {
				$reflection = new \ReflectionMethod($object_class, $name);
			}
		}
		
		//return
		return $reflection;
	}
	
	/**
	 * Calculate hash from a given function.
	 * 
	 * @see https://php.net/manual/en/function.hash.php
	 * @param callable|array|string $function
	 * <p>The function to calculate from.</p>
	 * @param string $algorithm [default = 'SHA1']
	 * <p>The hash algorithm to use, which can be any supported by the PHP <code>hash</code> function.</p>
	 * @param bool $raw [default = false]
	 * <p>Return the raw binary form of the hash, instead of its human-readable hexadecimal representation.</p>
	 * @return string
	 * <p>The hash from the given function.</p>
	 */
	final public static function hash($function, string $algorithm = 'SHA1', bool $raw = false): string
	{
		return self::memoize(function ($function, string $algorithm = 'SHA1', bool $raw = false): string {
			$data = self::name($function, true);
			if ($data === null) {
				//initialize
				$reflection = self::reflection($function);
				$data = $reflection->getName();
				
				//lines
				$start_line = $reflection->getStartLine();
				$end_line = $reflection->getEndLine();
				if ($start_line !== false && $end_line !== false) {
					$data .= "({$start_line}-{$end_line})";
				}
				
				//scope class
				$reflection_scope_class = $reflection->getClosureScopeClass();
				if ($reflection_scope_class !== null) {
					$data = "{$reflection_scope_class->getName()}:{$data}";
				}
			}
			return hash($algorithm, $data, $raw);
		});
	}
	
	/**
	 * Get modifiers from a given function.
	 * 
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @return string[]
	 * <p>The modifiers from the given function.</p>
	 */
	final public static function modifiers($function): array
	{
		//reflection
		$reflection = self::reflection($function, true);
		if (!($reflection instanceof \ReflectionMethod)) {
			return [];
		}
		
		//modifiers
		$modifiers = [];
		$flags = $reflection->getModifiers();
		if ($flags & \ReflectionMethod::IS_ABSTRACT) {
			$modifiers[] = 'abstract';
		} elseif ($flags & \ReflectionMethod::IS_FINAL) {
			$modifiers[] = 'final';
		}
		if ($flags & \ReflectionMethod::IS_PUBLIC) {
			$modifiers[] = 'public';
		} elseif ($flags & \ReflectionMethod::IS_PROTECTED) {
			$modifiers[] = 'protected';
		} elseif ($flags & \ReflectionMethod::IS_PRIVATE) {
			$modifiers[] = 'private';
		}
		if ($flags & \ReflectionMethod::IS_STATIC) {
			$modifiers[] = 'static';
		}
		return $modifiers;
	}
	
	/**
	 * Get name from a given function.
	 * 
	 * If the given function is anonymous, then <code>null</code> is returned.<br>
	 * If the given function belongs to a class and the <var>$full</var> parameter is given as 
	 * boolean <code>true</code>, then a string in the format <code>Class::name</code> is returned.<br>
	 * In every other case only the name itself is returned.
	 * 
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @param bool $full [default = false]
	 * <p>Return the full name, including the class it's declared in.</p>
	 * @param bool $short [default = false]
	 * <p>Return the short form of the class name instead of the full namespaced one.</p>
	 * @return string|null
	 * <p>The name from the given function or <code>null</code> if it has no name (anonymous).</p>
	 */
	final public static function name($function, bool $full = false, bool $short = false): ?string
	{
		$reflection = self::reflection($function);
		$name = $reflection->getName();
		if (preg_match('/\{closure\}$/', $name)) {
			return null;
		} elseif ($full) {
			$class = self::class($function, $short);
			if (isset($class)) {
				return "{$class}::{$name}";
			}
		}
		return $name;
	}
	
	/**
	 * Get parameters from a given function.
	 * 
	 * The returning parameters from the given function are represented by their types, names and default values.<br>
	 * The expected return format for each parameter is <samp>type name</samp> or <samp>type name = value</samp>.<br>
	 * <br>
	 * In parameters passed by reference, an additional <code>&amp;</code> is prepended.<br>
	 * In variadic parameters, an additional <code>...</code> is also prepended.
	 * 
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::PARAMETERS_CONSTANTS_VALUES</code> : 
	 * Return the constants values instead of their names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::PARAMETERS_TYPES_SHORT_NAMES</code> : 
	 * Return short names for classes and interfaces instead of full namespaced names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::PARAMETERS_NAMESPACES_LEADING_SLASH</code> : 
	 * Return namespaces with the leading slash.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::PARAMETERS_NO_MIXED_TYPE</code> : 
	 * Do not return mixed parameters with the <code>mixed</code> type keyword.</p>
	 * @return string[]
	 * <p>The parameters from the given function.</p>
	 */
	final public static function parameters($function, int $flags = 0x00): array
	{
		return self::memoize(function ($function, int $flags = 0x00): array {
			//initialize
			$reflection = self::reflection($function);
			$is_method = Type::isA($reflection, \ReflectionMethod::class);
			
			//parameters
			$parameters = [];
			foreach ($reflection->getParameters() as $parameter) {
				//type
				$type = 'mixed';
				$ptype = $parameter->getType();
				if ($ptype !== null) {
					//initialize
					$types_map = array_flip(Type::mnormalize((string)$ptype));
					unset($types_map['null']);
					
					//types
					$types = [];
					foreach ($types_map as $t => $i) {
						if (Type::exists($t)) {
							if ($flags & self::PARAMETERS_TYPES_SHORT_NAMES) {
								$t = Type::shortname($t);
							} elseif ($flags & self::PARAMETERS_NAMESPACES_LEADING_SLASH) {
								$t = "\\{$t}";
							}
						}
						$types[] = $t;
					}
					
					//type
					$type = implode('|', $types);
					
					//null
					if ($ptype->allowsNull() && $type !== 'mixed') {
						$type = count($types) > 1 ? "{$type}|null" : "?{$type}";
					}
					
					//finalize
					unset($types_map, $types);
				}
				
				//mixed
				if ($type === 'mixed' && ($flags & self::PARAMETERS_NO_MIXED_TYPE)) {
					$type = '';
				}
				
				//name
				$name = '$' . $parameter->getName();
				if ($parameter->isVariadic()) {
					$name = "...{$name}";
				}
				if ($parameter->isPassedByReference()) {
					$name = "&{$name}";
				}
				
				//value
				$value = '';
				if ($parameter->isDefaultValueAvailable()) {
					$value = ' = ';
					if (!($flags & self::PARAMETERS_CONSTANTS_VALUES) && $parameter->isDefaultValueConstant()) {
						$constant = $parameter->getDefaultValueConstantName();
						if (strpos($constant, '::') !== false) {
							//method
							if ($is_method) {
								$constant = str_replace(
									'self::', "{$parameter->getDeclaringClass()->getName()}::", $constant
								);
							}
							
							//flags
							if ($flags & self::PARAMETERS_TYPES_SHORT_NAMES) {
								[$constant_class, $constant_name] = explode('::', $constant);
								$constant = Type::shortname($constant_class) . '::' . $constant_name;
							} elseif ($flags & self::PARAMETERS_NAMESPACES_LEADING_SLASH) {
								$constant = "\\{$constant}";
							}
							
						} else {
							//name
							if (!defined($constant) && preg_match('/\\\\(?P<name>\w+)$/', $constant, $matches)) {
								$constant = $matches['name'];
							}
							
							//flags
							if ($flags & self::PARAMETERS_NAMESPACES_LEADING_SLASH) {
								$constant = "\\{$constant}";
							}
						}
						$value .= $constant;
					} else {
						$value .= Type::phpfy($parameter->getDefaultValue());
					}
				}
				
				//append
				$parameters[] = trim("{$type} {$name}{$value}");
			}
			
			//return
			return $parameters;
		});
	}
	
	/**
	 * Get type from a given function.
	 * 
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::TYPE_NO_MIXED</code> : 
	 * Do not return the <code>mixed</code> type keyword.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TYPE_SHORT_NAME</code> : 
	 * Return a short name for a class or interface instead of a full namespaced name.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TYPE_NAMESPACE_LEADING_SLASH</code> : 
	 * Return namespace with the leading slash.</p>
	 * @return string
	 * <p>The type from the given function.</p>
	 */
	final public static function type($function, int $flags = 0x00): string
	{
		//initialize
		$type = 'mixed';
		$rtype = self::reflection($function)->getReturnType();
		
		//process
		if ($rtype !== null) {
			//initialize
			$types_map = array_flip(Type::mnormalize((string)$rtype));
			unset($types_map['null']);
			
			//types
			$types = [];
			foreach ($types_map as $t => $i) {
				if (Type::exists($t)) {
					if ($flags & self::TYPE_SHORT_NAME) {
						$t = Type::shortname($t);
					} elseif ($flags & self::TYPE_NAMESPACE_LEADING_SLASH) {
						$t = "\\{$t}";
					}
				}
				$types[] = $t;
			}
			
			//type
			$type = implode('|', $types);
			
			//null
			if ($rtype->allowsNull() && $type !== 'mixed') {
				$type = count($types) > 1 ? "{$type}|null" : "?{$type}";
			}
		}
		
		//mixed
		if ($type === 'mixed' && ($flags & self::TYPE_NO_MIXED)) {
			$type = '';
		}
		
		//return
		return $type;
	}
	
	/**
	 * Get header from a given function.
	 * 
	 * The returning header from the given function is represented by its modifiers, name, parameters and type.<br>
	 * The expected return format is 
	 * <samp>modifier function name(type1 param1, type2 param2 = value2, ...): type</samp> .
	 * 
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::HEADER_CONSTANTS_VALUES</code> : 
	 * Return the constants values instead of their names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::HEADER_TYPES_SHORT_NAMES</code> : 
	 * Return short names for classes and interfaces instead of full namespaced names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::HEADER_NAMESPACES_LEADING_SLASH</code> : 
	 * Return namespaces with the leading slash.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::HEADER_NO_MIXED_TYPE</code> : 
	 * Do not return a mixed type nor parameters with the <code>mixed</code> type keyword.</p>
	 * @return string
	 * <p>The header from the given function.</p>
	 */
	final public static function header($function, int $flags = 0x00): string
	{
		//initialize
		$modifiers = self::modifiers($function);
		$name = self::name($function);
		
		//parameters
		$parameters_flags = 0x00;
		if ($flags & self::HEADER_CONSTANTS_VALUES) {
			$parameters_flags |= self::PARAMETERS_CONSTANTS_VALUES;
		}
		if ($flags & self::HEADER_TYPES_SHORT_NAMES) {
			$parameters_flags |= self::PARAMETERS_TYPES_SHORT_NAMES;
		}
		if ($flags & self::HEADER_NAMESPACES_LEADING_SLASH) {
			$parameters_flags |= self::PARAMETERS_NAMESPACES_LEADING_SLASH;
		}
		if ($flags & self::HEADER_NO_MIXED_TYPE) {
			$parameters_flags |= self::PARAMETERS_NO_MIXED_TYPE;
		}
		$parameters = self::parameters($function, $parameters_flags);
		
		//type
		$type_flags = 0x00;
		if ($flags & self::HEADER_NO_MIXED_TYPE) {
			$type_flags |= self::TYPE_NO_MIXED;
		}
		if ($flags & self::HEADER_TYPES_SHORT_NAMES) {
			$type_flags |= self::TYPE_SHORT_NAME;
		}
		if ($flags & self::HEADER_NAMESPACES_LEADING_SLASH) {
			$type_flags |= self::TYPE_NAMESPACE_LEADING_SLASH;
		}
		$type = self::type($function, $type_flags);
		
		//header
		$header = 'function ';
		if (!empty($modifiers)) {
			$header = implode(' ', $modifiers) . ' ' . $header;
		}
		if (isset($name)) {
			$header .= $name;
		}
		$header .= '(' . implode(', ', $parameters) . ')';
		if ($type !== '') {
			$header .= ": {$type}";
		}
		return $header;
	}
	
	/**
	 * Get body from a given function.
	 * 
	 * The returning body from the given function is its internal PHP code.
	 * 
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @return string
	 * <p>The body from the given function.</p>
	 */
	final public static function body($function): string
	{
		return self::memoize(function ($function): string {
			//initialize
			$reflection = self::reflection($function);
			$filepath = $reflection->getFileName();
			if ($filepath === false) {
				return '';
			}
			
			//lines
			$start_line = $reflection->getStartLine();
			$end_line = $reflection->getEndLine();
			$lines = array_slice(file($filepath, FILE_IGNORE_NEW_LINES), $start_line - 1, $end_line - $start_line + 1);
			if (empty($lines)) {
				return '';
			}
			$lines[] = preg_replace('/\}.+$/', '}', array_pop($lines));
			
			//body
			$body = preg_replace(
				['/^[^{]+(?:\{(.*)\}|;).*$/sm', '/^(?:\s*\n)+|(?:\n\s*)+$/'], ['$1', ''], implode("\n", $lines)
			);
			if (preg_match('/^\s+/', $body, $matches)) {
				$body = preg_replace('/^' . preg_quote($matches[0], '/') . '/m', '', $body);
			}
			
			//return
			return $body;
		});
	}
	
	/**
	 * Get source from a given function.
	 * 
	 * The returning source from the given function is the entirety of its PHP code (both header and body).
	 * 
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::SOURCE_CONSTANTS_VALUES</code> : 
	 * Return the parameters constants values instead of their names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SOURCE_TYPES_SHORT_NAMES</code> : 
	 * Return short names for type and parameters classes and interfaces instead of full namespaced names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SOURCE_NAMESPACES_LEADING_SLASH</code> : 
	 * Return namespaces with the leading slash.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SOURCE_NO_MIXED_TYPE</code> : 
	 * Do not return a mixed type nor parameters with the <code>mixed</code> type keyword.</p>
	 * @return string
	 * <p>The source from the given function.</p>
	 */
	final public static function source($function, int $flags = 0x00): string
	{
		return self::memoize(function ($function, int $flags = 0x00): string {
			//header
			$header_flags = 0x00;
			if ($flags & self::SOURCE_CONSTANTS_VALUES) {
				$header_flags |= self::HEADER_CONSTANTS_VALUES;
			}
			if ($flags & self::SOURCE_TYPES_SHORT_NAMES) {
				$header_flags |= self::HEADER_TYPES_SHORT_NAMES;
			}
			if ($flags & self::SOURCE_NAMESPACES_LEADING_SLASH) {
				$header_flags |= self::HEADER_NAMESPACES_LEADING_SLASH;
			}
			if ($flags & self::SOURCE_NO_MIXED_TYPE) {
				$header_flags |= self::HEADER_NO_MIXED_TYPE;
			}
			$header = self::header($function, $header_flags);
			
			//body
			$body = self::body($function);
			
			//finalize
			if ($body !== '') {
				$body = Text::indentate($body);
				return "{$header}\n{\n{$body}\n}";
			} elseif (in_array('abstract', self::modifiers($function), true)) {
				return "{$header};";
			}
			return "{$header} {}";
		});
	}
	
	/**
	 * Get signature from a given function.
	 * 
	 * The returning signature from the given function is represented only by its parameters and return types.<br>
	 * The expected return format is as follows:<br>
	 * <samp>( parameter1_type , parameter2_type [, optional_parameter3_type [, ... ]] ): return_type</samp>
	 * 
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @return string
	 * <p>The signature from the given function.</p>
	 */
	final public static function signature($function): string
	{
		return self::memoize(function ($function): string {
			//initialize
			$reflection = self::reflection($function);
			
			//parameter types
			$optionals = 0;
			$parameter_types = [];
			foreach ($reflection->getParameters() as $i => $parameter) {
				//type
				$parameter_type = 'mixed';
				$ptype = $parameter->getType();
				if ($ptype !== null) {
					$types_map = array_flip(Type::mnormalize((string)$ptype));
					unset($types_map['null']);
					$types = array_keys($types_map);
					$parameter_type = implode('|', $types);
					if ($ptype->allowsNull() && $parameter_type !== 'mixed') {
						$parameter_type = count($types) > 1 ? "{$parameter_type}|null" : "?{$parameter_type}";
					}
					unset($types_map, $types);
				}
				if ($parameter->isVariadic()) {
					$parameter_type = "...{$parameter_type}";
				}
				if ($parameter->isPassedByReference()) {
					$parameter_type = "&{$parameter_type}";
				}
				$parameter_types[] = " {$parameter_type} ";
				
				//optional
				if ($parameter->isOptional()) {
					$optionals++;
					if ($i > 0) {
						$parameter_types[$i - 1] .= '[';
					} else {
						$parameter_types[$i] = "[{$parameter_types[$i]}";
					}
				}
			}
			$signature = '(' . implode(',', $parameter_types) . str_repeat(']', $optionals) . ')';
			unset($parameter_types, $parameter_type);
			
			//return type
			$return_type = 'mixed';
			$rtype = $reflection->getReturnType();
			if ($rtype !== null) {
				$types_map = array_flip(Type::mnormalize((string)$rtype));
				unset($types_map['null']);
				$types = array_keys($types_map);
				$return_type = implode('|', $types);
				if ($rtype->allowsNull() && $return_type !== 'mixed') {
					$return_type = count($types) > 1 ? "{$return_type}|null" : "?{$return_type}";
				}
				unset($types_map, $types);
			}
			$signature .= ": {$return_type}";
			
			//return
			return $signature;
		});
	}
	
	/**
	 * Check if a given function is compatible with a given template.
	 * 
	 * A function is considered to be compatible with a template whenever, 
	 * upon calling both with the same number and type of parameters, 
	 * they are guaranteed to succeed and to yield the expected return type.<br>
	 * <br>
	 * In other words, for a given function to be compatible with a given template, 
	 * the following conditions must be met:<br>
	 * &nbsp; &#8226; &nbsp; the function must support at least the same number of parameters as the template, 
	 * and any additional function parameters must be optional;<br>
	 * &nbsp; &#8226; &nbsp; for each template optional parameter, 
	 * the corresponding function parameter must be optional as well;<br>
	 * &nbsp; &#8226; &nbsp; each function parameter type must be an invariant or contravariant of each 
	 * corresponding template parameter type;<br>
	 * &nbsp; &#8226; &nbsp; the function return type must be an invariant or covariant of the template return type.
	 * 
	 * @param callable|array|string $function
	 * <p>The function to check.</p>
	 * @param callable|array|string $template
	 * <p>The template callable declaration to check against.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given function is compatible with the given template.</p>
	 */
	final public static function compatible($function, $template): bool
	{
		return self::memoize(function ($function, $template): bool {
			//initialize
			$f_reflection = self::reflection($function);
			$t_reflection = self::reflection($template);
			
			//parameters (check)
			$f_parameters = $f_reflection->getParameters();
			$t_parameters = $t_reflection->getParameters();
			if (count($f_parameters) < count($t_parameters)) {
				return false;
			}
			
			//parameters (contravariance)
			foreach ($f_parameters as $i => $f_parameter) {
				//additional parameter
				if (!isset($t_parameters[$i])) {
					if (!$f_parameter->isOptional()) {
						return false;
					}
					continue;
				}
				
				//parameter
				$t_parameter = $t_parameters[$i];
				if (
					$f_parameter->isPassedByReference() !== $t_parameter->isPassedByReference() || 
					$f_parameter->isVariadic() !== $t_parameter->isVariadic() || 
					(!$f_parameter->isOptional() && $t_parameter->isOptional())
				) {
					return false;
				}
				
				//contravariance
				$f_type_reflection = $f_parameter->getType();
				$t_type_reflection = $t_parameter->getType();
				$f_type = $f_type_reflection !== null ? (string)$f_type_reflection : 'mixed';
				$t_type = $t_type_reflection !== null ? (string)$t_type_reflection : 'mixed';
				if (!Type::contravariant($f_type, $t_type)) {
					return false;
				}
			}
			
			//return type (covariance)
			$f_type_reflection = $f_reflection->getReturnType();
			$t_type_reflection = $t_reflection->getReturnType();
			$f_type = $f_type_reflection !== null ? (string)$f_type_reflection : 'mixed';
			$t_type = $t_type_reflection !== null ? (string)$t_type_reflection : 'mixed';
			if (!Type::covariant($f_type, $t_type)) {
				return false;
			}
			
			//return
			return true;
		});
	}
	
	/**
	 * Assert if a given function is compatible with a given template, with a given name.
	 * 
	 * This assertion is only performed in a debug environment.
	 * 
	 * @param string $name
	 * <p>The name to assert with.</p>
	 * @param callable|array|string $function
	 * <p>The function to assert.</p>
	 * @param callable|array|string $template
	 * <p>The template callable declaration to assert against.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Call\Exceptions\AssertionFailed
	 * @return void|bool
	 * <p>If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then boolean <code>true</code> is returned if the assertion succeeded, 
	 * with the given function being compatible with the given template, or boolean <code>false</code> if otherwise.</p>
	 */
	final public static function assert(string $name, $function, $template, bool $no_throw = false)
	{
		if (System::isDebug() && !self::compatible($function, $template)) {
			if ($no_throw) {
				return false;
			}
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1] ?? [];
			throw new Exceptions\AssertionFailed([
				'name' => $name,
				'function' => $function,
				'template' => $template,
				'source_object_class' => $backtrace['object'] ?? $backtrace['class'] ?? null,
				'source_function_name' => $backtrace['function'] ?? null
			]);
		} elseif ($no_throw) {
			return true;
		}
	}
	
	/**
	 * Get object from a given function.
	 * 
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @return object|null
	 * <p>The object from the given function or <code>null</code> if it has no object bound.</p>
	 */
	final public static function object($function): ?object
	{
		self::validate($function);
		if (is_object($function)) {
			return $function instanceof \Closure ? self::reflection($function)->getClosureThis() : $function;
		} elseif (
			is_array($function) && is_object($function[0]) && !in_array('static', self::modifiers($function), true)
		) {
			return $function[0];
		}
		return null;
	}
	
	/**
	 * Get class from a given function.
	 * 
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @param bool $short [default = false]
	 * <p>Return the short form of the class instead of the full namespaced one.</p>
	 * @return string|null
	 * <p>The class from the given function or <code>null</code> if it has no class bound.</p>
	 */
	final public static function class($function, bool $short = false): ?string
	{
		//initialize
		self::validate($function);
		$reflection_class = null;
		
		//class
		$object = self::object($function);
		if (isset($object)) {
			if (!$short) {
				return get_class($object);
			}
			$reflection_class = new \ReflectionClass($object);
		} elseif ($function instanceof \Closure) {
			$reflection_class = self::reflection($function)->getClosureScopeClass();
		} elseif (is_array($function) && is_string($function[0])) {
			$reflection_class = new \ReflectionClass($function[0]);
		} else {
			$reflection = self::reflection($function);
			if ($reflection instanceof \ReflectionMethod) {
				$reflection_class = $reflection->getDeclaringClass();
			}
		}
		
		//reflection
		if (isset($reflection_class)) {
			return $short ? $reflection_class->getShortName() : $reflection_class->getName();
		}
		
		//return
		return null;
	}
	
	/**
	 * Get extension from a given function.
	 * 
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @return string|null
	 * <p>The extension from the given function or <code>null</code> if it does not belong to any extension.</p>
	 */
	final public static function extension($function): ?string
	{
		$extension = self::reflection($function)->getExtensionName();
		return $extension === false ? null : $extension;
	}
	
	/**
	 * Evaluate a given value as a callable.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param callable|array|string|null $template [default = null]
	 * <p>The template callable declaration to validate the compatibility against.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @param bool $assertive [default = false]
	 * <p>Evaluate in an assertive manner, in other words, perform the heavier validations, 
	 * such as the template compatibility one, only when in a debug environment.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into a callable.</p>
	 */
	final public static function evaluate(
		&$value, $template = null, bool $nullable = false, bool $assertive = false
	): bool
	{
		return self::processCoercion($value, $template, $nullable, $assertive, true);
	}
	
	/**
	 * Coerce a given value into a callable.
	 * 
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param callable|array|string|null $template [default = null]
	 * <p>The template callable declaration to validate the compatibility against.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $assertive [default = false]
	 * <p>Coerce in an assertive manner, in other words, perform the heavier validations, 
	 * such as the template compatibility one, only when in a debug environment.</p>
	 * @throws \Dracodeum\Kit\Utilities\Call\Exceptions\CoercionFailed
	 * @return callable|null
	 * <p>The given value coerced into a callable.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerce(
		$value, $template = null, bool $nullable = false, bool $assertive = false
	): ?callable
	{
		self::processCoercion($value, $template, $nullable, $assertive);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into a callable.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param callable|array|string|null $template [default = null]
	 * <p>The template callable declaration to validate the compatibility against.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $assertive [default = false]
	 * <p>Coerce in an assertive manner, in other words, perform the heavier validations, 
	 * such as the template compatibility one, only when in a debug environment.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Call\Exceptions\CoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into a callable.</p>
	 */
	final public static function processCoercion(
		&$value, $template = null, bool $nullable = false, bool $assertive = false, bool $no_throw = false
	): bool
	{
		//coerce
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		} elseif (!is_callable($value)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
				'error_message' => "Only a callable value is allowed."
			]);
		} elseif (isset($template) && (!$assertive || System::isDebug()) && !self::compatible($value, $template)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_SIGNATURE,
				'error_message' => Text::fill(
					"Only a callable value with a signature compatible with {{template_signature}} is allowed.",
					['template_signature' => self::signature($template)]
				)
			]);
		}
		
		//closure
		try {
			$value = \Closure::fromCallable($value);
		} catch (\Throwable $throwable) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_CALLABLE,
				'error_message' => $throwable->getMessage()
			]);
		}
		
		//return
		return true;
	}
	
	/**
	 * Get previous class from the current stack.
	 * 
	 * @param int $offset [default = 0]
	 * <p>The offset to get from.</p>
	 * @return string|null
	 * <p>The previous class from the current stack 
	 * or <code>null</code> if the previous call in the stack was not called from a class.</p>
	 */
	final public static function stackPreviousClass(int $offset = 0): ?string
	{
		return self::stackPreviousClasses($offset + 1, 1)[0] ?? null;
	}
	
	/**
	 * Get previous classes from the current stack.
	 * 
	 * @param int $offset [default = 0]
	 * <p>The offset to get from.</p>
	 * @param int|null $limit [default = null]
	 * <p>The limit to use on the number of classes to get.<br>
	 * If not set, then no limit is applied.</p>
	 * @return (string|null)[]
	 * <p>The previous classes from the current stack.</p>
	 */
	final public static function stackPreviousClasses(int $offset = 0, ?int $limit = null): array
	{
		//guard
		self::guardParameter('offset', $offset, $offset >= 0, [
			'hint_message' => "Only a value greater than or equal to 0 is allowed."
		]);
		self::guardParameter('limit', $limit, !isset($limit) || $limit > 0, [
			'hint_message' => "Only null or a value greater than 0 is allowed."
		]);
		
		//classes
		$classes = [];
		$limit = isset($limit) ? $limit + $offset + 2 : 0;
		$debug_flags = DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT;
		foreach (array_slice(debug_backtrace($debug_flags, $limit), $offset + 2) as $backtrace) {
			$classes[] = $backtrace['class'] ?? null;
		}
		return $classes;
	}
	
	/**
	 * Get previous object from the current stack.
	 * 
	 * @param int $offset [default = 0]
	 * <p>The offset to get from.</p>
	 * @return object|null
	 * <p>The previous object from the current stack 
	 * or <code>null</code> if the previous call in the stack was not called from an object.</p>
	 */
	final public static function stackPreviousObject(int $offset = 0): ?object
	{
		return self::stackPreviousObjects($offset + 1, 1)[0] ?? null;
	}
	
	/**
	 * Get previous objects from the current stack.
	 * 
	 * @param int $offset [default = 0]
	 * <p>The offset to get from.</p>
	 * @param int|null $limit [default = null]
	 * <p>The limit to use on the number of objects to get.<br>
	 * If not set, then no limit is applied.</p>
	 * @return (object|null)[]
	 * <p>The previous objects from the current stack.</p>
	 */
	final public static function stackPreviousObjects(int $offset = 0, ?int $limit = null): array
	{
		//guard
		self::guardParameter('offset', $offset, $offset >= 0, [
			'hint_message' => "Only a value greater than or equal to 0 is allowed."
		]);
		self::guardParameter('limit', $limit, !isset($limit) || $limit > 0, [
			'hint_message' => "Only null or a value greater than 0 is allowed."
		]);
		
		//objects
		$objects = [];
		$limit = isset($limit) ? $limit + $offset + 2 : 0;
		$debug_flags = DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT;
		foreach (array_slice(debug_backtrace($debug_flags, $limit), $offset + 2) as $backtrace) {
			$objects[] = $backtrace['object'] ?? null;
		}
		return $objects;
	}
	
	/**
	 * Get previous object or class from the current stack.
	 * 
	 * @param int $offset [default = 0]
	 * <p>The offset to get from.</p>
	 * @return object|string|null
	 * <p>The previous object or class from the current stack 
	 * or <code>null</code> if the previous call in the stack was not called from an object nor a class.</p>
	 */
	final public static function stackPreviousObjectClass(int $offset = 0)
	{
		return self::stackPreviousObjectsClasses($offset + 1, 1)[0] ?? null;
	}
	
	/**
	 * Get previous objects and classes from the current stack.
	 * 
	 * @param int $offset [default = 0]
	 * <p>The offset to get from.</p>
	 * @param int|null $limit [default = null]
	 * <p>The limit to use on the number of objects and classes to get.<br>
	 * If not set, then no limit is applied.</p>
	 * @return (object|string|null)[]
	 * <p>The previous objects and classes from the current stack.</p>
	 */
	final public static function stackPreviousObjectsClasses(int $offset = 0, ?int $limit = null): array
	{
		//guard
		self::guardParameter('offset', $offset, $offset >= 0, [
			'hint_message' => "Only a value greater than or equal to 0 is allowed."
		]);
		self::guardParameter('limit', $limit, !isset($limit) || $limit > 0, [
			'hint_message' => "Only null or a value greater than 0 is allowed."
		]);
		
		//objects and classes
		$objects_classes = [];
		$limit = isset($limit) ? $limit + $offset + 2 : 0;
		$debug_flags = DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT;
		foreach (array_slice(debug_backtrace($debug_flags, $limit), $offset + 2) as $backtrace) {
			$objects_classes[] = $backtrace['object'] ?? $backtrace['class'] ?? null;
		}
		return $objects_classes;
	}
	
	/**
	 * Get previous function name from the current stack.
	 * 
	 * If the previous function is anonymous, then <code>null</code> is returned.<br>
	 * If the previous function belongs to a class and the <var>$full</var> parameter is given as
	 * boolean <code>true</code>, then a string in the format <code>Class::name</code> is returned.<br>
	 * In every other case only the name itself is returned.
	 * 
	 * @param bool $full [default = false]
	 * <p>Return the full name, including the class it's declared in.</p>
	 * @param bool $short [default = false]
	 * <p>Return the short form of the class name instead of the full namespaced one.</p>
	 * @param int $offset [default = 0]
	 * <p>The offset to get from.</p>
	 * @return string|null
	 * <p>The previous function name from the current stack
	 * or <code>null</code> if there is no previous call in the stack or if it has no name (anonymous).</p>
	 */
	final public static function stackPreviousName(bool $full = false, bool $short = false, int $offset = 0): ?string
	{
		return self::stackPreviousNames($full, $short, $offset + 1, 1)[0] ?? null;
	}
	
	/**
	 * Get previous function names from the current stack.
	 * 
	 * If a previous function is anonymous, then <code>null</code> is returned.<br>
	 * If a previous function belongs to a class and the <var>$full</var> parameter is given as 
	 * boolean <code>true</code>, then a string in the format <code>Class::name</code> is returned.<br>
	 * In every other case only the name itself is returned.
	 * 
	 * @param bool $full [default = false]
	 * <p>Return the full names, including the classes they're declared in.</p>
	 * @param bool $short [default = false]
	 * <p>Return the short form of the class names instead of the full namespaced ones.</p>
	 * @param int $offset [default = 0]
	 * <p>The offset to get from.</p>
	 * @param int|null $limit [default = null]
	 * <p>The limit to use on the number of names to get.<br>
	 * If not set, then no limit is applied.</p>
	 * @return (string|null)[]
	 * <p>The previous function names from the current stack.</p>
	 */
	final public static function stackPreviousNames(
		bool $full = false, bool $short = false, int $offset = 0, ?int $limit = null
	): array
	{
		//guard
		if ($offset < 0) {
			self::haltParameter('offset', $offset, [
				'hint_message' => "Only a value greater than or equal to 0 is allowed."
			]);
		} elseif ($limit !== null && $limit <= 0) {
			self::haltParameter('limit', $limit, [
				'hint_message' => "Only null or a value greater than 0 is allowed."
			]);
		}
		
		//names
		$names = [];
		$limit = $limit !== null ? $limit + $offset + 2 : 0;
		$debug_flags = DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT;
		foreach (array_slice(debug_backtrace($debug_flags, $limit), $offset + 2) as $backtrace) {
			//name
			$name = $backtrace['function'];
			if (preg_match('/^(?:[\w\\\\]+\\\\)?\{closure\}$/', $name)) {
				$name = null;
			}
			
			//full name
			if ($full && $name !== null) {
				//class
				$class = null;
				if (isset($backtrace['object'])) {
					$class = get_class($backtrace['object']);
				} elseif (isset($backtrace['class'])) {
					$class = $backtrace['class'];
				}
				
				//name
				if ($class !== null) {
					if ($short) {
						$class = Type::shortname($class);
					}
					$name = "{$class}::{$name}";
				}
			}
			
			//finalize
			$names[] = $name;
		}
		return $names;
	}
	
	/**
	 * Halt the current function or method call in the stack.
	 * 
	 * @param \Dracodeum\Kit\Utilities\Call\Options\Halt|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Utilities\Call\Exceptions\Halt\NotAllowed
	 * @return void
	 */
	final public static function halt($options = null): void
	{
		//initialize
		$options = Options\Halt::coerce($options);
		$stack_index = $options->stack_offset + 1;
		$debug_flags = DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT;
		
		//backtrace
		$backtrace = debug_backtrace($debug_flags, $options->stack_offset + 2);
		if (!isset($backtrace[$stack_index]['function'])) {
			throw new Exceptions\Halt\NotAllowed([
				'function_name' => 'halt',
				'object_class' => self::class,
				'hint_message' => "This method may only be called from within a function or method."
			]);
		}
		$backtrace = $backtrace[$stack_index];
		
		//exception
		throw new Exceptions\Halt\NotAllowed([
			'function_name' => $options->function_name ?? $backtrace['function'],
			'object_class' => $options->object_class ?? $backtrace['object'] ?? $backtrace['class'] ?? null
		] + self::getHaltMessages($options));
	}
	
	/**
	 * Guard the current function or method in the stack so it may only be called depending on a given assertion.
	 * 
	 * @param bool $assertion
	 * <p>The assertion to depend on.<br>
	 * If set to boolean <code>false</code>, then an exception is thrown, 
	 * preventing the execution of the current function or method in the stack.</p>
	 * @param \Dracodeum\Kit\Utilities\Call\Options\Halt|array|callable|null $halt_options [default = null]
	 * <p>Additional halt options to use, as an instance, a set of <samp>name => value</samp> pairs or 
	 * a function compatible with the following signature:<br>
	 * <br>
	 * <code>function ()</code><br>
	 * <br>
	 * Return: <code><b>\Dracodeum\Kit\Utilities\Call\Options\Halt|array</b></code><br>
	 * The halt options, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Utilities\Call\Exceptions\Halt\NotAllowed
	 * @return void
	 */
	final public static function guard(bool $assertion, $halt_options = null): void
	{
		if ($assertion) {
			return;
		} elseif (is_callable($halt_options)) {
			self::assert('halt_options', $halt_options, function () {});
			$halt_options = $halt_options();
		}
		$halt_options = Options\Halt::coerce($halt_options, true);
		$halt_options->stack_offset++;
		self::halt($halt_options);
	}
	
	/**
	 * Halt the current function or method call in the stack over a given parameter name and value.
	 * 
	 * @param string $name
	 * <p>The name to use.</p>
	 * @param mixed $value
	 * <p>The value to use.</p>
	 * @param \Dracodeum\Kit\Utilities\Call\Options\HaltParameter|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Utilities\Call\Exceptions\Halt\ParameterNotAllowed
	 * @return void
	 */
	final public static function haltParameter(string $name, $value, $options = null): void
	{
		//initialize
		$options = Options\HaltParameter::coerce($options);
		$stack_index = $options->stack_offset + 1;
		$debug_flags = DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT;
		
		//backtrace
		$backtrace = debug_backtrace($debug_flags, $options->stack_offset + 2);
		if (!isset($backtrace[$stack_index]['function'])) {
			self::halt(['hint_message' => "This method may only be called from within a function or method."]);
		}
		$backtrace = $backtrace[$stack_index];
		
		//exception
		throw new Exceptions\Halt\ParameterNotAllowed([
			'name' => $name,
			'value' => $value,
			'function_name' => $options->function_name ?? $backtrace['function'],
			'object_class' => $options->object_class ?? $backtrace['object'] ?? $backtrace['class'] ?? null
		] + self::getHaltMessages($options));
	}
	
	/**
	 * Guard the current function or method in the stack so it may only be called depending on a given assertion 
	 * relative to a given parameter name and value.
	 * 
	 * @param string $name
	 * <p>The name to use.</p>
	 * @param mixed $value
	 * <p>The value to use.</p>
	 * @param bool $assertion
	 * <p>The assertion to depend on.<br>
	 * If set to boolean <code>false</code>, then an exception is thrown, 
	 * preventing the execution of the current function or method in the stack.</p>
	 * @param \Dracodeum\Kit\Utilities\Call\Options\HaltParameter|array|callable|null $halt_options [default = null]
	 * <p>Additional halt options to use, as an instance, a set of <samp>name => value</samp> pairs or 
	 * a function compatible with the following signature:<br>
	 * <br>
	 * <code>function ()</code><br>
	 * <br>
	 * Return: <code><b>\Dracodeum\Kit\Utilities\Call\Options\HaltParameter|array</b></code><br>
	 * The halt options, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Utilities\Call\Exceptions\Halt\ParameterNotAllowed
	 * @return void
	 */
	final public static function guardParameter(string $name, $value, bool $assertion, $halt_options = null): void
	{
		if ($assertion) {
			return;
		} elseif (is_callable($halt_options)) {
			self::assert('halt_options', $halt_options, function () {});
			$halt_options = $halt_options();
		}
		$halt_options = Options\HaltParameter::coerce($halt_options, true);
		$halt_options->stack_offset++;
		self::haltParameter($name, $value, $halt_options);
	}
	
	/**
	 * Halt the current function or method call in the stack over an internal error.
	 * 
	 * @param \Dracodeum\Kit\Utilities\Call\Options\HaltInternal|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Utilities\Call\Exceptions\Halt\InternalError
	 * @return void
	 */
	final public static function haltInternal($options = null): void
	{
		//initialize
		$options = Options\HaltInternal::coerce($options);
		$stack_index = $options->stack_offset + 1;
		$debug_flags = DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT;
		
		//backtrace
		$backtrace = debug_backtrace($debug_flags, $options->stack_offset + 2);
		if (!isset($backtrace[$stack_index]['function'])) {
			self::halt(['hint_message' => "This method may only be called from within a function or method."]);
		}
		$backtrace = $backtrace[$stack_index];
		
		//exception
		throw new Exceptions\Halt\InternalError([
			'function_name' => $options->function_name ?? $backtrace['function'],
			'object_class' => $options->object_class ?? $backtrace['object'] ?? $backtrace['class'] ?? null
		] + self::getHaltMessages($options));
	}
	
	/**
	 * Guard the current function or method in the stack so it may only continue its internal execution 
	 * depending on a given assertion.
	 * 
	 * @param bool $assertion
	 * <p>The assertion to depend on.<br>
	 * If set to boolean <code>false</code>, then an exception is thrown, 
	 * preventing the current function or method in the stack from continuing its internal execution.</p>
	 * @param \Dracodeum\Kit\Utilities\Call\Options\HaltInternal|array|callable|null $halt_options [default = null]
	 * <p>Additional halt options to use, as an instance, a set of <samp>name => value</samp> pairs or 
	 * a function compatible with the following signature:<br>
	 * <br>
	 * <code>function ()</code><br>
	 * <br>
	 * Return: <code><b>\Dracodeum\Kit\Utilities\Call\Options\HaltInternal|array</b></code><br>
	 * The halt options, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Utilities\Call\Exceptions\Halt\InternalError
	 * @return void
	 */
	final public static function guardInternal(bool $assertion, $halt_options = null): void
	{
		if ($assertion) {
			return;
		} elseif (is_callable($halt_options)) {
			self::assert('halt_options', $halt_options, function () {});
			$halt_options = $halt_options();
		}
		$halt_options = Options\HaltInternal::coerce($halt_options, true);
		$halt_options->stack_offset++;
		self::haltInternal($halt_options);
	}
	
	/**
	 * Halt the current function or method call in the stack over the execution of a given function.
	 * 
	 * @param callable $function
	 * <p>The executed function to use.</p>
	 * @param \Dracodeum\Kit\Utilities\Call\Options\HaltExecution|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Utilities\Call\Exceptions\Halt\ReturnError
	 * @throws \Dracodeum\Kit\Utilities\Call\Exceptions\Halt\ReturnNotAllowed
	 * @return void
	 */
	final public static function haltExecution(callable $function, $options = null): void
	{
		//initialize
		$options = Options\HaltExecution::coerce($options);
		$stack_index = $options->stack_offset + 1;
		$debug_flags = DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT;
		
		//backtrace
		$backtrace = debug_backtrace($debug_flags, $options->stack_offset + 2);
		if (!isset($backtrace[$stack_index]['function'])) {
			self::halt(['hint_message' => "This method may only be called from within a function or method."]);
		}
		$backtrace = $backtrace[$stack_index];
		
		//exception properties
		$exception_properties = [
			'value' => $options->value,
			'exec_function_full_name' => self::name($function, true),
			'function_name' => $options->function_name ?? $backtrace['function'],
			'object_class' => $options->object_class ?? $backtrace['object'] ?? $backtrace['class'] ?? null
		] + self::getHaltMessages($options);
		
		//exception
		$exception = $options->exception;
		throw $exception !== null
			? new Exceptions\Halt\ReturnError($exception_properties + ['error_message' => $exception->getMessage()])
			: new Exceptions\Halt\ReturnNotAllowed($exception_properties);
	}
	
	/**
	 * Guard the current function or method in the stack over the execution of a given function 
	 * with a given set of arguments, with its returning value validated and sanitized by a given callback function.
	 * 
	 * The given callback function is executed with the returned value from the given executed function.<br>
	 * Any exception thrown from either the given function or callback function is internally caught, 
	 * being functionally equivalent to returning boolean <code>false</code> in order to halt execution.
	 * 
	 * @param callable $function
	 * <p>The function to execute.</p>
	 * @param array $arguments
	 * <p>The arguments to execute with.</p>
	 * @param callable $callback
	 * <p>The callback function to validate and sanitize the returning value from the given function to be executed.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (&$value): bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The value to validate and sanitize.<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given value was successfully validated and sanitized.</p>
	 * @param \Dracodeum\Kit\Utilities\Call\Options\GuardExecution|array|callable|null $options [default = null]
	 * <p>Additional options to use, as an instance, a set of <samp>name => value</samp> pairs or a function compatible 
	 * with the following signature:<br>
	 * <br>
	 * <code>function ()</code><br>
	 * <br>
	 * Return: <code><b>\Dracodeum\Kit\Utilities\Call\Options\GuardExecution|array</b></code><br>
	 * The options, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Utilities\Call\Exceptions\Halt\ReturnError
	 * @throws \Dracodeum\Kit\Utilities\Call\Exceptions\Halt\ReturnNotAllowed
	 * @return mixed
	 * <p>The returned value from the given executed function with the given set of arguments, 
	 * validated and sanitized by the given callback function.</p>
	 */
	final public static function guardExecution(
		callable $function, array $arguments, callable $callback, $options = null
	) {
		//assert
		self::assert('callback', $callback, function (&$value): bool {});
		
		//execute
		$value = $exception = null;
		try {
			//function
			$value = $function(...$arguments);
			
			//callback
			$v = $value;
			if ($callback($v)) {
				return $v;
			}
			
		} catch (\Exception $exception) {}
		
		//options
		if (is_callable($options)) {
			self::assert('options', $options, function () {});
			$options = $options();
		}
		$options = Options\GuardExecution::coerce($options);
		
		//halt
		$halt_options = Options\HaltExecution::coerce($options);
		$halt_options->value = $value;
		$halt_options->exception = $exception;
		$halt_options->stack_offset++;
		self::haltExecution($function, $halt_options);
	}
	
	
	
	//Private static methods
	/**
	 * Get halt messages from a given options instance.
	 * 
	 * @param \Dracodeum\Kit\Utilities\Call\Options\Halt $options
	 * <p>The options instance to get from.</p>
	 * @return string[]
	 * <p>The halt messages from the given options instance, as:<br>
	 * <code>[<br>
	 * &nbsp; &nbsp; 'error_message' => &lt;error_message&gt;,<br>
	 * &nbsp; &nbsp; 'hint_message' => &lt;hint_message&gt;<br>
	 * ]</code></p>
	 */
	private static function getHaltMessages(Options\Halt $options): array
	{
		//stringifier
		$stringifier = $options->stringifier;
		if (!isset($stringifier)) {
			$stringifier = function (string $placeholder, $value) use ($options): ?string {
				$string_options = $options->string_options->clone();
				if (!$string_options->loaded('quote_strings')) {
					$string_options->quote_strings = true;
				}
				if (!$string_options->loaded('prepend_type')) {
					$string_options->prepend_type = is_bool($value);
				}
				if (!$string_options->loaded('non_stringable')) {
					$string_options->non_stringable = true;
				}
				return Text::stringify($value, null, $string_options);
			};
		}
		
		//error message
		$error_message = null;
		if (isset($options->error_message)) {
			if (isset($options->error_message_plural) && isset($options->error_message_number)) {
				$error_message = Text::pfill(
					$options->error_message, $options->error_message_plural, $options->error_message_number,
					$options->error_message_number_placeholder, $options->parameters, null, [
						'string_options' => $options->string_options, 'stringifier' => $stringifier
					]
				);
			} elseif (!empty($options->parameters)) {
				$error_message = Text::fill($options->error_message, $options->parameters, null, [
					'string_options' => $options->string_options, 'stringifier' => $stringifier
				]);
			} else {
				$error_message = $options->error_message;
			}
		}
		
		//hint message
		$hint_message = null;
		if (isset($options->hint_message)) {
			if (isset($options->hint_message_plural) && isset($options->hint_message_number)) {
				$hint_message = Text::pfill(
					$options->hint_message, $options->hint_message_plural, $options->hint_message_number,
					$options->hint_message_number_placeholder, $options->parameters, null, [
						'string_options' => $options->string_options, 'stringifier' => $stringifier
					]
				);
			} elseif (!empty($options->parameters)) {
				$hint_message = Text::fill($options->hint_message, $options->parameters, null, [
					'string_options' => $options->string_options, 'stringifier' => $stringifier
				]);
			} else {
				$hint_message = $options->hint_message;
			}
		}
		
		//messages
		$messages = [];
		if ($error_message !== null) {
			$messages['error_message'] = $error_message;
		}
		if ($hint_message !== null) {
			$messages['hint_message'] = $hint_message;
		}
		
		//return
		return $messages;
	}
}
