<?php include '../view/shared/header.php'; ?>
<main>

    <h2>Customer Login</h2>
    <p>You must login before you can register a product.</p>
    <!-- display a search form -->
    <form action="." method="post" id="aligned">
        <input type="hidden" name="action" value="get_customer">
        <label>Email:</label>
        <input type="text" name="email" value="<?= htmlspecialchars($email); ?>">
        <br>
        <label>&nbsp;</label>
        <input type="submit" value="Login">
    </form>

</main>
<?php include '../view/shared/footer.php'; ?>