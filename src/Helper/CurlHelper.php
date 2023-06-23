<?php
/**
 * NbFeed
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 23.06.23
 */

namespace NetBrothers\NbFeed\Helper;

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
        $fp = fopen($file, "w");
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        if(curl_error($ch)) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);
        fclose($fp);
    }
}
