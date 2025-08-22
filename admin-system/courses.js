let currentPage = 1;
let currentPublished = '';

document.addEventListener('DOMContentLoaded', function() {
    // Check authentication
    if (!localStorage.getItem('csrf_token')) {
        window.location.href = 'login.php';
        return;
    }

    // Display user info
    const userInfo = document.getElementById('userInfo');
    const userName = localStorage.getItem('user_name') || 'Admin';
    userInfo.textContent = `Welcome, ${userName}`;

    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-open');
        });
    }

    // Load courses
    loadCourses();
});

async function loadCourses(page = 1, published = '') {
    try {
        let url = `/admin-system/API/courses.php?page=${page}&limit=20`;
        if (published !== '') {
            url += `&is_published=${published}`;
        }

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.status === 401 || response.status === 403) {
            localStorage.clear();
            window.location.href = 'login.php';
            return;
        }

        const data = await response.json();
        
        if (response.ok && data.data) {
            displayCourses(data.data);
            updatePagination(data.meta);
        } else {
            console.error('Failed to load courses:', data.error);
        }
    } catch (error) {
        console.error('Error loading courses:', error);
    }
}

function displayCourses(courses) {
    const tbody = document.getElementById('coursesTableBody');
    
    if (!courses || courses.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No courses found</td></tr>';
        return;
    }

    let html = '';
    courses.forEach(course => {
        const publishedClass = course.is_published ? 'bg-success' : 'bg-warning';
        const publishedText = course.is_published ? 'Yes' : 'No';
        
        html += `
            <tr>
                <td>${course.course_id}</td>
                <td>${course.title}</td>
                <td>${course.artist_id}</td>
                <td><span class="badge bg-info">${course.course_type}</span></td>
                <td><span class="badge bg-secondary">${course.difficulty}</span></td>
                <td>$${(course.price || 0).toLocaleString()}</td>
                <td><span class="badge ${publishedClass}">${publishedText}</span></td>
                <td>${new Date(course.created_at).toLocaleDateString()}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editCourse(${course.course_id})">
                        Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteCourse(${course.course_id})">
                        Delete
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function updatePagination(meta) {
    const pagination = document.getElementById('pagination');
    
    if (!meta || meta.total <= meta.limit) {
        pagination.innerHTML = '';
        return;
    }

    const totalPages = Math.ceil(meta.total / meta.limit);
    let html = '';

    // Previous button
    html += `
        <li class="page-item ${meta.page <= 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${meta.page - 1})">Previous</a>
        </li>
    `;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === meta.page) {
            html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${i})">${i}</a></li>`;
        }
    }

    // Next button
    html += `
        <li class="page-item ${meta.page >= totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${meta.page + 1})">Next</a>
        </li>
    `;

    pagination.innerHTML = html;
}

function changePage(page) {
    if (page < 1) return;
    currentPage = page;
    loadCourses(currentPage, currentPublished);
}

function filterCourses() {
    currentPublished = document.getElementById('publishedFilter').value;
    currentPage = 1;
    loadCourses(currentPage, currentPublished);
}

function openAddCourseModal() {
    document.getElementById('courseModalTitle').textContent = 'Add New Course';
    document.getElementById('courseForm').reset();
    document.getElementById('courseId').value = '';
}

async function editCourse(courseId) {
    try {
        const response = await fetch(`/admin-system/API/courses.php?id=${courseId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            const course = data.data;
            
            document.getElementById('courseModalTitle').textContent = 'Edit Course';
            document.getElementById('courseId').value = course.course_id;
            document.getElementById('title').value = course.title;
            document.getElementById('artistId').value = course.artist_id;
            document.getElementById('courseType').value = course.course_type;
            document.getElementById('difficulty').value = course.difficulty;
            document.getElementById('price').value = course.price;
            document.getElementById('duration').value = course.duration_date;
            document.getElementById('rate').value = course.rate || '';
            document.getElementById('description').value = course.description || '';
            document.getElementById('requirement').value = course.requirement || '';
            document.getElementById('thumbnail').value = course.thumbnail || '';
            document.getElementById('isPublished').checked = course.is_published == 1;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('courseModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Error loading course details:', error);
    }
}

async function saveCourse() {
    const courseId = document.getElementById('courseId').value;
    const isEdit = courseId !== '';
    
    const courseData = {
        title: document.getElementById('title').value,
        artist_id: parseInt(document.getElementById('artistId').value),
        course_type: document.getElementById('courseType').value,
        difficulty: document.getElementById('difficulty').value,
        price: parseFloat(document.getElementById('price').value),
        duration_date: parseInt(document.getElementById('duration').value),
        rate: parseFloat(document.getElementById('rate').value) || 0,
        description: document.getElementById('description').value,
        requirement: document.getElementById('requirement').value,
        thumbnail: document.getElementById('thumbnail').value,
        is_published: document.getElementById('isPublished').checked ? 1 : 0
    };

    try {
        const url = isEdit ? `/admin-system/API/courses.php?id=${courseId}` : '/admin-system/API/courses.php';
        const method = isEdit ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': localStorage.getItem('csrf_token')
            },
            body: JSON.stringify(courseData)
        });

        if (response.status === 401 || response.status === 403) {
            localStorage.clear();
            window.location.href = 'login.php';
            return;
        }

        const data = await response.json();
        
        if (response.ok) {
            // Close modal and reload courses
            const modal = bootstrap.Modal.getInstance(document.getElementById('courseModal'));
            modal.hide();
            loadCourses(currentPage, currentPublished);
        } else {
            alert('Error: ' + (data.error || 'Failed to save course'));
        }
    } catch (error) {
        console.error('Error saving course:', error);
        alert('Error saving course. Please try again.');
    }
}

async function deleteCourse(courseId) {
    if (!confirm('Are you sure you want to delete this course?')) {
        return;
    }

    try {
        const response = await fetch(`/admin-system/API/courses.php?id=${courseId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': localStorage.getItem('csrf_token')
            }
        });

        if (response.status === 401 || response.status === 403) {
            localStorage.clear();
            window.location.href = 'login.php';
            return;
        }

        const data = await response.json();
        
        if (response.ok) {
            loadCourses(currentPage, currentPublished);
        } else {
            alert('Error: ' + (data.error || 'Failed to delete course'));
        }
    } catch (error) {
        console.error('Error deleting course:', error);
        alert('Error deleting course. Please try again.');
    }
}

function logout() {
    fetch('/admin-system/API/logout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': localStorage.getItem('csrf_token')
        }
    }).finally(() => {
        localStorage.clear();
        window.location.href = 'login.php';
    });
}
