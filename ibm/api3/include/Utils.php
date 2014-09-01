<?php

/**
 * API Utils
 */
class Utils
{
    /**
     * 
     * Convert name value list to keyed array
     * @param unknown_type $nameValueList
     */
    public static function nvlToArray($nameValueList)
    {
        if (! is_array($nameValueList)) {
            return null;
        }
        $array = array();
        foreach ($nameValueList as $item) {
            $array[$item->name] = $item->value;
        }
        return $array;
    }

    /**
     * 
     * Execute array of queries
     * @param unknown_type $sql
     */
    public static function executeQueries($sql)
    {
        $db = Db_Factory::getInstance();
        foreach ($sql as $query) {
            $db->query($query);
        }
    }

    /**
     * 
     * Save content to file
     * @param string $file
     * @param string $content
     * @param bool $append
     */
    public static function dumpToFile($file, $content, $append = false)
    {
        ($append == false) ? $mode = "w" : $mode = "a";
        $fh = fopen($file, $mode);
        if ($fh) {
            fwrite($fh, $content);
            fclose($fh);
        }
    }
    
    /**
     * 
     * Echo a string to the screen with end of line character
     * @param string $line
     */
    public static function echoLine($line)
    {
        echo $line . PHP_EOL;
    }

    /**
     * 
     * Return text based on boolean status
     * @param bool $bool
     * @param array $txt - array containing true/false keys with text to return
     * @return string
     */
    public static function returnBoolTxt($bool, $txt = array('true' => 'yes', 'false' => 'no'))
    {
        if ($bool) {
            return $txt['true'];
        } else {
            return $txt['false'];
        }
    }

    /**
     *
     * Quick CLI table function
     * @param array $data - array containing rows, keys are column names
     * @param string $mask - sprintf formatter
     */
    public static function drawTable($data, $mask = '')
    {
        Utils::echoLine("");
        $firstRow = true;
        foreach ($data as $row) {
            $titleArray = array($mask);
            $dataArray = array($mask);
            foreach ($row as $key => $value) {
                if ($firstRow) {
                    $titleArray[] = $key;
                }
                $dataArray[] = $value;
            }
            if ($firstRow) {
                call_user_func_array('printf', $titleArray);
                $titleArray = array($mask);
                foreach ($row as $value) {
                    $titleArray[] = '----------------------------------';
                }
                call_user_func_array('printf', $titleArray);
                $firstRow = false;
            }
            call_user_func_array('printf', $dataArray);
        }
        Utils::echoLine("");
    }

    /**
     * Scan given directory recursively
     * @param String $path
     * @param String $scanFileName
     * @return array 
     */
    public static function scanDirRecursively($path, $scanFileName = '')
    {
        $iter = new DirectoryIterator("./".$path);
        $data = array();

        foreach ($iter as $item) {
            if ($item->isDot()) {
                continue;
            }
            $filename = $item->getFilename();
            if ($item->isDir()) {
                $subDirData = self::scanDirRecursively($path.$filename."/", $scanFileName);
                if (!$scanFileName) { //return empty subdir when $scanFileName was not set
                    $data[$filename] = $subDirData;
                } elseif (!empty($subDirData)) { //return file only when $scanFileName matches
                    $data[$filename] = $subDirData;
                }
            } elseif ($scanFileName) {
                if (substr($filename, strlen($scanFileName)*-1) == $scanFileName) {
                    $data[$filename] = 1;
                }
            } else {
                $data[$filename] = 1;
            }
        }

        return $data;
    }
    
    /**
     * find a file in the include path
     * @param String $file
     * @return string or false
     */
    public function apiFileExists($file)
    {
        $paths = explode(PATH_SEPARATOR, get_include_path());
        foreach ($paths as $path) {
            if (file_exists("$path/$file")) {
                return "$path/$file";
            }
        }
        return false;
    }
}
