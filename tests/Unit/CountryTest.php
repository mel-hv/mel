<?php

namespace MelTests\Unit;

use Mel\Country;
use MelTests\TestCase;

class CountryTest extends TestCase
{
    public function testCreateCountryInstance()
    {
        $country = new Country(Country::BRASIL);

        $this->assertEquals('MLB', $country->id());
        $this->assertEquals('Brasil', $country->name());
    }

    /**
     * @expectedException \Mel\Exceptions\MelException
     * @expectedExceptionMessage Use a valid Country. See: https://api.mercadolibre.com/sites/
     */
    public function testThrowExceptionIfCountryIsInvalid()
    {
        new Country('InvalidCountry');
    }
}
