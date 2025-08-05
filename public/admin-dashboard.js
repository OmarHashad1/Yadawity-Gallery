import { Chart } from "@/components/ui/chart"
// Dashboard JavaScript
class Dashboard {
  constructor() {
    this.charts = {}
    this.currentSection = "dashboard"
    this.init()
  }

  init() {
    this.setupEventListeners()
    this.initializeCharts()
    this.startRealTimeUpdates()
    this.setupSidebar()
  }

  setupEventListeners() {
    // Sidebar navigation
    document.querySelectorAll(".sidebarLink").forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault()
        const section = link.getAttribute("data-section")
        this.switchSection(section)
      })
    })

    // Sidebar toggle for mobile
    const sidebarToggle = document.getElementById("sidebarToggle")
    if (sidebarToggle) {
      sidebarToggle.addEventListener("click", () => {
        this.toggleSidebar()
      })
    }

    // Control panel filters
    document.getElementById("timePeriod").addEventListener("change", (e) => {
      this.updateDataByTimePeriod(e.target.value)
    })

    document.getElementById("category").addEventListener("change", (e) => {
      this.filterByCategory(e.target.value)
    })

    // Search functionality
    document.getElementById("searchBtn").addEventListener("click", () => {
      this.performSearch()
    })

    document.getElementById("searchInput").addEventListener("keypress", (e) => {
      if (e.key === "Enter") {
        this.performSearch()
      }
    })

    // Action buttons
    document.getElementById("refreshBtn").addEventListener("click", () => {
      this.refreshData()
    })

    document.getElementById("exportBtn").addEventListener("click", () => {
      this.exportData()
    })

    // Table action buttons
    document.querySelectorAll(".actionBtn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const action = e.target.classList.contains("btn-view") ? "view" : "reply"
        const row = e.target.closest("tr")
        this.handleTableAction(action, row)
      })
    })

    // Modal close
    document.getElementById("modalClose").addEventListener("click", () => {
      this.closeModal()
    })

    // Close modal on backdrop click
    document.getElementById("detailModal").addEventListener("click", (e) => {
      if (e.target.id === "detailModal") {
        this.closeModal()
      }
    })
  }

  setupSidebar() {
    // Add mobile menu toggle button to navbar
    const navContainer = document.querySelector(".navContainer")
    const mobileToggle = document.createElement("button")
    mobileToggle.className = "sidebarToggle"
    mobileToggle.innerHTML = '<i class="fas fa-bars"></i>'
    mobileToggle.style.display = "none"
    mobileToggle.addEventListener("click", () => {
      this.toggleSidebar()
    })

    // Insert before nav menu
    navContainer.insertBefore(mobileToggle, navContainer.querySelector(".navMenu"))

    // Show/hide mobile toggle based on screen size
    const checkScreenSize = () => {
      if (window.innerWidth <= 1024) {
        mobileToggle.style.display = "block"
      } else {
        mobileToggle.style.display = "none"
        document.getElementById("sidebar").classList.remove("active")
      }
    }

    window.addEventListener("resize", checkScreenSize)
    checkScreenSize()
  }

  toggleSidebar() {
    const sidebar = document.getElementById("sidebar")
    sidebar.classList.toggle("active")
  }

  switchSection(sectionName) {
    // Hide all sections
    document.querySelectorAll(".content-section").forEach((section) => {
      section.classList.remove("active")
    })

    // Show selected section
    const targetSection = document.getElementById(`${sectionName}-section`)
    if (targetSection) {
      targetSection.classList.add("active")
    }

    // Update sidebar active state
    document.querySelectorAll(".sidebarLink").forEach((link) => {
      link.classList.remove("active")
    })

    const activeLink = document.querySelector(`[data-section="${sectionName}"]`)
    if (activeLink) {
      activeLink.classList.add("active")
    }

    this.currentSection = sectionName

    // Initialize section-specific functionality
    if (sectionName === "analytics") {
      setTimeout(() => {
        this.initializeAnalyticsCharts()
      }, 100)
    }
  }

  initializeCharts() {
    this.initializeRevenueChart()
    this.initializeTrafficChart()
  }

  initializeRevenueChart() {
    const ctx = document.getElementById("revenueChart")
    if (!ctx) return

    this.charts.revenue = new Chart(ctx, {
      type: "line",
      data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
        datasets: [
          {
            label: "Revenue",
            data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
            borderColor: "#6b4423",
            backgroundColor: "rgba(107, 68, 35, 0.1)",
            borderWidth: 3,
            fill: true,
            tension: 0.4,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: (value) => "$" + value.toLocaleString(),
            },
          },
        },
      },
    })
  }

  initializeTrafficChart() {
    const ctx = document.getElementById("trafficChart")
    if (!ctx) return

    this.charts.traffic = new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: ["Direct", "Social Media", "Email", "Search", "Referral"],
        datasets: [
          {
            data: [35, 25, 20, 15, 5],
            backgroundColor: ["#6b4423", "#d4a574", "#8b5a2b", "#5a7c65", "#c5534a"],
            borderWidth: 0,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
            labels: {
              padding: 20,
              usePointStyle: true,
            },
          },
        },
      },
    })
  }

  initializeAnalyticsCharts() {
    this.initializeEngagementChart()
    this.initializeSalesChart()
  }

  initializeEngagementChart() {
    const ctx = document.getElementById("engagementChart")
    if (!ctx || this.charts.engagement) return

    this.charts.engagement = new Chart(ctx, {
      type: "bar",
      data: {
        labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
        datasets: [
          {
            label: "Page Views",
            data: [1200, 1900, 1500, 2500, 2200, 1800, 1600],
            backgroundColor: "#d4a574",
            borderRadius: 8,
          },
          {
            label: "Unique Visitors",
            data: [800, 1200, 1000, 1600, 1400, 1100, 1000],
            backgroundColor: "#6b4423",
            borderRadius: 8,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "top",
          },
        },
        scales: {
          y: {
            beginAtZero: true,
          },
        },
      },
    })
  }

  initializeSalesChart() {
    const ctx = document.getElementById("salesChart")
    if (!ctx || this.charts.sales) return

    this.charts.sales = new Chart(ctx, {
      type: "line",
      data: {
        labels: ["Week 1", "Week 2", "Week 3", "Week 4"],
        datasets: [
          {
            label: "Sales",
            data: [4500, 5200, 4800, 6100],
            borderColor: "#5a7c65",
            backgroundColor: "rgba(90, 124, 101, 0.1)",
            borderWidth: 3,
            fill: true,
            tension: 0.4,
          },
          {
            label: "Target",
            data: [5000, 5000, 5000, 5000],
            borderColor: "#c5534a",
            borderDash: [5, 5],
            borderWidth: 2,
            fill: false,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "top",
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: (value) => "$" + value.toLocaleString(),
            },
          },
        },
      },
    })
  }

  updateDataByTimePeriod(period) {
    console.log("Updating data for period:", period)

    // Simulate data update based on time period
    const multipliers = {
      today: 0.3,
      week: 1,
      month: 4.3,
      year: 52,
    }

    const multiplier = multipliers[period] || 1

    // Update stats
    document.getElementById("totalRevenue").textContent = "$" + Math.round(24580 * multiplier).toLocaleString()
    document.getElementById("totalUsers").textContent = Math.round(1247 * multiplier).toLocaleString()
    document.getElementById("totalOrders").textContent = Math.round(342 * multiplier).toLocaleString()

    // Update charts if they exist
    if (this.charts.revenue) {
      const newData = this.charts.revenue.data.datasets[0].data.map((val) =>
        Math.round(val * multiplier * (0.8 + Math.random() * 0.4)),
      )
      this.charts.revenue.data.datasets[0].data = newData
      this.charts.revenue.update()
    }

    this.showNotification("Data updated for " + period, "success")
  }

  filterByCategory(category) {
    console.log("Filtering by category:", category)
    this.showNotification("Filtered by category: " + category, "info")
  }

  performSearch() {
    const query = document.getElementById("searchInput").value
    console.log("Searching for:", query)

    if (query.trim()) {
      this.showNotification("Searching for: " + query, "info")
      // Simulate search results
      setTimeout(() => {
        this.showNotification('Found 12 results for "' + query + '"', "success")
      }, 1000)
    }
  }

  refreshData() {
    this.showNotification("Refreshing data...", "info")

    // Simulate data refresh
    setTimeout(() => {
      // Update stats with random variations
      const revenue = 24580 + Math.round((Math.random() - 0.5) * 5000)
      const users = 1247 + Math.round((Math.random() - 0.5) * 200)
      const orders = 342 + Math.round((Math.random() - 0.5) * 50)

      document.getElementById("totalRevenue").textContent = "$" + revenue.toLocaleString()
      document.getElementById("totalUsers").textContent = users.toLocaleString()
      document.getElementById("totalOrders").textContent = orders.toLocaleString()

      this.showNotification("Data refreshed successfully!", "success")
    }, 1500)
  }

  exportData() {
    this.showNotification("Preparing export...", "info")

    // Simulate export process
    setTimeout(() => {
      // Create a simple CSV export simulation
      const csvContent =
        "data:text/csv;charset=utf-8," +
        "Date,Revenue,Users,Orders\n" +
        "2024-01-01,24580,1247,342\n" +
        "2024-01-02,25120,1289,356\n" +
        "2024-01-03,23890,1198,328\n"

      const encodedUri = encodeURI(csvContent)
      const link = document.createElement("a")
      link.setAttribute("href", encodedUri)
      link.setAttribute("download", "dashboard_data.csv")
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)

      this.showNotification("Data exported successfully!", "success")
    }, 1000)
  }

  handleTableAction(action, row) {
    const cells = row.querySelectorAll("td")
    const userData = {
      time: cells[0].textContent,
      user: cells[1].textContent,
      action: cells[2].textContent,
      status: cells[3].textContent,
      value: cells[4].textContent,
    }

    if (action === "view") {
      this.showModal("User Details", this.generateUserDetailsHTML(userData))
    } else if (action === "reply") {
      this.showModal("Contact User", this.generateContactFormHTML(userData))
    }
  }

  generateUserDetailsHTML(userData) {
    return `
            <div class="formGroup">
                <label>Time:</label>
                <p>${userData.time}</p>
            </div>
            <div class="formGroup">
                <label>User:</label>
                <p>${userData.user}</p>
            </div>
            <div class="formGroup">
                <label>Action:</label>
                <p>${userData.action}</p>
            </div>
            <div class="formGroup">
                <label>Status:</label>
                <p>${userData.status}</p>
            </div>
            <div class="formGroup">
                <label>Value:</label>
                <p>${userData.value}</p>
            </div>
        `
  }

  generateContactFormHTML(userData) {
    return `
            <form id="contactForm">
                <div class="formGroup">
                    <label>To:</label>
                    <input type="text" value="${userData.user}" readonly>
                </div>
                <div class="formGroup">
                    <label>Subject:</label>
                    <input type="text" value="Re: ${userData.action}" required>
                </div>
                <div class="formGroup">
                    <label>Message:</label>
                    <textarea rows="5" placeholder="Enter your message..." required></textarea>
                </div>
                <div class="formActions">
                    <button type="button" class="btn btn-outline" onclick="dashboard.closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </form>
        `
  }

  showModal(title, content) {
    document.getElementById("modalTitle").textContent = title
    document.getElementById("modalBody").innerHTML = content
    document.getElementById("detailModal").classList.add("active")

    // Add form submission handler if it's a contact form
    const form = document.getElementById("contactForm")
    if (form) {
      form.addEventListener("submit", (e) => {
        e.preventDefault()
        this.showNotification("Message sent successfully!", "success")
        this.closeModal()
      })
    }
  }

  closeModal() {
    document.getElementById("detailModal").classList.remove("active")
  }

  showNotification(message, type = "info") {
    // Create notification element
    const notification = document.createElement("div")
    notification.className = `notification notification-${type}`
    notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            background: ${type === "success" ? "#5a7c65" : type === "error" ? "#c5534a" : "#6b4423"};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            z-index: 3000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 300px;
        `
    notification.textContent = message

    document.body.appendChild(notification)

    // Animate in
    setTimeout(() => {
      notification.style.transform = "translateX(0)"
    }, 100)

    // Auto remove after 3 seconds
    setTimeout(() => {
      notification.style.transform = "translateX(100%)"
      setTimeout(() => {
        if (notification.parentNode) {
          document.body.removeChild(notification)
        }
      }, 300)
    }, 3000)
  }

  startRealTimeUpdates() {
    // Simulate real-time data updates
    setInterval(() => {
      if (this.currentSection === "dashboard") {
        this.updateRealTimeStats()
      }
    }, 30000) // Update every 30 seconds

    // Update activity table periodically
    setInterval(() => {
      this.addNewActivity()
    }, 45000) // Add new activity every 45 seconds
  }

  updateRealTimeStats() {
    // Simulate small changes in stats
    const revenueElement = document.getElementById("totalRevenue")
    const usersElement = document.getElementById("totalUsers")
    const ordersElement = document.getElementById("totalOrders")

    if (revenueElement) {
      const currentRevenue = Number.parseInt(revenueElement.textContent.replace(/[$,]/g, ""))
      const change = Math.round((Math.random() - 0.5) * 1000)
      const newRevenue = Math.max(0, currentRevenue + change)
      revenueElement.textContent = "$" + newRevenue.toLocaleString()
    }

    if (usersElement) {
      const currentUsers = Number.parseInt(usersElement.textContent.replace(/,/g, ""))
      const change = Math.round((Math.random() - 0.5) * 20)
      const newUsers = Math.max(0, currentUsers + change)
      usersElement.textContent = newUsers.toLocaleString()
    }

    if (ordersElement) {
      const currentOrders = Number.parseInt(ordersElement.textContent.replace(/,/g, ""))
      const change = Math.round((Math.random() - 0.5) * 10)
      const newOrders = Math.max(0, currentOrders + change)
      ordersElement.textContent = newOrders.toLocaleString()
    }
  }

  addNewActivity() {
    const tableBody = document.getElementById("activityTableBody")
    if (!tableBody) return

    const activities = [
      {
        user: "Alex Johnson",
        action: "Product purchased",
        status: "status-resolved",
        value: "$" + Math.round(Math.random() * 500 + 50),
      },
      { user: "Maria Garcia", action: "Account created", status: "status-open", value: "-" },
      { user: "David Chen", action: "Support ticket", status: "status-pending", value: "-" },
      { user: "Lisa Brown", action: "Newsletter signup", status: "status-resolved", value: "-" },
      { user: "Tom Wilson", action: "Product review", status: "status-resolved", value: "4 stars" },
    ]

    const randomActivity = activities[Math.floor(Math.random() * activities.length)]
    const now = new Date()
    const timeAgo = "Just now"

    const newRow = document.createElement("tr")
    newRow.innerHTML = `
            <td>${timeAgo}</td>
            <td>${randomActivity.user}</td>
            <td>${randomActivity.action}</td>
            <td><span class="statusBadge ${randomActivity.status}">${randomActivity.status.replace("status-", "").charAt(0).toUpperCase() + randomActivity.status.replace("status-", "").slice(1)}</span></td>
            <td>${randomActivity.value}</td>
            <td>
                <button class="actionBtn btn-view">View</button>
                <button class="actionBtn btn-reply">Contact</button>
            </td>
        `

    // Add event listeners to new buttons
    newRow.querySelectorAll(".actionBtn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const action = e.target.classList.contains("btn-view") ? "view" : "reply"
        const row = e.target.closest("tr")
        this.handleTableAction(action, row)
      })
    })

    // Insert at the beginning and remove last row if more than 10 rows
    tableBody.insertBefore(newRow, tableBody.firstChild)
    if (tableBody.children.length > 10) {
      tableBody.removeChild(tableBody.lastChild)
    }

    // Highlight new row briefly
    newRow.style.backgroundColor = "rgba(212, 165, 116, 0.2)"
    setTimeout(() => {
      newRow.style.backgroundColor = ""
    }, 2000)
  }
}

// Initialize dashboard when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  window.dashboard = new Dashboard()
})

// Handle window resize for responsive charts
window.addEventListener("resize", () => {
  if (window.dashboard && window.dashboard.charts) {
    Object.values(window.dashboard.charts).forEach((chart) => {
      if (chart) {
        chart.resize()
      }
    })
  }
})
