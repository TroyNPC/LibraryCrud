    <?php
    session_start(); 
    ob_start();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "booksdb";

    $mysql = new mysqli($servername, $username, $password, $dbname);

    if ($mysql->connect_error) {
        die("Connection failed: " . $mysql->connect_error);
    }

    if (!isset($_SESSION['username'])) {

        header("Location: Login.php");
        exit();
    }
    $currentUsername = $_SESSION['username'];

    if (isset($_POST['logout'])) {
        session_destroy();
        header("Location: Login.php");
        exit();
    }

$bookStatusQuery = "
    SELECT b.book_id, b.title, a.author_name, p.publisher_name, 
           IFNULL(bsi.stock_quantity, 0) AS stock_quantity, 
           b.book_image, 
           b.price  
    FROM books b
    LEFT JOIN authors a ON b.author_id = a.author_id
    LEFT JOIN publishers p ON b.publisher_id = p.publisher_id
    LEFT JOIN book_sales_inventory bsi ON b.book_id = bsi.book_id
";

    $bookStatusResult = $mysql->query($bookStatusQuery);

    $orderedBooksQuery = "
        SELECT 
            t.transaction_id, 
            b.book_id, 
            b.title, 
            a.author_name, 
            t.quantity_purchased, 
            b.book_image, 
            p.publisher_name, 
            b.price AS book_price
        FROM transactions_order t
        JOIN books b ON t.book_id = b.book_id
        JOIN authors a ON b.author_id = a.author_id
        JOIN publishers p ON b.publisher_id = p.publisher_id
        WHERE t.user_id = (SELECT user_id FROM users WHERE user_name = '" . $mysql->real_escape_string($currentUsername) . "')
    ";

    $orderedBooksResult = $mysql->query($orderedBooksQuery);

    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book Records</title>
    <link href="MainCss/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="SMain.css">
