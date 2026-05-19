function loadActivity() {

    const projectId =
        document.getElementById('project_id').value;

    const userId =
        document.getElementById('member-filter').value;

    fetch(
        `../controllers/ActivityController.php?project_id=${projectId}&user_id=${userId}`
    )
    .then(res => res.json())
    .then(data => {

        let html = '';

        if (data.length === 0) {

            html = `
                <div class="empty-state">
                    No activity found.
                </div>
            `;

        } else {

            data.forEach(log => {

                html += `
                    <div class="activity-item">

                        <div class="avatar">
                            ${log.initials}
                        </div>

                        <div class="activity-content">

                            <div class="action">
                                ${log.action_text}
                            </div>

                            <div class="time">
                                ${log.time_ago}
                            </div>

                        </div>

                    </div>
                `;
            });
        }

        document.getElementById('activity-list').innerHTML = html;
    });
}


document
    .getElementById('member-filter')
    .addEventListener('change', loadActivity);

loadActivity();