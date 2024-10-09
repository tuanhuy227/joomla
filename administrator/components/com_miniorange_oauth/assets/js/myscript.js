
function add_css_tab(element) {
    jQuery(".mo_nav_tab_active").removeClass("mo_nav_tab_active").removeClass("active");
    jQuery(element).addClass("mo_nav_tab_active");
}

function copyToClipboard(element) { 
    var temp = jQuery("<input>");
    jQuery("body").append(temp);
    temp.val(jQuery(element).val()).select();
    document.execCommand("copy");
    temp.remove();
}

function copyToClipboard(element1 , element2) { 
    var temp = jQuery("<input>");
    jQuery("body").append(temp);
	$value = jQuery(element2).val()+jQuery(element1).val();
    temp.val($value).select();
    document.execCommand("copy");
    temp.remove();
}

function validateEmail(emailField) 
{
    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    if (reg.test(emailField.value) == false) 
    {
        document.getElementById('email_error').style.display = "block";
        document.getElementById('submit_button').disabled = true;
    } 
    else 
    {
        document.getElementById('email_error').style.display = "none";
        document.getElementById('submit_button').disabled = false;
    }
}


jQuery(document).ready(function () {
    jQuery('.premium').click(function () {
        jQuery('.nav-tabs a[href=#licensing-plans]').tab('show');
    });
});



function upgradeBtn()
{
    jQuery("#myModal").css("display","block");
}
function upgradeClose()
{
    jQuery("#myModal").css("display","none");
}
function oauth_back_to_register()
{
    jQuery('#oauth_cancel_form').submit();
}

function mo_oauth_show_proxy_form() {
	jQuery('#submit_proxy1').show();
	jQuery('#register_with_miniorange').hide();
	jQuery('#proxy_setup1').hide();
}
		
function mo_oauth_hide_proxy_form() {
	jQuery('#submit_proxy1').hide();
	jQuery('#register_with_miniorange').show();
	jQuery('#proxy_setup1').show();
	jQuery('#submit_proxy2').hide();
	jQuery('#mo_oauth_registered_page').show();
}
		
function mo_oauth_show_proxy_form2() {
	jQuery('#submit_proxy2').show();
	jQuery('#mo_oauth_registered_page').hide();
}

window.addEventListener('DOMContentLoaded', function(){
	let supportButtons=document.getElementsByClassName('moJoom-OauthClient-supportButton-SideButton');
	let supportForms  = document.getElementsByClassName('moJoom-OauthClient-supportForm');
	for(let i=0;i<supportButtons.length;i++){
	supportButtons[i].addEventListener("click",function (e) {
    if (supportForms[i].style.right != "0px") {
        supportForms[i].style.right= "0px";
        
    }
    else {
        supportForms[i].style.right= "-391px";
    }
 });
}
//  

let appSearchInput = document.getElementById('moAuthAppsearchInput');
let moAuthAppsTable = document.getElementById('moAuthAppsTable');
let allHtml='';
if(moAuthAppsTable!=null)
	allHtml         = moAuthAppsTable.innerHTML;
let allTds = document.querySelectorAll("#moAuthAppsTable tr td");
noAppFoundStr = '<tr><td>No applications found in this category, matching your search query. Please select a custom application from below OR <b><a href="#" style="cursor:pointer;text-decoration:none;" >Contact Us</a></b> </td></tr>';
if(appSearchInput!=null)
appSearchInput.onkeyup=function(e){
	let j=1;
	let htmlStr='';
	for(let i=0;i<allTds.length;i++)
	{
		if(allTds[i].attributes.moauthappselector.value.search(new RegExp(appSearchInput.value, "i"))!=-1){

			if(j%6==1 || i==allTds.length){
				htmlStr=htmlStr+'<tr>';
			}
			htmlStr = htmlStr+'<td>'+allTds[i].innerHTML+'</td>';
			if(j%6==0){
			 htmlStr=htmlStr+'</tr>'	
			}
			j++;
		}
	}
	if(appSearchInput.value=='')
		moAuthAppsTable.innerHTML=allHtml;
	else if(j==1)
		moAuthAppsTable.innerHTML=noAppFoundStr;
	else
		moAuthAppsTable.innerHTML=htmlStr;
	};
}
);