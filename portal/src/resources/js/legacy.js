// LEGACY CODE THAT IS STILL IN PLACE BUT SHOULD BE PORTED TO
// VUE COMPONENTS

jQuery(document).ready(function ($) {

    ////////////////////////////////////////////////////
    //  Initialize additional javascript helpers.
    ////////////////////////////////////////////////////
    require('./calendar');
    require('./rpa');

    ////////////////////////////////////////////////////
    // Prevent enter from submitting the form. Instead, let enter go to the next field / row
    ////////////////////////////////////////////////////
    $('input.form-control').keydown(function (e) {
        if (e.which === 13) {
            var self = $(this), form = self.parents('form:eq(0)'), focusable, next;
            var parent = self.parent();
            if (parent.is('td')) {
                // input inside a table, move to beginning of next row.
                var nextRow = parent.closest('tr').next('tr');
                if (nextRow) {
                    nextRow.find('td input:text').first('input').focus();
                }
            } else {
                // input inside form, move to next field.
                focusable = form.find('input').filter(':visible');
                next = focusable.eq(focusable.index(this) + 1);
                if (next.length) {
                    next.focus();
                }
            }
            return false;
        }
    });

    ////////////////////////////////////////////////////
    // Auto cloning rows upon entry
    ////////////////////////////////////////////////////
    function cloneRow(el) {
        if (!el.val()) {
            var tr = el.closest('tr');
            var clone = tr.clone(true);
            clone.insertAfter(tr).find('.auto-row-clone').one("focus", function () {
                cloneRow($(this));
            });

            clone.find('.form-control').each(function (i, input) {
                // Bump input field names
                input.name = input.name.replace(/\d+/, function (n) {
                    return ++n
                });
                input.value = '';
            });

            // Also, the current TR now gets its 'delete' button unhidden.
            tr.find('.btn-delete').removeClass('invisible');
        }
    }

    // Make auto row clone fields actually clone a row (upon the first keypress in the input field)
    $(".auto-row-clone").one("focus", function () {
        cloneRow($(this));
    });

    ////////////////////////////////////////////////////
    // Delete button in tables
    ////////////////////////////////////////////////////
    $(".btn-delete").click(function () {
        $(this).closest('tr').remove();
    });

    ////////////////////////////////////////////////////
    // Sidebar appearance
    ////////////////////////////////////////////////////
    $('.sidebar-open').on('click', function () {
        console.log('sidebar appears!');
        let contactUuid = $(this).data('uuid');

        $.ajax({
            type: "GET",
            url: '/task/' + contactUuid + '/questionnaire',
            data: null,
            success: function (data) {
                $('.sidebar-content').html(data);
                $('.sidebar').collapse('show');
            }
        });

    });

});