<?php

/**
 * NbFeed
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 23.06.23
 */

namespace NetBrothers\NbFeed\Service;

use Exception;
use NetBrothers\NbFeed\Helper\StorageHelper;
use NetBrothers\NbFeed\Helper\CurlHelper;
use SimpleXMLElement;

/**
 * Class FeedService
 * @package NetBrothers\NbFeed\Service
 */
class FeedService
{
    private ConfigService $configService;

    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    /** get Feed as array
     *
     * @param string $feedUrl use url to fetch feed
     * @param bool $useCache use cache
     * @return array<int, array<string, mixed>>
     * @throws Exception thrown on parse errors
     */
    public function getFeed(string $feedUrl, bool $useCache = true): array
    {
        $feedFile = $this->writeFeedToDisk($feedUrl, $useCache);
        $parseErrorMsg = sprintf(
            'FeedService error: Unable to parse contents of %s.',
            $feedFile
        );
        $feedContent = file_get_contents($feedFile);
        if ($feedContent === false) {
            throw new Exception($parseErrorMsg);
        }
        $result = json_decode($feedContent, true);
        if (!is_array($result)) {
            throw new Exception($parseErrorMsg);
        }
        return $result;
    }

    /** get Feed from Server and save
     *
     * @param string $feedUrl use url to fetch feed
     * @param bool $useCache use cache
     * @return string file path of the JSON file with the new content
     * @throws Exception thrown on parse errors
     */
    public function writeFeedToDisk(string $feedUrl, bool $useCache = true): string
    {
        $feedFile = $this->configService->getStoragePath() . $this->configService->getFeedFileName() . '.json';
        if (true !== $useCache) {
            $this->saveFeedFromExtern($feedUrl);
        } elseif(!file_exists($feedFile) or filemtime($feedFile) < (time() - $this->configService->getCacheMaxAge())) {
            $this->saveFeedFromExtern($feedUrl);
        }
        return $feedFile;
    }

    /**
     * @param string $feedUrl
     * @return void
     * @throws \RuntimeException | Exception
     */
    private function saveFeedFromExtern(string $feedUrl): void
    {
        if (null === $this->configService->getStoragePath()) {
            throw new \RuntimeException('No storage configured');
        }
        $baseFileName = $this->configService->getFeedFileName();
        $jsonFile = $this->configService->getStoragePath() . $baseFileName . '.json';
        $rawXmlDataFile = $this->configService->getStoragePath() . $baseFileName . '.rss';
        StorageHelper::removeFile($rawXmlDataFile);
        CurlHelper::getFeedWithCurl($feedUrl, $rawXmlDataFile);
        $feedData = $this->formatFeed($rawXmlDataFile);
        try {
            StorageHelper::removeFile($jsonFile);
            file_put_contents($jsonFile, json_encode($feedData));
        } catch (Exception $exception) {
            throw new \RuntimeException('Cannot save response', 500, $exception);
        }
    }

    /**
     * @param string $rawXmlDataFile
     * @return array<int, array<string, mixed>>
     * @throws Exception thrown on parse errors
     */
    private function formatFeed(string $rawXmlDataFile): array
    {
        $output = [];
        $xml = simplexml_load_file($rawXmlDataFile);
        if ($xml === false) {
            throw new Exception(sprintf(
                'FeedService error: Unable to parse XML contents of %s.',
                $rawXmlDataFile
            ));
        }
        $entries = $xml->channel->item;
        $counter = 0;
        foreach($entries as $root) {
            if (0 < $this->configService->getMaxEntriesToSave()) {
                $counter++;
                if($counter > $this->configService->getMaxEntriesToSave()) {
                    break;
                }
            }
            $output[] = $this->formatXmlEntry($root);
        }
        return $output;
    }

    /**
     * @param SimpleXMLElement $element
     * @return array<string, mixed>
     */
    private function formatXmlEntry(SimpleXMLElement $element): array
    {
        $output = [
            'title' => (string) $element->title,
            'pubDate' => strtotime($element->pubDate),
            'link' => (string) $element->link,
            'permaLink' => null,
            'content' => (string) $element->description
        ];
        $guid = $element->guid;
        if ($guid && $guid['isPermaLink'] && boolval($guid['isPermaLink']) === true) {
            $output['permaLink'] = (string) $guid;
        }
        return $output;
    }
}
