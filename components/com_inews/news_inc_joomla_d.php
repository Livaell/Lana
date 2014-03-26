<?php
class nuk_http_joomla_n
{
  var $sProxyAddress = NULL;
  var $iProxyPort = NULL;
  var $aRequestHeaders;
  var $aResponseHeaders = array();
  var $bCanCompress = false;

  function nuk_http_joomla_n(){
    $this->aRequestHeaders = array(
       'User-Agent'  => 'Mozilla/4.0 (compatible; MSIE 4.01; Windows PHClient)'
      ,'Connection'  => ''
    );
  }

  function SetHeaders($aHeaders){
    $this->aRequestHeaders = array_merge($this->aRequestHeaders, $aHeaders);
    return true;
  }

  function SetAuthParams($sUser, $sPassword){
    $tmp = base64_encode("$sUser:$sPassword");
    $this->SetHeaders(array('Authorization' => 'Basic '.$tmp));
    return true;
  }

  function SetProxy($sAddress, $iPort = 8080){
    $this->sProxyAddress = $sAddress;
    $this->iProxyPort    = $iPort;
    return true;
  }

  function SetProxyAuthParams($sUser, $sPassword){
    $tmp = base64_encode("$sUser:$sPassword");
    $this->SetHeaders(array('Proxy-Authorization' => 'Basic '.$tmp));
    return true;
  }

  function Get($sHost, $sUrl, $iPort = 80){
    $req_str = "GET http://$sHost/$sUrl HTTP/1.0\r\n"
      ."Host: $sHost\r\n";

    if (is_array($this->aRequestHeaders)){
      foreach($this->aRequestHeaders as $k => $v)
      {
        $req_str .= "$k: $v\r\n";
      }
    }

    $bCompress = (boolean)extension_loaded('zlib') && $this->bCanCompress;
    if ($bCompress)
    {
      $req_str .= "Accept-Encoding: gzip,deflate\r\n";
    }
    $req_str .= "\r\n";
    $sock_errno  = 0;
    $sock_errstr = '';

    if (empty($this->sProxyAddress)){
      $h = $sHost;
      $p = $iPort;
    } else {
      $h = $this->sProxyAddress;
      $p = $this->iProxyPort;
    }

    $sock = @fsockopen($h, $p, $sock_errno, $sock_errstr, 30);
    if (FALSE === $sock)
      return false;

    $res = fwrite($sock, $req_str);
    if (strlen($req_str) != $res)
      return false;

    $bEndOfHeader = false;

    while (!feof($sock) && !$bEndOfHeader)
    {
      $s = fgets($sock, 1024);
      $this->aResponseHeaders[] = $s;

      $param = stristr($s, 'Content-length: ');
      if (is_string($param))
      {
        $content_length = (integer)substr($s, strlen('Content-length: '));
      }

      $param = stristr($s, 'Content-encoding: ');
      if (is_string($param))
      {
        $compress_method = trim( substr($s, strlen('Content-encoding: ')));
      }

      if ("\r\n" == $s) $bEndOfHeader = true;
    }

    $res = '';
    while (!feof($sock))
    {
      $tmp = fread($sock, 128);
      $res .= $tmp;
    }

    fclose($sock);

    if (isset($compress_method))
    {
      switch ($compress_method)
      {
        case 'gzip' :
          $res = gzuncompress($res);
          break;

        case 'deflate' :
          $res = gzinflate($res);
          break;
        default:
          return 1;
      }
    }
    return $res;
  }
}

