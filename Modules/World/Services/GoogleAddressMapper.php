<?php

namespace Modules\World\Services;

class GoogleAddressMapper
{
    public function map(array $address): array
    {
        return [
            'country_id'  => null,
            'province_id' => null,
            'county_id'   => null,
        ];
    }
}