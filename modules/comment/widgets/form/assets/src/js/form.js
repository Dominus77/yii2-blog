$(document).ready(function () {
    let commentButton = $('.comment-button'),
        btnReply = $('.btn-reply'),
        form = $('#reply-form'),
        inputEntityId = $('#comment-entity_id'),
        inputParentId = $('#comment-parentid'),
        formContainerId = '#form-container',
        prefixFormContainer = '#form-container-',
        target,
        id,
        entityId,
        replyContainer;

    btnReply.on('click', function (e) {
        e.preventDefault();

        btnReply.show();
        $(this).hide();
        commentButton.show();

        target = $(this);
        id = target.data('id');
        entityId = target.data('entityid');
        replyContainer = prefixFormContainer + id;

        form.appendTo(replyContainer);
        //form.show();
        inputEntityId.val(entityId);
        inputParentId.val(id);
    });

    commentButton.on('click', function () {
        btnReply.show();
        $(formContainerId).show();
        commentButton.hide();
        form.appendTo(formContainerId);
        inputParentId.val(null);
    });
});
