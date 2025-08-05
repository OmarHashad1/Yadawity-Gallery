// Orders specific JavaScript

class Orders {
  constructor() {
    this.selectedOrders = new Set()
    this.init()
  }

  init() {
    this.setupEventListeners()
    this.loadOrdersData()
    this.setupBulkActions()
  }

  setupEventListeners() {
    // Export orders button
    const exportOrdersBtn = document.getElementById("exportOrdersBtn")
    if (exportOrdersBtn) {
      exportOrdersBtn.addEventListener("click", () => this.exportOrders())
    }

    // Manual order button
    const manualOrderBtn = document.getElementById("manualOrderBtn")
    if (manualOrderBtn) {
      manualOrderBtn.addEventListener("click", () => this.showManualOrderModal())
    }

    // Bulk order actions button
    const bulkOrderActionsBtn = document.getElementById("bulkOrderActionsBtn")
    if (bulkOrderActionsBtn) {
      bulkOrderActionsBtn.addEventListener("click", () => this.showBulkOrderActionsModal())
    }

    // Search functionality
    const searchOrdersBtn = document.getElementById("searchOrdersBtn")
    const orderSearch = document.getElementById("orderSearch")

    if (searchOrdersBtn) {
      searchOrdersBtn.addEventListener("click", () => this.searchOrders())
    }

    if (orderSearch) {
      orderSearch.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
          this.searchOrders()
        }
      })
    }

    // Filter dropdowns
    const orderStatusFilter = document.getElementById("orderStatusFilter")
    const dateRangeFilter = document.getElementById("dateRangeFilter")

    if (orderStatusFilter) {
      orderStatusFilter.addEventListener("change", () => this.applyFilters())
    }

    if (dateRangeFilter) {
      dateRangeFilter.addEventListener("change", () => this.applyFilters())
    }

    // Action buttons
    this.setupActionButtons()
  }

  setupBulkActions() {
    // Select all checkbox
    const selectAllOrders = document.getElementById("selectAllOrders")
    if (selectAllOrders) {
      selectAllOrders.addEventListener("change", (e) => {
        const checkboxes = document.querySelectorAll(".order-checkbox")
        checkboxes.forEach((checkbox) => {
          checkbox.checked = e.target.checked
          if (e.target.checked) {
            this.selectedOrders.add(checkbox.dataset.id)
          } else {
            this.selectedOrders.delete(checkbox.dataset.id)
          }
        })
      })
    }

    // Individual checkboxes
    const checkboxes = document.querySelectorAll(".order-checkbox")
    checkboxes.forEach((checkbox) => {
      checkbox.addEventListener("change", (e) => {
        if (e.target.checked) {
          this.selectedOrders.add(e.target.dataset.id)
        } else {
          this.selectedOrders.delete(e.target.dataset.id)
        }
      })
    })
  }

  setupActionButtons() {
    const viewButtons = document.querySelectorAll(".btn-view")
    const updateButtons = document.querySelectorAll(".btn-reply")

    viewButtons.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        this.viewOrderDetails(btn)
      })
    })

    updateButtons.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        this.updateOrder(btn)
      })
    })
  }

  showManualOrderModal() {
    const fields = [
      { name: "customer", label: "Customer Email", type: "email" },
      { name: "artwork", label: "Artwork", type: "text" },
      { name: "amount", label: "Amount ($)", type: "number" },
      {
        name: "status",
        label: "Status",
        type: "select",
        options: [
          { value: "pending", label: "Pending" },
          { value: "processing", label: "Processing" },
          { value: "shipped", label: "Shipped" },
        ],
      },
      { name: "notes", label: "Notes", type: "textarea" },
    ]

    commonAdmin.showModal("Create Manual Order", commonAdmin.getEditModalContent(fields))
  }

  showBulkOrderActionsModal() {
    if (this.selectedOrders.size === 0) {
      commonAdmin.showNotification("Please select orders first", "error")
      return
    }

    const content = `
            <div class="formGroup">
                <label>Bulk Actions for ${this.selectedOrders.size} selected orders</label>
                <div style="display: grid; gap: 15px; margin-top: 15px;">
                    <button class="btn btn-primary" onclick="orders.bulkOrderAction('process')">
                        <i class="fas fa-cog"></i> Mark as Processing
                    </button>
                    <button class="btn btn-secondary" onclick="orders.bulkOrderAction('ship')">
                        <i class="fas fa-truck"></i> Mark as Shipped
                    </button>
                    <button class="btn btn-outline" onclick="orders.bulkOrderAction('export')">
                        <i class="fas fa-download"></i> Export Selected
                    </button>
                </div>
            </div>
            <div class="formActions">
                <button class="btn btn-outline" onclick="commonAdmin.closeModal()">Cancel</button>
            </div>
        `
    commonAdmin.showModal("Bulk Order Actions", content)
  }

  viewOrderDetails(button) {
    const row = button.closest("tr")
    const orderId = row.cells[1].textContent
    const customerName = row.querySelector('div[style*="font-weight: 600"]').textContent

    const data = {
      "Order ID": orderId,
      Customer: customerName,
      Artwork: row.cells[3].textContent,
      Amount: row.cells[4].textContent,
      Date: row.cells[5].textContent,
      Status: row.cells[6].textContent.trim(),
    }

    commonAdmin.showModal("Order Details", commonAdmin.getViewModalContent(data))
  }

  updateOrder(button) {
    const row = button.closest("tr")
    const orderId = row.cells[1].textContent
    const customerName = row.querySelector('div[style*="font-weight: 600"]').textContent

    const fields = [
      { name: "orderId", label: "Order ID", value: orderId, type: "text", readonly: true },
      { name: "customer", label: "Customer", value: customerName, type: "text" },
      {
        name: "status",
        label: "Status",
        value: "processing",
        type: "select",
        options: [
          { value: "pending", label: "Pending" },
          { value: "processing", label: "Processing" },
          { value: "shipped", label: "Shipped" },
          { value: "delivered", label: "Delivered" },
          { value: "cancelled", label: "Cancelled" },
        ],
      },
      { name: "trackingNumber", label: "Tracking Number", type: "text" },
      { name: "notes", label: "Notes", type: "textarea" },
    ]

    commonAdmin.showModal("Update Order", commonAdmin.getEditModalContent(fields))
  }

  bulkOrderAction(action) {
    const count = this.selectedOrders.size
    let message = ""

    switch (action) {
      case "process":
        message = `Marking ${count} orders as processing...`
        break
      case "ship":
        message = `Marking ${count} orders as shipped...`
        break
      case "export":
        message = `Exporting ${count} orders...`
        break
    }

    commonAdmin.showNotification(message, "info")

    setTimeout(() => {
      commonAdmin.showNotification(`Bulk action completed for ${count} orders!`, "success")
      this.selectedOrders.clear()
      document.getElementById("selectAllOrders").checked = false
      document.querySelectorAll(".order-checkbox").forEach((cb) => (cb.checked = false))
    }, 2000)

    commonAdmin.closeModal()
  }

  searchOrders() {
    const searchTerm = document.getElementById("orderSearch").value.trim()
    if (searchTerm) {
      commonAdmin.showNotification(`Searching for: ${searchTerm}`, "info")
      // Implement search functionality here
    }
  }

  applyFilters() {
    const status = document.getElementById("orderStatusFilter").value
    const dateRange = document.getElementById("dateRangeFilter").value

    commonAdmin.showNotification("Applying filters...", "info")
    // Implement filter functionality here
  }

  exportOrders() {
    commonAdmin.showNotification("Exporting order data...", "info")
    setTimeout(() => {
      commonAdmin.showNotification("Order data exported successfully!", "success")
    }, 2000)
  }

  loadOrdersData() {
    // Simulate loading orders data
    const orders = [
      {
        id: "ORD-2024-001",
        customer: { name: "Sarah Johnson", email: "sarah@example.com" },
        artwork: "Abstract Harmony",
        amount: 3200,
        date: "Mar 15, 2024",
        status: "processing",
      },
      {
        id: "ORD-2024-002",
        customer: { name: "Michael Brown", email: "michael@example.com" },
        artwork: "Urban Landscape",
        amount: 2800,
        date: "Mar 14, 2024",
        status: "shipped",
      },
      {
        id: "ORD-2024-003",
        customer: { name: "Emma Davis", email: "emma@example.com" },
        artwork: "Portrait Study #7",
        amount: 1950,
        date: "Mar 13, 2024",
        status: "delivered",
      },
    ]

    this.updateOrdersTable(orders)
  }

  updateOrdersTable(orders) {
    const tbody = document.getElementById("ordersTable")
    if (!tbody) return

    tbody.innerHTML = orders
      .map(
        (order) => `
            <tr>
                <td>
                    <input type="checkbox" class="order-checkbox" data-id="${order.id}">
                </td>
                <td>${order.id}</td>
                <td>
                    <div>
                        <div style="font-weight: 600;">${order.customer.name}</div>
                        <div style="font-size: 0.8rem; color: var(--text-light);">${order.customer.email}</div>
                    </div>
                </td>
                <td>${order.artwork}</td>
                <td>$${order.amount.toLocaleString()}</td>
                <td>${order.date}</td>
                <td><span class="statusBadge status-${order.status === "processing" ? "pending" : order.status === "shipped" ? "open" : "resolved"}">${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span></td>
                <td>
                    <button class="actionBtn btn-view">View</button>
                    <button class="actionBtn btn-reply">${order.status === "shipped" ? "Track" : "Update"}</button>
                </td>
            </tr>
        `,
      )
      .join("")

    // Re-setup action buttons and bulk actions for new content
    this.setupActionButtons()
    this.setupBulkActions()
  }
}

// Initialize orders
let orders
document.addEventListener("DOMContentLoaded", () => {
  orders = new Orders()
})
