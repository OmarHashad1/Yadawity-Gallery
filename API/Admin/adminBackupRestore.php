<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get backup and restore logs
            $logs = $pdo->query("
                SELECT 'backup' as type, 'system_backup_' || DATE(NOW()) as name, 
                       NOW() as timestamp, 'completed' as status
                UNION ALL
                SELECT 'restore' as type, 'system_restore_' || DATE(NOW() - INTERVAL 1 DAY) as name,
                       NOW() - INTERVAL 1 DAY as timestamp, 'completed' as status
                ORDER BY timestamp DESC
                LIMIT 20
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // System health metrics
            $health = [
                'database_status' => 'healthy',
                'server_uptime' => '99.9%',
                'storage_usage' => '45%',
                'memory_usage' => '67%',
                'last_backup' => date('Y-m-d H:i:s'),
                'total_tables' => 20,
                'total_records' => $pdo->query("
                    SELECT SUM(table_rows) as total
                    FROM information_schema.tables
                    WHERE table_schema = DATABASE()
                ")->fetchColumn()
            ];
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'backup_logs' => $logs,
                    'system_health' => $health
                ]
            ]);
            break;
            
        case 'POST':
            // Create backup
            $input = json_decode(file_get_contents('php://input'), true);
            $backupType = $input['backup_type'] ?? 'full';
            
            // In a real system, this would trigger actual backup processes
            $backupName = 'backup_' . $backupType . '_' . date('Y-m-d_H-i-s');
            
            // Simulate backup creation
            sleep(2); // Simulate backup time
            
            echo json_encode([
                'success' => true,
                'message' => 'Backup created successfully',
                'backup_name' => $backupName
            ]);
            break;
            
        case 'PUT':
            // Restore from backup
            $input = json_decode(file_get_contents('php://input'), true);
            $backupName = $input['backup_name'];
            
            // In a real system, this would trigger actual restore processes
            // Simulate restore process
            sleep(3); // Simulate restore time
            
            echo json_encode([
                'success' => true,
                'message' => 'System restored from backup: ' . $backupName
            ]);
            break;
            
        case 'DELETE':
            // Delete backup
            $input = json_decode(file_get_contents('php://input'), true);
            $backupName = $input['backup_name'];
            
            // In a real system, this would delete the actual backup file
            echo json_encode([
                'success' => true,
                'message' => 'Backup deleted: ' . $backupName
            ]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
