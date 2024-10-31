jQuery(document).ready(function ($) {

    $(".payamito-edd-open-modal").click(function () {
        debugger;

        $('#payamito-edd-modal').modal();
    })

    $('.payamito-edd-tag-modal').click(function () {
        $(this).CopyToClipboard()
    });

    $('.payamito-edd-tag-modal').jTippy({
        trigger: 'click',
        theme: 'green',
        position: 'bottom',
        size: 'small',
        title: 'کپی شد'
    });
});