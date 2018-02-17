<?php
declare(strict_types=1);
namespace Nekudo\ShinyBlog\Responder;

class HttpResponder extends Responder
{
    /**
     * @var int $statusCode
     */
    protected $statusCode = 200;

    /**
     * @var string $payload The payload to be send to the client.
     */
    protected $payload = '';

    /**
     * @var array $statusMessages List of supported status codes/messages.
     */
    protected $statusMessages = [
        200 => 'OK',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Internal Server Error',
    ];

    /**
     * Responds with a 200 OK header.
     *
     * @param string $payload
     */
    public function found(string $payload = '')
    {
        $this->payload = $payload;
        $this->statusCode = 200;
    }

    /**
     * Responds with a 404 not found header.
     *
     * @param string $payload
     */
    public function notFound(string $payload = '')
    {
        $this->payload = $payload;
        $this->statusCode = 404;
    }

    /**
     * Responds with a 405 method not allowed header.
     *
     * @param string $payload
     */
    public function methodNotAllowed(string $payload = '')
    {
        $this->payload = $payload;
        $this->statusCode = 405;
    }

    /**
     * Responds with a 500 internal server error header.
     *
     * @param string $payload
     */
    public function error(string $payload = '')
    {
        $this->payload = $payload;
        $this->statusCode = 500;
    }

    /**
     * Echos out the response header and content.
     */
    public function respond()
    {
        $statusMessage = $this->statusMessages[$this->statusCode];
        $header = sprintf('HTTP/1.1 %d %s', $this->statusCode, $statusMessage);
        header($header);
        if (!empty($this->payload)) {
            echo $this->payload;
        }
    }
}
