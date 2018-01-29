<?php

namespace Mel;

use Mel\Exceptions\MelException;

class Country
{
    // @see https://api.mercadolibre.com/sites/
    const ARGENTINA = 'MLA';
    const BOLIVIA = 'MBO';
    const BRASIL = 'MLB';
    const CHILE = 'MLC';
    const COLOMBIA = 'MCO';
    const COSTA_RICA = 'MCR';
    const DOMINICANA = 'MRD';
    const EL_SALVADOR = 'MSV';
    const EQUADOR = 'MEC';
    const HONDURAS = 'MHN';
    const GUATEMALA = 'MGT';
    const MEXICO = 'MLM';
    const NICARAGUA = 'MNI';
    const PANAMA = 'MPA';
    const PARAGUAI = 'MPY';
    const PERU = 'MPE';
    const PORTUGAL = 'MPT';
    const URUGUAI = 'MLU';
    const VENEZUELA = 'MLV';

    /**
     * Country id, used by Mercado Libre
     * @var string
     */
    protected $country;

    /**
     * Country constructor.
     * @param string $country Country id
     * @throws MelException
     */
    public function __construct($country)
    {
        if (!$this->isValid($country)) {
            throw new MelException(
                sprintf(
                    'Use a valid Country. See: https://api.mercadolibre.com/sites/ or %1$s',
                    __CLASS__
                )
            );
        }

        $this->country = $country;
    }

    /**
     * Return if country is valid option
     *
     * @param $country
     *
     * @return bool
     */
    protected function isValid($country)
    {
        return in_array($country, $this->getCountriesList());
    }

    /**
     * Return list of the registered countries
     * @return array
     */
    public function getCountriesList()
    {
        $reflection = new \ReflectionClass(__CLASS__);
        $countries = $reflection->getConstants();

        return $countries;
    }

    /**
     * Return Country id
     * @return string
     */
    public function id()
    {
        return $this->country;
    }

    /**
     * Return Country name
     * @return string
     */
    public function name()
    {
        $countryName = (string)array_search($this->country, $this->getCountriesList());

        return ucfirst(strtolower($countryName));
    }
}