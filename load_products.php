<?php
include('database/connection.php');

function generate_star_rating($rating) {
    $rating = (float)$rating;
    $stars_html = '';
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5;
    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
    for ($i = 0; $i < $full_stars; $i++) { $stars_html .= '<i class="bi bi-star-fill"></i>'; }
    if ($half_star) { $stars_html .= '<i class="bi bi-star-half"></i>'; }
    for ($i = 0; $i < $empty_stars; $i++) { $stars_html .= '<i class="bi bi-star"></i>'; }
    return $stars_html;
}

$pwebsite = '';
if ($conn) {
    $res = $conn->query("SELECT site FROM credentials LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) {
        $pwebsite = rtrim($row['site'], '/');
    }
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

if ($conn) {
    $stmt = $conn->prepare("SELECT * FROM products LIMIT ? OFFSET ?");
    if ($stmt) {
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $products_array = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        foreach ($products_array as $fetch_product) {
            $wow_price = round((float)$fetch_product['total'] * 0.95);
            $star_rating = (float)($fetch_product['star'] ?? 0);
?>
<a href="singlepageview?pid=<?php echo $fetch_product['id']; ?>" class="products">
    <div class="productcard">
        <div class="imagecontainer">
            <img src="<?php echo htmlspecialchars($pwebsite) ?>/assets/uploads/<?php echo htmlspecialchars($fetch_product['image']); ?>" class="productimage" loading="lazy" alt="<?php echo htmlspecialchars($fetch_product['name']); ?>"/>
        </div>
        <div class="product-info">
            <p class="product-name"><?php echo htmlspecialchars($fetch_product['name']); ?></p>
            <div class="price-line">
                <span class="selling-price">₹<?php echo number_format((float)$fetch_product['total']); ?></span>
                <del class="mrp">₹<?php echo number_format((float)$fetch_product['price']); ?></del>
                <span class="discount"><?php echo htmlspecialchars($fetch_product['discount']); ?>% off</span>
            </div>
            <div class="wow-offer">
                <img class="wow-badge" src="<?php echo htmlspecialchars($pwebsite) ?>/assets/catogary/wow.webp" alt="WOW Offer">
                <span class="wow-price">₹<?php echo number_format($wow_price); ?></span>
                <span class="offer-text">with 2 offers</span>
            </div>
            <div class="rating-line">
                <div class="rating-stars"><?php echo generate_star_rating($star_rating); ?></div>
                <img class="fassured-logo-small" src="<?php echo htmlspecialchars($pwebsite) ?>/assets/catogary/assured.png" alt="F-Assured" />
            </div>
        </div>
    </div>
</a>
<?php
        }
    }
}
