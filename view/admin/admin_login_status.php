<div>
    <h2>Login Status</h2>

    <p>You are logged in as <?= $admin['username'] ?>.</p>

    <form action="." method="post">
        <input type="hidden" name="action" value="logout_admin">
        <input type="submit" value="Logout">
    </form>
</div>