// Admin Marketing & Promotion JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeAdminMarketing();
});

function initializeAdminMarketing() {
    initializeSidebar();
    initializeTabs();
    loadCampaigns();
    loadSocialPosts();
    loadEmailCampaigns();
    loadPromotions();
    initializeModals();
    initializeEventListeners();
    initializeFilters();
}

// Sample data
const campaigns = [
    {
        id: 1,
        name: 'Summer Art Festival 2025',
        type: 'exhibition',
        status: 'active',
        startDate: '2025-07-01',
        endDate: '2025-08-31',
        budget: 25000,
        spent: 18500,
        impressions: 45200,
        clicks: 1536,
        conversions: 42,
        description: 'Promoting our summer art festival featuring contemporary Egyptian artists.',
        targetAudience: 'Art enthusiasts, collectors, and cultural tourists'
    },
    {
        id: 2,
        name: 'New Artist Spotlight',
        type: 'artist',
        status: 'active',
        startDate: '2025-07-15',
        endDate: '2025-08-15',
        budget: 15000,
        spent: 8200,
        impressions: 28900,
        clicks: 892,
        conversions: 28,
        description: 'Highlighting emerging artists in the Egyptian art scene.',
        targetAudience: 'Young art collectors and gallery visitors'
    },
    {
        id: 3,
        name: 'Ramadan Art Collection',
        type: 'seasonal',
        status: 'completed',
        startDate: '2025-03-01',
        endDate: '2025-04-30',
        budget: 20000,
        spent: 19500,
        impressions: 67800,
        clicks: 2340,
        conversions: 85,
        description: 'Special collection celebrating Ramadan with traditional and modern art.',
        targetAudience: 'Cultural enthusiasts and religious art collectors'
    },
    {
        id: 4,
        name: 'Virtual Gallery Launch',
        type: 'general',
        status: 'draft',
        startDate: '2025-08-01',
        endDate: '2025-09-30',
        budget: 30000,
        spent: 0,
        impressions: 0,
        clicks: 0,
        conversions: 0,
        description: 'Launching our new virtual reality gallery experience.',
        targetAudience: 'Tech-savvy art enthusiasts and international visitors'
    }
];

const socialPosts = [
    {
        id: 1,
        content: 'Discover the vibrant colors and rich heritage of Egyptian contemporary art at our gallery. 🎨 #EgyptianArt #Contemporary',
        platforms: ['facebook', 'instagram'],
        scheduledDate: '2025-07-26',
        scheduledTime: '14:00',
        status: 'scheduled',
        image: null
    },
    {
        id: 2,
        content: 'Behind the scenes: Watch our featured artist Marina Kovač create her latest masterpiece. Link in bio for full video! 📹',
        platforms: ['instagram', 'twitter'],
        scheduledDate: '2025-07-27',
        scheduledTime: '16:30',
        status: 'scheduled',
        image: 'artist-video-thumbnail.jpg'
    },
    {
        id: 3,
        content: 'Last week to visit our "Harmony in Color" exhibition. Don\'t miss out on this incredible showcase! 🎭',
        platforms: ['facebook'],
        scheduledDate: '2025-07-25',
        scheduledTime: '10:00',
        status: 'published',
        image: null
    }
];

const emailCampaigns = [
    {
        id: 1,
        name: 'Monthly Newsletter - July 2025',
        subject: 'New Exhibitions & Featured Artists This Month',
        content: 'Dear art enthusiasts, discover what\'s new at Yadawity this July...',
        segment: 'all',
        sendDate: '2025-07-01',
        sendTime: '09:00',
        status: 'sent',
        recipients: 2847,
        opened: 692,
        clicked: 108
    },
    {
        id: 2,
        name: 'VIP Collector Preview',
        subject: 'Exclusive Preview: Summer Collection 2025',
        content: 'As a valued VIP member, you get first access to our summer collection...',
        segment: 'vip',
        sendDate: '2025-07-28',
        sendTime: '14:00',
        status: 'scheduled',
        recipients: 156,
        opened: 0,
        clicked: 0
    },
    {
        id: 3,
        name: 'Artist Workshop Invitation',
        subject: 'Join Our Digital Art Workshop - Limited Spots!',
        content: 'Learn from professional artists in our exclusive workshop series...',
        segment: 'artists',
        sendDate: '2025-07-30',
        sendTime: '11:00',
        status: 'draft',
        recipients: 0,
        opened: 0,
        clicked: 0
    }
];

