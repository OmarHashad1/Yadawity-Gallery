<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'db.php';

try {
    // Get request parameters
    $action = $_GET['action'] ?? 'all';
    $page = (int)($_GET['page'] ?? 1);
    $limit = (int)($_GET['limit'] ?? 10);
    $search = $_GET['search'] ?? '';
    $status_filter = $_GET['status'] ?? '';
    $role_filter = $_GET['role'] ?? '';
    $sort_by = $_GET['sort_by'] ?? 'created_at';
    $sort_order = $_GET['sort_order'] ?? 'DESC';

    // Validate sort parameters
    $allowed_sort_fields = ['user_id', 'first_name', 'last_name', 'email', 'user_type', 'is_active', 'created_at'];
    if (!in_array($sort_by, $allowed_sort_fields)) {
        $sort_by = 'created_at';
    }
    
    if (!in_array(strtoupper($sort_order), ['ASC', 'DESC'])) {
        $sort_order = 'DESC';
    }

    $response = [];

    switch ($action) {
        case 'stats':
            // Get user statistics
            $stats = getUserStats($db);
            $response = $stats;
            break;
            
        case 'users':
            // Get paginated users list
            $users = getUsersList($db, $page, $limit, $search, $status_filter, $role_filter, $sort_by, $sort_order);
            $response = $users;
            break;
            
        case 'all':
        default:
            // Get both stats and users
            $stats = getUserStats($db);
            $users = getUsersList($db, $page, $limit, $search, $status_filter, $role_filter, $sort_by, $sort_order);
            
            $response = [
                'success' => true,
                'stats' => $stats['data'],
                'users' => $users['data'],
                'pagination' => $users['pagination']
            ];
            break;
    }

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

function getUserStats($db) {
    try {
        // Total users count
        $total_query = "SELECT COUNT(*) as total FROM users";
        $total_result = $db->query($total_query);
        $total_users = $total_result->fetch_assoc()['total'];

        // Active users count
        $active_query = "SELECT COUNT(*) as active FROM users WHERE is_active = 1";
        $active_result = $db->query($active_query);
        $active_users = $active_result->fetch_assoc()['active'];

        // Inactive users count
        $inactive_users = $total_users - $active_users;

        // New users this month
        $new_month_query = "SELECT COUNT(*) as new_month FROM users WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        $new_month_result = $db->query($new_month_query);
        $new_users_month = $new_month_result->fetch_assoc()['new_month'];

        // New users this week
        $new_week_query = "SELECT COUNT(*) as new_week FROM users WHERE WEEK(created_at) = WEEK(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        $new_week_result = $db->query($new_week_query);
        $new_users_week = $new_week_result->fetch_assoc()['new_week'];

        // Users by type
        $type_query = "SELECT user_type, COUNT(*) as count FROM users GROUP BY user_type";
        $type_result = $db->query($type_query);
        $users_by_type = [];
        while ($row = $type_result->fetch_assoc()) {
            $users_by_type[$row['user_type']] = (int)$row['count'];
        }

        // Recent login activity (users with active sessions)
        $active_sessions_query = "SELECT COUNT(DISTINCT user_id) as active_sessions 
                                 FROM user_login_sessions 
                                 WHERE is_active = 1 AND expires_at > NOW()";
        $active_sessions_result = $db->query($active_sessions_query);
        $active_sessions = $active_sessions_result->fetch_assoc()['active_sessions'];

        // Users registered in last 30 days
        $recent_registrations_query = "SELECT COUNT(*) as recent FROM users WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $recent_result = $db->query($recent_registrations_query);
        $recent_registrations = $recent_result->fetch_assoc()['recent'];

        return [
            'success' => true,
            'data' => [
                'total_users' => (int)$total_users,
                'active_users' => (int)$active_users,
                'inactive_users' => (int)$inactive_users,
                'new_users_month' => (int)$new_users_month,
                'new_users_week' => (int)$new_users_week,
                'active_sessions' => (int)$active_sessions,
                'recent_registrations' => (int)$recent_registrations,
                'users_by_type' => $users_by_type,
                'growth_rate' => $total_users > 0 ? round(($new_users_month / $total_users) * 100, 2) : 0
            ]
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error fetching user statistics: ' . $e->getMessage()
        ];
    }
}

function getUsersList($db, $page, $limit, $search, $status_filter, $role_filter, $sort_by, $sort_order) {
    try {
        $offset = ($page - 1) * $limit;
        
        // Build WHERE clause
        $where_conditions = [];
        $params = [];
        $types = '';

        if (!empty($search)) {
            $where_conditions[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $search_param = "%$search%";
            $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
            $types .= 'ssss';
        }

        if ($status_filter !== '') {
            $is_active = ($status_filter === 'active') ? 1 : 0;
            $where_conditions[] = "is_active = ?";
            $params[] = $is_active;
            $types .= 'i';
        }

        if (!empty($role_filter)) {
            $where_conditions[] = "user_type = ?";
            $params[] = $role_filter;
            $types .= 's';
        }

        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

        // Get total count for pagination
        $count_query = "SELECT COUNT(*) as total FROM users $where_clause";
        if (!empty($params)) {
            $count_stmt = $db->prepare($count_query);
            $count_stmt->bind_param($types, ...$params);
            $count_stmt->execute();
            $count_result = $count_stmt->get_result();
        } else {
            $count_result = $db->query($count_query);
        }
        $total_records = $count_result->fetch_assoc()['total'];

        // Get users data with last login information
        $users_query = "SELECT 
                            u.user_id,
                            u.first_name,
                            u.last_name,
                            u.email,
                            u.phone,
                            u.user_type,
                            u.is_active,
                            u.profile_picture,
                            u.bio,
                            u.location,
                            u.created_at,
                            uls.login_time as last_login
                        FROM users u
                        LEFT JOIN (
                            SELECT user_id, MAX(login_time) as login_time
                            FROM user_login_sessions
                            GROUP BY user_id
                        ) uls ON u.user_id = uls.user_id
                        $where_clause
                        ORDER BY $sort_by $sort_order
                        LIMIT ? OFFSET ?";

        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $users_stmt = $db->prepare($users_query);
        if (!empty($params)) {
            $users_stmt->bind_param($types, ...$params);
        }
        $users_stmt->execute();
        $users_result = $users_stmt->get_result();

        $users = [];
        while ($row = $users_result->fetch_assoc()) {
            // Format user data
            $user = [
                'id' => (int)$row['user_id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'full_name' => $row['first_name'] . ' ' . $row['last_name'],
                'email' => $row['email'],
                'phone' => $row['phone'] ?? '',
                'role' => $row['user_type'],
                'status' => $row['is_active'] ? 'active' : 'inactive',
                'profile_picture' => $row['profile_picture'],
                'bio' => $row['bio'],
                'location' => $row['location'],
                'last_login' => $row['last_login'] ? date('Y-m-d H:i:s', strtotime($row['last_login'])) : 'Never',
                'created_at' => date('Y-m-d H:i:s', strtotime($row['created_at'])),
                'account_age_days' => floor((time() - strtotime($row['created_at'])) / (60 * 60 * 24))
            ];

            // Add role-specific information
            if ($row['user_type'] === 'artist') {
                $artist_info = getArtistInfo($db, $row['user_id']);
                $user['artist_info'] = $artist_info;
            }

            $users[] = $user;
        }

        // Calculate pagination info
        $total_pages = ceil($total_records / $limit);

        return [
            'success' => true,
            'data' => $users,
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
            'message' => 'Error fetching users list: ' . $e->getMessage()
        ];
    }
}

function getArtistInfo($db, $user_id) {
    try {
        $artist_query = "SELECT 
                            art_specialty,
                            years_of_experience,
                            achievements,
                            artist_bio,
                            education
                         FROM users 
                         WHERE user_id = ? AND user_type = 'artist'";
        
        $stmt = $db->prepare($artist_query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($artist_data = $result->fetch_assoc()) {
            // Get artist statistics
            $stats_query = "SELECT 
                               (SELECT COUNT(*) FROM artworks WHERE artist_id = ?) as total_artworks,
                               (SELECT COUNT(*) FROM courses WHERE artist_id = ?) as total_courses,
                               (SELECT COUNT(*) FROM galleries WHERE artist_id = ?) as total_galleries,
                               (SELECT AVG(rating) FROM artist_reviews WHERE artist_id = ?) as avg_rating
                           ";
            
            $stats_stmt = $db->prepare($stats_query);
            $stats_stmt->bind_param('iiii', $user_id, $user_id, $user_id, $user_id);
            $stats_stmt->execute();
            $stats_result = $stats_stmt->get_result();
            $stats = $stats_result->fetch_assoc();
            
            return [
                'specialty' => $artist_data['art_specialty'],
                'experience_years' => (int)$artist_data['years_of_experience'],
                'achievements' => $artist_data['achievements'],
                'artist_bio' => $artist_data['artist_bio'],
                'education' => $artist_data['education'],
                'statistics' => [
                    'total_artworks' => (int)$stats['total_artworks'],
                    'total_courses' => (int)$stats['total_courses'],
                    'total_galleries' => (int)$stats['total_galleries'],
                    'average_rating' => $stats['avg_rating'] ? round((float)$stats['avg_rating'], 2) : 0
                ]
            ];
        }
        
        return null;
        
    } catch (Exception $e) {
        return null;
    }
}

?>