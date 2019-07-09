<?php

namespace tpr\db\core;

use ZipArchive;

class ZipFile
{
    private static $instance;

    private static $zip_path;

    private static $err_info = '';

    private $uniq;

    private $zipArchive;

    private $file_list = [];

    public function __construct()
    {
        $this->uniq       = md5(__FILE__ . uniqid(md5(microtime(true)), true));
        $this->zipArchive = new ZipArchive();
    }

    public static function instance($zip_path = null)
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        if (null !== $zip_path && $zip_path !== self::$zip_path) {
            self::$zip_path = $zip_path;
        }

        return self::$instance;
    }

    public static function clear()
    {
        self::$instance = null;
    }

    public function setZipPath($zip_path)
    {
        self::$zip_path = $zip_path;

        return $this;
    }

    public function zip($save_path, $clear_data_file = false)
    {
        if (is_dir($save_path)) {
            throw new \Exception('save_path must be zip file : ' . $save_path);
        }
        $dir = basename($save_path);
        $this->makeDir($dir);
        $this->zipArchive = new ZipArchive();
        if (true !== $this->zipArchive->open($save_path, ZipArchive::CREATE)) {
            throw new \Exception('can not open ' . $save_path);
        }

        $this->addFileList(self::$zip_path, ['.DS_Store']);
        foreach ($this->file_list as $filename) {
            $key = str_replace(self::$zip_path, '', $filename);
            $this->zipArchive->addFile($filename, $key);
        }
        if ($clear_data_file) {
            $this->clearFile(self::$zip_path);
        }

        return $this->zipArchive->close();
    }

    public function getError()
    {
        return self::$err_info;
    }

    private function addFileList($path, $exception = [])
    {
        $path = '/' == substr($path, -1) ? substr($path, 0, -1) : $path;
        if (is_dir($path)) {
            $dirHandle = opendir($path);
            while (false !== ($fileName = readdir($dirHandle))) {
                $subFile = $path . \DIRECTORY_SEPARATOR . $fileName;
                $tmp     = str_replace('.', '', $fileName);
                if (!is_dir($subFile) && '' != $tmp && !\in_array($fileName, $exception)) {
                    $this->file_list[] = $subFile;
                } elseif (is_dir($subFile) && '' != $tmp) {
                    $this->addFileList($subFile, $exception);
                }
            }
            closedir($dirHandle);
        } else {
            $this->file_list[] = $path;
        }
    }

    private function makeDir($path)
    {
        $path = \DIRECTORY_SEPARATOR != substr($path, -1) ? $path . \DIRECTORY_SEPARATOR : $path;
        if (!file_exists($path)) {
            if (!mkdir($path, 0700, true)) {
                return null;
            }
        }

        return $path;
    }

    private function clearFile($path)
    {
        if (is_dir($path)) {
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