const promotions = [
    {
        id: 1,
        name: 'Summer Sale 2025',
        code: 'SUMMER25',
        type: 'percentage',
        value: 20,
        startDate: '2025-07-01',
        endDate: '2025-08-31',
        usageLimit: 500,
        usedCount: 127,
        minAmount: 1000,
        status: 'active',
        description: '20% off on all artwork purchases during summer season'
    },
    {
        id: 2,
        name: 'New Customer Welcome',
        code: 'WELCOME15',
        type: 'percentage',
        value: 15,
        startDate: '2025-01-01',
        endDate: '2025-12-31',
        usageLimit: null,
        usedCount: 89,
        minAmount: 500,
        status: 'active',
        description: 'Welcome discount for first-time customers'
    },
    {
        id: 3,
        name: 'Free Shipping Weekend',
        code: 'FREESHIP',
        type: 'shipping',
        value: 0,
        startDate: '2025-08-01',
        endDate: '2025-08-03',
        usageLimit: 200,
        usedCount: 0,
        minAmount: 2000,
        status: 'scheduled',
        description: 'Free shipping on orders over EGP 2000'
    },
    {
        id: 4,
        name: 'Ramadan Special',
        code: 'RAMADAN30',
        type: 'percentage',
        value: 30,
        startDate: '2025-03-01',
        endDate: '2025-04-30',
        usageLimit: 300,
        usedCount: 298,
        minAmount: 1500,
        status: 'expired',
        description: 'Special Ramadan discount on religious and cultural art'
    }
];

// Initialize sidebar functionality
function initializeSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });

        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 1024) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    }
}

// Initialize tabs functionality
function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tabBtn');
    const tabPanes = document.querySelectorAll('.tabPane');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;

            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));

            this.classList.add('active');
            const targetPane = document.getElementById(targetTab);
            if (targetPane) {
                targetPane.classList.add('active');
                
                // Load specific content based on tab
                switch(targetTab) {
                    case 'campaigns':
                        loadCampaigns();
                        break;
                    case 'social':
                        loadSocialPosts();
                        break;
                    case 'email':
                        loadEmailCampaigns();
                        break;
                    case 'promotions':
                        loadPromotions();
                        break;
                    case 'analytics':
                        loadAnalytics();
                        break;
                }
            }
        });
    });
}

// Load campaigns
function loadCampaigns() {
    const campaignsGrid = document.getElementById('campaignsGrid');
    if (!campaignsGrid) return;

    campaignsGrid.innerHTML = '';

    if (campaigns.length === 0) {
        campaignsGrid.innerHTML = `
            <div class="emptyState">
                <i class="fas fa-bullhorn"></i>
                <h3>No Campaigns</h3>
                <p>Create your first marketing campaign to get started</p>
                <button class="btn btn-primary" onclick="showCampaignModal()">
                    <i class="fas fa-plus"></i>
                    Create Campaign
                </button>
            </div>
        `;
        return;
    }

    campaigns.forEach(campaign => {
        const campaignCard = document.createElement('div');
        campaignCard.className = 'campaignCard';
        
        const ctr = campaign.clicks > 0 ? ((campaign.clicks / campaign.impressions) * 100).toFixed(2) : '0.00';
        const conversionRate = campaign.clicks > 0 ? ((campaign.conversions / campaign.clicks) * 100).toFixed(2) : '0.00';
        const budgetUsed = campaign.budget > 0 ? ((campaign.spent / campaign.budget) * 100).toFixed(0) : '0';

        campaignCard.innerHTML = `
            <div class="campaignHeader">
                <div class="campaignInfo">
                    <h3>${campaign.name}</h3>
                    <div class="campaignMeta">
                        <span><i class="fas fa-tag"></i> ${formatCampaignType(campaign.type)}</span>
                        <span><i class="fas fa-calendar"></i> ${formatDateRange(campaign.startDate, campaign.endDate)}</span>
                        <span><i class="fas fa-dollar-sign"></i> EGP ${campaign.budget.toLocaleString()}</span>
                    </div>
                    <span class="status status-${campaign.status}">${formatStatus(campaign.status)}</span>
                </div>
                <div class="campaignActions">
                    <button class="btn btn-sm btn-outline" onclick="editCampaign(${campaign.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline" onclick="deleteCampaign(${campaign.id})" style="color: var(--danger-red); border-color: var(--danger-red);">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <p class="campaignDescription">${campaign.description}</p>
            <div class="campaignMetrics">
                <div class="campaignMetric">
                    <span class="metricValue">${campaign.impressions.toLocaleString()}</span>
                    <span class="metricLabel">Impressions</span>
                </div>
                <div class="campaignMetric">
                    <span class="metricValue">${ctr}%</span>
                    <span class="metricLabel">CTR</span>
                </div>
                <div class="campaignMetric">
                    <span class="metricValue">${budgetUsed}%</span>
                    <span class="metricLabel">Budget Used</span>
                </div>
            </div>
        `;
        campaignsGrid.appendChild(campaignCard);
    });
}

