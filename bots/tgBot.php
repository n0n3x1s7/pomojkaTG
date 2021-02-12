<?php

namespace Bots;

use Http\curl;

class tgBot
{
    public $apiUrl;
    private $chatID;
    public $url = 'https://api.telegram.org/';
    private $curl;

    public function __construct($chatID, $token)
    {
        $this->apiUrl = $this->url . 'bot' . $token;
        $this->chatID = $chatID;
        $this->curl = new curl();
    }

    public function apiSendMessage($text)
    {
        $params = [
            'chat_id' => $this->chatID,
            'text' => $text
        ];
        $this->curl->request('sendMessage', $this->apiUrl, $params);
    }

    public function apiCommandResponse($command)
    {
        //если ответ с кнопки обрезаем лишнее
        $command = $command['callback_query'] ? $command['callback_query'] : $command['message'];

        // преобразуем любые команды в нижний регистр utf-8
        $message = mb_strtolower(($command['text'] ? $command['text'] : $command['data']), 'utf-8');

        switch ($message)
        {
            case 'показать кто в доте':  case 'показать кто в дискорде':
            $params = ['text' => 'Будет доступно в следующей версии'];
            break;
            case 'го': case 'сбор!':
            $params = ['text' => $message['from']['username'] . 'собирает тварей! @FL00D @Gubernateur @Mikhai11 @gitaroshei @Borgyy @Durdom @n0n3x1s7 @aivanova4'];
            break;
            case 'бот':
                $params = [
                    'text' => 'Че хочешь тварь?',
                    'reply_markup' => [
                        'resize_keyboard' => true,
                        'keyboard' => [
                            [
                                ['text' => 'Показать кто в доте'],
                                ['text' => 'Показать кто в дискорде'],
                                ['text' => 'Сбор!']
                            ]
                        ]
                    ]
                ];
                break;
            default:
                break;
        }

        $this->curl->request('sendMessage', $this->apiUrl, $params);
    }
}