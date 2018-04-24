<?php

namespace Mel\Resources\Questions;

use Mel\Resources\AbstractResource;

class Question extends AbstractResource
{
    /**
     * Get question using question id
     *
     * @param $id
     *
     * @return \Mel\Resources\Questions\Question
     * @throws \Http\Client\Exception
     */
    public function getById($id)
    {
        $uri = $this->createUri('questions/{id}', ['id' => $id]);

        $response = $this->httpClient()->get($uri);

        return $this->hydrate($response)->first();
    }

    /**
     * Search any question made to user’s items
     *
     * @param $itemId
     *
     * @return \Mel\Resources\Questions\Question
     * @throws \Http\Client\Exception
     */
    public function searchByItem($itemId)
    {
        $uri = $this->createUri('/questions/search', [], ['item' => $itemId]);

        $response = $this->httpClient()->get($uri);

        return $this->hydrate($response)->first();
    }

    /**
     * Create a question
     *
     * @param array $params
     *
     * @return \Mel\Resources\Questions\Question
     * @throws \Http\Client\Exception
     */
    public function create(array $params)
    {
        $uri = $this->createUri('/questions/{id}', ['id' => $params['item_id']]);

        $response = $this->httpClient()->post($uri, [], json_encode($params));

        return $this->hydrate($response)->first();
    }

    /**
     * Answer questions made on your items
     *
     * @param array $params
     *
     * @return \Mel\Resources\Questions\Question
     * @throws \Http\Client\Exception
     */
    public function answer(array $params)
    {
        $uri = $this->createUri('/answers');

        $response = $this->httpClient()->post($uri, [], json_encode($params));

        return $this->hydrate($response)->first();
    }

    /**
     * Customize the return of "from" attributes
     *
     * @param array $value
     *
     * @return object|\stdClass
     */
    public function getFromAttribute(array $value)
    {
        return (object)$value;
    }

    /**
     * Customize the return of "answer" attributes
     *
     * @param array $value
     *
     * @return object|\stdClass
     */
    public function getAnswerAttribute(array $value)
    {
        return (object)$value;
    }
}