<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'db.php';

try {
    // Get request parameters
    $action = $_GET['action'] ?? 'overview';
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Get filters and pagination
    $page = (int)($_GET['page'] ?? 1);
    $limit = (int)($_GET['limit'] ?? 10);
    $status_filter = $_GET['status'] ?? '';
    $priority_filter = $_GET['priority'] ?? '';
    $category_filter = $_GET['category'] ?? '';
    $search = $_GET['search'] ?? '';

    $response = [];

    // Handle different actions based on HTTP method
    if ($method === 'POST') {
        // Handle POST requests (create new tickets, messages, announcements)
        handlePostRequests($db, $action);
    } elseif ($method === 'PUT') {
        // Handle PUT requests (update tickets, reply to messages)
        handlePutRequests($db, $action);
    } elseif ($method === 'DELETE') {
        // Handle DELETE requests (delete tickets, messages)
        handleDeleteRequests($db, $action);
    } else {
        // Handle GET requests
        switch ($action) {
            case 'stats':
                $stats = getCommunicationStats($db);
                $response = $stats;
                break;
                
            case 'tickets':
                $tickets = getSupportTickets($db, $page, $limit, $status_filter, $priority_filter, $category_filter, $search);
                $response = $tickets;
                break;
                
            case 'messages':
                $messages = getRecentMessages($db, $page, $limit);
                $response = $messages;
                break;
                
            case 'announcements':
                $announcements = getAnnouncements($db, $page, $limit);
                $response = $announcements;
                break;
                
            case 'ticket_details':
                $ticket_id = $_GET['ticket_id'] ?? 0;
                $ticket_details = getTicketDetails($db, $ticket_id);
                $response = $ticket_details;
                break;
                
            case 'overview':
            default:
                // Get complete communication overview
                $stats = getCommunicationStats($db);
                $tickets = getSupportTickets($db, $page, $limit, $status_filter, $priority_filter, $category_filter, $search);
                $messages = getRecentMessages($db, 1, 5); // Get only recent 5 messages for overview
                
                $response = [
                    'success' => true,
                    'stats' => $stats['success'] ? $stats['data'] : null,
                    'tickets' => $tickets['success'] ? $tickets['data'] : [],
                    'pagination' => $tickets['success'] ? $tickets['pagination'] : null,
                    'recent_messages' => $messages['success'] ? $messages['data'] : [],
                    'last_updated' => date('Y-m-d H:i:s'),
                    'errors' => []
                ];
                
                // Collect any errors
                if (!$stats['success']) {
                    $response['errors'][] = 'Stats: ' . $stats['message'];
                }
                if (!$tickets['success']) {
                    $response['errors'][] = 'Tickets: ' . $tickets['message'];
                }
                if (!$messages['success']) {
                    $response['errors'][] = 'Messages: ' . $messages['message'];
                }
                break;
        }
    }

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

function getCommunicationStats($db) {
    try {
        // Initialize support tables if they don't exist
        initializeSupportTables($db);
        
        // Open tickets count
        $open_tickets_query = "SELECT COUNT(*) as count FROM support_tickets WHERE status IN ('open', 'pending')";
        $open_tickets_result = $db->query($open_tickets_query);
        $open_tickets = $open_tickets_result ? $open_tickets_result->fetch_assoc()['count'] : 0;

        // New tickets today
        $new_today_query = "SELECT COUNT(*) as count FROM support_tickets WHERE DATE(created_at) = CURDATE()";
        $new_today_result = $db->query($new_today_query);
        $new_today = $new_today_result ? $new_today_result->fetch_assoc()['count'] : 0;

        // Average response time (in hours) - simulated for now
        $avg_response_time = 2.4;

        // Resolution rate
        $total_tickets_query = "SELECT COUNT(*) as total FROM support_tickets WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $resolved_tickets_query = "SELECT COUNT(*) as resolved FROM support_tickets WHERE status = 'resolved' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        
        $total_tickets_result = $db->query($total_tickets_query);
        $resolved_tickets_result = $db->query($resolved_tickets_query);
        
        $total_tickets = $total_tickets_result ? $total_tickets_result->fetch_assoc()['total'] : 0;
        $resolved_tickets = $resolved_tickets_result ? $resolved_tickets_result->fetch_assoc()['resolved'] : 0;
        
        $resolution_rate = $total_tickets > 0 ? ($resolved_tickets / $total_tickets) * 100 : 0;

        // Satisfaction score (simulated - would come from feedback)
        $satisfaction_score = 4.7;

        // Tickets by category
        $category_query = "SELECT category, COUNT(*) as count FROM support_tickets GROUP BY category";
        $category_result = $db->query($category_query);
        $tickets_by_category = [];
        if ($category_result) {
            while ($row = $category_result->fetch_assoc()) {
                $tickets_by_category[$row['category']] = (int)$row['count'];
            }
        }

        // Tickets by status
        $status_query = "SELECT status, COUNT(*) as count FROM support_tickets GROUP BY status";
        $status_result = $db->query($status_query);
        $tickets_by_status = [];
        if ($status_result) {
            while ($row = $status_result->fetch_assoc()) {
                $tickets_by_status[$row['status']] = (int)$row['count'];
            }
        }

        // Recent activity metrics
        $unread_messages_query = "SELECT COUNT(*) as count FROM messages WHERE is_read = 0 AND recipient_type = 'admin'";
        $unread_messages_result = $db->query($unread_messages_query);
        $unread_messages = $unread_messages_result ? $unread_messages_result->fetch_assoc()['count'] : 0;

        return [
            'success' => true,
            'data' => [
                'open_tickets' => (int)$open_tickets,
                'new_tickets_today' => (int)$new_today,
                'avg_response_time' => [
                    'value' => $avg_response_time,
                    'formatted' => $avg_response_time . 'h',
                    'trend' => 'stable'
                ],
                'resolution_rate' => [
                    'value' => round($resolution_rate, 1),
                    'formatted' => round($resolution_rate, 1) . '%',
                    'trend' => 'positive'
                ],
                'satisfaction_score' => [
                    'value' => $satisfaction_score,
                    'formatted' => $satisfaction_score,
                    'trend' => 'positive'
                ],
                'unread_messages' => (int)$unread_messages,
                'tickets_by_category' => $tickets_by_category,
                'tickets_by_status' => $tickets_by_status
            ]
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error fetching communication stats: ' . $e->getMessage(),
            'data' => null
        ];
    }
}

function getSupportTickets($db, $page, $limit, $status_filter, $priority_filter, $category_filter, $search) {
    try {
        $offset = ($page - 1) * $limit;
        
        // Build WHERE clause
        $where_conditions = ['1=1']; // Always true condition to start
        $params = [];
        $types = '';

        if (!empty($status_filter)) {
            $where_conditions[] = "status = ?";
            $params[] = $status_filter;
            $types .= 's';
        }

        if (!empty($priority_filter)) {
            $where_conditions[] = "priority = ?";
            $params[] = $priority_filter;
            $types .= 's';
        }

        if (!empty($category_filter)) {
            $where_conditions[] = "category = ?";
            $params[] = $category_filter;
            $types .= 's';
        }

        if (!empty($search)) {
            $where_conditions[] = "(subject LIKE ? OR description LIKE ? OR ticket_id LIKE ?)";
            $search_param = "%$search%";
            $params = array_merge($params, [$search_param, $search_param, $search_param]);
            $types .= 'sss';
        }

        $where_clause = implode(' AND ', $where_conditions);

        // Get total count
        $count_query = "SELECT COUNT(*) as total FROM support_tickets WHERE $where_clause";
        if (!empty($params)) {
            $count_stmt = $db->prepare($count_query);
            if ($count_stmt) {
                $count_stmt->bind_param($types, ...$params);
                $count_stmt->execute();
                $count_result = $count_stmt->get_result();
                $total_records = $count_result->fetch_assoc()['total'];
            } else {
                $total_records = 0;
            }
        } else {
            $count_result = $db->query($count_query);
            $total_records = $count_result ? $count_result->fetch_assoc()['total'] : 0;
        }

        // Get tickets data
        $tickets_query = "SELECT 
                            st.ticket_id,
                            st.user_id,
                            st.subject,
                            st.description,
                            st.category,
                            st.priority,
                            st.status,
                            st.created_at,
                            st.updated_at,
                            CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                            u.email as customer_email,
                            u.profile_picture
                         FROM support_tickets st
                         LEFT JOIN users u ON st.user_id = u.user_id
                         WHERE $where_clause
                         ORDER BY st.created_at DESC
                         LIMIT ? OFFSET ?";

        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $tickets_stmt = $db->prepare($tickets_query);
        $tickets = [];
        
        if ($tickets_stmt) {
            $tickets_stmt->bind_param($types, ...$params);
            $tickets_stmt->execute();
            $tickets_result = $tickets_stmt->get_result();

            while ($row = $tickets_result->fetch_assoc()) {
                $tickets[] = [
                    'ticket_id' => $row['ticket_id'],
                    'user_id' => $row['user_id'],
                    'customer_name' => $row['customer_name'],
                    'customer_email' => $row['customer_email'],
                    'profile_picture' => $row['profile_picture'],
                    'subject' => $row['subject'],
                    'description' => substr($row['description'], 0, 100) . '...',
                    'category' => $row['category'],
                    'priority' => $row['priority'],
                    'status' => $row['status'],
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at'],
                    'time_ago' => getTimeAgo($row['created_at'])
                ];
            }
        }

        // Calculate pagination
        $total_pages = ceil($total_records / $limit);

        return [
            'success' => true,
            'data' => $tickets,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_records' => (int)$total_records,
                'per_page' => $limit,
                'has_next' => $page < $total_pages,
                'has_prev' => $page > 1
            ]
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error fetching support tickets: ' . $e->getMessage(),
            'data' => [],
            'pagination' => null
        ];
    }
}

