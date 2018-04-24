<?php

namespace Mel\Http;

use Mel\Mel;
use Mel\Country;
use Psr\Http\Message\UriInterface;
use Http\Discovery\UriFactoryDiscovery;

class UriGenerator
{
    const API_URI = 'https://api.mercadolibre.com';

    /**
     * Auth url list by country
     *
     * @var array
     */
    protected static $authUri = [
        Country::ARGENTINA   => "https://auth.mercadolibre.com.ar",
        Country::BOLIVIA     => "https://auth.mercadolibre.com.bo",
        Country::BRASIL      => "https://auth.mercadolivre.com.br",
        Country::CHILE       => "https://auth.mercadolibre.cl",
        Country::COLOMBIA    => "https://auth.mercadolibre.com.co",
        Country::COSTA_RICA  => "https://auth.mercadolibre.com.cr",
        Country::DOMINICANA  => "https://auth.mercadolibre.com.do",
        Country::EL_SALVADOR => "https://auth.mercadolibre.com.sv",
        Country::EQUADOR     => "https://auth.mercadolibre.com.ec",
        Country::GUATEMALA   => "https://auth.mercadolibre.com.gt",
        Country::HONDURAS    => "https://auth.mercadolibre.com.hn",
        Country::MEXICO      => "https://auth.mercadolibre.com.mx",
        Country::NICARAGUA   => "https://auth.mercadolibre.com.ni",
        Country::PANAMA      => "https://auth.mercadolibre.com.pa",
        Country::PARAGUAI    => "https://auth.mercadolibre.com.py",
        Country::PERU        => "https://auth.mercadolibre.com.pe",
        Country::PORTUGAL    => "https://auth.mercadolibre.com.pt",
        Country::URUGUAI     => "https://auth.mercadolibre.com.uy",
        Country::VENEZUELA   => "https://auth.mercadolibre.com.ve",
    ];

    /**
     * @var \Http\Message\UriFactory A URI Factory
     */
    protected $uriFactory;

    /**
     * @var Mel
     */
    protected $mel;

    /**
     * UriGenerator constructor.
     *
     * @param Mel $mel
     */
    public function __construct(Mel $mel)
    {
        $this->mel = $mel;
        $this->uriFactory = UriFactoryDiscovery::find();
    }

    /**
     * Creates an PSR-7 URI
     *
     * @param string|UriInterface $uri
     * @param string|null         $path
     * @param array               $params
     *
     * @return UriGenerator|UriInterface|static
     */
    public function createUri($uri, $path = null, array $params = [])
    {
        $uri = $this->uriFactory->createUri($uri);

        if (is_string($path)) {
            $uri = $uri->withPath($path);
        }

        $uri = $this->addQuery($uri, $params);

        return $uri;
    }

    /**
     * Creates an PSR-7 URI using api url string
     *
     * @param string|null $path
     * @param array       $params
     *
     * @return UriGenerator|UriInterface
     */
    public function createApiUri($path = null, array $params = [])
    {
        return $this->createUri(self::API_URI, $path, $params);
    }

    /**
     * Creates an PSR-7 URI using oAuth url string
     *
     * @param string|null $path
     * @param array       $params
     *
     * @return UriGenerator|UriInterface
     */
    public function createOAuthUri($path = null, array $params = [])
    {
        $country = $this->mel->country();

        return $this->createUri(self::$authUri[$country->id()], $path, $params);
    }

    /**
     *  Creates a new URI with a query string based in array of the parameters
     *
     * @param UriInterface $uri
     * @param array        $params
     *
     * @return UriInterface|static
     */
    public function addQuery(UriInterface $uri, array $params)
    {
        $query = http_build_query($params, null, '&');

        $uri = $uri->withQuery($query);

        return $uri;
    }
}