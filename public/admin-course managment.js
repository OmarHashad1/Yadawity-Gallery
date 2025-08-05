// Artworks specific JavaScript

class Artworks {
  constructor() {
    this.selectedArtworks = new Set()
    this.init()
  }

  init() {
    this.setupEventListeners()
    this.loadArtworksData()
    this.setupBulkActions()
  }

  setupEventListeners() {
    // Filter artworks button
    const filterArtworksBtn = document.getElementById("filterArtworksBtn")
    if (filterArtworksBtn) {
      filterArtworksBtn.addEventListener("click", () => this.showFilterModal())
    }

    // Add artwork button
    const addArtworkBtn = document.getElementById("addArtworkBtn")
    if (addArtworkBtn) {
      addArtworkBtn.addEventListener("click", () => this.showAddArtworkModal())
    }

    // Bulk actions button
    const bulkActionsBtn = document.getElementById("bulkActionsBtn")
    if (bulkActionsBtn) {
      bulkActionsBtn.addEventListener("click", () => this.showBulkActionsModal())
    }

    // Search functionality
    const searchArtworksBtn = document.getElementById("searchArtworksBtn")
    const artworkSearch = document.getElementById("artworkSearch")

    if (searchArtworksBtn) {
      searchArtworksBtn.addEventListener("click", () => this.searchArtworks())
    }

    if (artworkSearch) {
      artworkSearch.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
          this.searchArtworks()
        }
      })
    }

    // Filter dropdowns
    const categoryFilter = document.getElementById("categoryFilter")
    const statusFilter = document.getElementById("statusFilter")

    if (categoryFilter) {
      categoryFilter.addEventListener("change", () => this.applyFilters())
    }

    if (statusFilter) {
      statusFilter.addEventListener("change", () => this.applyFilters())
    }

    // Action buttons
    this.setupActionButtons()
  }

  setupBulkActions() {
    // Select all checkbox
    const selectAll = document.getElementById("selectAll")
    if (selectAll) {
      selectAll.addEventListener("change", (e) => {
        const checkboxes = document.querySelectorAll(".artwork-checkbox")
        checkboxes.forEach((checkbox) => {
          checkbox.checked = e.target.checked
          if (e.target.checked) {
            this.selectedArtworks.add(checkbox.dataset.id)
          } else {
            this.selectedArtworks.delete(checkbox.dataset.id)
          }
        })
      })
    }

    // Individual checkboxes
    const checkboxes = document.querySelectorAll(".artwork-checkbox")
    checkboxes.forEach((checkbox) => {
      checkbox.addEventListener("change", (e) => {
        if (e.target.checked) {
          this.selectedArtworks.add(e.target.dataset.id)
        } else {
          this.selectedArtworks.delete(e.target.dataset.id)
        }
      })
    })
  }

  setupActionButtons() {
    const reviewButtons = document.querySelectorAll(".btn-view")
    const approveButtons = document.querySelectorAll(".btn-reply")

    reviewButtons.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        this.reviewArtwork(btn)
      })
    })

    approveButtons.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        this.approveArtwork(btn)
      })
    })
  }

  showFilterModal() {
    const content = `
            <div class="formGroup">
                <label for="filterCategory">Category</label>
                <select id="filterCategory">
                    <option value="">All Categories</option>
                    <option value="painting">Painting</option>
                    <option value="photography">Photography</option>
                    <option value="sculpture">Sculpture</option>
                    <option value="digital">Digital Art</option>
                </select>
            </div>
            <div class="formGroup">
                <label for="filterArtworkStatus">Status</label>
                <select id="filterArtworkStatus">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="featured">Featured</option>
                </select>
            </div>
            <div class="formGroup">
                <label for="filterPriceRange">Price Range</label>
                <select id="filterPriceRange">
                    <option value="">All Prices</option>
                    <option value="0-500">$0 - $500</option>
                    <option value="500-1000">$500 - $1,000</option>
                    <option value="1000-5000">$1,000 - $5,000</option>
                    <option value="5000+">$5,000+</option>
                </select>
            </div>
            <div class="formActions">
                <button class="btn btn-outline" onclick="window.commonAdmin.closeModal()">Cancel</button>
                <button class="btn btn-primary" onclick="window.artworks.applyAdvancedFilters()">Apply Filters</button>
            </div>
        `
    window.commonAdmin.showModal("Filter Artworks", content)
  }

  showAddArtworkModal() {
    const fields = [
      { name: "title", label: "Artwork Title", type: "text" },
      { name: "artist", label: "Artist Name", type: "text" },
      {
        name: "category",
        label: "Category",
        type: "select",
        options: [
          { value: "painting", label: "Painting" },
          { value: "photography", label: "Photography" },
          { value: "sculpture", label: "Sculpture" },
          { value: "digital", label: "Digital Art" },
        ],
      },
      { name: "price", label: "Price ($)", type: "number" },
      { name: "description", label: "Description", type: "textarea" },
    ]

    window.commonAdmin.showModal("Add New Artwork", window.commonAdmin.getEditModalContent(fields))
  }

  showBulkActionsModal() {
    if (this.selectedArtworks.size === 0) {
      window.commonAdmin.showNotification("Please select artworks first", "error")
      return
    }

    const content = `
            <div class="formGroup">
                <label>Bulk Actions for ${this.selectedArtworks.size} selected artworks</label>
                <div style="display: grid; gap: 15px; margin-top: 15px;">
                    <button class="btn btn-primary" onclick="window.artworks.bulkAction('approve')">
                        <i class="fas fa-check"></i> Approve Selected
                    </button>
                    <button class="btn btn-secondary" onclick="window.artworks.bulkAction('reject')">
                        <i class="fas fa-times"></i> Reject Selected
                    </button>
                    <button class="btn btn-outline" onclick="window.artworks.bulkAction('feature')">
                        <i class="fas fa-star"></i> Feature Selected
                    </button>
                </div>
            </div>
            <div class="formActions">
                <button class="btn btn-outline" onclick="window.commonAdmin.closeModal()">Cancel</button>
            </div>
        `
    window.commonAdmin.showModal("Bulk Actions", content)
  }

  reviewArtwork(button) {
    const row = button.closest("tr")
    const artworkTitle = row.querySelector("span").textContent

    const data = {
      Title: artworkTitle,
      Artist: row.cells[2].textContent,
      Category: row.cells[3].textContent,
      Price: row.cells[4].textContent,
      Submitted: row.cells[5].textContent,
      Status: row.cells[6].textContent.trim(),
    }

    window.commonAdmin.showModal("Review Artwork", window.commonAdmin.getViewModalContent(data))
  }

  approveArtwork(button) {
    const row = button.closest("tr")
    const artworkTitle = row.querySelector("span").textContent

    window.commonAdmin.showNotification(`Approving "${artworkTitle}"...`, "success")

    // Update status in the table
    setTimeout(() => {
      const statusBadge = row.querySelector(".statusBadge")
      statusBadge.className = "statusBadge status-open"
      statusBadge.textContent = "Approved"
      window.commonAdmin.showNotification("Artwork approved successfully!", "success")
    }, 1000)
  }

  bulkAction(action) {
    const count = this.selectedArtworks.size
    let message = ""

    switch (action) {
      case "approve":
        message = `Approving ${count} artworks...`
        break
      case "reject":
        message = `Rejecting ${count} artworks...`
        break
      case "feature":
        message = `Featuring ${count} artworks...`
        break
    }

    window.commonAdmin.showNotification(message, "info")

    setTimeout(() => {
      window.commonAdmin.showNotification(`Bulk action completed for ${count} artworks!`, "success")
      this.selectedArtworks.clear()
      document.getElementById("selectAll").checked = false
      document.querySelectorAll(".artwork-checkbox").forEach((cb) => (cb.checked = false))
    }, 2000)

    window.commonAdmin.closeModal()
  }

  searchArtworks() {
    const searchTerm = document.getElementById("artworkSearch").value.trim()
    if (searchTerm) {
      window.commonAdmin.showNotification(`Searching for: ${searchTerm}`, "info")
      // Implement search functionality here
    }
  }

  applyFilters() {
    const category = document.getElementById("categoryFilter").value
    const status = document.getElementById("statusFilter").value

    window.commonAdmin.showNotification("Applying filters...", "info")
    // Implement filter functionality here
  }

  applyAdvancedFilters() {
    const category = document.getElementById("filterCategory").value
    const status = document.getElementById("filterArtworkStatus").value
    const priceRange = document.getElementById("filterPriceRange").value

    window.commonAdmin.showNotification("Advanced filters applied!", "success")
    window.commonAdmin.closeModal()
  }

  loadArtworksData() {
    // Simulate loading artworks data
    const artworks = [
      {
        id: 1,
        title: "Sunset Dreams",
        artist: "Elena Rodriguez",
        category: "Painting",
        price: 1200,
        submitted: "2 hours ago",
        status: "pending",
      },
      {
        id: 2,
        title: "Urban Reflections",
        artist: "Marcus Chen",
        category: "Photography",
        price: 800,
        submitted: "1 day ago",
        status: "approved",
      },
      {
        id: 3,
        title: "Abstract Harmony",
        artist: "Isabella Thompson",
        category: "Digital Art",
        price: 950,
        submitted: "3 days ago",
        status: "featured",
      },
    ]

    this.updateArtworksTable(artworks)
  }

  updateArtworksTable(artworks) {
    const tbody = document.getElementById("artworksTable")
    if (!tbody) return

    tbody.innerHTML = artworks
      .map(
        (artwork) => `
            <tr>
                <td>
                    <input type="checkbox" class="artwork-checkbox" data-id="${artwork.id}">
                </td>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; background: var(--light-gold); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image"></i>
                        </div>
                        <span>${artwork.title}</span>
                    </div>
                </td>
                <td>${artwork.artist}</td>
                <td>${artwork.category}</td>
                <td>$${artwork.price.toLocaleString()}</td>
                <td>${artwork.submitted}</td>
                <td><span class="statusBadge status-${artwork.status === "approved" ? "open" : artwork.status === "featured" ? "resolved" : "pending"}">${artwork.status.charAt(0).toUpperCase() + artwork.status.slice(1)}</span></td>
                <td>
                    <button class="actionBtn btn-view">Review</button>
                    <button class="actionBtn btn-reply">${artwork.status === "pending" ? "Approve" : "Edit"}</button>
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

// Initialize artworks
let artworks
document.addEventListener("DOMContentLoaded", () => {
  artworks = new Artworks()
})

// Declare commonAdmin variable
const commonAdmin = {
  showModal: (title, content) => {
    // Implementation for showModal
  },
  showNotification: (message, type) => {
    // Implementation for showNotification
  },
  closeModal: () => {
    // Implementation for closeModal
  },
  getEditModalContent: (fields) => {
    // Implementation for getEditModalContent
  },
  getViewModalContent: (data) => {
    // Implementation for getViewModalContent
  },
}
