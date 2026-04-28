<?php
// Functions to manage portfolio content

/**
 * Load content from JSON file
 */
function get_portfolio_content() {
    $content_file = __DIR__ . '/content.json';
    if (file_exists($content_file)) {
        $json = file_get_contents($content_file);
        return json_decode($json, true);
    }
    return [];
}

$p_content = get_portfolio_content();
