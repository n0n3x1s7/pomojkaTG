<?

class bot
{
    public $apiUrl;
    private $chatID = -1001254914492;
    public $url = 'https://api.telegram.org/';
    private $token = 'YOUR_TOKEN';

    public function __construct()
    {
        $this->apiUrl = $this->url . 'bot' . $this->token;
    }

    public function apiSendMessage($text)
    {
        $params = ['text' => $text];
        $this->request('sendMessage', $params);
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

        $this->request('sendMessage', $params);
    }

    public function request($method, $params = [], $options = [])
    {
        $netDelay = 1;
        $netTimeout = 10;
        $netConnectTimeout = 5;

        $ch = curl_init();
        $options += [
            'http_method' => 'POST',
            'timeout' => $netTimeout,
        ];
        $params_arr = [];
        foreach ($params as $key => &$val) {
            if (!is_numeric($val) && !is_string($val)) {
                $val = json_encode($val);
            }
            $params_arr[] = urlencode($key) . '=' . urlencode($val);
        }
        $params_arr[] = 'chat_id =' . $this->chatID;
        $query_string = implode('&', $params_arr);

        $url = $this->apiUrl . '/' . $method;

        if ($options['http_method'] === 'POST') {
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
        } else {
            $url .= ($query_string ? '?' . $query_string : '');
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        }

        $connect_timeout = $netConnectTimeout;
        $timeout = $options['timeout'] ?: $netTimeout;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $response_str = curl_exec($ch);
        $errno = curl_errno($ch);
        $http_code = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));

        if ($http_code == 401) {
            throw new Exception('Invalid access token provided');
        } else {
            if ($http_code >= 500 || $errno) {
                sleep($this->$netDelay);
                if ($this->$netDelay < 30) {
                    $this->$netDelay *= 2;
                }
            }
        }

        return json_decode($response_str, true);
    }
}

// при входящем сообщении бота пишем в command
$command = json_decode(file_get_contents('php://input'), true);

// если пустая команда то выход
if (!empty($command['message']['text'])) {
    $ex = new bot();
    $ex->apiCommandResponse($command);
    unset($ex);
}else{
    exit();
}

$ex = new bot();
$message = 'Сбор тварей @FL00D @Gubernateur @Mikhai11 @gitaroshei @Borgyy @Durdom @n0n3x1s7 @aivanova4';
$ex->apiSendMessage($message);