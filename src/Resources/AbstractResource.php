<?php

namespace Mel\Resources;

use Mel\Mel;
use Mel\Collection\Collection;
use Psr\Http\Message\ResponseInterface;
use Stringy\Stringy;
use ArrayAccess;

abstract class AbstractResource implements  ResourceInterface, ArrayAccess
{
    /**
     * @var Mel Main container instance
     */
    protected $mel;

    /**
     * List of the attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * AbstractResource constructor.
     *
     * @param Mel $mel
     */
    public function __construct(Mel $mel)
    {
        $this->mel = $mel;

        if (method_exists($this, 'initHelpers')) {
            $this->initHelpers($this->mel);
        }
    }

    /**
     * Create a collection of resources from psr response
     *
     * @param ResponseInterface $response
     *
     * @return Collection
     */
    public function hydrate(ResponseInterface $response)
    {
        $decodedResponse = $this->resolveJsonResponse($response->getBody()->__toString());

        return $this->createCollection(array_map(function ($item) {
            return $this->newInstance($item);
        }, $decodedResponse));
    }

    /**
     * Transform single objects json in array
     *
     * @param $json
     *
     * @return array
     */
    public function resolveJsonResponse($json)
    {
        if (is_object(json_decode($json))) {
            $decodedJson[] = json_decode($json, true);
        } else {
            $decodedJson = json_decode($json, true);
        }

        return $decodedJson;
    }

    /**
     * Create a collection of the objects
     *
     * @param $input
     *
     * @return Collection
     */
    public function createCollection($input)
    {
        return new Collection($input);
    }

    /**
     * Build a resource new instance
     *
     * @param array $attributes
     *
     * @return static
     */
    public function newInstance(array $attributes = [])
    {
        $instance = new static($this->mel);

        if (!empty($attributes)) {
            $instance->fill($attributes);
        }

        return $instance;
    }

    /**
     * Fill the resource with an array of attributes.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function fill($attributes = [])
    {
        foreach ($attributes as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }

        return $this;
    }

    /**
     * Get a attribute value
     *
     * @param $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = isset($this->attributes[$key]) ? $this->attributes[$key] : null;

        if ($this->hasAccessorAttribute($key)) {
            return $this->callAccessorAttribute($key, $value);
        }

        return $value;
    }

    /**
     * Call a custom accessor to get attributes
     *
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    protected function callAccessorAttribute($key, $value)
    {
        $method = 'get' . Stringy::create($key)->upperCamelize() . 'Attribute';
        return $this->{$method}($value);
    }

    /**
     * Test if exist custom acessor to attribute
     *
     * @param string $key Attribute name
     *
     * @return bool
     */
    protected function hasAccessorAttribute($key)
    {
        return method_exists($this, 'get' . Stringy::create($key)->upperCamelize() . 'Attribute');
    }

    /**
     * Set a attribute value
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if ($this->hasMutatorAttribute($key)) {
            return $this->callMutatorAttribute($key, $value);
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Set value of the attribute using a custom mutator
     *
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    protected function callMutatorAttribute($key, $value)
    {
        $method = 'set' . Stringy::create($key)->upperCamelize() . 'Attribute';

        return $this->{$method}($value);
    }

    /**
     * Return if exist a custom mutator used to set attribute
     *
     * @param string $key Attribute name
     *
     * @return bool
     */
    protected function hasMutatorAttribute($key)
    {
        return method_exists($this, 'set' . Stringy::create($key)->upperCamelize() . 'Attribute');
    }

    /**
     * Get all attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the resource
     *
     * @param       $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Determine if the given attribute exists
     *
     * @param $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Unset an attribute on the resource
     *
     * @param $key
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * Get the value for a given offset
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * Set the value for a given offset
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    /**
     * Determine if the given attribute exists
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Unset the value for a given offset
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }
}