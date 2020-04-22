$(document).ready(function () {
    let rowDetail = $('.row-detail'),
        detail = $('.detail'),
        selector = 'span.row-collapse',
        expand = 'row-collapse glyphicon glyphicon-expand',
        down = 'row-collapse glyphicon glyphicon-collapse-down',
        sel,
        key,
        target,
        targetDetail,
        span;

    setLocation();
    setExpand();

    rowDetail.on('click', function () {
        target = $(this);
        key = target.parent('tr').data('key');
        targetDetail = $('#detail-' + key);
        span = this.querySelector('span.row-collapse');

        if (targetDetail.is(':visible')) {
            span.className = expand;
            targetDetail.hide();
        } else {
            detail.hide();
            span.className = down;
            targetDetail.show();
        }
        setExpand();
    });

    function setLocation() {
        let loc = window.location.hash.replace('#', ''),
            tr;
        if (loc !== '') {
            tr = $('#' + loc).parent().parent().parent('tr');
            tr.show();
        }
    }

    function setExpand() {
        let dataDetail;
        rowDetail.each(function () {
            dataDetail = $(this).data('detail');
            targetDetail = $('#' + dataDetail);
            sel = this.querySelector(selector);
            if (sel) {
                sel.className = expand;
                if (targetDetail.is(':visible')) {
                    sel.className = down;
                }
            }
        });
    }
});
