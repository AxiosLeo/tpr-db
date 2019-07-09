<?php

namespace tpr\db\core;

class FilesOpt
{
    public static function searchFile($dir, $extArray = [])
    {
        $list = [];
        if (is_dir($dir)) {
            $dir       = self::filePath($dir);
            $dirHandle = opendir($dir);
            while (false !== ($file_name = readdir($dirHandle))) {
                $subFile = $dir . $file_name;
                $tmp     = str_replace('.', '', $file_name);
                $ext     = pathinfo($file_name, PATHINFO_EXTENSION);
                if (!is_dir($subFile) && '' != $tmp && \in_array($ext, $extArray)) {
                    $list[$file_name] = $subFile;
                }
            }
            closedir($dirHandle);
        }

        return $list;
    }

    public static function searchDir($dir)
    {
        $list = [];
        if (is_dir($dir)) {
            $dir       = self::filePath($dir);
            $dirHandle = opendir($dir);
            while (false !== ($file_name = readdir($dirHandle))) {
                $subFile = $dir . $file_name;
                $tmp     = str_replace('.', '', $file_name);
                if (is_dir($subFile) && '' != $tmp) {
                    $list[$file_name] = $subFile;
                }
            }
            closedir($dirHandle);
        }

        return $list;
    }

    public static function filePath($path)
    {
        $path = \DIRECTORY_SEPARATOR != substr($path, -1) ? $path . \DIRECTORY_SEPARATOR : $path;
        if (!file_exists($path)) {
            if (!mkdir($path, 0700, true)) {
                return null;
            }
        }

        return $path;
    }

    public static function saveFile($filename, $sql, $blank = 0)
    {
        $fp = fopen($filename, 'a+');
        if (flock($fp, LOCK_EX)) {
            while ($blank > 0) {
                fwrite($fp, "\r\n");
                $blank = $blank - 1;
            }
            fwrite($fp, $sql . "\r\n");
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }

    public function clearFile($path)
    {
        if (is_dir($path)) {
            $path   = self::filePath($path);
            $handle = opendir($path);
            while (false !== ($fileName = readdir($handle))) {
                $subFile = $path . \DIRECTORY_SEPARATOR . $fileName;
                $tmp     = str_replace('.', '', $fileName);
                if ('' != $tmp && is_dir($subFile)) {
                    $this->clearFile($subFile);
                } elseif ('' != $tmp && !is_dir($subFile)) {
                    @unlink($subFile);
                }
            }
            closedir($handle);
            @rmdir($path);
        } else {
            @unlink($path);
        }
    }
}
