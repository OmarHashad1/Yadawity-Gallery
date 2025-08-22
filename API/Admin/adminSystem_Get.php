<?php
include_once 'db.php';

try {
    $info_type = $_GET['type'] ?? 'overview'; // overview, database, files, performance
    
    $systemInfo = [];
    
    if ($info_type === 'overview' || $info_type === 'all') {
        // Database Statistics
        $dbStats = [];
        
        // Get table sizes and row counts
        $tables = ['users', 'artworks', 'auctions', 'orders', 'courses', 'galleries', 'cart', 'wishlists'];
        
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM $table");
                $stmt->execute();
                $count = $stmt->fetch()['count'];
                
                $dbStats[$table] = [
                    'rows' => (int)$count,
                    'table_name' => ucfirst($table)
                ];
            } catch (Exception $e) {
                $dbStats[$table] = [
                    'rows' => 0,
                    'table_name' => ucfirst($table),
                    'error' => 'Unable to fetch count'
                ];
            }
        }
        
        $systemInfo['database'] = $dbStats;
        
        // System Overview
        $systemInfo['overview'] = [
            'php_version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'current_time' => date('Y-m-d H:i:s'),
            'timezone' => date_default_timezone_get(),
            'memory_limit' => ini_get('memory_limit'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_execution_time' => ini_get('max_execution_time')
        ];
    }
    
    if ($info_type === 'database' || $info_type === 'all') {
        // Detailed Database Information
        try {
            // Database size
            $dbSizeStmt = $pdo->prepare("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb'
                FROM information_schema.tables 
                WHERE table_schema = 'yadawity'
            ");
            $dbSizeStmt->execute();
            $dbSize = $dbSizeStmt->fetch()['size_mb'] ?? 0;
            
            // Table details
            $tableDetailsStmt = $pdo->prepare("
                SELECT 
                    table_name,
                    table_rows,
                    ROUND((data_length + index_length) / 1024 / 1024, 2) AS 'size_mb',
                    engine,
                    table_collation
                FROM information_schema.tables 
                WHERE table_schema = 'yadawity'
                ORDER BY (data_length + index_length) DESC
            ");
            $tableDetailsStmt->execute();
            $tableDetails = $tableDetailsStmt->fetchAll();
            
            $systemInfo['database_details'] = [
                'total_size_mb' => (float)$dbSize,
                'tables' => $tableDetails
            ];
            
        } catch (Exception $e) {
            $systemInfo['database_details'] = [
                'error' => 'Unable to fetch database details: ' . $e->getMessage()
            ];
        }
    }
    
    if ($info_type === 'performance' || $info_type === 'all') {
        // Performance Metrics
        $performance = [];
        
        // Recent activity performance
        try {
            $activityStmt = $pdo->prepare("
                SELECT 
                    'Users' as metric,
                    COUNT(*) as total,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 END) as last_24h,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as last_7d
                FROM users
                UNION ALL
                SELECT 
                    'Artworks' as metric,
                    COUNT(*) as total,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 END) as last_24h,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as last_7d
                FROM artworks
                UNION ALL
                SELECT 
                    'Orders' as metric,
                    COUNT(*) as total,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 END) as last_24h,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as last_7d
                FROM orders
            ");
            $activityStmt->execute();
            $activityMetrics = $activityStmt->fetchAll();
            
            $performance['activity'] = $activityMetrics;
            
            // Server load simulation (since we can't get real server load in this environment)
            $performance['server'] = [
                'memory_usage' => memory_get_usage(true),
                'memory_peak' => memory_get_peak_usage(true),
                'memory_limit' => ini_get('memory_limit'),
                'process_time' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]
            ];
            
        } catch (Exception $e) {
            $performance['error'] = 'Unable to fetch performance metrics: ' . $e->getMessage();
        }
        
        $systemInfo['performance'] = $performance;
    }
    
    if ($info_type === 'files' || $info_type === 'all') {
        // File System Information
        $fileInfo = [];
        
        try {
            // Check if upload directory exists and is writable
            $uploadDir = '../image/'; // Adjust path as needed
            
            $fileInfo['upload_directory'] = [
                'path' => $uploadDir,
                'exists' => file_exists($uploadDir),
                'writable' => is_writable($uploadDir),
                'permissions' => file_exists($uploadDir) ? substr(sprintf('%o', fileperms($uploadDir)), -4) : 'N/A'
            ];
            
            // Count image files (if directory exists)
            if (file_exists($uploadDir)) {
                $imageFiles = glob($uploadDir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
                $fileInfo['image_count'] = count($imageFiles);
                
                // Calculate total size
                $totalSize = 0;
                foreach ($imageFiles as $file) {
                    $totalSize += filesize($file);
                }
                $fileInfo['total_size_mb'] = round($totalSize / 1024 / 1024, 2);
            } else {
                $fileInfo['image_count'] = 0;
                $fileInfo['total_size_mb'] = 0;
            }
            
        } catch (Exception $e) {
            $fileInfo['error'] = 'Unable to fetch file information: ' . $e->getMessage();
        }
        
        $systemInfo['files'] = $fileInfo;
    }
    
    sendResponse(true, 'System information retrieved successfully', $systemInfo);

} catch (Exception $e) {
    sendResponse(false, 'Error retrieving system information: ' . $e->getMessage(), null, 500);
}
?>
