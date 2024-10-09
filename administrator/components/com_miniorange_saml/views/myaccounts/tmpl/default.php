<?php
defined('_JEXEC') or die;
/*
 * @package    miniOrange
 * @subpackage Plugins
 * @license    GNU/GPLv3
 * @copyright  Copyright 2015 miniOrange. All Rights Reserved.
*/

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('jquery.framework');

$document = Factory::getApplication()->getDocument();
$document->addScript(Uri::base() . 'components/com_miniorange_saml/assets/js/samlUtility.js');
$document->addScript(Uri::base() . 'components/com_miniorange_saml/assets/js/bootstrap-select-min.js');

// Add your custom CSS files
$document->addStyleSheet(Uri::base() . 'components/com_miniorange_saml/assets/css/mo_saml_style.css');
$document->addStyleSheet(Uri::base() . 'components/com_miniorange_saml/assets/css/bootstrap-select-min.css');
$document->addStyleSheet(Uri::base() . 'components/com_miniorange_saml/assets/css/miniorange_boot.css');

// Add Font Awesome CSS from CDN
$document->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
$cms_version = SAML_Utilities::getJoomlaCmsVersion();
if($cms_version >= 4.0)
{
    $document->addScript('https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js');
}
?>

<?php
if (!Mo_Saml_Local_Util::is_curl_installed())
{
?>
    <div id="help_curl_warning_title" class="alert alert-danger">
        <p><a target="_blank" style="cursor: pointer;" onClick="show_curl_msg()"><?php echo Text::_('COM_MINIORANGE_SAML_CURL_WARNING'); ?> <?php echo Text::_('COM_MINIORANGE_SAML_CURL_SPAN'); ?></a></p>
    </div>
    <div id="help_curl_warning_desc" class="TC_modal">
        <div class="TC_modal-content">
            <div class="mo_boot_row">
                <div class="mo_boot_col-12 mo_boot_text-center">
                    <span style="font-size: 28px;"><strong>Troubleshoot</strong></span>
                    <span class="TC_modal_close" onclick="close_curl_modal()">&times;</span>  <hr>
                </div>
                <div class="mo_boot_col-12">
                    <?php echo Text::_('COM_MINIORANGE_SAML_LIST'); ?>
                    <?php echo Text::_('COM_MINIORANGE_SAML_CONTACT'); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

$tab = "overview";
$get = Factory::getApplication()->input->get->getArray();
$test_config = isset($get['test-config']) ? true: false;
if (isset($get['tab']) && !empty($get['tab']))
{
    $tab = $get['tab'];
    ?>
    <script>
    jQuery(document).ready(function () {
            jQuery('#subhead-container').css('min-height', '55px');
            var subheadDiv = document.getElementById('subhead-container');
            var trialButton = '<div class=""> <a style="float:right;margin-right:10px;font-weight:500;border-radius:18px;background-color: #c52827 !important;color:white;border:1px solid #c52827;text-decoration:none" class="mo_boot_btn mo_boot_py-1"  href="<?php echo Route::_('index.php?option=com_miniorange_saml&tab=addon_tab')?>"><i class="fa fa-puzzle-piece mo_boot_mx-1"></i>Add-Ons</a><a style="float:right; margin-right:10px;font-weight:500;border-radius:18px;background-color: #c52827 !important;color:white;border:1px solid #c52827;text-decoration:none" class="mo_boot_btn mo_boot_py-1" href="<?php echo Route::_('index.php?option=com_miniorange_saml&tab=request_demo')?>"><i class="fa fa-envelope mo_boot_mx-1"></i>Free Trial</a>  </div> ';
            subheadDiv.innerHTML = trialButton;
        });
    </script>
       <!-- jQuery(document).ready(function () {
        jQuery('#subhead-container').removeClass('subhead');
        jQuery('#subhead-container').removeClass('mb-3');
        jQuery('#subhead-container').addClass('mb-2');
    }); -->
    <?php
}
?>
<?php
    $saml_configuration=SAML_Utilities::_get_values_from_table('#__miniorange_saml_config');
    $session = Factory::getSession();
    $session->set('show_test_config', false);
    if($test_config)
    {
        $session->set('show_test_config', true);
    }

?>

<div class="mo_boot_container-fluid mo_boot_p-2">
    <div class="mo_boot_row">
        <div id="mo_saml_nav_parent" class="mo_boot_col-sm-12 mo_boot_p-0 mo_boot_m-0"  style="display:flex;background-color:white;">
            <a id="overviewtab" class=" mo_boot_p-3  mo_nav-tab mo_nav_tab_<?php echo $tab == 'overview' ? 'active' : ''; ?>" href="#overview_plugin" onclick="add_css_tab('#overviewtab');" data-toggle="tab">
                <span><i class="fa fa-solid fa-bars"> </i></span>
                <?php echo Text::_('COM_MINIORANGE_SAML_SP_OVERVIEW');?>
            </a>
            <a id="idptab" class=" mo_boot_p-3 mo_nav-tab mo_nav_tab_<?php echo $tab == 'idp' ? 'active' : ''; ?>" href="#identity-provider" onclick="add_css_tab('#idptab');" data-toggle="tab">
                <span><i class="fa fa-solid fa-bars"></i></span>
                <?php echo Text::_('COM_MINIORANGE_SAML_IDP');?> 
            </a>
            <a id= "descriptiontab" class=" mo_boot_p-3 mo_nav-tab mo_nav_tab_<?php echo $tab == 'description' ? 'active' : ''; ?>" href="#description" onclick="add_css_tab('#descriptiontab');" data-toggle="tab">
                <span><i class="fa fa-solid fa-bars"> </i></span>
                <?php echo Text::_('COM_MINIORANGE_SAML_DESCRIPTION');?>
            </a>
            <a id= "sso_login" class=" mo_boot_p-3 mo_nav-tab mo_nav_tab_<?php echo $tab == 'sso_settings' ? 'active' : ''; ?>" href="#sso_settings" onclick="add_css_tab('#sso_login');" data-toggle="tab">
                <span><i class="fa fa-solid fa-bars"> </i></span>
                <?php echo Text::_('COM_MINIORANGE_SAML_LOGINSETTINGS');?>
            </a>
            <a id= "attributemappingtab" class=" mo_boot_p-3 mo_nav-tab mo_nav_tab_<?php echo $tab == 'attribute_mapping' ? 'active' : ''; ?>" href="#attribute-mapping" onclick="add_css_tab('#attributemappingtab');" data-toggle="tab">
                <span><i class="fa fa-solid fa-bars"> </i></span>
                <?php echo Text::_('COM_MINIORANGE_SAML_ATTRIBUTEMAPPING');?>
            </a>
            <a id="groupmappingtab" class=" mo_boot_p-3 mo_nav-tab mo_nav_tab_<?php echo $tab == 'group_mapping' ? 'active' : ''; ?>" href="#group-mapping" onclick="add_css_tab('#groupmappingtab');" data-toggle="tab">
                <span><i class="fa fa-solid fa-bars"> </i></span>
                <?php echo Text::_('COM_MINIORANGE_SAML_GROUPMAPPING');?>
            </a>
            <a id="licensingtab" class="mo_boot_p-3 mo_nav-tab mo_nav_tab_<?php echo $tab == 'licensing' ? 'active' : ''; ?>" href="#licensing-plans" onclick="add_css_tab('#licensingtab');" data-toggle="tab">
                <span><i class="fa fa-solid fa-bars"> </i></span>
                <?php echo Text::_('COM_MINIORANGE_SAML_LICENSING');?>
            </a>
          
           <?php
              if($cms_version <= 4.0)
              {
            ?>
                <a id="request_demo" class="mo_boot_p-3 mo_nav-tab mo_nav_tab_<?php echo $tab == 'request_demo' ? 'active' : ''; ?>" href="#request-demo" data-toggle="tab" onclick="add_css_tab('#request_demo');" data-toggle="tab">
                    <span><i class="fa fa-envelope"> </i></span> Free Trial
                </a>
                <a id="addon_tab" class="mo_boot_p-3 mo_nav-tab mo_nav_tab_<?php echo $tab == 'addon_tab' ? 'active' : ''; ?>" href="#addon-tab" data-toggle="tab" onclick="add_css_tab('#addon_tab');"  >
                    <span><i class="fa fa-puzzle-piece"> </i></span> Add-Ons
                </a>
            <?php
              }
            ?>
        </div>
    </div>
</div>
        
<div class=" mo_boot_mx-2 mo_boot_my-2 mo_container tab-content " id="myTabContent">
    <div id="overview_plugin" class="tab-pane <?php if ($tab == 'overview') echo 'active'; ?> ">
        <div class="mo_boot_row ">
            <?php show_plugin_overview(); ?>
        </div>
    </div>
    <div id="identity-provider" class="tab-pane <?php echo $tab == 'idp' ? 'active' : ''; ?>">
        <div class="mo_boot_row">
            <?php select_identity_provider(); ?>
        </div>
    </div>
    <div id="description" class="tab-pane <?php echo $tab == 'description' ? 'active' : ''; ?>">
        <div class="mo_boot_row">
            <?php description(); ?>
        </div>
    </div>
    <div id="sso_settings" class="tab-pane <?php echo $tab == 'sso_settings' ? 'active' : ''; ?>">
        <div class="mo_boot_row">
            <?php mo_sso_login(); ?>
        </div>
    </div>
    <div id="attribute-mapping" class="tab-pane <?php echo $tab == 'attribute_mapping' ? 'active' : ''; ?>">
        <div class="mo_boot_row">
            <?php attribute_mapping(); ?>
        </div>
    </div>
    <div id="group-mapping" class="tab-pane <?php echo $tab == 'group_mapping' ? 'active' : ''; ?>">
        <div class="mo_boot_row">
            <?php group_mapping(); ?>
        </div>
    </div>
    <div id="licensing-plans" class="tab-pane <?php echo $tab == 'licensing' ? 'active' : ''; ?>">
        <div class="mo_boot_row">
            <?php licensing_page(); ?>
        </div>
    </div>
    <div id="support-tab" class="tab-pane <?php if ($tab == 'support_tab') echo 'active'; ?>" >
        <div class="mo_boot_row">
            <?php mo_saml_local_support(); ?>
        </div>
    </div>
    <div id="addon-tab" class="tab-pane <?php if ($tab == 'addon_tab') echo 'active'; ?>">
        <div class="mo_boot_row">
            <?php add_on_description(); ?>
        </div>
    </div>
    <div id="request-demo" class="tab-pane <?php if ($tab == 'request_demo') echo 'active'; ?>">
        <div class="mo_boot_row">
            <?php request_for_demo(); ?>
        </div>
    </div>
  
  
</div>

<?php

function account_tab()
{
    $customer_details = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_customer_details');
    $login_status = $customer_details['login_status'];
    $registration_status = $customer_details['registration_status'];  
    if (!Mo_Saml_Local_Util::is_customer_registered())
    {
        mo_saml_local_login_page();
    }
    else
    {
        mo_saml_local_account_page();
    }
}

function mo_saml_local_login_page()
{
    ?>
  
    <div class="mo_boot_col-sm-12 mo_boot_mx-2" style="box-shadow: 0px 0px 15px 5px lightgray;">
        <div class="mo_boot_row mo_tab_border">
            <div class="mo_boot_col-sm-8" style="border-right:1px solid #001b4c">
                <div class="mo_boot_col-sm-12 ">
                    <div class="mo_boot_row mo_boot_mt-3">
                        <div class="mo_boot_col-lg-5">
                            <h3 class="mo_saml_form_head"><?php echo Text::_('COM_MINIORANGE_SAML_LOGIN_HEADING'); ?></h3>
                        </div>
                    </div>
                    <form name="f" method="post" action="<?php echo Route::_('index.php?option=com_miniorange_saml&task=myaccount.verifyCustomer'); ?>">
                        <div class="mo_boot_row mo_boot_mt-2">
                            <div class="mo_boot_col-sm-12 mo_boot_p-2">
                                <div class="mo_boot_row mo_boot_mt-4">
                                    <div class="mo_boot_col-sm-2  mo_boot_ml-5">
                                        <span class="mo_boot-ml-5"><?php echo Text::_('COM_MINIORANGE_SAML_EMAIL'); ?> :</span>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <input class="mo_saml_table_textbox mo_boot_form-control mo_saml_proxy_setup" type="email" name="email" required placeholder="person@example.com" value="" />
                                    </div>
                                </div>
                                <div class="mo_boot_row mo_boot_mt-4">
                                    <div class="mo_boot_col-sm-2  mo_boot_ml-5">
                                        <span class="mo_boot-ml-5"><?php echo Text::_('COM_MINIORANGE_SAML_PASSWORD'); ?> :</span>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <input class="mo_saml_table_textbox mo_boot_form-control mo_saml_proxy_setup" required type="password" name="password" placeholder="   <?php echo Text::_('COM_MINIORANGE_SAML_PASS_PLACEHOLDER'); ?>" />
                                        <a class=" mo_boot_mt-1" style="color:#2e3030e3;font-family: ui-sans-serif;float:left;cursor:pointer;" href="<?php echo Mo_saml_Local_Util::getHostname(); ?>/moas/idp/resetpassword" target="_blank"><u><?php echo Text::_('COM_MINIORANGE_SAML_FOROGET_PASS_BTN');?></u></a>
                                    </div>
                                </div>
                                <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                                    <div class=" mo_boot_text-center">
                                        <input type="submit" class="mo_boot_btn btn_cstm" value="<?php echo Text::_('COM_MINIORANGE_SAML_LOGIN_BTN'); ?>"/>
                                    </div>
                                    <div class=" mo_boot_text-center">
                                        <a class="mo_boot_mt-1" style="color:#2e3030e3;font-family: ui-sans-serif;cursor:pointer;" href="https://www.miniorange.com/businessfreetrial" target='_blank' ><u><?php echo Text::_('COM_MINIORANGE_SAML_SIGN_UP_BTN');?></u></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="option1" value="mo_saml_local_verify_customer" />
                    </form>
                </div>
            </div>
            <div class="mo_boot_col-sm-4">
                <div class="mo_boot_col-12 mo_boot_mt-4">
                    <div class="mo_boot_text-center">
                        <span class="mo_saml_login_header"><?php echo Text::_('COM_MINIORANGE_SAML_WHY_LOGIN'); ?></span><hr>
                    </div>
                    <p style="font-size:0.9rm"><?php echo Text::_('COM_MINIORANGE_SAML_LOGIN_REASON'); ?></p>
                    <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                        <div class=" mo_boot_text-center">
                            <a href="https://faq.miniorange.com/kb/joomla/" target="__blank" class="mo_boot_btn btn_cstm"><?php echo Text::_('COM_MINIORANGE_SAML_FAQ'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function show_plugin_overview()
{
    ?>
    <div class="mo_boot_col-sm-12 mo_boot_mx-2 mo_tab_border" style="box-shadow: 0px 0px 15px 5px lightgray;">
        <section class="mo_saml_section">
            <div class="mo_saml_circle"></div>
            <div class="mo_saml_content mo_boot_mx-4">
                <div class="mo_boot_text_box">
                    <h2>miniOrange SAML SP plugin for Joomla</h2>
                    <p style="font-size:14px">
                        <?php 
                            if(MoConstants::MO_SAML_SP=='ALL')
                            {
                                echo Text::_('COM_MINIORANGE_SAML_IDP_ALL');
                            }else if(MoConstants::MO_SAML_SP=='ADFS')
                            {
                                echo Text::_('COM_MINIORANGE_SAML_SP_ADFS');
                            }else if(MoConstants::MO_SAML_SP=='GOOGLEAPPS')
                            {
                                echo Text::_('COM_MINIORANGE_SAML_SP_GOOGLE_APPS');
                            }
                        ?>
                    </p>
                    <input type="button" class="mo_boot_btn btn_cstm" target="_blank" onclick="window.open('https://plugins.miniorange.com/step-by-step-guide-for-joomla-single-sign-on-sso/')" value="<?php echo Text::_('COM_MINIORANGE_SAML_GUIDES'); ?>" />
                    <a class="mo_boot_btn btn_cstm" href="<?php echo Route::_('index.php?option=com_miniorange_saml&tab=idp')?>"><?php echo Text::_('COM_MINIORANGE_SAML_CONFIGURATION'); ?></a>
                </div>
            </div>
            <div class="imgBox">
                <img style="height:348px;width:514px"src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/overview_tab.png">
            </div>
        </section>
    </div>	
    <?php
}

function mo_saml_local_account_page()
{
    $result = new Mo_saml_Local_Util();
    $result = $result->_load_db_values('#__miniorange_saml_customer_details');
    $email = $result['email'];
    $customer_key = $result['customer_key'];
    $api_key = $result['api_key'];
    $customer_token = $result['customer_token'];
    $hostname = Mo_Saml_Local_Util::getHostname();
    $joomla_version=SAML_Utilities::getJoomlaCmsVersion();
    $phpVersion = phpversion();
    $PluginVersion = SAML_Utilities::GetPluginVersion();
    ?>
    <div class="mo_boot_col-sm-12 mo_boot_mx-2" style="box-shadow: 0px 0px 15px 5px lightgray;" id="cum_pro">
        <div class="mo_boot_row mo_tab_border">
            <div class="mo_boot_col-sm-12 mo_boot_p-2">
                <div class="mo_boot_row mo_boot_mt-3">
                    <div class="mo_boot_col-lg-5">
                        <h3 class="mo_saml_form_head"><?php echo Text::_('COM_MINIORANGE_SAML_YOUR_PROFILE'); ?></h3>
                    </div>
                </div>
                <div class="mo_boot_col-sm-12">
                    <div class=" mo_boot_offset-1">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-11 alert alert-info">
                                <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden">Info</span><span style="margin-left:10px"><?php echo Text::_('COM_MINIORANGE_SAML_REGISTRAION_MSG'); ?> </span>  
                            </div>    
                        </div> 
                    </div>
                </div> 
                <div class="mo_boot_col-sm-10 mo_boot_offset-1 table-responsive mo_boot_mt-3">
                    <table class="table table-striped table-hover table-bordered ">
                        <tr>
                            <td class="mo_profile_td_h"><?php echo Text::_('COM_MINIORANGE_SAML_USERNAME'); ?></td>
                            <td class="mo_profile_td"><?php echo $email ?></td>
                        </tr>
                        <tr>
                            <td class="mo_profile_td_h"><?php echo Text::_('COM_MINIORANGE_SAML_CUSTOMER_ID'); ?></td>
                            <td class="mo_profile_td"><?php echo $customer_key ?></td>
                        </tr>
                        <tr>
                            <td class="mo_profile_td_h"><?php echo Text::_('COM_MINIORANGE_SAML_JVERSION'); ?></td>
                            <td class="mo_profile_td"><?php echo  $joomla_version ?></td>
                        </tr>
                        <tr>
                            <td class="mo_profile_td_h"><?php echo Text::_('COM_MINIORANGE_SAML_PHP_VERSION'); ?></td>
                            <td class="mo_profile_td"><?php echo  $phpVersion ?></td>
                        </tr>
                        <tr>
                            <td class="mo_profile_td_h"><?php echo Text::_('COM_MINIORANGE_SAML_PLUGIN_VERSION'); ?></td>
                            <td class="mo_profile_td"><?php echo $PluginVersion ?></td>
                        </tr>
                    </table>
                </div>
                <div class="mo_boot_text-center">
                    <input id="sp_proxy" type="button" class='mo_boot_btn btn_cstm' onclick='show_proxy_form()' value="<?php echo Text::_('COM_MINIORANGE_SAML_PROXY'); ?>"/>
                    <form class="mo_boot_d-inline-block" action="<?php echo Route::_('index.php?option=com_miniorange_saml&task=myaccount.ResetAccount'); ?>" name="reset_useraccount" method="post">
                        <input type="button"  value="<?php echo Text::_('COM_MINIORANGE_SAML_RM_ACCOUNT'); ?>" onclick='submit();' class="mo_boot_btn btn_cstm"  /> <br/>
                    </form>
                </div>
            </div>
        </div>
    </div>
  
    <div class="mo_boot_col-sm-12 mo_boot_mx-2" id="submit_proxy" style=" display:none ;" >
        <?php proxy_setup() ?>
    </div>
    <?php
}

function description()
{
    $siteUrl = Uri::root();
    $sp_base_url = '';

    $result = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_config');
    $sp_entity_id = isset($result['sp_entity_id']) ? $result['sp_entity_id'] : '';

    if($sp_entity_id == ''){
        $sp_entity_id = $siteUrl . 'plugins/authentication/miniorangesaml';
    }

    if(isset($result['sp_base_url'])){
        $sp_base_url = $result['sp_base_url'];
    }

    if (empty($sp_base_url))
        $sp_base_url = $siteUrl;

    $org_name=$result['organization_name'];
    $org_dis_name=$result['organization_display_name'];
    $org_url=$result['organization_url'];
    $tech_name=$result['tech_per_name'];
    $tech_email=$result['tech_email_add'];
    $support_name=$result['support_per_name'];
    $support_email=$result['support_email_add'];
    $licensing_page_link=Uri::base().'index.php?option=com_miniorange_saml&tab=licensing';
    mo_sticky_support();
    ?>
        <div class="mo_boot_col-sm-12 mo_boot_mx-2" style="box-shadow: 0px 0px 15px 5px lightgray;">
            <div class="mo_boot_row mo_tab_border mo_boot_p-2">
                <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-lg-5">
                            <h3 class="mo_saml_form_head"><?php echo Text::_('COM_MINIORANGE_SAML_SP_METADATA'); ?><a href="https://developers.miniorange.com/docs/joomla/saml-sso/saml-service-provider-metadata" target="_blank" class="mo_saml_know_more" title="<?php echo Text::_('COM_MINIORANGE_SAML_KNOW_MORE'); ?>"><div class="fa fa-question-circle-o"></div></a></h3>
                        </div>
                    </div>
                </div>
                
                <div class="mo_boot_col-sm-12 mo_boot_mt-5">
                    <h4 class="form-head form-head-bar"><?php echo Text::_('COM_MINIORANGE_SAML_PROVIDE_METADATA_URL'); ?></h4>
                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-2 mo_boot_col-sm-2  mo_boot_ml-5">
                            <span ><?php echo Text::_('COM_MINIORANGE_SAML_METADATA_URL'); ?> :</span>
                        </div>
                        <div class="mo_boot_col-sm-5">
                            <span id="idp_metadata_url" class=" mo_saml_highlight_background_url_note" style="float:right!important ">
                                <a  href='<?php echo $sp_base_url . '?morequest=metadata'; ?>' id='metadata-linkss' target='_blank'><?php echo '<strong>' . $sp_base_url . '?morequest=metadata </strong>'; ?></a>
                            </span> 
                        </div>
                        <div class="mo_boot_col-sm-1">
                            <em class="fa fa-lg fa-copy mo_copy_sso_url mo_copytooltip" style="float:left!important" onclick="copyToClipboard('#idp_metadata_url');" ><span class="mo_copytooltiptext copied_text"><?php echo Text::_('COM_MINIORANGE_SAML_COPY_BTN'); ?></span></em> 
                        </div>
                    </div>

                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-2 mo_boot_ml-5">
                            <span class="mo_boot-ml-5"><?php echo Text::_('COM_MINIORANGE_SAML_METADATA_FILE'); ?> :</span>
                        </div>
                        <div class="mo_boot_col-sm-5">
                            <a href="<?php echo $sp_base_url . '?morequest=download_metadata'; ?>" class="mo_boot_btn btn_cstm anchor_tag">
                                <?php echo Text::_('COM_MINIORANGE_SAML_METADATA_BTN'); ?>
                            </a>
                        </div>
                    </div>
        
                    <div class="mo_boot_mt-5 ">
                        <div class="mo_boot_text-center metadata_or" >
                            <div  style="width: 100%;height: 4.5px; ">
                                <span class="mo_boot_btn mo_saml_rounded_circle mo_boot_p-2" ><?php echo Text::_('COM_MINIORANGE_SAML_OR'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mo_boot_col-sm-12 mo_boot_mt-5">
                    <h4 class="form-head form-head-bar"><?php echo Text::_('COM_MINIORANGE_SAML_METADATA_SEC'); ?></h4>
                    <div id="mo_other_idp" class="mo_boot_p-4">
                        <table class='customtemp'>
                            <tr>
                                <td class="mo_table_td_style"><?php echo Text::_('COM_MINIORANGE_SAML_ISSUER'); ?></td>
                                <td><span id="entidy_id"><?php echo $sp_entity_id; ?></span>
                                    <em class="fa fa-pull-right  fa-lg fa-copy mo_copy mo_copytooltip" 
                                        onclick="copyToClipboard('#entidy_id');"><span class="mo_copytooltiptext copied_text"><?php echo Text::_('COM_MINIORANGE_SAML_COPY_BTN'); ?></span></em>
                                </td>
                            </tr>
                            <tr>
                                <td class="mo_table_td_style"><?php echo Text::_('COM_MINIORANGE_SAML_ASC'); ?></td>
                                <td>
                                    <span id="acs_url"><?php echo $sp_base_url . '?morequest=acs'; ?></span>
                                    <em class="fa fa-pull-right  fa-lg fa-copy mo_copy mo_copytooltip" onclick="copyToClipboard('#acs_url');"><span class="mo_copytooltiptext copied_text"><?php echo Text::_('COM_MINIORANGE_SAML_COPY_BTN'); ?></span> </em>
                                </td>
                            </tr>
                            <tr>
                                <td class="mo_table_td_style"><?php echo Text::_('COM_MINIORANGE_SAML_AUDIENCE'); ?></td>
                                <td>
                                    <span id="audience_url"><?php echo $sp_entity_id; ?></span>
                                    <em class="fa fa-pull-right  fa-lg fa-copy mo_copy mo_copytooltip"
                                        onclick="copyToClipboard('#audience_url');" ><span class="mo_copytooltiptext copied_text"><?php echo Text::_('COM_MINIORANGE_SAML_COPY_BTN'); ?></span></em>
                                </td>
                            </tr>
                            <tr>
                                <td class="mo_table_td_style"><?php echo Text::_('COM_MINIORANGE_SAML_NAMEID_FORMAT'); ?></td>
                                <td>
                                    <span id="sp_name_id_format">urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified</span>
                                    <em class="fa fa-pull-right  fa-lg fa-copy mo_copy mo_copytooltip"
                                        onclick="copyToClipboard('#sp_name_id_format');"><span class="mo_copytooltiptext copied_text"><?php echo Text::_('COM_MINIORANGE_SAML_COPY_BTN'); ?></span> <em>
                                </td>
                            </tr>
                            <tr>
                                <td class="mo_table_td_style">
                                    <?php echo Text::_('COM_MINIORANGE_SAML_SLO'); ?>
                                </td>
                                <td>
                                    <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Premium</strong></a>
                                    <img class="crown_img_small" style="float:right!important" src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/crown.webp">
                                </td>
                            </tr>
                            <tr>
                                <td class="mo_table_td_style">
                                    <?php echo Text::_('COM_MINIORANGE_SAML_DEFAULT_REALY'); ?>
                                </td>
                                <td>
                                    <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Premium</strong></a>
                                    <img class="crown_img_small" style="float:right!important" src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/crown.webp">
                                </td>
                            </tr>
                        
                            <tr>
                                <td style="font-weight:bold;padding: 15px;">
                                    <?php echo Text::_('COM_MINIORANGE_SAML_CRT'); ?>
                                </td>
                                <td>
                                    <strong> <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Premium</strong></a>
                                    <img class="crown_img_small" style="float:right!important" src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/crown.webp">
                                </td>
                            </tr>

                            <tr>
                                <td style="font-weight:bold;padding: 15px;">
                                    <?php echo Text::_('COM_MINIORANGE_SAML_CSTM_CRT'); ?>
                                </td>
                                <td>
                                    Click <a href="#" onClick="show_custom_crt_modal()">here</a> to generate custom certificate
                                    <img class="crown_img_small" style="float:right!important;" src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/crown.webp">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="mo_boot_col-sm-12">
                    <div class="metadata_or"style="width: 100%;height: 4.5px; " ></div>
                </div>
                <div class="mo_boot_col-sm-12 mo_boot_mt-5">
                    <h4 class="form-head form-head-bar "><?php echo Text::_('COM_MINIORANGE_SAML_UPDATE_ENTITY'); ?></h4>
                    <form action="<?php echo Route::_('index.php?option=com_miniorange_saml&task=myaccount.updateSPIssuerOrBaseUrl'); ?>" method="post" name="updateissueer" id="identity_provider_update_form">
                        <div class="mo_boot_row mo_boot_mt-4">
                            <div class="mo_boot_col-sm-2  mo_boot_ml-5">
                                <span class="mo_boot-ml-5"><?php echo Text::_('COM_MINIORANGE_SAML_ISSUER'); ?> :</span>
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <input class="mo_saml_table_textbox mo_boot_form-control mo_saml_proxy_setup" type="text" name="sp_entity_id" value="<?php echo $sp_entity_id; ?>" required />
                                <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_ISSUE_NOTE'); ?></span>
                            </div>
                        </div>
                        <div class="mo_boot_row mo_boot_mt-4">
                            <div class="mo_boot_col-sm-2 mo_boot_ml-5">
                                <span class="mo_boot-ml-5"><?php echo Text::_('COM_MINIORANGE_SAML_BASE_URL'); ?> :</span>
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <input class="mo_saml_table_textbox mo_boot_form-control mo_saml_proxy_setup" type="text" name="sp_base_url" value="<?php echo $sp_base_url; ?>" required />
                            </div>
                        </div>
                        <div class="mo_boot_row mo_boot_mt-4">
                            <div class="mo_boot_col-sm-12 mo_boot_text-center">
                                <input type="submit" class="mo_boot_btn btn_cstm" value="<?php echo Text::_('COM_MINIORANGE_SAML_UPDATE_BTN'); ?>"/>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="mo_boot_col-sm-12 mo_boot_mt-5">
                    <details !important>
                        <summary class="mo_saml_main_summary" ><?php echo Text::_('COM_MINIORANGE_SAML_ORG_DETAILS'); ?><sup><strong><a href='#' class='premium' onclick="moSAMLUpgrade();"> [Standard, Premium, Enterprise]</a></strong></sup></summary><hr>
                        <div class="mo_boot_ml-5">
                            <h5 class="form-head form-head-bar"><?php echo Text::_('COM_MINIORANGE_SAML_ORG'); ?></h5>
                        </div>
                        <div class="mo_boot_row mo_boot_ml-5">
                            <div class="mo_boot_col-sm-3">
                                <?php echo Text::_('COM_MINIORANGE_SAML_ORG_NAME'); ?><span class="mo_saml_required">*</span> :
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <input class="mo_saml_table_textbox mo_boot_form-control mo_saml_proxy_setup" type="text" name="organization_name" value="<?php echo $org_name; ?>" required disabled/>
                            </div>
                            <div class="mo_boot_col-sm-3 mo_boot_mt-2">
                                <?php echo Text::_('COM_MINIORANGE_SAML_DIS_NAME'); ?><span class="mo_saml_required" >*</span> :
                            </div>
                            <div class="mo_boot_col-sm-8  mo_boot_mt-2">
                                <input class="mo_saml_table_textbox mo_boot_form-control mo_saml_proxy_setup" type="text"  name="organization_display_name" value="<?php echo $org_dis_name; ?>" required  disabled/>
                            </div>
                            <div class="mo_boot_col-sm-3  mo_boot_mt-2">
                                <?php echo Text::_('COM_MINIORANGE_SAML_ORG_URL'); ?><span class="mo_saml_required" >*</span> :
                            </div>
                            <div class="mo_boot_col-sm-8  mo_boot_mt-2">
                                <input class="mo_saml_table_textbox mo_boot_form-control mo_saml_proxy_setup" type="text" name="organization_url" value="<?php echo $org_url; ?>" required  disabled/>
                            </div>
                        </div>
                        <div class="mo_boot_ml-5 mo_boot_mt-3">
                            <h5 class="form-head form-head-bar"><?php echo Text::_('COM_MINIORANGE_SAML_TECH_CONTACT'); ?></h5>
                        </div>
                        <div class="mo_boot_row mo_boot_ml-5">
                            <div class="mo_boot_col-sm-3">
                                <?php echo Text::_('COM_MINIORANGE_SAML_PERSON'); ?><span class="mo_saml_required">*</span> :
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <input class="mo_saml_table_textbox mo_boot_form-control mo_saml_proxy_setup" type="text" name="tech_per_name" value="<?php echo $tech_name; ?>" required   disabled/>
                            </div>
                            <div class="mo_boot_col-sm-3 mo_boot_mt-2">
                                <?php echo Text::_('COM_MINIORANGE_SAML_PERSON_EMAIL'); ?><span class="mo_saml_required">*</span> :
                            </div>
                            <div class="mo_boot_col-sm-8 mo_boot_mt-2">
                                <input class="mo_saml_table_textbox mo_boot_form-control mo_saml_proxy_setup" type="text" name="tech_email_add" value="<?php echo $tech_email; ?>" required  disabled/>
                            </div>
                        </div>
                        <div class="mo_boot_ml-5 mo_boot_mt-3">
                            <h5 class="form-head form-head-bar"><?php echo Text::_('COM_MINIORANGE_SAML_SUPPORT_CONTACT'); ?></h5>
                        </div>
                        <div class="mo_boot_row mo_boot_ml-5">
                            <div class="mo_boot_col-sm-3">
                                <?php echo Text::_('COM_MINIORANGE_SAML_PERSON'); ?><span class="mo_saml_required">*</span> :
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <input class="mo_saml_table_textbox mo_boot_form-control mo_saml_proxy_setup" type="text" name="support_per_name"  value="<?php echo $support_name; ?>" required  disabled />
                            </div>
                            <div class="mo_boot_col-sm-3 mo_boot_mt-2">
                                <?php echo Text::_('COM_MINIORANGE_SAML_PERSON_EMAIL'); ?><span class="mo_saml_required">*</span> :
                            </div>
                            <div class="mo_boot_col-sm-8 mo_boot_mt-2">
                                <input class="mo_saml_table_textbox mo_boot_form-control mo_saml_proxy_setup" type="text" name="support_email_add" value="<?php echo $support_email; ?>" required  disabled/>
                            </div>
                        </div>
                        <div class="mo_boot_col-sm-12 mo_boot_mt-3 mo_boot_text-center">
                            <input type="submit" class="mo_boot_btn btn_cstm" value="<?php echo Text::_('COM_MINIORANGE_SAML_UPDATE_BTN'); ?>" disabled/>
                        </div>
                    </details>
                </div>
            </div>
        </div>
        <div id="my_custom_crt_modal" class="TC_modal" >
            <div class="mo_boot_row TC_modal-content" >
                <div class="mo_boot_col-sm-12 mo_boot_mt-3" id="generate_certificate_form" style="display:none">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-10">
                            <h3 class="mo_saml_form_head"><?php echo Text::_('COM_MINIORANGE_SAML_CUSTOM_CERTIFICATE_TAB'); ?><div class="mo_tooltip"><img class="crown_img_small" style="margin-left:10px;float:right!important" src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/crown.webp"><span class="mo_tooltiptext small"><?php echo Text::sprintf('COM_MINIORANGE_SAML_AVAIABLE_FROM_ENTERPRISE',$licensing_page_link); ?></span></div></h3>
                        </div>
                        <div class="mo_boot_col-sm-2">
                            <input type="button" class="mo_boot_btn btn_cstm" value=" <?php echo Text::_('COM_MINIORANGE_SAML_BACK'); ?>" onclick = "hide_gen_cert_form()"/>           
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-3">
                            <?php echo Text::_('COM_MINIORANGE_SAML_COUNTRY_CODE'); ?><span class="mo_saml_required">*</span> :
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input class="mo_saml_table_textbox  mo_boot_form-control" type="text"  placeholder=" <?php echo Text::_('COM_MINIORANGE_SAML_COUNTRY_CODE_PLACEHOLDER'); ?>" disabled>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3">
                        <div class="mo_boot_col-sm-3">
                            <?php echo Text::_('COM_MINIORANGE_SAML_STATE'); ?><span class="mo_saml_required">*</span> :
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input class="mo_saml_table_textbox mo_boot_form-control" type="text"  placeholder=" <?php echo Text::_('COM_MINIORANGE_SAML_STATE_PLACEHOLDER'); ?>" disabled />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3">
                        <div class="mo_boot_col-sm-3">
                            <?php echo Text::_('COM_MINIORANGE_SAML_COMPANY'); ?><span class="mo_saml_required">*</span> :
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input  class="mo_saml_table_textbox mo_boot_form-control" type="text"  placeholder=" <?php echo Text::_('COM_MINIORANGE_SAML_COMPANY_PLACEHOLDER'); ?>" disabled />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3">
                        <div class="mo_boot_col-sm-3">
                            <?php echo Text::_('COM_MINIORANGE_SAML_UNIT'); ?><span class="mo_saml_required">*</span> :
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input  class="mo_saml_table_textbox mo_boot_form-control" type="text" placeholder=" <?php echo Text::_('COM_MINIORANGE_SAML_UNIT_PLACEHOLDER'); ?>" disabled />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3">
                        <div class="mo_boot_col-sm-3">
                            <?php echo Text::_('COM_MINIORANGE_SAML_COMMON'); ?><span class="mo_saml_required">*</span> :
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input  class="mo_saml_table_textbox mo_boot_form-control" type="text" placeholder=" <?php echo Text::_('COM_MINIORANGE_SAML_COMMON_PLACEHOLDER'); ?>" disabled />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3">
                        <div class="mo_boot_col-sm-3">
                            <?php echo Text::_('COM_MINIORANGE_SAML_DIGEST_ALGORITH'); ?><span class="mo_saml_required">*</span> :
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <select class="mo_saml_table_textbox mo_boot_form-control">  <?php echo Text::_('COM_MINIORANGE_SAML_VALID_DAYS'); ?>                             
                                <option>SHA512</option>
                                <option>SHA384</option>
                                <option>SHA256</option>
                                <option>SHA1</option>                            
                            </select>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3">
                        <div class="mo_boot_col-sm-3">
                            <?php echo Text::_('COM_MINIORANGE_SAML_PRIVATE_KEY'); ?><span class="mo_saml_required">*</span> :
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <select class="mo_saml_table_textbox mo_boot_form-control">  <?php echo Text::_('COM_MINIORANGE_SAML_VALID_DAYS'); ?>                             
                                <option>2048 bits</option>
                                <option>1024 bits</option>                                                               
                            </select>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3">
                        <div class="mo_boot_col-sm-3">
                            <?php echo Text::_('COM_MINIORANGE_SAML_VALID_DAYS'); ?><span class="mo_saml_required">*</span> :
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <select class="mo_saml_table_textbox mo_boot_form-control">                               
                                <option>365 <?php echo Text::_('COM_MINIORANGE_SAML_DAYS'); ?></option>                                                                                               
                                <option>180 <?php echo Text::_('COM_MINIORANGE_SAML_DAYS'); ?></option>                                                                                               
                                <option>90 <?php echo Text::_('COM_MINIORANGE_SAML_DAYS'); ?></option>                                                                                               
                                <option>45 <?php echo Text::_('COM_MINIORANGE_SAML_DAYS'); ?></option>                                                                                               
                                <option>30 <?php echo Text::_('COM_MINIORANGE_SAML_DAYS'); ?></option>                                                                                               
                                <option>15 <?php echo Text::_('COM_MINIORANGE_SAML_DAYS'); ?></option>                                                                                               
                                <option>7 <?php echo Text::_('COM_MINIORANGE_SAML_DAYS'); ?></option>                                                                                               
                            </select>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_text-center mo_boot_mt-3">
                        <div class="mo_boot_col-sm-12">
                            <input type="submit" value=" <?php echo Text::_('COM_MINIORANGE_SAML_SELF_SIGNED'); ?>" disabled class="mo_boot_btn btn_cstm"; />
                        </div>
                    </div>
                </div>
                <div class="mo_boot_col-sm-12 mo_boot_mt-3" id="mo_gen_cert" >
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-10">
                            <h3 class="mo_saml_form_head"><?php echo Text::_('COM_MINIORANGE_SAML_CUSTOM_CERTIFICATE_TAB'); ?><div class="mo_tooltip"><img class="crown_img_small" style="margin-left:10px;float:right!important" src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/crown.webp"><span class="mo_tooltiptext small"><?php echo Text::sprintf('COM_MINIORANGE_SAML_AVAIABLE_FROM_ENTERPRISE',$licensing_page_link); ?></span></div></h3>
                        </div>
                        <div class="mo_boot_col-sm-2">
                            <button class="TC_modal_close mo_boot_btn btn_cstm_red" onclick="close_custom_crt_modal()">&times;</button>
                        </div>
                        <div class="mo_boot_col-sm-12 alert alert-info" >
                            <?php echo Text::_('COM_MINIORANGE_SAML_CUSTOM_CRT_NOTE'); ?> 
                        </div>
                        <div class="mo_boot_col-sm-12 mo_boot_mt-3" id="customCertificateData"><br>
                            <div class="mo_boot_row custom_certificate_table"  >
                                <div class="mo_boot_col-sm-3">
                                        <?php echo Text::_('COM_MINIORANGE_SAML_PUBLIC_CRT'); ?>
                                        <span class="mo_saml_required">*</span>
                                </div>
                                <div class="mo_boot_col-sm-8">
                                    <textarea disabled="disabled" rows="5" cols="100" class="mo_saml_table_textbox mo_boot_w-100"></textarea>
                                </div>
                            </div>
                            <div class="mo_boot_row custom_certificate_table"  >
                                <div class="mo_boot_col-sm-3">
                                        <?php echo Text::_('COM_MINIORANGE_SAML_PRIVATE_CRT'); ?>
                                        <span class="mo_saml_required">*</span>
                                </div>
                                <div class="mo_boot_col-sm-8">
                                    <textarea disabled="disabled" rows="5" cols="100" class="mo_saml_table_textbox mo_boot_w-100"></textarea>
                                </div>
                            </div>
                            <div class="mo_boot_row mo_boot_mt-3 custom_certificate_table"  id="save_config_element">
                                <div class="mo_boot_col-sm-12 mo_boot_text-center">
                                    <input disabled="disabled" type="submit" name="submit" value=" <?php echo Text::_('COM_MINIORANGE_SAML_UPLOAD'); ?>" class="mo_boot_btn btn_cstm"/> &nbsp;&nbsp;
                                    <input type="button" name="submit" value=" <?php echo Text::_('COM_MINIORANGE_SAML_GENERATE'); ?>" class="mo_boot_btn btn_cstm" onclick="show_gen_cert_form()"/>&nbsp;&nbsp;
                                    <input disabled type="submit" name="submit" value=" <?php echo Text::_('COM_MINIORANGE_SAML_RM'); ?>" class="mo_boot_btn btn_cstm"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mo_boot_row" style="display:none">
                    <div class="mo_boot_col-12 mo_boot_text-center">
                        <span style="font-size: 28px;"><strong><?php echo Text::_('COM_MINIORANGE_SAML_TC'); ?></strong></span>
                        <span class="TC_modal_close" onclick="close_TC_modal()">&times;</span>
                    </div>    
                </div>
              
            </div>
        </div>
    <?php
}

function licensing_page()
{
	$useremail = new Mo_saml_Local_Util();
	$useremail = $useremail->_load_db_values('#__miniorange_saml_customer_details');
    if (isset($useremail)) $user_email = $useremail['email'];
    else $user_email = "xyz";

    $circle_icon = '
        <svg class="min-w-[8px] min-h-[8px]" width="8" height="8" viewBox="0 0 18 18" fill="none">
            <circle id="a89fc99c6ce659f06983e2283c1865f1" cx="9" cy="9" r="7" stroke="rgb(99 102 241)" stroke-width="4"></circle>
        </svg>
    ';
    $circle_premium_icon='
    <svg class="min-w-[8px] min-h-[8px]" width="20" height="15" viewBox="0 0 18 18" fill="none">
        <circle id="a89fc99c6ce659f06983e2283c1865f1" cx="9" cy="9" r="7" stroke="rgb(99 102 241)" stroke-width="4"></circle>
    </svg>
    ';
   
    ?>
    <div class="mo_boot_col-sm-12 mo_boot_mx-2" style="box-shadow: 0px 0px 15px 5px lightgray;">
        <div class="mo_boot_row mo_tab_border mo_boot_p-2">
            <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-lg-5">
                        <h3 class="mo_saml_form_head"><?php echo Text::_('COM_MINIORANGE_SAML_LICENSING_HEADER'); ?></h3>
                    </div>
                    <div class="mo_boot_col-lg-7">
                     <a  class="mo_boot_btn btn_cstm" style="float:right!important" href="<?php echo Route::_('index.php?option=com_miniorange_saml&tab=request_demo')?>">Free Trial</a>
                    </div>
                </div>
            </div>
            <div class="mo_boot_col-sm-12">
                <div id="mo_saml_pricing_page" class="mo_saml_pricing_page mo_boot_my-2">
                    <div class="mo_saml_pricing_snippet_grid">
                        <div class="mo_saml_pricing_card" >
                            <h5><?php echo Text::_('COM_MINIORANGE_SAML_STANDARD_HEADER');?></h5>
                            <h1>$249<span style="font-size:1rem; margin-top:5%"><i> /instance</i></span></h1>
                            <ul class="mo_boot_mt-mo-4 grow" >
                                <li class="mo_saml_feature_snippet"><span><?php echo $circle_icon;?></span><span style='font-size:14px;line-height:1.75rem'>Unlimited User Auto-Creation and Authentication</span></li>
                                <li class="mo_saml_feature_snippet"><span><?php echo $circle_icon;?></span><span style='font-size:14px;line-height:1.75rem'>Import and Export Configuration</span></li>
                                <li class="mo_saml_feature_snippet"><span><?php echo $circle_icon;?></span><span style='font-size:14px;line-height:1.75rem'>Configure SP Using Metadata XML File and URL</span></li>
                                <li class="mo_saml_feature_snippet"><span><?php echo $circle_icon;?></span><span style='font-size:14px;line-height:1.75rem'>Basic Attribute and Group Mapping</span.</li>    
                                <li class="mo_saml_feature_snippet"><span><?php echo $circle_icon;?></span><span style='font-size:14px;line-height:1.75rem'>Options to select SAML Request Binding Type</span></li>    
                                <li class="mo_saml_feature_snippet"><span><?php echo $circle_icon;?></span><span style='font-size:14px;line-height:1.75rem'>Sign SAML Request</span></li>    
                                <li class="mo_saml_feature_snippet"><span><?php echo $circle_icon;?></span><span style='font-size:14px;line-height:1.75rem'>Auto-Redirect to IdP</span></li>    
                                <li class="mo_saml_feature_snippet"><span><?php echo $circle_icon;?></span><span style='font-size:14px;line-height:1.75rem'>Default redirect URL after Login and Logout</span></li>                           
                            </ul> 
                            <button class="mo-button primary" onclick=" window.open('https://portal.miniorange.com/initializePayment?requestOrigin=joomla_saml_sso_standard_plan')"><?php echo Text::_('COM_MINIORANGE_SAML_UPGRADE_BTN');?></button>
                        </div>
                        <div class="mo_saml_pricing_card">
                            <h5><?php echo Text::_('COM_MINIORANGE_SAML_PREMIUM_HEADER');?></h5>
                            <h1>$399<span style="font-size:1rem; margin-top:5%"><i> /instance</i></span></h1>
                            <p class="mo_boot_font-weight-bold">All standard features +</p>
                            <ul class="mt-mo-4 grow" >
                                <li class="mo_saml_feature_snippet"><span><?php echo $circle_icon;?></span><span style='font-size:14px;line-height:1.75rem'>Custom Group Mapping</span></li> 
                                <li class="mo_saml_feature_snippet"><span><?php echo $circle_icon;?></span><span style='font-size:14px;line-height:1.75rem'>Profile Attribute Mapping</span></li> 
                                <li class="mo_saml_feature_snippet"><span><?php echo $circle_icon;?></span><span style='font-size:14px;line-height:1.75rem'>Single Logout</span></li>   
                                <li class="mo_saml_feature_snippet"><span><?php echo $circle_icon;?></span><span style='font-size:14px;line-height:1.75rem'>Backend SSO Login for Super-Users/administrator/Manager</span></li>    
                                <li class="mo_saml_feature_snippet"><span><?php echo $circle_icon;?></span><span style='font-size:14px;line-height:1.75rem'>Backdoor URL</span></li>                      
                            </ul>
                            <button class="mo-button primary" onclick=" window.open('https://portal.miniorange.com/initializePayment?requestOrigin=joomla_saml_sso_premium_plan')"><?php echo Text::_('COM_MINIORANGE_SAML_UPGRADE_BTN');?></button>
                        </div>

                        <div class="mo_saml_pricing_card premium">
                            <h5 class="text-white"><?php echo Text::_('COM_MINIORANGE_SAML_ENTERPRISE_HEADER');?></h5>
                            <h1 class="text-white">$449<span style="font-size:1rem; margin-top:5%"><i> /instance</i></span></h1>
                            <p class="mo_boot_font-weight-bold">All premium plan features +</p>
                            <ul class="mt-mo-4 grow" >
                                <li class="mo_saml_feature_snippet">
                                    <span style="margin-top: 0.2rem; margin-left: -8px;" >
                                        <?php echo $circle_premium_icon;?>                                 
                                    </span>
                                    <span style='font-size:14px;line-height:1.75rem'>Authenticate users from Multiple IdPs</span>
                                </li>  
                                <li class="mo_saml_feature_snippet">
                                    <span style="margin-top: 0.2rem; margin-left: -8px;" >
                                        <?php echo $circle_premium_icon;?>                                    
                                    </span>
                                    <span style='font-size:14px;line-height:1.75rem'>Auto-sync IdP Configuration from metadata</span>
                                </li>  
                                <li class="mo_saml_feature_snippet">  
                                    <span style="margin-top: 0.2rem; margin-left: -8px;" >
                                        <?php echo $circle_premium_icon;?>                                    
                                    </span></span><span style='font-size:14px;line-height:1.75rem'>Support multiple certificates of IdP</span>
                                </li> 
                                <li class="mo_saml_feature_snippet">  
                                    <span style="margin-top: 0.2rem; margin-left: -8px;" >
                                        <?php echo $circle_premium_icon;?>          
                                    </span><span style='font-size:14px;line-height:1.75rem'>User Field and Contact Mapping</span>
                                </li>  
                                <li class="mo_saml_feature_snippet">  
                                    <span style="margin-top: 0.2rem; margin-left: -8px;" >
                                        <?php echo $circle_premium_icon;?>                                    
                                    </span><span style='font-size:14px;line-height:1.75rem'>Generate Custom SP Certificate</span>
                                </li>
                                <li class="mo_saml_feature_snippet">  <span style="margin-top: 0.2rem; margin-left: -8px;" >
                                        <?php echo $circle_premium_icon;?>                                   
                                    </span><span style='font-size:14px;line-height:1.75rem'>Domain Mapping</span>
                                </li> 
                            </ul>
                            <button class="mo-button premium"  onclick=" window.open('https://portal.miniorange.com/initializePayment?requestOrigin=joomla_saml_sso_enterprise_plan')">Upgrade Now</button>
                        </div>

                        <div class="mo_saml_pricing_card " style="border:none;">
                            <h5><?php echo Text::_('COM_MINIORANGE_SAML_ALL_INCLUSIVE_HEADER');?></h5>
                            <h1>$649<span style="font-size:1rem; margin-top:5%"><i> /instance</i></span></h1>
                            <p class="mo_boot_font-weight-bold">All enterprise plan features +</p>
                            <ul class="mt-mo-4 grow"> 
                                <li class="mo_saml_feature_snippet"><span><?php echo $circle_icon;?></span><span style='font-size:14px;line-height:1.75rem'>Access to all premium add-ons</span></li>
                                <ul>
                                    <li class="mo_boot_mx-4" style="font-size:14px;line-height:1.75rem"><span style="font-weight:bold;color:#263C59;padding:2px;">-</span>SSO Login Audit</li>
                                    <li class="mo_boot_mx-4" style="font-size:14px;line-height:1.75rem"><span style="font-weight:bold;color:#263C59;padding:2px;font-size:14px;line-height:1.75rem">-</span>Role/Group Based Redirection</li>
                                    <li class="mo_boot_mx-4" style="font-size:14px;line-height:1.75rem"><span style="font-weight:bold;color:#263C59;padding:2px;font-size:14px;line-height:1.75rem">-</span>Integrate with Community Builder</li>
                                    <li class="mo_boot_mx-4" style="font-size:14px;line-height:1.75rem"><span style="font-weight:bold;color:#263C59;padding:2px;font-size:14px;line-height:1.75rem">-</span>Page & Article Restriction</li>
                                </ul>
                            </ul>
                            <button class="mo-button primary"  onclick=" window.open('https://www.miniorange.com/contact')">Get Quote</button>
                            <!-- <button class="mo-button primary" onclick="window.open('https://www.miniorange.com/contact')"><?php echo Text::_('COM_MINIORANGE_SAML_CONTACT_US');?></a> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="mo_boot_col-sm-12 mo_boot_my-2" >
                <div class=" mo_boot_offset-1">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-11 alert alert-info">
                            <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden">Info</span><span style="margin-left:10px"><?php echo Text::_('COM_MINIORANGE_BASIC_SUBSCRIPTION');?> </span>  
                        </div>    
                    </div> 
                </div>
            </div>
            <div class=" mo_boot_col-sm-12 mo_boot_my-4">
                <h4 class="form-head form-head-bar"><?php echo Text::_('COM_MINIORANGE_SAML_UPGRADE_HEADER');?></h4>
                <section id="mo_saml_section-steps" class="mo_boot_mt-4" >
                    <div class="mo_boot_col-sm-12 mo_boot_row ">
                        <div class=" mo_boot_col-sm-6 mo_works-step" style="padding-left: 40px">
                            <div style="padding-top:2px"><strong>1</strong></div>
                            <p><?php echo Text::_('COM_MINIORANGE_SAML_UPGRADE_ONE');?></p>
                        </div>
                        <div class="mo_boot_col-sm-6 mo_works-step">
                            <div style="padding-top:2px"><strong>4</strong></div>
                            <p><?php echo Text::_('COM_MINIORANGE_SAML_UPGRADE_FOUR');?></p>
                        </div>            
                    </div>

                    <div class="mo_boot_col-sm-12 mo_boot_row">
                        <div class=" mo_boot_col-sm-6 mo_works-step" style="padding-left: 40px">
                            <div style="padding-top:2px"><strong>2</strong></div>
                            <p> <?php echo Text::_('COM_MINIORANGE_SAML_UPGRADE_TWo');?> </p>
                        </div>
                        <div class="mo_boot_col-sm-6 mo_works-step">
                            <div style="padding-top:2px"><strong>5</strong></div>
                            <p><?php echo Text::_('COM_MINIORANGE_SAML_UPGRADE_FIVE');?> </p>
                        </div>         
                    </div>

                    <div class="mo_boot_col-sm-12 mo_boot_row ">
                        <div class="mo_boot_col-sm-6 mo_works-step" style="padding-left: 40px">
                            <div style="padding-top:2px"><strong>3</strong></div>
                            <p><?php echo Text::_('COM_MINIORANGE_SAML_UPGRADE_THREE');?></p>
                        </div>
                        <div class=" mo_boot_col-sm-6 mo_works-step">
                            <div style="padding-top:2px"><strong>6</strong></div>
                            <p><?php echo Text::_('COM_MINIORANGE_SAML_UPGRADE_SIX');?></p>
                        </div>       
                    </div> 
                </section>        
            </div>
            <div class=" mo_boot_col-sm-12 mo_boot_my-4">
                <h4 class="form-head form-head-bar"><?php echo Text::_('COM_MINIORANGE_FRQUENTLY_ASKED');?></h4>
                <div class="mo_boot_mx-4">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-6">
                            <h3 class="mo_saml_faq_page"><?php echo Text::_('COM_MINIORANGE_FAQ1');?></h3>
                            <div class="mo_saml_faq_body">
                                <p><?php echo Text::_('COM_MINIORANGE_FAQ1_DETAILS');?></p>
                            </div>
                            <hr class="mo_saml_hr_line">
                        </div>
                    
                        <div class="mo_boot_col-sm-6">
                            <h3 class="mo_saml_faq_page"><?php echo Text::_('COM_MINIORANGE_FAQ2');?></h3>
                            <div class="mo_saml_faq_body">
                                <p><?php echo Text::_('COM_MINIORANGE_FAQ2_DETAILS');?></p>
                            </div>
                            <hr class="mo_saml_hr_line">
                        </div>
                    </div>
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-6">
                            <h3 class="mo_saml_faq_page"><?php echo Text::_('COM_MINIORANGE_FAQ3');?></h3>
                            <div class="mo_saml_faq_body">
                                <p><?php echo Text::_('COM_MINIORANGE_FAQ3_DETAILS');?></p>
                            </div>
                            <hr class="mo_saml_hr_line">
                        </div>

                        <div class="mo_boot_col-sm-6">
                            <h3 class="mo_saml_faq_page"><?php echo Text::_('COM_MINIORANGE_FAQ4');?></h3>
                            <div class="mo_saml_faq_body">
                                <p><?php echo Text::_('COM_MINIORANGE_FAQ4_DETAILS');?></p>
                            </div>
                            <hr class="mo_saml_hr_line">
                        </div>
                    </div>
                    <div class="mo_boot_row">	
                        <div class="mo_boot_col-sm-6">
                            <h3 class="mo_saml_faq_page"><?php echo Text::_('COM_MINIORANGE_FAQ5');?></h3>
                            <div class="mo_saml_faq_body">
                                <p><?php echo Text::_('COM_MINIORANGE_FAQ5_DETAILS');?></p>
                            </div>
                            <hr class="mo_saml_hr_line">
                        </div>
                        <div class="mo_boot_col-sm-6">
                            <h3 class="mo_saml_faq_page"><?php echo Text::_('COM_MINIORANGE_FAQ6');?></h3>
                            <div class="mo_saml_faq_body">
                                <?php echo Text::_('COM_MINIORANGE_FAQ6_DETAILS');?>
                            </div>
                            <hr class="mo_saml_hr_line">
                        </div>
                    </div>
                </div>	
                <script>
                    var test = document.querySelectorAll('.mo_saml_faq_page');
                    test.forEach(function(header) {
                        header.addEventListener('click', function() {
                            var body = this.nextElementSibling;
                            body.style.display = body.style.display === 'none' || body.style.display =="" ? 'block' : 'none';
                        });
                    });
                </script>
            </div>

        </div>
    </div>

    <?php
}


function group_mapping()
{

    $saml_db_values = new Mo_saml_Local_Util();
    $role_mapping = $saml_db_values->_load_db_values('#__miniorange_saml_role_mapping');
    $role_mapping_key_value = array();
    $attribute = $saml_db_values->_load_db_values('#__miniorange_saml_config');

    if ($attribute) {
        $group_attr = $attribute['grp'];
    } else {
        $group_attr = '';
    }

    $mapping_value_default='Registered';

    $enable_role_mapping = 0;
    if (isset($role_mapping['enable_saml_role_mapping'])) $enable_role_mapping = $role_mapping['enable_saml_role_mapping'];
    
    $db = Factory::getDbo();
    $db->setQuery($db->getQuery(true)
        ->select('*')
        ->from("#__usergroups"));
    $groups = $db->loadRowList();
    $licensing_page_link=Uri::base().'index.php?option=com_miniorange_saml&tab=licensing';
    mo_sticky_support();
    ?>

    <div class="mo_boot_col-sm-12 mo_boot_mx-2" style="box-shadow: 0px 0px 15px 5px lightgray;">
        <div class="mo_boot_row mo_tab_border mo_boot_p-2">
            <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-lg-5">
                        <h3 class="mo_saml_form_head"><?php echo Text::_('COM_MINIORANGE_SAML_GROUP_MAPPING_TAB'); ?><a href="https://developers.miniorange.com/docs/joomla/saml-sso/saml-group-mapping" target="_blank" class="mo_saml_know_more" title="<?php echo Text::_('COM_MINIORANGE_SAML_KNOW_MORE'); ?>"><div class="fa fa-question-circle-o"></div></a></h3>
                    </div>
                </div>
            </div>
       
            <div class="mo_boot_col-sm-12 mo_boot_mt-5">
                <h4 class="form-head form-head-bar"><?php echo Text::_('COM_MINIORANGE_SAML_PREMIUM_VERSIONS_FEATURE'); ?><div class="mo_tooltip"><img class="crown_img_small" style="margin-left:10px;" src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/crown.webp"><span class="mo_tooltiptext small"><?php echo Text::sprintf('COM_MINIORANGE_SAML_AVAIABLE_FROM_STANDARD',$licensing_page_link); ?></span></div></h4>
                <div class="mo_boot_offset-1 mo_boot_mt-5">
                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-1">
                            <label class="mo_saml_switch">
                                <input type="checkbox" disabled>
                                <span class="mo_saml_slider"></span>
                            </label>
                        </div>
                        <div class="mo_boot_col-sm-9" style="float:right">
                            <?php echo Text::_('COM_MINIORANGE_SAML_CHECK_ONE'); ?><br>
                            <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_CHECK_ONE_NOTE'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="mo_boot_offset-1 mo_boot_mt-3">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-1">
                            <label class="mo_saml_switch">
                                <input type="checkbox" disabled>
                                <span class="mo_saml_slider"></span>
                            </label>
                        </div>
                        <div class="mo_boot_col-sm-9" style="float:right">
                            <?php echo Text::_('COM_MINIORANGE_SAML_CHECK_TWO'); ?>
                        </div>
                    </div>
                </div>
                <div class="mo_boot_offset-1 mo_boot_mt-3">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-1">
                            <label class="mo_saml_switch">
                                <input type="checkbox" disabled>
                                <span class="mo_saml_slider"></span>
                            </label>
                        </div>
                        <div class="mo_boot_col-sm-9" style="float:right">
                            <?php echo Text::_('COM_MINIORANGE_SAML_CHECK_THREE'); ?>
                        </div>
                    </div>
                </div>
                <div class="mo_boot_offset-1 mo_boot_mt-3">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-1">
                            <label class="mo_saml_switch">
                                <input type="checkbox" disabled>
                                <span class="mo_saml_slider"></span>
                            </label>
                        </div>
                        <div class="mo_boot_col-sm-9" style="float:right">
                            <?php echo Text::_('COM_MINIORANGE_SAML_CHECK_FOUR'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mo_boot_col-sm-12 mo_boot_mt-5">
                <h4 class="form-head form-head-bar"><?php echo Text::_('COM_MINIORANGE_SAML_GROUP_MAPPING'); ?><div class="mo_tooltip"><img class="crown_img_small" style="margin-left:10px;" src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/crown.webp"><span class="mo_tooltiptext small"><?php echo Text::sprintf('COM_MINIORANGE_SAML_AVAIABLE_FROM_PRM',$licensing_page_link); ?></span></div></h4>
                <div class=" mo_boot_offset-1">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-11 alert alert-info">
                            <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden">Info</span><span style="margin-left:10px"><?php echo Text::_('COM_MINIORANGE_SAML_GROUP_MAPPING_NOTE'); ?> </span>  
                        </div>    
                    </div> 
                </div>
                <div class="mo_boot_offset-1 mo_boot_mt-5">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-4">
                            <?php echo Text::_('COM_MINIORANGE_SAML_DEFAULT_GROUP'); ?> :
                        </div>
                        <div class="mo_boot_col-sm-7">
                            <select class="mo_boot_form-control" readonly name="mapping_value_default" style="width:100%" id="default_group_mapping" >
                                <?php

                                    foreach ($groups as $group) {
                                        if ($group[4] != 'Super Users') {
                                            
                                            if ($mapping_value_default == $group[4]) echo '<option selected="selected" value = "' . $group[0] . '">' . $group[4] . '</option>';
                                            else echo '<option  value = "' . $group[0] . '">' . $group[4] . '</option>';
                                        }
                                    }
                                
                                ?>
                            </select>
                            <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_DEFAULT_GROUP_NOTE'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="mo_boot_offset-1 mo_boot_mt-3">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-4">
                            <?php echo Text::_('COM_MINIORANGE_SAML_GROUP'); ?> :
                        </div>
                        <div class="mo_boot_col-sm-7">
                            <input disabled class="mo_saml_table_textbox mo_boot_form-control" type="text" required placeholder="<?php echo Text::_('COM_MINIORANGE_SAML_GROUP_ATTRIBUTE_NAME'); ?>"  />
                            <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_GROUP_NOTE'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mo_boot_col-sm-12 mo_boot_mt-5">
                <h4 class="form-head form-head-bar"><?php echo Text::_('COM_MINIORANGE_SAML_GROUP_ATTRIBUTE_TABLE'); ?><div class="mo_tooltip"><img class="crown_img_small" style="margin-left:10px;" src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/crown.webp"><span class="mo_tooltiptext small"><?php echo Text::sprintf('COM_MINIORANGE_SAML_AVAIABLE_FROM_PRM',$licensing_page_link); ?></span></div></h4>
                <div class="mo_boot_mt-5">
                    <div class=" mo_boot_offset-1">
                        <div class="mo_boot_row ">
                            <div class="mo_boot_col-sm-5 ">
                                <?php echo Text::_('COM_MINIORANGE_SAML_GROUP_MAP_HEADER1'); ?>
                            </div>
                            <div class="mo_boot_col-sm-5">
                                <?php echo Text::_('COM_MINIORANGE_SAML_GROUP_MAP_HEADRER2'); ?>
                            </div>
                            <div class="mo_boot_col-sm-2 ">
                                <input type="button" class="mo_boot_btn btn_cstm mo_group_mapping_btn" disabled value="+" />
                            </div>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-5">
                            <select class="mo_boot_form-control" readonly name="mapping_value_default" style="width:100%" id="default_group_mapping" >
                                <?php

                                    foreach ($groups as $group) {
                                        if ($group[4] != 'Super Users') {
                                            
                                            if ($mapping_value_default == $group[4]) echo '<option selected="selected" value = "' . $group[0] . '">' . $group[4] . '</option>';
                                            else echo '<option  value = "' . $group[0] . '">' . $group[4] . '</option>';
                                        }
                                    }
                                
                                ?>
                            </select>
                        </div>
                        <div class="mo_boot_col-sm-5">
                            <input disabled type="text" class="mo_saml_table_textbox mo_boot_form-control " placeholder=" <?php echo Text::_('COM_MINIORANGE_SAML_GROUP_MAPPING_PLACEHOLDER'); ?>" />
                        </div>
                        <div class="mo_boot_col-sm-2 ">
                            <input type="button"  class="mo_boot_btn btn_cstm_red mo_group_mapping_btn" disabled value="-" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="mo_boot_col-sm-10 mo_boot_mt-5">
                <div class="mo_boot_text-center">
                    <input type="submit" class="mo_boot_btn btn_cstm" disabled value="<?php echo Text::_('COM_MINIORANGE_SAML_SAVE_BTN'); ?>">
                </div>
            </div>
        </div>
    </div>

    <?php

}

function mo_sso_login()
{

    $siteUrl = Uri::root();
    $sp_base_url = $siteUrl;
    $button_style="{
        border: 1px solid rgba(0, 0, 0, 0.2);
        color: #fff;
        background-color: #226a8b !important;
        padding: 4px 12px;
        border-radius: 3px;
    }";
    $main_menu_link=Uri::base().'index.php?option=com_menus&view=items&menutype=mainmenu';
    
    $licensing_page_link=Uri::base().'index.php?option=com_miniorange_saml&tab=licensing';
    mo_sticky_support();
    ?>
     <div class="mo_boot_col-sm-12 mo_boot_mx-2" style="box-shadow: 0px 0px 15px 5px lightgray;">
        <div class="mo_boot_row mo_tab_border mo_boot_p-2">
            <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-lg-5">
                        <h3 class="mo_saml_form_head"><?php echo Text::_('COM_MINIORANGE_SAML_LOGIN_SETTING_TAB'); ?><a href="https://developers.miniorange.com/docs/joomla/saml-sso/saml-redirection-and-sso-links" target="_blank" class="mo_saml_know_more" title="<?php echo Text::_('COM_MINIORANGE_SAML_KNOW_MORE'); ?>"><div class="fa fa-question-circle-o"></div></a></h3>
                    </div>
                </div>
            </div>
            <div class="mo_boot_col-sm-12 mo_boot_mt-5">
                <h4 class="form-head form-head-bar"><?php echo Text::_('COM_MINIORANGE_SAML_SSO_LINK'); ?></h4>
                <div class="mo_boot_offset-1"><?php echo Text::sprintf('COM_MINIORANGE_SAML_SSO_LINK_FIRST_STEPS',$main_menu_link); ?></div>
                <div class="mo_boot_row mo_boot_offset-1" >
                    <div class="mo_boot_col-sm-2">
                        <span style="float:right!important;padding: 0.55em!important"><?php echo Text::_('COM_MINIORANGE_SAML_SSO_URL'); ?></span>
                    </div>
                    <div class="mo_boot_col-sm-8">
                        <div class="mo_saml_highlight_background_url_note">
                            <div class="mo_boot_row">
                                <div class="mo_boot_col-10">
                                    <span id="show_sso_login_url" style="color:#2a69b8">
                                        <strong><?php echo  $sp_base_url . '?morequest=sso'; ?></strong>
                                    </span>
                                </div>
                                <div class="mo_boot_col-2">
                                    <em class="fa fa-lg fa-copy mo_copy_sso_login_url mo_copytooltip" onclick="copyToClipboard('#show_sso_login_url');"><span class="mo_copytooltiptext copied_text"><?php echo Text::_('COM_MINIORANGE_SAML_COPY_BTN'); ?></span> </em>   
                                </div>
                            </div>
                        </div>  
                    </div>
                </div>
                <div class="mo_boot_offset-1 mo_boot_mt-2"><?php echo Text::_('COM_MINIORANGE_SAML_SSO_LINK_SECOND_STEPS'); ?></div>
            </div>
            
            <div class="mo_boot_col-sm-12 mo_boot_mt-5 mo_boot_p-2">
                <h4 class="form-head form-head-bar"><?php echo Text::_('COM_MINIORANGE_SAML_PREMIUM_VERSIONS_FEATURE'); ?><div class="mo_tooltip"><img class="crown_img_small" style="margin-left:10px;" src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/crown.webp"><span class="mo_tooltiptext small"><?php echo Text::sprintf('COM_MINIORANGE_SAML_AVAIABLE_FROM_STANDARD',$licensing_page_link); ?></span></div></h4>
                <div class="mo_boot_offset-1 mo_boot_mt-5">
                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-1">
                            <label class="mo_saml_switch">
                                <input type="checkbox" disabled>
                                <span class="mo_saml_slider"></span>
                            </label>
                        </div>
                        <div class="mo_boot_col-sm-9" style="float:right">
                            <?php echo Text::_('COM_MINIORANGE_SAML_SSO_FEATURE_ONE'); ?><br>
                            <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_SSO_FEATURE_ONE_NOTE'); ?></span>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-1">
                            <label class="mo_saml_switch">
                                <input type="checkbox" disabled>
                                <span class="mo_saml_slider"></span>
                            </label>
                        </div>
                        <div class="mo_boot_col-sm-9" style="float:right">
                            <?php echo Text::_('COM_MINIORANGE_SAML_SSO_FEATURE_TWO'); ?><br>
                            <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_SSO_FEATURE_TWO_NOTE'); ?></span>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-1">
                            <label class="mo_saml_switch">
                                <input type="checkbox" disabled>
                                <span class="mo_saml_slider"></span>
                            </label>
                        </div>
                        <div class="mo_boot_col-sm-9" style="float:right">
                            <?php echo Text::_('COM_MINIORANGE_SAML_SSO_FEATURE_THREE'); ?><br>
                            <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_SSO_FEATURE_THREE_NOTE'); ?></span>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-1">
                            <label class="mo_saml_switch">
                                <input type="checkbox" disabled>
                                <span class="mo_saml_slider"></span>
                            </label>
                        </div>
                        <div class="mo_boot_col-sm-9" style="float:right">
                            <?php echo Text::_('COM_MINIORANGE_SAML_SSO_FEATURE_FIVE'); ?><br>
                            <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_SSO_FEATURE_FIVE_NOTE'); ?></span>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-1">
                            <label class="mo_saml_switch">
                                <input type="checkbox" disabled>
                                <span class="mo_saml_slider"></span>
                            </label>
                        </div>
                        <div class="mo_boot_col-sm-9" style="float:right">
                            <?php echo Text::_('COM_MINIORANGE_SAML_SSO_FEATURE_FOUR'); ?><br>
                            <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_SSO_FEATURE_FOUR_NOTE'); ?></span>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-1">
                            <label class="mo_saml_switch">
                                <input type="checkbox" disabled>
                                <span class="mo_saml_slider"></span>
                            </label>
                        </div>
                        <div class="mo_boot_col-sm-9" style="float:right">
                            <?php echo Text::_('COM_MINIORANGE_SAML_SSO_FEATURE_SIX'); ?><br>
                            <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_SSO_FEATURE_SIX_NOTE'); ?></span>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-1">
                            <label class="mo_saml_switch">
                                <input type="checkbox" disabled>
                                <span class="mo_saml_slider"></span>
                            </label>
                        </div>
                        <div class="mo_boot_col-sm-9" style="float:right">
                            <?php echo Text::_('COM_MINIORANGE_SAML_SSO_FEATURE_SEVEN'); ?><br>
                            <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_SSO_FEATURE_SEVEN_NOTE'); ?></span>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-1">
                            <label class="mo_saml_switch">
                                <input type="checkbox" disabled>
                                <span class="mo_saml_slider"></span>
                            </label>
                        </div>
                        <div class="mo_boot_col-sm-9" style="float:right">
                            <?php echo Text::_('COM_MINIORANGE_SAML_SSO_BTN'); ?><br>
                            <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_SSO_FEATURE_SEVEN_NOTE'); ?></span>
                        </div>
                    </div>
                    
                </div>
                <div class="mo_boot_row mo_boot_mt-5">
                    <div class="mo_boot_col-sm-12 mo_boot_text-center">
                        <input type="submit" class="mo_boot_btn btn_cstm" disabled value="<?php echo Text::_('COM_MINIORANGE_SAML_SAVE_BTN'); ?>">
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <?php
}

function attribute_mapping()
{
    $licensing_page_link=Uri::base().'index.php?option=com_miniorange_saml&tab=licensing';
    mo_sticky_support();
    ?>
    <div class="mo_boot_col-sm-12 mo_boot_mx-2" style="box-shadow: 0px 0px 15px 5px lightgray;">
        <div class="mo_boot_row mo_tab_border mo_boot_p-2">
            <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-lg-5">
                        <h3 class="mo_saml_form_head"><?php echo Text::_('COM_MINIORANGE_SAML_ATTRIBUTE_MAPPING_TAB'); ?><a href="https://developers.miniorange.com/docs/joomla/saml-sso/saml-attribute-mapping" target="_blank" class="mo_saml_know_more" title="<?php echo Text::_('COM_MINIORANGE_SAML_KNOW_MORE'); ?>"><div class="fa fa-question-circle-o"></div></a></h3>
                    </div>
                </div>
            </div>
            <div class="mo_boot_col-sm-12 mo_boot_mt-5">
                <div class="mo_boot_row mo_boot_offset-1">
                    <div class="mo_boot_col-sm-1">
                        <label class="mo_saml_switch">
                            <input type="checkbox" disabled>
                            <span class="mo_saml_slider"></span>
                        </label>
                    </div>
                    <div class="mo_boot_col-sm-9" style="float:left">
                        <?php echo Text::_('COM_MINIORANGE_SAML_ATTRIBUTE_MAPPING_CHECKBOX'); ?><div class="mo_tooltip"><img class="crown_img_small" style="margin-left:10px;" src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/crown.webp"><span class="mo_tooltiptext small"><?php echo Text::sprintf('COM_MINIORANGE_SAML_AVAIABLE_FROM_PRM',$licensing_page_link); ?></span></div><br>
                        <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_ATTRIBUTE_MAPPING_CHECKBOX_NOTE'); ?></span>
                    </div>
                </div>
            </div>
            <div class="mo_boot_col-sm-12 mo_boot_mt-5">
                <h4 class="form-head form-head-bar"><?php echo Text::_('COM_MINIORANGE_SAML_BASIC_ATTRIBUTE_MAPPING'); ?><div class="mo_tooltip"><img class="crown_img_small" style="margin-left:10px;" src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/crown.webp"><span class="mo_tooltiptext small"><?php echo Text::sprintf('COM_MINIORANGE_SAML_FEATURE_AVAIABLE_FROM_STANDARD',$licensing_page_link); ?></span></div></h4>
                <div class="alert alert-info">
                    <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden">Info</span><span style="margin-left:5px"><?php echo Text::_('COM_MINIORANGE_SAML_ATTRIBUTE_MAPPING_NOTE'); ?>  </span>           
                </div>
                <div class="mo_boot_row mo_boot_mt-4">
                    <div class="mo_boot_col-sm-5 ">
                        <span class="mo_boot_offset-3"><?php echo Text::_('COM_MINIORANGE_SAML_ATTRIBUTE_USERNAME'); ?> :</span>
                    </div>
                    <div class="mo_boot_col-sm-6">
                        <input disabled class="mo_saml_table_textbox mo_boot_form-control" type="text" name="username"required placeholder="NameID" value="NameID" />
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-2">
                    <div class="mo_boot_col-sm-5 ">
                        <span class="mo_boot_offset-3"><?php echo Text::_('COM_MINIORANGE_SAML_ATTRIBUTE_EMAIL'); ?> :</span>
                    </div>
                    <div class="mo_boot_col-sm-6">
                        <input disabled class="mo_saml_table_textbox mo_boot_form-control" type="text" name="email"required placeholder="NameID" value="NameID" />
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-2">
                    <div class="mo_boot_col-sm-5">
                        <span class="mo_boot_offset-3"><?php echo Text::_('COM_MINIORANGE_SAML_ATTRIBUTE_NAME'); ?> :</span>
                    </div>
                    <div class="mo_boot_col-sm-6">
                        <input disabled class="mo_saml_table_textbox mo_boot_form-control" type="text" name="username"required placeholder="NameID" value="Enter the attribute name for Name" />
                    </div>
                </div>
            </div>
            <div class="mo_boot_col-sm-12 mo_boot_mt-5">
                <h4 class="form-head form-head-bar"><?php echo Text::_('COM_MINIORANGE_SAML_PROFILE_ATTRIBUTE_MAPPING'); ?><div class="mo_tooltip"><img class="crown_img_small" style="margin-left:10px;" src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/crown.webp"><span class="mo_tooltiptext small"><?php echo Text::sprintf('COM_MINIORANGE_SAML_AVAIABLE_FROM_PRM',$licensing_page_link); ?></span></div></h4>
                <div class="alert alert-info">
                    <?php echo Text::_('COM_MINIORANGE_SAML_PROFILE_ATTRIBUTE_NOTE2'); ?>        
                </div>
                <div class="mo_boot_p-4">
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-5 mo_boot_text-center">
                            <?php echo Text::_('COM_MINIORANGE_SAML_PROFILE_ATTRIBUTE_HEADER'); ?>
                        </div>
                        <div class="mo_boot_col-sm-5 mo_boot_text-center">
                            <?php echo Text::_('COM_MINIORANGE_SAML_IDP_ATTRIBUTE'); ?>
                        </div>
                        <div class="mo_boot_col-sm-2 mo_boot_text-center">
                            <input type="button" class="mo_boot_btn btn_cstm mo_group_mapping_btn" disabled value="+" />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-5">
                            <input disabled type="text" class="mo_saml_table_textbox mo_boot_form-control " />
                        </div>
                        <div class="mo_boot_col-sm-5">
                            <input disabled type="text" class="mo_saml_table_textbox mo_boot_form-control " />
                        </div>
                        <div class="mo_boot_col-sm-2 mo_boot_text-center">
                            <input type="button" class="mo_boot_btn btn_cstm_red mo_group_mapping_btn" disabled value="-" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="mo_boot_col-sm-12 mo_boot_mt-5">
                <h4 class="form-head form-head-bar"><?php echo Text::_('COM_MINIORANGE_SAML_FIELD_ATTRIBUTE_MAPPING'); ?><div class="mo_tooltip"><img class="crown_img_small" style="margin-left:10px;" src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/crown.webp"><span class="mo_tooltiptext small"><?php echo Text::sprintf('COM_MINIORANGE_SAML_AVAIABLE_FROM_PRM',$licensing_page_link); ?></span></div></h4>
                <div class="alert alert-info">
                    <?php echo Text::_('COM_MINIORANGE_SAML_FIELD_ATTRIBUTE_NOTE2'); ?>        
                </div>
                <div class="mo_boot_p-4">
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-5 mo_boot_text-center">
                            <?php echo Text::_('COM_MINIORANGE_SAML_FIELD_ATTRIBUTE_HEADER'); ?>
                        </div>
                        <div class="mo_boot_col-sm-5 mo_boot_text-center">
                            <?php echo Text::_('COM_MINIORANGE_SAML_IDP_ATTRIBUTE'); ?>
                        </div>
                        <div class="mo_boot_col-sm-2 mo_boot_text-center">
                            <input type="button" class="mo_boot_btn btn_cstm mo_group_mapping_btn" disabled value="+" />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-5">
                            <input disabled type="text" class="mo_saml_table_textbox mo_boot_form-control " />
                        </div>
                        <div class="mo_boot_col-sm-5">
                            <input disabled type="text" class="mo_saml_table_textbox mo_boot_form-control " />
                        </div>
                        <div class="mo_boot_col-sm-2 mo_boot_text-center">
                            <input type="button" class="mo_boot_btn btn_cstm_red mo_group_mapping_btn" disabled value="-" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="mo_boot_col-sm-12 mo_boot_mt-5">
                <h4 class="form-head form-head-bar"><?php echo Text::_('COM_MINIORANGE_SAML_CONTACT_ATTRIBUTE_MAPPING'); ?><div class="mo_tooltip"><img class="crown_img_small" style="margin-left:10px;" src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/crown.webp"><span class="mo_tooltiptext small"><?php echo Text::sprintf('COM_MINIORANGE_SAML_AVAIABLE_FROM_PRM',$licensing_page_link); ?></span></div></h4>
                <div class="alert alert-info">
                    <?php echo Text::_('COM_MINIORANGE_SAML_CONTACT_ATTRIBUTE_NOTE2'); ?>        
                </div>
                <div class="mo_boot_p-4">
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-5 mo_boot_text-center">
                            <?php echo Text::_('COM_MINIORANGE_SAML_CONTACT_ATTRIBUTE_HEADER'); ?>
                        </div>
                        <div class="mo_boot_col-sm-5 mo_boot_text-center">
                            <?php echo Text::_('COM_MINIORANGE_SAML_IDP_ATTRIBUTE'); ?>
                        </div>
                        <div class="mo_boot_col-sm-2 mo_boot_text-center">
                            <input type="button" class="mo_boot_btn btn_cstm mo_group_mapping_btn" disabled value="+" />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-5">
                            <input disabled type="text" class="mo_saml_table_textbox mo_boot_form-control " />
                        </div>
                        <div class="mo_boot_col-sm-5">
                            <input disabled type="text" class="mo_saml_table_textbox mo_boot_form-control " />
                        </div>
                        <div class="mo_boot_col-sm-2 mo_boot_text-center">
                            <input type="button" class="mo_boot_btn btn_cstm_red mo_group_mapping_btn" disabled value="-" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="mo_boot_col-sm-10 mo_boot_mt-5">
                <div class="mo_boot_text-center">
                    <input type="submit" class="mo_boot_btn btn_cstm" disabled value="<?php echo Text::_('COM_MINIORANGE_SAML_SAVE_BTN'); ?>">
                </div>
            </div>
        </div>
    </div>
    <?php
}

function proxy_setup()
{
    $proxy = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_proxy_setup');
    $proxy_host_name = isset($proxy['proxy_host_name']) ? $proxy['proxy_host_name'] : '';
    $port_number = isset($proxy['port_number']) ? $proxy['port_number'] : '';
    $username = isset($proxy['username']) ? $proxy['username'] : '';
    $password = isset($proxy['password']) ? base64_decode($proxy['password']) : '';
    ?>
     <div style="box-shadow: 0px 0px 15px 5px lightgray;" id="mo_sp_proxy_config">
        <div class="mo_boot_row mo_tab_border">
            <div class="mo_boot_col-sm-12 mo_boot_p-2">
                <div class="mo_boot_row mo_boot_mt-3">
                    <div class="mo_boot_col-sm-6 ">
                        <h3 class="mo_saml_form_head"><?php echo Text::_('COM_MINIORANGE_SAML_PROXY_SERVER'); ?></h3>
                    </div>
                    <div class="mo_boot_col-sm-6">
                        <input type="button" style="float:right" class=" mo_boot_btn btn_cstm" value="<?php echo Text::_('COM_MINIORANGE_SAML_CANCEL'); ?>" onclick = "hide_proxy_form();"/>
                    </div>
                </div>
            </div>
            <div class="mo_boot_col-sm-12">
                <div class=" mo_boot_offset-1">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-11 alert alert-info">
                            <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden">Info</span><span style="margin-left:10px"><?php echo Text::_('COM_MINIORANGE_SAML_PROXY_SERVER_NOTE'); ?> </span>  
                        </div>    
                    </div> 
                </div>
            </div>    
            <div class="mo_boot_col-sm-12 mo_boot_p-2">
                <form action="<?php echo Route::_('index.php?option=com_miniorange_saml&task=myaccount.proxyConfig'); ?>" name="proxy_form" method="post">
                    <input type="hidden" name="option1" value="mo_saml_save_proxy_setting" />
                    <div class="mo_boot_offset-1 mo_boot_mt-2 ">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-3">
                                <?php echo Text::_('COM_MINIORANGE_SAML_PROXY_HOST_NAME'); ?> :
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <input type="text" name="mo_proxy_host" placeholder="<?php echo Text::_('COM_MINIORANGE_SAML_HOST_PLACEHOLDER'); ?>" class="mo_saml_proxy_setup mo_boot_form-control" value="<?php echo $proxy_host_name ?>" required/>
                            </div>
                        </div>  
                    </div>
                    <div class="mo_boot_offset-1 mo_boot_mt-2">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-3">
                                <?php echo Text::_('COM_MINIORANGE_SAML_PORT_NUMBER'); ?> :
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <input type="number" name="mo_proxy_port" placeholder="<?php echo Text::_('COM_MINIORANGE_SAML_PORT_NUMBER_PLACEHOLDER'); ?>" class="mo_boot_form-control mo_saml_proxy_setup" value="<?php echo $port_number ?>" required/>
                            </div>
                        </div>  
                    </div>
                    <div class="mo_boot_offset-1 mo_boot_mt-2">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-3">
                                <?php echo Text::_('COM_MINIORANGE_SAML_PORT_NUMBER'); ?> :
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <input type="text" name="mo_proxy_username" placeholder="<?php echo Text::_('COM_MINIORANGE_SAML_PROXY_USERNAME'); ?>" class="mo_boot_form-control mo_saml_proxy_setup" value="<?php echo $username ?>" />
                            </div>
                        </div>  
                    </div>
                    <div class="mo_boot_offset-1 mo_boot_mt-2">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-3">
                                <?php echo Text::_('COM_MINIORANGE_SAML_PASSWORD'); ?> :
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <input type="password" name="mo_proxy_password" placeholder="<?php echo Text::_('COM_MINIORANGE_SAML_PROXY_PASSWORD'); ?>" class="mo_boot_form-control mo_saml_proxy_setup" value="<?php echo $password ?>">
                            </div>
                        </div>  
                    </div>
                    <div class="mo_boot_row mo_boot_text-center mo_boot_mt-3">
                        <div class="mo_boot_col-sm-12">
                            <input type="submit" value="<?php echo Text::_('COM_MINIORANGE_SAML_SAVE_BTN'); ?>" class="mo_boot_btn btn_cstm" />
                            <input type="button" onclick="resetProxy()" value="<?php echo Text::_('COM_MINIORANGE_SAML_PROXY_RESET_BTN'); ?>"  class="mo_boot_btn btn_cstm" />
                        </div>
                    </div>
            
                </form>
                <form action="<?php echo Route::_('index.php?option=com_miniorange_saml&task=myaccount.proxyConfigReset'); ?>" id="reset_proxy" name="proxy_form1" method="post">
                </form>
            </div>
        </div>
    </div>
    <?php
}


function request_for_demo()
{
    $current_user = Factory::getUser();
    $result = new Mo_saml_Local_Util();
    $result = $result->_load_db_values('#__miniorange_saml_customer_details');
    $admin_email = isset($result['email']) ? $result['email'] : '';
    if ($admin_email == '') $admin_email = $current_user->email;
  
    ?>

    <div class="mo_boot_col-sm-12 mo_boot_mx-2" style="box-shadow: 0px 0px 15px 5px lightgray;">
        <div class="mo_boot_row mo_tab_border mo_boot_p-2">
            <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-lg-5">
                        <h3 class="mo_saml_form_head"><?php echo Text::_('COM_MINIORANGE_SAML_TRIAL_TAB'); ?></h3>
                    </div>
                </div>
            </div>
            <div class="mo_boot_col-sm-12">
                <div class=" mo_boot_offset-1">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-11 alert alert-info">
                            <span><?php echo Text::_('COM_MINIORANGE_SAML_TRIAL_DESC'); ?> </span>
                        </div>    
                    </div> 
                </div>
            </div>  
            <div class="mo_boot_col-sm-12 mo_boot_p-2">
                <form  name="demo_request" method="post" action="<?php echo Route::_('index.php?option=com_miniorange_saml&task=myaccount.requestForTrialPlan');?>">
                    <input type="hidden" name="option1" value="mo_saml_login_send_query"/>
                    <div class="mo_boot_offset-1 mo_boot_mt-2 ">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-3">
                                <?php echo Text::_('COM_MINIORANGE_SAML_EMAIL'); ?> :
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <input  type="email" class="mo_saml_table_textbox mo_boot_form-control" name="email" value="<?php echo $admin_email; ?>" placeholder="person@example.com" required />
                            </div>
                        </div>  
                    </div>
                    <div class="mo_boot_offset-1 mo_boot_mt-2 ">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-3">
                                <?php echo Text::_('COM_MINIORANGE_SAML_REQUEST_TRIAL'); ?> :
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <select required class="mo_boot_form-control mo_saml_proxy_setup"  name="plan">
                                    <option disabled selected style="text-align: center">----------------------- <?php echo Text::_('COM_MINIORANGE_SAML_SELECT'); ?> -----------------------</option>
                                    <option value="Joomla SAML Standard Plugin">Joomla SAML SP Standard Plugin</option>
                                    <option value="Joomla SAML Premium Plugin">Joomla SAML SP Premium Plugin</option>
                                    <option value="Joomla SAML Enterprise Plugin">Joomla SAML SP Enterprise Plugin</option>
                                    <option value="Not Sure"> <?php echo Text::_('COM_MINIORANGE_SAML_NOT_SURE'); ?></option>
                                </select>
                            </div>
                        </div>  
                    </div>
                    <div class="mo_boot_offset-1 mo_boot_mt-2 ">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-3">
                                <?php echo Text::_('COM_MINIORANGE_SAML_DESC'); ?> :
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <textarea  name="description" class="mo_boot_form-text-control mo_saml_proxy_setup" style="border-radius:4px;resize: vertical;width:100%;" cols="52" rows="7" onkeyup="mo_saml_valid(this)"
                                    onblur="mo_saml_valid(this)" onkeypress="mo_saml_valid(this)" required placeholder=" <?php echo Text::_('COM_MINIORANGE_SAML_TRIAL_ASSISTANCE'); ?>"></textarea>
                        </div>  
                    </div>
                    <div class="mo_boot_row mo_boot_text-center mo_boot_mt-3">
                        <div class="mo_boot_col-sm-12">
                            <input type="submit" value="<?php echo Text::_('COM_MINIORANGE_SAML_TC_BTN'); ?>" class="mo_boot_btn btn_cstm" />
                        </div>
                    </div>
                </form>
            </div> 
        </div>
    </div>
    <?php
}


function select_identity_provider()
{
    $attribute = new Mo_saml_Local_Util();
    $attribute = $attribute->_load_db_values('#__miniorange_saml_config');
    $idp_entity_id = "";
    $single_signon_service_url = "";
    $name_id_format = "";
    $certificate = "";
    $dynamicLink="Login with IDP";
    $siteUrl = Uri::root();
    $sp_base_url = $siteUrl;

    $session = Factory::getSession();
    $current_state=$session->get('show_test_config');
    if($current_state)
    {
        ?>
        <script>
            jQuery(document).ready(function () {
                var elem = document.getElementById("test-config");
                elem.scrollIntoView();
            });
        </script>
        <?php
        $session->set('show_test_config', false);
        }
    if (isset($attribute['idp_entity_id']))
    {
        $idp_entity_id = $attribute['idp_entity_id'];
        $single_signon_service_url = $attribute['single_signon_service_url'];
        $name_id_format = $attribute['name_id_format'];
        $certificate = $attribute['certificate'];
    }
    $isAuthEnabled = PluginHelper::isEnabled('authentication', 'miniorangesaml');
    $isSystemEnabled = PluginHelper::isEnabled('system', 'samlredirect');
    if (!$isSystemEnabled || !$isAuthEnabled)
    {
        ?>
        <div id="system-message-container">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <div class="alert alert-error">
                <h4 class="alert-heading"><?php echo Text::_('COM_MINIORANGE_SAML_WARNING'); ?>Warning!</h4>
                <div class="alert-message">
                    <?php echo Text::_('COM_MINIORANGE_SAML_WARNING_MSG'); ?>
                </div>
            </div>
        </div>
        <?php
    }
    
     $setup_guides=json_decode(SAML_Utilities::setupGuides(),true);
     $guide_count = count($setup_guides);
     mo_sticky_support();
    ?>

    <div class="mo_boot_col-sm-12 mo_boot_mx-2" style="box-shadow: 0px 0px 15px 5px lightgray;">
        <div class="mo_boot_row">
            <div class="mo_boot_col-sm-8 mo_tab_border" style="border-right:1px solid #001b4c">
                <div class="mo_boot_col-sm-12 mo_boot_p-2">
                    <form action="<?php echo Route::_('index.php?option=com_miniorange_saml&task=myaccount.saveConfig'); ?>" method="post" name="adminForm" id="identity_provider_settings_form" enctype="multipart/form-data">
                        <input type="hidden" name="option1" value="mo_saml_save_config">
                        <div class="mo_boot_row mo_boot_mt-3" >
                            <div class="mo_boot_col-lg-5">
                                <h3 class="mo_saml_form_head"><?php echo Text::_('COM_MINIORANGE_SAML_SERVICE_PROVIDER_SETUP'); ?> <a href="https://developers.miniorange.com/docs/joomla/saml-sso/saml-service-provider-setup" target="_blank" class="mo_saml_know_more" title="<?php echo Text::_('COM_MINIORANGE_SAML_KNOW_MORE'); ?>"><i class="fa fa-question-circle-o"></i></a></h3>
                            </div>
                            <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                                <ul class="switch_tab_sp mo_boot_text-center mo_boot_p-2">
                                    <li class="mo_saml_current_tab" id="manual_configuration"><a href="#" id="mo_saml_idp_manual_tab" class="mo_saml_bs_btn" onclick="hide_metadata_form()"><?php echo Text::_('COM_MINIORANGE_SAML_MANUAL_CONFIG'); ?></a></li>
                                    <li class="mo_boot_col-sm-2"><?php echo Text::_('COM_MINIORANGE_SAML_OR'); ?></li>
                                    <li class="" id="auto_configuration"><a href="#" id="mo_saml_upload_idp_tab" class="mo_saml_bs_btn" onclick="show_metadata_form()"><?php echo Text::_('COM_MINIORANGE_SAML_UPLOAD_METADATA_TAB'); ?></a></li>
                                </ul>
                            </div>
                        </div> 
                        <div id="idpdata" class="mo_boot_mt-4">
                            <div class="mo_boot_row mo_boot_mt-3" id="sp_entity_id_idp">
                                <div class="mo_boot_col-sm-4">
                                    <span class="saml_sp_label_css"><?php echo Text::_('COM_MINIORANGE_SAML_IDP_ISSUER'); ?><span class="mo_saml_required">*</span></span>   
                                </div>
                                <div class="mo_boot_col-sm-8">
                                    <input type="text" class="mo_boot_form-control mo_boot_was-validated mo_saml_proxy_setup" name="idp_entity_id" placeholder="<?php echo Text::_('COM_MINIORANGE_SAML_ISSUER_PLACEHOLDER'); ?>" value="<?php echo $idp_entity_id; ?>" required />
                                    <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_ISSUER_TIP'); ?></span>
                                </div>
                            </div>
                            <div class="mo_boot_row mo_boot_mt-3" id="sp_nameid_format_idp">
                                <div class="mo_boot_col-sm-4">
                                    <?php echo Text::_('COM_MINIORANGE_SAML_NAMEID_FORMAT'); ?>
                                </div>
                                <div class="mo_boot_col-sm-8">
                                    <select class="mo_boot_form-control mo_saml_proxy_setup" id="name_id_format" name="name_id_format">
                                        <option value="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress"
                                            <?php if ($name_id_format == "urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress") echo 'selected = "selected"' ?>>
                                            urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress
                                        </option>
                                        <option value="urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified"
                                            <?php if ($name_id_format == "urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified") echo 'selected = "selected"' ?>>
                                            urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified
                                        </option>
                                    </select>
                                    <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_NAMEID_TIP'); ?></span>
                                </div>
                            </div>
                            
                            <div class="mo_boot_row mo_boot_mt-3" id="sp_sso_url_idp">
                                <div class="mo_boot_col-sm-4">
                                    <?php echo Text::_('COM_MINIORANGE_SAML_IDP_SSO_URL'); ?><span class="mo_saml_required">*</span> 
                                </div>
                                <div class="mo_boot_col-sm-8">
                                    <input class="mo_boot_was-validated mo_boot_form-control mo_saml_proxy_setup" type="url" placeholder="Single Sign-On Service URL (Http-Redirect) binding of your IdP" name="single_signon_service_url"  value="<?php echo $single_signon_service_url; ?>" required />
                                    <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_IDP_SSO_TIP'); ?></span>
                                </div>
                            </div>
                            <div class="mo_boot_row mo_boot_mt-3" id="sp_certificate_idp">
                                <div class="mo_boot_col-sm-4">
                                    <?php echo Text::_('COM_MINIORANGE_SAML_ADD_CRT'); ?>
                                </div>
                                <div class="mo_boot_col-sm-8">
                                    <div class="mo_boot_row">
                                        <div class="mo_boot_col-lg-4">
                                            <label><input type="radio" name="cert"  value="text_cert" CHECKED ><?php echo Text::_('COM_MINIORANGE_SAML_ENTER_TEXT'); ?></label>
                                        </div>
                                        <div class="mo_boot_col-lg-5">
                                            <label><input type="radio" name="cert"  value="upload_cert" > <?php echo Text::_('COM_MINIORANGE_SAML_UPLOAD_CRT'); ?></label>
                                        </div>
                                    </div>
                                    <div class="upload_cert selectt" >
                                        <div class="mo_saml_border">
                                                <input type="file" id="myFile" name="myFile" class="mo_certficate_file" >
                                        </div>
                                        <span id="uploaded_cert"></span>
                                    </div>
                                    <div class="text_cert selectt">
                                        <textarea rows="5" cols="80" name="certificate" class="mo_boot_form-text-control mo_saml_proxy_setup" placeholder="Format of Certificat
---BEGIN CERTIFICATE---
XXXXXXXXXXXXXX
---END CERTIFICATE---                                            "><?php echo $certificate; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="mo_boot_row mo_boot_mt-4" id="saml_login">
                                <div class="mo_boot_col-sm-4">
                                    <?php echo Text::_('COM_MINIORANGE_SAML_ENABLE_BTN'); ?>
                                </div>
                                <div class="mo_boot_col-sm-1">
                                    <label class="mo_saml_switch">
                                        <input type="checkbox" id ="login_link_check" name="login_link_check"  onclick="showLink()" value="1"
                                                <?php 
                                                    $count = isset($attribute['login_link_check']) ? $attribute['login_link_check'] : "";
                                                    $dynamicLink=isset($attribute['dynamic_link']) && !empty($attribute['dynamic_link']) ? $attribute['dynamic_link'] : "";
                                                    if($count ==1)                        
                                                        echo 'checked="checked"';                           
                                                    else
                                                        $dynamicLink="Login with your IDP";
                                                ?>
                                        >
                                        <span class="mo_saml_slider"></span>
                                    </label>
                                </div>
                                <div class="mo_boot_col-sm-7">
                                 <input type="text" id="dynamicText" name="dynamic_link" placeholder="Enter button name eg. Login with IDP" value="<?php echo $dynamicLink; ?>" class="mo_boot_form-control mo_boot_p-3" >
                                    <?php
                                        if($count!=1)
                                        {
                                            echo '<script>document.getElementById("dynamicText").style.display="none"</script>';
                                        }
                                    ?>
                                </div>
                                
                            </div>
                            <div class="mo_boot_row" >
                                <div class="mo_boot_col-sm-4">
                                </div>
                                <div class="mo_boot_col-sm-8">
                                    <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_ADD_LINK'); ?></span>
                                </div>
                            </div>
                            <div class="mo_boot_row mo_boot_mt-4" >
                                <div class="mo_boot_col-sm-4">
                                    <?php echo Text::_('COM_MINIORANGE_SAML_SSO_URL'); ?>
                                </div>
                                <div class="mo_boot_col-sm-8">
                                    <div class="mo_saml_highlight_background_url_note">
                                        <div class="mo_boot_row">
                                            <div class="mo_boot_col-10">
                                                <span id="show_sso_url" style="color:#2a69b8">
                                                    <strong><?php echo  $sp_base_url . '?morequest=sso'; ?></strong>
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-2">
                                                <em class="fa fa-lg fa-copy mo_copy_sso_login_url mo_copytooltip" onclick="copyToClipboard('#show_sso_url');"><span class="mo_copytooltiptext copied_text"><?php echo Text::_('COM_MINIORANGE_SAML_COPY_BTN'); ?></span> </em>   
                                            </div>
                                        </div>
                                    </div>  
                                    <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_SSO_URL_TIP'); ?></span>
                                </div>
                            </div><br>
                            <details !important>
                                <summary class="mo_saml_main_summary" ><?php echo Text::_('COM_MINIORANGE_SAML_PREMIUM_VERSIONS_FEATURE'); ?><sup><strong><a href='#' class='premium' onclick="moSAMLUpgrade();"> [Standard, Premium, Enterprise]</a></strong></sup></summary><hr>
                                <div class="mo_boot_row mo_boot_mt-3" id="sp_slo_idp">
                                    <div class="mo_boot_col-sm-4">
                                        <?php echo Text::_('COM_MINIORANGE_SAML_IDP_SLO'); ?> <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext small"><?php echo Text::_('COM_MINIORANGE_SAML_SLO_TIP'); ?></span></div></strong>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <input class="mo_saml_table_textbox mo_boot_form-control" type="text" name="single_logout_url" placeholder="Single Logout URL" disabled>
                                    </div>
                                </div>
                                <div class="mo_boot_row mo_boot_mt-3">
                                    <div class="mo_boot_col-sm-4">
                                        <?php echo Text::_('COM_MINIORANGE_SAML_SIGN_ALGO'); ?> <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext"><?php echo Text::_('COM_MINIORANGE_SAML_SIGN_ALGO_TIP'); ?></span></div>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <select class="mo_boot_form-control mo_saml_proxy_setup" readonly>
                                            <option>sha256</option>
                                            <option>sha384</option>
                                            <option>sha512</option>
                                            <option>sha1</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mo_boot_row mo_boot_mt-3" id="sp_binding_type">
                                    <div class="mo_boot_col-sm-4">
                                       <?php echo Text::_('COM_MINIORANGE_SAML_SELECT_BIND'); ?>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <input type="radio" name="miniorange_saml_idp_sso_binding" value="HttpRedirect" checked=1 aria-invalid="false" disabled> <span><?php echo Text::_('COM_MINIORANGE_SAML_BIND_ONE'); ?></span><br>
                                        <input type="radio"  name="miniorange_saml_idp_sso_binding" value="HttpPost" aria-invalid="false" disabled> <span><?php echo Text::_('COM_MINIORANGE_SAML_BIND_TWO'); ?> </span>
                                    </div>
                                </div>
                                <div class="mo_boot_row mo_boot_mt-3" id="sp_saml_request_idp">
                                    <div class="mo_boot_col-sm-4">
                                        <?php echo Text::_('COM_MINIORANGE_SAML_SIGN_SLO'); ?> 
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <label class="mo_saml_switch">
                                            <input type="checkbox" disabled>
                                            <span class="mo_saml_slider"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="mo_boot_row mo_boot_mt-3" id="sp_saml_context_class">
                                    <div class="mo_boot_col-sm-4">
                                        <?php echo Text::_('COM_MINIORANGE_SAML_CONTEXT'); ?> <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext"><?php echo Text::_('COM_MINIORANGE_SAML_CONTEXT_TIP'); ?></span></div>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <select class="mo_boot_form-control mo_saml_proxy_setup"  readonly>
                                            <option>PasswordProtectedTransport</option>
                                            <option>Password</option>
                                            <option>Unspecified</option>
                                            <option>TLS Client</option>
                                            <option>X.509 Certificate</option>

                                        </select>
                                    </div>
                                </div><br>
                            </details>
                            <div class="mo_boot_row mo_boot_mt-5">
                                <div class="mo_boot_col-sm-12 mo_boot_text-center">
                                    <input type="hidden" value="<?php echo  $sp_base_url . 'administrator/index.php?morequest=sso&q=test_config'; ?>" id="test_config_url">
                                    <input type="hidden" value="" id="testarati">
                                    <input type="submit" class="mo_boot_btn btn_cstm " value="<?php echo Text::_('COM_MINIORANGE_SAML_SAVE_BTN'); ?>"/>
                                    <input  type="button" id='test-config' <?php if ($idp_entity_id) echo "enabled";else echo "disabled"; ?> title='<?php echo Text::_('COM_MINIORANGE_SAML_TEST_CONFIG_TITLE'); ?>' class="mo_boot_btn btn_cstm " onclick='showTestWindow()' value="<?php echo Text::_('COM_MINIORANGE_SAML_TEST_CONFIG'); ?>">
                                    <input type="button" class="mo_boot_btn btn_cstm "  <?php if ($idp_entity_id) echo "enabled";else echo "disabled"; ?>  onclick="jQuery('#mo_sp_exp_exportconfig').submit();" value="<?php echo Text::_('COM_MINIORANGE_SAML_EXPORT_CONFIG'); ?>"> 
                                </div>
                            </div>
                        </div>
                    </form>
                    <form name="f" id="mo_sp_exp_exportconfig"  method="post" action="<?php echo Route::_('index.php?option=com_miniorange_saml&task=myaccount.importexport'); ?>">
                    </form>
                    <div class="mo_boot_row mo_boot_mt-5 mo_boot_mt-3 mo_boot_py-3 mo_boot_px-2" id="upload_metadata_form" style="display:none ;">
                        <div class="mo_boot_col-sm-12 mo_boot_mt-1">
                            <form action="<?php echo Route::_('index.php?option=com_miniorange_saml&task=myaccount.handle_upload_metadata'); ?>" name="metadataForm" method="post" id="IDP_meatadata_form" enctype="multipart/form-data">
                                <div class="mo_boot_row mo_boot_mt-2">
                                    <div class="mo_boot_col-sm-3">
                                        <input id="mo_saml_upload_metadata_form_action" type="hidden" name="option1" value="upload_metadata" />
                                        <?php echo Text::_('COM_MINIORANGE_SAML_UPLOAD_MEATADATA_BTN'); ?>  :
                                    </div>
                                    <div class="mo_boot_col-sm-6">
                                        <input type="hidden" name="action"  value="upload_metadata" />
                                        <input type="file"  id="metadata_uploaded_file" class="mo_boot_form-control-file"  name="metadata_file" />
                                    </div>
                                    <div class="mo_boot_col-sm-3">
                                        <button type="button" class="mo_boot_btn btn_cstm" id="upload_metadata_file"  name="option1" method="post" style="float:right!important"><svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
										<path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
										<path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z" />
									    </svg>&nbsp;&nbsp;<?php echo Text::_('COM_MINIORANGE_SAML_UPLOAD'); ?></button>
                                    </div>
                                </div>
                                <div class="mo_boot_mt-5 ">
                                    <div class="mo_boot_text-center metadata_or  " >
                                        <div  style="width: 100%;height: 4.5px; ">
                                            <span class="mo_boot_btn  mo_saml_rounded_circle mo_boot_p-2"><?php echo Text::_('COM_MINIORANGE_SAML_OR'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mo_boot_row mo_boot_mt-5">
                                    <div class="mo_boot_col-sm-3">
                                        <input type="hidden" name="action" value="fetch_metadata" />
                                       <?php echo Text::_('COM_MINIORANGE_SAML_ENTER_URL'); ?>:
                                    </div>
                                    <div class="mo_boot_col-sm-6">
                                        <input type="url" id="metadata_url" name="metadata_url" placeholder=" <?php echo Text::_('COM_MINIORANGE_SAML_ENTER_METADATA_URL'); ?>" class="mo_boot_form-control"/>
                                    </div>
                                    <div class="mo_boot_col-sm-3 mo_boot_text-center">
                                        <button type="button" class=" mo_boot_float-lg-right mo_boot_btn btn_cstm" name="option1" method="post" id="fetch_metadata">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M3.5 6a.5.5 0 0 0-.5.5v8a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-8a.5.5 0 0 0-.5-.5h-2a.5.5 0 0 1 0-1h2A1.5 1.5 0 0 1 14 6.5v8a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-8A1.5 1.5 0 0 1 3.5 5h2a.5.5 0 0 1 0 1h-2z"></path>
                                            <path fill-rule="evenodd" d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"></path>
                                            </svg> <?php echo Text::_('COM_MINIORANGE_SAML_FETCH_METADATA'); ?>
                                        </button>
                                    </div>
                                </div>
                                <details !important open class="mo_boot_mt-5">
                                    <summary class="mo_saml_main_summary" ><?php echo Text::_('COM_MINIORANGE_SAML_PREMIUM_VERSIONS_FEATURE'); ?><sup><strong><a href='#' class='premium' onclick="moSAMLUpgrade();"> [ Enterprise]</a></strong></sup></summary><hr>
                                    <div class="mo_boot_row mo_boot_mt-5">
                                        <div class="mo_boot_col-sm-4">
                                            <?php echo Text::_('COM_MINIORANGE_SAML_SYNC_METADATA'); ?> :
                                        </div>
                                        <div class="mo_boot_col-sm-8">
                                            <label class="mo_saml_switch">
                                                <input type="checkbox" disabled>
                                                <span class="mo_saml_slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mo_boot_row mo_boot_mt-3">
                                        <div class="mo_boot_col-sm-4">
                                            <?php echo Text::_('COM_MINIORANGE_SAML_SELECT_METADATA_SYNC_DURATION'); ?> : 
                                        </div>
                                        <div class="mo_boot_col-sm-8">
                                            <select name = "sync_interval" class="mo_boot_form-control" readonly>
                                                <option value = "hourly"> <?php echo Text::_('COM_MINIORANGE_SAML_SYNC_HR'); ?></option>
                                                <option value = "daily"> <?php echo Text::_('COM_MINIORANGE_SAML_SYNC_DAILY'); ?></option>
                                                <option value = "weekly"> <?php echo Text::_('COM_MINIORANGE_SAML_SYNC_WEEKLY'); ?></option>
                                                <option value = "monthly"> <?php echo Text::_('COM_MINIORANGE_SAML_SYNC_MONTHLY'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mo_boot_row mo_boot_mt-2">
                                        <div class="mo_boot_col-sm-4">
                                        </div>
                                        <div class="mo_boot_col-sm-8">
                                            <span class="small"><strong><?php echo Text::_('COM_MINIORANGE_SAML_NOTE'); ?>: </strong><?php echo Text::_('COM_MINIORANGE_SAML_SELECT_METADATA_SYNC'); ?></span>
                                        </div>
                                    </div>
                                </details>
                            </form>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="mo_boot_col-sm-4 mo_tab_border">
                <div class=" mo_boot_m-0 mo_boot_p-0">
                    <div class="mo_boot_col-sm-12 mo_boot_p-2">
                        <div class="mo_setup_guide_title mo_boot_text-center">
                            <strong>Identity Provider Setup Guides</strong>
                        </div>
                    </div>
                </div>
                <div class="mo_boot_col-sm-12  mo_boot_m-0 mo_boot_px-4 mo_boot_py-4" >
                    <?php 
                    for($i=1;$i<$guide_count;$i+=2)
                    {
                    ?>
                    <div class="mo_boot_row mo_boot_p-3" style="border-bottom: 0.0625rem #868383 solid" >
                        <div class="mo_boot_col-sm-6 mo_boot_text-center">
                            <strong><a href="<?php  echo $setup_guides[$i]['link']; ?>" target="_blank" style="color: #fe7e00;"><?php  echo $setup_guides[$i]['name']; ?></a></strong>
                        </div><hr>
                        <div class="mo_boot_col-sm-6 mo_boot_text-center">
                            <strong><a href="<?php  echo $setup_guides[$i+1]['link']; ?>" target="_blank" style="color: #fe7e00;"><?php  echo $setup_guides[$i+1]['name']; ?></a></strong>
                        </div>
                    </div>
                    <?php   
                    }
                    ?>
                    <div class="mo_boot_row mo_boot_p-3">
                        <div class="mo_boot_col-sm-12 mo_boot_text-center">
                            <strong><a href="<?php  echo $setup_guides[25]['link']; ?>" target="_blank" style="color: #fe7e00;"><?php  echo $setup_guides[25]['name']; ?></a></strong>
                        </div>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_p-2">
                    <div class="mo_boot_col-sm-12 mo_boot_text-center">
                        <a href="https://www.youtube.com/playlist?list=PL2vweZ-PcNpdkpUxUzUCo66tZsEHJJDRl"  target="_blank">
                            <span class="mo_boot_btn btn_cstm"><?php echo Text::_('COM_MINIORANGE_SAML_VIDEOS'); ?></span>
                        </a> 
                        <a href="https://faq.miniorange.com/kb/joomla/"  target="_blank">
                            <span class="mo_boot_btn btn_cstm" style="margin-left:5px"><?php echo Text::_('COM_MINIORANGE_SAML_FAQ'); ?></span>
                        </a> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function mo_saml_local_support()
{

    $current_user = Factory::getUser();
    $result       = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_customer_details');
    $admin_email  = isset($result['email']) ? $result['email'] : '';
    $admin_phone  = isset($result['admin_phone']) ? $result['admin_phone'] : '';
	if($admin_email == '')
		$admin_email = $current_user->email;
	?>
    
    <div class="mo_boot_col-sm-12 mo_boot_mx-2" style="box-shadow: 0px 0px 15px 5px lightgray;">
        <div class="mo_boot_row mo_tab_border mo_boot_p-2">
            <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-lg-5">
                        <h3 class="mo_saml_form_head"><?php echo Text::_('COM_MINIORANGE_SAML_SUPPORT'); ?></h3>
                    </div>
                </div>
            </div>
            <div class="mo_boot_col-sm-12">
                <div class=" mo_boot_offset-1">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-11 alert alert-info">
                            <span><?php echo Text::_('COM_MINIORANGE_SAML_SUPPORT_DESC'); ?> </span>
                        </div>    
                    </div> 
                </div>
            </div> 
            <div class="mo_boot_col-sm-12 mo_boot_p-2">
                <form  name="f" method="post" action="<?php echo Route::_('index.php?option=com_miniorange_saml&task=myaccount.contactUs');?>">
                    <input type="hidden" name="option1" value="mo_saml_login_send_query"/>
                    <div class="mo_boot_offset-1 mo_boot_mt-2 ">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-3">
                                <?php echo Text::_('COM_MINIORANGE_SAML_EMAIL'); ?> :
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <input type="email" class="mo_saml_table_textbox mo_boot_form-control mo_saml_proxy_setup" name="query_email" value="<?php echo $admin_email; ?>" placeholder="<?php echo Text::_('COM_MINIORANGE_SAML_ENTER_EMAIL'); ?>" required />
                            </div>
                        </div>  
                    </div>
                    <div class="mo_boot_offset-1 mo_boot_mt-2 ">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-3">
                                <?php echo Text::_('COM_MINIORANGE_SAML_SUPPORT_NUMBER'); ?> :
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <input type="text" class="mo_saml_table_textbox mo_boot_form-control mo_saml_proxy_setup" name="query_phone" pattern="[\+]\d{11,14}|[\+]\d{1,4}([\s]{0,1})(\d{0}|\d{9,10})" value="<?php echo $admin_phone; ?>" placeholder="<?php echo Text::_('COM_MINIORANGE_SAML_PHONE_PLACEHOLDER'); ?>"/>
                            </div>
                        </div>  
                    </div>
                    <div class="mo_boot_offset-1 mo_boot_mt-2 ">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-3">
                                <?php echo Text::_('COM_MINIORANGE_SAML_SUPPORT_QUERY'); ?> :
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <textarea  name="mo_saml_query_support" class="mo_boot_form-text-control mo_saml_proxy_setup" cols="52" rows="7" required placeholder="<?php echo Text::_('COM_MINIORANGE_SAML_WRITE_QUERY'); ?>"></textarea>
                            </div>
                        </div>  
                    </div>
                    <div class="mo_boot_row mo_boot_text-center mo_boot_mt-3">
                        <div class="mo_boot_col-sm-12">
                            <input type="submit" name="send_query" value="<?php echo Text::_('COM_MINIORANGE_SAML_SUBMIT_QUERY'); ?>" class="mo_boot_btn btn_cstm" />
                        </div>
                    </div>
                </form>
            </div> 
        </div>
    </div>
	<div id="sp_support_saml" style="display:none">
		<div class="mo_boot_row mo_boot_p-3 mo_tab_border">
			<div class="mo_boot_col-sm-12">
				<div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12 mo_boot_p-2">
                        <h4><?php echo Text::_('COM_MINIORANGE_SAML_SUPPORT'); ?></h4>
                    </div>
				</div>
				<hr>
			</div>
			<div class="mo_boot_col-sm-12">
				<form  name="f" method="post" action="<?php echo Route::_('index.php?option=com_miniorange_saml&task=myaccount.contactUs');?>">
                    <div class="mo_boot_col-sm-12">	
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-2">
                                <img src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/phone.svg" width="27" height="27"  alt="Phone Image"> 
                            </div>
                            <div class="mo_boot_col-sm-10">
                                <p><strong><?php echo Text::_('COM_MINIORANGE_SAML_SUPPORT_NOTE'); ?></strong></p><br>
                            </div>
                            
                        </div>
                    </div>
                    <p><?php echo Text::_('COM_MINIORANGE_SAML_SUPPORT_DESC'); ?></p>
                    <div class="mo_boot_row mo_boot_text-center">
                        <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                            <input type="email" class="mo_saml_table_textbox mo_boot_form-control mo_saml_proxy_setup" name="query_email" value="<?php echo $admin_email; ?>" placeholder="<?php echo Text::_('COM_MINIORANGE_SAML_ENTER_EMAIL'); ?>" required />
                        </div>
                        <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                            <input type="text" class="mo_saml_table_textbox mo_boot_form-control mo_saml_proxy_setup" name="query_phone" pattern="[\+]\d{11,14}|[\+]\d{1,4}([\s]{0,1})(\d{0}|\d{9,10})" value="<?php echo $admin_phone; ?>" placeholder="<?php echo Text::_('COM_MINIORANGE_SAML_PHONE_PLACEHOLDER'); ?>"/>
                        </div>
                        <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                            <textarea  name="mo_saml_query_support" class="mo_boot_form-text-control mo_saml_proxy_setup" cols="52" rows="7" required placeholder="<?php echo Text::_('COM_MINIORANGE_SAML_WRITE_QUERY'); ?>"></textarea>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_text-center mo_boot_mt-3">
                        <div class="mo_boot_col-sm-12">
                            <input type="hidden" name="option1" value="mo_saml_login_send_query"/>
                            <input type="submit" name="send_query" value="<?php echo Text::_('COM_MINIORANGE_SAML_SUBMIT_QUERY'); ?>" class="mo_boot_btn mo_boot_btn-success" />
                        </div>
                    </div><hr>
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-12">
                            <p><br><?php echo Text::_('COM_MINIORANGE_SAML_SUPPORT_EMAIL'); ?> <a style="word-wrap:break-word!important;" href="mailto:joomlasupport@xecurify.com"> joomlasupport@xecurify.com</a> </p>
                        </div>
                    </div>
			    </form>
			</div>
		</div>
	</div>
    <?php
}

function add_on_description()
{
    ?>

    <div class="mo_boot_col-sm-12 mo_boot_mx-2" style="box-shadow: 0px 0px 15px 5px lightgray;">
        <div class="mo_boot_row mo_tab_border mo_boot_p-2">
            <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-lg-5">
                        <h3 class="mo_saml_form_head"><?php echo Text::_('COM_MINIORANGE_SAML_ADDON_TAB'); ?></h3>
                    </div>
                </div>
            </div>
            <div class="mo_boot_col-sm-12">
                <div class="mo_boot_row mo_boot_mt-4">
                    <div class="vtab mo_boot_col-sm-3">
                        <button class="vtab_btn active" onclick="openTab(event, 'vaddon1')" id="defaultTab">Community Builder Integration</button>
                        <button class="vtab_btn" onclick="openTab(event, 'vaddon3')">Sync users from your IdP in Joomla</button>
                        <button class="vtab_btn" onclick="openTab(event, 'vaddon4')">Page & Article Restriction</button>
                        <button class="vtab_btn" onclick="openTab(event, 'vaddon5')">Media Restriction</button>
                        <button class="vtab_btn" onclick="openTab(event, 'vaddon6')">Role/Group Based Redirection</button>
                        <button class="vtab_btn" onclick="openTab(event, 'vaddon7')">Login Audit</button>
                    </div>

                    <div class="vtab-box mo_boot_col-sm-9">
                        <div class="vtab_content" id="vaddon1" style="display:block">
                            <h4 class="vheader">Community Builder Integration</h4>
                            <p class="vcontent">By Community Builder Integration add-on, you have the capability to map IdP user details to the Community Builder's comprofiler fields table during the SSO process. 
                                This streamlines the process and minimizes manual efforts required to update user attributes in the Community Builder plugin.</p>
                            <a href="https://www.miniorange.com/contact"><button class="mo_boot_btn btn_cstm">Learn More</button></a>
                        </div>

                        <div class="vtab_content" id="vaddon3" style="display:none">
                            <h4 class="vheader">Sync users from your IdP in Joomla (SCIM Plugin)</h4>
                            <p class="vcontent">By using SCIM plugin, you can automate user creation, updation, and deletion (de-provisioning) in real-time, ensuring that user information remains accurate and synchronized across IDPs like Azure AD, Okta, GSuite/ Google Apps / Google Workspace, Keycloak, Centrify, One Login, PingOne, Jumpcloud, miniOrange, etc.
                                This enables you to streamline user management on your Joomla site, saving your time and effort.
                            </p>
                            <a href="https://plugins.miniorange.com/joomla-scim-user-provisioning"><button class="mo_boot_btn btn_cstm">Learn More</button></a>
                        </div>

                        <div class="vtab_content" id="vaddon4" style="display:none">
                            <h4 class="vheader">Page & Article Restriction</h4>
                            <p class="vcontent">By using Page & Article Restriction plugin, you can restrict users from accessing particular pages or URLs and redirect them to either Joomla's default login page, IDP login page, custom URLs or show custom error messages according to your configuration.
                            This feature includes IP and URL whitelisting and blacklisting capabilities.
                            </p>
                            <a href="https://plugins.miniorange.com/page-and-article-restriction-for-joomla"><button class="mo_boot_btn btn_cstm">Learn More</button></a>
                        </div>

                        <div class="vtab_content" id="vaddon5" style="display:none">
                            <h4 class="vheader">Media Restriction</h4>
                            <p class="vcontent">By using Media Restriction plugin, can securely restrict access to your Joomla files, directories, and subfolder based on user login status, group or custom requirements.
                                Our plugin supports both Apache and NGIX server and restrcit the media files by writing specific rules.
                            </p>
                            <a href="https://plugins.miniorange.com/media-restriction-in-joomla"><button class="mo_boot_btn btn_cstm">Learn More</button></a>
                        </div>

                        <div class="vtab_content" id="vaddon6" style="display:none">
                            <h4 class="vheader">Role/Group Based Redirection</h4>
                            <p class="vcontent">By using Role/Group Based Redirection plugin, you can you to redirect your users to different pages after they login or logout your site, based on the role/group sent by your Identity Provider.
                            </p>
                            <a href="https://plugins.miniorange.com/role-based-redirection-for-joomla"><button class="mo_boot_btn btn_cstm">Learn More</button></a>
                        </div>

                        <div class="vtab_content" id="vaddon7" style="display:none">
                            <h4 class="vheader">Login Audit</h4>
                            <p class="vcontent">By using Login Audit plugin, you can generate a report containing a variety of details of the logging-in user, such as their IP Address, username, user action(whether it's a login or register operation), status and time. 
                                This plugin also shows the report of SSO users.
                            </p>
                            <a href="https://plugins.miniorange.com/joomla-login-audit-login-activity-report"><button class="mo_boot_btn btn_cstm">Learn More</button></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
}

function mo_sticky_support()
{
    $cms_version = SAML_Utilities::getJoomlaCmsVersion();
    $sticky_style = ($cms_version>=4.0)?"bottom:1rem":"bottom:2rem";
	echo '
	<div class="mo_contact-us-container" style="z-index:1;'.$sticky_style.'"> 
    	<div id="mo_contact_us" class="mo_boot_d-flex gap-mo-4 relative justify-end">
        	<input id="contact-us-toggle" type="checkbox" class="peer sr-only"/>

        	<a href="index.php?option=com_miniorange_saml&tab=support_tab" style="text-decoration:none" class="mo_contact_us_box">
            	<span class="mo-heading text-white leading-normal" style="font-size:14px;">Hello there! Need Help?<br>Drop us an Email</span>
        	</a>

        	<a href="index.php?option=com_miniorange_saml&tab=support_tab" class="mo_sticky_support" style="">
            	<svg width="60" height="60" viewBox="0 0 102 103" fill="none" class="cursor-pointer">
              		<g id="d4c51d1a6d24c668e01e2eb6a39325d7">
                		<rect width="102" height="103" rx="51" fill="url(#b69bc691e4b17a460c917ded85c3988c)"></rect>
                		<g id="0df790d6c3b93208dd73e487cf02eedc">
                 		<path id="e161bdf1e94ee39e424acc659f19e97c" fill-rule="evenodd" clip-rule="evenodd" d="M32 51.2336C32 37.5574 36.7619 33 51.0476 33C65.3333 33 70.0952 37.5574 70.0952 51.2336C70.0952 64.9078 65.3333 69.4672 51.0476 69.4672C36.7619 69.4672 32 64.9078 32 51.2336Z" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"></path>
                  		<path id="c79e8f13aac8a6b146b9542a01c31ddc" d="M69.0957 44.2959C69.0957 44.2959 56.6508 55.7959 51.5957 55.7959C46.5406 55.7959 34.0957 44.2959 34.0957 44.2959" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"></path>
                		</g>
              		</g>
              		<defs>
                		<linearGradient id="b69bc691e4b17a460c917ded85c3988c" x1="0" y1="0" x2="102" y2="103" gradientUnits="userSpaceOnUse">
                  			<stop stop-color="#2563eb"></stop>
                  			<stop offset="1" stop-color="#1d4ed8"></stop>
                		</linearGradient>
              		</defs>
           		 </svg>
        	</a>
		</div>
		
	</div>';
}