
var WikidsShare = (function() {

    function getLink() {
        return location.toString();
    }

    function getFullLink()
    {
        return location.origin + location.pathname;
    }

    return {
        getLink: getLink,
        getFullLink: getFullLink
    };
})();

var $share = $('#share');

var share = Ya.share2($share[0], {
    content: {
        url: WikidsShare.getFullLink()
    },
    theme: {
        services: 'vkontakte,facebook,odnoklassniki,lj,twitter,evernote,whatsapp',
        counter: false,
        lang: 'ru',
        size: 'm',
        bare: false
    }
});

$('#share-slide-checkbox').on('change', function() {
    var content = {
        title: $share.data("title"),
        url: this.checked ? WikidsShare.getLink() : WikidsShare.getFullLink(),
        description: $share.data("description"),
        image: $share.data("image")
    };
    share.updateContent(content);
    $('#share-link').val(content.url);
}).change();
