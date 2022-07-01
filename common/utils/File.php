<?php


namespace app\common\utils;

/**
 * 文件
 */
class File
{
    /**
     * 删除目录下的所有文件
     * @param string $dir       要删除的文件所在的目录
     * @param bool   $removeDir 是否删除目录
     */
    public static function deleteFiles(string $dir, bool $removeDir = true)
    {
        //先删除目录下的文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullPath = $dir . "/" . $file;
                if (!is_dir($fullPath)) {
                    unlink($fullPath);
                } else {
                    if ($removeDir) {
                        self::deleteFiles($fullPath, true);
                        rmdir($fullPath);
                    }
                }
            }
        }
        closedir($dh);
    }
}
