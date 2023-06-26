<?php

/**
 * NbFeed
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 23.06.23
 */

namespace NetBrothers\NbFeed\Helper;

/**
 * Class StorageHelper
 * @package NetBrothers\NbFeed\Helper
 */
class StorageHelper
{
    /**
     * @param string $path
     * @return string
     * @throws \RuntimeException
     */
    public static function createPath(string $path): string
    {
        if (!is_dir($path) && !mkdir($path, 0777, true)) {
            throw new \RuntimeException(sprintf('Cannot create %s', $path));
        }
        if (!(is_writable($path) && is_readable($path))) {
            throw new \RuntimeException(sprintf('Check permissions for writing and/or reading in %s', $path));
        }
        return (true !== str_ends_with($path, "/")) ? $path . "/" : $path;
    }

    /**
     * @param string $file
     * @return void
     * @throws \RuntimeException
     */
    public static function removeFile(string $file): void
    {
        if (file_exists($file)) {
            if (is_readable($file) && is_writable($file)) {
                unlink($file);
            } else {
                throw new \RuntimeException('Cannot remove file: ' . $file);
            }
        }
    }
}
