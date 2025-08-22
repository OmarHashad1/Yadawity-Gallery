// Enhanced Dashboard JavaScript with Smooth Animations
document.addEventListener("DOMContentLoaded", () => {
    // Check authentication
    if (!localStorage.getItem("csrf_token")) {
      window.location.href = "login.php"
      return
    }
  
    // Display user info with animation
    const userInfo = document.getElementById("userInfo")
    const userName = localStorage.getItem("user_name") || "Admin"
    userInfo.textContent = `Welcome, ${userName}`
    userInfo.style.opacity = "0"
    setTimeout(() => {
      userInfo.style.transition = "opacity 0.5s ease-in-out"
      userInfo.style.opacity = "1"
    }, 100)
  
    // Enhanced sidebar toggle with smooth animations
    const sidebarToggle = document.getElementById("sidebarToggle")
    if (sidebarToggle) {
      sidebarToggle.addEventListener("click", () => {
        document.body.classList.toggle("sidebar-open")
  
        // Add ripple effect
        const ripple = document.createElement("span")
        ripple.style.cssText = `
                  position: absolute;
                  border-radius: 50%;
                  background: rgba(255,255,255,0.3);
                  transform: scale(0);
                  animation: ripple 0.6s linear;
                  pointer-events: none;
              `
  
        const rect = sidebarToggle.getBoundingClientRect()
        const size = Math.max(rect.width, rect.height)
        ripple.style.width = ripple.style.height = size + "px"
        ripple.style.left = rect.width / 2 - size / 2 + "px"
        ripple.style.top = rect.height / 2 - size / 2 + "px"
  
        sidebarToggle.appendChild(ripple)
        setTimeout(() => ripple.remove(), 600)
      })
    }
  
    // Add active state to current page navigation
    highlightCurrentPage()
  
    // Load dashboard data with loading animation
    loadDashboardData()
  
    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = "smooth"
  })
  
  function highlightCurrentPage() {
    const currentPage = window.location.pathname.split("/").pop()
    const navLinks = document.querySelectorAll("#sidebar .nav-link")
  
    navLinks.forEach((link) => {
      const href = link.getAttribute("href")
      if (href === currentPage || (currentPage === "" && href === "dashboard.php")) {
        link.classList.add("active")
      }
    })
  }
  
  async function loadDashboardData() {
    // Add loading state to all metric cards
    const metricCards = document.querySelectorAll('[id$="Total"], [id*="users"], [id*="artworks"], [id*="auctions"]')
    metricCards.forEach((card) => {
      const cardElement = card.closest(".card")
      if (cardElement) {
        cardElement.classList.add("loading")
      }
    })
  
    try {
      const response = await fetch("/admin-system/API/dashboard.php", {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      })
  
      if (response.status === 401 || response.status === 403) {
        // Redirect to login if unauthorized
        localStorage.clear()
        window.location.href = "login.php"
        return
      }
  
      const data = await response.json()
  
      if (response.ok && data.data) {
        // Remove loading states
        metricCards.forEach((card) => {
          const cardElement = card.closest(".card")
          if (cardElement) {
            cardElement.classList.remove("loading")
          }
        })
  
        // Update metrics with staggered animation
        updateDashboardMetrics(data.data)
      } else {
        console.error("Failed to load dashboard data:", data.error)
        showErrorState()
      }
    } catch (error) {
      console.error("Error loading dashboard data:", error)
      showErrorState()
    }
  }
  
  function updateDashboardMetrics(metrics) {
    // Animate number updates
    animateNumber("usersTotal", metrics.users_total || 0)
    animateNumber("artworksTotal", metrics.artworks_total || 0)
    animateNumber("ordersTotal", metrics.orders_total || 0)
    animateRevenue("revenueTotal", metrics.revenue_total || 0)
  
    // Update user breakdown with delay
    setTimeout(() => {
      animateNumber("usersArtists", metrics.users_artists || 0)
      animateNumber("usersBuyers", metrics.users_buyers || 0)
    }, 200)
  
    // Update artwork status with delay
    setTimeout(() => {
      animateNumber("artworksAvailable", metrics.artworks_available || 0)
      animateNumber("artworksOnAuction", metrics.artworks_on_auction || 0)
    }, 400)
  
    // Update orders by status
    setTimeout(() => {
      updateOrdersByStatus(metrics.orders_by_status)
    }, 600)
  
    // Update auctions with delay
    setTimeout(() => {
      animateNumber("auctionsActive", metrics.auctions_active || 0)
      animateNumber("auctionsUpcoming", metrics.auctions_upcoming || 0)
    }, 800)
  }
  
  function animateNumber(elementId, targetValue, duration = 1000) {
    const element = document.getElementById(elementId)
    if (!element) return
  
    const startValue = 0
    const startTime = performance.now()
  
    function updateNumber(currentTime) {
      const elapsed = currentTime - startTime
      const progress = Math.min(elapsed / duration, 1)
  
      // Easing function for smooth animation
      const easeOutQuart = 1 - Math.pow(1 - progress, 4)
      const currentValue = Math.floor(startValue + (targetValue - startValue) * easeOutQuart)
  
      element.textContent = currentValue
  
      if (progress < 1) {
        requestAnimationFrame(updateNumber)
      } else {
        element.textContent = targetValue
      }
    }
  
    requestAnimationFrame(updateNumber)
  }
  
  function animateRevenue(elementId, targetValue, duration = 1000) {
    const element = document.getElementById(elementId)
    if (!element) return
  
    const startValue = 0
    const startTime = performance.now()
  
    function updateRevenue(currentTime) {
      const elapsed = currentTime - startTime
      const progress = Math.min(elapsed / duration, 1)
  
      const easeOutQuart = 1 - Math.pow(1 - progress, 4)
      const currentValue = Math.floor(startValue + (targetValue - startValue) * easeOutQuart)
  
      element.textContent = `$${currentValue.toLocaleString()}`
  
      if (progress < 1) {
        requestAnimationFrame(updateRevenue)
      } else {
        element.textContent = `$${targetValue.toLocaleString()}`
      }
    }
  
    requestAnimationFrame(updateRevenue)
  }
  
  function updateOrdersByStatus(ordersByStatus) {
    const container = document.getElementById("ordersByStatus")
  
    if (!ordersByStatus || Object.keys(ordersByStatus).length === 0) {
      container.innerHTML = '<p class="text-muted">No orders data available</p>'
      return
    }
  
    let html = ""
    for (const [status, count] of Object.entries(ordersByStatus)) {
      const statusClass = getStatusClass(status)
      html += `
              <div class="d-flex justify-content-between align-items-center mb-2" style="opacity: 0; transform: translateX(-20px);">
                  <span class="badge ${statusClass}">${status}</span>
                  <span class="fw-bold">${count}</span>
              </div>
          `
    }
  
    container.innerHTML = html
  
    // Animate each status item
    const statusItems = container.querySelectorAll(".d-flex")
    statusItems.forEach((item, index) => {
      setTimeout(() => {
        item.style.transition = "opacity 0.3s ease-out, transform 0.3s ease-out"
        item.style.opacity = "1"
        item.style.transform = "translateX(0)"
      }, index * 100)
    })
  }
  
  function getStatusClass(status) {
    const statusClasses = {
      pending: "bg-warning",
      confirmed: "bg-info",
      shipped: "bg-primary",
      delivered: "bg-success",
      cancelled: "bg-danger",
    }
    return statusClasses[status] || "bg-secondary"
  }
  
  function showErrorState() {
    const metricCards = document.querySelectorAll('[id$="Total"], [id*="users"], [id*="artworks"], [id*="auctions"]')
    metricCards.forEach((card) => {
      const cardElement = card.closest(".card")
      if (cardElement) {
        cardElement.classList.remove("loading")
      }
      card.textContent = "Error"
      card.style.color = "var(--color-error)"
    })
  }
  
  function logout() {
    // Add loading state to logout button
    const logoutBtn = event.target
    const originalText = logoutBtn.textContent
    logoutBtn.textContent = "Logging out..."
    logoutBtn.disabled = true
  
    // Call logout API
    fetch("/admin-system/API/logout.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": localStorage.getItem("csrf_token"),
      },
    }).finally(() => {
      // Clear localStorage and redirect to login
      localStorage.clear()
  
      // Add fade out animation before redirect
      document.body.style.transition = "opacity 0.3s ease-out"
      document.body.style.opacity = "0"
  
      setTimeout(() => {
        window.location.href = "login.php"
      }, 300)
    })
  }
  
  // Add CSS animation keyframes dynamically
  const style = document.createElement("style")
  style.textContent = `
      @keyframes ripple {
          to {
              transform: scale(4);
              opacity: 0;
          }
      }
  `
  document.head.appendChild(style)
  