<?php

namespace MelTests\Unit\Exceptions;

use Mel\Exceptions\HttpResponseException;
use Mel\Http\Responses\Response;
use MelTests\TestCase;

class HttpResponseExceptionTest extends TestCase
{
    /**
     * @var HttpResponseException
     */
    protected $httpException;

    protected $responseBody;

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    protected $request;

    protected function setUp()
    {
        parent::setUp();

        $this->responseBody = $this->getJsonFileContent('error-response.json');

        $this->response = $this->createResponse($this->responseBody, 502);

        $this->request = $this->createRequest('GET', '/endpoint');

        $response = new Response($this->response, $this->request);


        $message = sprintf(
            '',
            $this->request->getRequestTarget(),
            $this->request->getMethod(),
            $this->response->getStatusCode(),
            $this->response->getReasonPhrase()
        );


        $this->httpException = new HttpResponseException(
            $message,
            $response
        );

        $this->responseBody = json_decode($this->responseBody);
    }

    public function testGetOriginalRequestAndOriginalResponse()
    {
        $this->assertSame($this->request, $this->httpException->getPsrRequest());
        $this->assertSame($this->response, $this->httpException->getPsrResponse());
    }

    public function testShouldReturnErrorData()
    {
        $this->assertEquals(502, $this->httpException->getCode());
        $this->assertEquals(502, $this->httpException->getErrorStatus());
        $this->assertEquals('error_id', $this->httpException->getErrorId());
        $this->assertEquals(
            [
                "first cause",
                "second cause",
            ],
            $this->httpException->getErrorCauses()
        );
        $this->assertEquals($this->responseBody->message, $this->httpException->getMessage());
    }
}
