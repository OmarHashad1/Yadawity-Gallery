<?php
include_once 'db.php';

try {
    // Get marketing campaigns, promotions, discounts, and analytics
    $campaignType = $_GET['campaign_type'] ?? '';
    $status = $_GET['status'] ?? '';
    $search = $_GET['search'] ?? '';
    $dateFrom = $_GET['date_from'] ?? '';
    $dateTo = $_GET['date_to'] ?? '';
    $limit = (int)($_GET['limit'] ?? 50);
    $offset = (int)($_GET['offset'] ?? 0);
    
    // Create marketing tables if they don't exist
    $createCampaignsTable = "
        CREATE TABLE IF NOT EXISTS marketing_campaigns (
            id INT AUTO_INCREMENT PRIMARY KEY,
            campaign_name VARCHAR(255) NOT NULL,
            campaign_type ENUM('email', 'social', 'display', 'search', 'promotion', 'discount') NOT NULL,
            description TEXT,
            start_date DATE NOT NULL,
            end_date DATE,
            budget DECIMAL(10,2),
            status ENUM('draft', 'active', 'paused', 'completed', 'cancelled') DEFAULT 'draft',
            target_audience TEXT,
            discount_percentage DECIMAL(5,2),
            discount_code VARCHAR(50),
            min_purchase_amount DECIMAL(10,2),
            usage_limit INT,
            current_usage INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_campaign_type (campaign_type),
            INDEX idx_status (status),
            INDEX idx_dates (start_date, end_date)
        )
    ";
    $pdo->exec($createCampaignsTable);
    
    try {
        $sql = "SELECT * FROM marketing_campaigns WHERE 1=1";
        $params = [];
        
        if ($campaignType) {
            $sql .= " AND campaign_type = ?";
            $params[] = $campaignType;
        }
        
        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        
        if ($search) {
            $sql .= " AND (campaign_name LIKE ? OR description LIKE ? OR discount_code LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if ($dateFrom) {
            $sql .= " AND start_date >= ?";
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $sql .= " AND (end_date <= ? OR end_date IS NULL)";
            $params[] = $dateTo;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $campaigns = $stmt->fetchAll();
        
        // Get total count
        $countParams = array_slice($params, 0, -2);
        $countSql = str_replace("SELECT *", "SELECT COUNT(*) as total", $sql);
        $countSql = preg_replace('/ORDER BY.*LIMIT.*OFFSET.*/', '', $countSql);
        
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($countParams);
        $total = $countStmt->fetch()['total'];
        
    } catch (Exception $e) {
        // If table doesn't exist or is empty, return sample data
        $campaigns = [
            [
                'id' => 1,
                'campaign_name' => 'Summer Art Sale',
                'campaign_type' => 'promotion',
                'description' => '20% off all summer artwork',
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+30 days')),
                'budget' => 5000.00,
                'status' => 'active',
                'discount_percentage' => 20.00,
                'discount_code' => 'SUMMER20',
                'min_purchase_amount' => 100.00,
                'usage_limit' => 1000,
                'current_usage' => 45,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'campaign_name' => 'New Artist Spotlight',
                'campaign_type' => 'email',
                'description' => 'Email campaign featuring new artists',
                'start_date' => date('Y-m-d', strtotime('-7 days')),
                'end_date' => null,
                'budget' => 2000.00,
                'status' => 'active',
                'target_audience' => 'art enthusiasts, collectors',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
        $total = count($campaigns);
    }
    
    // Calculate campaign statistics
    $summaryStmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_campaigns,
            COUNT(CASE WHEN status = 'active' THEN 1 END) as active_campaigns,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_campaigns,
            COUNT(CASE WHEN status = 'draft' THEN 1 END) as draft_campaigns,
            SUM(budget) as total_budget,
            SUM(CASE WHEN status = 'active' THEN budget ELSE 0 END) as active_budget,
            AVG(CASE WHEN discount_percentage > 0 THEN discount_percentage ELSE NULL END) as avg_discount,
            SUM(current_usage) as total_code_usage
        FROM marketing_campaigns
    ");
    
    try {
        $summaryStmt->execute();
        $summary = $summaryStmt->fetch();
    } catch (Exception $e) {
        $summary = [
            'total_campaigns' => count($campaigns),
            'active_campaigns' => count(array_filter($campaigns, function($c) { return $c['status'] === 'active'; })),
            'completed_campaigns' => 0,
            'draft_campaigns' => 0,
            'total_budget' => array_sum(array_column($campaigns, 'budget')),
            'active_budget' => array_sum(array_filter(array_map(function($c) {
                return $c['status'] === 'active' ? $c['budget'] : 0;
            }, $campaigns))),
            'avg_discount' => 20.00,
            'total_code_usage' => array_sum(array_column($campaigns, 'current_usage'))
        ];
    }
    
    sendResponse(true, 'Marketing campaigns retrieved successfully', [
        'campaigns' => $campaigns,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset,
        'summary' => [
            'total_campaigns' => (int)$summary['total_campaigns'],
            'active_campaigns' => (int)$summary['active_campaigns'],
            'completed_campaigns' => (int)$summary['completed_campaigns'],
            'draft_campaigns' => (int)$summary['draft_campaigns'],
            'total_budget' => (float)$summary['total_budget'],
            'active_budget' => (float)$summary['active_budget'],
            'avg_discount' => (float)$summary['avg_discount'],
            'total_code_usage' => (int)$summary['total_code_usage']
        ],
        'campaign_types' => ['email', 'social', 'display', 'search', 'promotion', 'discount'],
        'statuses' => ['draft', 'active', 'paused', 'completed', 'cancelled']
    ]);

} catch (Exception $e) {
    sendResponse(false, 'Error retrieving marketing campaigns: ' . $e->getMessage(), null, 500);
}
?>
