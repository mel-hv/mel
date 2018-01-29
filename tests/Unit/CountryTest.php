<?php

namespace MelTests\Unit;

use Mel\Country;
use PHPUnit\Framework\TestCase;

class CountryTest extends TestCase
{
    public function testCreateCountryInstance()
    {
        $country = new Country(Country::BRASIL);

        $this->assertEquals('MLB', $country->id());
        $this->assertEquals('Brasil', $country->name());
    }

    public function testThrowExceptionIfCountryIsInvalid()
    {
        $this->expectException(\Mel\Exceptions\MelException::class);
        $this->expectExceptionMessage('Use a valid Country. See: https://api.mercadolibre.com/sites/');

        new Country('InvalidCountry');
    }
}
