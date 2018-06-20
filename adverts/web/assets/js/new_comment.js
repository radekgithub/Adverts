$(document).ready(function($)
{
    $("#comment_wrap_toggle").click(function()
    {
        $("#comment_wrap").slideToggle(300);

        if ($("#comment_wrap_toggle").text() == "Add Comment")
        {
            $("#comment_wrap_toggle").html("Hide")
        }
        else
        {
            $("#comment_wrap_toggle").text("Add Comment")
        }
    });
});