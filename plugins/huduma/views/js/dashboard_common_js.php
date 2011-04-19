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
    
    function updateComment(commentId, containerHide, containerShow, action)  {
        // POST data
        var postData = { comment_id:commentId, action: action };
        
        // Execute POST
        $.post('<?php echo url::site(); ?>dashboards/home/update_comment', 
                postData, 
                function(response){
                    if (response.success) {
                        // Hide and show HTML elements
                        $("#"+containerHide+"").hide();
                        $("#"+containerShow+"").show();
                    }
                }
        );
    }