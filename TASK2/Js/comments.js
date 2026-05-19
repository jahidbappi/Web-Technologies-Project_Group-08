const commentForm = document.getElementById('comment-form');

commentForm.addEventListener('submit', function (e) {

    e.preventDefault();

    const taskId = document.getElementById('task_id').value;
    const body = document.getElementById('comment_body').value;

    const formData = new FormData();

    formData.append('task_id', taskId);
    formData.append('body', body);

    fetch('../controllers/CommentController.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        const errorDiv = document.getElementById('error-message');

        if (data.error) {

            errorDiv.innerText = data.error;
            errorDiv.style.display = 'block';
            return;
        }

        errorDiv.style.display = 'none';

        const html = `
            <div class="comment" id="comment-${data.id}">

                <strong>${data.author_name}</strong>

                : ${data.body}

                <small class="time-text">
                    Just now
                </small>

                <a href="#"
                   onclick="deleteComment(${data.id}); return false;"
                   class="delete-btn">
                   Delete
                </a>

            </div>
        `;

        document
            .getElementById('comment-thread')
            .insertAdjacentHTML('beforeend', html);

        document.getElementById('comment_body').value = '';
    });
});

function deleteComment(commentId) {

    fetch(`../controllers/CommentController.php?id=${commentId}`, {
        method: 'DELETE'
    })
    .then(res => res.json())
    .then(data => {

        if (data.ok) {

            const el = document.getElementById(`comment-${commentId}`);

            el.classList.add('fade-out');

            setTimeout(() => {
                el.remove();
            }, 500);

        } else {

            alert(data.error);

        }
    });
}