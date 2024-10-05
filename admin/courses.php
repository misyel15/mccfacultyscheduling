<script>
    // Function to reset the form when adding a new course
    function _reset(){
        $('#manage-course').get(0).reset();
        $('#manage-course input,#manage-course textarea').val('');
    }

    // Submit function for adding or updating a course
    $('#manage-course').submit(function(e){
        e.preventDefault();

        $.ajax({
            url: 'ajax.php?action=save_course',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(resp){
                if(resp == 1){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Data successfully added!',
                        showConfirmButton: true,
                    }).then(function() {
                        location.reload();
                    });
                } else if(resp == 2){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Data successfully updated!',
                        showConfirmButton: true,
                    }).then(function() {
                        location.reload();
                    });
                } else if(resp == 0){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Course already exists!',
                        showConfirmButton: true,
                    });
                }
            }
        });
    });

    // Edit button action
    document.querySelectorAll('.edit_course').forEach(function(button) {
        button.addEventListener('click', function() {
            // Reset the form
            var cat = document.querySelector('#manage-course');
            cat.reset();
            
            // Set values for edit
            cat.querySelector("[name='id']").value = this.getAttribute('data-id');
            cat.querySelector("[name='course']").value = this.getAttribute('data-course');
            cat.querySelector("[name='description']").value = this.getAttribute('data-description');
        });
    });

    // Delete button action
    document.querySelectorAll('.delete_course').forEach(function(button) {
        button.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
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
    });

    // Function to delete the course via AJAX
    function delete_course(id){
        $.ajax({
            url: 'ajax.php?action=delete_course',
            method: 'POST',
            data: { id: id },
            success: function(resp){
                if(resp == 1){
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Data successfully deleted.',
                        showConfirmButton: true,
                    }).then(function() {
                        location.reload();
                    });
                }
            }
        });
    }

    // Initialize DataTable
    $('table').dataTable();
</script>
