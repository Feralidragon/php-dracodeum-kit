# Changes

* Root `Vendor` class removed
  * `Vendor::useAsLibrary()` &#8594; `System::setAsFramework()`
  * `Vendor::isLibrary()` &#8594; `System::isFramework()`
* Renamed `Stringifiable` interface and trait to `Stringable`
  * `non_stringifiable` &#8594; `non_stringable` (property)
* Renamed `NoConstructor` trait to `EmptyConstructor`
* Removed `$recursive` parameter from most methods
  * `Arrayable` interface
    * `toArray(bool $recursive = false)` &#8594; `toArray()`
  * `Cloneable` interface
    * `clone(bool $recursive = false)` &#8594; `clone()`
  * `DebugInfo` interface
    * `getDebugInfo(bool $recursive = false)` &#8594; `getDebugInfo()`
  * `Readonlyable` interface
    * `isReadonly(bool $recursive = false)` &#8594; `isReadonly()`
    * `setAsReadonly(bool $recursive = false)` &#8594; `setAsReadonly()`
    * `addReadonlyCallback(callable $callback)`
  * `Stringable` interface
    * `toString(?TextOptions $text_options = null)` &#8594; `toString(?coercible:options(TextOptions) $text_options = null)`
  * `Data` utility
    * `evaluate(&$value, ?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $recursive = false, bool $nullable = false)` &#8594; `evaluate(&$value, ?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $nullable = false)`
    * `coerce($value, ?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $recursive = false, bool $nullable = false)` &#8594; `coerce($value, ?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $nullable = false)`
    * `processCoercion(&$value, ?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $recursive = false, bool $nullable = false, bool $no_throw = false)` &#8594; `processCoercion(&$value, ?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $nullable = false, bool $no_throw = false)`
  * `Type` utility
    * `clone(object $object, bool $recursive = false)` &#8594; `clone(object $object)`
    * `cloneValue($value, bool $recursive = false)` &#8594; `cloneValue($value)`
    * `readonly(object $object, bool $recursive = false)` &#8594; `readonly(object $object)`
    * `readonlyValue($value, bool $recursive = false, bool $readonlyables_only = false)` &#8594; `readonlyValue($value, bool $readonlyables_only = false)`
    * `setValueAsReadonly($value, bool $recursive = false)` &#8594; `setValueAsReadonly($value)`
    * `setAsReadonly(object $object, bool $recursive = false)` &#8594; `setAsReadonly(object $object)`
    * `persistedValue($value, bool $recursive = false, bool $persistables_only = false)` &#8594; `persistedValue($value, bool $persistables_only = false)`
    * `persistValue($value, bool $recursive = false)` &#8594; `persistValue($value)`
    * `unpersistValue($value, bool $recursive = false)` &#8594; `unpersistValue($value)`
  * `Evaluators` manager and trait
    * `setAsArray(?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $recursive = false, bool $nullable = false)` &#8594; `setAsArray(?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $nullable = false)`
    * `setAsOptions(string $class, ?bool $clone_recursive = null, ?callable $builder = null, bool $nullable = false)` &#8594; `setAsOptions(string $class, bool $clone = false, ?callable $builder = null, bool $nullable = false)`
    * `setAsStructure(string $class, ?bool $clone_recursive = null, ?callable $builder = null, bool $nullable = false)` &#8594; `setAsStructure(string $class, bool $clone = false, ?callable $builder = null, bool $nullable = false)`
    * `setAsDictionary(?Dictionary $template = null, ?bool $clone_recursive = null, bool $nullable = false)` &#8594; `setAsDictionary(?Dictionary $template = null, bool $clone = false, bool $nullable = false)`
    * `setAsVector(?Vector $template = null, ?bool $clone_recursive = null, bool $nullable = false)` &#8594; `setAsVector(?Vector $template = null, bool $clone = false, bool $nullable = false)`
  * `KeyEvaluators` trait
    * `setKeyAsArray(?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $recursive = false, bool $nullable = false)` &#8594; `setKeyAsArray(?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $nullable = false)`
    * `setKeyAsOptions(string $class, ?bool $clone_recursive = null, ?callable $builder = null, bool $nullable = false)` &#8594; `setKeyAsOptions(string $class, bool $clone = false, ?callable $builder = null, bool $nullable = false)`
    * `setKeyAsStructure(string $class, ?bool $clone_recursive = null, ?callable $builder = null, bool $nullable = false)` &#8594; `setKeyAsStructure(string $class, bool $clone = false, ?callable $builder = null, bool $nullable = false)`
    * `setKeyAsDictionary(?Dictionary $template = null, ?bool $clone_recursive = null, bool $nullable = false)` &#8594; `setKeyAsDictionary(?Dictionary $template = null, bool $clone = false, bool $nullable = false)`
    * `setKeyAsVector(?Vector $template = null, ?bool $clone_recursive = null, bool $nullable = false)` &#8594; `setKeyAsVector(?Vector $template = null, bool $clone = false, bool $nullable = false)`
  * `Dictionary` primitive
    * `evaluate(&$value, ?Dictionary $template = null, ?bool $clone_recursive = null, bool $nullable = false)` &#8594; `evaluate(&$value, ?Dictionary $template = null, bool $clone = false, bool $nullable = false)`
    * `coerce($value, ?Dictionary $template = null, ?bool $clone_recursive = null, bool $nullable = false)` &#8594; `coerce($value, ?Dictionary $template = null, bool $clone = false, bool $nullable = false)`
    * `processCoercion(&$value, ?Dictionary $template = null, ?bool $clone_recursive = null, bool $nullable = false, bool $no_throw = false)` &#8594; `processCoercion(&$value, ?Dictionary $template = null, bool $clone = false, bool $nullable = false, bool $no_throw = false)`
  * `Vector` primitive
    * `evaluate(&$value, ?Vector $template = null, ?bool $clone_recursive = null, bool $nullable = false)` &#8594; `evaluate(&$value, ?Vector $template = null, bool $clone = false, bool $nullable = false)`
    * `coerce($value, ?Vector $template = null, ?bool $clone_recursive = null, bool $nullable = false)` &#8594; `coerce($value, ?Vector $template = null, bool $clone = false, bool $nullable = false)`
    * `processCoercion(&$value, ?Vector $template = null, ?bool $clone_recursive = null, bool $nullable = false, bool $no_throw = false)` &#8594; `processCoercion(&$value, ?Vector $template = null, bool $clone = false, bool $nullable = false, bool $no_throw = false)`
  * `Store` component
    * `coerceUid($uid, ?bool $clone_recursive = null)` &#8594; `coerceUid($uid, bool $clone = false)`
  * `Options`
    * `evaluate(&$value, ?bool $clone_recursive = null, ?callable $builder = null, bool $nullable = false)` &#8594; `evaluate(&$value, bool $clone = false, ?callable $builder = null, bool $nullable = false)`
    * `coerce($value, ?bool $clone_recursive = null, ?callable $builder = null, bool $nullable = false)` &#8594; `coerce($value, bool $clone = false, ?callable $builder = null, bool $nullable = false)`
    * `processCoercion(&$value, ?bool $clone_recursive = null, ?callable $builder = null, bool $nullable = false, bool $no_throw = false)` &#8594; `processCoercion(&$value, bool $clone = false, ?callable $builder = null, bool $nullable = false, bool $no_throw = false)`
  * `Structure`
    * `evaluate(&$value, ?bool $clone_recursive = null, ?callable $builder = null, bool $nullable = false)` &#8594; `evaluate(&$value, bool $clone = false, ?callable $builder = null, bool $nullable = false)`
    * `coerce($value, ?bool $clone_recursive = null, ?callable $builder = null, bool $nullable = false)` &#8594; `coerce($value, bool $clone = false, ?callable $builder = null, bool $nullable = false)`
    * `processCoercion(&$value, ?bool $clone_recursive = null, ?callable $builder = null, bool $nullable = false, bool $no_throw = false)` &#8594; `processCoercion(&$value, bool $clone = false, ?callable $builder = null, bool $nullable = false, bool $no_throw = false)`
