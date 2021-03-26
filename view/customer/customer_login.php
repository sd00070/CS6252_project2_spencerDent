<?php include '../view/shared/header.php'; ?>
<main>

    <h2>Customer Login</h2>

    <?php if (isset($message)) : ?>
        <p class="error"><?= $message ?></p>
    <?php endif; ?>

    <p>You must login before you can register a product.</p>

    <form action="." method="post" id="aligned">
        <input type="hidden" name="action" value="get_customer">

        <label>Email:</label>
        <input type="text" name="email">
        <br>

        <label>Password:</label>
        <input type="password" name="password">
        <br>

        <label>&nbsp;</label>
        <input type="submit" value="Login">
    </form>

</main>
<?php include '../view/shared/footer.php'; ?>