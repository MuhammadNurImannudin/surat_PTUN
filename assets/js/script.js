// PTUN Banjarmasin - Custom JavaScript Functions

$(document).ready(function() {
    // Initialize all components
    initializeComponents();
    
    // Auto-hide alerts after 5 seconds
    $('.alert').delay(5000).fadeOut('slow');
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

function initializeComponents() {
    // Initialize DataTables
    if ($.fn.DataTable) {
        $('.data-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']],
            dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-5"i><"col-sm-7"p>>',
            columnDefs: [{
                targets: 'no-sort',
                orderable: false
            }]
        });
    }
    
    // Initialize Select2
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih...',
            allowClear: true
        });
    }
    
    // Initialize date pickers
    initializeDatePickers();
    
    // Initialize file upload preview
    initializeFileUpload();
    
    // Initialize form validation
    initializeFormValidation();
}

// Sidebar toggle for mobile
$('#sidebarToggle').click(function() {
    $('#sidebar').toggleClass('show');
});

// Close sidebar when clicking outside on mobile
$(document).click(function(event) {
    if ($(window).width() <= 768) {
        if (!$(event.target).closest('#sidebar, #sidebarToggle').length) {
            $('#sidebar').removeClass('show');
        }
    }
});

// Confirm delete with SweetAlert
$(document).on('click', '.btn-delete', function(e) {
    e.preventDefault();
    const url = $(this).attr('href');
    const title = $(this).data('title') || 'data ini';
    
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus ${title}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            showLoadingOverlay();
            window.location.href = url;
        }
    });
});

// Show loading overlay
function showLoadingOverlay(message = 'Memproses...') {
    const overlay = $(`
        <div class="loading-overlay">
            <div class="text-center text-white">
                <div class="loading-spinner mb-3"></div>
                <p>${message}</p>
            </div>
        </div>
    `);
    $('body').append(overlay);
}

// Hide loading overlay
function hideLoadingOverlay() {
    $('.loading-overlay').remove();
}

// Format number to Indonesian format
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

// Format currency to Indonesian Rupiah
function formatCurrency(num) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(num);
}

// Format date to Indonesian format
function formatDate(date, options = {}) {
    const defaultOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    
    return new Date(date).toLocaleDateString('id-ID', { ...defaultOptions, ...options });
}

// Initialize date pickers
function initializeDatePickers() {
    $('input[type="date"]').each(function() {
        if (!$(this).val() && $(this).hasClass('default-today')) {
            $(this).val(new Date().toISOString().split('T')[0]);
        }
    });
}

// Initialize file upload with preview
function initializeFileUpload() {
    $('input[type="file"]').on('change', function() {
        const file = this.files[0];
        const preview = $(this).siblings('.file-preview');
        
        if (file) {
            const reader = new FileReader();
            const fileType = file.type;
            const fileName = file.name;
            
            reader.onload = function(e) {
                if (fileType.startsWith('image/')) {
                    preview.html(`
                        <div class="text-center">
                            <img src="${e.target.result}" class="img-fluid" style="max-height: 200px;">
                            <p class="mt-2 mb-0 text-muted">${fileName}</p>
                        </div>
                    `);
                } else {
                    const icon = getFileIcon(fileName);
                    preview.html(`
                        <div class="text-center">
                            <i class="${icon} fa-4x mb-2"></i>
                            <p class="mb-0 text-muted">${fileName}</p>
                            <small class="text-muted">${formatFileSize(file.size)}</small>
                        </div>
                    `);
                }
                preview.show();
            };
            
            reader.readAsDataURL(file);
        } else {
            preview.hide();
        }
    });
}

// Get file icon based on extension
function getFileIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    const icons = {
        'pdf': 'fas fa-file-pdf text-danger',
        'doc': 'fas fa-file-word text-primary',
        'docx': 'fas fa-file-word text-primary',
        'xls': 'fas fa-file-excel text-success',
        'xlsx': 'fas fa-file-excel text-success',
        'jpg': 'fas fa-file-image text-info',
        'jpeg': 'fas fa-file-image text-info',
        'png': 'fas fa-file-image text-info',
        'gif': 'fas fa-file-image text-info',
        'zip': 'fas fa-file-archive text-warning',
        'rar': 'fas fa-file-archive text-warning'
    };
    
    return icons[ext] || 'fas fa-file text-secondary';
}

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Initialize form validation
function initializeFormValidation() {
    // Custom validation methods
    $.validator?.addMethod('indonesianDate', function(value, element) {
        return this.optional(element) || /^\d{4}-\d{2}-\d{2}$/.test(value);
    }, 'Format tanggal tidak valid');
    
    $.validator?.addMethod('fileSize', function(value, element, param) {
        if (this.optional(element)) return true;
        
        const fileSize = element.files[0]?.size || 0;
        return fileSize <= param;
    }, 'Ukuran file terlalu besar');
    
    // Initialize validation on forms
    $('form.needs-validation').each(function() {
        $(this).validate({
            errorClass: 'is-invalid',
            validClass: 'is-valid',
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element) {
                $(element).addClass('is-valid').removeClass('is-invalid');
            }
        });
    });
}