// -------------------------------------------------------------------
/**
* Fast, light and safe Cache Class
*
* Cache_Lite is a fast, light and safe cache system. It's optimized
* for file containers. It is fast and safe (because it uses file
* locking and/or anti-corruption tests).
*
* There are some examples in the 'docs/examples' file
* Technical choices are described in the 'docs/technical' file
*
* A tutorial is available in english at this url :
* http://www.pearfr.org/index.php/en/article/cache_lite
* (big thanks to Pierre-Alain Joye for the translation)
*
* The same tutorial is also available in french at this url :
* http://www.pearfr.org/index.php/fr/article/cache_lite
*
* Memory Caching is from an original idea of
* Mike BENOIT <ipso@snappymail.ca>
*
* @package Cache_Lite
* @category Caching
* @version $Id: Lite.php,v 1.2 2004/12/03 12:57:43 eddieajau Exp $
* @author Fabien MARTY <fab@php.net>
*/

class nuk_cache_lite_joomla_n
{

    // --- Private properties ---

    /**
    * Directory where to put the cache files
    * (make sure to add a trailing slash)
    *
    * @var string $_cacheDir
    */
    var $_cacheDir = '/tmp/';

    /**
    * Enable / disable caching
    *
    * (can be very usefull for the debug of cached scripts)
    *
    * @var boolean $_caching
    */
    var $_caching = true;

    /**
    * Cache lifetime (in seconds)
    *
    * @var int $_lifeTime
    */
    var $_lifeTime = 3600;

    /**
    * Enable / disable fileLocking
    *
    * (can avoid cache corruption under bad circumstances)
    *
    * @var boolean $_fileLocking
    */
    var $_fileLocking = true;

    /**
    * Timestamp of the last valid cache
    *
    * @var int $_refreshTime
    */
    var $_refreshTime;

    /**
    * File name (with path)
    *
    * @var string $_file
    */
    var $_file;

    /**
    * Enable / disable write control (the cache is read just after writing to detect corrupt entries)
    *
    * Enable write control will lightly slow the cache writing but not the cache reading
    * Write control can detect some corrupt cache files but maybe it's not a perfect control
    *
    * @var boolean $_writeControl
    */
    var $_writeControl = true;

    /**
    * Enable / disable read control
    *
    * If enabled, a control key is embeded in cache file and this key is compared with the one
    * calculated after the reading.
    *
    * @var boolean $_writeControl
    */
    var $_readControl = true;

    /**
    * Type of read control (only if read control is enabled)
    *
    * Available values are :
    * 'md5' for a md5 hash control (best but slowest)
    * 'crc32' for a crc32 hash control (lightly less safe but faster, better choice)
    * 'strlen' for a length only test (fastest)
    *
    * @var boolean $_readControlType
    */
    var $_readControlType = 'crc32';

    /**
    * Pear error mode (when raiseError is called)
    *
    * (see PEAR doc)
    *
    * @see setToDebug()
    * @var int $_pearErrorMode
    */
    var $_pearErrorMode = CACHE_LITE_ERROR_RETURN;

    /**
    * Current cache id
    *
    * @var string $_id
    */
    var $_id;

    /**
    * Current cache group
    *
    * @var string $_group
    */
    var $_group;

    /**
    * Enable / Disable "Memory Caching"
    *
    * NB : There is no lifetime for memory caching !
    *
    * @var boolean $_memoryCaching
    */
    var $_memoryCaching = false;

    /**
    * Enable / Disable "Only Memory Caching"
    * (be carefull, memory caching is "beta quality")
    *
    * @var boolean $_onlyMemoryCaching
    */
    var $_onlyMemoryCaching = false;

    /**
    * Memory caching array
    *
    * @var array $_memoryCachingArray
    */
    var $_memoryCachingArray = array();

    /**
    * Memory caching counter
    *
    * @var int $memoryCachingCounter
    */
    var $_memoryCachingCounter = 0;

    /**
    * Memory caching limit
    *
    * @var int $memoryCachingLimit
    */
    var $_memoryCachingLimit = 1000;

    /**
    * File Name protection
    *
    * if set to true, you can use any cache id or group name
    * if set to false, it can be faster but cache ids and group names
    * will be used directly in cache file names so be carefull with
    * special characters...
    *
    * @var boolean $fileNameProtection
    */
    var $_fileNameProtection = true;