// Load social posts
function loadSocialPosts() {
    const postsList = document.getElementById('scheduledPostsList');
    if (!postsList) return;

    postsList.innerHTML = '';

    socialPosts.forEach(post => {
        const postItem = document.createElement('div');
        postItem.className = 'scheduledPost';
        
        const platformIcons = post.platforms.map(platform => {
            const iconMap = {
                facebook: 'fab fa-facebook',
                instagram: 'fab fa-instagram',
                twitter: 'fab fa-twitter'
            };
            return `<i class="${iconMap[platform]}" style="color: ${getPlatformColor(platform)};"></i>`;
        }).join(' ');

        postItem.innerHTML = `
            <div class="postHeader">
                <div class="postPlatforms">${platformIcons}</div>
                <div class="postTime">${formatDateTime(post.scheduledDate, post.scheduledTime)}</div>
            </div>
            <div class="postContent">${post.content}</div>
            <div class="postActions" style="margin-top: 0.5rem;">
                <span class="status status-${post.status}">${formatStatus(post.status)}</span>
                <button class="btn btn-sm btn-outline" onclick="editSocialPost(${post.id})" style="margin-left: auto;">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
        `;
        postsList.appendChild(postItem);
    });
}

// Load email campaigns
function loadEmailCampaigns() {
    const emailList = document.getElementById('emailCampaignsList');
    if (!emailList) return;

    emailList.innerHTML = '';

    emailCampaigns.forEach(campaign => {
        const campaignItem = document.createElement('div');
        campaignItem.className = 'emailCampaignItem';
        
        const openRate = campaign.recipients > 0 ? ((campaign.opened / campaign.recipients) * 100).toFixed(1) : '0.0';
        const clickRate = campaign.opened > 0 ? ((campaign.clicked / campaign.opened) * 100).toFixed(1) : '0.0';

        campaignItem.innerHTML = `
            <div class="emailCampaignInfo">
                <h4>${campaign.name}</h4>
                <p>${campaign.subject}</p>
                <div style="margin-top: 0.5rem;">
                    <span class="status status-${campaign.status}">${formatStatus(campaign.status)}</span>
                    <span style="margin-left: 1rem; color: var(--text-muted); font-size: 0.85rem;">
                        ${formatSegment(campaign.segment)}
                    </span>
                </div>
            </div>
            <div class="emailCampaignStats">
                <span>Recipients: <strong>${campaign.recipients}</strong></span>
                <span>Open Rate: <strong>${openRate}%</strong></span>
                <span>Click Rate: <strong>${clickRate}%</strong></span>
            </div>
            <div class="campaignActions">
                <button class="btn btn-sm btn-outline" onclick="editEmailCampaign(${campaign.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline" onclick="deleteEmailCampaign(${campaign.id})" style="color: var(--danger-red); border-color: var(--danger-red);">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        emailList.appendChild(campaignItem);
    });
}

// Load promotions
function loadPromotions() {
    const promotionsList = document.getElementById('promotionsList');
    if (!promotionsList) return;

    promotionsList.innerHTML = '';

    promotions.forEach(promotion => {
        const promotionItem = document.createElement('div');
        promotionItem.className = 'promotionItem';
        
        const usageText = promotion.usageLimit ? 
            `${promotion.usedCount}/${promotion.usageLimit} used` : 
            `${promotion.usedCount} used`;

        const discountText = promotion.type === 'percentage' ? 
            `${promotion.value}% off` :
            promotion.type === 'fixed' ? 
            `EGP ${promotion.value} off` :
            promotion.type === 'shipping' ? 
            'Free shipping' : 
            'Special offer';

        promotionItem.innerHTML = `
            <div class="promotionInfo">
                <h4>${promotion.name}</h4>
                <div class="promotionCode">${promotion.code}</div>
                <div class="promotionDetails">
                    <span><i class="fas fa-percentage"></i> ${discountText}</span>
                    <span><i class="fas fa-calendar"></i> ${formatDateRange(promotion.startDate, promotion.endDate)}</span>
                    ${promotion.minAmount ? `<span><i class="fas fa-dollar-sign"></i> Min. EGP ${promotion.minAmount}</span>` : ''}
                </div>
            </div>
            <div class="promotionStats">
                <span class="status status-${promotion.status}">${formatStatus(promotion.status)}</span>
                <div class="promotionUsage">${usageText}</div>
            </div>
            <div class="promotionActions">
                <button class="btn btn-sm btn-outline" onclick="editPromotion(${promotion.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline" onclick="deletePromotion(${promotion.id})" style="color: var(--danger-red); border-color: var(--danger-red);">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        promotionsList.appendChild(promotionItem);
    });
}

