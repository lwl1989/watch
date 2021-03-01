<?php


namespace App\Library\Constant;


class StaticRoute
{
	/**
	 * @return array
	 */
	public static function getAll() : array {
        return self::_getRouterMapping();
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public static function getByName(string $name) : string {
		$arr = self::_getRouterMapping();
		return $arr[$name] ?? '';
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public static function geyByPath(string $path) : string {
		$result = array_search(ltrim($path,'/'), self::_getRouterMapping());
		return $result === false ? $result : '';
	}

	/**
	 * @return array
	 */
	public static function getAllPath() :array {
        return array_values(self::_getRouterMapping());
	}

	/**
	 * @return array
	 */
	public static function getAllName() : array {
        return array_keys(self::_getRouterMapping());
	}

    /**
     * @param $role
     * @param array $names
     * @return array
     */
	public static function getPathFromName($role, array $names) : array {
	    $router = [];
        $routersMapping = self::_getRouterMapping($role);

	    foreach ($names as $name) {
	        if(array_key_exists($name, $routersMapping)) {
                $router[] = $routersMapping[$name];
            }

        }
        return $router;
    }

    private static function _getRouterMapping($role = Common::ADMIN_ROLE_MANAGER) : array
    {
        $arr = Common::PERMISSION_MAPPING;
        if($role == Common::ADMIN_ROLE_MANAGER) {
            $arr = Common::PERMISSION_MAPPING_ADMIN;
        }
        if($role == Common::ADMIN_ROLE_SHOP) {
            $arr = Common::PERMISSION_MAPPING_SHOP;
        }
        $routersMapping = array_column($arr,'actions');
        $routersMapping = array_reduce($routersMapping, function ($result, $value) {
            return array_merge($result, array_values($value));
        }, array());

        return $routersMapping = array_column($routersMapping, 'vue','en');
    }
}