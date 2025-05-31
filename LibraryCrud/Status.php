<?php
$mysqls = new mysqli('localhost', 'root', '', 'booksdb');

// Check connection
if ($mysqls->connect_error) {
    die("Connection failed: " . $mysqls->connect_error);
}

// Fetch transaction data with user and book information
$transactionQuery = "
    SELECT 
        t.transaction_id, 
        u.user_name, 
        td.country, 
        td.province, 
        td.address, 
        td.contactnumber,
        b.title AS book_title, 
        t.quantity_purchased, 
        (t.quantity_purchased * b.price) AS total_price
    FROM 
        transactions_order t
    JOIN 
        users u ON t.user_id = u.user_id
    JOIN 
        books b ON t.book_id = b.book_id
    JOIN 
        transaction_details td ON t.transaction_id = td.transaction_id
";

$transactionResult = $mysqls->query($transactionQuery);

if (!$transactionResult) {
    echo "<tr><td colspan='9'>Error fetching data: " . $mysqls->error . "</td></tr>";
} elseif ($transactionResult->num_rows == 0) {
    echo "<tr><td colspan='9'>No transactions found.</td></tr>";
} else {
    while ($row = $transactionResult->fetch_assoc()) {
        $transaction_id = htmlspecialchars($row['transaction_id']);
        $user_name = htmlspecialchars($row['user_name']);
        $country = htmlspecialchars($row['country']);
        $province = htmlspecialchars($row['province']);
        $address = htmlspecialchars($row['address']);
        $contactnumber = htmlspecialchars($row['contactnumber']);
        $book_title = htmlspecialchars($row['book_title']);
        $quantity_purchased = htmlspecialchars($row['quantity_purchased']);
        $total_price = htmlspecialchars($row['total_price']);

        echo "<tr>";
        echo "<td>" . $transaction_id . "</td>";
        echo "<td>" . $user_name . "</td>";
        echo "<td>" . $country . "</td>";
        echo "<td>" . $province . "</td>";
        echo "<td>" . $address . "</td>";
        echo "<td>" . $contactnumber . "</td>";
        echo "<td>" . $book_title . "</td>";
        echo "<td>" . $quantity_purchased . "</td>";
        echo "<td>" . $total_price . "</td>";
        echo "</tr>";
    }
}
?>
