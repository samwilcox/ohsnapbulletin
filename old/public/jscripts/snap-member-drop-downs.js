$(document).click(function(e) {
	if (e.target.id != 'mmlink' && !$('#mmlink').find(e.target).length) {
        $("#member_menu").fadeOut(500);
    }
});

function toggleMemberMenu()
{	
	var elHeight = $("#member_menu").height();	
	elHeight     = (elHeight + 10);
	$("#member_menu").css({'bottom':-elHeight});
	$("#member_menu").fadeToggle(500);
}