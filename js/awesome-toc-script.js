jQuery(document).ready(function ($) {
    $('.awesome_toc-title').click(function () {
        const parent = $(this).parent();
        const toggleBtn = $('.awesome_toc-toggle');
        toggleBtn.toggleClass('collapsed');
        if (parent.hasClass('awesome_toc-open')) {
            parent.removeClass('awesome_toc-open');
            $(this).siblings('.awesome_toc-list').slideUp();
        } else {
            parent.addClass('awesome_toc-open');
            $(this).siblings('.awesome_toc-list').slideDown();
        }
    });
});
