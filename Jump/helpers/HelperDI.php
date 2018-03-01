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
    public static function get($dependencyName = NULL)
    {
		global $di;
		
		return is_null($dependencyName) ? $di : $di->get($dependencyName);
    }
}
