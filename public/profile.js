// Profile page JS
// Sidebar navigation
const navItems = document.querySelectorAll('.profile-nav li');
const sections = document.querySelectorAll('.profile-section');
navItems.forEach(item => {
    item.addEventListener('click', () => {
        navItems.forEach(i => i.classList.remove('active'));
        item.classList.add('active');
        sections.forEach(sec => sec.classList.remove('active'));
        document.getElementById(item.dataset.section).classList.add('active');
        // If reviews tab is clicked, load reviews
        if(item.dataset.section === 'reviews'){
            loadReviews();
        }
    });
});
// Fetch reviews
function loadReviews(){
    fetch('API/getArtistReviews.php')
        .then(res => res.json())
        .then(data => {
            const reviewList = document.getElementById('reviewList');
            if(data.reviews && data.reviews.length){
                reviewList.innerHTML = data.reviews.map(review => `
                    <div class='order-item'>
                        <strong>${review.reviewer}</strong><br>
                        <span>Rating: ${review.rating} / 5</span><br>
                        <p>${review.comment}</p>
                    </div>
                `).join('');
            } else {
                reviewList.innerHTML = '<p>No reviews found.</p>';
            }
        });
}

// Fetch user info
fetch('API/getUserInfo.php')
    .then(res => res.json())
    .then(response => {
        // If API response is wrapped in 'data', use response.data
        const data = response.data || response;
        document.getElementById('firstName').value = data.first_name || data.firstName || '';
        document.getElementById('lastName').value = data.last_name || data.lastName || '';
        document.getElementById('email').value = data.email || '';
        document.getElementById('phone').value = data.phone || '';
        document.getElementById('bio').value = data.bio || '';
        document.getElementById('memberSince').textContent = data.created_at ? new Date(data.created_at).getFullYear() : '';
        document.getElementById('purchaseCount').textContent = data.purchases || 0;
        document.getElementById('wishlistCount').textContent = data.wishlist || 0;
        document.getElementById('reviewCount').textContent = data.reviews || 0;
    });

// Edit/save personal info
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('editPersonal');
    const saveBtn = document.getElementById('savePersonal');
    const personalForm = document.getElementById('personalForm');
    if (editBtn && personalForm && saveBtn) {
        editBtn.addEventListener('click', () => {
            console.log('Edit button clicked');
            Array.from(personalForm.elements).forEach(el => el.disabled = false);
            editBtn.style.display = 'none';
            saveBtn.style.display = 'inline-block';
        });
        personalForm.addEventListener('submit', e => {
            e.preventDefault();
            const formData = new FormData(personalForm);
            fetch('API/updateUserInfo.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    alert('Profile updated!');
                    Array.from(personalForm.elements).forEach(el => el.disabled = true);
                    editBtn.style.display = 'inline-block';
                    saveBtn.style.display = 'none';
                } else {
                    alert('Update failed!');
                }
            });
        });
        Array.from(personalForm.elements).forEach(el => el.disabled = true);
    }
});

// Fetch order history
fetch('API/getUserOrder.php')
    .then(res => res.json())
    .then(data => {
        const orderList = document.getElementById('orderList');
        orderList.innerHTML = data.orders && data.orders.length ?
            data.orders.map(order => `<div class='order-item'>Order #${order.id} - ${order.status}</div>`).join('') :
            '<p>No orders found.</p>';
    });

// Fetch addresses
function loadAddresses(){
    fetch('API/getUserInfo.php')
        .then(res => res.json())
        .then(data => {
            const addressList = document.getElementById('addressList');
            if(data.addresses && data.addresses.length){
                addressList.innerHTML = data.addresses.map(addr => `
                    <div class='address-card'>
                        <strong>${addr.type} Address</strong><br>
                        Name: ${addr.name}<br>
                        Street: ${addr.street}<br>
                        City: ${addr.city}<br>
                        Phone: ${addr.phone}<br>
                        <span class='default-badge'>${addr.default ? 'Default' : ''}</span>
                        <button class='edit-address-btn' data-id='${addr.id}'>Edit</button>
                    </div>
                `).join('');
            } else {
                addressList.innerHTML = '<p>No addresses found.</p>';
            }
        });
}
loadAddresses();

// Add address
const addAddressBtn = document.getElementById('addAddress');
addAddressBtn.addEventListener('click', () => {
    // Show address form (implement modal or inline form as needed)
    // On submit, POST to API/addAddress.php
    // Then reload addresses
    alert('Add address form goes here.');
});

// Change password
const changePasswordBtn = document.getElementById('changePassword');
changePasswordBtn.addEventListener('click', () => {
    // Show password change form (implement modal or inline form as needed)
    // On submit, POST to API/changePassword.php
    alert('Change password form goes here.');
});
