// Profile Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
  // Initialize profile functionality
  initializeProfileTabs();
  initializePersonalInfoForm();
  initializeOrderFilters();
  initializeAddressManagement();
  initializePaymentManagement();
  initializeNotificationSettings();
  initializeSecuritySettings();
  initializeProfileImageUpload();
});

// Tab Navigation
function initializeProfileTabs() {
  const menuItems = document.querySelectorAll('.menuItem');
  const tabContents = document.querySelectorAll('.tabContent');

  menuItems.forEach(item => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      
      const targetTab = item.getAttribute('data-tab');
      
      // Remove active class from all menu items and tab contents
      menuItems.forEach(mi => mi.classList.remove('active'));
      tabContents.forEach(tc => tc.classList.remove('active'));
      
      // Add active class to clicked menu item and corresponding tab
      item.classList.add('active');
      document.getElementById(targetTab).classList.add('active');
      
      // Update URL hash
      window.location.hash = targetTab;
    });
  });

  // Handle initial hash navigation
  if (window.location.hash) {
    const hash = window.location.hash.substring(1);
    const targetMenuItem = document.querySelector(`[data-tab="${hash}"]`);
    if (targetMenuItem) {
      targetMenuItem.click();
    }
  }
}

// Personal Information Form
function initializePersonalInfoForm() {
  const editBtn = document.getElementById('editPersonalInfo');
  const cancelBtn = document.getElementById('cancelPersonalInfo');
  const form = document.getElementById('personalInfoForm');
  const formActions = form.querySelector('.formActions');
  const inputs = form.querySelectorAll('input, select, textarea');

  editBtn.addEventListener('click', () => {
    toggleEditMode(true);
  });

  cancelBtn.addEventListener('click', () => {
    toggleEditMode(false);
    resetFormValues();
  });

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    savePersonalInfo();
  });

  function toggleEditMode(isEditing) {
    inputs.forEach(input => {
      if (input.type !== 'email') { // Keep email readonly for security
        input.readOnly = !isEditing;
        input.disabled = !isEditing;
      }
    });
    
    editBtn.style.display = isEditing ? 'none' : 'flex';
    formActions.style.display = isEditing ? 'block' : 'none';
  }

  function resetFormValues() {
    // Reset to original values (in real app, fetch from server)
    document.getElementById('firstName').value = 'Ahmed';
    document.getElementById('lastName').value = 'Hassan';
    document.getElementById('phone').value = '+20 1099359953';
    document.getElementById('dateOfBirth').value = '1990-05-15';
    document.getElementById('gender').value = 'male';
    document.getElementById('bio').value = 'Art enthusiast with a passion for contemporary Middle Eastern art. I particularly enjoy abstract paintings and modern sculptures.';
  }

  function savePersonalInfo() {
    // Show loading state
    const saveBtn = form.querySelector('.saveBtn');
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Saving...';
    saveBtn.disabled = true;

    // Simulate API call
    setTimeout(() => {
      showNotification('Personal information updated successfully!', 'success');
      toggleEditMode(false);
      saveBtn.textContent = originalText;
      saveBtn.disabled = false;
    }, 1500);
  }
}

// Order Filters
function initializeOrderFilters() {
  const statusFilter = document.getElementById('orderStatusFilter');
  const dateFilter = document.getElementById('orderDateFilter');
  const orderItems = document.querySelectorAll('.orderItem');

  if (statusFilter) {
    statusFilter.addEventListener('change', filterOrders);
  }
  
  if (dateFilter) {
    dateFilter.addEventListener('change', filterOrders);
  }

  function filterOrders() {
    const statusValue = statusFilter?.value || '';
    const dateValue = dateFilter?.value || '';

    orderItems.forEach(item => {
      const status = item.querySelector('.statusBadge').textContent.toLowerCase();
      const orderDate = new Date(item.querySelector('.orderDate').textContent);
      const now = new Date();
      
      let showItem = true;

      // Filter by status
      if (statusValue && !status.includes(statusValue)) {
        showItem = false;
      }

      // Filter by date
      if (dateValue) {
        const daysAgo = parseInt(dateValue);
        const cutoffDate = new Date(now.getTime() - (daysAgo * 24 * 60 * 60 * 1000));
        if (orderDate < cutoffDate) {
          showItem = false;
        }
      }

      item.style.display = showItem ? 'block' : 'none';
    });
  }

  // Order action buttons
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('orderActionBtn')) {
      const action = e.target.textContent.trim();
      const orderItem = e.target.closest('.orderItem');
      const orderId = orderItem.querySelector('h4').textContent;

      switch (action) {
        case 'View Details':
          showNotification(`Opening details for ${orderId}`, 'info');
          break;
        case 'Track Package':
          showNotification(`Tracking ${orderId}`, 'info');
          break;
        case 'Leave Review':
          showNotification(`Opening review form for ${orderId}`, 'info');
          break;
        case 'Reorder':
          showNotification(`Adding items from ${orderId} to cart`, 'success');
          break;
      }
    }
  });
}

