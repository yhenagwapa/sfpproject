<script>
    function checkFormBeforeModal() {
        const form = document.getElementById('cycleForm');
        if (form.checkValidity()) {
            // Form is valid, show the modal
            const modal = new bootstrap.Modal(document.getElementById('cycleConfirmationModal'));
            modal.show();
        } else {
            // Trigger native validation UI
            form.reportValidity();
        }
    }

    function submitForm() {
        // Add actual form submission logic here
        document.getElementById('cycleForm').submit();
    }
</script>
