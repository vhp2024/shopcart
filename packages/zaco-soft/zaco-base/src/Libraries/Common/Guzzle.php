<?php

namespace ZacoSoft\ZacoBase\Libraries\Common;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

/**
 * Guzzle lib
 * Dependency Injection Class
 * Retrieve: app('guzzle')
 */
class Guzzle
{
    /**
     * @contructor
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    public function initialize($config = array())
    {
        if (!empty($config)) {
            foreach ($config as $key => $value) {
                $this->{$key} = $value;
            }
        }
        return $this;
    }

    /**
     * Get instance client
     *
     * @return Client
     */
    public function setHeader($key, $value)
    {
        $config = $this->client->getConfig();
        $config['headers'][$key] = $value;
        $this->client = new Client($config);
    }

    /**
     * Get instance client
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Send POST Request
     * @param type $url
     * @param type $data ['form_params'=> [], ['headers' => []]
     * @return type
     */
    public function post($url, $data)
    {
        return $result = $this->client->post($url, $data);
    }

    /**
     * Send GET Request
     * @param type $url
     * @param type $data ['form_params'=> [], ['headers' => []]
     * @return type
     */
    public function get($url, $data = [])
    {
        return $result = $this->client->get($url, $data);
    }

    /**
     * Send Put Request
     * @param type $url
     * @param type $data ['body'=> [], ['headers' => [], 'timeout'=> 5]
     * @return type
     */
    public function put($url, $data)
    {
        return $result = $this->client->request('PUT', $url, [
            RequestOptions::JSON => $data,
        ]);
    }

    /**
     * Used to send an application/x-www-form-urlencoded POST request.
     * REF: http://docs.guzzlephp.org/en/stable/request-options.html#query
     * @param type $method
     * @param type $action
     * @param array $data
     * @return type
     */
    public function query($action, array $data)
    {
        return $result = $this->client->request('GET', $action, [
            RequestOptions::QUERY => $data,
        ]);
    }

    /**
     * Used to send an application/x-www-form-urlencoded POST request.
     * REF: http://docs.guzzlephp.org/en/stable/request-options.html#form-params
     * @param type $method
     * @param type $action
     * @param array $data
     * @return type
     */
    public function formParams($action, array $data)
    {
        return $result = $this->client->request('POST', $action, [
            RequestOptions::FORM_PARAMS => $data,
        ]);
    }

    /**
     * REF: http://docs.guzzlephp.org/en/stable/request-options.html#json
     * @param type $method
     * @param type $action
     * @param type $data
     * @return type
     */
    public function json($action, array $data)
    {
        return $result = $this->client->request('PUT', $action, [
            RequestOptions::JSON => $data,
        ]);
    }

    public function custom($method, $url, $data)
    {
        return $this->client->request($method, $url, $data);
    }
}
