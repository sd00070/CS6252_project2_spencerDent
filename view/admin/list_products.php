<?php include '../view/shared/header.php'; ?>
<main>
    <h1>Product List</h1>

    <!-- display a table of products -->
    <table>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Version</th>
            <th>Release Date</th>
            <th>&nbsp;</th>
        </tr>
        <?php foreach ($products as $product) : ?>
            <tr>
                <td><?= htmlspecialchars($product['productCode']); ?></td>
                <td><?= htmlspecialchars($product['name']); ?></td>
                <td><?= htmlspecialchars($product['version']); ?></td>
                <td><?= htmlspecialchars($product['releaseDate']); ?></td>
                <td>
                    <form action="." method="post">
                        <input type="hidden" name="action" value="delete_product">
                        <input type="hidden" name="product_code" value="<?= htmlspecialchars($product['productCode']); ?>">
                        <input type="submit" value="Delete">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p><a href="?action=show_add_form">Add Product</a></p>

</main>
<?php include '../view/shared/footer.php'; ?>