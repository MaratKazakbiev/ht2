<?php
declare(strict_types =1 );
use Maratkazakbiev\HtOtus1\MathBracketsResolver;
require __DIR__ . '/../vendor/autoload.php';

$path = readline("Укажите путь к файлу: ");
$exercise = file_get_contents($path);

$new = new MathBracketsResolver($exercise);
$new->Resolve();