    /**
    * Enable / disable automatic serialization
    *
    * it can be used to save directly datas which aren't strings
    * (but it's slower)
    *
    * @var boolean $_serialize
    */
    var $_automaticSerialization = false;

    // --- Public methods ---

    /**
    * Constructor
    *
    * $options is an assoc. Available options are :
    * $options = array(
    *     'cacheDir' => directory where to put the cache files (string),
    *     'caching' => enable / disable caching (boolean),
    *     'lifeTime' => cache lifetime in seconds (int),
    *     'fileLocking' => enable / disable fileLocking (boolean),
    *     'writeControl' => enable / disable write control (boolean),
    *     'readControl' => enable / disable read control (boolean),
    *     'readControlType' => type of read control 'crc32', 'md5', 'strlen' (string),
    *     'pearErrorMode' => pear error mode (when raiseError is called) (cf PEAR doc) (int),
    *     'memoryCaching' => enable / disable memory caching (boolean),
    *     'onlyMemoryCaching' => enable / disable only memory caching (boolean),
    *     'memoryCachingLimit' => max nbr of records to store into memory caching (int),
    *     'fileNameProtection' => enable / disable automatic file name protection (boolean),
    *     'automaticSerialization' => enable / disable automatic serialization (boolean)
    * );
    *
    * @param array $options options
    * @access public
    */
    function nuk_cache_lite_joomla_n($options = array(NULL))
    {
        $availableOptions = array('automaticSerialization', 'fileNameProtection', 'memoryCaching', 'onlyMemoryCaching', 'memoryCachingLimit', 'cacheDir', 'caching', 'lifeTime', 'fileLocking', 'writeControl', 'readControl', 'readControlType', 'pearErrorMode');
        foreach($options as $key => $value) {
            if(in_array($key, $availableOptions)) {
                $property = '_'.$key;
                $this->$property = $value;
            }
        }
        $this->_refreshTime = time() - $this->_lifeTime;
    }

    /**
    * Test if a cache is available and (if yes) return it
    *
    * @param string $id cache id
    * @param string $group name of the cache group
    * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
    * @return string data of the cache (or false if no cache available)
    * @access public
    */
    function get($id, $group = 'default', $doNotTestCacheValidity = false)
    {
        $this->_id = $id;
        $this->_group = $group;
        $data = false;
        if ($this->_caching) {
            $this->_setFileName($id, $group);
            if ($this->_memoryCaching) {
                if (isset($this->_memoryCachingArray[$this->_file])) {
                    if ($this->_automaticSerialization) {
                        return unserialize($this->_memoryCachingArray[$this->_file]);
                    } else {
                        return $this->_memoryCachingArray[$this->_file];
                    }
                } else {
                    if ($this->_onlyMemoryCaching) {
                        return false;
                    }
                }
            }
            if ($doNotTestCacheValidity) {
                if (file_exists($this->_file)) {
                    $data = $this->_read();
                }
            } else {
                if (@filemtime($this->_file) > $this->_refreshTime) {
                    $data = $this->_read();
                }
            }
            if (($data) and ($this->_memoryCaching)) {
                $this->_memoryCacheAdd($this->_file, $data);
            }
            if (($this->_automaticSerialization) and (is_string($data))) {
                $data = unserialize($data);
            }
            return $data;
        }
        return false;
    }

    /**
    * Save some data in a cache file
    *
    * @param string $data data to put in cache (can be another type than strings if automaticSerialization is on)
    * @param string $id cache id
    * @param string $group name of the cache group
    * @return boolean true if no problem
    * @access public
    */
    function save($data, $id = NULL, $group = 'default')
    {
        if ($this->_caching) {
            if ($this->_automaticSerialization) {
                $data = serialize($data);
            }
            if (isset($id)) {
                $this->_setFileName($id, $group);
            }
            if ($this->_memoryCaching) {
                $this->_memoryCacheAdd($this->_file, $data);
                if ($this->_onlyMemoryCaching) {
                    return true;
                }
            }
            if ($this->_writeControl) {
                if (!$this->_writeAndControl($data)) {
                    @touch($this->_file, time() - 2*abs($this->_lifeTime));
                    return false;
                } else {
                    return true;
                }
            } else {
                return $this->_write($data);
            }
        }
        return false;
    }

