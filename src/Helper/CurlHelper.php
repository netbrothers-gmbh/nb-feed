<?php
/**
 * NbFeed
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 23.06.23
 */

namespace NetBrothers\NbFeed\Helper;

use Exception;

/**
 * Class CurlHelper
 * @package NetBrothers\NbFeed\Helper
 */
class CurlHelper
{
    /** get feed from url via curl and save response to file
     *
     * @param string $feedUrl Url to fetch
     * @param string $file Save content to this file
     * @return void
     * @throws \Exception
     */
    public static function getFeedWithCurl(string $feedUrl, string $file): void
    {
        $ch = curl_init($feedUrl);
        if ($ch === false) {
            throw new Exception('cURL error: Unable to create handle.');
        }
        $fp = fopen($file, 'w');
        if ($fp === false) {
            throw new Exception(sprintf(
                'I/O error: Unable to create file handle for %s.',
                $file
            ));
        }
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        if (curl_error($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        fclose($fp);
    }
}
