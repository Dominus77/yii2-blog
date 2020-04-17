let commentThisButton = $('#comment-this-button'),
    parentid = $('#comment-parentid'),
    form = $('#comment-form'),
    replyContainer = $('.reply-container'),
    replyButton = $('.reply-button'),
    container = '.comment-widget-form';

replyContainer.show();

function reply(e) {
    let target = e,
        id = target.dataset.id,
        replyContainer = '#reply-form-container-' + id;

    replyButton.show();
    replyButton = $('#reply-button-' + id);

    form.appendTo(replyContainer);
    parentid.val(id);
    replyButton.hide();
    commentThisButton.show();
}

commentThisButton.on('click', function () {
    replyButton.show();
    commentThisButton.hide();
    form.appendTo(container);
    parentid.val(null);
});
