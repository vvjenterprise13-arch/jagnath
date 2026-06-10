<?php
if (!isset($conn)) {
    include_once 'database/connection.php';
}

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

$footer_contact_settings = [];
if (isset($conn)) {
    $settings_keys_to_fetch = ['contact_email', 'contact_phone', 'full_address'];
    foreach ($settings_keys_to_fetch as $key) {
        $sql = "SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1";
        $data = get_cached_query_result($conn, $sql, 's', [$key]);
        if (!empty($data)) {
            $footer_contact_settings[$key] = htmlspecialchars($data[0]['setting_value']);
        }
    }
}

$footer_brandName = $brandName ?? 'YourStore';
$footer_contactEmail = $footer_contact_settings['contact_email'] ?? 'info@yourstore.com';
$footer_contactPhone = $footer_contact_settings['contact_phone'] ?? '+91 9999999999';
$footer_fullAddress = $footer_contact_settings['full_address'] ?? 'India';
?>

<div class="offcanvas offcanvas-start" tabindex="-1" id="sideMenu">
    <div class="offcanvas-header" style="background-color: #1F74BA; color: white;">
        <h5 class="offcanvas-title">Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <nav class="nav flex-column">
            <a class="nav-link" href="/" style="color: #212121;"><i class="bi bi-house-door-fill"></i> Home</a>
            <?php if (isset($all_categories) && !empty($all_categories)): ?>
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#categoryCollapse" style="color: #212121;"><i class="bi bi-grid-fill"></i> Shop by Category</a>
                <div class="collapse" id="categoryCollapse">
                    <ul class="list-unstyled m-0 p-0">
                        <?php foreach ($all_categories as $category): ?>
                        <li><a class="dropdown-item ps-5" href="category_products?category=<?php echo urlencode($category); ?>" style="color: #212121;"><?php echo htmlspecialchars(ucfirst($category)); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
            <a class="nav-link" href="/about-us" style="color: #212121;"><i class="bi bi-info-circle-fill"></i> About Us</a>
<a class="nav-link" href="/contact-us" style="color: #212121;"><i class="bi bi-telephone-fill"></i> Contact Us</a>
<hr class="my-2">
<a class="nav-link" href="/shipping-policy" style="color: #212121;"><i class="bi bi-truck"></i> Shipping Policy</a>
<a class="nav-link" href="/return-policy" style="color: #212121;"><i class="bi bi-box-arrow-left"></i> Return Policy</a>
        </nav>
    </div>
</div>

<footer class="bg-dark text-white mt-5">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
                <h5><?php echo $footer_brandName; ?></h5>
                <p>Shop the latest trends in fashion, electronics, and home goods.</p>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                  <li><a href="/about-us" class="text-white-50">About Us</a></li>
<li><a href="/contact-us" class="text-white-50">Contact Us</a></li>
<li><a href="/privacy-policy" class="text-white-50">Privacy Policy</a></li>
<li><a href="/about-us" class="text-white-50">Terms & Conditions</a></li>
<li><a href="/shipping-policy" class="text-white-50">Shipping Policy</a></li>
<li><a href="/return-policy" class="text-white-50">Return & Refund Policy</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Contact Us</h5>
                <ul class="list-unstyled">
                    <li><i class="bi bi-geo-alt-fill me-2"></i><?php echo $footer_fullAddress; ?></li>
                    <li><i class="bi bi-envelope-fill me-2"></i><?php echo $footer_contactEmail; ?></li>
                    <li><i class="bi bi-phone-fill me-2"></i><?php echo $footer_contactPhone; ?></li>
                </ul>
            </div>
        </div>
        <hr class="mt-4 mb-3">
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date("Y"); ?> <?php echo $footer_brandName; ?>. All rights reserved.</p>
        </div>
    </div>
</footer>
