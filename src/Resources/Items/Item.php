<?php

namespace Mel\Resources\Items;

use Mel\Mel;
use Mel\Http\HttpClient;
use Mel\Resources\Categories;

class Item
{
    const ENDPOINT = '/items/';

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var Categories
     */
    protected $categories;

    /**
     * @var ItemsHelper
     */
    protected $itemsHelper;

    /**
     * @var array Item details
     */
    protected $details;

    /**
     * Item constructor.
     *
     * @param Mel   $mel
     * @param array $details
     */
    public function __construct(Mel $mel, $details = [])
    {
        $this->httpClient = $mel->httpClient();
        $this->categories = $mel->categories();
        $this->itemsHelper = $mel->items();
        $this->details = $details;
    }

    /**
     * Get item data using id
     *
     * @return \Mel\Http\Responses\Response
     */
    public function get()
    {
        $response = $this->httpClient->sendRequest('GET', self::ENDPOINT . $this->details['id']);

        $this->details = $response->getDecodedBody();

        return $response;
    }

    /**
     * Publish Item
     *
     * @return \Mel\Http\Responses\Response
     */
    public function publish()
    {
        $response = $this->httpClient->sendRequest('POST', self::ENDPOINT, $this->details);

        $this->details = $response->getDecodedBody();

        return $response;
    }

    /**
     * Update item details
     *
     * @param array $details
     *
     * @return \Mel\Http\Responses\Response
     */
    public function update(array $details)
    {
        $response = $this->httpClient->sendRequest('PUT', self::ENDPOINT . $this->id, $details);

        $this->details = $response->getDecodedBody();

        return $response;
    }

    /**
     * Get category using title and add to details
     *
     * @param string|null $title
     */
    public function categorize($title = null)
    {
        if (!$title) {
            $title = $this->details['title'];
        }

        $response = $this->categories->predict($title);

        $this->details['category_id'] = $response->getBodyItem('id');
    }

    /**
     * Return if item has valid details
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->itemsHelper->validate($this);
    }

    /**
     * is utilized for reading data from inaccessible members.
     *
     * @param $name string
     *
     * @return mixed
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __get($name)
    {
        $detailsKeys[] = $name;

        $detailsKeys[] = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));;


        foreach ($detailsKeys as $key) {
            if (array_key_exists($key, $this->details)) {
                return $this->details[$key];
            }
        }

        return null;
    }
}