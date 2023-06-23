# NetBrothers NbFeed
This library pulls RSS-Feed to your hard disk, transforms the items to array and save the result as json on hard disk.

## Installation
On the command prompt, change into your project's root directory and execute:

```console
composer require netbrothers-gmbh/nb-feed
```

## Configuration
*NbFeed* needs a readable and writeable directory. All other configurations are optional.
How to configure depends on your environment. See the content of [ConfigService](./src/Service/ConfigService.php).

| Variable           | Description                                         | Default  |
|--------------------|-----------------------------------------------------|----------|
| *maxEntriesToSave* | How many items from the feed should be saved.       | 0 (all)  |
| *cacheMaxAge*      | How long should the file been cached                | 1800 sec |
| *storagePath*      | Absolute path to a readable and writeable directory | null     |
| *feedFileName*     | Name of the file to write to/read from              | nb-feed  |

## Example
There is an example under [netbrothers-feed.php](./example/netbrothers-feed.php). To use it:
1. Checkout the repository
2. Call `composer install` after the checkout
3. Enter `php ./example/netbrothers-feed.php`
4. Feel free to manipulate the file [netbrothers-feed.php](./example/netbrothers-feed.php)

## Licence

MIT

## Authors

- [Stefan Wessel, NetBrothers GmbH](https://netbrothers.de)
- [Thilo Ratnaweera, NetBrothers GmbH](https://netbrothers.de)

[![nb.logo](https://netbrothers.de/wp-content/uploads/2020/12/netbrothers_logo.png)](https://netbrothers.de)
