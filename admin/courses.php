<script>
    function _reset() {
        $('#manage-course').get(0).reset();
        $('#manage-course input, #manage-course textarea').val('');
        $("input[name='dept_id']").val('<?php echo $dept_id; ?>');
    }

    $('#manage-course').submit(function(e) {
        e.preventDefault();
        console.log("Form submitted!");

        let course = $("input[name='course']").val().trim();
        let description = $("textarea[name='description']").val().trim();

        if (course === '' || description === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please fill out all required fields.',
                showConfirmButton: true
            });
            return; // Stop if validation fails
        }

        $.ajax({
            url: 'ajax.php?action=save_course',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function(resp) {
                console.log("Server response:", resp);
                if (resp == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Data successfully added/updated!',
                        showConfirmButton: true
                    }).then(function() {
                        location.reload();
                    });
                } else if (resp == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Course already exists!',
                        showConfirmButton: true
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to save data!',
                        showConfirmButton: true
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'There was a problem with the request.',
                    showConfirmButton: true
                });
            }
        });
    });

    $('.edit_course').click(function() {
        _reset();
        var cat = $('#manage-course');
        cat.find("[name='id']").val($(this).attr('data-id'));
        cat.find("[name='course']").val($(this).attr('data-course'));
        cat.find("[name='description']").val($(this).attr('data-description'));
    });

    $('.delete_course').click(function() {
        var id = $(this).attr('data-id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                delete_course(id);
            }
        });
    });

    function delete_course(id) {
        $.ajax({
            url: 'ajax.php?action=delete_course',
            method: 'POST',
            data: { id: id },
            success: function(resp) {
                console.log("Delete response:", resp);
                if (resp == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Data successfully deleted.',
                        showConfirmButton: true
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to delete data.',
                        showConfirmButton: true
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Delete AJAX error:", status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'There was a problem with the delete request.',
                    showConfirmButton: true
                });
            }
        });
    }

    // Initialize DataTable
    $(document).ready(function() {
        $('#course-table').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true
        });
    });
</script>
