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
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function fetchBlogPost(): array {
        $response = $this->http->request(
            'POST',
            'http://localhost:8000/blog/post/1', [
                'verify_peer' => false
            ]
        );
        return json_decode($response->getContent(), true);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function fetchBlogPosts(): array {
        $response = $this->http->request(
            'POST',
            'http://localhost:8000/blog/posts', [
                'verify_peer' => false
            ]
        );
        return json_decode($response->getContent(), true);
    }
}