<?php include '../view/shared/header.php'; ?>
<main>

    <h2>Administrator Login</h2>

    <?php if (isset($message)) : ?>
        <p class="error"><?= $message ?></p>
    <?php endif; ?>

    <form action="." method="post" id="aligned">
        <input type="hidden" name="action" value="login_admin">

        <label>Username:</label>
        <input type="text" name="username">
        <br>

        <label>Password:</label>
        <input type="password" name="password">
        <br>

        <label>&nbsp;</label>
        <input type="submit" value="Login">
    </form>

</main>
<?php include '../view/shared/footer.php'; ?>