<?php

//args
$type = $argv[1] ?? 'nouns';
[$len1, $len2] = isset($argv[2]) ? (is_numeric($argv[2]) ? [$argv[2],$argv[2]] : explode('-', $argv[2])) : [null, null];
if (isset($len1) && isset($len2)) {
	$len1 = (int)$len1;
	$len2 = (int)$len2;
}
$lite = isset($argv[3]) && $argv[3] === 'lite';

//urls
$urls = [
    'http://source-code-wordle.de/NounsVariables.html',
    'http://source-code-wordle.de/NounsClasses.html'
];
if ($type === 'verbs') {
    $urls = [
        'http://source-code-wordle.de/VerbsAll.html',
        'http://source-code-wordle.de/VerbsBoolean.html'
    ];
}

//get words
$words = [];
foreach ($urls as $url) {
    preg_match_all("/javascript:show\('(\w+)'\)/", file_get_contents($url), $m);
    $words = array_merge($words, $m[1]);
}

//json urls
if (!$lite) {
	$json_urls = [
		'https://anvaka.github.io/common-words/static/data/js/index.json',
		'https://anvaka.github.io/common-words/static/data/jsx/index.json',
		'https://anvaka.github.io/common-words/static/data/css/index.json',
		'https://anvaka.github.io/common-words/static/data/html/index.json',
		'https://anvaka.github.io/common-words/static/data/java/index.json',
		'https://anvaka.github.io/common-words/static/data/py/index.json',
		'https://anvaka.github.io/common-words/static/data/lua/index.json',
		'https://anvaka.github.io/common-words/static/data/php/index.json',
		'https://anvaka.github.io/common-words/static/data/rb/index.json',
		'https://anvaka.github.io/common-words/static/data/cpp/index.json',
		'https://anvaka.github.io/common-words/static/data/pl/index.json',
		'https://anvaka.github.io/common-words/static/data/cs/index.json',
		'https://anvaka.github.io/common-words/static/data/scala/index.json',
		'https://anvaka.github.io/common-words/static/data/go/index.json',
		'https://anvaka.github.io/common-words/static/data/sql/index.json',
		'https://anvaka.github.io/common-words/static/data/rs/index.json',
		'https://anvaka.github.io/common-words/static/data/lisp/index.json',
		'https://anvaka.github.io/common-words/static/data/clj/index.json',
		'https://anvaka.github.io/common-words/static/data/kt/index.json',
		'https://anvaka.github.io/common-words/static/data/swift/index.json',
		'https://anvaka.github.io/common-words/static/data/hs/index.json',
		'https://anvaka.github.io/common-words/static/data/ex/index.json',
		'https://anvaka.github.io/common-words/static/data/objc/index.json',
		'https://anvaka.github.io/common-words/static/data/objc/index.json',
	];
	foreach ($json_urls as $json_url) {
		$words = array_merge($words, array_column(json_decode(file_get_contents($json_url), true), 'text'));
	}
}

//merge
$words = array_values(array_unique(array_map('strtolower', $words)));

//sort
usort($words, function ($a, $b) {
    $la = strlen($a);
    $lb = strlen($b);
    return $la === $lb ? strcmp($a, $b) : $la - $lb;
});


//print words
$row_size = 8;
$row = 0;
echo "\n\n";
foreach ($words as $word) {
	$len = strlen($word);
	if (preg_match('/^[a-z]+$/', $word) && (!isset($len1) || !isset($len2) || ($len >= $len1 && $len <= $len2))) {
        echo "  $word  ";
        $row++;
        if ($row >= $row_size) {
            echo "\n";
            $row = 0;
        }
    }
}
echo "\n\n";