// Load analytics (placeholder)
function loadAnalytics() {
    // This would typically load real analytics data
    console.log('Loading analytics data...');
}

// Initialize modals
function initializeModals() {
    const modals = ['campaignModal'];
    
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        const closeBtn = document.getElementById(`close${modalId.charAt(0).toUpperCase() + modalId.slice(1, -5)}Modal`);
        const cancelBtn = document.getElementById(`cancel${modalId.charAt(0).toUpperCase() + modalId.slice(1, -5)}`);

        // Close modal handlers
        [closeBtn, cancelBtn].forEach(btn => {
            if (btn) {
                btn.addEventListener('click', () => closeModal(modal));
            }
        });

        // Backdrop click
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal(modal);
            });
        }
    });

    // Form submissions
    const campaignForm = document.getElementById('campaignForm');
    if (campaignForm) {
        campaignForm.addEventListener('submit', handleCampaignSubmit);
    }
}

// Initialize event listeners
function initializeEventListeners() {
    // Header actions
    const newCampaignBtn = document.getElementById('newCampaignBtn');
    if (newCampaignBtn) {
        newCampaignBtn.addEventListener('click', () => showCampaignModal());
    }

    const analyticsBtn = document.getElementById('analyticsBtn');
    if (analyticsBtn) {
        analyticsBtn.addEventListener('click', () => {
            // Switch to analytics tab
            document.querySelector('[data-tab="analytics"]').click();
        });
    }
}

// Initialize filters
function initializeFilters() {
    const campaignStatusFilter = document.getElementById('campaignStatusFilter');
    const campaignTypeFilter = document.getElementById('campaignTypeFilter');
    const campaignSearch = document.getElementById('campaignSearch');

    if (campaignStatusFilter) {
        campaignStatusFilter.addEventListener('change', filterCampaigns);
    }

    if (campaignTypeFilter) {
        campaignTypeFilter.addEventListener('change', filterCampaigns);
    }

    if (campaignSearch) {
        campaignSearch.addEventListener('input', debounce(filterCampaigns, 300));
    }
}

