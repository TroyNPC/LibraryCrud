<?php
class BookCrud {
    private $mysql;

    public function __construct() {
        $this->mysql = new mysqli('localhost', 'root', '', 'booksdb');
        if ($this->mysql->connect_error) {
            die("Connection failed: " . $this->mysql->connect_error);
        }
    }

public function add_book() {
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addbookBUTTON'])) {
        $title = $_POST['title'];
        $publish_date = $_POST['publish_date'];
        $author = $_POST['author'];
        $author_birthday = $_POST['author_birthday'];
        $publisher = $_POST['publisher'];
        $publisher_contact = $_POST['publisher_contact'];
        $genre = $_POST['genre'];
        $price = $_POST['price'];
        $stocks = $_POST['stocks'];  

        // Temporarily disable foreign key checks
        $this->mysql->query("SET foreign_key_checks = 0;");


        $author_id = $this->get_next_id('authors', 'author_id');
        $query_author = "INSERT IGNORE INTO authors (author_id, author_name, birth_date) 
                         VALUES ('$author_id', '$author', '$author_birthday')";
        $this->mysql->query($query_author);

        // Directly get or insert publisher
        $publisher_id = $this->get_next_id('publishers', 'publisher_id');
        $query_publisher = "INSERT IGNORE INTO publishers (publisher_id, publisher_name, contact_number) 
                            VALUES ('$publisher_id', '$publisher', '$publisher_contact')";
        $this->mysql->query($query_publisher);


        $book_id = $this->get_next_id('books', 'book_id');
        // Directly get or insert author
        $book_image = null;
        if (isset($_FILES['book_image']) && $_FILES['book_image']['error'] == 0) {
            $image_name = $_FILES['book_image']['name'];
            $book_image = 'Bookpics/' . $image_name; 
        }

        $query_book = "INSERT INTO books (book_id, title, published_date, author_id, publisher_id, genre, price, book_image) 
                       VALUES ('$book_id', '$title', '$publish_date', '$author_id', '$publisher_id', '$genre', '$price', '$book_image')";
        $this->mysql->query($query_book);

        // Insert into inventory
        $query_inventory = "INSERT INTO book_sales_inventory (book_id, stock_quantity) 
                            VALUES ('$book_id', '$stocks')";
        $this->mysql->query($query_inventory);

        
    }
}


public function edit_book() {
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['editBook'])) {
        $id = $_POST['editBookId'];
        $title = $_POST['title'];
        $publish_date = $_POST['publish_date'];
        $author = $_POST['author']; 
        $publisher = $_POST['publisher']; 
        $genre = $_POST['genre'];
        $birth_date = $_POST['author_birthday'];
        $book_image = $_FILES['book_image']['name'] ?? null;
        $price = $_POST['price'];
        $new_stock = $_POST['stock'];

        $this->mysql->query("SET foreign_key_checks = 1;");

        // Update book details
        $query = "UPDATE books SET title = ?, published_date = ?, genre = ? WHERE book_id = ?";
        $stmt = $this->mysql->prepare($query);
        $stmt->bind_param('sssi', $title, $publish_date, $genre, $id);
        $stmt->execute();

        // Handle image upload
        if ($book_image) {
            $image_name = $_FILES['book_image']['name'];
            $image_tmp_name = $_FILES['book_image']['tmp_name'];
            $book_image_path = 'Bookpics/' . $image_name;

            if (move_uploaded_file($image_tmp_name, $book_image_path)) {
                $query_update_image = "UPDATE books SET book_image = ? WHERE book_id = ?";
                $stmt_update_image = $this->mysql->prepare($query_update_image);
                $stmt_update_image->bind_param('si', $book_image_path, $id);
                $stmt_update_image->execute();
            } else {
                echo "Failed to upload image.";
            }
        }

        // Update stock quantity
        $query_check_orders = "SELECT SUM(quantity_purchased) AS total_ordered FROM transactions_order WHERE book_id = ?";
        $stmt_check_orders = $this->mysql->prepare($query_check_orders);
        $stmt_check_orders->bind_param('i', $id);
        $stmt_check_orders->execute();
        $result = $stmt_check_orders->get_result();
        $row = $result->fetch_assoc();
        $total_ordered = $row['total_ordered'] ?? 0;

        if ($total_ordered > 0 && $new_stock < $total_ordered) {
            $new_stock = $total_ordered;
        } elseif ($new_stock < 0) {
            $new_stock = 0;
        }

        $query_update_stock = "UPDATE book_sales_inventory SET stock_quantity = ? WHERE book_id = ?";
        $stmt_update_stock = $this->mysql->prepare($query_update_stock);
        $stmt_update_stock->bind_param('ii', $new_stock, $id);
        $stmt_update_stock->execute();

        // Update author details
        if (!empty($author)) {
            $query_update_author = "UPDATE authors SET author_name = ?, birth_date = ? 
                                    WHERE author_id = (SELECT author_id FROM books WHERE book_id = ?)";
            $stmt_update_author = $this->mysql->prepare($query_update_author);
            $stmt_update_author->bind_param('ssi', $author, $birth_date, $id);
            $stmt_update_author->execute();
        }

        // Update publisher details
        if (!empty($publisher)) {
            $query_update_publisher = "UPDATE publishers SET publisher_name = ? 
                                       WHERE publisher_id = (SELECT publisher_id FROM books WHERE book_id = ?)";
            $stmt_update_publisher = $this->mysql->prepare($query_update_publisher);
            $stmt_update_publisher->bind_param('si', $publisher, $id);
            $stmt_update_publisher->execute();
        }

        // Update price if provided
        if (!empty($price)) {
            $query_update_price = "UPDATE books SET price = ? WHERE book_id = ?";
            $stmt_update_price = $this->mysql->prepare($query_update_price);
            $stmt_update_price->bind_param('di', $price, $id);
            $stmt_update_price->execute();
        }

        $this->mysql->query("SET foreign_key_checks = 1;");
    }
}




