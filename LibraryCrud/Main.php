<?php ob_start(); session_start(); include 'Functions/MainCrud.php'; include 'Functions/Deletenonused.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book Records</title>
    <link href="MainCss/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" href="Smain.css">
    <style>
        h2{
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

</head>
<body style="background-image: url('pics/Background_image.jpg');">
    <div id="mySidenav" class="sidenav" style = "overflow: hidden;">
        <div class="image">
            <img  class="img" src="pics/file.png" style="width: 30px; height: 30px;">
            <label class="texts" id="texts">Add Books</label><br>
            <img class="img" id="allbooks" src="pics/book.png" style="width: 30px; height: 30px;">
            <label class="texts" id="texts">Books</label><br>
            <img class="img" src="pics/borrow.png" style="width: 30px; height: 30px;">
            <label class="texts" id="texts">Transaction</label><br>
            <form method="POST">
            <button class="buttonlogout" id="logouttext" name="logout" style="margin-top: 225px;"><img src="pics/logout.png" style="width: 30px; height: 30px;"><label class="logouttext">Log out</label></button>
            </button>
        </form>
        </div>
    </div>
    <button type="button" class="img button" id="buttonnav" style="position: fixed; width: 10px; height: 40px; background-color: transparent; border: 0px">
        <img src="menu.png" width="30" height="30" id="invert">
    </button>
    <div class="Container" style="position: relative; width: 200vh; margin-left: 35px;">
        <div class="tab" id="booksTab" style="display: block;">
            <h2 class = "readsection">-------------------- Read Book Section --------------------</h2>
            <table class="table table-bordered table-custom">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Image</th>
                        <th scope="col">Title</th>
                        <th scope="col">Publish Date</th>
                        <th scope="col">Author</th>
                        <th scope="col">Publisher</th>
                        <th scope="col">Genre</th>
                        <th scope="col">Price</th>
                        <th scope="col">Current Stocks</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
<?php
    include 'addtab.php';
?>
                </tbody>
            </table>
        </div>
<div class="tab" id="addbookTab" style="display: none;">
        <h2 style="text-align: center;">-------------------- Add Book Section --------------------</h2>
    <div style="display:flex">
    <form method="POST" enctype="multipart/form-data">
</div>
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" placeholder="Enter Book Title" style="margin-bottom: 15px; width: 100%; padding: 10px;" required><br>

 
        <label for="publishDate">Publish Date:</label><br>
        <input type="date" id="publishDate" name="publish_date" style="margin-bottom: 15px; width: 100%; padding: 10px;" required><br>


        <label for="author">Author:</label><br>
        <input type="text" id="author" name="author" placeholder="Enter Author Name" style="margin-bottom: 15px; width: 100%; padding: 10px;" required><br>


        <label for="authorBirthday">Author Birthday:</label><br>
        <input type="date" id="authorBirthday" name="author_birthday" style="margin-bottom: 15px; width: 100%; padding: 10px;" required><br>


        <label for="publisher">Publisher:</label><br>
        <input type="text" id="publisher" name="publisher" placeholder="Enter Publisher Name" style="margin-bottom: 15px; width: 100%; padding: 10px;" required><br>


        <label for="publisherContact">Publisher Contact Number:</label><br>
        <input type="tel" id="publisherContact" name="publisher_contact" placeholder="Enter Publisher Contact Number" style="margin-bottom: 15px; width: 100%; padding: 10px;" required><br>

        <label for="genre">Genre:</label><br>
        <select id="genre" name="genre" style="margin-bottom: 15px; width: 100%; padding: 10px;" required>
            <option value="" disabled selected>Select Genre</option>
            <option value="comedy">Comedy</option>
            <option value="drama">Drama</option>
            <option value="romance">Romance</option>
            <option value="scifi">Sci-Fi</option>
            <option value="horror">Horror</option>
            <option value="fantasy">Fantasy</option>
            <option value="thriller">Thriller</option>
            <option value="mystery">Mystery</option>
            <option value="nonfiction">Non-Fiction</option>
            <option value="historical">Historical</option>
            <option value="adventure">Adventure</option>
            <option value="biography">Biography</option>
            <option value="youngadult">Young Adult</option>
        </select><br>

        <label for="price">Price:</label><br>
        <input type="number" id="price" name="price" placeholder="Enter Price" min = "1" style="margin-bottom: 15px; width: 100%; padding: 10px;" step = '.00001' required><br>

        <label for="stocks">Stocks:</label><br>
        <input type="number" id="stocks" name="stocks" placeholder="Enter stocks" min = "1" style="margin-bottom: 15px; width: 100%; padding: 10px;" required><br>


        <label for="bookImage">Book Image:</label><br>
        <input type="file" id="bookImage" name="book_image" accept="image/*" style="margin-bottom: 15px; width: 100%; padding: 5px;" required><br>
            <div id="bookImagePreview">
            <img id="previewImage" name="book_image" src="" alt="Book Image">
        </div>
        <br><br>

        <button type="submit" name="addbookBUTTON" style="position:relative; background-color: #4CAF50; color: white; padding: 10px 20px; border: none; margin-top: 30px; border-radius: 5px; cursor: pointer;">Add Book</button>
    </form>

    <script>
             document.getElementById('bookImage').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const previewImage = document.getElementById('previewImage');
                previewImage.src = e.target.result;
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        });
    </script>