// Modal functions
function showCampaignModal(campaignId = null) {
    const modal = document.getElementById('campaignModal');
    const form = document.getElementById('campaignForm');
    
    if (campaignId) {
        const campaign = campaigns.find(c => c.id === campaignId);
        if (campaign) {
            document.getElementById('campaignName').value = campaign.name;
            document.getElementById('campaignType').value = campaign.type;
            document.getElementById('campaignStartDate').value = campaign.startDate;
            document.getElementById('campaignEndDate').value = campaign.endDate;
            document.getElementById('campaignBudget').value = campaign.budget;
            document.getElementById('campaignDescription').value = campaign.description;
        }
    } else {
        form.reset();
    }
    
    showModal(modal);
}

// Form handlers
function handleCampaignSubmit(e) {
    e.preventDefault();
    
    const campaignData = {
        name: document.getElementById('campaignName').value,
        type: document.getElementById('campaignType').value,
        startDate: document.getElementById('campaignStartDate').value,
        endDate: document.getElementById('campaignEndDate').value,
        budget: parseInt(document.getElementById('campaignBudget').value),
        description: document.getElementById('campaignDescription').value,
        status: 'draft'
    };

    showNotification('Saving campaign...', 'info');
    
    setTimeout(() => {
        showNotification(`Campaign "${campaignData.name}" saved successfully`, 'success');
        closeModal(document.getElementById('campaignModal'));
        
        // Add to campaigns array (simulated)
        campaigns.push({
            id: campaigns.length + 1,
            ...campaignData,
            spent: 0,
            impressions: 0,
            clicks: 0,
            conversions: 0,
            targetAudience: 'General audience'
        });
        
        loadCampaigns();
    }, 1500);
}

// CRUD operations
function editCampaign(campaignId) {
    showCampaignModal(campaignId);
}

function deleteCampaign(campaignId) {
    const campaign = campaigns.find(c => c.id === campaignId);
    if (!campaign) return;

    if (confirm(`Are you sure you want to delete "${campaign.name}"? This action cannot be undone.`)) {
        showNotification(`Campaign "${campaign.name}" deleted successfully`, 'success');
        
        const index = campaigns.findIndex(c => c.id === campaignId);
        if (index > -1) {
            campaigns.splice(index, 1);
            loadCampaigns();
        }
    }
}

function editSocialPost(postId) {
    showNotification('Social post editing functionality would be implemented here', 'info');
}

function editEmailCampaign(campaignId) {
    showNotification('Email campaign editing functionality would be implemented here', 'info');
}

function deleteEmailCampaign(campaignId) {
    const campaign = emailCampaigns.find(c => c.id === campaignId);
    if (!campaign) return;

    if (confirm(`Are you sure you want to delete "${campaign.name}"?`)) {
        showNotification(`Email campaign "${campaign.name}" deleted successfully`, 'success');
        
        const index = emailCampaigns.findIndex(c => c.id === campaignId);
        if (index > -1) {
            emailCampaigns.splice(index, 1);
            loadEmailCampaigns();
        }
    }
}

function editPromotion(promotionId) {
    showNotification('Promotion editing functionality would be implemented here', 'info');
}

function deletePromotion(promotionId) {
    const promotion = promotions.find(p => p.id === promotionId);
    if (!promotion) return;

    if (confirm(`Are you sure you want to delete "${promotion.name}"?`)) {
        showNotification(`Promotion "${promotion.name}" deleted successfully`, 'success');
        
        const index = promotions.findIndex(p => p.id === promotionId);
        if (index > -1) {
            promotions.splice(index, 1);
            loadPromotions();
        }
    }
}

// Filter functions
function filterCampaigns() {
    const statusFilter = document.getElementById('campaignStatusFilter').value;
    const typeFilter = document.getElementById('campaignTypeFilter').value;
    const searchTerm = document.getElementById('campaignSearch').value.toLowerCase();

    const filteredCampaigns = campaigns.filter(campaign => {
        const matchesStatus = !statusFilter || campaign.status === statusFilter;
        const matchesType = !typeFilter || campaign.type === typeFilter;
        const matchesSearch = !searchTerm || 
            campaign.name.toLowerCase().includes(searchTerm) ||
            campaign.description.toLowerCase().includes(searchTerm);

        return matchesStatus && matchesType && matchesSearch;
    });

    updateCampaignsDisplay(filteredCampaigns);
}

