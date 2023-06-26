![build status](https://github.com/netbrothers-gmbh/nb-feed/actions/workflows/build-workflow.yml/badge.svg)

# NetBrothers NbFeed

This library pulls an RSS feed to your hard disk, transforms the items to an
array and saves the results as a JSON file on your hard disk.

## Installation

On the command prompt, change into your project's root directory and execute:

```console
composer require netbrothers-gmbh/nb-feed
```

## Configuration

*NbFeed* needs a readable and writeable directory. All other configurations
are optional. The configuration depends on your environment. See the content of
[ConfigService](./src/Service/ConfigService.php).

| Variable           | Description                                         | Default  |
|--------------------|-----------------------------------------------------|----------|
| *maxEntriesToSave* | maximum number of feed items to be saved            | 0 (all)  |
| *cacheMaxAge*      | maximum caching time of the file (in seconds)       | 1800 sec |
| *storagePath*      | absolute path to a readable and writeable directory | null     |
| *feedFileName*     | name of the file to write to/read from              | nb-feed  |

## Example

There is an [example file](./example/netbrothers-feed.php). To use it:

1. Checkout the repository,
2. run `composer install --no-dev`,
3. run the example: `php ./example/netbrothers-feed.php`.

Feel free to use the example file as a starting point for your own purposes.

## Licence

MIT

## Authors

- [Stefan Wessel, NetBrothers GmbH](https://netbrothers.de)
- [Thilo Ratnaweera, NetBrothers GmbH](https://netbrothers.de)

[![nb.logo](https://netbrothers.de/wp-content/uploads/2020/12/netbrothers_logo.png)](https://netbrothers.de)
