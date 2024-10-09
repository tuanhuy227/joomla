<?php
defined('_JEXEC') or die;
/*
 * @package    miniOrange
 * @subpackage Plugins
 * @license    GNU/GPLv3
 * @copyright  Copyright 2015 miniOrange. All Rights Reserved.
*/
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

$document = Factory::getApplication()->getDocument();
$document->addStyleSheet(Uri::base() . 'components/com_miniorange_saml/assets/css/miniorange_boot.css');





function mo_saml_advertise(){
	?>
	<div id="sp_advertise" class="">
		<div class="mo_boot_row mo_boot_p-3 mo_tab_border">
			<div class="mo_boot_col-sm-12">
				<div class="mo_boot_row">
                    <div class="mo_boot_col-sm-2">
                        <img src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/miniorange_i.ico" alt="miniorange">  
                    </div>
                    <div class="mo_boot_col-sm-10">
                        <h4><?php echo Text::_('COM_MINIORANGE_SAML_SCIM_ADD'); ?></h4>
                    </div>
				</div><hr>
			</div>
			<div class="mo_boot_col-sm-12">
               <div class="mo_boot_px-3  mo_boot_text-center">
                     <img src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/scim-icon.png" width="100" height="100" alt="SCIM">
                </div>
               <p><br><br>
                    <?php echo Text::_('COM_MINIORANGE_SAML_SICM_INFO'); ?>
                </p>
               <div class="mo_boot_row mo_boot_text-center mo_boot_mt-5">
                   <div class="mo_boot_col-sm-12">
                        <input type="button" onclick="window.open('https://prod-marketing-site.s3.amazonaws.com/plugins/joomla/scim-user-provisioning-for-joomla.zip')" target="_blank" value="<?php echo Text::_('COM_MINIORANGE_SAML_DOWNLOAD'); ?>"   class="mo_boot_btn mo_boot_btn-saml" />
                        <input type="button" onclick="window.open('https://plugins.miniorange.com/joomla-scim-user-provisioning')" target="_blank" value="<?php echo Text::_('COM_MINIORANGE_SAML_KNOW_MORE'); ?>"   class="mo_boot_btn mo_boot_btn-success mo_boot_ml-1" />
                    </div>
               </div>
			</div>
		</div>
	</div>
<?php
}

