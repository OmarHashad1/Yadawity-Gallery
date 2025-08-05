// Content Management System
class ContentManager {
    constructor() {
        this.content = [...contentData];
        this.filteredContent = [...this.content];
        this.currentPage = 1;
        this.recordsPerPage = 10;
        this.currentEditContentId = null;
        
        this.initializeEventListeners();
        this.renderContent();
        this.updateStats();
    }

    initializeEventListeners() {
        // Modal controls
        document.getElementById('addContentBtn').addEventListener('click', () => this.openContentModal());
        document.getElementById('closeModal').addEventListener('click', () => this.closeContentModal());
        document.getElementById('cancelBtn').addEventListener('click', () => this.closeContentModal());
        
        // Form submission
        document.getElementById('contentForm').addEventListener('submit', (e) => this.handleFormSubmit(e));
        
        // Search and filter
        document.getElementById('searchInput').addEventListener('input', () => this.handleSearch());
        document.getElementById('searchBtn').addEventListener('click', () => this.handleSearch());
        document.getElementById('statusFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('categoryFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('sortBy').addEventListener('change', () => this.applySorting());
        
        // Pagination
        document.getElementById('recordsPerPage').addEventListener('change', () => this.changeRecordsPerPage());
        document.getElementById('prevPage').addEventListener('click', () => this.previousPage());
        document.getElementById('nextPage').addEventListener('click', () => this.nextPage());
        
        // Other controls
        document.getElementById('refreshBtn').addEventListener('click', () => this.refreshData());
        document.getElementById('publishBtn').addEventListener('click', () => this.publishChanges());
        
        // Close modal when clicking outside
        document.getElementById('contentModal').addEventListener('click', (e) => {
            if (e.target.id === 'contentModal') this.closeContentModal();
        });
    }

    renderContent() {
        const tbody = document.getElementById('contentTableBody');
        const startIndex = (this.currentPage - 1) * this.recordsPerPage;
        const endIndex = startIndex + this.recordsPerPage;
        const contentToShow = this.filteredContent.slice(startIndex, endIndex);
        
        tbody.innerHTML = '';
        
        contentToShow.forEach(content => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <input type="checkbox" class="contentSelect" data-content-id="${content.id}">
                </td>
                <td>
                    <div style="max-width: 200px;">
                        <h4 style="margin: 0; font-size: 0.9rem;">${content.title}</h4>
                        <p style="margin: 0; font-size: 0.8rem; color: var(--text-light);">${content.excerpt}</p>
                    </div>
                </td>
                <td>
                    <span class="typeBadge type-${content.type}">
                        ${content.type.toUpperCase()}
                    </span>
                </td>
                <td>
                    <span class="categoryBadge category-${content.category}">
                        ${content.category}
                    </span>
                </td>
                <td>
                    <span class="statusBadge status-${content.status}">
                        ${content.status.toUpperCase()}
                    </span>
                </td>
                <td>${content.author}</td>
                <td>${content.views.toLocaleString()}</td>
                <td>${this.formatDate(content.lastModified)}</td>
                <td>
                    <button class="actionBtn btn-view" onclick="contentManager.viewContent(${content.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="actionBtn btn-edit" onclick="contentManager.editContent(${content.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="actionBtn btn-delete" onclick="contentManager.deleteContent(${content.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
        
        this.updatePaginationInfo();
    }

    updateStats() {
        const publishedContent = this.content.filter(c => c.status === 'published').length;
        const draftContent = this.content.filter(c => c.status === 'draft').length;
        const mediaFiles = this.content.filter(c => c.type === 'media').length;
        
        document.getElementById('totalContent').textContent = this.content.length;
        document.getElementById('publishedContent').textContent = publishedContent;
        document.getElementById('draftContent').textContent = draftContent;
        document.getElementById('mediaFiles').textContent = mediaFiles;
    }

    openContentModal(content = null) {
        const modal = document.getElementById('contentModal');
        const title = document.getElementById('modalTitle');
        const form = document.getElementById('contentForm');
        
        if (content) {
            title.textContent = 'Edit Content';
            this.currentEditContentId = content.id;
            this.populateForm(content);
        } else {
            title.textContent = 'Add New Content';
            this.currentEditContentId = null;
            form.reset();
        }
        
        modal.classList.add('active');
    }

    closeContentModal() {
        document.getElementById('contentModal').classList.remove('active');
        document.getElementById('contentForm').reset();
        this.currentEditContentId = null;
    }

    populateForm(content) {
        document.getElementById('contentId').value = content.id;
        document.getElementById('title').value = content.title;
        document.getElementById('type').value = content.type;
        document.getElementById('category').value = content.category;
        document.getElementById('status').value = content.status;
        document.getElementById('content').value = content.content;
        document.getElementById('excerpt').value = content.excerpt;
    }

    handleFormSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const contentData = {
            title: formData.get('title') || document.getElementById('title').value,
            type: formData.get('type') || document.getElementById('type').value,
            category: formData.get('category') || document.getElementById('category').value,
            status: formData.get('status') || document.getElementById('status').value,
            content: formData.get('content') || document.getElementById('content').value,
            excerpt: formData.get('excerpt') || document.getElementById('excerpt').value
        };
        
        if (this.currentEditContentId) {
            this.updateContent(this.currentEditContentId, contentData);
        } else {
            this.addContent(contentData);
        }
        
        this.closeContentModal();
    }

    addContent(contentData) {
        const newContent = {
            id: Math.max(...this.content.map(c => c.id)) + 1,
            ...contentData,
            author: 'Admin',
            views: 0,
            lastModified: new Date().toISOString().split('T')[0],
            created: new Date().toISOString().split('T')[0]
        };
        
        this.content.unshift(newContent);
        this.applyFilters();
        this.updateStats();
        this.showNotification('Content added successfully!', 'success');
    }

    updateContent(contentId, contentData) {
        const contentIndex = this.content.findIndex(c => c.id === contentId);
        if (contentIndex !== -1) {
            this.content[contentIndex] = { 
                ...this.content[contentIndex], 
                ...contentData,
                lastModified: new Date().toISOString().split('T')[0]
            };
            this.applyFilters();
            this.updateStats();
            this.showNotification('Content updated successfully!', 'success');
        }
    }

    viewContent(contentId) {
        const content = this.content.find(c => c.id === contentId);
        if (content) {
            alert(`Content Details:\n\nTitle: ${content.title}\nType: ${content.type}\nCategory: ${content.category}\nStatus: ${content.status}\nAuthor: ${content.author}\nViews: ${content.views}\nCreated: ${content.created}\n\nContent:\n${content.content}`);
        }
    }

    editContent(contentId) {
        const content = this.content.find(c => c.id === contentId);
        if (content) {
            this.openContentModal(content);
        }
    }

    deleteContent(contentId) {
        if (confirm('Are you sure you want to delete this content?')) {
            this.content = this.content.filter(c => c.id !== contentId);
            this.applyFilters();
            this.updateStats();
            this.showNotification('Content deleted successfully!', 'success');
        }
    }

    handleSearch() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        
        if (searchTerm === '') {
            this.filteredContent = [...this.content];
        } else {
            this.filteredContent = this.content.filter(content => 
                content.title.toLowerCase().includes(searchTerm) ||
                content.author.toLowerCase().includes(searchTerm) ||
                content.content.toLowerCase().includes(searchTerm)
            );
        }
        
        this.currentPage = 1;
        this.renderContent();
    }

    applyFilters() {
        const statusFilter = document.getElementById('statusFilter').value;
        const categoryFilter = document.getElementById('categoryFilter').value;
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        
        this.filteredContent = this.content.filter(content => {
            const matchesSearch = searchTerm === '' || 
                content.title.toLowerCase().includes(searchTerm) ||
                content.author.toLowerCase().includes(searchTerm) ||
                content.content.toLowerCase().includes(searchTerm);
            
            const matchesStatus = statusFilter === '' || content.status === statusFilter;
            const matchesCategory = categoryFilter === '' || content.category === categoryFilter;
            
            return matchesSearch && matchesStatus && matchesCategory;
        });
        
        this.currentPage = 1;
        this.applySorting();
    }

    applySorting() {
        const sortBy = document.getElementById('sortBy').value;
        
        this.filteredContent.sort((a, b) => {
            switch (sortBy) {
                case 'title':
                    return a.title.localeCompare(b.title);
                case 'author':
                    return a.author.localeCompare(b.author);
                case 'date':
                    return new Date(b.created) - new Date(a.created);
                case 'views':
                    return b.views - a.views;
                default:
                    return 0;
            }
        });
        
        this.renderContent();
    }

    changeRecordsPerPage() {
        this.recordsPerPage = parseInt(document.getElementById('recordsPerPage').value);
        this.currentPage = 1;
        this.renderContent();
    }

    previousPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.renderContent();
        }
    }