<script>
    document.getElementById("imageUpload").addEventListener("change", function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("previewImage").src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
</div>

<div class="tab" id="transactionTab" style="display: none;">
    <h2>-------------------- Transaction Order Section --------------------</h2>
     <table class="table table-bordered table-custom">
        <thead>
            <tr>
            <th>Transaction ID</th>
            <th>User Name</th>
            <th>Country</th>
            <th>Province</th>
            <th>Address</th>
            <th>Contact Number</th>
            <th>Book Title</th>
            <th>Quantity Purchased</th>
            <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include 'Status.php';
            ?>
        </tbody>
    </table>
</div>
    </div>
    
    <?php include "Functions/Buttonclicked.php"?>
    
    <script src="bootstrap.min.js"></script>
    <script src="bootstrap.bundle.min.js"></script>
</body>


<script>
            var image = document.getElementById('invert'); 
        image.style.filter = 'invert(100%)';
        var logour = document.getElementById('logouttext')
        let array_class = [];
        document.querySelectorAll('#texts').forEach(item => {
            array_class.push(item);
        });

        function openNav() {
            document.getElementById("mySidenav").style.width = "250px";
            array_class.forEach((element) => {
                element.style.color = "white";
                element.style.animation = "1s";
                logouttext.style.color = "white";
            });
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "85px";
            array_class.forEach((element) => {
                element.style.color = "transparent";
                element.style.animation = "0.4s";
                logouttext.style.color = "transparent";
            });
        }

        let opened = false;
        document.getElementById("buttonnav").addEventListener("click", () => {
            if (opened == false) {
                openNav();
                opened = true;
            } else {
                opened = false;
                closeNav();
            }
        });

            document.querySelectorAll('.texts').forEach((element) => {
                element.addEventListener('click', () => {
                    const tabText = element.innerHTML.trim().toLowerCase(); 
                    handleTabSwitch(tabText);
                });
            });
            document.querySelectorAll('.img').forEach((element) => {
                element.addEventListener('click', () => {
                    const tabText = element.nextElementSibling.innerHTML.trim().toLowerCase(); 
                    handleTabSwitch(tabText);
                });
            });

            function handleTabSwitch(tabText) {
                let tabId = '';
                if (tabText === 'add books') {
                    tabId = 'addbookTab';
                } else if (tabText === 'books') {
                    tabId = 'booksTab';
                } else if (tabText === 'transaction') {
                    tabId = 'transactionTab';
                } else if (tabText === 'publishers') {
                    tabId = 'publishersTab';
                } else if (tabText === 'authors') {
                    tabId = 'authorsTab';
                } else {
                    console.error(`No tab found for text: ${tabText}`);
                    return; 
                }

                document.querySelectorAll('.tab').forEach((tab) => {
                    tab.style.display = 'none';
                });
                const tabElement = document.getElementById(tabId);
                if (tabElement) {
                    tabElement.style.display = 'block';
                } else {
                    console.error(`Tab with ID "${tabId}" not found.`);
                }
            }
            var myModal = new bootstrap.Modal(document.getElementById('editModal' + bookId));
            myModal.show();

                document.getElementById('bookImage').addEventListener('change', function(event) {
                const file = event.target.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const previewImage = document.getElementById('previewImage');
                    previewImage.src = e.target.result;
                };

                if (file) {
                    reader.readAsDataURL(file);
                }
        });
</script>

</html>
