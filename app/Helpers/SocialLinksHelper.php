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
            // Skip invalid URLs
            if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }

            // Convert to standard format
            $platforms[] = [
                'platform' => strtolower($platform),
                'url' => $url
            ];
        }

        return $platforms;
    }
}