// Address Management
function initializeAddressManagement() {
  const addAddressBtn = document.getElementById('addAddressBtn');
  
  if (addAddressBtn) {
    addAddressBtn.addEventListener('click', () => {
      showNotification('Opening add address form...', 'info');
    });
  }

  // Address action buttons
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('editAddressBtn')) {
      const addressCard = e.target.closest('.addressCard');
      const addressTitle = addressCard.querySelector('h4').textContent;
      showNotification(`Editing ${addressTitle}...`, 'info');
    }

    if (e.target.classList.contains('deleteAddressBtn')) {
      const addressCard = e.target.closest('.addressCard');
      const addressTitle = addressCard.querySelector('h4').textContent;
      
      if (confirm(`Are you sure you want to delete ${addressTitle}?`)) {
        addressCard.remove();
        showNotification(`${addressTitle} deleted successfully!`, 'success');
      }
    }

    if (e.target.classList.contains('setDefaultBtn')) {
      const addressCard = e.target.closest('.addressCard');
      const addressTitle = addressCard.querySelector('h4').textContent;
      
      // Remove default badge from other addresses
      document.querySelectorAll('.defaultBadge').forEach(badge => {
        if (badge.closest('.addressCard') !== addressCard) {
          badge.remove();
        }
      });
      
      // Add default badge to this address
      const header = addressCard.querySelector('.addressHeader');
      if (!header.querySelector('.defaultBadge')) {
        const badge = document.createElement('span');
        badge.className = 'defaultBadge';
        badge.textContent = 'Default';
        header.appendChild(badge);
      }
      
      // Hide the set default button
      e.target.style.display = 'none';
      
      showNotification(`${addressTitle} set as default address!`, 'success');
    }
  });
}

// Payment Management
function initializePaymentManagement() {
  const addPaymentBtn = document.getElementById('addPaymentBtn');
  
  if (addPaymentBtn) {
    addPaymentBtn.addEventListener('click', () => {
      showNotification('Opening add payment method form...', 'info');
    });
  }

  // Payment action buttons
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('editPaymentBtn')) {
      const paymentCard = e.target.closest('.paymentCard');
      const cardNumber = paymentCard.querySelector('.cardNumber').textContent;
      showNotification(`Editing card ${cardNumber}...`, 'info');
    }

    if (e.target.classList.contains('deletePaymentBtn')) {
      const paymentCard = e.target.closest('.paymentCard');
      const cardNumber = paymentCard.querySelector('.cardNumber').textContent;
      
      if (confirm(`Are you sure you want to delete card ${cardNumber}?`)) {
        paymentCard.remove();
        showNotification(`Card ${cardNumber} deleted successfully!`, 'success');
      }
    }

    if (e.target.classList.contains('setDefaultPaymentBtn')) {
      const paymentCard = e.target.closest('.paymentCard');
      const cardNumber = paymentCard.querySelector('.cardNumber').textContent;
      
      // Remove default badge from other payment methods
      document.querySelectorAll('.paymentCard .defaultBadge').forEach(badge => {
        if (badge.closest('.paymentCard') !== paymentCard) {
          badge.remove();
        }
      });
      
      // Add default badge to this payment method
      const header = paymentCard.querySelector('.paymentHeader');
      if (!header.querySelector('.defaultBadge')) {
        const badge = document.createElement('span');
        badge.className = 'defaultBadge';
        badge.textContent = 'Default';
        header.appendChild(badge);
      }
      
      // Hide the set default button
      e.target.style.display = 'none';
      
      showNotification(`Card ${cardNumber} set as default payment method!`, 'success');
    }
  });
}

// Notification Settings
function initializeNotificationSettings() {
  const saveNotificationsBtn = document.querySelector('.saveNotificationsBtn');
  
  if (saveNotificationsBtn) {
    saveNotificationsBtn.addEventListener('click', () => {
      const originalText = saveNotificationsBtn.textContent;
      saveNotificationsBtn.textContent = 'Saving...';
      saveNotificationsBtn.disabled = true;

      setTimeout(() => {
        showNotification('Notification preferences saved successfully!', 'success');
        saveNotificationsBtn.textContent = originalText;
        saveNotificationsBtn.disabled = false;
      }, 1000);
    });
  }

  // Handle checkbox changes
  document.addEventListener('change', (e) => {
    if (e.target.classList.contains('settingCheckbox')) {
      const label = e.target.closest('.settingLabel').textContent.trim();
      const isEnabled = e.target.checked;
      
      // Auto-save notification preferences
      setTimeout(() => {
        showNotification(
          `${label} ${isEnabled ? 'enabled' : 'disabled'}`, 
          'info'
        );
      }, 300);
    }
  });
}

