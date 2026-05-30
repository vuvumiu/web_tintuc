<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container {
    width: 100% !important;
}

.select2-container--default .select2-selection--single {
    min-height: calc(2.25rem + 2px);
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    padding: 0.375rem 2rem 0.375rem 0.75rem;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 1.5rem;
    padding-left: 0;
    color: #495057;
}

.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #6c757d;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(2.25rem + 2px);
    right: 6px;
}

.select2-results__option {
    white-space: normal;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function() {
    $('.select2').each(function() {
        var $select = $(this);
        $select.select2({
            width: '100%',
            placeholder: $select.data('placeholder') || '',
            allowClear: !$select.prop('required')
        });
    });
});
</script>