function mo_saml_adv_pagerestriction(){
    ?>
    <div id="sp_advertise" class="">
        <div class="mo_boot_row mo_boot_p-3 mo_tab_border">
            <div class="mo_boot_col-sm-12">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-2">
                        <img src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/miniorange_i.ico" alt="miniorange">  
                    </div>
                    <div class="mo_boot_col-sm-10">
                        <h4><?php echo Text::_('COM_MINIORANGE_SAML_PAGE_RESTRICTION'); ?></h4>
                    </div>
                </div><hr>
            </div>
            <div class="mo_boot_col-sm-12 ">
                <div class="mo_boot_px-3  mo_boot_text-center">
                         <img src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/session-management-addon.webp" alt="Session Management" width="150"  height="150" >
                </div>
                <p><br>
                    <?php echo Text::_('COM_MINIORANGE_SAML_PAGE_RESTRICTION_INFO'); ?>
                </p>
                <div class="mo_boot_row mo_boot_text-center mo_boot_mt-5">
                    <div class="mo_boot_col-sm-12">
                        <input type="button" onclick="window.open('https://plugins.miniorange.com/page-and-article-restriction-for-joomla')" target="_blank" value="<?php echo Text::_('COM_MINIORANGE_SAML_KNOW_MORE'); ?>" class="mo_boot_btn mo_boot_btn-success" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function mo_saml_adv_net(){
    ?>
    <div id="sp_advertise" class="">
        <div class="mo_boot_row mo_boot_p-3 mo_tab_border" >
            <div class="mo_boot_col-sm-12">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-2">
                        <img src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/miniorange_i.ico" alt="miniorange">  
                    </div>
                    <div class="mo_boot_col-sm-10">
                        <h4><?php echo Text::_('COM_MINIORANGE_SAML_WEB_SECURITY'); ?></h4>
                    </div>
                </div><hr>
            </div>
            <div class="mo_boot_col-sm-12 ">
                <div class="mo_boot_px-3  mo_boot_text-center">
                    <img src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/network.webp" alt="Web Security">
                </div><p><?php echo Text::_('COM_MINIORANGE_SAML_WEB_SECURITY_INFO'); ?></p>
                <div class="mo_boot_row mo_boot_text-center mo_boot_mt-5">
                    <div class="mo_boot_col-sm-12">
                        <input type="button" onclick="window.open('https://prod-marketing-site.s3.amazonaws.com/plugins/joomla/miniorange_joomla_network_security.zip')" target="_blank" value="<?php echo Text::_('COM_MINIORANGE_SAML_DOWNLOAD'); ?>" class="mo_boot_btn mo_boot_btn-saml" />
                        <input type="button" onclick="window.open('https://plugins.miniorange.com/joomla-network-security')" target="_blank" value="<?php echo Text::_('COM_MINIORANGE_SAML_KNOW_MORE'); ?>"  class="mo_boot_btn mo_boot_btn-success mo_boot_ml-1" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function mo_saml_adv_loginaudit(){
    ?>
    <div id="sp_advertise" class="">
        <div class="mo_boot_row mo_boot_p-3 mo_tab_border">
            <div class="mo_boot_col-sm-12">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-2">
                        <img src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/miniorange_i.ico" alt="miniorange">  
                    </div>
                    <div class="mo_boot_col-sm-10">
                        <h4><?php echo Text::_('COM_MINIORANGE_SAML_LOGIN_AUDIT'); ?></h4>
                    </div>
                </div><hr>
                </div>
                <div class="mo_boot_col-sm-12 ">
                    <p><br><?php echo Text::_('COM_MINIORANGE_SAML_LOGIN_AUDIT_INFO'); ?></p>
                   <div class="mo_boot_row mo_boot_text-center mo_boot_mt-4">
                       <div class="mo_boot_col-sm-12">
                            <input type="button" onclick="window.open('https://plugins.miniorange.com/joomla-login-audit-login-activity-report')" target="_blank" value="<?php echo Text::_('COM_MINIORANGE_SAML_KNOW_MORE'); ?>" class="mo_boot_btn mo_boot_btn-success" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
}

function mo_saml_adv_idp(){
    ?>
    <div id="sp_advertise" class="">
        <div class="mo_boot_row mo_boot_p-3 mo_tab_border">
            <div class="mo_boot_col-sm-12">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-2">
                        <img src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/miniorange_i.ico" alt="miniorange">  
                    </div>
                    <div class="mo_boot_col-sm-10">
                        <h4><?php echo Text::_('COM_MINIORANGE_SAML_MEDIA_RESTRICTION'); ?></h4>
                    </div>
                </div><hr>
                </div>
                <div class="mo_boot_col-sm-12 ">
                   <div class="mo_boot_px-3  mo_boot_text-center">
                       <img src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/login-audit-addon.webp" alt="Login Audit"  width="120"  height="120" >
                    </div>
                   <p><br><br>
                        <?php echo Text::_('COM_MINIORANGE_SAML_MEDIA_RESTRICTION_INFO'); ?>
                    </p>
                   <div class="mo_boot_row mo_boot_text-center mo_boot_mt-5">
                       <div class="mo_boot_col-sm-12">
                            <input type="button" onclick="window.open('https://prod-marketing-site.s3.amazonaws.com/plugins/joomla/joomla-media-restriction-free.zip')" target="_blank" value="<?php echo Text::_('COM_MINIORANGE_SAML_DOWNLOAD'); ?>" class="mo_boot_btn mo_boot_btn-saml" />
                            <input type="button" onclick="window.open('https://plugins.miniorange.com/media-restriction-in-joomla')" target="_blank" value="<?php echo Text::_('COM_MINIORANGE_SAML_KNOW_MORE'); ?>" class="mo_boot_btn mo_boot_btn-success" />
                        </div>
                   </div>
                </div>
            </div>
        </div>
    <?php
    }

    function mo_saml_adv_web3(){
        ?>
        <div id="sp_advertise" class="">
            <div class="mo_boot_row mo_boot_p-3 mo_tab_border">
                <div class="mo_boot_col-sm-12">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-2">
                            <img src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/miniorange_i.ico" alt="miniorange">  
                        </div>
                        <div class="mo_boot_col-sm-10">
                            <h4><?php echo Text::_('COM_MINIORANGE_SAML_WEB3_ADD'); ?></h4>
                        </div>
                    </div><hr>
                    </div>
                    <div class="mo_boot_col-sm-12 ">
                       <div class="mo_boot_px-3  mo_boot_text-center">
                           <img src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/web3_plugin.jpg" alt="Web3 AUthentication"  width="200"  height="150" >
                        </div>
                       <p><br><br>
                            <?php echo Text::_('COM_MINIORANGE_SAML_WEB3_INFO'); ?>
                        </p>
                       <div class="mo_boot_row mo_boot_text-center mo_boot_mt-5">
                           <div class="mo_boot_col-sm-12">
                                <input type="button" onclick="window.open('https://prod-marketing-site.s3.amazonaws.com/plugins/joomla/nft-web3-authentication-for-joomla.zip')" target="_blank" value="<?php echo Text::_('COM_MINIORANGE_SAML_DOWNLOAD'); ?>" class="mo_boot_btn mo_boot_btn-saml" />
                                <input type="button" onclick="window.open('https://plugins.miniorange.com/web3-login-for-joomla')" target="_blank" value="<?php echo Text::_('COM_MINIORANGE_SAML_KNOW_MORE'); ?>" class="mo_boot_btn mo_boot_btn-success" />
                            </div>
                       </div>
                    </div>
                </div>
            </div>
        <?php
    }

    function mo_saml_adv_customapi(){
        ?>
        <div id="sp_advertise" class="">
            <div class="mo_boot_row mo_boot_p-3 mo_tab_border">
                <div class="mo_boot_col-sm-12">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-2">
                            <img src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/miniorange_i.ico" alt="miniorange">  
                        </div>
                        <div class="mo_boot_col-sm-10">
                            <h4><?php echo Text::_('COM_MINIORANGE_SAML_CUSTOMAPI_ADD'); ?></h4>
                        </div>
                    </div><hr>
                    </div>
                    <div class="mo_boot_col-sm-12 ">
                       <div class="mo_boot_px-3  mo_boot_text-center">
                           <img src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/customapi_plugin.jpg" alt="Web3 AUthentication"  width="200"  height="150" >
                        </div>
                       <p><br><br>
                            <?php echo Text::_('COM_MINIORANGE_SAML_CUSTOMAPI_INFO'); ?>
                        </p>
                       <div class="mo_boot_row mo_boot_text-center mo_boot_mt-5">
                           <div class="mo_boot_col-sm-12">
                                <input type="button" onclick="window.open('https://prod-marketing-site.s3.amazonaws.com/plugins/joomla/joomla-custom-api-free.zip')" target="_blank" value="<?php echo Text::_('COM_MINIORANGE_SAML_DOWNLOAD'); ?>" class="mo_boot_btn mo_boot_btn-saml" />
                                <input type="button" onclick="window.open('https://plugins.miniorange.com/custom-api-for-joomla')" target="_blank" value="<?php echo Text::_('COM_MINIORANGE_SAML_KNOW_MORE'); ?>" class="mo_boot_btn mo_boot_btn-success" />
                            </div>
                       </div>
                    </div>
                </div>
            </div>
        <?php
    }

    function mo_saml_adv_rolebased_redirect(){
        ?>
        <div id="sp_advertise" class="">
            <div class="mo_boot_row mo_boot_p-3 mo_tab_border">
                <div class="mo_boot_col-sm-12">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-2">
                            <img src="<?php echo Uri::base();?>/components/com_miniorange_saml/assets/images/miniorange_i.ico" alt="miniorange">  
                        </div>
                        <div class="mo_boot_col-sm-10">
                            <h4><?php echo Text::_('COM_MINIORANGE_SAML_ROLEREDIRECTION_ADD'); ?></h4>
                        </div>
                    </div><hr>
                    </div>
                    <div class="mo_boot_col-sm-12 ">
                       <p><br>
                            <?php echo Text::_('COM_MINIORANGE_SAML_ROLEREDIRECTION_INFO'); ?>
                        </p>
                       <div class="mo_boot_row mo_boot_text-center mo_boot_mt-5">
                           <div class="mo_boot_col-sm-12">
                                <input type="button" onclick="window.open('https://plugins.miniorange.com/role-based-redirection-for-joomla')" target="_blank" value="<?php echo Text::_('COM_MINIORANGE_SAML_KNOW_MORE'); ?>" class="mo_boot_btn mo_boot_btn-success" />
                            </div>
                       </div>
                    </div>
                </div>
            </div>
        <?php
    }
?>