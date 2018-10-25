$(document).ready(function($)
{
    $("#comment").html("<i class=\"fa fa-comment\"></i> ");

    $("#comment_wrap_toggle").click(function()
    {
        $("#comment_wrap").slideToggle(300);

        if ($("#span").text() == "Add Comment")
        {
            $("#span").text("Cancel");
            $("#comment").html("<i class=\"fa fa-times\"></i> ");
        }
        else
        {
            $("#span").text("Add Comment");
            $("#comment").html("<i class=\"fa fa-comment\"></i> ");
        }
    });
});