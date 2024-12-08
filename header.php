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
</header>

<script>
    function searchProduct() {
        var query = document.getElementById('searchInput').value;

        if (query.length >= 1) {  // Start pas na 3 karakters
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'search.php?search=' + query, true); // zoekresultaten via GET naar search.php sturen
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var results = JSON.parse(xhr.responseText);
                    displaySearchResults(results);
                }
            };
            xhr.send();
        } else {
            document.getElementById('searchResults').innerHTML = '';  // Verberg zoekresultaten als de zoekterm te kort is
        }
    }

    function displaySearchResults(results) {
        var searchResultsContainer = document.getElementById('searchResults');
        searchResultsContainer.innerHTML = ''; // Maak de container leeg voordat we nieuwe resultaten tonen

        if (results.length > 0) {
            results.forEach(function(product) {
                var productDiv = document.createElement('div');
                productDiv.classList.add('search-result-item');
                productDiv.innerHTML = `
                    <a href="detail.php?id=${product.id}">
                        <h4>${product.title}</h4>
                        <p>${product.description}</p>
                        <p>€${product.price}</p>
                    </a>
                `;
                searchResultsContainer.appendChild(productDiv);
            });
        } else {
            searchResultsContainer.innerHTML = 'Geen resultaten gevonden.';
        }
    }
</script>


<style>
    .search-results {
    position: absolute;
    background-color: white; /* Zorg voor contrast met je zwarte achtergrond */
    
    border-radius: 5px;
    max-width: 300px;
    width: 100%;
    max-height: 350px;
    overflow-y: auto;
    z-index: 1000;
    margin-top: 380px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
}

.search-result-item {
    padding: 10px;
    border-bottom: 1px solid #f1f1f1;
    transition: background-color 0.3s ease;
    cursor: pointer;
}

.search-result-item:hover {
    background-color: #f9f9f9; /* Lichte achtergrond bij hover */
}

.search-result-item a {
    text-decoration: none;
    color: black; /* Contrastkleur die past bij de styling */
    display: block;
}

.search-result-item h4 {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
}

.search-result-item p {
    margin: 5px 0 0;
    font-size: 14px;
    color: #555;
}

</style>