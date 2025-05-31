        var image = document.getElementById('invert'); 
        image.style.filter = 'invert(100%)';
        var logour = document.getElementById('logouttext')
        let array_class = [];
        var availableBooksLabels = document.getElementById('availableBooksLabel');
        var borrowedBooksLabels = document.getElementById('borrowedBooksLabel');
        console.log(document.getElementById('borrowedBooksLabel'));
        function openNav() {
            document.getElementById("mySidenav").style.width = "250px";
            document.querySelector(".Container").style.marginLeft = "200px";
            array_class.forEach((element) => {
                element.style.color = "white";
                element.style.animation = "1s";
                logouttext.style.color = "white";
                availableBooksLabel.style.color = "white";
                borrowedBooksLabel.style.color = "white";
            });
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "85px";
            document.querySelector(".Container").style.marginLeft = "10px";
            array_class.forEach((element) => {
                element.style.color = "transparent";
                element.style.animation = "0.4s";
                logouttext.style.color = "transparent";
                availableBooksLabel.style.color = "transparent";
                borrowedBooksLabel.style.color = "transparent";
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
