
jQuery(function($) {
console.log('hey');
});
let siteBasePath = "/drupal8/web";
function delete_concept(cid,pid){
    if (confirm('Do you really want to really delete this concept?')) jQuery.ajax({
        type: "GET",
        url: `${siteBasePath}/ajax/actions/delete_concept`,
        data: "cid="+cid+"&pid="+pid,
        success: function (){
            alert('Concept was deleted.');
            //location.reload(true);
            top.location.href = `${siteBasePath}/project/concepts/`+pid;
        }
    });
}
function copy_concept(pid, cid){
    console.log('copy');
    jQuery.ajax({
        type: "GET",
        url: `${siteBasePath}/ajax/actions/copy`,
        data: "cid="+cid+"&pid="+pid,
        success: function (){
            alert('A copy of this concept has been created.');
            top.location.href = `${siteBasePath}/project/concepts/`+pid;
        }
    });
}


function set_reference_concept(pid, cid){
    jQuery.ajax({
        type: "GET",
        url: `${siteBasePath}/ajax/actions/set_reference`,
        data: "pid="+pid+"&cid="+cid,
        success: function (){
            alert('This concept is now the reference.');
            location.reload(true);

        }
    });
}

function set_final_concept(pid, cid){
    jQuery.ajax({
        type: "GET",
        url: `${siteBasePath}/ajax/actions/set_final`,
        data: "pid="+pid+"&cid="+cid,
        success: function (){
            alert('This concept has been selected as the final for this project.');
            location.reload(true);
        }
    });
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

  function project_edit_add_to_page(page) {
	jQuery('form #edit-to-page').val(page);
	jQuery('form #edit-submit').click();
  }
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
