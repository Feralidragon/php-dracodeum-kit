<?php

//args
$type = $argv[1] ?? 'nouns';
[$len1, $len2] = isset($argv[2]) ? (is_numeric($argv[2]) ? [$argv[2],$argv[2]] : explode('-', $argv[2])) : [null, null];
if (isset($len1) && isset($len2)) {
	$len1 = (int)$len1;
	$len2 = (int)$len2;
}
$lite = isset($argv[3]) && $argv[3] === 'lite';

//files (lite)
$files = [];
if ($type === 'verbs') {
	$files[] = 'source-code-wordle.de_NounsClasses.txt';
	$files[] = 'source-code-wordle.de_NounsVariables.txt';
} else {
	$files[] = 'source-code-wordle.de_VerbsAll.txt';
	$files[] = 'source-code-wordle.de_VerbsBoolean.txt';
}

//files (full)
if (!$lite) {
	$files[] = 'anvaka.github.io_common-words_clj.txt';
	$files[] = 'anvaka.github.io_common-words_cpp.txt';
	$files[] = 'anvaka.github.io_common-words_cs.txt';
	$files[] = 'anvaka.github.io_common-words_css.txt';
	$files[] = 'anvaka.github.io_common-words_ex.txt';
	$files[] = 'anvaka.github.io_common-words_go.txt';
	$files[] = 'anvaka.github.io_common-words_hs.txt';
	$files[] = 'anvaka.github.io_common-words_html.txt';
	$files[] = 'anvaka.github.io_common-words_java.txt';
	$files[] = 'anvaka.github.io_common-words_js.txt';
	$files[] = 'anvaka.github.io_common-words_jsx.txt';
	$files[] = 'anvaka.github.io_common-words_kt.txt';
	$files[] = 'anvaka.github.io_common-words_lisp.txt';
	$files[] = 'anvaka.github.io_common-words_lua.txt';
	$files[] = 'anvaka.github.io_common-words_objc.txt';
	$files[] = 'anvaka.github.io_common-words_php.txt';
	$files[] = 'anvaka.github.io_common-words_pl.txt';
	$files[] = 'anvaka.github.io_common-words_py.txt';
	$files[] = 'anvaka.github.io_common-words_rb.txt';
	$files[] = 'anvaka.github.io_common-words_rs.txt';
	$files[] = 'anvaka.github.io_common-words_scala.txt';
	$files[] = 'anvaka.github.io_common-words_sql.txt';
	$files[] = 'anvaka.github.io_common-words_swift.txt';
}

//words (load)
$words = [];
foreach ($files as $file) {
	$words = array_merge($words, explode("\n", file_get_contents(__DIR__ . "/words/{$file}")));
}

//words (prepare)
$words = array_values(array_unique(array_map('strtolower', $words)));
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