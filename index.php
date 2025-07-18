<?php
require_once 'includes/header.php';
$result = mysqli_query($conn, "SELECT * FROM product LIMIT 6");
?>
<h2>Our Footwear Collection</h2>
<div class="products">
    <?php while ($product = mysqli_fetch_assoc($result)): ?>
        <div class="product">
            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
            <h3><?php echo $product['name']; ?></h3>
            <p><?php echo $product['description']; ?></p>
            <p>NRS <?php echo number_format($product['price'], 2); ?></p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="cart.php" method="post" onsubmit="return validateQuantity(this)">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" required>
                    <button type="submit" name="add_to_cart">Add to Cart</button>
                </form>
            <?php else: ?>
                <p><a href="login.php">Login</a> to add to cart</p>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>
<?php require_once 'includes/footer.php'; ?>