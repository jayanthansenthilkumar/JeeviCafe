// Global jQuery wrapper for all actions
function handleAction(action, id = null) {
    let data = { action: action };
    if (id !== null) data.id = id;

    $.post('backend.php', data, function(res) {
        if (res.status === 'success') {
            if(res.message) {
                Swal.fire('Success', res.message, 'success').then(() => {
                    if(res.redirect) window.location.href = res.redirect;
                    else location.reload();
                });
            } else {
                if(res.redirect) window.location.href = res.redirect;
                else location.reload();
            }
        } else {
            Swal.fire('Error', res.message || 'Action failed', 'error');
        }
    });
}

function confirmAction(action, id, text) {
    if(confirm(text)) {
        handleAction(action, id);
    }
}

// Global Form Submissions
$(document).ready(function() {
    $(document).on('submit', 'form.ajax-form', function(e) {
        e.preventDefault();
        
        // Add loading indicator
        let btn = $(this).find('button[type="submit"]');
        let origText = btn.text();
        btn.text('Processing...').prop('disabled', true);
        
        let formData = new FormData(this);
        let action = $(this).data('action');
        formData.append('action', action);

        $.ajax({
            url: 'backend.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                if (res.status === 'success') {
                    if(res.message) {
                        Swal.fire('Success', res.message, 'success').then(() => {
                            if (res.redirect) window.location.href = res.redirect;
                            else location.reload();
                        });
                    } else {
                        if (res.redirect) window.location.href = res.redirect;
                        else location.reload();
                    }
                } else {
                    Swal.fire('Error', res.message || 'Submission failed.', 'error').then(()=> {
                        btn.text(origText).prop('disabled', false);
                    });
                }
            },
            error: function() {
                Swal.fire('Error', 'Server connection failed', 'error');
                btn.text(origText).prop('disabled', false);
            }
        });
    });
});
