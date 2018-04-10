<?php

namespace Mel\Resources\HttpHelpers;

use Mel\Resources\AbstractResource;

trait GetByIdHelper
{
    use BaseHelper;

    /**
     * Return resource using a id in path
     *
     * @param string|int $id
     *
     * @return AbstractResource Resource instance
     * @throws \Http\Client\Exception
     * @throws \Mel\Exceptions\MelException
     */
    public function getById($id)
    {
        $path = $this->getPath('get-by-id', ['id' => $id]);
        $response = $this->httpClient()->get($path);

        return $this->hydrate($response)->first();
    }
}