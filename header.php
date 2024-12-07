<!-- header.php -->
<header class="header">
    <div class="header-container">
        <!-- Logo -->
        <h1 class="logo">Soundscape</h1>
        
        <!-- Link naar index -->
        <a href="index.php" class="products-link">Al mijn producten</a>


        <a href="index.php?action=logout" onclick="return confirm('Weet je zeker dat je wilt uitloggen?');">Uitloggen</a>

        
        <!-- Navigatie pictogrammen -->
        <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search..." onkeyup="searchProduct()">
                <button type="submit">
                    <img src="icons/search.svg" alt="Search" class="icon">
                </button>
                <div id="searchResults" class="search-results"></div>
            </div>


            <!-- Shop icoon -->
             <a href="cart.php">

                 <img class="icon shop" src="icons/shop.svg" alt="Shop">
                </a>
            
            <!-- Person icoon -->
             <a href="person.php">

                 <img class="icon person" src="icons/person.svg" alt="Person">
                </a>
        </div>
    </div>
</header>
