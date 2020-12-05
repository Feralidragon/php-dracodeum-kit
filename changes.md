# Changes

* Root `Vendor` class removed
  * `Vendor::useAsLibrary()` &#8594; `System::setAsFramework()`
  * `Vendor::isLibrary()` &#8594; `System::isFramework()`
* Renamed `Stringifiable` interface and trait to `Stringable`
  * `non_stringifiable` &#8594; `non_stringable` (property)
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
  * `KeyEvaluators` trait
    * `setKeyAsArray(?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $recursive = false, bool $nullable = false)` &#8594; `setKeyAsArray(?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $nullable = false)`
