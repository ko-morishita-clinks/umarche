export function deletePost(e) {
    'use strict';
    if (confirm('本当に削除してもいいですか?')) {
        document.getElementById('delete_' + e.dataset.id).submit();
    }
}

window.deletePost = deletePost;