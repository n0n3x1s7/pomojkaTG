<?php

require_once 'vendor/autoload.php';

use Bots\tgBot;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// при входящем сообщении бота пишем в command
$command = json_decode(file_get_contents('php://input'), true);

// если пустая команда то выход
if (!empty($command['message']['text'])) {
    $bot = new tgBot(getenv('TG_CHAT_ID'), getenv('TG_API_KEY'));
    $bot->apiCommandResponse($command);
}else{
    exit();
}