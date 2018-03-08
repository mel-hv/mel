<?php

namespace Mel\Resources\Items;

interface ItemValidatorInterface
{
    /**
     * Return if Item is filled correctly
     *
     * @param Item $item
     *
     * @return bool
     */
    public function validate(Item $item);
}