</head>
<style>

    h3{
            text-align: center;
            margin-bottom: 10px;
            font-family: 'Arial', sans-serif;
            font-size: 24px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 2px;
            background: linear-gradient(90deg, #f7f7f7, #d1d1d1);
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>
<body style="background-image: url('pics/Background_image.jpg');">
    <div id="mySidenav" class="sidenav" style = "overflow: hidden;">
        <div class="image">
            <img class="img" id="allbook" src="pics/book.png" style="width: 30px; height: 30px;">
            <label class="texts" id="availableBooksLabel" style = "color:transparent;">Available Books</label><br>
            <img class="img" id="orderedbooks" src="pics/borrow.png" style="width: 30px; height: 30px;">
            <label class="texts" id="orderedBooksLabel" style = "color:transparent;" >Ordered Books</label><br>
            <form method="POST">
                <button class="buttonlogout" id="logouttext" name="logout" style="margin-top: 280px;">
                    <img src="pics/logout.png" style="width: 30px; height: 30px;">
                    <label class="logouttext">Log out</label>
                </button>
            </form>
        </div>
    </div>

    <button type="button" class="img button" id="buttonnav" style="position: fixed; background-color: transparent; border: 0px;">
        <img src="menu.png" width="30" height="30" id="invert">
    </button>

    <div class="Container">
        <div id="availableBooksSection">
            <h3>-------------------- Available Books --------------------</h3>
            <table class="table">
                <thead>
                    <tr STYLE = "text-align: CENTER;">
                        <th>Book ID</th>
                        <th>Book Cover</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Publisher</th>
                        <th>Stock Quantity</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $user_name = $currentUsername;

                    if (!$bookStatusResult) {
                        echo "<tr><td colspan='6'>Error fetching data: " . $mysql->error . "</td></tr>";
                    } elseif ($bookStatusResult->num_rows == 0) {
                        echo "<tr><td colspan='6'>No books found.</td></tr>";
                    } else {
                        while ($row = $bookStatusResult->fetch_assoc()) {
                   $book_id = $row['book_id'];
                    $title = $row['title'];
                    $author = $row['author_name'];
                    $publisher = $row['publisher_name'];
                    $stock_quantity = $row['stock_quantity'];
                    $book_image = $row['book_image']; 
                    $price = $row['price'];

echo "
<tr STYLE = 'TEXT-ALIGN:CENTER;'>
        <td>$book_id</td>
        <td><img src='$book_image' alt='$title' style='width: 100px; height: 100px;'></td> <!-- Book Image -->
        <td>$title</td>
        <td>$author</td>
        <td>$publisher</td>
        <td>$stock_quantity</td>
         <td>" . number_format($price, 2) . "</td>
    <td>
        <button class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#orderModal$book_id'>
            Order
        </button>
    </td>
</tr>

<div class='modal fade' id='orderModal$book_id' tabindex='-1' aria-labelledby='orderModalLabel$book_id' aria-hidden='true' data-bs-backdrop='false' data-bs-keyboard='false'>
    <div class='modal-dialog modal-dialog-scrollable'> <!-- Ensures scrollable dialog -->
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='orderModalLabel$book_id'>Order Book</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <form method='POST'>
                <div class='modal-body' style='max-height: 400px; overflow-y: auto;'> <!-- Enable scrolling here -->
                    <!-- Book ID (Hidden) -->
                    <input type='hidden' name='book_id' value='$book_id' readonly>

                    <!-- User Name -->
                    <div class='mb-3'>
                        <label for='user_name_$book_id' class='form-label'>User Name</label>
                        <input type='text' class='form-control' name='user_name' id='user_name_$book_id' value='$user_name' readonly>
                    </div>

                    <!-- Book Title -->
                    <div class='mb-3'>
                        <label for='book_title_$book_id' class='form-label'>Book Title</label>
                        <input type='text' class='form-control' name='book_title' id='book_title_$book_id' value='$title' readonly>
                    </div>

                    <!-- Available Stock -->
                    <div class='mb-3'>
                        <label for='stock_quantity_$book_id' class='form-label'>Available Stock</label>
                        <input type='number' class='form-control' name='stock_quantity' id='stock_quantity_$book_id' value='$stock_quantity' readonly>
                    </div>

                    <!-- Order Quantity -->
                    <div class='mb-3'>
                        <label for='order_quantity_$book_id' class='form-label'>Order Quantity</label>
                        <input type='number' class='form-control' name='order_quantity' id='order_quantity_$book_id' min='1' max='$stock_quantity' required>
                    </div>

                <!-- Country -->
                <div class='mb-3'>
                    <label for='country_$book_id' class='form-label'>Country</label>
                    <input type='text' class='form-control' name='country' id='country_$book_id' required>
                </div>

                <!-- Province -->
                <div class='mb-3'>
                    <label for='province_$book_id' class='form-label'>Province</label>
                    <input type='text' class='form-control' name='province' id='province_$book_id' required>
                </div>

                <!-- Address -->
                <div class='mb-3'>
                    <label for='address_$book_id' class='form-label'>Address</label>
                    <input type='text' class='form-control' name='address' id='address_$book_id' required>
                </div>

                <!-- Contact Number -->
                <div class='mb-3'>
                    <label for='contactnumber_$book_id' class='form-label'>Contact Number</label>
                    <input type='text' class='form-control' name='contactnumber' id='contactnumber_$book_id' required>
                </div>

                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                    <button type='submit' class='btn btn-primary' name='submit_order'>Submit Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

";
            }
        }
    ?>
                </tbody>
            </table>
        </div>

<?php
if (isset($_POST['submit_order'])) {
    // Sanitize and fetch inputs
    $book_id = $_POST['book_id'];
    $order_quantity = $_POST['order_quantity'];
    $country = $_POST['country'];
    $province = $_POST['province'];
    $address = $_POST['address'];
    $contactnumber = $_POST['contactnumber'];

    // Fetch book details (price and stock quantity)
    $query = "SELECT stock_quantity, price FROM book_sales_inventory
              JOIN books ON book_sales_inventory.book_id = books.book_id
              WHERE books.book_id = $book_id";
    $result = $mysql->query($query);
    $book_data = $result->fetch_assoc();
    $current_stock = $book_data['stock_quantity'];
    $book_price = $book_data['price'];

    // Validate order quantity
    if ($order_quantity > $current_stock || $order_quantity < 1) {
        echo "<script>alert('Invalid order quantity. Please ensure it is within the available stock.');</script>";
    } else {
        $price_total = $book_price * $order_quantity;

        // Get user ID from the users table
        $user_name = $currentUsername; // Replace with dynamic value
        $user_id_query = "SELECT user_id FROM users WHERE user_name = '$user_name'";
        $user_result = $mysql->query($user_id_query);
        $user_id = $user_result->fetch_assoc()['user_id'];

        // Begin a transaction
        $mysql->begin_transaction();

        // Insert into transactions_order table
        $transaction_query = "
            INSERT INTO transactions_order (user_id, book_id, quantity_purchased, pricetotal)
            VALUES ($user_id, $book_id, $order_quantity, $price_total)
        ";

        if ($mysql->query($transaction_query)) {
            $transaction_id = $mysql->insert_id; // Get the last inserted transaction_id

            // Insert into transaction_details table
            $insert_details_query = "
                INSERT INTO transaction_details (transaction_id, country, province, address, contactnumber)
                VALUES ($transaction_id, '$country', '$province', '$address', '$contactnumber')
            ";

            if ($mysql->query($insert_details_query)) {
                // Update stock quantity in book_sales_inventory
                $new_stock = $current_stock - $order_quantity;
                $update_stock_query = "UPDATE book_sales_inventory SET stock_quantity = $new_stock WHERE book_id = $book_id";
                $mysql->query($update_stock_query);

                // Reorganize transaction order and transaction details IDs sequentially
                $mysql->query("SET @new_id = 0;");
                $mysql->query("UPDATE transactions_order SET transaction_id = (@new_id := @new_id + 1) ORDER BY transaction_id;");
                
                $mysql->query("SET @new_detail_id = 0;");
                $mysql->query("UPDATE transaction_details SET transaction_id = (@new_detail_id := @new_detail_id + 1) ORDER BY transaction_id;");

                // Commit transaction
                $mysql->commit();

                // Redirect or success message
                echo "<script>window.location.href = 'UserLogin.php';</script>";
                exit;
            } else {
                $mysql->rollback();
                echo "<script>alert('Error inserting into transaction details table: " . $mysql->error . "');</script>";
            }
        } else {
            $mysql->rollback();
            echo "<script>alert('Error placing order: " . $mysql->error . "');</script>";
        }
    }
}
?>


<div id="orderedBooksSection" style="display:none;">
    <h3>Your Ordered Books</h3>
    <table class="table">
        <thead>
            <tr STYLE = 'text-align: CENTER;'>
                <th>Book ID</th>
                <th>Book Cover</th>
                <th>Title</th>
                <th>Author</th>
                <th>Publisher</th>
                <th>Quantity Purchased</th>
                <th>Total Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!$orderedBooksResult) {
                echo "<tr><td colspan='8'>Error fetching data: " . $mysql->error . "</td></tr>";
            } elseif ($orderedBooksResult->num_rows == 0) {
                echo "<tr><td colspan='8'>No ordered books found.</td></tr>";
            } else {
                while ($row = $orderedBooksResult->fetch_assoc()) {
                    $totalPrice = $row['quantity_purchased'] * $row['book_price'];
                    // Fetch available stock
                    $available_stock_query = "SELECT stock_quantity FROM book_sales_inventory WHERE book_id = " . $row['book_id'];
                    $available_stock_result = $mysql->query($available_stock_query);
                    $available_stock = $available_stock_result->fetch_assoc()['stock_quantity'];
                    $max_quantity = $row['quantity_purchased'] + $available_stock;
                    echo "<tr STYLE = 'text-align: CENTER;'>
                            <td>" . $row['book_id'] . "</td>
                            <td><img src='" . $row['book_image'] . "' alt='Book Image' style='width:50px; height:auto;'></td>
                            <td>" . $row['title'] . "</td>
                            <td>" . $row['author_name'] . "</td>
                            <td>" . $row['publisher_name'] . "</td>
                            <td>" . $row['quantity_purchased'] . "</td>
                            <td>" . number_format($totalPrice, 2) . "</td>
                            <td>
                                <button class='btn btn-info' style = 'display:none' data-toggle='modal' data-target='#editModal_" . $row['transaction_id'] . "'>Edit</button>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='delete_order' value='1'>
                                <input type='hidden' name='transaction_id' value='" . $row['transaction_id'] . "'>
                                <button type='submit' class='btn btn-danger'>Cancel</button>
                            </form>
                            </td>
                          </tr>";
                    echo "<div class='modal fade' id='editModal_" . $row['transaction_id'] . "' tabindex='-1' role='dialog' aria-labelledby='editModalLabel' aria-hidden='true'>
                            <div class='modal-dialog' role='document'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h5 class='modal-title' id='editModalLabel'>Edit Quantity</h5>
                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                            <span aria-hidden='true'>&times;</span>
                                        </button>
                                    </div>
                                    <div class='modal-body'>
                                        <form method='POST'>
                                            <input type='hidden' name='edit_transaction_id' value='" . $row['transaction_id'] . "'>
                                            <input type='hidden' name='book_id' value='" . $row['book_id'] . "'>
                                            <input type='hidden' name='current_quantity' value='" . $row['quantity_purchased'] . "'>
                                            <label for='new_quantity'>Edit Quantity (Max: $max_quantity)</label>
                                            <input type='number' name='new_quantity' class='form-control' min='0' max='" . $max_quantity . "' value='" . $row['quantity_purchased'] . "' required>
                                            <button type='submit' class='btn btn-primary mt-2'>Update Quantity</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                          </div>";
                }
            }
            ?>
        </tbody>
    </table>
</div>

<?php
// Handling the Edit and Delete functionality
if (isset($_POST['edit_transaction_id'])) {
    $transactionId = $_POST['edit_transaction_id'];
    $bookId = $_POST['book_id'];
    $newQuantity = (int)$_POST['new_quantity'];
    $currentQuantity = (int)$_POST['current_quantity'];

    // Fetch current stock
    $query = "SELECT stock_quantity FROM book_sales_inventory WHERE book_id = $bookId";
    $result = $mysql->query($query);
    $currentStock = $result->fetch_assoc()['stock_quantity'];

    // Calculate updated stock
    $updatedStock = $currentStock + $currentQuantity - $newQuantity;

    // Update transaction and stock
    $updateTransaction = "UPDATE transactions_order SET quantity_purchased = $newQuantity WHERE transaction_id = $transactionId";
    $updateStock = "UPDATE book_sales_inventory SET stock_quantity = $updatedStock WHERE book_id = $bookId";

        echo "<script>window.location.href = 'UserLogin.php';</script>";
}

if (isset($_POST['delete_order'])) {
    $transaction_id = (int)$_POST['transaction_id']; 

    // First, fetch the quantity purchased from the transactions_order table
    $quantity_query = "SELECT quantity_purchased, book_id FROM transactions_order WHERE transaction_id = ?";
    $stmt_quantity = $mysql->prepare($quantity_query);
    $stmt_quantity->bind_param("i", $transaction_id);
    $stmt_quantity->execute();
    $stmt_quantity->bind_result($quantity_purchased, $book_id);
    $stmt_quantity->fetch();
    $stmt_quantity->close();

    // Delete from the details table
    $delete_details_query = "DELETE FROM transaction_details WHERE transaction_id = ?";
    $stmt_details = $mysql->prepare($delete_details_query);
    $stmt_details->bind_param("i", $transaction_id);
    $stmt_details->execute();

    // Delete from the transactions_order table
    $delete_order_query = "DELETE FROM transactions_order WHERE transaction_id = ?";
    $stmt_order = $mysql->prepare($delete_order_query);
    $stmt_order->bind_param("i", $transaction_id);
    $stmt_order->execute();

    // If both deletions were successful, update the stock
    if ($stmt_details->affected_rows > 0 && $stmt_order->affected_rows > 0) {
        // Update the stock in book_sales_inventory
        $update_stock_query = "UPDATE book_sales_inventory SET stock_quantity = stock_quantity + ? WHERE book_id = ?";
        $stmt_stock = $mysql->prepare($update_stock_query);
        $stmt_stock->bind_param("ii", $quantity_purchased, $book_id);
        $stmt_stock->execute();
        $stmt_stock->close();

        // Reorder transaction IDs
        $reorder_transactions_query = "SET @count = 0;";
        $mysql->query($reorder_transactions_query);

        $reorder_transactions_update_query = "UPDATE transactions_order SET transaction_id = (@count := @count + 1) ORDER BY transaction_id";
        $mysql->query($reorder_transactions_update_query);

        // Reorder transaction details IDs
        $reorder_details_query = "SET @count = 0;";
        $mysql->query($reorder_details_query);

        $reorder_details_update_query = "UPDATE transaction_details SET transaction_id = (@count := @count + 1) ORDER BY transaction_id";
        $mysql->query($reorder_details_update_query);

        // Reset the auto-increment values to the next available ID after reordering
        $reset_auto_increment_transactions = "ALTER TABLE transactions_order AUTO_INCREMENT = 1";
        $reset_auto_increment_details = "ALTER TABLE transaction_details AUTO_INCREMENT = 1";
        $mysql->query($reset_auto_increment_transactions);
        $mysql->query($reset_auto_increment_details);

        // Commit the transaction
        $mysql->commit();

        // Refresh the page
        header("Refresh:0");        
        exit; 
    }

}
ob_end_flush();
?>






    <script src="bootstrap.min.js"></script>
    <script src="bootstrap.bundle.min.js"></script>
    <script>
        var availableBooksLabel = document.getElementById('availableBooksLabel');
        var orderedBooksLabel = document.getElementById('orderedBooksLabel');
        var logoutButton = document.getElementById('logouttext');
        var image = document.getElementById('invert'); 
        var array_class = [];
        if (availableBooksLabel) array_class.push(availableBooksLabel);
        if (orderedBooksLabel) array_class.push(orderedBooksLabel);
        if (logoutButton) array_class.push(logoutButton);
        if (image) image.style.filter = 'invert(100%)';

        // Open navigation
        function openNav() {
            document.getElementById("mySidenav").style.width = "250px";
            document.querySelector(".Container").style.marginLeft = "200px";
            array_class.forEach((element) => {
                element.style.color = "white";  
                element.style.animation = "1s"; 
            });
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "85px";
            document.querySelector(".Container").style.marginLeft = "10px";
            array_class.forEach((element) => {
                element.style.color = "transparent"; 
                element.style.animation = "0.4s"; 
            });
        }
            var opened = false;
            document.getElementById("buttonnav").addEventListener("click", () => {
            if (opened == false) {
                openNav();
                opened = true;
            } else {
                opened = false;
                closeNav();
            }
        });

        document.getElementById('allbook').onclick = ()=>{
            document.getElementById("availableBooksSection").style.display = "block";
                document.getElementById("orderedBooksSection").style.display = "none";
        }
        document.getElementById('orderedbooks').onclick = ()=>{
            document.getElementById("availableBooksSection").style.display = "none";
                document.getElementById("orderedBooksSection").style.display = "block";
        }
        function handleTabSwitch(tabText) {
            if (tabText === "available books") {
                document.getElementById("availableBooksSection").style.display = "block";
                document.getElementById("orderedBooksSection").style.display = "none";
            } else if (tabText === "ordered books") {
                document.getElementById("availableBooksSection").style.display = "none";
                document.getElementById("orderedBooksSection").style.display = "block";
            }
        }

        document.querySelectorAll('.texts').forEach((element) => {
            element.addEventListener('click', () => {
                const tabText = element.innerHTML.trim().toLowerCase(); 
                handleTabSwitch(tabText);
            });
        });
    </script>
</body>
</html>
