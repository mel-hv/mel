<?php

namespace Mel\Resources\Questions;

use Mel\Resources\AbstractResource;
use Mel\Resources\HttpHelpers\GetByIdHelper;

class Question extends AbstractResource
{
//    use GetByIdHelper;
//
//    protected $paths = [
//        'get-by-id' => 'questions/{id}'
//    ];

    /**
     * Search any question made to user’s items
     *
     * @param $itemId
     *
     * @return \Mel\Collection\\Collection|mixed
     * @throws \Http\Client\Exception
     */
    public function searchByItem($itemId)
    {
        $uri = $this->createUri('/questions/search', [], ['item' => $itemId]);

        $response = $this->httpClient()->get($uri);

        return $this->hydrate($response)->first();
    }
}