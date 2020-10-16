<?php
namespace App\Application\Responses;

use App\Application\Models\ProxyData;
use DBCO\Application\Responses\Response;

/**
 * Proxy response.
 */
class ProxyResponse extends Response
{
    /**
     * @var ProxyData
     */
    private ProxyData $data;

    /**
     * Constructor.
     *
     * @param ProxyData $data Data.
     */
    public function __construct(ProxyData $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        $headers = [];

        foreach ($this->data->headers as $header) {
            $headers[$header->name] = $header->values;
        }

        return $headers;
    }

    /**
     * @inheritDoc
     */
    public function getBody(): string
    {
        return $this->data->body;
    }
}
