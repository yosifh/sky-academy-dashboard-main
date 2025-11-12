<?php
/**
 * API Helper Functions
 * Handles all HTTP requests to Supabase Edge Functions
 */

/**
 * Make an API request to Supabase Edge Function
 * 
 * @param string $method HTTP method (GET, POST, PATCH, DELETE)
 * @param string $url Full URL to the endpoint
 * @param string|null $token Authorization token
 * @param array|null $payload Request body data
 * @return array Response with http_code, data, and raw_response
 */
function api_request($method, $url, $token = null, $payload = null) {
    $ch = curl_init($url);
    
    // Set common options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    // Build headers
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // Add payload for POST/PATCH requests
    if (in_array($method, ['POST', 'PATCH', 'PUT']) && $payload !== null) {
        $json_payload = json_encode($payload);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
    }
    
    // Execute request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    // Handle curl errors
    if ($response === false) {
        return [
            'http_code' => 0,
            'data' => ['error' => 'CURL Error: ' . $curl_error],
            'raw_response' => null
        ];
    }
    
    // Parse JSON response
    $data = json_decode($response, true);
    
    // Return structured response
    return [
        'http_code' => $http_code,
        'data' => is_array($data) ? $data : [],
        'raw_response' => $response
    ];
}

/**
 * Upload image to Supabase Storage
 * 
 * @param string $base64_image Base64 encoded image
 * @param string $mime_type MIME type of the image
 * @param string $filename Original filename
 * @param string $bucket Storage bucket name
 * @param string $token Authorization token
 * @return array Response with success status and publicUrl
 */
function upload_image($base64_image, $mime_type, $filename, $bucket, $token) {
    $endpoint = 'https://qxkyfdasymxphjjzxwfn.supabase.co/functions/v1/admin-courses-lectures-teachers';
    $url = $endpoint . '?resource=images&action=upload';
    
    $payload = [
        'image_base64' => $base64_image,
        'image_mime_type' => $mime_type,
        'image_file_name' => $filename,
        'bucket' => $bucket
    ];
    
    return api_request('POST', $url, $token, $payload);
}

/**
 * Format price with currency
 * 
 * @param float $price Price value
 * @param string $currency Currency symbol
 * @return string Formatted price
 */
function format_price($price, $currency = 'د.ع') {
    return number_format($price, 2) . ' ' . $currency;
}

/**
 * Truncate text to specified length
 * 
 * @param string $text Text to truncate
 * @param int $length Maximum length
 * @param string $suffix Suffix to add if truncated
 * @return string Truncated text
 */
function truncate_text($text, $length = 50, $suffix = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Get course type badge class
 * 
 * @param int $type_id Course type ID
 * @return string CSS classes for badge
 */
function get_course_type_badge($type_id) {
    return $type_id == 1 ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800';
}

/**
 * Sanitize HTML output
 * 
 * @param string $text Text to sanitize
 * @return string Sanitized text
 */
function safe_html($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}