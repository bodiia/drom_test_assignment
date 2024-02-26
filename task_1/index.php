#!/usr/bin/env php
<?php

if (! isset($argv[1])) {
    echo "provide directory as argument" . PHP_EOL;
    exit(1);
}

$root = $argv[1];

if (! file_exists($root)) {
    echo "directory dont exists" . PHP_EOL;
    exit(1);
}

if (! is_dir($root)) {
    echo "this is not a directory" . PHP_EOL;
    exit(1);
}

$filename = 'count';
$iterator = function (string $parent) use (&$iterator, $filename): \Generator {
    foreach (array_diff_key(scandir($parent), ['.', '..']) as $node) {
        $path =  $parent . DIRECTORY_SEPARATOR . $node;

        if (is_dir($path)) {
            yield from $iterator($path);
        }

        if (is_file($path) && basename($path) === $filename) {
            yield fgets($f = fopen($path, 'r'));
            fclose($f);
        }
    }
};

$total = 0;
foreach ($iterator($root) as $value) {
    if (filter_var($value, FILTER_VALIDATE_INT)) {
        $total += $value;
    }
}

echo $total . PHP_EOL;
