<?php

/**
 * NbFeed
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 23.06.23
 */

use NetBrothers\NbFeed\Service\ConfigService;
use NetBrothers\NbFeed\Service\FeedService;

/**
 * To use this example, call `composer install --no-dev` after the checkout.
 * The script tries to load the `autoload.php` automatically. If this does not
 * work, set the path manually.
 */
$libModeAutoload = __DIR__ . '/../../../autoload.php';
if (file_exists($libModeAutoload)) {
    // when used as a vendor package
    require $libModeAutoload;
} else {
    // when used entirely standalone (e.g. git clone)
    require __DIR__ . '/../vendor/autoload.php';
}

/**
 * How you set the configuration might depend on the PHP and/or DI framework you
 * are using. For demonstration purposes, the configuration is set manually in
 * this example.
 */

// URL RSS-Feed (required)
$feedUrl = 'https://www.heise.de/security/rss/alert-news.rdf';

// Initialize `ConfigService`
$configService = new ConfigService();
$configService->setStoragePath(__DIR__ . '/../tmp');
$configService->setFeedFileName('heise-security');
$configService->setCacheMaxAge(300);

// Instantiating the `FeedService`
$feedService = new FeedService($configService);

/**
 * Retrieve the feed:
 * - As we have never loaded anything before, the feed is now being pulled,
 *   transformed and saved to disk.
 * - If there is a file on the hard disk that is **not** older than allowed, the
 *   results are pulled from the cache.
 */

$feedArray = $feedService->getFeed($feedUrl, true);
print PHP_EOL;
var_dump($feedArray);
print PHP_EOL;
unset($feedArray);