// Print functionality
function printElement(elementId) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const printWindow = window.open('', '_blank');
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                @media print {
                    body { font-size: 12px; }
                    .table { font-size: 11px; }
                    .table th, .table td { padding: 4px !important; }
                    .no-print { display: none !important; }
                }
            </style>
        </head>
        <body>
            ${element.innerHTML}
            <script>
                window.onload = function() {
                    window.print();
                    window.close();
                }
            </script>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
}

// Export table to Excel (CSV format)
function exportTableToExcel(tableId, filename = 'data') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = table.querySelectorAll('tr');
    let csv = '';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('th, td');
        const rowData = [];
        
        cells.forEach((cell, index) => {
            // Skip action columns
            if (!cell.classList.contains('no-export')) {
                rowData.push('"' + cell.textContent.replace(/"/g, '""').trim() + '"');
            }
        });
        
        csv += rowData.join(',') + '\n';
    });
    
    // Create and trigger download
    const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename + '_' + new Date().toISOString().split('T')[0] + '.csv';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Chart utilities
function createChart(canvasId, type, data, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;
    
    return new Chart(ctx, {
        type: type,
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            ...options
        }
    });
}

// AJAX form submission helper
function submitFormAjax(form, options = {}) {
    const $form = $(form);
    const url = $form.attr('action') || window.location.href;
    const method = $form.attr('method') || 'POST';
    
    const formData = new FormData($form[0]);
    
    $.ajax({
        url: url,
        type: method,
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            showLoadingOverlay('Menyimpan data...');
            $form.find('button[type="submit"]').prop('disabled', true);
        },
        success: function(response) {
            hideLoadingOverlay();
            $form.find('button[type="submit"]').prop('disabled', false);
            
            if (options.onSuccess) {
                options.onSuccess(response);
            } else {
                Swal.fire('Berhasil!', 'Data berhasil disimpan.', 'success')
                    .then(() => {
                        if (options.redirect) {
                            window.location.href = options.redirect;
                        } else {
                            location.reload();
                        }
                    });
            }
        },
        error: function(xhr) {
            hideLoadingOverlay();
            $form.find('button[type="submit"]').prop('disabled', false);
            
            if (options.onError) {
                options.onError(xhr);
            } else {
                Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan data.', 'error');
            }
        }
    });
}

// Search functionality
function initializeSearch(inputId, targetClass) {
    $(`#${inputId}`).on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        $(`.${targetClass}`).each(function() {
            const text = $(this).text().toLowerCase();
            const matches = text.includes(searchTerm);
            
            $(this).toggle(matches);
        });
    });
}

// Auto-save draft functionality
function initializeAutoSave(formId, interval = 30000) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    setInterval(function() {
        const formData = new FormData(form);
        formData.append('action', 'auto_save');
        
        fetch(form.action, {
            method: 'POST',
            body: formData
        }).then(response => {
            if (response.ok) {
                console.log('Draft saved automatically');
                // Show subtle notification
                $('.auto-save-indicator').removeClass('d-none').fadeIn().delay(2000).fadeOut();
            }
        }).catch(error => {
            console.error('Auto-save failed:', error);
        });
    }, interval);
}

// Notification system
function showNotification(message, type = 'info', duration = 5000) {
    const alertTypes = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const alertClass = alertTypes[type] || 'alert-info';
    const icon = {
        'success': 'fas fa-check-circle',
        'error': 'fas fa-exclamation-circle',
        'warning': 'fas fa-exclamation-triangle',
        'info': 'fas fa-info-circle'
    }[type] || 'fas fa-info-circle';
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 80px; right: 20px; z-index: 1050; min-width: 300px;">
            <i class="${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(() => {
        notification.fadeOut(() => notification.remove());
    }, duration);
}

// Keyboard shortcuts
$(document).keydown(function(e) {
    // Ctrl + S to save form
    if (e.ctrlKey && e.which === 83) {
        e.preventDefault();
        const form = $('form').first();
        if (form.length) {
            form.submit();
        }
    }
    
    // Escape to close modals
    if (e.which === 27) {
        $('.modal.show').modal('hide');
    }
});

// Real-time clock
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID');
    const dateString = now.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    $('.current-time').text(timeString);
    $('.current-date').text(dateString);
}

// Update clock every second
setInterval(updateClock, 1000);
updateClock(); // Initial call

// Lazy loading for images
function initializeLazyLoading() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
}

// Initialize lazy loading
initializeLazyLoading();