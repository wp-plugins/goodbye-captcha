jQuery( document ).ready(function($) {
    var maxLogsDaysElm = $('#gdbcsettingsadminmodule-settings-MaxLogsDays');

    if(maxLogsDaysElm.length !== 0) {
        if(maxLogsDaysElm.val() == 0) {
            maxLogsDaysElm.parent().children('p').first().toggle(false);
            maxLogsDaysElm.parent().children('p').last().toggle(true);
        }

        maxLogsDaysElm.change(function () {
            if ($(this).val() != 0) {
                $(this).parent().children('p').first().toggle(true);
                $(this).parent().children('p').last().toggle(false);
            }
            else {
                $(this).parent().children('p').first().toggle(false);
                $(this).parent().children('p').last().toggle(true);
            }

        });
    }
});