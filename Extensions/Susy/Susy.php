<?php

require_once dirname(__FILE__).'/../ExtensionInterface.php';

class Susy
implements ExtensionInterface
{
	/** @var string */
	public static $filesFolder='stylesheets';
	/** @var array */
	public static $filePaths=NULL;
	/** @var array List with alias functions in Susy */
	public static $functions=array();


	/**
	 * @param type $namespace
	 * @return array
	 */
	public static function getFunctions($namespace)
	{
		return array();
	}

	/**
	 * Returns an array with all files in $root path recursively and assign each array Key with clean alias
	 *
	 * @param string $root
	 * @return array
	 */
	public static function getFilesArray($root)
	{
		$alias=array();
		$directories=array();
		$last_letter=$root[strlen($root)-1];
		$root= ($last_letter=='\\' || $last_letter=='/')
			? $root
			: $root.DIRECTORY_SEPARATOR;

		$directories[]=$root;

		while (sizeof($directories)) {
			$dir=array_pop($directories);
			if ($handle=opendir($dir)) {
				while (FALSE!==($file=readdir($handle))) {
					if ($file=='.' || $file=='..') {
						continue;
						}
					$file=$dir.$file;
					if (is_dir($file)) {
						$directory_path=$file.DIRECTORY_SEPARATOR;
						array_push($directories, $directory_path);
						}
					elseif (is_file($file)) {
						$key=basename($file);
						$alias[substr($key, 1, strpos($key, '.')-1)]=$file;
						}
					}
				closedir($handle);
				}
			}

		return $alias;
	}

	/**
	 * Implementation of hook_resolve_path_NAMESPACE().
	 *
	 * @param string $callerImport
	 * @param type $parser
	 * @param string $syntax
	 * @return string
	 */
	public static function resolveExtensionPath($callerImport, $parser, $syntax='scss')
	{
		$extension='';
		$alias=str_replace('/_', '/', str_replace(array('.scss', '.sass'), '', $callerImport));
		if (strrpos($alias, '/')!==FALSE) {
			$extension=substr($alias, 0, strpos($alias, '/'));
			$alias=substr($alias, strrpos($alias, '/')+1);
			}
		if (self::$filePaths==NULL) {
			self::$filePaths=self::getFilesArray(dirname(__FILE__).'/'.self::$filesFolder.'/');
			}
		if (isset(self::$filePaths[$alias]) && $extension!='compass') {
			return self::$filePaths[$alias];
			}
	}
}
