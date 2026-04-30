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

/**
 * Validate if the profile has been filled out
 * Returns true if valid, or a string error message if a field is missing/default
 */
function validate_profile($p) {
    // Basic Profile Info
    if (empty($p['hero']['name']) || strpos($p['hero']['name'], 'Add Name') !== false) return "Please enter your Name.";
    if (empty($p['hero']['degrees']) || strpos($p['hero']['degrees'], 'Add Degree') !== false) return "Please enter your Degree.";
    if (empty($p['hero']['image']) || strpos($p['hero']['image'], 'placehold.co') !== false) return "Please upload your Portrait Image.";
    
    // Customization & Sidebar
    if (empty($p['customization']['sidebar_title']) || strpos($p['customization']['sidebar_title'], 'Add Sidebar Title') !== false) return "Please enter your Sidebar Title.";
    
    // Contact Info
    if (empty($p['contact']['email']) || $p['contact']['email'] == 'example@email.com') return "Please enter your Email Address.";
    if (empty($p['contact']['phones'][0]) || $p['contact']['phones'][0] == '+00 00000-00000') return "Please enter your Phone Number.";
    if (empty($p['contact']['mailing_address']) || strpos($p['contact']['mailing_address'], 'Add Your Full Mailing Address Here') !== false) return "Please enter your Mailing Address.";
    
    // Education & Experience
    if (!empty($p['about']['education']) && (empty($p['about']['education'][0]['title']) || strpos($p['about']['education'][0]['title'], 'Add Degree') !== false)) return "Please enter your Education Details.";
    if (!empty($p['experience']['journey']) && (empty($p['experience']['journey'][0]['role']) || strpos($p['experience']['journey'][0]['role'], 'Add Job Role') !== false)) return "Please enter your Job Role.";
    
    // Skills
    if (!empty($p['skills']) && (empty($p['skills'][0]['category']) || strpos($p['skills'][0]['category'], 'Add Skill') !== false)) return "Please add your Technical Skills.";
    if (!empty($p['skills']) && (empty($p['skills'][0]['items']) || strpos($p['skills'][0]['items'], 'Skill 1 | Skill 2') !== false)) return "Please specify your Skills.";
    
    // Recursive check for ANY empty fields or "Add " placeholders as a catch-all
    $missing_field = null;
    array_walk_recursive($p, function($item, $key) use (&$missing_field) {
        // Skip checking the 'is_profile_setup' boolean
        if ($key === 'is_profile_setup') return;
        
        $is_placeholder = is_string($item) && strpos($item, 'Add ') === 0;
        $is_empty = is_string($item) ? trim($item) === '' : ($item === null || $item === [] || $item === '');
        
        if (($is_placeholder || $is_empty) && !$missing_field) {
            $readable_key = ucwords(str_replace('_', ' ', $key));
            $missing_field = "Please fill out the missing field: '{$readable_key}'.";
        }
    });
    
    if ($missing_field) {
        return $missing_field;
    }
    
    // Check if the overall profile setup flag is still false as a fallback
    if (isset($p['customization']['is_profile_setup']) && $p['customization']['is_profile_setup'] === false) {
        return "Please update your profile in the admin panel before downloading the CV.";
    }
    
    return true;
}
