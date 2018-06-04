<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities;

use Feralygon\Kit\{
	Traits,
	Utility
};
use Feralygon\Kit\Utilities\Call\{
	Options,
	Exceptions
};
use Feralygon\Kit\Root\System;

/**
 * This utility implements a set of methods used to get information from existing PHP functions, 
 * methods and callables.
 * 
 * @since 1.0.0
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
	
	/** Parameters classes short names (flag). */
	public const PARAMETERS_CLASSES_SHORT_NAMES = 0x02;
	
	/** Parameters classes leading slash (flag). */
	public const PARAMETERS_CLASSES_LEADING_SLASH = 0x04;
	
	/** Parameters no mixed type (flag). */
	public const PARAMETERS_NO_MIXED_TYPE = 0x08;
	
	/** Type no mixed (flag). */
	public const TYPE_NO_MIXED = 0x01;
	
	/** Type class short name (flag). */
	public const TYPE_CLASS_SHORT_NAME = 0x02;
	
	/** Type class leading slash (flag). */
	public const TYPE_CLASS_LEADING_SLASH = 0x04;
	
	/** Header constants values (flag). */
	public const HEADER_CONSTANTS_VALUES = 0x01;
	
	/** Header classes short names (flag). */
	public const HEADER_CLASSES_SHORT_NAMES = 0x02;
	
	/** Header classes leading slash (flag). */
	public const HEADER_CLASSES_LEADING_SLASH = 0x04;
	
	/** Header no mixed type (flag). */
	public const HEADER_NO_MIXED_TYPE = 0x08;
	
	/** Source constants values (flag). */
	public const SOURCE_CONSTANTS_VALUES = 0x01;
	
	/** Source classes short names (flag). */
	public const SOURCE_CLASSES_SHORT_NAMES = 0x02;
	
	/** Source classes leading slash (flag). */
	public const SOURCE_CLASSES_LEADING_SLASH = 0x04;
	
	/** Source no mixed type (flag). */
	public const SOURCE_NO_MIXED_TYPE = 0x08;
	
	
	
	//Final public static methods
	/**
	 * Validate a given function reference.
	 * 
	 * @since 1.0.0
	 * @param callable|array|string $function
	 * <p>The function to validate.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Utilities\Call\Exceptions\InvalidFunction
	 * @return void|bool
	 * <p>If <var>$no_throw</var> is set to <code>true</code>, 
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
				is_object($function[0]) || 
				(is_string($function[0]) && (class_exists($function[0]) || interface_exists($function[0])))
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
				(count($f) === 2 && (class_exists($f[0]) || interface_exists($f[0])) && method_exists($f[0], $f[1]))
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
		throw new Exceptions\InvalidFunction(['function' => $function]);
	}
	
	/**
	 * Get a new reflection instance from a given function.
	 * 
	 * The returning reflection instance depends on the type of function given.<br>
	 * In the case of a class or instance method, 
	 * a reflection instance of the <code>ReflectionMethod</code> class is returned.<br>
	 * If, however, it's any other type of function, such as a global, local or anonymous function, 
	 * a reflection instance of the <code>ReflectionFunction</code> class is returned instead.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/class.reflectionfunction.php
	 * @see https://php.net/manual/en/class.reflectionmethod.php
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Utilities\Call\Exceptions\InvalidFunction
	 * @return \ReflectionFunction|\ReflectionMethod|null
	 * <p>A new reflection instance from the given function.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then <code>null</code> is returned if it could not be got.</p>
	 */
	final public static function reflection($function, bool $no_throw = false) : ?\ReflectionFunctionAbstract
	{
		//validate
		if ($no_throw && !self::validate($function, true)) {
			return null;
		} else {
			self::validate($function);
		}
		
		//method
		if (is_array($function)) {
			$function = (is_object($function[0]) ? get_class($function[0]) : $function[0]) . '::' . $function[1];
		}
		if (is_string($function)) {
			$function = str_replace('->', '::', $function);
			if (strpos($function, '::') !== false) {
				return new \ReflectionMethod($function);
			}
		}
		
		//function
		return new \ReflectionFunction($function);
	}
	
	/**
	 * Calculate hash from a given function.
	 * 
	 * The returning hash of the given function is calculated mostly based on its declaration location, 
	 * but also its signature, therefore it's unique to the function signature and scope, thus resulting in different 
	 * hashes even if two functions share exactly the same signature and source provided that they are declared 
	 * in different locations.
	 * 
	 * @since 1.0.0
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
	final public static function hash($function, string $algorithm = 'SHA1', bool $raw = false) : string
	{
		return self::memoize(function () use ($function, $algorithm, $raw) : string {
			$reflection = self::reflection($function);
			$export = Type::isA($reflection, \ReflectionMethod::class)
				? $reflection::export($reflection->getDeclaringClass()->getName(), $reflection->getName(), true)
				: $reflection::export($reflection->getClosure(), true);
			return hash($algorithm, $export, $raw);
		});
	}
	
	/**
	 * Get modifiers from a given function.
	 * 
	 * @since 1.0.0
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @return string[]
	 * <p>The modifiers from the given function.</p>
	 */
	final public static function modifiers($function) : array
	{
		//reflection
		$reflection = self::reflection($function);
		if (!Type::isA($reflection, \ReflectionMethod::class)) {
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
	 * If the given function belongs to a class and the <var>$full</var> parameter is passed as <code>true</code>, 
	 * then a string in the format <code>Class::name</code> is returned.<br>
	 * In every other case only the name itself is returned.
	 * 
	 * @since 1.0.0
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @param bool $full [default = false]
	 * <p>Return the full name, including the class it's declared in.</p>
	 * @param bool $short [default = false]
	 * <p>Return the short form of the class name instead of the full namespaced one.</p>
	 * @return string|null
	 * <p>The name from the given function or <code>null</code> if the function has no name (anonymous).</p>
	 */
	final public static function name($function, bool $full = false, bool $short = false) : ?string
	{
		//optimization
		if (!$full) {
			if (is_array($function)) {
				return $function[1];
			} elseif (is_string($function)) {
				$parts = explode('::', $function);
				return end($parts);
			}
		} elseif (!$short && is_callable($function, false, $name)) {
			return $name === 'Closure::__invoke' ? null : $name;
		}
		
		//reflection
		$reflection = self::reflection($function);
		$name = $reflection->getName();
		if ($name === '' || $name === '{closure}') {
			return null;
		} elseif ($full && Type::isA($reflection, \ReflectionMethod::class)) {
			$reflection_class = $reflection->getDeclaringClass();
			$name = ($short ? $reflection_class->getShortName() : $reflection_class->getName()) . "::{$name}";
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
	 * @since 1.0.0
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::PARAMETERS_CONSTANTS_VALUES</code> : 
	 * Return the constants values instead of their names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::PARAMETERS_CLASSES_SHORT_NAMES</code> : 
	 * Return short names for classes instead of full namespaced names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::PARAMETERS_CLASSES_LEADING_SLASH</code> : 
	 * Return classes with the leading slash.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::PARAMETERS_NO_MIXED_TYPE</code> : 
	 * Do not return mixed parameters with the <code>mixed</code> type keyword.</p>
	 * @return string[]
	 * <p>The parameters from the given function.</p>
	 */
	final public static function parameters($function, int $flags = 0x00) : array
	{
		return self::memoize(function () use ($function, $flags) : array {
			//initialize
			$reflection = self::reflection($function);
			$is_method = Type::isA($reflection, \ReflectionMethod::class);
			
			//parameters
			$parameters = [];
			foreach ($reflection->getParameters() as $parameter) {
				//type
				$type = ($flags & self::PARAMETERS_NO_MIXED_TYPE) ? '' : 'mixed';
				$ptype = $parameter->getType();
				if (isset($ptype)) {
					$type = (string)$ptype;
					if ($flags & (self::PARAMETERS_CLASSES_SHORT_NAMES | self::PARAMETERS_CLASSES_LEADING_SLASH)) {
						$ptype_class = $parameter->getClass();
						if (isset($ptype_class)) {
							if ($flags & self::PARAMETERS_CLASSES_SHORT_NAMES) {
								$type = $ptype_class->getShortName();
							} elseif ($flags & self::PARAMETERS_CLASSES_LEADING_SLASH) {
								$type = "\\{$type}";
							}
						}
					}
					if ($ptype->allowsNull()) {
						$type = "?{$type}";
					}
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
							if ($is_method) {
								$constant = str_replace(
									'self::', "{$parameter->getDeclaringClass()->getName()}::", $constant
								);
							}
							if ($flags & self::PARAMETERS_CLASSES_SHORT_NAMES) {
								[$constant_class, $constant_name] = explode('::', $constant);
								$constant = Type::basename($constant_class) . '::' . $constant_name;
							} elseif ($flags & self::PARAMETERS_CLASSES_LEADING_SLASH) {
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
	 * @since 1.0.0
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TYPE_NO_MIXED</code> : 
	 * Do not return the <code>mixed</code> type keyword.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TYPE_CLASS_SHORT_NAME</code> : 
	 * Return a short name for a class instead of a full namespaced name.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TYPE_CLASS_LEADING_SLASH</code> : 
	 * Return class with the leading slash.</p>
	 * @return string
	 * <p>The type from the given function.</p>
	 */
	final public static function type($function, int $flags = 0x00) : string
	{
		$type = ($flags & self::TYPE_NO_MIXED) ? '' : 'mixed';
		$rtype = self::reflection($function)->getReturnType();
		if (isset($rtype)) {
			$type = (string)$rtype;
			if (($flags & (self::TYPE_CLASS_SHORT_NAME | self::TYPE_CLASS_LEADING_SLASH)) && class_exists($type)) {
				if ($flags & self::TYPE_CLASS_SHORT_NAME) {
					$type = Type::basename($type);
				} elseif ($flags & self::TYPE_CLASS_LEADING_SLASH) {
					$type = "\\{$type}";
				}
			}
			if ($rtype->allowsNull()) {
				$type = "?{$type}";
			}
		}
		return $type;
	}
	
	/**
	 * Get header from a given function.
	 * 
	 * The returning header from the given function is represented by its modifiers, name, parameters and type.<br>
	 * The expected return format is 
	 * <samp>modifier function name(type1 param1, type2 param2 = value2, ...) : type</samp> .
	 * 
	 * @since 1.0.0
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::HEADER_CONSTANTS_VALUES</code> : 
	 * Return the constants values instead of their names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::HEADER_CLASSES_SHORT_NAMES</code> : 
	 * Return short names for classes instead of full namespaced names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::HEADER_CLASSES_LEADING_SLASH</code> : 
	 * Return classes with the leading slash.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::HEADER_NO_MIXED_TYPE</code> : 
	 * Do not return a mixed type nor parameters with the <code>mixed</code> type keyword.</p>
	 * @return string
	 * <p>The header from the given function.</p>
	 */
	final public static function header($function, int $flags = 0x00) : string
	{
		//initialize
		$modifiers = self::modifiers($function);
		$name = self::name($function);
		
		//initialize parameters
		$parameters_flags = 0x00;
		if ($flags & self::HEADER_CONSTANTS_VALUES) {
			$parameters_flags |= self::PARAMETERS_CONSTANTS_VALUES;
		}
		if ($flags & self::HEADER_CLASSES_SHORT_NAMES) {
			$parameters_flags |= self::PARAMETERS_CLASSES_SHORT_NAMES;
		}
		if ($flags & self::HEADER_CLASSES_LEADING_SLASH) {
			$parameters_flags |= self::PARAMETERS_CLASSES_LEADING_SLASH;
		}
		if ($flags & self::HEADER_NO_MIXED_TYPE) {
			$parameters_flags |= self::PARAMETERS_NO_MIXED_TYPE;
		}
		$parameters = self::parameters($function, $parameters_flags);
		
		//initialize type
		$type_flags = 0x00;
		if ($flags & self::HEADER_NO_MIXED_TYPE) {
			$type_flags |= self::TYPE_NO_MIXED;
		}
		if ($flags & self::HEADER_CLASSES_SHORT_NAMES) {
			$type_flags |= self::TYPE_CLASS_SHORT_NAME;
		}
		if ($flags & self::HEADER_CLASSES_LEADING_SLASH) {
			$type_flags |= self::TYPE_CLASS_LEADING_SLASH;
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
			$header .= " : {$type}";
		}
		return $header;
	}
	
	/**
	 * Get body from a given function.
	 * 
	 * The returning body from the given function is its PHP code.
	 * 
	 * @since 1.0.0
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @return string
	 * <p>The body from the given function.</p>
	 */
	final public static function body($function) : string
	{
		return self::memoize(function () use ($function) : string {
			//initialize
			$reflection = self::reflection($function);
			$filepath = $reflection->getFileName();
			if ($filepath === false) {
				return '';
			}
			
			//body
			$start_line = $reflection->getStartLine();
			$end_line = $reflection->getEndLine();
			$body = implode("\n", array_slice(
				file($filepath, FILE_IGNORE_NEW_LINES), $start_line - 1, $end_line - $start_line + 1
			));
			$body = preg_replace(['/^[^{]+\{(.*)\}.*$/sm', '/^(?:\s*\n)+|(?:\n\s*)+$/'], ['$1', ''], $body);
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
	 * @since 1.0.0
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SOURCE_CONSTANTS_VALUES</code> : 
	 * Return the parameters constants values instead of their names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SOURCE_CLASSES_SHORT_NAMES</code> : 
	 * Return short names for type and parameters classes instead of full namespaced names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SOURCE_CLASSES_LEADING_SLASH</code> : 
	 * Return classes with the leading slash.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SOURCE_NO_MIXED_TYPE</code> : 
	 * Do not return a mixed type nor parameters with the <code>mixed</code> type keyword.</p>
	 * @return string
	 * <p>The source from the given function.</p>
	 */
	final public static function source($function, int $flags = 0x00) : string
	{
		return self::memoize(function () use ($function, $flags) : string {
			//initialize header
			$header_flags = 0x00;
			if ($flags & self::SOURCE_CONSTANTS_VALUES) {
				$header_flags |= self::HEADER_CONSTANTS_VALUES;
			}
			if ($flags & self::SOURCE_CLASSES_SHORT_NAMES) {
				$header_flags |= self::HEADER_CLASSES_SHORT_NAMES;
			}
			if ($flags & self::SOURCE_CLASSES_LEADING_SLASH) {
				$header_flags |= self::HEADER_CLASSES_LEADING_SLASH;
			}
			if ($flags & self::SOURCE_NO_MIXED_TYPE) {
				$header_flags |= self::HEADER_NO_MIXED_TYPE;
			}
			$header = self::header($function, $header_flags);
			
			//initialize body
			$body = Text::indentate(self::body($function));
			
			//return
			return "{$header}\n{\n{$body}\n}";
		});
	}
	
	/**
	 * Get signature from a given function.
	 * 
	 * The returning signature from the given function is represented only by its parameters and return types.<br>
	 * The expected return format is as follows:<br>
	 * <samp>( parameter1_type , parameter2_type [, optional_parameter3_type [, ... ]] ) : return_type</samp>
	 * 
	 * @since 1.0.0
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @return string
	 * <p>The signature from the given function.</p>
	 */
	final public static function signature($function) : string
	{
		return self::memoize(function () use ($function) : string {
			//initialize
			$reflection = self::reflection($function);
			
			//parameter types
			$optionals = 0;
			$parameter_types = [];
			foreach ($reflection->getParameters() as $i => $parameter) {
				//type
				$parameter_type = 'mixed';
				$ptype = $parameter->getType();
				if (isset($ptype)) {
					$parameter_type = (string)$ptype;
					if ($ptype->allowsNull()) {
						$parameter_type = "?{$parameter_type}";
					}
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
			if (isset($rtype)) {
				$return_type = (string)$rtype;
				if ($rtype->allowsNull()) {
					$return_type = "?{$return_type}";
				}
			}
			$signature .= " : {$return_type}";
			
			//return
			return $signature;
		});
	}
	
	/**
	 * Assert if a given function is compatible with a given template, with a given name.
	 * 
	 * This assertion is only performed in a debug environment.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to assert with.</p>
	 * @param callable|array|string $function
	 * <p>The function to assert.</p>
	 * @param callable|array|string $template
	 * <p>The template callable declaration to assert against.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Utilities\Call\Exceptions\AssertionFailed
	 * @return void|bool
	 * <p>If <var>$no_throw</var> is set to <code>true</code>, 
	 * then boolean <code>true</code> is returned if the assertion succeeded, 
	 * with the given function being compatible with the given template, or boolean <code>false</code> if otherwise.</p>
	 */
	final public static function assert(string $name, $function, $template, bool $no_throw = false)
	{
		if (System::isDebug() && !self::isCompatible($function, $template)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\AssertionFailed([
				'name' => $name,
				'function' => $function,
				'template' => $template,
				'object_class' => self::stackPreviousObjectClass()
			]);
		} elseif ($no_throw) {
			return true;
		}
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
	 * @since 1.0.0
	 * @param callable|array|string $function
	 * <p>The function to check.</p>
	 * @param callable|array|string $template
	 * <p>The template callable declaration to check against.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given function is compatible with the given template.</p>
	 */
	final public static function isCompatible($function, $template) : bool
	{
		return self::memoize(function () use ($function, $template) : bool {
			//initialize
			$f_reflection = self::reflection($function);
			$t_reflection = self::reflection($template);
			
			//parameters contravariance
			$f_parameters = $f_reflection->getParameters();
			$t_parameters = $t_reflection->getParameters();
			if (count($f_parameters) < count($t_parameters)) {
				return false;
			}
			foreach ($f_parameters as $i => $f_parameter) {
				//additional function parameter
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
				
				//parameter type
				$f_type_reflection = $f_parameter->getType();
				$t_type_reflection = $t_parameter->getType();
				$f_type = isset($f_type_reflection) ? (string)$f_type_reflection : 'mixed';
				$t_type = isset($t_type_reflection) ? (string)$t_type_reflection : 'mixed';
				if ($f_type !== 'mixed') {
					$f_type_allows_null = isset($f_type_reflection) ? $f_type_reflection->allowsNull() : true;
					$t_type_allows_null = isset($t_type_reflection) ? $t_type_reflection->allowsNull() : true;
					if (
						(!$f_type_allows_null && $t_type_allows_null) || (
							$f_type !== $t_type && 
							(!class_exists($f_type) || !class_exists($t_type) || !Type::isA($t_type, $f_type)) && 
							($f_type !== 'object' || !class_exists($t_type))
						)
					) {
						return false;
					}
				}
			}
			
			//return type covariance
			$f_type_reflection = $f_reflection->getReturnType();
			$t_type_reflection = $t_reflection->getReturnType();
			$f_type = isset($f_type_reflection) ? (string)$f_type_reflection : 'mixed';
			$t_type = isset($t_type_reflection) ? (string)$t_type_reflection : 'mixed';
			if ($f_type === 'void' && $t_type !== 'void') {
				return false;
			} elseif ($t_type !== 'void' && $t_type !== 'mixed') {
				$f_type_allows_null = isset($f_type_reflection) ? $f_type_reflection->allowsNull() : true;
				$t_type_allows_null = isset($t_type_reflection) ? $t_type_reflection->allowsNull() : true;
				if (
					($f_type_allows_null && !$t_type_allows_null) || (
						$f_type !== $t_type && 
						(!class_exists($f_type) || !class_exists($t_type) || !Type::isA($f_type, $t_type)) && 
						(!class_exists($f_type) || $t_type !== 'object')
					)
				) {
					return false;
				}
			}
			
			//return
			return true;
		});
	}
	
	/**
	 * Get extension from a given function.
	 * 
	 * @since 1.0.0
	 * @param callable|array|string $function
	 * <p>The function to get from.</p>
	 * @return string|null
	 * <p>The extension from the given function 
	 * or <code>null</code> if the function does not belong to any extension.</p>
	 */
	final public static function extension($function) : ?string
	{
		$extension = self::reflection($function)->getExtensionName();
		return $extension === false ? null : $extension;
	}
	
	/**
	 * Evaluate a given value as a callable.
	 * 
	 * @since 1.0.0
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
	) : bool
	{
		try {
			$value = self::coerce($value, $template, $nullable, $assertive);
		} catch (Exceptions\CoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into a callable.
	 * 
	 * @since 1.0.0
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param callable|array|string|null $template [default = null]
	 * <p>The template callable declaration to validate the compatibility against.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $assertive [default = false]
	 * <p>Coerce in an assertive manner, in other words, perform the heavier validations, 
	 * such as the template compatibility one, only when in a debug environment.</p>
	 * @throws \Feralygon\Kit\Utilities\Call\Exceptions\CoercionFailed
	 * @return callable|null
	 * <p>The given value coerced into a callable.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerce(
		$value, $template = null, bool $nullable = false, bool $assertive = false
	) : ?callable
	{
		if (!isset($value)) {
			if ($nullable) {
				return null;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		} elseif (!is_callable($value)) {
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
				'error_message' => "Only a callable value is allowed."
			]);
		} elseif (isset($template) && (!$assertive || System::isDebug()) && !self::isCompatible($value, $template)) {
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_SIGNATURE,
				'error_message' => Text::fill(
					"Only a callable value with a signature compatible with {{template_signature}} is allowed.",
					['template_signature' => self::signature($template)]
				)
			]);
		}
		return \Closure::fromCallable($value);
	}
	
	/**
	 * Get previous class from the current stack.
	 * 
	 * @since 1.0.0
	 * @param int $offset [default = 0]
	 * <p>The offset to get from.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @return string|null
	 * <p>The previous class from the current stack 
	 * or <code>null</code> if the previous call in the stack was not called from a class.</p>
	 */
	final public static function stackPreviousClass(int $offset = 0) : ?string
	{
		self::guardParameter('offset', $offset, $offset >= 0, [
			'hint_message' => "Only a value greater than or equal to 0 is allowed."
		]);
		$debug_flags = DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT;
		return debug_backtrace($debug_flags, $offset + 3)[$offset + 2]['class'] ?? null;
	}
	
	/**
	 * Get previous classes from the current stack.
	 * 
	 * @since 1.0.0
	 * @param int $offset [default = 0]
	 * <p>The offset to get from.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @param int|null $limit [default = null]
	 * <p>The limit to use on the number of classes to get.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than <code>0</code>.</p>
	 * @return string[]|null[]
	 * <p>The previous classes from the current stack.</p>
	 */
	final public static function stackPreviousClasses(int $offset = 0, ?int $limit = null) : array
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
	 * @since 1.0.0
	 * @param int $offset [default = 0]
	 * <p>The offset to get from.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @return object|null
	 * <p>The previous object from the current stack 
	 * or <code>null</code> if the previous call in the stack was not called from an object.</p>
	 */
	final public static function stackPreviousObject(int $offset = 0) : ?object
	{
		self::guardParameter('offset', $offset, $offset >= 0, [
			'hint_message' => "Only a value greater than or equal to 0 is allowed."
		]);
		$debug_flags = DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT;
		return debug_backtrace($debug_flags, $offset + 3)[$offset + 2]['object'] ?? null;
	}
	
	/**
	 * Get previous objects from the current stack.
	 * 
	 * @since 1.0.0
	 * @param int $offset [default = 0]
	 * <p>The offset to get from.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @param int|null $limit [default = null]
	 * <p>The limit to use on the number of objects to get.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than <code>0</code>.</p>
	 * @return object[]|null[]
	 * <p>The previous objects from the current stack.</p>
	 */
	final public static function stackPreviousObjects(int $offset = 0, ?int $limit = null) : array
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
	 * @since 1.0.0
	 * @param int $offset [default = 0]
	 * <p>The offset to get from.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @return object|string|null
	 * <p>The previous object or class from the current stack 
	 * or <code>null</code> if the previous call in the stack was not called from an object nor a class.</p>
	 */
	final public static function stackPreviousObjectClass(int $offset = 0)
	{
		self::guardParameter('offset', $offset, $offset >= 0, [
			'hint_message' => "Only a value greater than or equal to 0 is allowed."
		]);
		$debug_flags = DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT;
		$backtrace = debug_backtrace($debug_flags, $offset + 3)[$offset + 2] ?? null;
		return isset($backtrace) ? ($backtrace['object'] ?? $backtrace['class'] ?? null) : null;
	}
	
	/**
	 * Get previous objects and classes from the current stack.
	 * 
	 * @since 1.0.0
	 * @param int $offset [default = 0]
	 * <p>The offset to get from.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @param int|null $limit [default = null]
	 * <p>The limit to use on the number of objects and classes to get.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than <code>0</code>.</p>
	 * @return object[]|string[]|null[]
	 * <p>The previous objects and classes from the current stack.</p>
	 */
	final public static function stackPreviousObjectsClasses(int $offset = 0, ?int $limit = null) : array
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
	 * Guard the current function or method in the stack from being called depending on a given assertion.
	 * 
	 * @since 1.0.0
	 * @param bool $assertion
	 * <p>The assertion to depend on.<br>
	 * If set to boolean <code>false</code>, then an exception is thrown, 
	 * preventing the execution of the current function or method in the stack.</p>
	 * @param \Feralygon\Kit\Utilities\Call\Options\Guard|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Utilities\Call\Exceptions\NotAllowed
	 * @return void
	 */
	final public static function guard(bool $assertion, $options = null) : void
	{
		//initialize
		if ($assertion) {
			return;
		}
		$options = Options\Guard::coerce($options);
		
		//backtrace
		$stack_index = $options->stack_offset + 1;
		$debug_flags = DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT;
		$backtrace = debug_backtrace($debug_flags, $options->stack_offset + 2);
		if (!isset($backtrace[$stack_index]['function'])) {
			throw new Exceptions\NotAllowed([
				'function_name' => 'guard',
				'object_class' => self::class,
				'hint_message' => "This method may only be called from within a function or method."
			]);
		}
		$backtrace = $backtrace[$stack_index];
		
		//exception
		throw new Exceptions\NotAllowed([
			'function_name' => $options->function_name ?? $backtrace['function'],
			'object_class' => $options->object_class ?? $backtrace['object'] ?? $backtrace['class'] ?? null
		] + self::getGuardMessages($options));
	}
	
	/**
	 * Guard the current function or method in the stack from being called depending on a given assertion 
	 * relative a given parameter name and value.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to use.</p>
	 * @param mixed $value
	 * <p>The value to use.</p>
	 * @param bool $assertion
	 * <p>The assertion to depend on.<br>
	 * If set to boolean <code>false</code>, then an exception is thrown, 
	 * preventing the execution of the current function or method in the stack.</p>
	 * @param \Feralygon\Kit\Utilities\Call\Options\GuardParameter|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Utilities\Call\Exceptions\ParameterNotAllowed
	 * @return void
	 */
	final public static function guardParameter(string $name, $value, bool $assertion, $options = null) : void
	{
		//initialize
		if ($assertion) {
			return;
		}
		$options = Options\GuardParameter::coerce($options);
		
		//backtrace
		$stack_index = $options->stack_offset + 1;
		$debug_flags = DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT;
		$backtrace = debug_backtrace($debug_flags, $options->stack_offset + 2);
		self::guard(isset($backtrace[$stack_index]['function']), [
			'hint_message' => "This method may only be called from within a function or method."
		]);
		$backtrace = $backtrace[$stack_index];
		
		//exception
		throw new Exceptions\ParameterNotAllowed([
			'name' => $name,
			'value' => $value,
			'function_name' => $options->function_name ?? $backtrace['function'],
			'object_class' => $options->object_class ?? $backtrace['object'] ?? $backtrace['class'] ?? null
		] + self::getGuardMessages($options));
	}
	
	/**
	 * Guard the current function or method in the stack from continuing its execution depending on a given assertion.
	 * 
	 * @since 1.0.0
	 * @param bool $assertion
	 * <p>The assertion to depend on.<br>
	 * If set to boolean <code>false</code>, then an exception is thrown, 
	 * preventing the current function or method in the stack from continuing to execute.</p>
	 * @param \Feralygon\Kit\Utilities\Call\Options\GuardInternal|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Utilities\Call\Exceptions\InternalError
	 * @return void
	 */
	final public static function guardInternal(bool $assertion, $options = null) : void
	{
		//initialize
		if ($assertion) {
			return;
		}
		$options = Options\GuardInternal::coerce($options);
		
		//backtrace
		$stack_index = $options->stack_offset + 1;
		$debug_flags = DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT;
		$backtrace = debug_backtrace($debug_flags, $options->stack_offset + 2);
		self::guard(isset($backtrace[$stack_index]['function']), [
			'hint_message' => "This method may only be called from within a function or method."
		]);
		$backtrace = $backtrace[$stack_index];
		
		//exception
		throw new Exceptions\InternalError([
			'function_name' => $options->function_name ?? $backtrace['function'],
			'object_class' => $options->object_class ?? $backtrace['object'] ?? $backtrace['class'] ?? null
		] + self::getGuardMessages($options));
	}
	
	
	
	//Final private static methods
	/**
	 * Get guard messages from a given options instance.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Utilities\Call\Options\Guard $options
	 * <p>The options instance to get from.</p>
	 * @return string[]
	 * <p>The guard messages from the given options instance, as:<br>
	 * <code>[<br>
	 * &nbsp; &nbsp; 'error_message' => &lt;error_message&gt;,<br>
	 * &nbsp; &nbsp; 'hint_message' => &lt;hint_message&gt;<br>
	 * ]</code></p>
	 */
	final private static function getGuardMessages(Options\Guard $options) : array
	{
		//stringifier
		$stringifier = $options->stringifier;
		if (!isset($stringifier)) {
			$stringifier = function (string $placeholder, $value) use ($options) : ?string {
				$string_options = $options->string_options->clone();
				$string_options->quote_strings = true;
				$string_options->prepend_type = is_bool($value);
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
		
		//return
		return ['error_message' => $error_message, 'hint_message' => $hint_message];
	}
}
