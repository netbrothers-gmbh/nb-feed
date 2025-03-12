<?php

/**
 * NbFeed
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 23.06.23
 */

namespace NetBrothers\NbFeed\Service;

use Laminas\Feed\Reader\Entry\EntryInterface;
use Laminas\Feed\Reader\Exception\InvalidHttpClientException;
use Laminas\Feed\Reader\Exception\RuntimeException as LaminasFeedReaderRuntimeException;
use Laminas\Feed\Reader\Reader;
use NetBrothers\NbFeed\Adapter\HttpClientAdapter;
use NetBrothers\NbFeed\Helper\StorageHelper;

use function file_exists;

class FeedService
{
    public function __construct(private ConfigService $configService)
    {
    }

    /**
     * Get feed content as array.
     *
     * @param string $feedUrl use url to fetch feed
     * @param bool $useCache use cache
     * @return array<int, array{
     *     authors: null|iterable,
     *     content: string,
     *     dateCreated: null|int,
     *     dateModified: null|int,
     *     description: string,
     *     title: string,
     *     link: string,
     *     permaLink: string,
     *     pubDate: int }>
     * @throws \Exception on deserialisation errors while reading cached data from disk
     * @throws \RuntimeException on errors while removing the cache file
     * @throws InvalidHttpClientException feed reader exception
     * @throws LaminasFeedReaderRuntimeException feed reader exception
     * @phpstan-ignore-next-line missingType.iterableValue This concerns authors (iterable) but we have to rely on the feed reader spec.
     */
    public function getFeed(string $feedUrl, bool $useCache = true): array
    {
        $feedFile = $this->refreshFeed($feedUrl, $useCache);
        $feedContent = file_get_contents($feedFile);
        $parseErrorMsg = sprintf(
            'FeedService error: Unable to parse contents of %s.',
            $feedFile
        );
        if ($feedContent === false) {
            throw new \Exception($parseErrorMsg);
        }
        $result = json_decode($feedContent, true);
        if (!is_array($result)) {
            throw new \Exception($parseErrorMsg);
        }
        // @phpstan-ignore-next-line return.type We have to rely on the fact that the deserialised JSON data is returned as once serialised.
        return $result;
    }

    /**
     * @param string $feedUrl
     * @return array<int, array{
     *     authors: null|iterable,
     *     content: string,
     *     dateCreated: null|int,
     *     dateModified: null|int,
     *     description: string,
     *     title: string,
     *     link: string,
     *     permaLink: string,
     *     pubDate: int }>
     * @throws InvalidHttpClientException 
     * @throws LaminasFeedReaderRuntimeException
     * @phpstan-ignore-next-line missingType.iterableValue This concerns authors (iterable) but we have to rely on the feed reader spec.
     */
    private function fetchFromRemote(string $feedUrl): array
    {
        Reader::setHttpClient(new HttpClientAdapter());
        $counter = 0;
        $isLimited = $this->configService->hasMaxEntriesDefined();
        $limit = $this->configService->getMaxEntriesToSave();
        $data = [];
        foreach (Reader::import($feedUrl) as $entry) {
            if ($isLimited && ++$counter > $limit) {
                break;
            }
            if (! $entry instanceof EntryInterface) {
                break;
            }
            /**
             * Structure of the legacy array shape:
             * {
             *     title: string,
             *     pubDate: int,
             *     link: string,
             *     permaLink: string,
             *     content: string
             * }
             *
             * Structure of the new array shape:
             * {
             *     authors: null|iterable,  // **NEW**
             *     content: string,         // **CHANGED** (actual content)
             *     dateCreated: int,        // **NEW**
             *     dateModified: int,       // **NEW**
             *     description: string,     // **NEW** (actual description)
             *     title: string,
             *     link: string,
             *     permaLink: string,
             *     pubDate: int,            // **DEPRECATED**
             * }
             */
            $item = [
                'authors' => $entry->getAuthors(),
                'content' => $entry->getContent(),
                'dateCreated' => $entry->getDateCreated()?->getTimestamp(),
                'dateModified' => $entry->getDateModified()?->getTimestamp(),
                'description' => $entry->getDescription(),
                'link' => $entry->getLink(),
                'permaLink' => $entry->getPermalink(),
                'pubDate' => $entry->getDateModified()?->getTimestamp() ?? 0,
                'title' => $entry->getTitle(),
            ];
            $data[] = $item;
        }
        return $data;
    }

    private function getFeedStoragePath(): string
    {
        return sprintf(
            '%s%s%s',
            $this->configService->getStoragePath(),
            $this->configService->getFeedFileName(),
            '.json'
        );
    }

    /**
     * If necessary get feed from the remote source and, transform in into
     * the nb-feed JSON format and save it to disk, otherwise (cached result is
     * still good) return content directly from disk.
     * 
     * @param string $feedUrl the feed URL
     * @param bool $useCache enable or disable cache
     * @return string file path of the JSON file with the updated content
     * @throws \RuntimeException storage helper (remove file)
     * @throws InvalidHttpClientException (feed reader)
     * @throws LaminasFeedReaderRuntimeException (feed reader)
     */
    private function refreshFeed(string $feedUrl, bool $useCache): string
    {
        $feedFile = $this->getFeedStoragePath();

        /**
         * If the cache is disabled or the feed file has been deleted or is
         * older than max age, we will fetch the feed from the remote source,
         */
        if (
            !$useCache
            || !file_exists($feedFile)
            || filemtime($feedFile) < (time() - $this->configService->getCacheMaxAge())
        ) {
            $feedData = $this->fetchFromRemote($feedUrl);
            $feedFile = $this->getFeedStoragePath();
            StorageHelper::removeFile($feedFile);
            file_put_contents($feedFile, json_encode($feedData));
        }
        // otherwise we use the local version.
        return $feedFile;
    }
}
