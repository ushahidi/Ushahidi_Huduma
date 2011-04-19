<?php
/**
 * Common javascript for the dashboard pages
 */
?>
    $(document).ready(function(){

        /* Form Actions */
        // Action on Save Only
        $('.btn_save').live('click', function () {
            $("#save").attr("value", "1");
            $(this).parents("form").submit();
            return false;
        });
    });