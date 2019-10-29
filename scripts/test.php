<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dracodeum\Kit\Root\System;
use Dracodeum\Kit\Factories\Component as FComponent;

System::setEnvironment('development');


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
