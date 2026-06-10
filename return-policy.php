<?php
include('database/connection.php');
session_start();

if (!function_exists('get_cached_query_result')) {
    function get_cached_query_result($conn, $sql, $types, $params, $cache_file = null, $ttl = null) {
        if (!$conn) return [];
        $stmt = $conn->prepare($sql);
        if (!$stmt) return [];
        if ($types && !empty($params)) { $stmt->bind_param($types, ...$params); }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }
}

$brandName = 'YourStore';
if ($conn) {
    $settings_sql = "SELECT setting_value FROM settings WHERE setting_key = 'brand_name' LIMIT 1";
    $settings_data = get_cached_query_result($conn, $settings_sql, null, []);
    if (!empty($settings_data)) {
        $brandName = htmlspecialchars($settings_data[0]['setting_value']);
    }
}

$pwebsite = '';
if ($conn) {
    $site_sql = "SELECT site FROM credentials LIMIT 1";
    $site_data = get_cached_query_result($conn, $site_sql, null, []);
    if (!empty($site_data)) {
        $pwebsite = rtrim($site_data[0]['site'], '/');
    }
}

$categories_sql = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category ASC";
$all_categories_data = get_cached_query_result($conn, $categories_sql, null, []);
$all_categories = [];
foreach ($all_categories_data as $row) {
    $all_categories[] = $row['category'];
}

$cart_count = (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) ? array_sum($_SESSION['cart']) : 0;
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$canonical_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="gu-IN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Policy | <?php echo $brandName; ?></title>
    <meta name="description" content="Read the Return Policy for <?php echo $brandName; ?> to understand our terms for returns, exchanges, and refunds.">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f1f2f4; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; }
        .main-container { max-width: 1248px; margin: 0 auto; background-color: #fff; }
        .page-header { background-color: #FFFFFF; padding: 8px 16px; position: sticky; top: 0; z-index: 1000; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.1); }
        .top-bar { display: flex; justify-content: space-between; align-items: center; }
        .logo-container .logo-img { height: 38px; vertical-align: middle; }
        .cart-link a { color: #212121; text-decoration: none; display: flex; align-items: center; position: relative; }
        .cart-icon { width: 24px; height: 24px; }
        .cart-link .badge { position: absolute; top: -8px; right: -10px; }
        .location-and-search { margin-top: 12px; }
        .search-bar { display: flex; align-items: center; background-color: #f0f2f5; border-radius: 8px; padding: 10px 16px; }
        .search-icon { width: 20px; height: 20px; margin-right: 12px; opacity: 0.6; }
        .search-bar input { border: none; outline: none; width: 100%; font-size: 14px; background-color: transparent; }
        .return-policy-content { padding: 30px; line-height: 1.6; }
        .return-policy-content h1, .return-policy-content h2, .return-policy-content h3 { color: #333; margin-bottom: 20px; margin-top: 30px; }
        .return-policy-content p, .return-policy-content ul, .return-policy-content ol, .return-policy-content table { color: #555; margin-bottom: 15px; }
        .return-policy-content ul, .return-policy-content ol { margin-left: 20px; }
        .return-policy-content ul li, .return-policy-content ol li { margin-bottom: 8px; }
        .return-policy-content table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .return-policy-content th, .return-policy-content td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .return-policy-content th { background-color: #f2f2f2; font-weight: bold; }
    </style>
</head>
<body>
<div class="main-container">
    <header class="page-header">
        <div class="top-bar">
            <div class="d-flex align-items-center">
                <button class="btn p-0 d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sideMenu" aria-label="Open Menu">
                    <i class="bi bi-list" style="color: #212121; font-size: 24px;"></i>
                </button>
                <div class="logo-container">
                    <img src="<?php echo $pwebsite ?>/assets/catogary/svg-image-1.svg" alt="Logo" class="logo-img">
                </div>
            </div>
            <div class="cart-link">
                <a href="cart">
                    <svg class="cart-icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#212121"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-1.45-5c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.37-.66-.11-1.48-.87-1.48H5.21l-.94-2H1v2h2l3.6 7.59-1.35 2.44C4.52 15.37 5.24 17 6.5 17h12v-2H6.5c-.25 0-.42-.21-.38-.45l.93-1.68h7.45z"/></svg>
                    <?php if ($cart_count > 0): ?>
                        <span class="badge bg-danger rounded-pill"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
        <div class="location-and-search">
            <div class="search-bar">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <input type="text" placeholder="Search for Products">
            </div>
        </div>
    </header>

    <main>
        <section class="return-policy-content">
            <h1 class="text-center mb-4">Return Policy for <?php echo $brandName; ?></h1>
            <p class="text-center lead mb-5"><em>Last updated: October 3, 2025</em></p>

            <p>At <?php echo $brandName; ?>, we want you to be completely satisfied with your purchase. If for any reason you are not, we offer a straightforward return and exchange policy.</p>

            <h2>1. Eligibility for Returns & Exchanges</h2>
            <ul>
                <li>Return request must be initiated within <strong>7 days</strong> of delivery.</li>
                <li>Item must be unused, unwashed, and in original condition.</li>
                <li>Must be in original packaging with all tags and accessories intact.</li>
                <li>Sale items may have different return conditions.</li>
                <li>Intimate apparel, customized, or perishable items may be non-returnable.</li>
            </ul>

            <h2>2. How to Initiate a Return</h2>
            <ol>
                <li>Log in and go to "Order History".</li>
                <li>Select the order and click "Return/Exchange".</li>
                <li>Follow on-screen instructions to submit your request.</li>
                <li>Guest checkouts: contact customer service with your order number.</li>
                <li>Our team will provide return instructions and shipping address.</li>
            </ol>

            <h2>3. Return Shipping</h2>
            <ul>
                <li>If return is due to our error (wrong/defective item), we cover shipping costs.</li>
                <li>If return is due to change of mind, customer bears shipping costs.</li>
                <li>Use a trackable shipping service — we are not responsible for lost returns.</li>
            </ul>

            <h2>4. Refunds</h2>
            <ul>
                <li>Refunds processed within <strong>7-10 business days</strong> after inspection.</li>
                <li>Refund applied to original payment method.</li>
                <li>Shipping charges are non-refundable unless return is due to our error.</li>
            </ul>

            <h2>5. Exchanges</h2>
            <p>Exchanges are subject to product availability. If the desired item is unavailable, a refund will be issued.</p>

            <h2>6. Damaged or Defective Items</h2>
            <p>Contact us within 24-48 hours of delivery with photos. We will arrange a replacement or full refund including return shipping.</p>

            <h2>7. Cancellations</h2>
            <p>Orders can be cancelled free of charge within <strong>24 hours</strong> of purchase if not yet shipped.</p>

            <h2>8. Contact Us</h2>
            <p>For questions about our Return Policy, please <a href="/contact-us">contact us</a>.</p>
        </section>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include('footer.php'); ?>
</body>
</html>
