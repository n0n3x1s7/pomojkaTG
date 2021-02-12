<?php


namespace Http;


class curl
{
    public $netDelay = 1;
    public $netTimeout = 10;
    public $netConnectTimeout = 5;
    private $ch;

    public function __construct()
    {
        $this->ch = curl_init();
    }

    public function request($method, $apiUrl, $params = [], $options = [])
    {
        $options += [
            'http_method' => 'POST',
            'timeout' => $this->netTimeout,
        ];
        $params_arr = [];
        foreach ($params as $key => &$val) {
            if (!is_numeric($val) && !is_string($val)) {
                $val = json_encode($val);
            }
            $params_arr[] = urlencode($key) . '=' . urlencode($val);
        }

        $query_string = implode('&', $params_arr);

        $url = $apiUrl . '/' . $method;

        if ($options['http_method'] === 'POST') {
            curl_setopt($this->ch, CURLOPT_SAFE_UPLOAD, false);
            curl_setopt($this->ch, CURLOPT_POST, true);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $query_string);
        } else {
            $url .= ($query_string ? '?' . $query_string : '');
            curl_setopt($this->ch, CURLOPT_HTTPGET, true);
        }

        $connect_timeout = $this->netConnectTimeout;
        $timeout = $options['timeout'] ?: $this->netTimeout;

        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);

        $response_str = curl_exec($this->ch);
        $errno = curl_errno($this->ch);
        $http_code = intval(curl_getinfo($this->ch, CURLINFO_HTTP_CODE));

        if ($http_code == 401) {
            throw new \Exception('Invalid access token provided');
        } else {
            if ($http_code >= 500 || $errno) {
                sleep($this->netDelay);
                if ($this->netDelay < 30) {
                    $this->netDelay *= 2;
                }
            }
        }

        return json_decode($response_str, true);
    }
}