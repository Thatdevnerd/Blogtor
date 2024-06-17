<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BlogPostFetchService
{
    private HttpClientInterface $http;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->http = $httpClient;
    }

    /**
     * @param bool $all
     * @param int|null $id
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function fetchPost(bool $all = false, int $id = null): array {
        if (!$all) {
            if (is_null($id)) { return ['error' => 'id is null']; }
            $response = $this->http->request('GET', 'http://localhost/blog/posts/' . $id);
        } else {
            $response = $this->http->request('GET', 'http://localhost/blog/posts');
        }
        return json_decode($response->getContent(), true);
    }
}