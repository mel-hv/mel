<?php

namespace Mel\Resources\HttpHelpers;

trait GetAllHelper
{
    use BaseHelper;

    /**
     * Get a collection of the resources
     *
     * @return \DusanKasan\Knapsack\Collection
     * @throws \Http\Client\Exception
     * @throws \Mel\Exceptions\MelException
     */
    public function getAll()
    {
        $path = $this->getPath('get-all');
        $response = $this->httpClient()->get($path);

        return $this->hydrate($response);
    }
}