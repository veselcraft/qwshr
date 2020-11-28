// Links related

$('#editLinkModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget)
    var link_id = button.data('id');
    var link_url = button.data('url');
    var link_caption = button.data('caption');
    $('#editlink_id').val(link_id);
    $('#editlink_url').val(link_url);
    $('#editlink_caption').val(link_caption);
})

$('#deleteLinkModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget)
    var link_id = button.data('id');
    $('#deletelink_id').val(link_id);
})