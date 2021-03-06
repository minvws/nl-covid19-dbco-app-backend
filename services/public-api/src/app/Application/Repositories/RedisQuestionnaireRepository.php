<?php
namespace DBCO\PublicAPI\Application\Repositories;

use DBCO\PublicAPI\Application\Models\Header;
use DBCO\PublicAPI\Application\Models\QuestionnaireList;
use Predis\Client as PredisClient;
use RuntimeException;

/**
 * Used for retrieving questionnaires from Redis.
 *
 * @package DBCO\PublicAPI\Application\Repositories
 */
class RedisQuestionnaireRepository implements QuestionnaireRepository
{
    const KEY_QUESTIONNAIRES = 'questionnaires';

    /**
     * Redis client.
     *
     * @var PredisClient
     */
    private PredisClient $client;

    /**
     * Constructor.
     *
     * @param PredisClient $client Redis client.
     */
    public function __construct(PredisClient $client)
    {
        $this->client = $client;
    }

    /**
     * Validate data.
     *
     * @param mixed $data
     */
    private function validateData($data)
    {
        if (!is_object($data)) {
            throw new RuntimeException('Questionnaire data invalid!');
        }

        if (!isset($data->headers) || !is_array($data->headers)) {
            throw new RuntimeException('Questionnaire headers invalid!');
        } else {
            foreach ($data->headers as $header) {
                if (empty($header->name) || !is_string($header->name)) {
                    throw new RuntimeException('Questionnaire headers invalid!');
                } else if (!isset($header->values) || !is_array($header->values)) {
                    throw new RuntimeException('Questionnaire headers invalid!');
                }
            }
        }

        if (empty($data->body) || !is_string($data->body)) {
            throw new RuntimeException('Questionnaire body invalid!');
        }
    }

    /**
     * Returns the questionnaire list.
     *
     * @param string Language.
     *
     * @return QuestionnaireList
     */
    public function getQuestionnaires(string $language): QuestionnaireList
    {
        // TODO: retrieve language specific version

        $json = $this->client->get(self::KEY_QUESTIONNAIRES);
        if ($json === null) {
            throw new RuntimeException('Questionnaires not available in Redis!');
        }

        $data = @json_decode($json);
        $this->validateData($data);

        $headers = [];
        foreach ($data->headers as $rawHeader) {
            $headers[] = new Header($rawHeader->name, $rawHeader->values);
        }

        $body = $data->body;

        return new QuestionnaireList($headers, $body);
    }
}
