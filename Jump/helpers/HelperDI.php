<?php
namespace Jump\helpers;

/**
 * Class HelperDI
 * @package Jump\helpers
 */
class HelperDI
{
    /**
     * @return \Jump\DI\DI
     */
    public static function get($dependencyName)
    {
        global $di;

        return $di->get($dependencyName);
    }
}
