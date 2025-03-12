<?php

/**
 * NbFeed
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 23.06.23
 */

namespace NetBrothers\NbFeed\Service;

use NetBrothers\NbFeed\Helper\StorageHelper;

/**
 * Class ConfigService
 * @package NetBrothers\NbFeed\Service
 */
class ConfigService
{
    /** number of items to save
     *
     * set to 0 to save all
     */
    private int $maxEntriesToSave = 0;

    /** cache in seconds
     *
     * If there is a file on the hard disk that is not older than allowed, the results are pulled from the cache
     *
     * @var int Seconds
     */
    private int $cacheMaxAge = 1800;

    /** @var string|null Storage-Path for saving files */
    private ?string $storagePath = null;

    private string $feedFileName = 'nb-feed';

    public function getMaxEntriesToSave(): int
    {
        return $this->maxEntriesToSave;
    }

    public function hasMaxEntriesDefined(): bool
    {
        return $this->getMaxEntriesToSave() > 0;
    }

    public function setMaxEntriesToSave(int $maxEntriesToSave): void
    {
        $this->maxEntriesToSave = $maxEntriesToSave;
    }

    public function getCacheMaxAge(): int
    {
        return $this->cacheMaxAge;
    }

    public function setCacheMaxAge(int $cacheMaxAge): void
    {
        $this->cacheMaxAge = $cacheMaxAge;
    }

    public function getStoragePath(): string
    {
        if ($this->storagePath === null) {
            throw new \RuntimeException('Storage path ist not initialized yet.');
        }
        return $this->storagePath;
    }

    /**
     * @param string $storagePath
     * @throws \RuntimeException thrown on I/O errors
     */
    public function setStoragePath(string $storagePath): void
    {
        $this->storagePath = StorageHelper::createPath($storagePath);
    }

    /**
     * @return string target file name without file extension
     */
    public function getFeedFileName(): string
    {
        return $this->feedFileName;
    }

    /**
     * @param string $feedFileName target file name without file extension
     */
    public function setFeedFileName(string $feedFileName): void
    {
        $this->feedFileName = $feedFileName;
    }
}
