<?php

namespace MelTests\Unit\Fixtures\Responses;

class PredictCategoryResponse extends FooBaseResponse
{
    /**
     * Example in Mercado Libre documentation
     *
     * @var array
     * @see http://developers.mercadolibre.com/list-products/
     */
    const BODY_ARRAY_FORMAT = [

        "id"                     => "MLB268372",
        "name"                   => "256GB",
        "path_from_root"         => [
            [
                "id"                     => "MLB1051",
                "name"                   => "Celulares e Telefones",
                "prediction_probability" => 0.9027805594621691,
            ],
            [
                "id"                     => "MLB1055",
                "name"                   => "Celulares e Smartphones",
                "prediction_probability" => 0.8622545869445731,
            ],
            [
                "id"                     => "MLB39328",
                "name"                   => "iPhone",
                "prediction_probability" => 0.8395478339724001,
            ],
            [
                "id"                     => "MLB268369",
                "name"                   => "iPhone 8",
                "prediction_probability" => 0.37704767397344263,
            ],
            [
                "id"                     => "MLB268372",
                "name"                   => "256GB",
                "prediction_probability" => 0.3770476739734426,
            ],
        ],
        "prediction_probability" => 0.3770476739734426,
        "shipping_modes"         => [
            "me2",
            "me1",
            "not_specified",
            "custom",
        ],

    ];

    protected $statusCode = 200;
}