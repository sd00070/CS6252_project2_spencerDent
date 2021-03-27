<?php include '../view/shared/header.php'; ?>
<main>

    <h2>Customer Search</h2>

    <!-- display a search form -->
    <form action="." method="post">
        <input type="hidden" name="action" value="display_customers">
        <label>Last Name:</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($last_name); ?>">
        <input type="submit" value="Search">
    </form>

    <?php if (isset($message)) : ?>
        <p class="error"><?= $message; ?></p>
    <?php elseif ($customers) : ?>
        <h2>Results</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email Address</th>
                <th>City</th>
                <th>&nbsp;</th>
            </tr>
            <?php foreach ($customers as $customer) : ?>
                <tr>
                    <td><?= htmlspecialchars(
                            $customer['firstName'] . ' ' .
                                $customer['lastName']
                        ); ?></td>
                    <td><?= htmlspecialchars($customer['email']); ?></td>
                    <td><?= htmlspecialchars($customer['city']); ?></td>
                    <td>
                        <form action="." method="post">
                            <input type="hidden" name="action" value="display_customer" />
                            <input type="hidden" name="customer_id" value="<?= htmlspecialchars($customer['customerID']); ?>" />
                            <input type="submit" value="Select" />
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?php include 'admin_login_status.php'; ?>

</main>
<?php include '../view/shared/footer.php'; ?>