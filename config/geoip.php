<?php

return [
    'databases' => [
        'city' => env('GEOIP_CITY_DB', storage_path('geoip/GeoLite2-City.mmdb')),
        'country' => env('GEOIP_COUNTRY_DB', storage_path('geoip/GeoLite2-Country.mmdb')),
        'asn' => env('GEOIP_ASN_DB', storage_path('geoip/GeoLite2-ASN.mmdb')),
    ],

    'locales' => ['en'],
];
