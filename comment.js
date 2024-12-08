
  
    function loadComments(productId) {
    fetch(`get_comments.php?id=${productId}`)
        .then(response => response.json())
        .then(data => {
            const commentSection = document.getElementById('comments');
            commentSection.innerHTML = ''; // Maak de sectie leeg

            if (data.error) {
                commentSection.innerHTML = `<p>Error: ${data.error}</p>`;
            } else if (data.length === 0) {
                commentSection.innerHTML = '<p>Er zijn nog geen reacties voor dit product.</p>';
            } else {
                data.forEach(comment => {
                    const commentDiv = document.createElement('div');
                    commentDiv.className = 'comment';
                    commentDiv.innerHTML = `
                        <p><strong>${comment.email}</strong> schreef:</p>
                        <p>${comment.comment}</p>
                        <p><small>Op ${comment.created_at}</small></p>
                    `;
                    commentSection.appendChild(commentDiv);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching comments:', error);
            document.getElementById('comments').innerHTML = '<p>Fout bij het laden van reacties.</p>';
        });
}
