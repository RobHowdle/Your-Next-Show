<?php

if (!function_exists('determinePlatform')) {
    /**
     * Determine the platform from a URL.
     *
     * @param string $urls
     * @return string
     */
    function determinePlatform($urls)
    {
        // Define the platforms we're interested in
        $platforms = ['facebook', 'x', 'instagram', 'tiktok', 'youtube', 'snapchat', 'bluesky'];
        $result = [];

        // Split the input URLs by commas
        $urlArray = explode(',', $urls);

        // Loop through the URLs and determine the platform
        foreach ($urlArray as $url) {
            $url = trim($url); // Remove extra spaces
            foreach ($platforms as $platform) {
                // Check if the platform is in the URL
                if (strpos($url, $platform) !== false) {
                    // If the platform has not been added yet, add it
                    if (!isset($result[$platform])) {
                        $result[$platform] = $url;
                    }
                }
            }
        }

        // Return the result as a JSON string
        return json_encode($result, JSON_UNESCAPED_SLASHES);
    }
}