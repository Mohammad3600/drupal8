
//set up uploadify widget on pageload
jQuery(document).ready(function() {
	jQuery("#uploadify_fileUpload").uploadify({
		'uploader': '/sites/all/modules/uploadify/jquery.uploadify-v2.1.0/uploadify.swf',
		'cancelImg': '/sites/all/modules/uploadify/jquery.uploadify-v2.1.0/cancel.png',
		'script': '/sites/all/modules/uploadify/jquery.uploadify-v2.1.0/uploadify.php',
		'folder': '/files',
		'fileDesc': 'Image Files',
		'fileExt': '*.jpg;*.jpeg;*.gif;*.png',
		'multi': false,
		'auto': true,
        /* call custom btn for browse */
        'buttonImg': '/sites/all/themes/sm_theme_01/images/buttons/browse.gif',
        'width': '120',
        'height': '20',
        'wmode': 'transparent',

		'scriptData': {userID: USER_ID}, //set in sustainable_minds.module
		'onComplete': 
			function(event, queueID, fileObj, response, data) {
				path = '/files/u' + USER_ID + '/' + fileObj['name'];
				jQuery('#edit-icon').val(path);
				//set img src to new file
				jQuery('#uploadify_current_img').src(path);
				
				/*jQuery.ajax({
				type: "GET",
				url: "/ajax/actions/upload_image",
				data: "userID="+USER_ID+"&folder=files&filename="+fileObj['name'],
				success: function (msg){
					//set hidden field to use on submit
					jQuery('#edit-icon').val(msg);
					//set img src to new file
					jQuery('#uploadify_current_img').src(msg);
					return true;
					}
				});*/
			}
	});
});

//when someone clicks "Use default image" retrieve that image and show it
function useDefaultImage(type, id) {
	 jQuery.ajax({
		type: "GET",
		url: "/ajax/actions/use_default_image",
		data: "type="+type+"&id="+id,
		success: function (msg){
			//set hidden field to use on submit
			jQuery('#edit-icon').val(msg);
			jQuery('#uploadify_current_img').src(msg);
			return true;
		}
	});
}
