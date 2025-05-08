<?php

namespace App\Helpers;

class SocialLinksHelper
{
    /**
     * Decode social links JSON and extract platforms and URLs.
     *
     * @param string|null $contactLink
     * @return array
     */
    public static function processSocialLinks($contactLink)
    {
        if (empty($contactLink)) {
            return [];
        }

        // Decode JSON string if not already an array
        $links = is_string($contactLink) ? json_decode($contactLink, true) : $contactLink;

        if (!$links || !is_array($links)) {
            return [];
        }

        $platforms = [];

        foreach ($links as $platform => $url) {
            // Handle cases where URL is an array
            if (is_array($url)) {
                // Use first non-empty value in array
                foreach ($url as $singleUrl) {
                    if (!empty($singleUrl) && filter_var($singleUrl, FILTER_VALIDATE_URL)) {
                        $platforms[] = [
                            'platform' => strtolower($platform),
                            'url' => $singleUrl
                        ];
                        break;  // Only use first valid URL
                    }
                }
            }
            // Handle case where URL is a direct string
            elseif (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                $platforms[] = [
                    'platform' => strtolower($platform),
                    'url' => $url
                ];
            }
        }

        return $platforms;
    }
}
