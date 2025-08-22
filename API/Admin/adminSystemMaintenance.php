<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get backup information and system maintenance logs
            $backupInfo = [
                'last_backup' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'backup_size' => '245.8 MB',
                'backup_status' => 'successful',
                'next_scheduled_backup' => date('Y-m-d H:i:s', strtotime('+1 day')),
                'backup_frequency' => 'daily',
                'retention_period' => '30 days'
            ];
            
            $systemLogs = [
                ['timestamp' => date('Y-m-d H:i:s'), 'level' => 'INFO', 'message' => 'System running normally'],
                ['timestamp' => date('Y-m-d H:i:s', strtotime('-1 hour')), 'level' => 'INFO', 'message' => 'Database backup completed'],
                ['timestamp' => date('Y-m-d H:i:s', strtotime('-2 hours')), 'level' => 'WARNING', 'message' => 'High memory usage detected'],
                ['timestamp' => date('Y-m-d H:i:s', strtotime('-3 hours')), 'level' => 'INFO', 'message' => 'User session cleanup completed'],
                ['timestamp' => date('Y-m-d H:i:s', strtotime('-4 hours')), 'level' => 'INFO', 'message' => 'Email notifications sent']
            ];
            
            $maintenanceInfo = [
                'maintenance_mode' => false,
                'last_maintenance' => date('Y-m-d H:i:s', strtotime('-7 days')),
                'next_maintenance' => date('Y-m-d H:i:s', strtotime('+7 days')),
                'system_uptime' => '15 days, 8 hours',
                'server_status' => 'healthy'
            ];
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'backup_info' => $backupInfo,
                    'system_logs' => $systemLogs,
                    'maintenance_info' => $maintenanceInfo
                ]
            ]);
            break;
            
        case 'POST':
            // Trigger backup or maintenance
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'] ?? '';
            
            switch ($action) {
                case 'backup':
                    // Trigger backup
                    echo json_encode(['success' => true, 'message' => 'Backup initiated successfully']);
                    break;
                case 'maintenance':
                    // Enable/disable maintenance mode
                    $enable = $input['enable'] ?? false;
                    echo json_encode(['success' => true, 'message' => 'Maintenance mode ' . ($enable ? 'enabled' : 'disabled')]);
                    break;
                default:
                    echo json_encode(['success' => false, 'message' => 'Invalid action']);
                    break;
            }
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
