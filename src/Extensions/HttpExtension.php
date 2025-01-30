<?php

namespace IsaEken\BrickEngine\Extensions;

use GuzzleHttp\Client;
use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Contracts\ExtensionInterface;

class HttpExtension implements ExtensionInterface
{
    private Client $client;
    public function __construct(public BrickEngine $engine)
    {
        $this->client = new Client();
    }

    public function register(): void
    {
        $this->engine->context->functions['fetch'] = [$this, 'fetch'];

        $this->engine->context->namespaces['http'] = [
            'get' => function ($url, $params = []) {
                $response = $this->client->request('GET', $url, $params);
                return $response->getBody()->getContents();
            },
            'post' => function ($url, $params = []) {
                $response = $this->client->request('POST', $url, $params);
                return $response->getBody()->getContents();
            },
            'put' => function ($url, $params = []) {
                $response = $this->client->request('PUT', $url, $params);
                return $response->getBody()->getContents();
            },
            'delete' => function ($url, $params = []) {
                $response = $this->client->request('DELETE', $url, $params);
                return $response->getBody()->getContents();
            },
        ];
    }

    /** @deprecated  */
    public function fetch(string $url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
