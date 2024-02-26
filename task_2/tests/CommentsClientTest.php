<?php

namespace Bodianskii\tests;

use Bodianskii\Task2\CommentsClient;
use Bodianskii\Task2\Exception\ApiException;
use Bodianskii\Task2\Model\Comment;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

#[CoversClass(CommentsClient::class)]
class CommentsClientTest extends TestCase
{
    private MockHandler $mockHandler;
    private static CommentsClient $client;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);

        $httpClient = new Client(['handler' => $handlerStack, RequestOptions::HTTP_ERRORS => false]);
        self::$client = new CommentsClient($httpClient, new HttpFactory(), new HttpFactory());
    }

    public static function requestDataProvider(): array
    {
        return [
            [
                $expected = [new Comment('fake', 'fake', 1)],
                ['data' => array_map(fn (Comment $comment) => $comment->toArray(), $expected)],
                fn () => self::$client->getComments(),
            ],
            [
                $expected = new Comment('fake', 'fake', 1),
                ['data' => $expected->toArray()],
                fn () => self::$client->createComment(new Comment('fake', 'fake')),
            ],
            [
                $expected = new Comment('fake', 'fake', 1),
                ['data' => $expected->toArray()],
                fn () => self::$client->putComment($expected),
            ],
        ];
    }

    #[Test]
    #[DataProvider('requestDataProvider')]
    public function requestSuccessful(array|Comment $expected, array $body, \Closure $action): void
    {
        $this->mockHandler->append(new Response(200, [], json_encode($body)));

        self::assertEquals($expected, $action());
    }

    public static function exceptionDataProvider(): array
    {
        return [
            [
                new Response(),
                [
                    fn () => self::$client->putComment(new Comment('fake', 'fake'))
                ]
            ],
            [
                new Response(400, [], json_encode(['error' => ['message' => 'bad request']])),
                [
                    fn () => self::$client->createComment(new Comment('', '')),
                    fn () => self::$client->putComment(new Comment('', '', 1))
                ]
            ]
        ];
    }

    #[Test]
    #[DataProvider('exceptionDataProvider')]
    public function requestExceptions(ResponseInterface $res, array $actions): void
    {
        foreach ($actions as $action) {
            self::expectException(ApiException::class);

            $this->mockHandler->append($res);

            $action();
        }
    }
}
