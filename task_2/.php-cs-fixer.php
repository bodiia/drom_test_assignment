<?php

$finder = (new PhpCsFixer\Finder())->in([
    __DIR__ . '/src',
    __DIR__ . '/tests',
]);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12:risky' => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true);
