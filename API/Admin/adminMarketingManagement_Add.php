<?php
include_once 'db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Only POST method allowed', null, 405);
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    $campaignName = $input['campaign_name'] ?? '';
    $campaignType = $input['campaign_type'] ?? '';
    $description = $input['description'] ?? '';
    $startDate = $input['start_date'] ?? '';
    $endDate = $input['end_date'] ?? null;
    $budget = $input['budget'] ?? 0;
    $status = $input['status'] ?? 'draft';
    $targetAudience = $input['target_audience'] ?? '';
    $discountPercentage = $input['discount_percentage'] ?? null;
    $discountCode = $input['discount_code'] ?? '';
    $minPurchaseAmount = $input['min_purchase_amount'] ?? null;
    $usageLimit = $input['usage_limit'] ?? null;
    
    // Validate required fields
    if (empty($campaignName) || empty($campaignType) || empty($startDate)) {
        sendResponse(false, 'campaign_name, campaign_type, and start_date are required', null, 400);
    }
    
    // Validate campaign type
    $allowedTypes = ['email', 'social', 'display', 'search', 'promotion', 'discount'];
    if (!in_array($campaignType, $allowedTypes)) {
        sendResponse(false, 'Invalid campaign_type. Allowed types: ' . implode(', ', $allowedTypes), null, 400);
    }
    
    // Validate status
    $allowedStatuses = ['draft', 'active', 'paused', 'completed', 'cancelled'];
    if (!in_array($status, $allowedStatuses)) {
        sendResponse(false, 'Invalid status. Allowed statuses: ' . implode(', ', $allowedStatuses), null, 400);
    }
    
    // Validate dates
    if (!strtotime($startDate)) {
        sendResponse(false, 'Invalid start_date format. Use YYYY-MM-DD', null, 400);
    }
    
    if ($endDate && !strtotime($endDate)) {
        sendResponse(false, 'Invalid end_date format. Use YYYY-MM-DD', null, 400);
    }
    
    if ($endDate && strtotime($endDate) < strtotime($startDate)) {
        sendResponse(false, 'end_date cannot be before start_date', null, 400);
    }
    
    // Validate discount code uniqueness if provided
    if ($discountCode) {
        $codeStmt = $pdo->prepare("SELECT id FROM marketing_campaigns WHERE discount_code = ? AND status IN ('active', 'draft')");
        $codeStmt->execute([$discountCode]);
        if ($codeStmt->fetch()) {
            sendResponse(false, 'Discount code already exists', null, 409);
        }
    }
    
    // Auto-generate discount code if needed for promotion/discount campaigns
    if (in_array($campaignType, ['promotion', 'discount']) && empty($discountCode)) {
        $discountCode = strtoupper(substr(str_replace(' ', '', $campaignName), 0, 6) . rand(10, 99));
    }
    
    // Validate discount percentage
    if ($discountPercentage !== null) {
        $discountPercentage = (float)$discountPercentage;
        if ($discountPercentage <= 0 || $discountPercentage > 100) {
            sendResponse(false, 'Discount percentage must be between 0 and 100', null, 400);
        }
    }
    
    // Create marketing table if it doesn't exist
    $createTableSql = "
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
    $pdo->exec($createTableSql);
    
    // Insert campaign
    $insertStmt = $pdo->prepare("
        INSERT INTO marketing_campaigns (
            campaign_name, campaign_type, description, start_date, end_date, budget,
            status, target_audience, discount_percentage, discount_code,
            min_purchase_amount, usage_limit, created_at
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $insertStmt->execute([
        $campaignName,
        $campaignType,
        $description,
        $startDate,
        $endDate,
        $budget,
        $status,
        $targetAudience,
        $discountPercentage,
        $discountCode,
        $minPurchaseAmount,
        $usageLimit
    ]);
    
    $campaignId = $pdo->lastInsertId();
    
    // Get the created campaign
    $selectStmt = $pdo->prepare("SELECT * FROM marketing_campaigns WHERE id = ?");
    $selectStmt->execute([$campaignId]);
    $campaign = $selectStmt->fetch();
    
    sendResponse(true, 'Marketing campaign created successfully', [
        'campaign' => $campaign,
        'campaign_id' => $campaignId
    ], 201);

} catch (Exception $e) {
    sendResponse(false, 'Error creating marketing campaign: ' . $e->getMessage(), null, 500);
}
?>
