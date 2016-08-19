$("#monthChange").change(function() {
	cur_url = ''+location;
	//console.log("Month changed.");
	user_id = $("#ukm_user_id").val();
	date = $("#monthChange").val();

	cur_url = cur_url.split("/user");
	//console.log(cur_url);
	url = cur_url[0]+'/user-'+user_id+'/'+date;
	location = url;
});