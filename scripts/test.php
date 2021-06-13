<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dracodeum\Kit\Root\System;
use Dracodeum\Kit\Factories\Component as FComponent;

System::setAsFramework();
System::setEnvironment('development');

$t = Dracodeum\Kit\Components\Type::build('array', [
	'mutators' => [
		'non_empty'
	]
]);
$v = [123];
$e = $t->process($v);

var_dump($v);
echo 
	"\n\n", 
	(string)$e?->getText(), 
	"\n\n\n",
	$e?->getText()->toString(['info_level' => 1]),
	"\n\n\n",
	$e?->getText()->toString(['info_level' => 2]),
	"\n\n"
;
die();


class Foo extends Dracodeum\Kit\Entity
{
	public static function getName(): string
	{
		return 'foo';
	}
	
	protected function loadProperties(): void
	{
		$this->addProperty('id')->setAsInteger();
		$this->addProperty('name')->setAsString()->setAsLazy();
	}
	
	protected static function produceStore()
	{
		return 'mem';
	}
	
	protected static function getIdPropertyName(): ?string
	{
		return 'id';
	}
	
	protected function processPreUpdate(array $old_values, array &$new_values): void
	{
		var_dump($old_values, $new_values);
	}
}

class Bar extends Dracodeum\Kit\Entity
{
	public static function getName(): string
	{
		return 'bar';
	}
	
	protected function loadProperties(): void
	{
		$this->addProperty('id')->setAsString(true);
		$this->addProperty('foo_id')->setAsAutoImmutable()->setDefaultGetter(function () {
			return $this->foo->id;
		});
		$this->addProperty('foo')->setAsEntity(Foo::class)->setAsLazy();
		$this->addProperty('name')->setAsString()->setAsLazy();
	}
	
	protected static function produceStore()
	{
		return 'mem';
	}
	
	protected static function getIdPropertyName(): ?string
	{
		return 'id';
	}
	
	protected function processPreInsert(array &$values): void
	{
		var_dump($values);
		$values['foo'] = $values['foo']->id;
	}
	
	protected function processPreUpdate(array $old_values, array &$new_values): void
	{
		var_dump($old_values, $new_values);
		if (isset($new_values['foo'])) {
			$old_values['foo'] = $old_values['foo']->id;
			$new_values['foo'] = $new_values['foo']->id;
		}
	}
	
	protected static function getBaseScope(): ?string
	{
		return '{{foo_id}}';
	}
}


$f = Foo::build(['id' => 123, 'name' => 11111111]);
$f->persist();

$f = Foo::build(['id' => 7200, 'name' => 'Yajpll']);
$f->persist();

$f = Foo::load(123);

$f->name = 6873222;
$f->persist();

$f = Foo::load(123);

//var_dump($f, $f->getId(), $f->getPersistentUid());

echo "\n\n";

$b = Bar::build(['id' => 'XHS77', 'foo' => 123, 'name' => 9e3]);

$b->persist();

$b = Bar::load('XHS77', ['foo_id' => $f->id]);
$b->foo = 123;
$b->persist();
var_dump($b);

die();





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
