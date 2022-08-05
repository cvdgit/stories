<?php
/** @var $count integer */

use yii\helpers\Url;

$css = <<< CSS
.notification-wrapper {
    display: inline-block;
    position: relative;
    width: 44px;
    height: 44px;
    margin-top: 4px;
    margin-right: 20px;
}
.notification-button {
    font-size: 24px !important;
    width: 100%;
    height: 100%;
    background: none;
    border: none;
}
.notification-counter {
    position: absolute;
    top: 1px;
    right: 1px;
    pointer-events: none;
}
#user-notifications .dropdown-menu > li > a {
    width: 400px;
    white-space: normal;
}
#user-notifications ul.dropdown-menu {
    overflow: hidden !important;
    overflow-y: auto !important;
}
#user-notifications .media-body i {
    display: block;
    font-size: 13px;
    line-height: 18px;
    color: rgb(96, 96, 96);
    margin-top: 4px;
}
CSS;
/** @var $this yii\web\View */
$this->registerCss($css);

$this->registerJsFile('/js/smartdate.js');
?>
<div class="notification-wrapper">
    <div class="dropdown" id="user-notifications">
        <button data-toggle="dropdown" class="dropdown-toggle notification-button"><i class="glyphicon glyphicon-bell" data-toggle="tooltip" title="Уведомления" data-placement="bottom"></i></button>
        <?php if ($count > 0): ?>
            <i class="badge badge-important notification-counter"><?= $count ?></i>
        <?php endif ?>
        <ul class="dropdown-menu pull-right"></ul>
    </div>
</div>
<?php
$action = Url::to(['/notification/unread']);
$js = <<< JS
var UserNotification = (function() {

    var loaded = false;

    function createNotification(notification) {
        var data = JSON.parse(notification.text);
        var a = $('<a/>')
            .attr('href', data.link)
            .append(
                $('<div/>')
                    .addClass('media')
                    .append(
                        $('<div/>').addClass('media-left').append(
                            $('<img/>').attr('src', data.image).css('width', '64px')
                        )
                    )
                    .append(
                        $('<div/>').addClass('media-body')
                            .append(data.text)
                            .append($('<i/>').addClass('smartdate').attr('data-timestamp', notification.created_at))
                    )
            );
        return $('<li/>').append(a);
    }

    function loadNotifications() {
        var list = $('#user-notifications .dropdown-menu');
        list.empty();
        list.append($('<img/>').attr('src', '/img/loading.gif').css('width', '32px'));
        $.getJSON('$action').done(function(response) {
            list.empty();
            var header = $('<li/>').addClass('dropdown-header').text('Уведомления');
            list.append(header);
            list.append($('<li/>').addClass('divider'));
            if (response.length) {
                $('#user-notifications .notification-counter').remove();
                var i = 1;
                response.forEach(function(item) {
                    var li = createNotification(item);
                    list.append(li);
                });
                smartdate.init({
                    locale: 'ru',
                    tagName: 'i',
                    mode: 'auto'
                });
            }
            else {
                list.append($('<li/>').addClass('disabled').append($('<a/>').attr('href', '#').text('Уведомлений нет')));
            }
        });
    }

    $('#user-notifications').on('show.bs.dropdown', function () {

        var vh = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);
        vh -= 100;
        $('#user-notifications .dropdown-menu').css('height', vh + 'px');

        if (!loaded) {
            loaded = true;
            loadNotifications();
        }
    })
})();
JS;
$this->registerJs($js);