    nextPage() {
        const totalPages = Math.ceil(this.filteredContent.length / this.recordsPerPage);
        if (this.currentPage < totalPages) {
            this.currentPage++;
            this.renderContent();
        }
    }

    updatePaginationInfo() {
        const totalContent = this.filteredContent.length;
        const startIndex = (this.currentPage - 1) * this.recordsPerPage;
        const endIndex = Math.min(startIndex + this.recordsPerPage, totalContent);
        const totalPages = Math.ceil(totalContent / this.recordsPerPage);
        
        document.getElementById('paginationInfo').textContent = 
            `Showing ${startIndex + 1}-${endIndex} of ${totalContent} items`;
        
        document.getElementById('prevPage').disabled = this.currentPage === 1;
        document.getElementById('nextPage').disabled = this.currentPage === totalPages;
    }

    refreshData() {
        this.content = [...contentData];
        this.applyFilters();
        this.updateStats();
        this.showNotification('Data refreshed successfully!', 'info');
    }

    publishChanges() {
        const draftCount = this.content.filter(c => c.status === 'draft').length;
        if (draftCount > 0) {
            if (confirm(`Publish ${draftCount} draft items?`)) {
                this.content.forEach(content => {
                    if (content.status === 'draft') {
                        content.status = 'published';
                        content.lastModified = new Date().toISOString().split('T')[0];
                    }
                });
                this.applyFilters();
                this.updateStats();
                this.showNotification(`Published ${draftCount} items successfully!`, 'success');
            }
        } else {
            this.showNotification('No draft content to publish', 'info');
        }
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            background: ${type === 'success' ? 'var(--success-green)' : type === 'error' ? 'var(--danger-red)' : 'var(--blue-accent)'};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            z-index: 3000;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        `;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
}

// Initialize the content manager when the page loads
let contentManager;
document.addEventListener('DOMContentLoaded', () => {
    contentManager = new ContentManager();
});