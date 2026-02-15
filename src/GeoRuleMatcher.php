<?php

namespace Skywalker\Location;

use Skywalker\Location\Facades\Location;

class GeoRuleMatcher
{
    /**
     * Match a rule against the current or provided position.
     *
     * DSL Format: "country:US,CA;risk<50;is_vpn:false"
     * Delimiters: ";" for AND, "," for OR (within value)
     * Operators: ":", "<", ">", "=", "!="
     *
     * @param string $rule
     * @param Position|null $position
     * @return bool
     */
    public static function matches($rule, $position = null)
    {
        $position = $position ?: Location::get();

        if (!$position) {
            return false;
        }

        $conditions = explode(';', $rule);

        foreach ($conditions as $condition) {
            if (!$condition) continue;

            if (!self::checkCondition($condition, $position)) {
                return false;
            }
        }

        return true;
    }

    protected static function checkCondition($condition, $position)
    {
        // Simple parser
        if (strpos($condition, '<') !== false) {
            [$key, $value] = explode('<', $condition);
            return self::getValue($key, $position) < (int)$value;
        }

        if (strpos($condition, '>') !== false) {
            [$key, $value] = explode('>', $condition);
            return self::getValue($key, $position) > (int)$value;
        }

        if (strpos($condition, '!=') !== false) {
            [$key, $values] = explode('!=', $condition);
            $values = explode(',', $values);
            return !in_array((string)self::getValue($key, $position), $values);
        }

        if (strpos($condition, ':') !== false) {
            [$key, $values] = explode(':', $condition);
            $values = explode(',', $values);
            // Treat boolean strings as bools
            if (in_array(strtolower($values[0]), ['true', 'false'])) {
                $check = self::getValue($key, $position);
                $expected = filter_var($values[0], FILTER_VALIDATE_BOOLEAN);
                return $check === $expected;
            }
            return in_array((string)self::getValue($key, $position), $values);
        }

        return false;
    }

    protected static function getValue($key, $position)
    {
        switch (trim($key)) {
            case 'country':
                return $position->countryCode;
            case 'risk':
                return $position->geoRiskScore;
            case 'is_vpn':
                return $position->isVpn;
            case 'is_proxy':
                return $position->isProxy;
            case 'is_tor':
                return $position->isTor;
                // Add more mappings as needed
            default:
                return null;
        }
    }
}