function getRecentMessages($db, $page, $limit) {
    try {
        $offset = ($page - 1) * $limit;
        
        $messages_query = "SELECT 
                              m.message_id,
                              m.sender_id,
                              m.subject,
                              m.content,
                              m.is_read,
                              m.created_at,
                              CONCAT(u.first_name, ' ', u.last_name) as sender_name,
                              u.email as sender_email,
                              u.profile_picture
                           FROM messages m
                           LEFT JOIN users u ON m.sender_id = u.user_id
                           WHERE m.recipient_type = 'admin'
                           ORDER BY m.created_at DESC
                           LIMIT ? OFFSET ?";

        $messages_stmt = $db->prepare($messages_query);
        $messages = [];
        
        if ($messages_stmt) {
            $messages_stmt->bind_param('ii', $limit, $offset);
            $messages_stmt->execute();
            $messages_result = $messages_stmt->get_result();

            while ($row = $messages_result->fetch_assoc()) {
                $messages[] = [
                    'message_id' => $row['message_id'],
                    'sender_id' => $row['sender_id'],
                    'sender_name' => $row['sender_name'],
                    'sender_email' => $row['sender_email'],
                    'profile_picture' => $row['profile_picture'],
                    'subject' => $row['subject'],
                    'content' => substr($row['content'], 0, 100) . '...',
                    'is_read' => (bool)$row['is_read'],
                    'created_at' => $row['created_at'],
                    'time_ago' => getTimeAgo($row['created_at'])
                ];
            }
        }

        return [
            'success' => true,
            'data' => $messages
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error fetching messages: ' . $e->getMessage(),
            'data' => []
        ];
    }
}

