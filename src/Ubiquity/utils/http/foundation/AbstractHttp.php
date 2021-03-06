<?php

namespace Ubiquity\utils\http\foundation;

/**
 * Ubiquity\utils\http\foundation$AbstractHttp
 * This class is part of Ubiquity
 *
 * @author jcheron <myaddressmail@gmail.com>
 * @version 1.0.0
 *
 */
abstract class AbstractHttp {

	public abstract function getAllHeaders();

	public abstract function header($key, $value, $replace = null, $http_response_code = null);

	public abstract function headersSent(string &$file = null, int &$line = null);

	public abstract function getInput();
}

