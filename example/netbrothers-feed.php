<?php

/**
 * NbFeed
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 23.06.23
 */

/**
 * To use this example, call `composer install` after the checkout.
 * The script tries to load the `autoload.php` automatically. If this does not work, please set the path manually.
 */
$libModeAutoload = __DIR__ . '/../../../autoload.php';
if (file_exists($libModeAutoload)) {
    // used as a binary script of a composer package (vendor/bin/nb-feed)
    require $libModeAutoload;
} else {
    // used entirely standalone (e.g. git clone)
    require __DIR__ . '/../vendor/autoload.php';
}

/**
 * Make some Configurations
 * How you set the configuration depends on the system you are using. Here, as an example, simply by hand.
 */

// URL RSS-Feed (required)
$feedUrl = 'https://www.heise.de/security/rss/alert-news.rdf';

// Init ConfigService and set some values
$configService = new \NetBrothers\NbFeed\Service\ConfigService();
$configService->setStoragePath(__DIR__ . '/../tmp');
$configService->setFeedFileName('heise-security');
$configService->setCacheMaxAge(300);

/**
 * Now using the logic
 */
$feedService = new \NetBrothers\NbFeed\Service\FeedService($configService);

/**
 * Now getting the Feeds:
 *  => As we have never loaded anything before, the feed is now being pulled, manipulated and saved on disk
 *  => If there is a file on the hard disk that is not older than allowed, the results are pulled from the cache.
 */
$feedArray = $feedService->getFeed($feedUrl, true);
print PHP_EOL;
var_dump($feedArray);
print PHP_EOL;
unset($feedArray);
