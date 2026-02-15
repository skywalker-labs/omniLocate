<?php

namespace Skywalker\Location\Models;

use Illuminate\Database\Eloquent\Model;

class GeoAnalytics extends Model
{
    protected $table = 'geo_analytics';

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

