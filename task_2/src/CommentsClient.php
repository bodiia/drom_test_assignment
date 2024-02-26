<?php

namespace Bodianskii\Task2;

use Bodianskii\Task2\Exception\ApiException;
use Bodianskii\Task2\Model\Comment;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

final readonly class CommentsClient
{
    private const BASE_URI = 'https://example.com';

    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory
    ) {
    }

    /**
     * @return Comment[]
     * @throws ApiException|ClientExceptionInterface|JsonException
     */
    public function getComments(): array
    {
        $req = $this->requestFactory
            ->createRequest('GET', self::BASE_URI . '/comments')
            ->withHeader('Accept', 'application/json');

        return array_map(
            Comment::fromResponseData(...),
            $this->validate($this->httpClient->sendRequest($req))
        );
    }

    /**
     * @throws ApiException|ClientExceptionInterface|JsonException
     */
    public function createComment(Comment $comment): Comment
    {
        $req = $this->requestFactory
            ->createRequest('GET', self::BASE_URI . '/comments')
            ->withBody($this->streamFactory->createStream($comment->toJson()))
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json');

        return Comment::fromResponseData(
            $this->validate($this->httpClient->sendRequest($req))
        );
    }

    /**
     * @throws ApiException|JsonException|ClientExceptionInterface
     */
    public function putComment(Comment $comment): Comment
    {
        if (! $comment->getId()) {
            throw new ApiException('comment does not have id');
        }

        $req = $this->requestFactory
            ->createRequest('PUT', self::BASE_URI . '/comments/' . $comment->getId())
            ->withBody($this->streamFactory->createStream($comment->toJson()))
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json');

        return Comment::fromResponseData(
            $this->validate($this->httpClient->sendRequest($req))
        );
    }

    /**
     * @throws JsonException|ApiException
     */
    public function validate(ResponseInterface $res): array
    {
        $json = json_decode($res->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if ($res->getStatusCode() !== 200) {
            throw new ApiException(
                sprintf('Response status code: %d, message: %s', $res->getStatusCode(), $json['error']['message'])
            );
        }
        
        return $json['data'];
    }
}
