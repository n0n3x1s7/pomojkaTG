<?
require_once 'vendor/autoload.php';

use Bots\tgBot;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$ex = new tgBot(getenv('TG_CHAT_ID'), getenv('TG_API_KEY'));
$message = 'Сбор тварей @FL00D @Gubernateur @Mikhai11 @gitaroshei @Borgyy @Durdom @n0n3x1s7 @aivanova4';
$ex->apiSendMessage($message);