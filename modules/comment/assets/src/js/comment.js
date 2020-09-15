function commentInit(param)
{
    $('#input-parent-id').on('change', function () {
        let parentId = $(this).val(),
            url = param.url,
            id = param.id,
            entityContainer = $('#entity-container'),
            childrenListContainer = $('#children-list-container'),
            childrenList = $('#input-children-list'),
            entity = $('#input-entity'),
            entityId = $('#input-entity-id');

        if (parentId === '') {
            childrenList.html(parentId);
            childrenListContainer.hide();
            entity.val(null);
            entityId.val(null);
            entityContainer.show();
        } else {
            entityContainer.hide();

            $.ajax({
                url: url,
                dataType: 'json',
                type: 'post',
                data: {id: id, parent: parentId}
            }).done(function (response) {
                childrenList.html(response.result);
                entity.val(response.entity);
                if (response.entityId === 0) {
                    entityId.val(null);
                } else {
                    entityId.val(response.entityId);
                }
                if (response.result === '') {
                    childrenListContainer.hide();
                } else {
                    childrenListContainer.show();
                }
            });
        }
    });
}
