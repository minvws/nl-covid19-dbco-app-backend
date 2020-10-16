<?php
namespace App\Application\Responses;

/**
 * Case task list response.
 */
class CaseResponse extends ProxyResponse
{
    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
       $headers = parent::getHeaders();
       $headers['Content-Type'] = 'application/json';
       return $headers;
    }
}
