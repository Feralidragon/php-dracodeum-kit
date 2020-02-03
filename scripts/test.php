<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dracodeum\Kit\Root\System;
use Dracodeum\Kit\Factories\Component as FComponent;

System::setEnvironment('development');

class Entity implements Dracodeum\Kit\Interfaces\Propertiesable, Dracodeum\Kit\Interfaces\DebugInfo, 
Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor, Dracodeum\Kit\Interfaces\Readonlyable
{
	use Dracodeum\Kit\Traits\DebugInfo;
	use Dracodeum\Kit\Traits\DebugInfo\ReadonlyPropertiesDumpProcessor;
	use Dracodeum\Kit\Traits\Properties;
	use Dracodeum\Kit\Traits\Readonly;
	
	final public function __construct(array $properties = [], bool $persisted = false)
	{
		$this->initializeProperties(\Closure::fromCallable([$this, 'loadProperties']), $properties, 'rw', $persisted);
		$this->addReadonlyCallback(function (bool $recursive): void {
			$this->setPropertiesAsReadonly();
		});
	}
	
	protected function loadProperties(): void
	{
		$this->addProperty('id')->setAsInteger()->setAsAutoImmutable();
		$this->addProperty('name')->setAsString(true)->setAsImmutable();
		$this->addProperty('value')->setAsFloat()->setDefaultValue(0.0)->setAsImmutable();
		$this->addProperty('reference')->setAsString()->setAsAutomatic()->setDefaultValue('ABC');
		$this->addProperty('enabled')->setAsBoolean()->setDefaultValue(false);
	}
}

$a = new Entity([
	'id' => 123,
	'name' => 'asd',
	'value' => 7.25,
	'reference' => 'fooBAR',
	'enabled' => 1
], true);
//$a->id = 1555;
//unset($a->reference);

var_dump($a);die();


$text_options = [
	'info_scope' => 0
];


$value = 'hasd';

$input = FComponent::input('string');
$input->addConstraint('values', [['asd', 'ggg', 12345678]]);



echo "INPUT:\n";
var_dump($input->getName());
var_dump($input->setValue($value, true));
var_dump($input->isInitialized() ? $input->getValue() : null);
var_dump($input->isInitialized() ? $input->getValueString($text_options) : null);
var_dump($input->getLabel($text_options));
var_dump($input->getDescription($text_options));
var_dump($input->getMessage($text_options));

$schema = $input->getSchema();
var_dump([
	'name' => $schema->name,
	'nullable' => $schema->nullable,
	'data' => $schema->data,
	'modifiers_count' => count($schema->modifiers)
]);

echo "\n\nINPUT ERROR: ";
var_dump($input->getErrorMessage($text_options));
echo "\n\n";


foreach ($input->getModifiers() as $i => $modifier) {
	echo "INPUT MODIFIER [{$i}]:\n";
	var_dump($modifier->getName());
	var_dump($modifier->getLabel($text_options));
	var_dump($modifier->getMessage($text_options));
	var_dump($modifier->getString($text_options));
	
	$schema = $modifier->getSchema();
	var_dump([
		'name' => $schema->name,
		'type' => $schema->type,
		'subtype' => $schema->subtype,
		'data' => $schema->data
	]);
	echo "\n\n";
}
