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
    $category_filter = $_GET['category'] ?? '';
    $type_filter = $_GET['type'] ?? '';
    $search = $_GET['search'] ?? '';
    $sort_by = $_GET['sort_by'] ?? 'created_at';
    $sort_order = $_GET['sort_order'] ?? 'DESC';

    // Validate sort parameters
    $allowed_sort_fields = ['content_id', 'title', 'type', 'category', 'status', 'author_id', 'views', 'created_at', 'updated_at'];
    if (!in_array($sort_by, $allowed_sort_fields)) {
        $sort_by = 'created_at';
    }
    
    if (!in_array(strtoupper($sort_order), ['ASC', 'DESC'])) {
        $sort_order = 'DESC';
    }

    $response = [];

    // Handle different actions based on HTTP method
    if ($method === 'POST') {
        // Handle POST requests (create new content)
        handlePostRequests($db, $action);
    } elseif ($method === 'PUT') {
        // Handle PUT requests (update content)
        handlePutRequests($db, $action);
    } elseif ($method === 'DELETE') {
        // Handle DELETE requests (delete content)
        handleDeleteRequests($db, $action);
    } else {
        // Handle GET requests
        switch ($action) {
            case 'stats':
                $stats = getContentStats($db);
                $response = $stats;
                break;
                
            case 'content':
                $content = getAllContent($db, $page, $limit, $status_filter, $category_filter, $type_filter, $search, $sort_by, $sort_order);
                $response = $content;
                break;
                
            case 'articles':
                $articles = getContentByType($db, 'article', $page, $limit, $status_filter, $category_filter, $search, $sort_by, $sort_order);
                $response = $articles;
                break;
                
            case 'pages':
                $pages = getContentByType($db, 'page', $page, $limit, $status_filter, $category_filter, $search, $sort_by, $sort_order);
                $response = $pages;
                break;
                
            case 'media':
                $media = getMediaFiles($db, $page, $limit, $search);
                $response = $media;
                break;
                
            case 'content_details':
                $content_id = $_GET['content_id'] ?? 0;
                $content_details = getContentDetails($db, $content_id);
                $response = $content_details;
                break;
                
            case 'overview':
            default:
                // Get complete content management overview
                $stats = getContentStats($db);
                $content = getAllContent($db, $page, $limit, $status_filter, $category_filter, $type_filter, $search, $sort_by, $sort_order);
                
                $response = [
                    'success' => true,
                    'stats' => $stats['success'] ? $stats['data'] : null,
                    'content' => $content['success'] ? $content['data'] : [],
                    'pagination' => $content['success'] ? $content['pagination'] : null,
                    'last_updated' => date('Y-m-d H:i:s'),
                    'errors' => []
                ];
                
                // Collect any errors
                if (!$stats['success']) {
                    $response['errors'][] = 'Stats: ' . $stats['message'];
                }
                if (!$content['success']) {
                    $response['errors'][] = 'Content: ' . $content['message'];
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

function getContentStats($db) {
    try {
        // Initialize content tables if they don't exist
        initializeContentTables($db);
        
        // Total content count
        $total_content_query = "SELECT COUNT(*) as count FROM content";
        $total_content_result = $db->query($total_content_query);
        $total_content = $total_content_result ? $total_content_result->fetch_assoc()['count'] : 0;

        // Published content count
        $published_content_query = "SELECT COUNT(*) as count FROM content WHERE status = 'published'";
        $published_content_result = $db->query($published_content_query);
        $published_content = $published_content_result ? $published_content_result->fetch_assoc()['count'] : 0;

        // Draft content count
        $draft_content_query = "SELECT COUNT(*) as count FROM content WHERE status = 'draft'";
        $draft_content_result = $db->query($draft_content_query);
        $draft_content = $draft_content_result ? $draft_content_result->fetch_assoc()['count'] : 0;

        // Media files count
        $media_files_query = "SELECT COUNT(*) as count FROM media_files";
        $media_files_result = $db->query($media_files_query);
        $media_files = $media_files_result ? $media_files_result->fetch_assoc()['count'] : 0;

        // New content this week
        $new_week_query = "SELECT COUNT(*) as count FROM content WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $new_week_result = $db->query($new_week_query);
        $new_week = $new_week_result ? $new_week_result->fetch_assoc()['count'] : 0;

        // New media this month
        $new_media_month_query = "SELECT COUNT(*) as count FROM media_files WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $new_media_month_result = $db->query($new_media_month_query);
        $new_media_month = $new_media_month_result ? $new_media_month_result->fetch_assoc()['count'] : 0;

        // Content by type
        $type_query = "SELECT type, COUNT(*) as count FROM content GROUP BY type";
        $type_result = $db->query($type_query);
        $content_by_type = [];
        if ($type_result) {
            while ($row = $type_result->fetch_assoc()) {
                $content_by_type[$row['type']] = (int)$row['count'];
            }
        }

        // Content by category
        $category_query = "SELECT category, COUNT(*) as count FROM content GROUP BY category";
        $category_result = $db->query($category_query);
        $content_by_category = [];
        if ($category_result) {
            while ($row = $category_result->fetch_assoc()) {
                $content_by_category[$row['category']] = (int)$row['count'];
            }
        }

        // Content by status
        $status_query = "SELECT status, COUNT(*) as count FROM content GROUP BY status";
        $status_result = $db->query($status_query);
        $content_by_status = [];
        if ($status_result) {
            while ($row = $status_result->fetch_assoc()) {
                $content_by_status[$row['status']] = (int)$row['count'];
            }
        }

        // Most viewed content
        $popular_query = "SELECT title, views FROM content WHERE status = 'published' ORDER BY views DESC LIMIT 5";
        $popular_result = $db->query($popular_query);
        $popular_content = [];
        if ($popular_result) {
            while ($row = $popular_result->fetch_assoc()) {
                $popular_content[] = [
                    'title' => $row['title'],
                    'views' => (int)$row['views']
                ];
            }
        }

        // Calculate published percentage
        $published_percentage = $total_content > 0 ? round(($published_content / $total_content) * 100, 1) : 0;

        return [
            'success' => true,
            'data' => [
                'total_content' => (int)$total_content,
                'published_content' => (int)$published_content,
                'draft_content' => (int)$draft_content,
                'media_files' => (int)$media_files,
                'new_content_week' => (int)$new_week,
                'new_media_month' => (int)$new_media_month,
                'published_percentage' => $published_percentage,
                'content_by_type' => $content_by_type,
                'content_by_category' => $content_by_category,
                'content_by_status' => $content_by_status,
                'popular_content' => $popular_content
            ]
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error fetching content stats: ' . $e->getMessage(),
            'data' => null
        ];
    }
}

function getAllContent($db, $page, $limit, $status_filter, $category_filter, $type_filter, $search, $sort_by, $sort_order) {
    try {
        $offset = ($page - 1) * $limit;
        
        // Build WHERE clause
        $where_conditions = ['1=1']; // Always true condition to start
        $params = [];
        $types = '';

        if (!empty($status_filter)) {
            $where_conditions[] = "c.status = ?";
            $params[] = $status_filter;
            $types .= 's';
        }

        if (!empty($category_filter)) {
            $where_conditions[] = "c.category = ?";
            $params[] = $category_filter;
            $types .= 's';
        }

        if (!empty($type_filter)) {
            $where_conditions[] = "c.type = ?";
            $params[] = $type_filter;
            $types .= 's';
        }

        if (!empty($search)) {
            $where_conditions[] = "(c.title LIKE ? OR c.content LIKE ? OR c.excerpt LIKE ?)";
            $search_param = "%$search%";
            $params = array_merge($params, [$search_param, $search_param, $search_param]);
            $types .= 'sss';
        }

        $where_clause = implode(' AND ', $where_conditions);

        // Get total count
        $count_query = "SELECT COUNT(*) as total FROM content c WHERE $where_clause";
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

        // Get content data
        $content_query = "SELECT 
                            c.content_id,
                            c.title,
                            c.type,
                            c.category,
                            c.status,
                            c.excerpt,
                            c.views,
                            c.featured_image,
                            c.created_at,
                            c.updated_at,
                            CONCAT(u.first_name, ' ', u.last_name) as author_name,
                            u.email as author_email,
                            u.profile_picture as author_picture
                         FROM content c
                         LEFT JOIN users u ON c.author_id = u.user_id
                         WHERE $where_clause
                         ORDER BY c.$sort_by $sort_order
                         LIMIT ? OFFSET ?";

        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $content_stmt = $db->prepare($content_query);
        $content_items = [];
        
        if ($content_stmt) {
            $content_stmt->bind_param($types, ...$params);
            $content_stmt->execute();
            $content_result = $content_stmt->get_result();

            while ($row = $content_result->fetch_assoc()) {
                $content_items[] = [
                    'content_id' => (int)$row['content_id'],
                    'title' => $row['title'],
                    'type' => $row['type'],
                    'category' => $row['category'],
                    'status' => $row['status'],
                    'excerpt' => $row['excerpt'],
                    'views' => (int)$row['views'],
                    'featured_image' => $row['featured_image'],
                    'author_name' => $row['author_name'],
                    'author_email' => $row['author_email'],
                    'author_picture' => $row['author_picture'],
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at'],
                    'time_ago' => getTimeAgo($row['updated_at'])
                ];
            }
        }

        // Calculate pagination
        $total_pages = ceil($total_records / $limit);

        return [
            'success' => true,
            'data' => $content_items,
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
            'message' => 'Error fetching content: ' . $e->getMessage(),
            'data' => [],
            'pagination' => null
        ];
    }
}

function getContentByType($db, $content_type, $page, $limit, $status_filter, $category_filter, $search, $sort_by, $sort_order) {
    return getAllContent($db, $page, $limit, $status_filter, $category_filter, $content_type, $search, $sort_by, $sort_order);
}

function getMediaFiles($db, $page, $limit, $search) {
    try {
        $offset = ($page - 1) * $limit;
        
        // Build WHERE clause for search
        $where_clause = '1=1';
        $params = [];
        $types = '';

        if (!empty($search)) {
            $where_clause = "filename LIKE ? OR original_name LIKE ?";
            $search_param = "%$search%";
            $params = [$search_param, $search_param];
            $types = 'ss';
        }

        // Get total count
        $count_query = "SELECT COUNT(*) as total FROM media_files WHERE $where_clause";
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

        // Get media files data
        $media_query = "SELECT 
                           media_id,
                           filename,
                           original_name,
                           file_type,
                           file_size,
                           file_path,
                           thumbnail_path,
                           created_at
                       FROM media_files 
                       WHERE $where_clause
                       ORDER BY created_at DESC
                       LIMIT ? OFFSET ?";

        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $media_stmt = $db->prepare($media_query);
        $media_files = [];
        
        if ($media_stmt) {
            $media_stmt->bind_param($types, ...$params);
            $media_stmt->execute();
            $media_result = $media_stmt->get_result();

            while ($row = $media_result->fetch_assoc()) {
                $media_files[] = [
                    'media_id' => (int)$row['media_id'],
                    'filename' => $row['filename'],
                    'original_name' => $row['original_name'],
                    'file_type' => $row['file_type'],
                    'file_size' => (int)$row['file_size'],
                    'file_size_formatted' => formatFileSize($row['file_size']),
                    'file_path' => $row['file_path'],
                    'thumbnail_path' => $row['thumbnail_path'],
                    'created_at' => $row['created_at'],
                    'time_ago' => getTimeAgo($row['created_at'])
                ];
            }
        }

        // Calculate pagination
        $total_pages = ceil($total_records / $limit);

        return [
            'success' => true,
            'data' => $media_files,
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
            'message' => 'Error fetching media files: ' . $e->getMessage(),
            'data' => [],
            'pagination' => null
        ];
    }
}

function getContentDetails($db, $content_id) {
    try {
        $content_query = "SELECT 
                            c.*,
                            CONCAT(u.first_name, ' ', u.last_name) as author_name,
                            u.email as author_email,
                            u.profile_picture as author_picture
                         FROM content c
                         LEFT JOIN users u ON c.author_id = u.user_id
                         WHERE c.content_id = ?";

        $content_stmt = $db->prepare($content_query);
        if ($content_stmt) {
            $content_stmt->bind_param('i', $content_id);
            $content_stmt->execute();
            $content_result = $content_stmt->get_result();
            
            if ($content_data = $content_result->fetch_assoc()) {
                // Get content revisions/versions
                $revisions_query = "SELECT * FROM content_revisions WHERE content_id = ? ORDER BY created_at DESC";
                $revisions_stmt = $db->prepare($revisions_query);
                $revisions = [];
                
                if ($revisions_stmt) {
                    $revisions_stmt->bind_param('i', $content_id);
                    $revisions_stmt->execute();
                    $revisions_result = $revisions_stmt->get_result();
                    
                    while ($revision = $revisions_result->fetch_assoc()) {
                        $revisions[] = [
                            'revision_id' => $revision['revision_id'],
                            'title' => $revision['title'],
                            'content' => substr($revision['content'], 0, 200) . '...',
                            'created_at' => $revision['created_at'],
                            'time_ago' => getTimeAgo($revision['created_at'])
                        ];
                    }
                }

                return [
                    'success' => true,
                    'data' => [
                        'content' => $content_data,
                        'revisions' => $revisions
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Content not found'
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'Error preparing content query'
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error fetching content details: ' . $e->getMessage()
        ];
    }
}

function initializeContentTables($db) {
    // Create content table
    $content_table = "CREATE TABLE IF NOT EXISTS content (
        content_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        type ENUM('article', 'page', 'menu', 'media') DEFAULT 'article',
        category ENUM('news', 'events', 'recipes', 'promotions', 'general') DEFAULT 'general',
        status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
        content TEXT NOT NULL,
        excerpt TEXT,
        featured_image VARCHAR(500),
        author_id INT NOT NULL,
        views INT DEFAULT 0,
        meta_title VARCHAR(255),
        meta_description TEXT,
        slug VARCHAR(255) UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (author_id) REFERENCES users(user_id) ON DELETE CASCADE,
        INDEX idx_status (status),
        INDEX idx_type (type),
        INDEX idx_category (category),
        INDEX idx_created_at (created_at)
    )";
    
    // Create media files table
    $media_table = "CREATE TABLE IF NOT EXISTS media_files (
        media_id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL,
        original_name VARCHAR(255) NOT NULL,
        file_type VARCHAR(50) NOT NULL,
        file_size INT NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        thumbnail_path VARCHAR(500),
        uploaded_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (uploaded_by) REFERENCES users(user_id) ON DELETE CASCADE,
        INDEX idx_file_type (file_type),
        INDEX idx_created_at (created_at)
    )";
    
    // Create content revisions table
    $revisions_table = "CREATE TABLE IF NOT EXISTS content_revisions (
        revision_id INT AUTO_INCREMENT PRIMARY KEY,
        content_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        excerpt TEXT,
        revision_note TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (content_id) REFERENCES content(content_id) ON DELETE CASCADE,
        INDEX idx_content_id (content_id),
        INDEX idx_created_at (created_at)
    )";
    
    // Create content tags table
    $tags_table = "CREATE TABLE IF NOT EXISTS content_tags (
        tag_id INT AUTO_INCREMENT PRIMARY KEY,
        content_id INT NOT NULL,
        tag_name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (content_id) REFERENCES content(content_id) ON DELETE CASCADE,
        INDEX idx_content_id (content_id),
        INDEX idx_tag_name (tag_name)
    )";

    $db->query($content_table);
    $db->query($media_table);
    $db->query($revisions_table);
    $db->query($tags_table);
}

function handlePostRequests($db, $action) {
    // Handle POST requests for creating new content
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'create_content':
            echo json_encode(createContent($db, $input));
            break;
        case 'upload_media':
            echo json_encode(uploadMedia($db, $_FILES));
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}

function handlePutRequests($db, $action) {
    // Handle PUT requests for updating content
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'update_content':
            echo json_encode(updateContent($db, $input));
            break;
        case 'publish_content':
            echo json_encode(publishContent($db, $input['content_id']));
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}

function handleDeleteRequests($db, $action) {
    // Handle DELETE requests for removing content
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'delete_content':
            echo json_encode(deleteContent($db, $input['content_id']));
            break;
        case 'delete_media':
            echo json_encode(deleteMedia($db, $input['media_id']));
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}

// Placeholder functions for CRUD operations
function createContent($db, $data) {
    // Implementation for creating new content
    return ['success' => true, 'message' => 'Content created successfully'];
}

function updateContent($db, $data) {
    // Implementation for updating content
    return ['success' => true, 'message' => 'Content updated successfully'];
}

function publishContent($db, $content_id) {
    // Implementation for publishing content
    return ['success' => true, 'message' => 'Content published successfully'];
}

function deleteContent($db, $content_id) {
    // Implementation for deleting content
    return ['success' => true, 'message' => 'Content deleted successfully'];
}

function uploadMedia($db, $files) {
    // Implementation for uploading media files
    return ['success' => true, 'message' => 'Media uploaded successfully'];
}

function deleteMedia($db, $media_id) {
    // Implementation for deleting media files
    return ['success' => true, 'message' => 'Media deleted successfully'];
}

// Helper functions
function getTimeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'Just now';
    if ($time < 3600) return floor($time/60) . ' min ago';
    if ($time < 86400) return floor($time/3600) . ' hr ago';
    if ($time < 604800) return floor($time/86400) . ' day' . (floor($time/86400) > 1 ? 's' : '') . ' ago';
    
    return date('M j, Y', strtotime($datetime));
}

function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

?>
