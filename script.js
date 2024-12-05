function searchProduct() {
    const query = document.getElementById('searchInput').value;

    if (query.length < 1) {
        document.getElementById('searchResults').innerHTML = '';
        return;
    }

    fetch(`search.php?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            const resultsContainer = document.getElementById('searchResults');
            resultsContainer.innerHTML = '';

            if (data.length > 0) {
                data.forEach(product => {
                    const resultItem = document.createElement('div');
                    resultItem.className = 'result-item';
                    resultItem.innerHTML = `
                        <a href="detail.php?id=${product.id}">
                            <h4>${product.name}</h4>
                            <p>${product.description}</p>
                        </a>
                    `;
                    resultsContainer.appendChild(resultItem);
                });
            } else {
                resultsContainer.innerHTML = '<p>No results found.</p>';
            }
        })
        .catch(error => console.error('Error fetching search results:', error));
}
