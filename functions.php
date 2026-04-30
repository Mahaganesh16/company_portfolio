<?php
// Functions to manage portfolio content

/**
 * Load content from JSON file
 */
function get_portfolio_content() {
    $static_file = __DIR__ . '/content.json';
    $update_file = __DIR__ . '/update_content.json';
    
    // Prioritize updated content if it exists
    if (file_exists($update_file)) {
        $json = file_get_contents($update_file);
        return json_decode($json, true);
    }
    
    // Fallback to static content
    if (file_exists($static_file)) {
        $json = file_get_contents($static_file);
        return json_decode($json, true);
    }
    return [];
}

$p_content = get_portfolio_content();