function updateCampaignsDisplay(campaignsToShow) {
    const campaignsGrid = document.getElementById('campaignsGrid');
    if (!campaignsGrid) return;

    campaignsGrid.innerHTML = '';

    campaignsToShow.forEach(campaign => {
        const campaignCard = document.createElement('div');
        campaignCard.className = 'campaignCard';
        
        const ctr = campaign.clicks > 0 ? ((campaign.clicks / campaign.impressions) * 100).toFixed(2) : '0.00';
        const budgetUsed = campaign.budget > 0 ? ((campaign.spent / campaign.budget) * 100).toFixed(0) : '0';

        campaignCard.innerHTML = `
            <div class="campaignHeader">
                <div class="campaignInfo">
                    <h3>${campaign.name}</h3>
                    <div class="campaignMeta">
                        <span><i class="fas fa-tag"></i> ${formatCampaignType(campaign.type)}</span>
                        <span><i class="fas fa-calendar"></i> ${formatDateRange(campaign.startDate, campaign.endDate)}</span>
                        <span><i class="fas fa-dollar-sign"></i> EGP ${campaign.budget.toLocaleString()}</span>
                    </div>
                    <span class="status status-${campaign.status}">${formatStatus(campaign.status)}</span>
                </div>
                <div class="campaignActions">
                    <button class="btn btn-sm btn-outline" onclick="editCampaign(${campaign.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline" onclick="deleteCampaign(${campaign.id})" style="color: var(--danger-red); border-color: var(--danger-red);">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <p class="campaignDescription">${campaign.description}</p>
            <div class="campaignMetrics">
                <div class="campaignMetric">
                    <span class="metricValue">${campaign.impressions.toLocaleString()}</span>
                    <span class="metricLabel">Impressions</span>
                </div>
                <div class="campaignMetric">
                    <span class="metricValue">${ctr}%</span>
                    <span class="metricLabel">CTR</span>
                </div>
                <div class="campaignMetric">
                    <span class="metricValue">${budgetUsed}%</span>
                    <span class="metricLabel">Budget Used</span>
                </div>
            </div>
        `;
        campaignsGrid.appendChild(campaignCard);
    });
}

// Utility functions
function showModal(modal) {
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modal) {
    if (modal) {
        modal.classList.remove('active');
    }
}

function formatStatus(status) {
    return status.charAt(0).toUpperCase() + status.slice(1);
}

function formatCampaignType(type) {
    const typeMap = {
        exhibition: 'Exhibition',
        artist: 'Artist Promotion',
        general: 'General Marketing',
        seasonal: 'Seasonal'
    };
    return typeMap[type] || type;
}

function formatDateRange(startDate, endDate) {
    const start = new Date(startDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    const end = new Date(endDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    return `${start} - ${end}`;
}

function formatDateTime(date, time) {
    const dateObj = new Date(`${date}T${time}`);
    return dateObj.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatSegment(segment) {
    const segmentMap = {
        all: 'All Subscribers',
        artists: 'Artists',
        collectors: 'Art Collectors',
        newsletter: 'Newsletter Subscribers',
        vip: 'VIP Customers'
    };
    return segmentMap[segment] || segment;
}

function getPlatformColor(platform) {
    const colorMap = {
        facebook: '#1877f2',
        instagram: '#E4405F',
        twitter: '#1DA1F2'
    };
    return colorMap[platform] || '#666';
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close">
            <i class="fas fa-times"></i>
        </button>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Position notification
    const notifications = document.querySelectorAll('.notification');
    const index = Array.from(notifications).indexOf(notification);
    notification.style.top = `${20 + (index * 70)}px`;

    // Show notification
    setTimeout(() => notification.classList.add('show'), 100);

    // Auto remove
    setTimeout(() => removeNotification(notification), 5000);

    // Close button
    notification.querySelector('.notification-close').addEventListener('click', () => {
        removeNotification(notification);
    });
}

function removeNotification(notification) {
    notification.classList.remove('show');
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
        repositionNotifications();
    }, 300);
}

function repositionNotifications() {
    const notifications = document.querySelectorAll('.notification');
    notifications.forEach((notification, index) => {
        notification.style.top = `${20 + (index * 70)}px`;
    });
}

function getNotificationIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}
