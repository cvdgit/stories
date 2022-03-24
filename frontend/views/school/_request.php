<div class="school-modal modal fade" tabindex="-1" id="contact-request-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<?php
$this->registerJs(<<<JS
(function() {
    $('.contact-request').on('click', function(e) {
        e.preventDefault();
        $('#contact-request-modal').modal('show', $(this));
    });
    $('#contact-request-modal')
        .on('show.bs.modal', function(e) {
            var elem = $(e.relatedTarget);
            $(this).find('.modal-content').load(elem.attr('href'));
        });
})();
JS
);