// Function to delete the book and reorganize IDs
public function delete_book() {
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['deleteBook'])) {
        $bookId = $_POST['deleteBookId'];
        
        $this->mysql->query("DELETE FROM books WHERE book_id = $bookId");

            $this->reorganize_ids('book_sales_inventory', 'book_id');
            $this->reorganize_ids('authors', 'author_id');
            $this->reorganize_ids('publishers', 'publisher_id');
            $this->reorganize_ids('books', 'book_id');
    }
}
public function reorganize_ids($table, $column) {
    // Disable foreign key checks temporarily
    $this->mysql->query("SET foreign_key_checks = 0;");

    // Define default values for minimum IDs
    $min_author_id = 1; // Default start value
    $min_publisher_id = 1; // Default start value

    // Get the minimum ID values for authors and publishers
    if ($table == 'authors') {
        $result = $this->mysql->query("SELECT MIN(author_id) AS min_author_id FROM authors");
        if ($row = $result->fetch_assoc()) {
            $min_author_id = $row['min_author_id'];
        }

        // Reorganize author_id sequentially starting from the minimum author_id
        $result = $this->mysql->query("SELECT author_id FROM authors ORDER BY author_id");
        $index = 1; // Reset index for sequential counting
        while ($row = $result->fetch_assoc()) {
            $this->mysql->query("UPDATE authors SET author_id = $index WHERE author_id = " . $row['author_id']);
            $index++;
        }
    }

    if ($table == 'publishers') {
        $result = $this->mysql->query("SELECT MIN(publisher_id) AS min_publisher_id FROM publishers");
        if ($row = $result->fetch_assoc()) {
            $min_publisher_id = $row['min_publisher_id'];
        }

        // Reorganize publisher_id sequentially starting from the minimum publisher_id
        $result = $this->mysql->query("SELECT publisher_id FROM publishers ORDER BY publisher_id");
        $index = 1; // Reset index for sequential counting
        while ($row = $result->fetch_assoc()) {
            $this->mysql->query("UPDATE publishers SET publisher_id = $index WHERE publisher_id = " . $row['publisher_id']);
            $index++;
        }
    }

    // Reorganize the books table
    if ($table == 'books') {
        // Update author_id inside books table
        $result = $this->mysql->query("SELECT book_id, author_id FROM books ORDER BY book_id");
        $index = $min_author_id; // Start from the minimum author_id
        while ($row = $result->fetch_assoc()) {
            $this->mysql->query("UPDATE books SET author_id = $index WHERE book_id = " . $row['book_id']);
            $index++;
        }

        // Update publisher_id inside books table
        $result = $this->mysql->query("SELECT book_id, publisher_id FROM books ORDER BY book_id");
        $index = $min_publisher_id; // Start from the minimum publisher_id
        while ($row = $result->fetch_assoc()) {
            $this->mysql->query("UPDATE books SET publisher_id = $index WHERE book_id = " . $row['book_id']);
            $index++;
        }

        // Reorganize book_id in books table sequentially
        $result = $this->mysql->query("SELECT book_id FROM books ORDER BY book_id");
        $index = 1; // Reset index for sequential counting
        while ($row = $result->fetch_assoc()) {
            $this->mysql->query("UPDATE books SET book_id = $index WHERE book_id = " . $row['book_id']);
            $index++;
        }
    }

    // Reorganize book_id in book_inventory table sequentially
    if ($table == 'book_sales_inventory') {
        $result = $this->mysql->query("SELECT book_id FROM book_sales_inventory ORDER BY book_id");
        $index = 1; // Reset index for sequential counting
        while ($row = $result->fetch_assoc()) {
            $this->mysql->query("UPDATE book_sales_inventory SET book_id = $index WHERE book_id = " . $row['book_id']);
            $index++;
        }
    }

    // Re-enable foreign key checks after the operations
    $this->mysql->query("SET foreign_key_checks = 1;");
}


    private function get_next_id($table, $column) {
        $query_get_max = "SELECT MAX($column) AS max_id FROM $table";
        $result = $this->mysql->query($query_get_max);
        $row = $result->fetch_assoc();

        return ($row['max_id'] ?? 0) + 1;
    }

    public function __destruct() {
        $this->mysql->close();
    }
}