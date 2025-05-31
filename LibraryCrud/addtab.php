<?php
    // Connect to database
    $mysql = new mysqli('localhost', 'root', '', 'booksdb');
    $query = "SELECT books.*, authors.author_name, publishers.publisher_name, book_sales_inventory.stock_quantity, authors.birth_date
              FROM books
              JOIN authors ON books.author_id = authors.author_id
              JOIN publishers ON books.publisher_id = publishers.publisher_id
              JOIN book_sales_inventory ON books.book_id = book_sales_inventory.book_id";

    // Execute query
    $doquery = $mysql->query($query);
    while ($row = $doquery->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['book_id'] . '</td>';
        echo '<td><img src="' . $row['book_image'] . '" width="50" alt="Book Image"></td>';
        echo '<td>' . htmlspecialchars($row['title']) . '</td>';
        echo '<td>' . htmlspecialchars($row['published_date']) . '</td>';
        echo '<td>' . htmlspecialchars($row['author_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['publisher_name']) . '</td>';
        echo '<td>' . htmlspecialchars(ucfirst($row['genre'])) . '</td>';
        echo '<td>' . htmlspecialchars($row['price']) . '</td>';
        echo '<td>' . htmlspecialchars($row['stock_quantity']) . '</td>';
        echo '<td>';

            echo '<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editModal' . $row['book_id'] . '" style="padding: 10px">Edit</button>';

            echo '<button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal' . $row['book_id'] . '" style="margin-left: 20px; padding: 10px">Delete</button>';
        echo '</td>';
        echo '</tr>';


        echo "
        <div class='modal fade' id='editModal" . $row['book_id'] . "' data-bs-backdrop='false' tabindex='-1' aria-labelledby='editModalLabel" . $row['book_id'] . "' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content' style='max-height: 80vh; overflow:auto;'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='editModalLabel" . $row['book_id'] . "'>Edit Book</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body' style='overflow:auto;'>
                        <form method='POST' enctype='multipart/form-data'>
                            <input type='hidden' name='editBookId' value='" . $row['book_id'] . "'>
                            <div class='mb-3'>
                                <label for='editTitle" . $row['book_id'] . "' class='form-label'>Title</label>
                                <input type='text' class='form-control' id='editTitle" . $row['book_id'] . "' name='title' value='" . $row['title'] . "' required>
                            </div>
                            <div class='mb-3'>
                                <label for='editPublishDate" . $row['book_id'] . "' class='form-label'>Publish Date</label>
                                <input type='date' class='form-control' id='editPublishDate" . $row['book_id'] . "' name='publish_date' value='" . $row['published_date'] . "' required>
                            </div>
                            <div class='mb-3'>
                                <label for='editAuthor" . $row['book_id'] . "' class='form-label'>Author</label>
                                <input type='text' class='form-control' id='editAuthor" . $row['book_id'] . "' name='author' value='" . $row['author_name'] . "' required>
                            </div>
                            <div class='mb-3'>
                                <label for='editAuthorBirthday" . $row['book_id'] . "' class='form-label'>Author Birthday</label>
                                <input type='date' class='form-control' id='editAuthorBirthday" . $row['book_id'] . "' name='author_birthday' value='" . (isset($row['birth_date']) ? $row['birth_date'] : '') . "' required>
                            </div>

                            <div class='mb-3'>
                                <label for='editPublisher" . $row['book_id'] . "' class='form-label'>Publisher</label>
                                <input type='text' class='form-control' id='editPublisher" . $row['book_id'] . "' name='publisher' value='" . $row['publisher_name'] . "' required>
                            </div>

                            <div class='mb-3'> <label for='editGenre" . $row['book_id'] . "' class='form-label'>Genre</label> <select class='form-control' id='editGenre" . $row['book_id'] . "' name='genre' required> <option value='comedy'" . ($row['genre'] === 'comedy' ? ' selected' : '') . ">Comedy</option> <option value='drama'" . ($row['genre'] === 'drama' ? ' selected' : '') . ">Drama</option> <option value='romance'" . ($row['genre'] === 'romance' ? ' selected' : '') . ">Romance</option> <option value='scifi'" . ($row['genre'] === 'scifi' ? ' selected' : '') . ">Sci-Fi</option> <option value='horror'" . ($row['genre'] === 'horror' ? ' selected' : '') . ">Horror</option> <option value='fantasy'" . ($row['genre'] === 'fantasy' ? ' selected' : '') . ">Fantasy</option> <option value='thriller'" . ($row['genre'] === 'thriller' ? ' selected' : '') . ">Thriller</option> <option value='mystery'" . ($row['genre'] === 'mystery' ? ' selected' : '') . ">Mystery</option> <option value='nonfiction'" . ($row['genre'] === 'nonfiction' ? ' selected' : '') . ">Non-Fiction</option> <option value='historical'" . ($row['genre'] === 'historical' ? ' selected' : '') . ">Historical</option> <option value='adventure'" . ($row['genre'] === 'adventure' ? ' selected' : '') . ">Adventure</option> <option value='biography'" . ($row['genre'] === 'biography' ? ' selected' : '') . ">Biography</option> <option value='youngadult'" . ($row['genre'] === 'youngadult' ? ' selected' : '') . ">Young Adult</option> </select>
                            </div>

                            <div class='mb-3'>
                                <label for='editPrice" . $row['book_id'] . "' class='form-label'>Price</label>
                                <input type='text' class='form-control' id='editPrice" . $row['book_id'] . "' name='price' value='" . $row['price'] . "' required>
                            </div>

                            <div class='mb-3'>
                            <label for='editStock" . $row['book_id'] . "' class='form-label'>Stock Quantity</label>
                            <input type='number' class='form-control' min='0' id='editStock" . $row['book_id'] . "' name='stock' value='" . htmlspecialchars($row['stock_quantity'], ENT_QUOTES) . "' required>
                            </div>

                            <div class='mb-3'>
                                <label for='editBookImage" . $row['book_id'] . "' class='form-label'>Book Image</label>
                                <input type='file' class='form-control' id='editBookImage" . $row['book_id'] . "' name='book_image' accept='image/*''>
                                <br>
                                <small>Current Image: " . (isset($row['book_image']) ? "<img src='" . $row['book_image'] . "' width='100'>" : "No image") . "</small>
                            </div>
                            <button type='submit' name='editBook' class='btn btn-primary'>Save changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>";

        echo "
        <div class='modal fade' id='deleteModal" . $row['book_id'] . "' data-bs-backdrop='false' tabindex='-1' aria-labelledby='deleteModalLabel" . $row['book_id'] . "' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content' style='overflow:auto;'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='deleteModalLabel" . $row['book_id'] . "'>Delete Book</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                        Are you sure you want to delete <strong>" . htmlspecialchars($row['title']) . "</strong>?
                    </div>
                    <div class='modal-footer'>
                        <form method='POST'>
                            <input type='hidden' name='deleteBookId' value='" . $row['book_id'] . "'>
                            <button type='submit' name='deleteBook' class='btn btn-danger'>Delete</button>
                        </form>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                    </div>
                </div>
            </div>
        </div>";
    }
?>
