<?php

/**
 * Cache traits
 */
namespace Ubiquity\cache\traits;

use Ubiquity\orm\parser\ModelParser;
use Ubiquity\cache\ClassUtils;
use Ubiquity\contents\validation\ValidatorsManager;

/**
 *
 * Ubiquity\cache\traits$ModelsCacheTrait
 * This class is part of Ubiquity
 *
 * @author jcheron <myaddressmail@gmail.com>
 * @version 1.0.0
 * @property \Ubiquity\cache\system\AbstractDataCache $cache
 */
trait ModelsCacheTrait {

	abstract protected static function _getFiles(&$config, $type, $silent = false);

	public static function createOrmModelCache($classname) {
		$key = self::getModelCacheKey ( $classname );
		if (isset ( self::$cache )) {
			$p = new ModelParser ();
			$p->parse ( $classname );
			self::$cache->store ( $key, $p->__toString (), 'models' );
			return self::$cache->fetch ( $key );
		}
	}

	public static function getOrmModelCache($classname) {
		return self::$cache->fetch ( self::getModelCacheKey ( $classname ) );
	}

	public static function getModelCacheKey($classname) {
		return \str_replace ( "\\", \DS, $classname );
	}

	public static function modelCacheExists($classname) {
		$key = self::getModelCacheKey ( $classname );
		if (isset ( self::$cache ))
			return self::$cache->exists ( $key );
		return false;
	}

	public static function initModelsCache(&$config, $forChecking = false, $silent = false) {
		$files = self::getModelsFiles ( $config, $silent );
		foreach ( $files as $file ) {
			if (is_file ( $file )) {
				$model = ClassUtils::getClassFullNameFromFile ( $file );
				if (! $forChecking) {
					self::createOrmModelCache ( $model );
				}
			}
		}
		if (! $forChecking) {
			ValidatorsManager::initModelsValidators ( $config );
		}
		if (! $silent) {
			echo "Models cache reset\n";
		}
	}

	/**
	 * Returns an array of files corresponding to models
	 *
	 * @param array $config
	 * @param boolean $silent
	 * @return array
	 */
	public static function getModelsFiles(&$config, $silent = false) {
		return self::_getFiles ( $config, "models", $silent );
	}

	/**
	 * Returns an array of the models class names
	 *
	 * @param array $config
	 * @param boolean $silent
	 * @return string[]
	 */
	public static function getModels(&$config, $silent = false) {
		$result = [ ];
		$files = self::getModelsFiles ( $config, $silent );
		foreach ( $files as $file ) {
			$result [] = ClassUtils::getClassFullNameFromFile ( $file );
		}
		return $result;
	}
}
