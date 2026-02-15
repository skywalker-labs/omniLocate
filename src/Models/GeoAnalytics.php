<?php

namespace Skywalker\Location\Models;

use Skywalker\Support\Database\PrefixedModel;

class GeoAnalytics extends PrefixedModel
{
    protected $table = 'location_geo_analytics';

    protected $fillable = [
        'ip',
        'country_code',
        'city',
        'isp',
        'is_proxy',
        'is_vpn',
        'is_tor',
        'risk_score',
        'url',
        'method',
    ];
}
