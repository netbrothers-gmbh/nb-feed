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

    /** How many items to save
     *
     * set to 0 to save all
     *
     * @var int
     */
    private int $maxEntriesToSave = 0;

    /** cache in seconds
     *
     * If there is a file on the hard disk that is not older than allowed, the results are pulled from the cache
     *
     * @var int Seconds
     */
    private int $cacheMaxAge = 1800;


    /** Storage-Path for saving files
     *
     * @var string|null
     */
    private ?string $storagePath = null;

    /**
     * @var string
     */
    private string $feedFileName = 'nb-feed';

    /**
     * @return int
     */
    public function getMaxEntriesToSave(): int
    {
        return $this->maxEntriesToSave;
    }

    /**
     * @param int $maxEntriesToSave
     */
    public function setMaxEntriesToSave(int $maxEntriesToSave): void
    {
        $this->maxEntriesToSave = $maxEntriesToSave;
    }

    /**
     * @return int
     */
    public function getCacheMaxAge(): int
    {
        return $this->cacheMaxAge;
    }

    /**
     * @param int $cacheMaxAge
     */
    public function setCacheMaxAge(int $cacheMaxAge): void
    {
        $this->cacheMaxAge = $cacheMaxAge;
    }

    /**
     * @return string|null
     */
    public function getStoragePath(): ?string
    {
        return $this->storagePath;
    }

    /**
     * @param string $storagePath
     * @throws \RuntimeException
     */
    public function setStoragePath(string $storagePath): void
    {
        $this->storagePath = StorageHelper::createPath($storagePath);
    }

    /**
     * @return string
     */
    public function getFeedFileName(): string
    {
        return $this->feedFileName;
    }

    /**
     * @param string $feedFileName
     */
    public function setFeedFileName(string $feedFileName): void
    {
        $this->feedFileName = $feedFileName;
    }

}