// Security Settings
function initializeSecuritySettings() {
  const changePasswordBtn = document.querySelector('.changePasswordBtn');
  const enableTwoFactorBtn = document.querySelector('.enableTwoFactorBtn');
  const deleteAccountBtn = document.querySelector('.deleteAccountBtn');

  if (changePasswordBtn) {
    changePasswordBtn.addEventListener('click', () => {
      showNotification('Opening change password form...', 'info');
    });
  }

  if (enableTwoFactorBtn) {
    enableTwoFactorBtn.addEventListener('click', () => {
      const statusIndicator = document.querySelector('.statusIndicator');
      const isEnabled = statusIndicator.classList.contains('enabled');
      
      if (isEnabled) {
        // Disable 2FA
        if (confirm('Are you sure you want to disable two-factor authentication?')) {
          statusIndicator.classList.remove('enabled');
          statusIndicator.classList.add('disabled');
          enableTwoFactorBtn.textContent = 'Enable';
          document.querySelector('.twoFactorStatus span').textContent = 'Two-factor authentication is disabled';
          showNotification('Two-factor authentication disabled', 'info');
        }
      } else {
        // Enable 2FA
        statusIndicator.classList.remove('disabled');
        statusIndicator.classList.add('enabled');
        enableTwoFactorBtn.textContent = 'Disable';
        document.querySelector('.twoFactorStatus span').textContent = 'Two-factor authentication is enabled';
        showNotification('Two-factor authentication enabled successfully!', 'success');
      }
    });
  }

  if (deleteAccountBtn) {
    deleteAccountBtn.addEventListener('click', () => {
      const confirmation = prompt('Type "DELETE" to confirm account deletion:');
      if (confirmation === 'DELETE') {
        showNotification('Account deletion process initiated. Check your email for confirmation.', 'warning');
      } else if (confirmation !== null) {
        showNotification('Account deletion cancelled - confirmation text did not match.', 'error');
      }
    });
  }
}

// Profile Image Upload
function initializeProfileImageUpload() {
  const changeImageBtn = document.getElementById('changeImageBtn');
  const profileImage = document.getElementById('profileImage');

  if (changeImageBtn) {
    changeImageBtn.addEventListener('click', () => {
      // Create hidden file input
      const fileInput = document.createElement('input');
      fileInput.type = 'file';
      fileInput.accept = 'image/*';
      fileInput.style.display = 'none';

      fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = (e) => {
            profileImage.src = e.target.result;
            showNotification('Profile picture updated successfully!', 'success');
          };
          reader.readAsDataURL(file);
        }
      });

      document.body.appendChild(fileInput);
      fileInput.click();
      document.body.removeChild(fileInput);
    });
  }
}

// Utility Functions
function showNotification(message, type = 'info') {
  // Remove existing notifications
  const existingNotifications = document.querySelectorAll('.notification');
  existingNotifications.forEach(n => n.remove());

  // Create notification element
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.innerHTML = `
    <div class="notification-content">
      <i class="fas fa-${getNotificationIcon(type)}"></i>
      <span>${message}</span>
      <button class="notification-close">
        <i class="fas fa-times"></i>
      </button>
    </div>
  `;

  // Add styles
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: ${getNotificationColor(type)};
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    max-width: 400px;
    animation: slideIn 0.3s ease-out;
  `;

  // Add animation styles
  if (!document.querySelector('#notification-styles')) {
    const styles = document.createElement('style');
    styles.id = 'notification-styles';
    styles.textContent = `
      @keyframes slideIn {
        from {
          transform: translateX(100%);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }
      
      @keyframes slideOut {
        from {
          transform: translateX(0);
          opacity: 1;
        }
        to {
          transform: translateX(100%);
          opacity: 0;
        }
      }
      
      .notification-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
      }
      
      .notification-close {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        opacity: 0.8;
        transition: opacity 0.2s ease;
      }
      
      .notification-close:hover {
        opacity: 1;
      }
    `;
    document.head.appendChild(styles);
  }

  // Add to page
  document.body.appendChild(notification);

  // Add close functionality
  const closeBtn = notification.querySelector('.notification-close');
  closeBtn.addEventListener('click', () => {
    removeNotification(notification);
  });

  // Auto remove after 5 seconds
  setTimeout(() => {
    if (notification.parentNode) {
      removeNotification(notification);
    }
  }, 5000);
}

function removeNotification(notification) {
  notification.style.animation = 'slideOut 0.3s ease-out';
  setTimeout(() => {
    if (notification.parentNode) {
      notification.remove();
    }
  }, 300);
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

function getNotificationColor(type) {
  const colors = {
    success: '#28a745',
    error: '#dc3545',
    warning: '#ffc107',
    info: '#17a2b8'
  };
  return colors[type] || '#17a2b8';
}

// Initialize on page load
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeProfile);
} else {
  initializeProfile();
}

function initializeProfile() {
  // Update active navigation states
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  
  // Update navbar
  const navLinks = document.querySelectorAll('.navLink');
  navLinks.forEach(link => {
    if (link.getAttribute('href') === currentPage) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });

  // Set user account as active
  const userAccount = document.getElementById('userAccount');
  if (userAccount) {
    userAccount.classList.add('active');
  }

  // Update burger menu
  const burgerLinks = document.querySelectorAll('.burgerNavLink');
  burgerLinks.forEach(link => {
    if (link.getAttribute('href') === currentPage) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });

  // Set burger user account as active
  const burgerUserAccount = document.getElementById('burgerUserAccount');
  if (burgerUserAccount) {
    burgerUserAccount.closest('.burgerActionLink').classList.add('active');
  }
}
