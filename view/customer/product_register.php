<?php include '../view/shared/header.php'; ?>
<main>

    <h2>Register Product</h2>
    <?php if (isset($message)) : ?>
        <p><?= $message; ?></p>
    <?php else : ?>
        <form action="." method="post" id="aligned">
            <input type="hidden" name="action" value="register_product">
            <input type="hidden" name="customer_id" value="<?= htmlspecialchars($customer['customerID']); ?>">

            <label>Customer:</label>
            <label><?= htmlspecialchars($customer['firstName'] . ' ' . $customer['lastName']) ?></label>
            <br>

            <label>Product:</label>
            <select name="product_code">
                <?php foreach ($products as $product) : ?>
                    <option value="<?= htmlspecialchars($product['productCode']); ?>">
                        <?= htmlspecialchars($product['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br>

            <label>&nbsp;</label>
            <input type="submit" value="Register Product" />
        </form>

        <p>You are logged in as <?= htmlspecialchars($customer['email']) ?></p>
        <form action="." method="POST">
            <input type="hidden" name="action" value="logout">
            <input type="submit" value="Logout">
        </form>
    <?php endif; ?>

</main>
<?php include '../view/shared/footer.php'; ?>