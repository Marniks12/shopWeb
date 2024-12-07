document.getElementById('comment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const comment = document.getElementById('comment').value;
    const productId = document.getElementById('product-id').value; // Zorg ervoor dat product_id beschikbaar is in een verborgen inputveld.

    fetch('add_comment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${productId}&comment=${encodeURIComponent(comment)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            const commentsList = document.getElementById('comments-list');
            const newComment = document.createElement('div');
            newComment.textContent = comment;
            commentsList.appendChild(newComment);
            document.getElementById('comment').value = '';
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Fout:', error));
});
