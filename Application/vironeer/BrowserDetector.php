<?php

namespace Vironeer;

class BrowserDetector
{
    public const BROWSERS = [
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i' => 'Handheld Browser',
    ];

    public static function browser($agent = null)
    {
        $browser = "Other";
        $agent = ($agent) ? $agent : (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        foreach (self::BROWSERS as $key => $value) {
            if (preg_match($key, $agent)) {
                $browser = $value;
            }
        }
        return $browser;
    }
}
