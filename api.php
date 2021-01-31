<?

class bot
{
    public $apiUrl;
    public $chatID = -1001254914492;
    public $url = 'https://api.telegram.org/';
    public $token = '1398922633:AAFe3PdHf_q1ErbybUaEZ_sFfcy1d-1L6d0';

    public function __construct()
    {
        $this->apiUrl = $this->url . 'bot' . $this->token;
    }

    public function apiSendMessage($text, $chatID)
    {
        $params = [
            'chat_id' => $chatID,
            'text' => $text
        ];
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

$ex = new bot();
$message = 'Сбор тварей @FL00D @Gubernateur @Mikhai11 @gitaroshei @Borgyy @Durdom @n0n3x1s7 @aivanova4';
$ex->apiSendMessage($message, $ex->chatID);