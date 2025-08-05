<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Abstract Harmony - Auction Preview - Yadawity Gallery</title>

    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
      integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="./components/Navbar/navbar.css" />`n    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css" />
    <link rel="stylesheet" href="./public/homePage.css" />
    <link rel="stylesheet" href="./public/auction-preview.css" />

  </head>
  <body>
    <?php include './components/includes/navbar.php'; ?>

    <?php include './components/includes/burger-menu.php'; ?>
        
      </div>
    </div>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
      <div class="breadcrumbContainer">
        <a href="index.php" class="breadcrumbItem">Home</a>
        <i class="fas fa-chevron-right"></i>
        <a href="auction.php" class="breadcrumbItem">Auction House</a>
        <i class="fas fa-chevron-right"></i>
        <span class="breadcrumbItem active">Abstract Harmony</span>
      </div>
    </div>

    <!-- Auction Preview Content -->
    <div class="auctionPreviewContainer">
      <!-- Left: Image Gallery -->
      <div class="auctionGallery">
        <div class="mainImageContainer">
          <img 
            src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=600&h=600&fit=crop" 
            alt="Abstract Harmony - Main View"
            class="mainImage"
            id="mainImage"
          />
          <div class="auctionStatus live">
            <i class="fas fa-circle"></i>
            <span>LIVE AUCTION</span>
          </div>
          <button class="fullscreenBtn" onclick="openImageFullscreen()">
            <i class="fas fa-expand"></i>
          </button>
        </div>
        
        <div class="thumbnailGallery">
          <div class="thumbnail active" onclick="changeMainImage(this)">
            <img 
              src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=100&h=100&fit=crop" 
              alt="View 1"
            />
          </div>
          <div class="thumbnail" onclick="changeMainImage(this)">
            <img 
              src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=100&h=100&fit=crop" 
              alt="View 2"
            />
          </div>
          <div class="thumbnail" onclick="changeMainImage(this)">
            <img 
              src="https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=100&h=100&fit=crop" 
              alt="View 3"
            />
          </div>
          <div class="thumbnail" onclick="changeMainImage(this)">
            <img 
              src="https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=100&h=100&fit=crop" 
              alt="View 4"
            />
          </div>
        </div>
      </div>

      <!-- Right: Auction Info -->
      <div class="auctionInfo">
        <!-- Auction Header -->
        <div class="auctionHeader">
          <div class="auctionMeta">
            <span class="lotNumber">LOT #247</span>
            <span class="auctionDate">Live Auction - Jan 25, 2025</span>
          </div>
          <h1 class="auctionTitle">Abstract Harmony</h1>
          <p class="artistInfo">
            by <a href="artist-profile.html" class="artistLink">Marina Kovač</a>
            <span class="artistDetails">(Serbian, b. 1985)</span>
          </p>
        </div>

        <!-- Auction Timer -->
        <div class="auctionTimer" id="auctionTimer">
          <div class="timerIcon">
            <i class="fas fa-clock"></i>
          </div>
          <div class="timerContent">
            <div class="timerLabel">Auction Ends In</div>
            <div class="timerDisplay" id="timerDisplay">2h 45m 32s</div>
          </div>
        </div>

        <!-- Current Bid -->
        <div class="currentBidSection">
          <div class="bidInfo">
            <div class="currentBid">
              <span class="bidLabel">Current Bid</span>
              <span class="bidAmount" id="currentBidAmount">EGP 75,000</span>
            </div>
            <div class="bidStats">
              <div class="bidStat">
                <i class="fas fa-users"></i>
                <span>12 bidders</span>
              </div>
              <div class="bidStat">
                <i class="fas fa-eye"></i>
                <span>248 watching</span>
              </div>
            </div>
          </div>
          <div class="nextBid">
            <span class="nextBidLabel">Next bid (min)</span>
            <span class="nextBidAmount">EGP 77,500</span>
          </div>
        </div>

        <!-- Bidding Section -->
        <div class="biddingSection">
          <div class="bidInputContainer">
            <div class="bidInput">
              <span class="currencySymbol">EGP</span>
              <input 
                type="number" 
                id="bidAmount" 
                placeholder="77500" 
                min="77500" 
                step="1000"
              />
            </div>
            <button class="placeBidBtn" id="placeBidBtn">
              <i class="fas fa-gavel"></i>
              Place Bid
            </button>
          </div>
          
          <div class="quickBidButtons">
            <button class="quickBidBtn" onclick="setQuickBid(77500)">
              EGP 77,500
            </button>
            <button class="quickBidBtn" onclick="setQuickBid(80000)">
              EGP 80,000
            </button>
            <button class="quickBidBtn" onclick="setQuickBid(85000)">
              EGP 85,000
            </button>
          </div>

          <div class="maxBidOption">
            <label class="maxBidLabel">
              <input type="checkbox" id="maxBidCheckbox">
              <span class="checkmark"></span>
              Set as maximum bid (auto-bid)
            </label>
          </div>
        </div>

        <!-- Secondary Actions -->
        <div class="secondaryActions">
          <button class="watchlistBtn" id="watchlistBtn">
            <i class="far fa-heart"></i>
            Add to Watchlist
          </button>
          <button class="shareBtn" onclick="shareAuction()">
            <i class="fas fa-share-alt"></i>
            Share
          </button>
        </div>

        <!-- Artwork Details -->
        <div class="artworkDetails">
          <h3 class="detailsTitle">Artwork Details</h3>
          <div class="detailsGrid">
            <div class="detailItem">
              <span class="detailLabel">Medium</span>
              <span class="detailValue">Oil on canvas</span>
            </div>
            <div class="detailItem">
              <span class="detailLabel">Dimensions</span>
              <span class="detailValue">80 × 100 cm (31.5 × 39.4 in)</span>
            </div>
            <div class="detailItem">
              <span class="detailLabel">Year</span>
              <span class="detailValue">2024</span>
            </div>
            <div class="detailItem">
              <span class="detailLabel">Signature</span>
              <span class="detailValue">Signed lower right</span>
            </div>
            <div class="detailItem">
              <span class="detailLabel">Provenance</span>
              <span class="detailValue">Artist's studio</span>
            </div>
            <div class="detailItem">
              <span class="detailLabel">Condition</span>
              <span class="detailValue">Excellent</span>
            </div>
          </div>
        </div>

        <!-- Estimate -->
        <div class="estimateSection">
          <h3 class="estimateTitle">Estimate</h3>
          <div class="estimateRange">EGP 60,000 - EGP 90,000</div>
          <p class="estimateNote">
            Estimates include buyer's premium and are subject to revision.
          </p>
        </div>
      </div>
    </div>

    <!-- Artwork Description -->
    <div class="artworkDescription">
      <div class="descriptionContainer">
        <div class="descriptionContent">
          <h2 class="descriptionTitle">About This Artwork</h2>
          <div class="descriptionText">
            <p>
              "Abstract Harmony" represents Marina Kovač's masterful exploration of color, form, and emotion. 
              This stunning oil painting captures the delicate balance between chaos and order through bold 
              brushstrokes and a sophisticated palette that evolves from warm earth tones to vibrant blues and golds.
            </p>
            <p>
              Created in 2024, this work exemplifies Kovač's mature style, where abstract expressionism meets 
              controlled composition. The piece invites viewers into a meditative journey, with each layer of 
              paint revealing new depths and interpretations. The artist's signature technique of layered 
              glazing creates an almost luminous quality that changes with the viewing angle and lighting conditions.
            </p>
            <p>
              Kovač, a rising star in the contemporary art scene, has exhibited in galleries across Europe and 
              North America. Her works are held in several private collections and have garnered critical acclaim 
              for their emotional depth and technical excellence.
            </p>
          </div>
        </div>
        
        <div class="artistBio">
          <div class="artistImage">
            <img 
              src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face" 
              alt="Marina Kovač"
            />
          </div>
          <div class="artistInfo">
            <h3 class="artistName">Marina Kovač</h3>
            <p class="artistBioText">
              Serbian contemporary artist known for her abstract expressionist works. 
              Graduated from the Academy of Fine Arts in Belgrade in 2008. Her work 
              explores themes of human emotion and the relationship between color and feeling.
            </p>
            <a href="artist-profile.html" class="viewProfileBtn">
              View Artist Profile
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Bidding History -->
    <div class="biddingHistory">
      <div class="historyContainer">
        <h2 class="historyTitle">Bidding History</h2>
        <div class="historyTable">
          <div class="historyHeader">
            <div class="historyCol">Bidder</div>
            <div class="historyCol">Amount</div>
            <div class="historyCol">Time</div>
          </div>
          <div class="historyBody" id="biddingHistoryBody">
            <div class="historyRow current">
              <div class="historyCol bidder">user_1847</div>
              <div class="historyCol amount">EGP 75,000</div>
              <div class="historyCol time">2 minutes ago</div>
            </div>
            <div class="historyRow">
              <div class="historyCol bidder">art_collector_92</div>
              <div class="historyCol amount">EGP 72,500</div>
              <div class="historyCol time">8 minutes ago</div>
            </div>
            <div class="historyRow">
              <div class="historyCol bidder">marina_fan</div>
              <div class="historyCol amount">EGP 70,000</div>
              <div class="historyCol time">15 minutes ago</div>
            </div>
            <div class="historyRow">
              <div class="historyCol bidder">gallery_owner</div>
              <div class="historyCol amount">EGP 67,500</div>
              <div class="historyCol time">23 minutes ago</div>
            </div>
            <div class="historyRow">
              <div class="historyCol bidder">user_5639</div>
              <div class="historyCol amount">EGP 65,000</div>
              <div class="historyCol time">31 minutes ago</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Similar Auctions -->
    <div class="similarAuctions">
      <div class="similarContainer">
        <h2 class="similarTitle">Similar Auctions</h2>
        <div class="similarGrid">
          <!-- Similar auction items -->
          <div class="similarCard">
            <div class="similarImage">
              <img 
                src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&h=200&fit=crop" 
                alt="Urban Reflections"
              />
              <div class="similarStatus live">LIVE</div>
            </div>
            <div class="similarInfo">
              <h4 class="similarArtworkTitle">Urban Reflections</h4>
              <p class="similarArtist">by Sarah Chen</p>
              <div class="similarBid">Current: EGP 35,000</div>
              <button class="similarViewBtn" onclick="window.location.href='auction-preview.html?id=3'">
                View Auction
              </button>
            </div>
          </div>

          <div class="similarCard">
            <div class="similarImage">
              <img 
                src="https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=300&h=200&fit=crop" 
                alt="Classical Portrait"
              />
              <div class="similarStatus ended">SOLD</div>
            </div>
            <div class="similarInfo">
              <h4 class="similarArtworkTitle">Classical Portrait</h4>
              <p class="similarArtist">by Elena Popović</p>
              <div class="similarBid">Final: EGP 180,000</div>
              <button class="similarViewBtn" onclick="window.location.href='auction-preview.html?id=4'">
                View Details
              </button>
            </div>
          </div>

          <div class="similarCard">
            <div class="similarImage">
              <img 
                src="https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=300&h=200&fit=crop" 
                alt="Sunset Serenity"
              />
              <div class="similarStatus upcoming">UPCOMING</div>
            </div>
            <div class="similarInfo">
              <h4 class="similarArtworkTitle">Sunset Serenity</h4>
              <p class="similarArtist">by Omar Farouk</p>
              <div class="similarBid">Starting: EGP 45,000</div>
              <button class="similarViewBtn" onclick="window.location.href='auction-preview.html?id=6'">
                Pre-Register
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php include './components/includes/footer.php'; ?>

    <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./components/Navbar/navbar.js"></script>
    <script src="./public/auction-preview.js"></script>
  </body>
</html>
