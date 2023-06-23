<?php
/**
 * NbFeed
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 23.06.23
 */

namespace NetBrothers\NbFeed\Service;
use NetBrothers\NbFeed\Helper\StorageHelper;
use NetBrothers\NbFeed\Helper\CurlHelper;

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
     * @return array
     * @throws \Exception
     */
    public function getFeed(string $feedUrl = 'https://www.heise.de/security/rss/alert-news.rdf', bool $useCache = true): array
    {
        $feedFile = $this->setFeed($feedUrl, $useCache);
        return json_decode(file_get_contents($feedFile));
    }

    /** get Feed from Server and save
     *
     * @param string $feedUrl use url to fetch feed
     * @param bool $useCache use cache
     * @return string Json file with content
     * @throws \Exception
     */
    public function setFeed(string $feedUrl = 'https://www.heise.de/security/rss/alert-news.rdf', bool $useCache = true): string
    {
        $feedFile = $this->configService->getStoragePath() . $this->configService->getFeedFileName();
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
     * @throws \RuntimeException | \Exception
     */
    private function saveFeedFromExtern(string $feedUrl): void
    {
        if (null === $this->configService->getStoragePath()) {
            throw new \RuntimeException('No storage configured');
        }
        $oldFile = $this->configService->getStoragePath() . $this->configService->getFeedFileName();
        $newFile = $this->configService->getStoragePath() . 'new-'. $this->configService->getFeedFileName();
        StorageHelper::removeFile($newFile);
        CurlHelper::getFeedWithCurl($feedUrl, $newFile);
        $output = $this->formatFeed($newFile);
        try {
            $json = json_encode($output);
            StorageHelper::removeFile($oldFile);
            file_put_contents($oldFile, $json);
        } catch (\Exception $exception) {
            throw new \RuntimeException('Cannot save response', 500, $exception);
        }
    }

    private function formatFeed(string $scrFile): array
    {
        $output = [];
        $xml = simplexml_load_file($scrFile);
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

    private function formatXmlEntry(\SimpleXMLElement $element): array
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
