let siteBasePath = "/drupal8/web";
jQuery(function($) {
    console.log("custom js file loaded");
    $('#messages').hide();
    $('.step-1').show();
    $('.step-2').hide();
    $('.step-1-req-red').css('visibility', 'none');
    $('#block-teaserpurchaseblock').show();
    $('#block-systemrequirementsblock').show();

    if ($('ul').find('li[data-id]:visible').length > 0) {
        $('#messages').show();
    }
    $("input[name='submit']").click(function(e) {
        e.preventDefault();

        let firstname = $('#edit-firstname').val();
        if (firstname.length == '') {
            $('#edit-firstname').addClass('border-danger');
            $('li[data-id="firstname"]').show();
            firstnameError = true;
        } else {
            $('#edit-firstname').removeClass('border-danger');
            firstnameError = false;
            $('li[data-id="firstname"]').hide();
        }
        let lastname = $('#edit-lastname').val();
        if (lastname.length == '') {
            $('#edit-lastname').addClass('border-danger');
            $('li[data-id="lastname"]').show();
            lastnameError = true;
        } else {
            $('#edit-lastname').removeClass('border-danger');
            lastnameError = false;
            $('li[data-id="lastname"]').hide();
        }
        let phone = $('#edit-phone').val();
        if (phone.length == '') {
            $('#edit-phone').addClass('border-danger');
            $('li[data-id="phone"]').show();
            phoneError = true;
        } else {
            $('#edit-phone').removeClass('border-danger');
            phoneError = false;
            $('li[data-id="phone"]').hide();
        }
        let title = $('#edit-title').val();
        if (title.length == '') {
            $('#edit-title').addClass('border-danger');
            $('li[data-id="title"]').show();
            titleError = true;
        } else {
            $('#edit-title').removeClass('border-danger');
            titleError = false;
            $('li[data-id="title"]').hide();
        }
        let state = $('#edit-state').val();
        if (state.length == '') {
            $('#edit-state').addClass('border-danger');
            $('li[data-id="state"]').show();
            stateError = true;
        } else {
            $('#edit-state').removeClass('border-danger');
            stateError = false;
            $('li[data-id="state"]').hide();
        }
        let username = $('#edit-username').val();
        if (username.length == '') {
            $('#edit-username').addClass('border-danger');
            $('li[data-id="username"]').show();
            usernameError = true;
        } else {
            $('#edit-username').removeClass('border-danger');
            usernameError = false;
            $('li[data-id="username"]').hide();
        }
        let password = $('#edit-password').val();
        if (password.length == '') {
            $('#edit-password').addClass('border-danger');
            $('li[data-id="password"]').show();
            passwordError = true;
        } else {
            $('#edit-password').removeClass('border-danger');
            passwordError = false;
            $('li[data-id="password"]').hide();
        }

        let business = $('#edit-business').val();
        if (business.length == 0) {
            $('#edit-business').addClass('border-danger');
            $('li[data-id="business"]').hide();
            businessError = true;
        } else {
            $('#edit-business').removeClass('border-danger');
            businessError = false;
            $('li[data-id="business"]').hide();
        }

        // Validate Email
        let email = $('#edit-email').val();
        if (email.length == '') {
            let regex =
                /^([_\-\.0-9a-zA-Z]+)@([_\-\.0-9a-zA-Z]+)\.([a-zA-Z]){2,7}$/;
            let s = email.value;
            if (regex.test(s)) {
                $('#edit-email').removeClass('border-danger');
                emailError = false;
                $('li[data-id="email"]').hide();
            } else {
                $('#edit-email').addClass('border-danger');
                emailError = true;
                $('li[data-id="email"]').show();
            }
        } else {
            $('#edit-email').removeClass('border-danger');
            emailError = false;
            $('li[data-id="email"]').hide();
        }
        let confirmPassword = $('#edit-confirm-password').val();
        if (confirmPassword.length == '') {
            $('#edit-confirm-password').addClass('border-danger');
            confirmPasswordError = true;
            $('li[data-id="confirm-password"]').html('Repeat password field is required.');
            $('li[data-id="confirm-password"]').show();
        } else {
            let passwordValue =
                $('#edit-password').val();
            if (passwordValue != '') {
                if (passwordValue != confirmPassword) {
                    $('li[data-id="confirm-password"]').html('The password and confirm password do not match');
                    $('li[data-id="confirm-password"]').show();
                    $('#edit-confirm-password').addClass('border-danger');
                    confirmPasswordError = true;
                } else {
                    $('#edit-confirm-password').removeClass('border-danger');
                    confirmPasswordError = false;
                    $('li[data-id="confirm-password"]').hide();
                }
            }
        }

        let acceptTerms = $("input[type='checkbox'][name='acceptterms']:checked");
        if (acceptTerms.length) {
            $('li[data-id="accept"]').hide();
            acceptError = false;
        } else {
            $('#edit-business').removeClass('border-danger');
            acceptError = true;
            $('li[data-id="accept"]').show();
        }

        if ((usernameError == true) ||
            (lastnameError == true) ||
            (firstnameError == true) ||
            (phoneError == true) ||
            (stateError == true) ||
            (titleError == true) ||
            (passwordError == true) ||
            (confirmPasswordError == true) ||
            (emailError == true) ||
            (businessError == true) ||
            (acceptError == true)) {
            $('#messages').show();
            return true;
        } else {
            $('.step-1').hide();
            $('.step-2').show();
            $('#block-teaserpurchaseblock').hide();
            $('#block-systemrequirementsblock').hide();
            $('.step-1-req-red').css('visibility', 'hidden');
            jQuery('#edit-password').parent('div').parent('div').parent('div').hide();
            jQuery('#edit-username').parent('div').parent('div').parent('div').hide();
            jQuery('#edit-confirm-password').parent('div').parent('div').parent('div').hide();
            $('#messages').hide();
            return false;
        }

    });
    /*logic for learning center right sidebar
     */
    function matchLinkText(path, machine_name) {
        if (parts[parts.length - 2] == path) {
            $(".users_guide_conducting_an_sm_lcaa ul li a").each(function() {
                var linkTextParts = $(this).attr('href').split("/");
                if (linkTextParts[linkTextParts.length - 1] == parts[parts.length - 1]) {
                    $(this).parent().parent().parent().addClass('active');
                }
            })
        }
    }
    stopwords = ['is', 'as', 'a']
    var url = location.href;
    parts = url.split("/");
    // last_part = parts[parts.length - 1];
    // last_but_one = parts[parts.length - 2];
    $('.lc_taxanomy').hide();
    if (parts[parts.length - 2] == 'learning-center' || parts[parts.length - 3] == 'learning-center') {
        if (parts[parts.length - 1] == 'why-sustainable-minds' || parts[parts.length - 2] == 'why-sustainable-minds') {
            if (parts[parts.length - 2] == 'why-sustainable-minds') {
                $(".why_sustainable_minds_ ul li a").each(function() {
                    var linkTextParts = $(this).attr('href').split("/");
                    if (parts[parts.length - 1] == 'why-sustainable-minds' && linkTextParts[linkTextParts.length - 1] == '') {
                        $(this).parent().parent().parent().addClass('active');
                    }
                    if (linkTextParts[linkTextParts.length - 1] == parts[parts.length - 1]) {
                        $(this).parent().parent().parent().addClass('active');
                    }
                })
            }
            $('.why_sustainable_minds_').parent('li').addClass('active');
            $('.why_sustainable_minds_').show();
        }
        if (parts[parts.length - 1] == "users-guide-conducting-sm-lca" || parts[parts.length - 2] == "conducting-sm-lca") {
            $('.users_guide_conducting_an_sm_lcaa').show();
            $('.users_guide_conducting_an_sm_lcaa').parent('li').addClass('active');
            if (parts[parts.length - 2] == 'conducting-sm-lca') {
                $(".users_guide_conducting_an_sm_lcaa ul li a").each(function() {
                    var linkTextParts = $(this).attr('href').split("/");
                    if (linkTextParts[linkTextParts.length - 1] == parts[parts.length - 1]) {
                        $(this).parent().parent().parent().addClass('active');
                    }
                })
            }
        }
        if (parts[parts.length - 1] == "ecodesign-and-lca" || parts[parts.length - 2] == "ecodesign-and-lca") {
            $('.ecodesign_and_lca').show();
            $('.ecodesign_and_lca').parent('li').addClass('active');
            if (parts[parts.length - 2] == 'ecodesign-and-lca') {
                $(".ecodesign_and_lca ul li a").each(function() {
                    var linkTextParts = $(this).attr('href').split("/");
                    if (linkTextParts[linkTextParts.length - 1] == parts[parts.length - 1]) {
                        $(this).parent().parent().parent().addClass('active');
                    }
                })
            }
        }
        if (parts[parts.length - 1] == "ecodesign-strategies" || parts[parts.length - 2] == "ecodesign-strategies") {
            $('.ecodesign_strategies').show();
            $('.ecodesign_strategies').parent('li').addClass('active');
            if (parts[parts.length - 2] == 'ecodesign-strategies') {
                $(".ecodesign_strategies ul li a").each(function() {
                    var linkTextParts = $(this).attr('href').split("/");
                    if (linkTextParts[linkTextParts.length - 1] == parts[parts.length - 1]) {
                        $(this).parent().parent().parent().addClass('active');
                    }
                })
            }
        }
        if (parts[parts.length - 1] == "methodology" || parts[parts.length - 2] == "methodology") {
            $('.sm2013_impact_assessment_methodology').show();
            $('.sm2013_impact_assessment_methodology').parent('li').addClass('active');
            if (parts[parts.length - 2] == 'methodology') {
                $(".sm2013_impact_assessment_methodology ul li a").each(function() {
                    var linkTextParts = $(this).attr('href').split("/");
                    if (linkTextParts[linkTextParts.length - 1] == parts[parts.length - 1]) {
                        $(this).parent().parent().parent().addClass('active');
                    }
                })
            }
        }
    }
});

