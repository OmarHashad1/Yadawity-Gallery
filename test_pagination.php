<?php
// Test pagination with both page and offset parameters
echo "<h2>Testing Pagination Fix</h2>";

echo "<h3>Test 1: Using page parameter (page=1, limit=5)</h3>";
$api_url1 = 'http://localhost/API/getAllArtworks.php?page=1&limit=5';
$response1 = file_get_contents($api_url1);
if ($response1) {
    $data1 = json_decode($response1, true);
    if ($data1 && isset($data1['pagination'])) {
        echo "<pre>";
        echo "Returned count: " . $data1['returned_count'] . "\n";
        echo "Total count: " . $data1['total_count'] . "\n";
        echo "Pagination info:\n";
        print_r($data1['pagination']);
        echo "</pre>";
    }
}

echo "<h3>Test 2: Using page parameter (page=2, limit=5)</h3>";
$api_url2 = 'http://localhost/API/getAllArtworks.php?page=2&limit=5';
$response2 = file_get_contents($api_url2);
if ($response2) {
    $data2 = json_decode($response2, true);
    if ($data2 && isset($data2['pagination'])) {
        echo "<pre>";
        echo "Returned count: " . $data2['returned_count'] . "\n";
        echo "Total count: " . $data2['total_count'] . "\n";
        echo "Pagination info:\n";
        print_r($data2['pagination']);
        echo "</pre>";
    }
}

echo "<h3>Test 3: Compare artwork IDs to ensure different pages</h3>";
if ($response1 && $response2) {
    $data1 = json_decode($response1, true);
    $data2 = json_decode($response2, true);
    
    echo "<strong>Page 1 artwork IDs:</strong> ";
    if (isset($data1['data'])) {
        $ids1 = array_map(function($artwork) { return $artwork['artwork_id']; }, $data1['data']);
        echo implode(', ', $ids1);
    }
    echo "<br>";
    
    echo "<strong>Page 2 artwork IDs:</strong> ";
    if (isset($data2['data'])) {
        $ids2 = array_map(function($artwork) { return $artwork['artwork_id']; }, $data2['data']);
        echo implode(', ', $ids2);
    }
    echo "<br>";
    
    if (isset($ids1) && isset($ids2)) {
        $overlap = array_intersect($ids1, $ids2);
        echo "<strong>Overlap (should be empty):</strong> " . (empty($overlap) ? "None (Good!)" : implode(', ', $overlap));
    }
}
?>
