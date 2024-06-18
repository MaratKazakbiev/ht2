<?php
use Maratkazakbiev\HtOtus1\MathBracketsResolver;
require __DIR__ . '/../vendor/autoload.php';

$port = $_ENV['PORT'];

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, '127.0.0.1', $port);
socket_listen($socket, 2);

while (true) {
    $msgsock = socket_accept($socket);

    $msg = "----Напишите свой пример----" . PHP_EOL;
    socket_write($msgsock, $msg);

    $pid = pcntl_fork();
    if ($pid == 0) { // Дочерний процесс
        do {
            $buffer = $_POST['string'];
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
    }
}