function project_edit_add_to_page(page) {
	jQuery('form #edit-to-page').val(page);
	jQuery('form #edit-submit').click();
}
    
    // $('#Product-Page').change(()=>{
    //     alert('hello');
    // });
    

    $('#NewProject').click(()=>{
        jQuery.ajax({
            type: "GET",
            url: `${siteBasePath}/ajax/actions/newproject`,
            success: function (pid){
                if (pid) top.location.href = `${siteBasePath}/project/add`+'/'+pid+'/'+'definition';
                else alert('Issues creating new project.');
            }
        });
    });
    function copy_project(pid) {
        jQuery.ajax({
            type: "GET",
            url: `${siteBasePath}/ajax/actions/copy_project`,
            data: "pid="+pid,
            success: function (){
                alert('A copy of this project has been created.');
                top.location.href = `${siteBasePath}/project/list/user`;
            }
        });
    }
    
    function copy_update_project(pid, version) {
        jQuery.ajax({
            type: "GET",
            url: `${siteBasePath}/ajax/actions/copy_update_project`,
            data: "pid="+pid+"&version="+version,
            success: function (){
                alert('A copy of this project has been created with an updated methodology.');
                top.location.href = `${siteBasePath}/project/list/user`;
            }
        });
    }
    function delete_project(pid){
        if(confirm("Deleting this project will also delete all concepts within it. Are you sure you want to proceed?")){
            jQuery.ajax({
                type: "GET",
                url: `${siteBasePath}/ajax/actions/delete_project`,
                data: "pid="+pid,
                success: function (){
                    alert('Project was deleted.');
                    top.location.href = `${siteBasePath}/project/list/user`;
        
                }
            });
        }
    }
    
    function copy_to_project(pid) {
        var target = prompt("Please enter the username", "username");
        if (target != null) {
            jQuery.ajax({
                type: "GET",
                url: `${siteBasePath}/ajax/actions/copy_to_project`,
                data: "pid="+pid+"&target="+target,
                success: function (tError){
                    console.log(tError);
                    if (tError == "noname") {
                        alert('The specified username does not exist - copy aborted.');
                    } else {
                        alert('A copy of this project has been created in '+target+' project folder.');
                    }
                    top.location.href = `${siteBasePath}/project/list/user`;
                }
            });
        }
    }
    $(document).ready(function () {
        jQuery('#product-functional-unit-edit').hide();
        var default_funame = "1 hour of use";
	/* 
        var default_funame = "1 hour of use";
	var default_fudesc = "Hour of use is a standard unit of measure when service delivered is measured by time.";
        */
/* default functional unit updated by K.L. 2-27-2012 */
        var default_funame = "1 unit of service";
	    var default_fudesc = "Unit of serivice is a generic unit of measure for service delivered.";
        
        jQuery('#product-functional-unit-value').html(jQuery('#edit-funame').val());
        if (jQuery('#edit-funame').val() == default_funame){
            jQuery('#product-functional-unit-value-note').html("&nbsp;(default)");
        }
        else{
        jQuery('#product-functional-unit-value-note').html("");
        jQuery('#product-functional-unit-desc').html(jQuery('#edit-fudesc').val());
        jQuery('#edit-product-functional-unit-edit-value').val(jQuery('#edit-funame').val());
        jQuery('#edit-product-functional-unit-edit-desc').val(jQuery('#edit-fudesc').val());
        }
        jQuery('#product-functional-unit-edit-cancel').click();
        jQuery('#product-functional-unit-change-button').click(function() {
            jQuery('#product-functional-unit-change').hide();
            jQuery('#product-functional-unit-edit').fadeIn();
            jQuery('#edit-product-functional-unit-edit-value').val(jQuery('#edit-funame').val());
            if (jQuery('#edit-fudesc').val() == default_fudesc) //Terry doesn't want default desc to populate textbox
                jQuery('#edit-product-functional-unit-edit-desc').val('');
            else
                jQuery('#edit-product-functional-unit-edit-desc').val(jQuery('#edit-fudesc').val());
        });
        
        jQuery('#product-functional-unit-edit-save').click(function() {
            if(jQuery('#edit-product-functional-unit-edit-value').val()) {
                jQuery('#edit-funame').val(jQuery('#edit-product-functional-unit-edit-value').val());
                jQuery('#edit-fudesc').val(jQuery('#edit-product-functional-unit-edit-desc').val());
                jQuery('#product-functional-unit-change').fadeIn();
                jQuery('#product-functional-unit-edit').hide();
                jQuery('#product-functional-unit-value').html(jQuery('#edit-funame').val());
                jQuery('#product-functional-unit-desc').html(jQuery('#edit-fudesc').val());
                if (jQuery('#edit-funame').val() == default_funame)
                    jQuery('#product-functional-unit-value-note').html("&nbsp;(default)");
                else
                    jQuery('#product-functional-unit-value-note').html("");
                jQuery('#edit-product-functional-unit-edit-value').css('border', '1px gray solid');
                jQuery('#product-functional-unit-edit .error').hide().html('');
            } else {
                jQuery('#edit-product-functional-unit-edit-value').css('border', '1px red solid');
                jQuery('#product-functional-unit-edit .error').fadeIn(400).append('Impact per functional unit is required.');
            }
        });
        
        jQuery('#product-functional-unit-edit-cancel').click(function() {
            jQuery('#edit-product-functional-unit-edit-value').css('border', '1px gray solid');
            jQuery('#product-functional-unit-edit .error').hide().html('');
            jQuery('#product-functional-unit-change').fadeIn();
            jQuery('#product-functional-unit-edit').hide();
        });
        jQuery('#Browse-img').click(()=>{
            jQuery('#edit-img-upload-upload').click();
        });
        
        jQuery('#edit-img-upload-upload').change(()=>{
            // uploadFile();
            var src = document.querySelector("#edit-img-upload-upload");
            var target = document.querySelector("#ShowImage");
            showImage(src, target);
        });
        jQuery('#Default-img').click(()=>{
            document.querySelector("#ShowImage").src = jQuery("#defaultPath").val();
            jQuery("#Icon").val(jQuery("#defaultPath").val());
        });
      
        function showImage(src, target) {
            var fr = new FileReader();
            fr.readAsDataURL(src.files[0]);
            fr.onload = function(e){
                //Initiate the JavaScript Image object.
                var image = new Image();

                //Set the Base64 string return from FileReader as source.
                image.src = e.target.result;

                //Validate the File Height and Width.
                image.onload = function () {
                    var width = this.width;
                    if (width > 675) {
                    alert("Image width should be less than 670px.");
                    return false;
                    }
                    else{
                        target.src = fr.result;
                        return true;
                    }
                };
            }    
        }
        if(location.href.indexOf('/project') > -1){
            jQuery('.nav-middle').addClass('active');
            if(location.href.indexOf('/project/edit') ==-1 && location.href.indexOf('/project/concept/add') ==-1 && location.href.indexOf('/project/concept/edit') ==-1){
                jQuery('#blue_grid').addClass('d-none');
            }else{
                jQuery('#blue_grid').addClass('p-3');
            }
        }
        jQuery('#'+jQuery('#Product-Page')?.val()+'-tab').addClass('active');
        // Execute code once the DOM is ready.
      });