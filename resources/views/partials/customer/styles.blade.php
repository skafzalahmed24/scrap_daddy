<style>
    body {
        background-color: #f8f9fa;
    }

    .dashboard-container {
        padding: 30px 15px;
        max-width: 1600px;
        margin: 0 auto;
    }

    /* --- Left Sidebar --- */
    .sidebar-wrapper {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        height: 100%;
    }

    .sidebar-profile {
        background: var(--primary-green, #1b5e20);
        padding: 30px 20px;
        display: flex;
        align-items: center;
        color: #fff;
    }

    .sidebar-nav-list {
        padding: 15px 0;
        list-style: none;
        margin: 0;
    }

    .sidebar-nav-list li {
        margin-bottom: 5px;
    }

    .sidebar-nav-list a {
        display: flex;
        align-items: center;
        padding: 12px 25px;
        color: #555;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s;
        border-left: 4px solid transparent;
    }

    .sidebar-nav-list a i {
        width: 25px;
        font-size: 1.1rem;
        color: #888;
        transition: color 0.2s;
    }

    .sidebar-nav-list a:hover,
    .sidebar-nav-list a.active {
        background: #f1f8f1;
        color: var(--primary-green, #1b5e20);
        border-left-color: var(--primary-green, #1b5e20);
    }

    .sidebar-nav-list a:hover i,
    .sidebar-nav-list a.active i {
        color: var(--primary-green, #1b5e20);
    }

    /* --- Right Sidebar --- */
    .btn-new-pickup-huge {
        background: var(--primary-green, #1b5e20);
        color: #fff;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        text-decoration: none;
        width: 100%;
        margin-bottom: 24px;
        transition: all 0.2s;
        box-shadow: 0 8px 24px rgba(27, 94, 32, 0.2);
        border: none;
    }

    .btn-new-pickup-huge:hover {
        background: #144d18;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(27, 94, 32, 0.3);
    }

    .btn-new-pickup-huge i {
        font-size: 1.8rem;
        margin-right: 16px;
        opacity: 0.9;
    }

    .btn-new-pickup-huge .text-block h5 {
        margin: 0;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .btn-new-pickup-huge .text-block p {
        margin: 0;
        font-size: 0.8rem;
        opacity: 0.9;
        margin-top: 2px;
    }

    .right-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #eaeaea;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    .right-card-title {
        display: flex;
        align-items: center;
        font-weight: 700;
        font-size: 1rem;
        color: #222;
        margin-bottom: 20px;
    }

    .timeline {
        padding-left: 10px;
        margin: 0;
        list-style: none;
        position: relative;
    }

    .timeline::before {
        content: '';
        position: absolute;
        top: 10px;
        bottom: 20px;
        left: 21px;
        width: 2px;
        background: #f0f0f0;
    }

    .timeline-item {
        position: relative;
        padding-left: 45px;
        margin-bottom: 24px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-icon {
        position: absolute;
        left: 0;
        top: 0;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        z-index: 2;
        font-size: 0.7rem;
    }

    .icon-success {
        color: #2e7d32;
        border: 2px solid #2e7d32;
    }

    .icon-warning {
        color: #f57f17;
        border: 2px solid #f57f17;
    }

    .timeline-content h6 {
        margin: 0;
        font-size: 0.85rem;
        font-weight: 700;
        color: #333;
    }

    .timeline-content p {
        margin: 0;
        font-size: 0.75rem;
        color: #777;
        margin-top: 2px;
        margin-bottom: 4px;
    }

    .timeline-meta {
        font-size: 0.7rem;
        color: #aaa;
        display: flex;
        align-items: center;
        font-weight: 600;
    }

    /* Main Center Card */
    .middle-card {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid #f0f0f0;
        min-height: 100%;
    }

    /* --- Hero Banner --- */
    .hero-banner-dash {
        background: #fff;
        border-radius: 16px;
        padding: 30px 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
    }

    .hero-banner-dash-content {
        position: relative;
        z-index: 2;
        max-width: 60%;
    }

    .hero-banner-dash h2 {
        font-weight: 800;
        color: #222;
        font-size: 1.8rem;
        margin-bottom: 8px;
    }

    .hero-banner-dash p {
        color: #666;
        font-size: 1rem;
        margin: 0;
    }

    .hero-banner-dash-bg {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        width: 45%;
        background-image: url('{{ asset('requestbanenr.png') }}');
        background-size: cover;
        background-position: center left;
        z-index: 1;
        -webkit-mask-image: linear-gradient(to right, transparent, black 30%);
        mask-image: linear-gradient(to right, transparent, black 30%);
    }

    @media (max-width: 991px) {
        .hero-banner-dash-bg {
            width: 100%;
            opacity: 0.2;
        }

        .hero-banner-dash-content {
            max-width: 100%;
        }
    }

    /* --- Styles from home.blade.php --- */
    /* Horizontally Scrolling Pickups */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        margin-top: 32px;
    }

    .section-header h5 {
        font-weight: 700;
        color: #222;
        margin: 0;
    }

    .section-header a {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--primary-green, #1b5e20);
        text-decoration: none;
    }

    .horizontal-scroll-container {
        display: flex;
        overflow-x: auto;
        gap: 16px;
        padding-bottom: 15px;
        scroll-snap-type: x mandatory;
        scrollbar-width: thin;
        scrollbar-color: #ccc transparent;
    }

    .horizontal-scroll-container::-webkit-scrollbar {
        height: 6px;
    }

    .horizontal-scroll-container::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 10px;
    }

    .pickup-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #eaeaea;
        padding: 20px;
        min-width: 280px;
        flex: 0 0 auto;
        scroll-snap-align: start;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
    }

    .pickup-card .badge-wrap {
        display: flex;
        justify-content: center;
        margin-bottom: 16px;
    }

    .pickup-card .badge {
        padding: 6px 12px;
        font-weight: 600;
        border-radius: 20px;
        font-size: 0.75rem;
    }

    .pickup-detail-row {
        display: flex;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .pickup-detail-row i {
        font-size: 1.4rem;
        color: #888;
        margin-right: 12px;
        margin-top: 4px;
    }

    .pickup-detail-row .text h6 {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 700;
        color: #333;
    }

    .pickup-detail-row .text p {
        margin: 0;
        font-size: 0.75rem;
        color: #777;
        margin-top: 2px;
    }

    .pickup-card .category-info {
        margin-bottom: 16px;
    }

    .pickup-card .category-info h6 {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 700;
        color: #333;
    }

    .pickup-card .category-info p {
        margin: 0;
        font-size: 0.75rem;
        color: #777;
    }

    .earned-box {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 16px;
    }

    .earned-box label {
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 2px;
        display: block;
    }

    .earned-box .amount {
        font-size: 1.2rem;
        font-weight: 800;
        margin: 0;
    }

    .earned-box.completed {
        background: #f1f8f1;
        color: var(--primary-green, #1b5e20);
    }

    .earned-box.pending {
        background: #fff8e1;
        color: #f57f17;
    }

    .btn-view-details {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        text-align: center;
        background: #fff;
        border: 1px solid var(--primary-green, #1b5e20);
        color: var(--primary-green, #1b5e20);
        transition: all 0.2s;
        margin-top: auto;
    }

    .btn-view-details:hover {
        background: var(--primary-green, #1b5e20);
        color: #fff;
    }

    .btn-view-details.warning {
        border-color: #f57f17;
        color: #f57f17;
    }

    .btn-view-details.warning:hover {
        background: #f57f17;
        color: #fff;
    }

    /* Quick Actions */
    .quick-actions-row {
        display: flex;
        gap: 16px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }

    .quick-action-card {
        background: #fff;
        border-radius: 12px;
        padding: 16px;
        border: 1px solid #eaeaea;
        display: flex;
        align-items: center;
        flex: 1;
        min-width: 180px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        cursor: pointer;
        transition: all 0.2s;
    }

    .quick-action-card:hover {
        border-color: var(--primary-green, #1b5e20);
    }

    .quick-action-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .quick-action-card h6 {
        margin: 0;
        font-size: 0.85rem;
        font-weight: 700;
        color: #333;
    }

    .quick-action-card p {
        margin: 0;
        font-size: 0.7rem;
        color: #777;
        margin-top: 2px;
    }

    .stat-green .stat-icon {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .stat-green .stat-info small {
        color: #2e7d32;
    }

    .stat-blue .stat-icon {
        background: #e3f2fd;
        color: #1565c0;
    }

    .stat-blue .stat-info small {
        color: #1565c0;
    }

    .stat-orange .stat-icon {
        background: #fff3e0;
        color: #f57c00;
    }

    .stat-orange .stat-info small {
        color: #f57c00;
    }

    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        height: 100%;
        border: 1px solid #f0f0f0;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-right: 16px;
        flex-shrink: 0;
    }

    .stat-info h3 {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 800;
        color: #222;
    }

    .stat-info p {
        margin: 0;
        font-size: 0.8rem;
        font-weight: 600;
        color: #555;
    }

    .stat-info small {
        font-size: 0.7rem;
        display: block;
        margin-top: 4px;
    }

    /* --- Styles from orders.blade.php --- */
    .filter-btn-group {
        display: inline-flex;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 4px;
        margin-bottom: 24px;
        flex-wrap: wrap;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }

    .filter-btn {
        background: transparent;
        border: none;
        color: #6c757d;
        padding: 8px 18px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-btn i {
        font-size: 1rem;
    }

    .filter-btn:hover {
        background: #f8f9fa;
        color: #495057;
    }

    .filter-btn.active {
        background: rgba(46, 125, 50, 0.08);
        color: #2e7d32;
    }
    .filter-btn.active i {
        color: #2e7d32 !important;
    }

    .order-item {
        background: #fafafa;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.2s ease;
    }

    .order-item:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        background: #fff;
        transform: translateY(-2px);
        border-color: #e0e0e0;
    }

    .b-orange {
        background-color: #fff3e0;
        color: #f57c00;
        border-color: #ffe0b2 !important;
    }

    .t-orange {
        color: #f57c00;
    }

    .b-blue {
        background-color: #e3f2fd;
        color: #1976d2;
        border-color: #bbdefb !important;
    }

    .t-blue {
        color: #1976d2;
    }

    .b-green {
        background-color: #e8f5e9;
        color: #2e7d32;
        border-color: #c8e6c9 !important;
    }

    .t-green {
        color: #2e7d32;
    }

    .b-red {
        background-color: #ffebee;
        color: #c62828;
        border-color: #ffcdd2 !important;
    }

    .t-red {
        color: #c62828;
    }

    .section-header-h5 {
        font-weight: 800;
        color: #1a2b4c;
        margin-bottom: 16px;
        font-size: 1.1rem;
    }

    /* --- Styles from payments.blade.php --- */
    .payment-item {
        background: #fafafa;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.2s ease;
    }

    .payment-item:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        background: #fff;
        transform: translateY(-2px);
        border-color: #e0e0e0;
    }

    /* --- Styles from show.blade.php --- */
    .page-content-wrapper {
        line-height: 1.8;
        font-size: 1.05rem;
        color: #444;
    }

    .page-content-wrapper h1,
    .page-content-wrapper h2,
    .page-content-wrapper h3 {
        color: var(--primary-blue, #0d2b4d);
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }

    .page-content-wrapper p {
        margin-bottom: 1.2rem;
    }

    /* --- Styles from payment.blade.php --- */
    .payment-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 100px);
        padding: 40px 20px;
    }

    .payment-card-centered {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 20px 50px rgba(22, 163, 74, 0.1);
        width: 100%;
        max-width: 650px;
        padding: 50px;
        text-align: center;
        position: relative;
    }

    .wallet-icon {
        font-size: 5rem;
        color: #22c55e;
        margin-bottom: 20px;
        position: relative;
        display: inline-block;
    }

    .wallet-icon .check-badge {
        position: absolute;
        bottom: 0;
        right: -10px;
        font-size: 2rem;
        color: #15803d;
        background: #fff;
        border-radius: 50%;
    }

    .title-main {
        font-weight: 800;
        color: #1e293b;
        font-size: 2.2rem;
        margin-bottom: 10px;
    }

    .title-main span {
        color: #16a34a;
    }

    .subtitle-main {
        color: #64748b;
        font-size: 1rem;
        margin-bottom: 30px;
    }

    .dashed-box {
        border: 2px dashed #bbf7d0;
        border-radius: 16px;
        padding: 30px;
        background: #ffffff;
        margin-bottom: 30px;
    }

    .amt-label {
        color: #16a34a;
        font-weight: 700;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 5px;
    }

    .amt-value {
        color: #14532d;
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 30px;
    }

    .features-row {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-bottom: 30px;
    }

    .feature-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        text-align: left;
    }

    .feature-item i {
        color: #16a34a;
        font-size: 1.4rem;
        margin-top: 2px;
    }

    .feature-item h6 {
        font-size: 0.85rem;
        font-weight: 700;
        color: #334155;
        margin: 0;
    }

    .feature-item p {
        font-size: 0.75rem;
        color: #94a3b8;
        margin: 0;
    }

    .razorpay-payment-button {
        width: 100%;
        background: #15803d !important;
        color: #fff !important;
        font-weight: 700 !important;
        font-size: 1.1rem !important;
        padding: 16px !important;
        border-radius: 10px !important;
        border: none !important;
        box-shadow: 0 4px 15px rgba(21, 128, 61, 0.3) !important;
        cursor: pointer !important;
        transition: all 0.3s !important;
    }

    .razorpay-payment-button:hover {
        background: #166534 !important;
        transform: translateY(-2px) !important;
    }

    .or-divider {
        display: flex;
        align-items: center;
        text-align: center;
        color: #94a3b8;
        font-size: 0.85rem;
        margin: 20px 0;
        width: 100%;
        max-width: 300px;
        margin-left: auto;
        margin-right: auto;
    }

    .or-divider::before,
    .or-divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e2e8f0;
    }

    .or-divider::before {
        margin-right: 10px;
    }

    .or-divider::after {
        margin-left: 10px;
    }

    .cancel-link {
        color: #475569;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
        display: inline-block;
        margin-bottom: 30px;
    }

    .cancel-link:hover {
        color: #0f172a;
    }

    .security-footer {
        background: #f8fafc;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-align: left;
    }

    .security-text {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .security-text .icon-circle {
        width: 40px;
        height: 40px;
        background: #16a34a;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .security-text h6 {
        font-size: 0.85rem;
        font-weight: 700;
        color: #334155;
        margin: 0;
    }

    .security-text p {
        font-size: 0.75rem;
        color: #64748b;
        margin: 0;
    }

    .security-logos {
        display: flex;
        gap: 10px;
        align-items: center;
        font-size: 2rem;
        color: #0d2b4d;
        opacity: 0.8;
    }

    @media (max-width: 600px) {
        .payment-card-centered {
            padding: 30px 20px;
        }

        .features-row {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
            padding-left: 20px;
        }

        .security-footer {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .security-text {
            flex-direction: column;
            text-align: center;
            gap: 10px;
        }
    }

    /* --- Styles from request-pickup.blade.php --- */
    .hero-banner {
        position: relative;
        background-color: #ffffff;
        border-radius: 16px 16px 0 0;
        padding: 40px;
        overflow: hidden;
    }

    .hero-banner-content {
        position: relative;
        z-index: 2;
    }

    .hero-banner-bg {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        width: 60%;
        background-image: url('{{ asset('requestbanenr.png') }}');
        background-size: cover;
        background-position: center left;
        background-repeat: no-repeat;
        z-index: 1;
        opacity: 0.9;
        -webkit-mask-image: linear-gradient(to right, transparent, black 40%);
        mask-image: linear-gradient(to right, transparent, black 40%);
    }

    .form-container {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
        position: relative;
        z-index: 10;
        margin-top: -20px;
    }

    /* Stepper UI */
    .stepper-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
        position: relative;
    }

    .stepper-line {
        position: absolute;
        top: 24px;
        left: 50px;
        right: 50px;
        height: 1px;
        background-image: linear-gradient(to right, #ccc 50%, rgba(255, 255, 255, 0) 0%);
        background-position: bottom;
        background-size: 8px 1px;
        background-repeat: repeat-x;
        z-index: 1;
    }

    .step-item {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        background: #fff;
        padding: 0 10px;
    }

    .step-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-right: 12px;
        border: 1px solid #e0e0e0;
        color: #6c757d;
        background: #fff;
        transition: all 0.3s;
    }

    .step-item.active .step-circle {
        background-color: var(--primary-green, #1b5e20);
        border-color: var(--primary-green, #1b5e20);
        color: #fff;
    }

    .step-item.completed .step-circle {
        border-color: var(--primary-green, #1b5e20);
        color: var(--primary-green, #1b5e20);
    }

    .step-text {
        display: flex;
        flex-direction: column;
    }

    .step-num {
        font-size: 0.8rem;
        color: #6c757d;
        font-weight: 600;
    }

    .step-title {
        font-size: 0.95rem;
        color: #333;
        font-weight: 600;
    }

    .step-item.active .step-title {
        color: var(--primary-green, #1b5e20);
    }

    /* Category Cards */
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 16px;
    }

    @media (max-width: 576px) {
        .category-grid {
            grid-template-columns: 1fr;
        }

        .hero-banner-bg {
            width: 100%;
            opacity: 0.2;
        }
    }

    .category-card {
        border: 1px solid #eaeaea;
        border-radius: 12px;
        padding: 16px;
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        background: #fff;
    }

    .category-card:hover {
        border-color: #d0d0d0;
    }

    .category-card.selected {
        border-color: var(--primary-green, #1b5e20);
        background-color: #f1f8f1;
    }

    .category-icon-wrapper {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: #f4f6f8;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        color: #555;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .category-card.selected .category-icon-wrapper {
        background: #e2f1e2;
        color: var(--primary-green, #1b5e20);
    }

    .category-info h6 {
        margin: 0;
        font-weight: 700;
        color: #222;
        font-size: 0.95rem;
    }

    .category-info p {
        margin: 0;
        font-size: 0.75rem;
        color: #777;
        margin-top: 2px;
    }

    .check-icon {
        position: absolute;
        top: 10px;
        right: 10px;
        color: var(--primary-green, #1b5e20);
        font-size: 1.1rem;
        display: none;
    }

    .category-card.selected .check-icon {
        display: block;
    }

    /* Custom Inputs */
    .section-title {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
    }

    .section-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e8f5e9;
        color: var(--primary-green, #1b5e20);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-size: 0.9rem;
    }

    .section-title h5 {
        margin: 0;
        font-weight: 700;
        font-size: 1.05rem;
    }

    .section-subtitle {
        margin: 0 0 0 44px;
        font-size: 0.85rem;
        color: #777;
        margin-bottom: 20px;
    }

    .location-input-group {
        display: flex;
        align-items: center;
        border: 1px solid #eaeaea;
        border-radius: 12px;
        padding: 6px 16px;
        background: #fff;
    }

    .location-input-group i.fa-location-dot {
        color: #555;
        margin-right: 12px;
    }

    .location-input-group input {
        border: none;
        outline: none;
        flex: 1;
        padding: 10px 0;
        font-size: 0.95rem;
    }

    .use-location-btn {
        background: none;
        border: none;
        color: var(--primary-green, #1b5e20);
        font-weight: 600;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        cursor: pointer;
        white-space: nowrap;
    }

    .use-location-btn i {
        margin-right: 6px;
    }

    .btn-next-step {
        background: var(--primary-green, #1b5e20);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 24px;
        font-weight: 600;
        transition: background 0.2s;
    }

    .btn-next-step:hover {
        background: #144d18;
        color: #fff;
    }

    .step-container {
        display: none;
        animation: fadeIn 0.4s ease-in-out;
    }

    .step-container.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Date & Time Step */
    .date-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 12px;
    }

    .date-btn {
        border: 1px solid #eaeaea;
        border-radius: 12px;
        padding: 16px 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fff;
        position: relative;
    }

    .date-btn:hover {
        border-color: #ccc;
    }

    .date-btn.selected {
        border: 2px solid var(--primary-green, #1b5e20);
        background-color: #f8fbf8;
    }

    .date-btn .day {
        font-weight: 800;
        font-size: 1.4rem;
        display: block;
        color: #222;
        line-height: 1.1;
    }

    .date-btn .month {
        font-size: 0.85rem;
        color: #666;
        font-weight: 600;
        display: block;
        margin-top: 4px;
    }

    .date-btn .day-name {
        font-size: 0.8rem;
        color: #888;
        display: block;
    }

    .date-btn.selected .day,
    .date-btn.selected .month,
    .date-btn.selected .day-name {
        color: var(--primary-green, #1b5e20);
    }

    .date-btn.selected .check-icon {
        display: block;
    }

    .time-card {
        border: 1px solid #eaeaea;
        border-radius: 12px;
        padding: 16px;
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fff;
        margin-bottom: 12px;
    }

    .time-card:hover {
        border-color: #ccc;
    }

    .time-card.selected {
        background: var(--primary-green, #1b5e20);
        border-color: var(--primary-green, #1b5e20);
        color: white;
    }

    .time-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        font-size: 1.2rem;
        color: #f59e0b;
    }

    .time-card.selected .time-icon {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .time-info h6 {
        margin: 0;
        font-weight: 700;
        font-size: 0.95rem;
        color: inherit;
    }

    .time-info p {
        margin: 0;
        font-size: 0.8rem;
        color: #888;
        margin-top: 2px;
    }

    .time-card.selected .time-info p {
        color: rgba(255, 255, 255, 0.8);
    }

    .time-radio {
        margin-left: auto;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .time-card.selected .time-radio {
        border-color: white;
        background: white;
    }

    .time-card.selected .time-radio::after {
        content: '\f00c';
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        color: var(--primary-green, #1b5e20);
        font-size: 0.7rem;
    }

    .custom-file-upload {
        border: 1px solid #eaeaea;
        border-radius: 12px;
        padding: 8px 16px;
        display: flex;
        align-items: center;
        background: #fff;
        cursor: pointer;
    }

    .upload-btn {
        background: #e8f5e9;
        color: var(--primary-green, #1b5e20);
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 0.85rem;
        margin-right: 16px;
        pointer-events: none;
    }

    .file-name-display {
        color: #666;
        font-size: 0.9rem;
    }

    .textarea-wrapper {
        position: relative;
    }

    .textarea-wrapper i {
        position: absolute;
        top: 16px;
        left: 16px;
        color: #888;
    }

    .textarea-wrapper textarea {
        padding-left: 45px;
        border: 1px solid #eaeaea;
        border-radius: 12px;
        padding-top: 14px;
    }

    .security-banner {
        background: #f1f8f1;
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        margin-top: 24px;
    }

    .security-icon-left {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e2f1e2;
        color: var(--primary-green, #1b5e20);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-right: 16px;
    }

    .security-text h6 {
        margin: 0;
        font-weight: 700;
        color: #222;
        font-size: 0.9rem;
    }

    .security-text p {
        margin: 0;
        font-size: 0.75rem;
        color: #666;
    }

    .security-icon-right {
        margin-left: auto;
        color: var(--primary-green, #1b5e20);
        font-size: 1.2rem;
    }

    .image-preview-wrapper {
        position: relative;
        display: inline-block;
        margin-right: 10px;
        margin-bottom: 10px;
    }

    .image-preview-wrapper img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    .remove-img-btn {
        position: absolute;
        top: -5px;
        right: -5px;
        background: red;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        line-height: 18px;
        text-align: center;
        cursor: pointer;
        font-size: 12px;
        border: none;
    }

    /* Step 3 Review Card */
    .review-card {
        border: 1px solid #eaeaea;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        background: #fff;
    }

    .review-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 24px;
    }

    .review-item:last-child {
        margin-bottom: 0;
    }

    .review-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e8f5e9;
        color: var(--primary-green, #1b5e20);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        flex-shrink: 0;
    }

    .review-text label {
        margin: 0;
        font-size: 0.8rem;
        color: #777;
        display: block;
        margin-bottom: 2px;
    }

    .review-text p {
        margin: 0;
        font-weight: 700;
        color: #222;
        font-size: 0.95rem;
    }

    .review-images {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }

    .review-images img {
        width: 50px;
        height: 50px;
        border-radius: 6px;
        object-fit: cover;
        border: 1px solid #ddd;
    }

    .alert-success-custom {
        background: #f1f8f1;
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        color: #444;
        font-size: 0.9rem;
    }

    .alert-success-custom i {
        color: var(--primary-green, #1b5e20);
        font-size: 1.2rem;
        margin-right: 12px;
    }

    .alert-success-custom strong {
        color: var(--primary-green, #1b5e20);
    }

    @media (min-width: 768px) {
        .border-end-md {
            border-right: 1px solid #eaeaea;
        }
    }

    /* Sidebar Components */
    .sidebar-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.03);
    }

    .feature-list {
        padding: 0;
        margin: 0;
    }

    .feature-list li {
        display: flex;
        align-items: flex-start;
        margin-bottom: 24px;
    }

    .feature-list li:last-child {
        margin-bottom: 0;
    }

    .feature-list .icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e8f5e9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-green, #1b5e20);
        margin-right: 16px;
        flex-shrink: 0;
    }

    .feature-list .text h6 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: #222;
    }

    .feature-list .text p {
        margin: 0;
        font-size: 0.8rem;
        color: #777;
        margin-top: 4px;
    }

    .contact-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        background: #f8f9fa;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        color: #333;
    }

    .contact-item i {
        width: 24px;
    }

    /* Trust Badges */
    .trust-badges-container {
        background: #f1f8f1;
        border-radius: 12px;
        padding: 24px;
    }

    .trust-item {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: left;
    }

    .trust-item i {
        font-size: 1.8rem;
        color: var(--primary-green, #1b5e20);
        margin-right: 12px;
    }

    .trust-item h6 {
        margin: 0;
        font-weight: 700;
        font-size: 0.95rem;
        color: #222;
    }

    .trust-item p {
        margin: 0;
        font-size: 0.75rem;
        color: #666;
    }
</style>