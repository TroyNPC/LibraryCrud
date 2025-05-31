        var image = document.getElementById('invert'); 
        image.style.filter = 'invert(100%)';
        var logour = document.getElementById('logouttext')
        let array_class = [];
        document.querySelectorAll('#texts').forEach(item => {
            array_class.push(item);
        });

        function openNav() {
            document.getElementById("mySidenav").style.width = "250px";
            document.querySelector(".Container").style.marginLeft = "200px";
            array_class.forEach((element) => {
                element.style.color = "white";
                element.style.animation = "1s";
                logouttext.style.color = "white";
            });
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "85px";
            document.querySelector(".Container").style.marginLeft = "10px";
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