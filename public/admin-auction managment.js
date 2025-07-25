// Auctions specific JavaScript

class Auctions {
  constructor(commonAdmin) {
    this.countdownIntervals = new Map()
    this.commonAdmin = commonAdmin
    this.init()
  }

  init() {
    this.setupEventListeners()
    this.loadAuctionsData()
    this.startCountdowns()
    this.setupAutoRefresh()
  }

  setupEventListeners() {
    // Refresh button
    const refreshBtn = document.getElementById("refreshBtn")
    if (refreshBtn) {
      refreshBtn.addEventListener("click", () => this.refreshAuctions())
    }

    // Create auction button
    const createAuctionBtn = document.getElementById("createAuctionBtn")
    if (createAuctionBtn) {
      createAuctionBtn.addEventListener("click", () => this.showCreateAuctionModal())
    }

    // Filter auctions button
    const filterAuctionsBtn = document.getElementById("filterAuctionsBtn")
    if (filterAuctionsBtn) {
      filterAuctionsBtn.addEventListener("click", () => this.showFilterModal())
    }

    // Action buttons
    this.setupActionButtons()
  }

  setupActionButtons() {
    const monitorButtons = document.querySelectorAll(".btn-view")
    const manageButtons = document.querySelectorAll(".btn-reply")

    monitorButtons.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        this.monitorAuction(btn)
      })
    })

    manageButtons.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        this.manageAuction(btn)
      })
    })
  }

  showCreateAuctionModal() {
    const fields = [
      { name: "artwork", label: "Artwork", type: "text" },
      { name: "startingBid", label: "Starting Bid ($)", type: "number" },
      { name: "reservePrice", label: "Reserve Price ($)", type: "number" },
      {
        name: "duration",
        label: "Duration",
        type: "select",
        options: [
          { value: "1", label: "1 Day" },
          { value: "3", label: "3 Days" },
          { value: "7", label: "7 Days" },
          { value: "14", label: "14 Days" },
        ],
      },
      { name: "description", label: "Description", type: "textarea" },
    ]

    this.commonAdmin.showModal("Create New Auction", this.commonAdmin.getEditModalContent(fields))
  }

  showFilterModal() {
    const content = `
            <div class="formGroup">
                <label for="filterAuctionStatus">Status</label>
                <select id="filterAuctionStatus">
                    <option value="">All Auctions</option>
                    <option value="active">Active</option>
                    <option value="ending">Ending Soon</option>
                    <option value="ended">Ended</option>
                </select>
            </div>
            <div class="formGroup">
                <label for="filterBidRange">Bid Range</label>
                <select id="filterBidRange">
                    <option value="">All Bids</option>
                    <option value="0-1000">$0 - $1,000</option>
                    <option value="1000-5000">$1,000 - $5,000</option>
                    <option value="5000+">$5,000+</option>
                </select>
            </div>
            <div class="formActions">
                <button class="btn btn-outline" onclick="this.commonAdmin.closeModal()">Cancel</button>
                <button class="btn btn-primary" onclick="this.applyFilters()">Apply Filters</button>
            </div>
        `
    this.commonAdmin.showModal("Filter Auctions", content)
  }

  monitorAuction(button) {
    const row = button.closest("tr")
    const artworkName = row.querySelector("span").textContent

    const data = {
      Artwork: artworkName,
      "Current Bid": row.cells[1].textContent,
      "Total Bidders": row.cells[2].textContent,
      "Time Remaining": row.cells[3].textContent,
      Status: row.cells[4].textContent.trim(),
    }

    this.commonAdmin.showModal("Auction Monitor", this.commonAdmin.getViewModalContent(data))
  }

  manageAuction(button) {
    const row = button.closest("tr")
    const artworkName = row.querySelector("span").textContent

    const fields = [
      { name: "artwork", label: "Artwork", value: artworkName, type: "text" },
      { name: "reservePrice", label: "Reserve Price ($)", value: "2000", type: "number" },
      {
        name: "status",
        label: "Status",
        value: "active",
        type: "select",
        options: [
          { value: "active", label: "Active" },
          { value: "paused", label: "Paused" },
          { value: "ended", label: "End Early" },
        ],
      },
    ]

    this.commonAdmin.showModal("Manage Auction", this.commonAdmin.getEditModalContent(fields))
  }

  refreshAuctions() {
    this.commonAdmin.showNotification("Refreshing auction data...", "info")
    this.loadAuctionsData()
    setTimeout(() => {
      this.commonAdmin.showNotification("Auction data refreshed!", "success")
    }, 1000)
  }

  applyFilters() {
    const status = document.getElementById("filterAuctionStatus").value
    const bidRange = document.getElementById("filterBidRange").value

    this.commonAdmin.showNotification("Filters applied!", "success")
    this.commonAdmin.closeModal()
  }

  startCountdowns() {
    const countdownElements = document.querySelectorAll(".countdown")

    countdownElements.forEach((element) => {
      const endTime = element.dataset.end
      if (endTime) {
        this.startCountdown(element, new Date(endTime))
      }
    })
  }

  startCountdown(element, endTime) {
    const interval = setInterval(() => {
      const now = new Date().getTime()
      const distance = endTime.getTime() - now

      if (distance < 0) {
        clearInterval(interval)
        element.textContent = "Ended"
        element.style.color = "var(--danger-red)"
        return
      }

      const hours = Math.floor(distance / (1000 * 60 * 60))
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))

      element.textContent = `${hours}h ${minutes}m`

      // Change color if ending soon (less than 1 hour)
      if (hours < 1) {
        element.style.color = "var(--danger-red)"
        element.style.fontWeight = "bold"
      }
    }, 60000) // Update every minute

    this.countdownIntervals.set(element, interval)
  }

  setupAutoRefresh() {
    // Auto-refresh auction data every 2 minutes
    setInterval(() => {
      this.loadAuctionsData()
    }, 120000)
  }

  loadAuctionsData() {
    // Simulate loading auctions data
    const auctions = [
      {
        artwork: "Modern Abstract #3",
        currentBid: 2400,
        bidders: 12,
        timeLeft: "2h 34m",
        status: "active",
        endTime: new Date(Date.now() + 2.5 * 60 * 60 * 1000), // 2.5 hours from now
      },
      {
        artwork: "Vintage Portrait",
        currentBid: 1800,
        bidders: 8,
        timeLeft: "45m",
        status: "ending",
        endTime: new Date(Date.now() + 45 * 60 * 1000), // 45 minutes from now
      },
    ]

    this.updateAuctionsTable(auctions)
  }

  updateAuctionsTable(auctions) {
    const tbody = document.getElementById("auctionsTable")
    if (!tbody) return

    // Clear existing countdown intervals
    this.countdownIntervals.forEach((interval) => clearInterval(interval))
    this.countdownIntervals.clear()

    tbody.innerHTML = auctions
      .map(
        (auction) => `
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; background: var(--light-gold); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image"></i>
                        </div>
                        <span>${auction.artwork}</span>
                    </div>
                </td>
                <td>$${auction.currentBid.toLocaleString()}</td>
                <td>${auction.bidders}</td>
                <td><span class="countdown" data-end="${auction.endTime.toISOString()}">${auction.timeLeft}</span></td>
                <td><span class="statusBadge status-${auction.status === "active" ? "open" : "pending"}">${auction.status === "ending" ? "Ending Soon" : "Active"}</span></td>
                <td>
                    <button class="actionBtn btn-view">Monitor</button>
                    <button class="actionBtn btn-reply">Manage</button>
                </td>
            </tr>
        `,
      )
      .join("")

    // Re-setup action buttons and countdowns for new content
    this.setupActionButtons()
    this.startCountdowns()
  }
}

// Initialize auctions
let auctions
document.addEventListener("DOMContentLoaded", () => {
  const commonAdmin = window.commonAdmin // Assuming commonAdmin is available in the global scope
  auctions = new Auctions(commonAdmin)
})