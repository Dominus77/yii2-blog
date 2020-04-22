$(document).ready(function () {
    let loc = window.location.hash.replace('#',''),
        tr;

    if (loc !== '') {
        tr = $('#' + loc).parent().parent().parent('tr');
        tr.show();
    }

    let rowDetail = $('.row-detail'),
        detail = $('.detail'),
        selector = 'span.row-collapse',
        expand = 'row-collapse glyphicon glyphicon-expand',
        down = 'row-collapse glyphicon glyphicon-collapse-down',
        sel;

    function setExpand() {
        rowDetail.each(function () {
            sel = this.querySelector(selector);
            if (sel) {
                sel.className = expand;
            }
        });
    }

    rowDetail.on('click', function () {
        let target = $(this),
            key = target.parent('tr').data('key'),
            targetDetail = $('#detail-' + key),
            span = this.querySelector('span.row-collapse');

        setExpand();

        if (targetDetail.is(':visible')) {
            span.className = expand;
            targetDetail.hide();
        } else {
            detail.hide();
            span.className = down;
            targetDetail.show();
        }
    });
});
