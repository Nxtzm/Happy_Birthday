<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>QuickServe | Local Commerce</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <style>
        :root {
            --md-sys-color-primary: #6750A4;
            --md-sys-color-on-primary: #FFFFFF;
            --md-sys-color-primary-container: #EADDFF;
            --md-sys-color-on-primary-container: #21005D;
            --md-sys-color-secondary: #625B71;
            --md-sys-color-on-secondary: #FFFFFF;
            --md-sys-color-surface: #FEF7FF;
            --md-sys-color-on-surface: #1D1B20;
            --md-sys-color-surface-variant: #E7E0EC;
            --md-sys-color-on-surface-variant: #49454F;
            --md-sys-color-outline: #79747E;
            --md-sys-color-error: #B3261E;
            --radius-large: 28px;
            --radius-medium: 16px;
            --radius-small: 12px;
            --shadow-soft: 0 4px 20px rgba(0,0,0,0.05);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --md-sys-color-primary: #D0BCFF;
                --md-sys-color-on-primary: #381E72;
                --md-sys-color-primary-container: #4F378B;
                --md-sys-color-on-primary-container: #EADDFF;
                --md-sys-color-surface: #1C1B1F;
                --md-sys-color-on-surface: #E6E1E5;
                --md-sys-color-surface-variant: #49454F;
                --md-sys-color-on-surface-variant: #CAC4D0;
            }
        }

        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--md-sys-color-surface); color: var(--md-sys-color-on-surface); margin: 0; padding: 0; transition: background 0.3s; }
        #app { max-width: 480px; margin: 0 auto; min-height: 100vh; background: var(--md-sys-color-surface); position: relative; display: flex; flex-direction: column; box-shadow: 0 0 50px rgba(0,0,0,0.05); }

        /* Navigation */
        .nav-bar { height: 72px; background: var(--md-sys-color-surface); display: flex; justify-content: space-around; align-items: center; position: sticky; bottom: 0; border-top: 1px solid var(--md-sys-color-surface-variant); z-index: 100; }
        .nav-item { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; font-size: 11px; font-weight: 500; color: var(--md-sys-color-on-surface-variant); cursor: pointer; transition: 0.2s; }
        .nav-item .material-icons-outlined { font-size: 24px; padding: 4px 16px; border-radius: 16px; transition: 0.2s; }
        .nav-item.active { color: var(--md-sys-color-primary); }
        .nav-item.active .material-icons-outlined { background: var(--md-sys-color-primary-container); color: var(--md-sys-color-on-primary-container); }

        /* Views */
        .view { display: none; flex: 1; overflow-y: auto; padding-bottom: 20px; animation: fadeIn 0.4s ease-out; }
        .view.active { display: flex; flex-direction: column; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* Splash */
        #view-splash { background: var(--md-sys-color-primary); color: var(--md-sys-color-on-primary); align-items: center; justify-content: center; z-index: 1000; position: fixed; inset: 0; }
        .logo-large { font-size: 42px; font-weight: 800; letter-spacing: -1px; }

        /* Header */
        header { padding: 16px 20px; display: flex; align-items: center; gap: 12px; position: sticky; top: 0; background: var(--md-sys-color-surface); z-index: 90; }
        .location-box { flex: 1; }
        .location-box .label { font-size: 11px; color: var(--md-sys-color-primary); font-weight: 700; text-transform: uppercase; }
        .location-box .value { font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 2px; }

        /* Search */
        .search-bar { background: var(--md-sys-color-surface-variant); border-radius: var(--radius-large); padding: 14px 20px; display: flex; align-items: center; gap: 12px; margin: 0 20px 20px; cursor: pointer; }
        .search-bar input { border: none; background: transparent; flex: 1; font-size: 15px; font-family: inherit; font-weight: 500; outline: none; }

        /* Categories */
        .category-scroll { display: flex; gap: 12px; overflow-x: auto; padding: 0 20px 20px; scrollbar-width: none; }
        .category-scroll::-webkit-scrollbar { display: none; }
        .category-pill { background: var(--md-sys-color-surface-variant); border-radius: var(--radius-medium); padding: 12px 20px; display: flex; flex-direction: column; align-items: center; gap: 8px; min-width: 90px; cursor: pointer; transition: 0.2s; }
        .category-pill:hover { background: var(--md-sys-color-primary-container); }
        .category-pill .material-icons-outlined { font-size: 28px; }
        .category-pill span { font-size: 12px; font-weight: 600; }

        /* Cards */
        .section-header { padding: 0 20px 12px; display: flex; justify-content: space-between; align-items: center; }
        .section-header h2 { margin: 0; font-size: 18px; font-weight: 700; }
        .section-header .see-all { font-size: 13px; color: var(--md-sys-color-primary); font-weight: 600; }

        .store-card { margin: 0 20px 20px; border-radius: var(--radius-medium); overflow: hidden; background: var(--md-sys-color-surface); box-shadow: var(--shadow-soft); border: 1px solid var(--md-sys-color-surface-variant); cursor: pointer; }
        .store-image { height: 180px; background: #eee; background-size: cover; background-position: center; position: relative; }
        .store-info { padding: 16px; }
        .store-name { font-weight: 700; font-size: 17px; margin-bottom: 4px; }
        .store-meta { display: flex; align-items: center; gap: 12px; font-size: 13px; color: var(--md-sys-color-on-surface-variant); }
        .rating { display: flex; align-items: center; gap: 4px; color: #E65100; font-weight: 700; }

        /* Buttons */
        .btn { border: none; border-radius: var(--radius-large); padding: 14px 24px; font-family: inherit; font-weight: 700; font-size: 15px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-primary { background: var(--md-sys-color-primary); color: var(--md-sys-color-on-primary); width: 100%; }
        .btn-primary:active { transform: scale(0.98); opacity: 0.9; }

        /* Product Item */
        .product-item { padding: 16px 20px; display: flex; gap: 16px; border-bottom: 1px solid var(--md-sys-color-surface-variant); align-items: center; }
        .product-info { flex: 1; }
        .product-name { font-weight: 600; font-size: 15px; margin-bottom: 4px; }
        .product-price { font-weight: 700; color: var(--md-sys-color-primary); }
        .product-image { width: 90px; height: 90px; background: #eee; border-radius: var(--radius-small); background-size: cover; }
        .btn-add-pill { border: 1px solid var(--md-sys-color-primary); color: var(--md-sys-color-primary); background: transparent; padding: 6px 20px; border-radius: var(--radius-large); font-weight: 700; font-size: 12px; cursor: pointer; }

        /* Cart Footer */
        .cart-footer { position: fixed; bottom: 72px; width: 100%; max-width: 480px; padding: 16px 20px; background: var(--md-sys-color-surface); border-top: 1px solid var(--md-sys-color-surface-variant); z-index: 90; }

        /* Tracking Map placeholder */
        .map-container { height: 240px; background: #f0f0f0; margin: 16px 20px; border-radius: var(--radius-medium); overflow: hidden; position: relative; }
        .rider-marker { position: absolute; top: 40%; left: 30%; color: var(--md-sys-color-primary); animation: moveRider 10s infinite alternate linear; }
        @keyframes moveRider { from { transform: translate(0,0); } to { transform: translate(100px, 40px); } }

        /* Utils */
        .empty-state { padding: 60px 40px; text-align: center; color: var(--md-sys-color-on-surface-variant); }
        .empty-state .material-icons-outlined { font-size: 64px; margin-bottom: 16px; opacity: 0.3; }

        .onboarding { padding: 40px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; }
        .onboarding-img { width: 280px; height: 280px; background: var(--md-sys-color-primary-container); border-radius: 50%; margin-bottom: 40px; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>
    <div id="app">
        <!-- View Splash -->
        <div id="view-splash" class="view active">
            <div class="logo-large">QuickServe</div>
            <div style="margin-top:20px; font-weight:500; opacity:0.8">Local Commerce Platform</div>
        </div>

        <!-- View Onboarding -->
        <div id="view-onboarding" class="view">
            <div class="onboarding">
                <div class="onboarding-img">
                    <span class="material-icons-outlined" style="font-size:120px; color:var(--md-sys-color-primary)">local_shipping</span>
                </div>
                <h1 style="font-weight:800; font-size:28px; margin:0 0 16px">Fastest Delivery</h1>
                <p style="color:var(--md-sys-color-on-surface-variant); line-height:1.6; margin-bottom:40px">Get food, groceries, and more delivered from your favorite local stores in minutes.</p>
                <button class="btn btn-primary" onclick="showView('login')">Get Started</button>
            </div>
        </div>

        <!-- View Login -->
        <div id="view-login" class="view">
            <div style="padding:40px 20px">
                <h2 style="font-weight:800; font-size:28px">Welcome back</h2>
                <p style="color:var(--md-sys-color-on-surface-variant); margin-bottom:32px">Enter your phone number to continue</p>
                <div class="search-bar" style="margin:0 0 16px; background:var(--md-sys-color-surface); border:1px solid var(--md-sys-color-outline)">
                    <span>+977</span>
                    <input type="tel" id="login-phone" placeholder="98XXXXXXXX" style="width:100%">
                </div>
                <div class="search-bar" style="margin:0 0 32px; background:var(--md-sys-color-surface); border:1px solid var(--md-sys-color-outline)">
                    <span class="material-icons-outlined">lock</span>
                    <input type="password" id="login-pass" placeholder="Password" style="width:100%">
                </div>
                <button class="btn btn-primary" onclick="doLogin()">Login</button>
                <p style="text-align:center; font-size:14px; margin-top:24px">Don't have an account? <a href="#" style="color:var(--md-sys-color-primary); font-weight:700">Register</a></p>
            </div>
        </div>

        <!-- View Home -->
        <div id="view-home" class="view">
            <header>
                <div class="location-box">
                    <div class="label">Deliver to</div>
                    <div class="value">Birtamode, Jhapa <span class="material-icons-outlined" style="font-size:16px">expand_more</span></div>
                </div>
                <div style="width:40px; height:40px; border-radius:50%; background:var(--md-sys-color-primary-container); display:flex; align-items:center; justify-content:center; color:var(--md-sys-color-primary)">
                    <span class="material-icons-outlined">person</span>
                </div>
            </header>
            <div class="search-bar" onclick="showView('search')">
                <span class="material-icons-outlined">search</span>
                <input type="text" placeholder="Search food, grocery, meat..." readonly>
            </div>

            <div class="category-scroll">
                <div class="category-pill"><span class="material-icons-outlined">restaurant</span><span>Food</span></div>
                <div class="category-pill"><span class="material-icons-outlined">shopping_basket</span><span>Grocery</span></div>
                <div class="category-pill"><span class="material-icons-outlined">medical_services</span><span>Pharmacy</span></div>
                <div class="category-pill"><span class="material-icons-outlined">kebab_dining</span><span>Meat</span></div>
                <div class="category-pill"><span class="material-icons-outlined">edit</span><span>Stationery</span></div>
            </div>

            <div class="section-header">
                <h2>Popular Stores</h2>
                <span class="see-all">See All</span>
            </div>
            <div id="home-stores-list"></div>
        </div>

        <!-- View Store -->
        <div id="view-store" class="view">
            <div id="store-hero" style="height:240px; background:#eee; position:relative">
                <button onclick="showView('home')" style="position:absolute; top:20px; left:20px; background:rgba(0,0,0,0.5); border:none; color:#fff; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center"><span class="material-icons-outlined">arrow_back</span></button>
            </div>
            <div style="padding:20px; margin-top:-30px; background:var(--md-sys-color-surface); border-radius:30px 30px 0 0">
                <h1 id="store-title" style="margin:0 0 8px; font-weight:800; font-size:24px"></h1>
                <div class="store-meta">
                    <span class="rating"><span class="material-icons-outlined" style="font-size:16px">star</span> <span id="store-rating">4.5</span></span>
                    <span>•</span>
                    <span id="store-time">25-35 min</span>
                    <span>•</span>
                    <span>Free Delivery</span>
                </div>
                <p id="store-desc" style="color:var(--md-sys-color-on-surface-variant); font-size:14px; line-height:1.6"></p>
            </div>
            <div id="store-products-list"></div>
        </div>

        <!-- View Search -->
        <div id="view-search" class="view">
            <header>
                <button onclick="showView('home')" style="background:none; border:none; padding:0"><span class="material-icons-outlined">arrow_back</span></button>
                <div class="search-bar" style="margin:0; flex:1; background:var(--md-sys-color-surface-variant)">
                    <input type="text" id="search-input" placeholder="Search for anything..." oninput="doSearch()" autofocus>
                </div>
            </header>
            <div id="search-results-list"></div>
        </div>

        <!-- View Tracking -->
        <div id="view-tracking" class="view">
            <header>
                <button onclick="showView('home')" style="background:none; border:none; padding:0"><span class="material-icons-outlined">arrow_back</span></button>
                <div style="flex:1; font-weight:700; text-align:center">Track Order #<span id="track-id"></span></div>
            </header>
            <div class="map-container">
                <div class="rider-marker"><span class="material-icons-outlined" style="font-size:48px">directions_bike</span></div>
            </div>
            <div style="padding:0 20px">
                <div style="background:var(--md-sys-color-primary-container); padding:20px; border-radius:var(--radius-medium); margin-bottom:24px; text-align:center">
                    <div style="font-size:11px; font-weight:700; color:var(--md-sys-color-on-primary-container); letter-spacing:1px">ESTIMATED ARRIVAL</div>
                    <div style="font-size:32px; font-weight:800; color:var(--md-sys-color-on-primary-container)">12 mins</div>
                </div>
                <div style="display:flex; gap:12px; margin-bottom:24px">
                    <button class="btn" style="flex:1; background:var(--md-sys-color-surface-variant); border-radius:12px"><span class="material-icons-outlined">chat</span> Chat</button>
                    <button class="btn" style="flex:1; background:var(--md-sys-color-surface-variant); border-radius:12px"><span class="material-icons-outlined">call</span> Call</button>
                </div>
                <div id="track-steps" style="display:flex; flex-direction:column; gap:20px"></div>
            </div>
        </div>

        <!-- View Merchant -->
        <div id="view-merchant" class="view">
            <header>
                <div style="flex:1"><h2 style="margin:0; font-size:20px; font-weight:800">Merchant Dashboard</h2></div>
                <div style="background:var(--md-sys-color-primary-container); padding:6px 12px; border-radius:16px; font-size:12px; font-weight:700">STORE OPEN</div>
            </header>
            <div id="merchant-stats" style="display:grid; grid-template-columns:1fr 1fr; gap:16px; padding:20px"></div>
            <div class="section-header"><h2>Active Orders</h2></div>
            <div id="merchant-orders-list"></div>
        </div>

        <!-- View Rider -->
        <div id="view-rider" class="view">
            <header>
                <div style="flex:1"><h2 style="margin:0; font-size:20px; font-weight:800">Rider Dashboard</h2></div>
                <div style="background:var(--md-sys-color-primary-container); padding:6px 12px; border-radius:16px; font-size:12px; font-weight:700">ONLINE</div>
            </header>
            <div id="rider-stats" style="display:grid; grid-template-columns:1fr 1fr; gap:16px; padding:20px"></div>
            <div class="section-header"><h2>Available for Pickup</h2></div>
            <div id="rider-orders-list"></div>
        </div>

        <!-- View Cart -->
        <div id="view-cart" class="view">
            <header><div style="flex:1"><h2 style="margin:0; font-weight:800">Your Cart</h2></div></header>
            <div id="cart-items-list" style="flex:1"></div>
            <div class="cart-footer">
                <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-weight:600">
                    <span>Total</span>
                    <span id="cart-total-display">Rs. 0</span>
                </div>
                <button class="btn btn-primary" onclick="checkout()">Checkout</button>
            </div>
        </div>

        <!-- View Admin -->
        <div id="view-admin" class="view">
            <header><div style="flex:1"><h2 style="margin:0; font-size:20px; font-weight:800">Admin Control</h2></div></header>
            <div id="admin-stats" style="display:grid; grid-template-columns:1fr 1fr; gap:16px; padding:20px"></div>
            <div class="section-header"><h2>System Orders</h2></div>
            <div id="admin-orders-list"></div>
        </div>

        <!-- Navigation (Sticky) -->
        <nav class="nav-bar" id="bottom-nav" style="display:none">
            <div class="nav-item active" onclick="showView('home')"><span class="material-icons-outlined">home</span><span>Home</span></div>
            <div class="nav-item" onclick="showView('search')"><span class="material-icons-outlined">search</span><span>Search</span></div>
            <div class="nav-item" onclick="showView('cart')"><span class="material-icons-outlined">shopping_cart</span><span>Cart</span></div>
            <div class="nav-item" id="nav-merchant" style="display:none" onclick="showView('merchant')"><span class="material-icons-outlined">store</span><span>Merchant</span></div>
            <div class="nav-item" id="nav-rider" style="display:none" onclick="showView('rider')"><span class="material-icons-outlined">directions_bike</span><span>Rider</span></div>
            <div class="nav-item" id="nav-admin" style="display:none" onclick="showView('admin')"><span class="material-icons-outlined">admin_panel_settings</span><span>Admin</span></div>
        </nav>
    </div>

    <script>
        let currentUser = <?php echo json_encode($user); ?>;
        let cart = { store_id: null, items: [] };

        async function init() {
            if (!currentUser) {
                setTimeout(() => showView('onboarding'), 2000);
            } else {
                setTimeout(() => {
                    document.getElementById('bottom-nav').style.display = 'flex';
                    updateNav();
                    showView('home');
                }, 1500);
            }
        }

        function showView(id) {
            document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
            const target = document.getElementById('view-' + id);
            if (target) target.classList.add('active');

            // Update active nav
            document.querySelectorAll('.nav-item').forEach(n => {
                n.classList.toggle('active', n.getAttribute('onclick')?.includes(`'${id}'`));
            });

            if (id === 'home') loadHome();
            if (id === 'merchant') loadMerchant();
            if (id === 'rider') loadRider();
            if (id === 'admin') loadAdmin();
            if (id === 'cart') renderCart();
        }

        function updateNav() {
            if (!currentUser) return;
            if (currentUser.role === 'admin' || currentUser.role === 'merchant') document.getElementById('nav-merchant').style.display = 'flex';
            if (currentUser.role === 'admin' || currentUser.role === 'rider') document.getElementById('nav-rider').style.display = 'flex';
            if (currentUser.role === 'admin') document.getElementById('nav-admin').style.display = 'flex';
        }

        async function loadMerchant() {
            const res = await fetch('?api=merchant&action=get_dashboard').then(r => r.json());
            const { stats, orders } = res.data;
            document.getElementById('merchant-stats').innerHTML = `
                <div style="background:var(--md-sys-color-primary-container); padding:20px; border-radius:16px">
                    <div style="font-size:12px; font-weight:700">TODAY</div><div style="font-size:20px; font-weight:800">Rs. ${stats.today_earnings}</div>
                </div>
                <div style="background:var(--md-sys-color-surface-variant); padding:20px; border-radius:16px">
                    <div style="font-size:12px; font-weight:700">PENDING</div><div style="font-size:20px; font-weight:800">${stats.pending_count}</div>
                </div>
            `;
            document.getElementById('merchant-orders-list').innerHTML = orders.map(o => `
                <div class="product-item">
                    <div class="product-info"><div class="product-name">Order #${o.id}</div><div style="font-size:12px">${o.status}</div></div>
                    <button class="btn-add-pill" onclick="updateStatus(${o.id}, 'ready')">Mark Ready</button>
                </div>
            `).join('') || '<div class="empty-state">No orders.</div>';
        }

        async function updateStatus(id, s) {
            await fetch('?api=merchant&action=update_status', { method: 'POST', body: JSON.stringify({order_id: id, status: s}) });
            loadMerchant();
        }

        async function loadRider() {
            const res = await fetch('?api=rider&action=get_available').then(r => r.json());
            document.getElementById('rider-orders-list').innerHTML = res.data.map(o => `
                <div class="product-item">
                    <div class="product-info"><div class="product-name">${o.store_name}</div><div style="font-size:12px">${o.delivery_address}</div></div>
                    <button class="btn-add-pill" onclick="acceptDelivery(${o.id})">Accept</button>
                </div>
            `).join('') || '<div class="empty-state">No deliveries.</div>';
        }

        async function acceptDelivery(id) {
            await fetch('?api=rider&action=accept', { method: 'POST', body: JSON.stringify({order_id: id}) });
            const otp = prompt("Customer OTP:");
            if(otp) await fetch('?api=rider&action=complete', { method: 'POST', body: JSON.stringify({order_id: id, otp}) });
            loadRider();
        }

        async function loadAdmin() {
            const [statsRes, ordersRes, merchantsRes, ridersRes] = await Promise.all([
                fetch('?api=admin&action=get_stats').then(r => r.json()),
                fetch('?api=admin&action=get_all_orders').then(r => r.json()),
                fetch('?api=admin&action=get_merchants').then(r => r.json()),
                fetch('?api=admin&action=get_riders').then(r => r.json())
            ]);

            const s = statsRes.data;
            document.getElementById('admin-stats').innerHTML = `
                <div style="background:#f5f5f5; padding:20px; border-radius:16px; border:1px solid #ddd">
                    <div style="font-size:11px; font-weight:700; color:#666">TOTAL REVENUE</div><div style="font-size:22px; font-weight:800">Rs. ${s.total_sales}</div>
                </div>
                <div style="background:#f5f5f5; padding:20px; border-radius:16px; border:1px solid #ddd">
                    <div style="font-size:11px; font-weight:700; color:#666">TOTAL ORDERS</div><div style="font-size:22px; font-weight:800">${s.total_orders}</div>
                </div>
            `;

            let html = '<div style="padding:0 20px">';
            html += '<div style="margin-top:20px; font-weight:700; font-size:14px; color:var(--md-sys-color-primary)">ACTIVE MERCHANTS</div>';
            html += merchantsRes.data.map(m => `<div class="product-item" style="padding-left:0; padding-right:0"><div class="product-info"><div class="product-name">${m.name}</div><div style="font-size:12px">${m.address}</div></div></div>`).join('');

            html += '<div style="margin-top:20px; font-weight:700; font-size:14px; color:var(--md-sys-color-primary)">ACTIVE RIDERS</div>';
            html += ridersRes.data.map(r => `<div class="product-item" style="padding-left:0; padding-right:0"><div class="product-info"><div class="product-name">${r.name}</div><div style="font-size:12px">Wallet: Rs. ${r.wallet_balance}</div></div></div>`).join('');

            html += '<div style="margin-top:20px; font-weight:700; font-size:14px; color:var(--md-sys-color-primary)">RECENT ORDERS</div>';
            html += ordersRes.data.map(o => `
                <div class="product-item" style="padding-left:0; padding-right:0">
                    <div class="product-info"><div class="product-name">${o.store_name}</div><div style="font-size:12px">Customer: ${o.customer_name}</div></div>
                    <div style="font-size:11px; font-weight:700; background:var(--md-sys-color-primary-container); padding:4px 8px; border-radius:4px">${o.status.toUpperCase()}</div>
                </div>
            `).join('');
            html += '</div>';

            document.getElementById('admin-orders-list').innerHTML = html;
        }

        async function loadSearch() {
            const list = document.getElementById('search-results-list');
            list.innerHTML = '<div class="empty-state">Start typing to search...</div>';
        }

        async function doSearch() {
            const q = document.getElementById('search-input').value;
            if (q.length < 2) return;
            const res = await fetch('?api=customer&action=search&q=' + q).then(r => r.json());
            const list = document.getElementById('search-results-list');
            list.innerHTML = '';

            if (res.data.stores.length > 0) {
                list.innerHTML += '<div class="section-header"><h2>Stores</h2></div>';
                list.innerHTML += res.data.stores.map(s => `<div class="product-item" onclick="loadStore(${s.id})">${s.name}</div>`).join('');
            }
            if (res.data.products.length > 0) {
                list.innerHTML += '<div class="section-header"><h2>Products</h2></div>';
                list.innerHTML += res.data.products.map(p => `<div class="product-item" onclick="loadStore(${p.store_id})">${p.name} - Rs. ${p.price}</div>`).join('');
            }
            if (list.innerHTML === '') list.innerHTML = '<div class="empty-state">No results found.</div>';
        }

        function renderCart() {
            const list = document.getElementById('cart-items-list');
            if (cart.items.length === 0) {
                list.innerHTML = '<div class="empty-state">Cart is empty.</div>';
                document.querySelector('#view-cart .cart-footer').style.display = 'none';
                return;
            }
            document.querySelector('#view-cart .cart-footer').style.display = 'block';
            list.innerHTML = cart.items.map(i => `
                <div class="product-item">
                    <div class="product-info">
                        <div class="product-name">${i.name}</div>
                        <div class="product-price">Rs. ${i.price} x ${i.quantity}</div>
                    </div>
                </div>
            `).join('');
            const total = cart.items.reduce((s, i) => s + (i.price * i.quantity), 0) + 50;
            document.getElementById('cart-total-display').textContent = 'Rs. ' + total;
        }

        async function checkout() {
            const subtotal = cart.items.reduce((s, i) => s + (i.price * i.quantity), 0);
            const res = await fetch('?api=customer&action=place_order', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    store_id: cart.store_id,
                    items: cart.items,
                    subtotal: subtotal,
                    delivery_fee: 50,
                    total: subtotal + 50,
                    payment_method: 'cod',
                    delivery_address: 'Birtamode'
                })
            }).then(r => r.json());

            if (res.success) {
                cart = { store_id: null, items: [] };
                trackOrder(res.data.order.id, res.data.order.otp);
            }
        }

        function trackOrder(id, otp) {
            document.getElementById('track-id').textContent = id;
            document.getElementById('track-steps').innerHTML = `
                <div style="display:flex; gap:16px; align-items:center">
                    <div style="width:12px; height:12px; border-radius:50%; background:var(--md-sys-color-primary)"></div>
                    <div style="font-weight:600">Order Received</div>
                </div>
                <div style="padding:16px; background:var(--md-sys-color-primary-container); border-radius:12px">
                    Share this OTP with rider: <strong style="font-size:20px">${otp}</strong>
                </div>
            `;
            showView('tracking');
        }

        function addToCart(sid, pid, name, price) {
            if (cart.store_id && cart.store_id !== sid) { cart = { store_id: sid, items: [] }; }
            cart.store_id = sid;
            const existing = cart.items.find(i => i.product_id === pid);
            if (existing) existing.quantity++;
            else cart.items.push({ product_id: pid, name, price, quantity: 1 });
            alert("Added!");
        }

        async function loadHome() {
            const res = await fetch('?api=customer&action=get_stores').then(r => r.json());
            const list = document.getElementById('home-stores-list');
            if (res.data.length === 0) {
                list.innerHTML = '<div class="empty-state"><span class="material-icons-outlined">storefront</span><div>No stores available nearby.</div></div>';
                return;
            }
            list.innerHTML = res.data.map(s => `
                <div class="store-card" onclick="loadStore(${s.id})">
                    <div class="store-image" style="background-image:url('${s.cover || 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600'}')"></div>
                    <div class="store-info">
                        <div class="store-name">${s.name}</div>
                        <div class="store-meta">
                            <span class="rating"><span class="material-icons-outlined" style="font-size:14px">star</span> 4.8</span>
                            <span>•</span>
                            <span>2.4 km</span>
                            <span>•</span>
                            <span>Rs. 50 Delivery</span>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        async function loadStore(id) {
            const res = await fetch('?api=customer&action=get_store&id=' + id).then(r => r.json());
            const s = res.data;
            document.getElementById('store-title').textContent = s.name;
            document.getElementById('store-desc').textContent = s.description;
            document.getElementById('store-products-list').innerHTML = s.products.map(p => `
                <div class="product-item">
                    <div class="product-info">
                        <div class="product-name">${p.name}</div>
                        <div style="font-size:12px; color:var(--md-sys-color-on-surface-variant); margin-bottom:8px">Juicy chicken patty with extra cheese and secret sauce.</div>
                        <div class="product-price">Rs. ${p.price}</div>
                    </div>
                    <div style="display:flex; flex-direction:column; align-items:center; gap:8px">
                        <div class="product-image" style="background-image:url('https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=200')"></div>
                        <button class="btn-add-pill" onclick="addToCart(${s.id}, ${p.id}, '${p.name}', ${p.price})">ADD</button>
                    </div>
                </div>
            `).join('');
            showView('store');
        }

        async function doLogin() {
            const phone = document.getElementById('login-phone').value;
            const pass = document.getElementById('login-pass').value;
            const res = await fetch('?api=auth&action=login', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({phone, password: pass})
            }).then(r => r.json());

            if (res.success) {
                currentUser = res.data.user;
                document.getElementById('bottom-nav').style.display = 'flex';
                updateNav();
                showView('home');
            } else {
                alert("Login failed: " + res.error);
            }
        }

        init();
    </script>
</body>
</html>
