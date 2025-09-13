// Global JS
$(function () {
    // Auto-hide alerts after 3 seconds
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 3000);

    // Confirm delete
    $('.btn-delete').on('click', function () {
        return confirm('Yakin ingin menghapus data ini?');
    });
});