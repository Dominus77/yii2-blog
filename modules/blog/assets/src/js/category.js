function categoryInit(param) {
    $('#input-parent-id').on('change', function () {
        let parentId = $(this).val(),
            url = param.url,
            id = param.id,
            positionContainer = $('#position-container'),
            inputPosition = $('#input-position'),
            childrenListContainer = $('#children-list-container'),
            childrenList = $('#input-children-list');

        if (parentId === '') {
            childrenList.html(parentId);
            childrenListContainer.hide();
            positionContainer.show();
        } else {
            inputPosition.val(0);
            positionContainer.hide();

            $.ajax({
                url: url,
                dataType: 'json',
                type: 'post',
                data: {id: id, parent: parentId}
            }).done(function (response) {
                childrenList.html(response.result);
                if (response.result === '') {
                    childrenListContainer.hide();
                } else {
                    childrenListContainer.show();
                }
            });
        }
    });
}