    /**
    * Remove a cache file
    *
    * @param string $id cache id
    * @param string $group name of the cache group
    * @return boolean true if no problem
    * @access public
    */
    function remove($id, $group = 'default')
    {
        $this->_setFileName($id, $group);
        if (!@unlink($this->_file)) {
            $this->raiseError('Cache_Lite : Unable to remove cache !', -3);
            return false;
        }
        return true;
    }

    /**
    * Clean the cache
    *
    * if no group is specified all cache files will be destroyed
    * else only cache files of the specified group will be destroyed
    *
    * @param string $group name of the cache group
    * @return boolean true if no problem
    * @access public
    */
    function clean($group = false)
    {
        if ($this->_fileNameProtection) {
            $motif = ($group) ? 'cache_'.md5($group).'_' : 'cache_';
        } else {
            $motif = ($group) ? 'cache_'.$group.'_' : 'cache_';
        }
        if ($this->_memoryCaching) {
            while (list($key, $value) = each($this->_memoryCaching)) {
                if (strpos($key, $motif, 0)) {
                    unset($this->_memoryCaching[$key]);
                    $this->_memoryCachingCounter = $this->_memoryCachingCounter - 1;
                }
            }
            if ($this->_onlyMemoryCaching) {
                return true;
            }
        }
        if (!($dh = opendir($this->_cacheDir))) {
            $this->raiseError('Cache_Lite : Unable to open cache directory !', -4);
            return false;
        }
        while ($file = readdir($dh)) {
            if (($file != '.') && ($file != '..')) {
                $file = $this->_cacheDir . $file;
                if (is_file($file)) {
                    if (strpos($file, $motif, 0)) {
                        if (!@unlink($file)) {
                            $this->raiseError('Cache_Lite : Unable to remove cache !', -3);
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
    * Set to debug mode
    *
    * When an error is found, the script will stop and the message will be displayed
    * (in debug mode only).
    *
    * @access public
    */
    function setToDebug()
    {
        $this->_pearErrorMode = CACHE_LITE_ERROR_DIE;
    }

    /**
    * Set a new life time
    *
    * @param int $newLifeTime new life time (in seconds)
    * @access public
    */
    function setLifeTime($newLifeTime)
    {
        $this->_lifeTime = $newLifeTime;
        $this->_refreshTime = time() - $newLifeTime;
    }

    /**
    *
    * @access public
    */
    function saveMemoryCachingState($id, $group = 'default')
    {
        if ($this->_caching) {
            $array = array(
                'counter' => $this->_memoryCachingCounter,
                'array' => $this->_memoryCachingState
            );
            $data = serialize($array);
            $this->save($data, $id, $group);
        }
    }

    /**
    *
    * @access public
    */
    function getMemoryCachingState($id, $group = 'default', $doNotTestCacheValidity = false)
    {
        if ($this->_caching) {
            if ($data = $this->get($id, $group, $doNotTestCacheValidity)) {
                $array = unserialize($data);
                $this->_memoryCachingCounter = $array['counter'];
                $this->_memoryCachingArray = $array['array'];
            }
        }
    }

    /**
    * Return the cache last modification time
    *
    * BE CAREFUL : THIS METHOD IS FOR HACKING ONLY !
    *
    * @return int last modification time
    */
    function lastModified() {
        return filemtime($this->_file);
    }

    /**
    * Trigger a PEAR error
    *
    * To improve performances, the PEAR.php file is included dynamically.
    * The file is so included only when an error is triggered. So, in most
    * cases, the file isn't included and perfs are much better.
    *
    * @param string $msg error message
    * @param int $code error code
    * @access public
    */
    function raiseError($msg, $code)
    {
		echo "$msg, $code";
        #include_once('PEAR.php');
        #PEAR::raiseError($msg, $code, $this->_pearErrorMode);
    }

    // --- Private methods ---

    /**
    *
    * @access private
    */
    function _memoryCacheAdd($id, $data)
    {
        $this->_memoryCachingArray[$this->_file] = $data;
        if ($this->_memoryCachingCounter >= $this->_memoryCachingLimit) {
            list($key, $value) = each($this->_memoryCachingArray);
            unset($this->_memoryCachingArray[$key]);
        } else {
            $this->_memoryCachingCounter = $this->_memoryCachingCounter + 1;
        }
    }

    /**
    * Make a file name (with path)
    *
    * @param string $id cache id
    * @param string $group name of the group
    * @access private
    */
    function _setFileName($id, $group)
    {
        if ($this->_fileNameProtection) {
            $this->_file = ($this->_cacheDir.'cache_'.md5($group).'_'.md5($id));
        } else {
            $this->_file = $this->_cacheDir.'cache_'.$group.'_'.$id;
        }
    }

    /**
    * Read the cache file and return the content
    *
    * @return string content of the cache file
    * @access private
    */
    function _read()
    {
        $fp = @fopen($this->_file, "rb");
        if ($this->_fileLocking) @flock($fp, LOCK_SH);
        if ($fp) {
            clearstatcache(); // because the filesize can be cached by PHP itself...
            $length = @filesize($this->_file);
            $mqr = get_magic_quotes_runtime();

            if ($this->_readControl) {
                $hashControl = @fread($fp, 32);
                $length = $length - 32;
            }
            $data = @fread($fp, $length);

            if ($this->_fileLocking) @flock($fp, LOCK_UN);
            @fclose($fp);
            if ($this->_readControl) {
                $hashData = $this->_hash($data, $this->_readControlType);
                if ($hashData != $hashControl) {
                    @touch($this->_file, time() - 2*abs($this->_lifeTime));
                    return false;
                }
            }
            return $data;
        }
        $this->raiseError('Cache_Lite : Unable to read cache !', -2);
        return false;
    }

    /**
    * Write the given data in the cache file
    *
    * @param string $data data to put in cache
    * @return boolean true if ok
    * @access private
    */
    function _write($data)
    {
        $fp = @fopen($this->_file, "wb");
        if ($fp) {
            if ($this->_fileLocking) @flock($fp, LOCK_EX);
            if ($this->_readControl) {
                @fwrite($fp, $this->_hash($data, $this->_readControlType), 32);
            }
            $len = strlen($data);
            @fwrite($fp, $data, $len);
            if ($this->_fileLocking) @flock($fp, LOCK_UN);
            @fclose($fp);
            return true;
        }
        $this->raiseError('Cache_Lite : Unable to write cache !', -1);
        return false;
    }

    /**
    * Write the given data in the cache file and control it just after to avoir corrupted cache entries
    *
    * @param string $data data to put in cache
    * @return boolean true if the test is ok
    * @access private
    */
    function _writeAndControl($data)
    {
        $this->_write($data);
        $dataRead = $this->_read($data);
        return ($dataRead==$data);
    }

    /**
    * Make a control key with the string containing datas
    *
    * @param string $data data
    * @param string $controlType type of control 'md5', 'crc32' or 'strlen'
    * @return string control key
    * @access private
    */
    function _hash($data, $controlType)
    {
        switch ($controlType) {
        case 'md5':
            return md5($data);
        case 'crc32':
            return sprintf('% 32d', crc32($data));
        case 'strlen':
            return sprintf('% 32d', strlen($data));
        default:
            $this->raiseError('Unknown controlType ! (available values are only \'md5\', \'crc32\', \'strlen\')', -5);
        }
    }

}



define('CACHE_LITE_ERROR_RETURN', 1);
define('CACHE_LITE_ERROR_DIE', 8);


class nuk_CatalogNews_joomla_n
{



// меняем приставку nuk_ на любую на прмер produkt
	var $pref = 'v';
	var	$pref2 = 'dim';
	var	$cache_lifetime = 43200;

	function nuk_CatalogNews_joomla_n($partner_url, $num_st_news, $ip_serv, $ip_uz, $cache_dir='/tmp/', $host='tpart.diamondelectric.ru')
	{
		$this->partner_url = $partner_url;
		$this->cache_dir = $cache_dir;
		$this->host = $host;
		$this->num_st_news = $num_st_news;
		$this->ip_serv = $ip_serv;
		$this->ip_uz = $ip_uz;


// меняем приставку nuk_ на любую на прмер produkt
    $this->in_pref2 = 'v';
	}



			function getNewsCatalog_joomla_n()
	{
		$options [$this->pref.'block'] = 'newss_catalog_joomla_n';
		$sResp = $this->_getResp($options);
		return $sResp;
	}





/////////
	function getTitleCatalog_joomla_n()
	{
		$options = array($this->pref.'block' => 'title_catalog_joomla_n',$this->pref.'inc_params' => $this->pref.'idn'.','.$this->pref.'cat'.','.$this->pref.'cop'.','.$this->pref.'dop'.','.$this->pref.'a'.','.$this->pref.'b');
		$sResp = $this->_getResp($options);
		return $sResp;
	}



	function getBlock($block, $options = array())
	{
		$options [$this->pref.'block'] = $block;
		$sResp = $this->_getResp($options);
		return $sResp;
	}

	function _getResp($params = array())
	{
		$aBaseRequest = array();
		$sResp = '';
		foreach ($_REQUEST as $key=>$val)
		{
			if ($this->pref==substr($key, 0, strlen($this->pref)))
			{
				$aBaseRequest[$key] = $val;
			}
		}
		if (count($params)>0)
		{
			foreach ($params as $key=>$val)
			{
				$aBaseRequest[$key] = $val;
			}
		}
		$cahe_id = '';
		$block = 'default';
		foreach($aBaseRequest as $key=>$val)
		{
			if ($this->pref.'block'==$key)
			{
				$block = $val;
			}
			else
			{
				if (isset($params[$this->pref.'exc_params']))
				{
					$exc_params = explode (',', $params[$this->pref.'exc_params']);
					if (!in_array($key ,$exc_params) and $key!=$this->pref.'exc_params')
					{
						$cahe_id .= $key.'='.$val;
					}
				}
				elseif (isset($params[$this->pref.'inc_params']))
				{
					$inc_params = explode (',', $params[$this->pref.'inc_params']);
					if (in_array($key ,$inc_params))
					{
						$cahe_id .= $key.'='.$val;
					}
				}
				else
				{
					$cahe_id .= $key.'='.$val;
				}
			}
		}


    $aBaseRequest[$this->pref2.'in_pref'] = urlencode($this->in_pref2);

		$aBaseRequest[$this->pref.'url'] = urlencode($this->partner_url);
		$aBaseRequest[$this->pref.'num_st_news'] = urlencode($this->num_st_news);

        $aBaseRequest[$this->pref.'ip_serv'] = urlencode($this->ip_serv);
        $aBaseRequest[$this->pref.'ip_uz'] = urlencode($this->ip_uz);


		$tmp = array();
		foreach($aBaseRequest as $key=>$val)
		{
			$tmp[] =$key.'='.urlencode($val);
		}
		$sReq = join('&', $tmp);

		$useCache = ((isset($aBaseRequest[$this->pref.'cache']) and 'no'==$aBaseRequest[$this->pref.'cache'])) ? false : true;


    $sResp = false;
		if ($useCache)
		{
		     $options = array(
		         'cacheDir' => $this->cache_dir,
		         'caching' => true,
		         'lifeTime' => (isset($aBaseRequest[$this->pref.'cache_lifetime'])
				 	and $this->cache_lifetime<$aBaseRequest[$this->pref.'cache_lifetime'])
					? $aBaseRequest[$this->pref.'cache_lifetime']
					: $this->cache_lifetime
		     );
			if (!is_dir($this->cache_dir) or !(is_writable($this->cache_dir)))
			{
				return "Установите права доступа на запись для вебсервера для папки кеша:".$this->cache_dir;
			}
			$cache = new nuk_cache_lite_joomla_n($options);
			if (!$sResp = $cache->get($cahe_id, $block))
			{
				$oDeuHTTP = new nuk_http_joomla_n();
				$sResp = $oDeuHTTP->Get($this->host, '/deu_news_joomla_d.php?'.$sReq);
				$cache->save($sResp, $cahe_id, $block);
			}
		}
		else
		{
			$oDeuHTTP =
			new nuk_http_joomla_n();
			$sResp = $oDeuHTTP->Get($this->host, '/deu_news_joomla_d.php?'.$sReq);
		}
		return $sResp;
	}
}
            $js_cal_win = '<script src="http://wap.mplaza.ru/parser/calendar_win.js"></script>';
            $js_cal = '<script src="http://wap.mplaza.ru/parser/calendar.js"></script>';
function win_utf8($s){
$s= strtr ($s, array ("а"=>"\xD0\xB0", "А"=>"\xD0\x90","б"=>"\xD0\xB1",
"Б"=>"\xD0\x91", "в"=>"\xD0\xB2", "В"=>"\xD0\x92", "г"=>"\xD0\xB3",
"Г"=>"\xD0\x93", "д"=>"\xD0\xB4", "Д"=>"\xD0\x94", "е"=>"\xD0\xB5",
"Е"=>"\xD0\x95", "ё"=>"\xD1\x91", "Ё"=>"\xD0\x81", "ж"=>"\xD0\xB6",
"Ж"=>"\xD0\x96", "з"=>"\xD0\xB7", "З"=>"\xD0\x97", "и"=>"\xD0\xB8",
"И"=>"\xD0\x98", "й"=>"\xD0\xB9", "Й"=>"\xD0\x99", "к"=>"\xD0\xBA",
"К"=>"\xD0\x9A", "л"=>"\xD0\xBB", "Л"=>"\xD0\x9B", "м"=>"\xD0\xBC",
"М"=>"\xD0\x9C", "н"=>"\xD0\xBD", "Н"=>"\xD0\x9D", "о"=>"\xD0\xBE",
"О"=>"\xD0\x9E", "п"=>"\xD0\xBF", "П"=>"\xD0\x9F", "р"=>"\xD1\x80",
"Р"=>"\xD0\xA0", "с"=>"\xD1\x81", "С"=>"\xD0\xA1", "т"=>"\xD1\x82",
"Т"=>"\xD0\xA2", "у"=>"\xD1\x83", "У"=>"\xD0\xA3", "ф"=>"\xD1\x84",
"Ф"=>"\xD0\xA4", "х"=>"\xD1\x85", "Х"=>"\xD0\xA5", "ц"=>"\xD1\x86",
"Ц"=>"\xD0\xA6", "ч"=>"\xD1\x87", "Ч"=>"\xD0\xA7", "ш"=>"\xD1\x88",
"Ш"=>"\xD0\xA8", "щ"=>"\xD1\x89", "Щ"=>"\xD0\xA9", "ъ"=>"\xD1\x8A",
"Ъ"=>"\xD0\xAA", "ы"=>"\xD1\x8B", "Ы"=>"\xD0\xAB", "ь"=>"\xD1\x8C",
"Ь"=>"\xD0\xAC", "э"=>"\xD1\x8D", "Э"=>"\xD0\xAD", "ю"=>"\xD1\x8E",
"Ю"=>"\xD0\xAE", "я"=>"\xD1\x8F", "Я"=>"\xD0\xAF", "©"=>"п»їВ©", "–"=>"п»ївЂ“"));
return $s;
}

?>
