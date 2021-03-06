<?php

namespace Dyln;

use Dyln\Config\Config;
use Dyln\Util\BooleanUtil;

class AppEnv
{
    const DEFAULT_ENV = 'development';
    const LIVE_ENV = 'production';
    const TEST_ENV = 'test';

    private static $placeholders = [
        'ROOT_DIR',
        'APPLICATION_ENV',
    ];
    public static $serverToEnvMap = [];

    public static function isDev()
    {
        return self::getAppEnv() !== self::LIVE_ENV;
    }

    public static function getAppEnv()
    {
        if (!defined('APPLICATION_ENV')) {
            $env = getenv('APPLICATION_ENV');
            if (!$env) {
                $env = get_cfg_var('APPLICATION_ENV');
                if (!$env) {
                    $env = self::getAppEnvFromServerName();
                    if (!$env) {
                        $env = self::DEFAULT_ENV;
                    }
                }
            }
            define('APPLICATION_ENV', $env);
        }
        self::putEnv('APPLICATION_ENV', APPLICATION_ENV);

        return APPLICATION_ENV;
    }

    public static function putEnv($key, $value)
    {
        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    public static function getAppEnvFromServerName()
    {
        if (php_sapi_name() == 'cli') {
            $servername = gethostname();
        } else {
            $servername = $_SERVER['SERVER_NAME'];
        }
        $list = self::$serverToEnvMap[self::LIVE_ENV] ?? [];
        if (in_array($servername, $list)) {
            return self::LIVE_ENV;
        }

        return self::DEFAULT_ENV;
    }

    public static function isLive()
    {
        return self::getAppEnv() == self::LIVE_ENV;
    }

    public static function isTest()
    {
        return self::getAppEnv() == self::TEST_ENV;
    }

    public static function env($key, $default = null)
    {
        if (!self::hasEnv($key)) {
            return $default;
        }
        $value = $_ENV[$key];
        if ($value === 'true') {
            return true;
        }
        if ($value === 'false') {
            return false;
        }
        if ($value === 'null') {
            return null;
        }
        if (strpos($value, '{{') !== false) {
            foreach (self::$placeholders as $placeholder) {
                $value = str_replace('{{' . $placeholder . '}}', self::env($placeholder), $value);
            }
        }

        return $value;
    }

    public static function cookie($key, $default = null)
    {
        return getin($_COOKIE, $key, $default);
    }

    public static function option($key, $default = null)
    {
        return self::env('_option.' . $key, $default);
    }

    public static function hasEnv($key)
    {
        return isset($_ENV[$key]);
    }

    public static function isDebugEnabled()
    {
        $debug = BooleanUtil::getBool(Config::get('app.debug.enabled', false));
        if (!$debug) {
            $overwrite = $_GET['debug'] ?? $_COOKIE['debug'] ?? null;
            if ($overwrite) {
                $debug = $overwrite === Config::get('app.debug.url_key');
            }
        }

        return $debug;
    }

    public static function isXdebugEnabled()
    {
        $overwrite = $_GET['XDEBUG_SESSION_START'] ?? $_COOKIE['XDEBUG_SESSION_START'] ?? $_GET['XDEBUG_SESSION'] ?? $_COOKIE['XDEBUG_SESSION'] ?? null;

        return !!$overwrite;
    }

    public static function isXdebugProfilerEnabled()
    {
        $overwrite = $_GET['XDEBUG_PROFILE'] ?? $_COOKIE['XDEBUG_PROFILE'] ?? null;

        return !!$overwrite;
    }

    public static function isApiSignatureCheckDisabled()
    {
        $check = $_GET['debug_sign'] ?? $_COOKIE['debug_sign'] ?? null;

        return $check === Config::get('app.debug.url_key');
    }

    public static function isCacheResetEnabled()
    {
        $resetKey = $_GET['reset'] ?? null;

        return $resetKey == Config::get('app.debug.url_key');
    }

    public static function isUrlKeyMatch($key, $value)
    {
        $urlKey = $_GET[$key] ?? null;
        if (!$urlKey) {
            return false;
        }

        return $urlKey === (string) $value;
    }

    public static function getLocaleFromBrowser()
    {
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en_GB';

        return \Locale::acceptFromHttp($acceptLanguage);
    }
}
