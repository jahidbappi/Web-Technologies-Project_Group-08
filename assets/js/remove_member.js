/**
 * assets/js/remove_member.js
 * AJAX DELETE to remove a workspace member.
 * Fades the table row out on success.
 */

function removeMember(memberId) {
  if (!confirm('Remove this member from the workspace?')) return;

  const row = document.querySelector(`tr[data-member-id="${memberId}"]`);

  fetch(`index.php?page=api_delete_member&id=${memberId}`, {
    method: 'DELETE',
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
    .then(res => res.json())
    .then(data => {
      if (data.ok) {
        if (row) {
          row.classList.add('fading-out');
          setTimeout(() => row.remove(), 380);
        }
        const badge = document.getElementById('member-count');
        if (badge) badge.textContent = parseInt(badge.textContent) - 1;
      } else {
        alert('Error: ' + (data.error || 'Could not remove member.'));
      }
    })
    .catch(() => alert('Network error. Please try again.'));
}