function getAnnouncements($db, $page, $limit) {
    try {
        $offset = ($page - 1) * $limit;
        
        $announcements_query = "SELECT * FROM announcements ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $announcements_stmt = $db->prepare($announcements_query);
        $announcements = [];
        
        if ($announcements_stmt) {
            $announcements_stmt->bind_param('ii', $limit, $offset);
            $announcements_stmt->execute();
            $announcements_result = $announcements_stmt->get_result();

            while ($row = $announcements_result->fetch_assoc()) {
                $announcements[] = [
                    'announcement_id' => $row['announcement_id'],
                    'title' => $row['title'],
                    'content' => $row['content'],
                    'target_audience' => $row['target_audience'],
                    'is_active' => (bool)$row['is_active'],
                    'created_at' => $row['created_at'],
                    'time_ago' => getTimeAgo($row['created_at'])
                ];
            }
        }

        return [
            'success' => true,
            'data' => $announcements
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error fetching announcements: ' . $e->getMessage(),
            'data' => []
        ];
    }
}

function getTicketDetails($db, $ticket_id) {
    try {
        $ticket_query = "SELECT 
                            st.*,
                            CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                            u.email as customer_email,
                            u.profile_picture,
                            u.phone
                         FROM support_tickets st
                         LEFT JOIN users u ON st.user_id = u.user_id
                         WHERE st.ticket_id = ?";

        $ticket_stmt = $db->prepare($ticket_query);
        if ($ticket_stmt) {
            $ticket_stmt->bind_param('s', $ticket_id);
            $ticket_stmt->execute();
            $ticket_result = $ticket_stmt->get_result();
            
            if ($ticket_data = $ticket_result->fetch_assoc()) {
                // Get ticket replies/responses
                $replies_query = "SELECT * FROM ticket_replies WHERE ticket_id = ? ORDER BY created_at ASC";
                $replies_stmt = $db->prepare($replies_query);
                $replies = [];
                
                if ($replies_stmt) {
                    $replies_stmt->bind_param('s', $ticket_id);
                    $replies_stmt->execute();
                    $replies_result = $replies_stmt->get_result();
                    
                    while ($reply = $replies_result->fetch_assoc()) {
                        $replies[] = [
                            'reply_id' => $reply['reply_id'],
                            'message' => $reply['message'],
                            'sender_type' => $reply['sender_type'],
                            'created_at' => $reply['created_at'],
                            'time_ago' => getTimeAgo($reply['created_at'])
                        ];
                    }
                }

                return [
                    'success' => true,
                    'data' => [
                        'ticket' => $ticket_data,
                        'replies' => $replies
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Ticket not found'
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'Error preparing ticket query'
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error fetching ticket details: ' . $e->getMessage()
        ];
    }
}

function initializeSupportTables($db) {
    // Create support tickets table
    $tickets_table = "CREATE TABLE IF NOT EXISTS support_tickets (
        ticket_id VARCHAR(20) PRIMARY KEY,
        user_id INT NOT NULL,
        subject VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        category ENUM('payment', 'artwork', 'shipping', 'account', 'technical', 'other') DEFAULT 'other',
        priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
        status ENUM('open', 'pending', 'resolved', 'closed') DEFAULT 'open',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )";
    
    // Create messages table
    $messages_table = "CREATE TABLE IF NOT EXISTS messages (
        message_id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        recipient_id INT NULL,
        recipient_type ENUM('user', 'admin') DEFAULT 'admin',
        subject VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE
    )";
    
    // Create announcements table
    $announcements_table = "CREATE TABLE IF NOT EXISTS announcements (
        announcement_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        target_audience ENUM('all', 'artists', 'buyers') DEFAULT 'all',
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    // Create ticket replies table
    $replies_table = "CREATE TABLE IF NOT EXISTS ticket_replies (
        reply_id INT AUTO_INCREMENT PRIMARY KEY,
        ticket_id VARCHAR(20) NOT NULL,
        message TEXT NOT NULL,
        sender_type ENUM('user', 'admin') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ticket_id) REFERENCES support_tickets(ticket_id) ON DELETE CASCADE
    )";

    $db->query($tickets_table);
    $db->query($messages_table);
    $db->query($announcements_table);
    $db->query($replies_table);
}

function handlePostRequests($db, $action) {
    // Handle POST requests for creating new items
    // Implementation would go here based on specific needs
}

function handlePutRequests($db, $action) {
    // Handle PUT requests for updating items
    // Implementation would go here based on specific needs
}

function handleDeleteRequests($db, $action) {
    // Handle DELETE requests for removing items
    // Implementation would go here based on specific needs
}

// Helper function (same as before)
function getTimeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'Just now';
    if ($time < 3600) return floor($time/60) . ' min ago';
    if ($time < 86400) return floor($time/3600) . ' hr ago';
    if ($time < 604800) return floor($time/86400) . ' day' . (floor($time/86400) > 1 ? 's' : '') . ' ago';
    
    return date('M j, Y', strtotime($datetime));
}

?>
