jQuery(document).ready(function($) {
    var pageNext = parseInt(ajax_load_more.pageNext);
    var pageMax = parseInt(ajax_load_more.pageMax);
    var pageLink = ajax_load_more.pageLink;
    var contentSelector = ajax_load_more.contentSelector;
    var postClassSelector = ajax_load_more.postClassSelector;
    var navigationSelector = ajax_load_more.navigationSelector;
    var buttonLabel = ajax_load_more.buttonLabel;
    var loadingMessage = ajax_load_more.loadingMessage;
    var finishedMessage = ajax_load_more.finishedMessage;

    if (pageNext <= pageMax) {
        $(contentSelector).append('<a id="ajax-load-more-by-bkker-theme-trigger">{text}</a>'.replace('{text}', buttonLabel));
        $(navigationSelector).remove();
    }

    $('#ajax-load-more-by-bkker-theme-trigger').click(function() {
        var next_link = pageLink.replace(/\d+(\/)?$/, pageNext + '$1');
        if (pageNext <= pageMax) {
            $(this).text(loadingMessage);
            $.ajax({ type: 'POST', url: next_link }).done(function(data) {
                pageNext++;
                $('#ajax-load-more-by-bkker-theme-trigger').before($(data).find(postClassSelector).fadeIn(1000));
                if (pageNext <= pageMax) {
                    $('#ajax-load-more-by-bkker-theme-trigger').text(buttonLabel);
                } else {
                    $('#ajax-load-more-by-bkker-theme-trigger').replaceWith($('<div id="ajax-load-more-by-bkker-theme-trigger-finished">' + finishedMessage + '</div>'));
                }
            });
        }
        return false;
    });
});
