<?php

namespace Vironeer;

use Illuminate\Support\Facades\Cache;

class IpInfo
{
    public static function ip()
    {
        $ip = null;
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
        } else {
            if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
                $ip = $_SERVER["REMOTE_ADDR"];
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                }
                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                }
            }
        }
        return $ip;
    }

    public static function lookup($ip = null)
    {
        $ip = ($ip) ? $ip : self::ip();
        if (Cache::has($ip)) {
            $ipInfo = Cache::get($ip);
        } else {
            $fields = "status,country,countryCode,city,zip,lat,lon,timezone,query";
            $ipInfo = (object) json_decode(curl_get_file_contents("http://ip-api.com/json/{$ip}?fields={$fields}"), true);
            Cache::forever($ip, $ipInfo);
        }
        $data['ip'] = $ipInfo->query ?? $ip;
        $data['location']['country'] = $ipInfo->country ?? "Other";
        $data['location']['country_code'] = $ipInfo->countryCode ?? "Other";
        $data['location']['timezone'] = $ipInfo->timezone ?? "Other";
        $data['location']['city'] = $ipInfo->city ?? "Other";
        $data['location']['postal_code'] = $ipInfo->zip ?? "Unknown";
        $data['location']['latitude'] = $ipInfo->lat ?? "Unknown";
        $data['location']['longitude'] = $ipInfo->lon ?? "Unknown";
        return $data;
    }

}
