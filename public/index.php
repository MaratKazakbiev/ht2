#!/usr/bin/php
<?php
declare(strict_types=1);
use Maratkazakbiev\HtOtus1\MathBracketsResolver;
use Symfony\Component\Yaml\Yaml;

require __DIR__ . '/../vendor/autoload.php';

// Читаем конфигурационный файл
$config = Yaml::parse(file_get_contents('config.yaml'));
$port = $config['port'];

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, '127.0.0.1', (int)$port);
socket_listen($socket, 2);

// Устанавливаем слушатель сигнала SIGHUP
pcntl_signal(SIGHUP, 'reloadConfig');

// Хранилище для принятых подключений
$connections = [];

while (true) {
    $msgsock = socket_accept($socket);
    $connections[] = $msgsock; // Сохраняем принятое подключение

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
    }
}

// Функция-обработчик сигнала SIGHUP
function reloadConfig($signal) {
    global $socket, $connections, $port;

    // Закрываем старый сокет
    socket_close($socket);

    // Перечитываем конфигурационный файл
    $config = Yaml::parse(file_get_contents('config.yaml'));
    $newPort = $config['port'];

    // Создаем новый сокет
    $newSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_bind($newSocket, '127.0.0.1', (int)$newPort);
    socket_listen($newSocket, 2);

    // Перенаправляем все принятые подключения на новый порт
    foreach ($connections as $connection) {
        socket_close($connection);
        $newConnection = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($newConnection, '127.0.0.1', (int)$newPort);
        $connections[] = $newConnection;
    }

    // Обновляем глобальные переменные
    $socket = $newSocket;
    $port = $newPort;
}