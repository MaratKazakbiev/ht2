#!/usr/bin/php
<?php
declare(strict_types=1);
use Maratkazakbiev\HtOtus1\MathBracketsResolver;
require DIR . '/../vendor/autoload.php';

$short_options = '';
$short_options .= 'p:';
$long_options = array('port:');
$port = getopt($short_options, $long_options)['port'];

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, '127.0.0.1', (int)$port);
socket_listen($socket, 2);

while (true) {
    $msgsock = socket_accept($socket);

    $msg = "----Напишите свой пример----" . PHP_EOL;
    socket_write($msgsock, $msg);

    $pid = pcntl_fork();
    if ($pid == 0) { // Дочерний процесс
        do {
            $buffer = socket_read($msgsock, 2048, PHP_BINARY_READ);
            $buffer = trim($buffer);

            if ($buffer == 'Выход') {
                break;
            }

            $new = new MathBracketsResolver($buffer);
            $answer = $new->Resolve();

            socket_write($msgsock, $answer);
        } while (true);
        socket_close($msgsock);
        exit(); // Завершаем дочерний процесс
    } else { // Родительский процесс
        // Продолжаем ожидать новых подключений
    }
}

socket_close($socket);
