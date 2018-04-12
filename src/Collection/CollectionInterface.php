<?php

namespace Mel\Collection;

interface CollectionInterface
{
    /**
     * Returns first item of this collection
     *
     * @return mixed|Collection
     */
    public function first();
}