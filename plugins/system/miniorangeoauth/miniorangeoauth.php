<?php

/**
 * @package     Joomla.System
 * @subpackage  plg_system_miniorangeoauth
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
require_once JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_miniorange_oauth'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mo_customer_setup.php';

class plgSystemMiniorangeoauth extends JPlugin
{
    private $attributesNames = "";
    public function onAfterRender()
    {
        $app            = JFactory::getApplication();
        $body           = $app->getBody();
        $tab = 0;
        $tables = JFactory::getDbo()->getTableList();
        foreach ($tables as $table)
        {
            if (strpos($table, "miniorange_oauth_config")!==FALSE)
                $tab = $table;
        }
        if($tab===0)
            return;
        
        $customerResult =$this->miniOauthFetchDb('#__miniorange_oauth_config',array('id'=>'1'));
        $applicationName=$customerResult['appname'];
        $linkCheck      =$customerResult['login_link_check'];
        if($linkCheck==1 && $app->isClient('site'))
        {
            $linkCondition = <<<EOD
            <button type="submit" tabindex="0" name="Submit" class="btn btn-primary login-button">
            EOD;
            if(stristr($body,$linkCondition))
            {
                if(stristr($body,"user.login"))
                {
                    $linkAddPlace="</button><br><a href = ".JURI::root()."?morequest=oauthredirect&app_name=".$applicationName."> Click Here For SSO ";
                    $body = str_replace('</button>', $linkAddPlace . '</a>', $body);
                    $app->setBody($body);           
                }
            }
        }
    }

    public function onAfterInitialise()
    {
        $app = JFactory::getApplication();
        $post = $app->input->post->getArray();

        if (isset($post['mojsp_feedback']))
        {
            $radio = $post['deactivate_plugin'];
            $data = $post['query_feedback'];
            $current_user = JFactory::getUser();
            $feedback_email = isset($post['feedback_email']) ? $post['feedback_email'] : $current_user->email;
            $fields = array(
                'uninstall_feedback'=>1
            );
            $conditions = array(
                'id'=>'1'
            );

            $this->miniOauthUpdateDb('#__miniorange_oauth_customer',$fields,$conditions);
            $customerResult=$this->miniOauthFetchDb('#__miniorange_oauth_customer',array('id'=>'1'));
            $admin_email = (isset($customerResult['email']) && !empty($customerResult['email'])) ? $customerResult['email'] : $feedback_email;
            $admin_phone = $customerResult['admin_phone'];
            $data1 = $radio . ' : ' . $data;
            require_once JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_miniorange_oauth' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_customer_setup.php';
            MoOauthCustomer::submit_feedback_form($admin_email, $admin_phone, $data1);
            require_once JPATH_SITE . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Installer' . DIRECTORY_SEPARATOR . 'Installer.php';
            
            foreach ($post['result'] as $fbkey) 
            {
                $result = $this->miniOauthFetchDb('#__extensions',array('extension_id'=>$fbkey),'loadColumn','type');
                $type = 0;
                foreach ($result as $results) 
                {
                    $type = $results;
                }
                if ($type) 
                {
                    $cid = 0;
                    $installer = new JInstaller();
                    $installer->uninstall($type, $fbkey, $cid);
                }
            }
        }
        /*----------------------Oauth Flow-------------------------------------------*/
        $get = $app->input->get->getArray();
        $session = JFactory::getSession();

        /*----------------------Test Configuration Handling----------------------------*/
        if (isset($get['morequest']) and $get['morequest'] == 'testattrmappingconfig')
        {
            $mo_oauth_app_name = $get['app'];
            $result=$app->redirect(JRoute::_(JURI::root() . '?morequest=oauthredirect&app_name=' . urlencode($mo_oauth_app_name) . '&test=true'));
        }

        /*-------------------------OAuth SSO starts with this if-----------*/
        /*            Opening of OAuth server dialog box
                     Step 1 of Oauth/OpenID flow
        */
        else if (isset($get['morequest']) and $get['morequest'] == 'oauthredirect') 
        {
            $appname = $get['app_name'];
            if (isset($get['test']))
                setcookie("mo_oauth_test", true);
            else
                setcookie("mo_oauth_test", false);

            // save the referrer in cookie so that we can come back to origin after SSO
            if (isset($_SERVER['HTTP_REFERER']))
                $loginredirurl = $_SERVER['HTTP_REFERER'];

            if (!empty($loginredirurl)) {
                setcookie("returnurl", $loginredirurl);
            }
            
            // get Ouath configuration from database
            
            $appdata = $this->miniOauthFetchDb('#__miniorange_oauth_config', array('custom_app'=>$appname));
            $session->set('appname', $appname);
            if(is_null($appdata))
                $appdata = $this->miniOauthFetchDb('#__miniorange_oauth_config', array('appname'=>$appname));
            
            if(empty($appdata['client_id']) || empty($appdata['app_scope'])){
                echo "<center><h3 style='color:indianred;border:1px dotted black;'>Sorry! client ID or Scope is empty.</h3></center>";
                exit;
            }

            $state = base64_encode($appname);
            $authorizationUrl = $appdata['authorize_endpoint'];

            if (strpos($authorizationUrl, '?') !== false)
                $authorizationUrl = $authorizationUrl . "&client_id=" . $appdata['client_id'] . "&scope=" . $appdata['app_scope'] . "&redirect_uri=" . JURI::root() . "&response_type=code&state=" . $state;
            else
                $authorizationUrl = $authorizationUrl . "?client_id=" . $appdata['client_id'] . "&scope=" . $appdata['app_scope'] . "&redirect_uri=" . JURI::root() . "&response_type=code&state=" . $state;
            
            if (session_id() == '' || !isset($session))
                session_start();
            $session->set('oauth2state', $state);
            header('Location: ' . $authorizationUrl);
            exit;
        } 
        /*
        *   Step 2 of OAuth Flow starts. We got the code
        *
        */
        else if (isset($get['code'])) 
        {
            if (session_id() == '' || !isset($session))
                session_start();
            try {
                // get the app name from session or by decoding state
                $currentappname = "";
                $session_var = $session->get('appname');
                if (isset($session_var) && !empty($session_var))
                    $currentappname = $session->get('appname');
                else if (isset($get['state']) && !empty($get['state']))
                    $currentappname = base64_decode($get['state']);
                if (empty($currentappname)) {
                    exit('No request found for this application.');
                }
                // get OAuth configuration
                $appname = $session->get('appname');
                $name_attr = "";
                $email_attr = "";
                $appdata = $this->miniOauthFetchDb('#__miniorange_oauth_config', array('custom_app'=>$appname));
                if(is_null($appdata))
                    $appdata = $this->miniOauthFetchDb('#__miniorange_oauth_config', array('appname'=>$appname));
                if ($appdata['userslim'] < $appdata['usrlmt'])
                    $userslimitexeed = 0;
                else
                    $userslimitexeed = 1;
                $currentapp = $appdata;
                if (isset($appdata['email_attr']))
                    $email_attr = $appdata['email_attr'];
                if (isset($appdata['first_name_attr']))
                    $name_attr = $appdata['first_name_attr'];
                if (!$currentapp)
                    exit('Application not configured.');
                $authBase = JPATH_BASE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_miniorange_oauth';
                include_once $authBase . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'oauth_handler.php';
                $mo_oauth_handler = new Mo_OAuth_Hanlder();
                /*
                 * make a back channel request for access token
                 * we may also get an ID token in openid flow
                 *
                 * */
                list($accessToken,$idToken) = $mo_oauth_handler->getAccessToken
                ($currentapp['access_token_endpoint'], 'authorization_code',
                    $currentapp['client_id'], $currentapp['client_secret'], $get['code'], JURI::root(),$currentapp['in_header_or_body']);
                $mo_oauth_handler->printError();
                /*
                * if access token is valid then call userInfo endpoint to get user info or resource  owner details or extract from Id-token
                */
                $resourceownerdetailsurl = $currentapp['user_info_endpoint'];
                if (substr($resourceownerdetailsurl, -1) == "=") {
                    $resourceownerdetailsurl .= $accessToken;
                }
                $resourceOwner = $mo_oauth_handler->getResourceOwner($resourceownerdetailsurl, $accessToken,$idToken);
                $mo_oauth_handler->printError();
                list($email,$name)=$this->getEmailAndName($resourceOwner,$email_attr,$name_attr);
                $checkUser = $this->get_user_from_joomla($email);
                //efficiency of the plugin
                $sso_eff = $this->miniOauthFetchDb('#__miniorange_oauth_customer',array('id'=>'1'));
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $fields = array(
                    'dno_ssos'=>$sso_eff['dno_ssos'] + 1,
                );
                $conditions = array(
                   'id' => '1'
                );
                $this->miniOauthUpdateDb('#__miniorange_oauth_customer',$fields,$conditions);
                $thrs = 85400;
                if ($sso_eff['previous_update'] == '' || time() > $sso_eff['previous_update'] + $thrs) 
                {
                    $tno_ssos = $sso_eff['tno_ssos'] + $sso_eff['dno_ssos'];
                    $fields = array(
                            'previous_update' =>time(),
                            'dno_ssos' => 1,
                            'tno_ssos'=>$tno_ssos,
                    );
                    $conditions = array('id'=>'1');
                    $result = $this->miniOauthUpdateDb('#__miniorange_oauth_customer',$fields,$conditions);
                    $dVar = new JConfig();
                    $check_email = $dVar->mailfrom;
                    if(isset($sso_eff['contact_admin_emiail']) && $sso_eff['contact_admin_emiail']!=NULL)
                    {
                        $check_email=$sso_eff['contact_admin_emiail'];
                    }
                    $base_url = JURI::root();
                    $appname = '';
                    $c_time = date('m/d/Y H:i:s', $sso_eff['cd_plugin']);
                    $present_update = date('m/d/Y H:i:s', time());
                    $previous_update = date('m/d/Y H:i:s', intval($sso_eff['previous_update']));
                    $dno_ssos = $sso_eff['dno_ssos'];
                    require_once JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_miniorange_oauth' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_customer_setup.php';
                    $reason=$session->get('reason');
                    MoOauthCustomer::plugin_efficiency_check($check_email, $appname, $base_url, $c_time, $dno_ssos, $tno_ssos, $previous_update, $present_update,$reason);
                }
                $result = $this->miniOauthFetchDb('#__miniorange_oauth_customer',array('id'=>'1'));
                $test = base64_decode($result['sso_var']);
                $test = $test ;
                $test2 = base64_decode($result['sso_test']);
                if ((int)$test2 > (int)$test+35) {
                    exit;
                }
                if ($checkUser) {
                    $this->loginCurrentUser($checkUser, $name, $email);
                } 
                else 
                {
					require_once JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_miniorange_oauth' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_customer_setup.php';
                    $dVar = new JConfig();
                    $check_email = $dVar->mailfrom;
                    if(isset($sso_eff['contact_admin_emiail']) && $sso_eff['contact_admin_emiail']!=NULL)
                    {
                        $check_email=$sso_eff['contact_admin_emiail'];
                    }
					$base_url = JURI::root();
                    $appname = '';
                    $c_time = date('m/d/Y H:i:s', $sso_eff['cd_plugin']);
                    $present_update = date('m/d/Y H:i:s', time());
                    $previous_update = date('m/d/Y H:i:s', intval($sso_eff['previous_update']));
                    $dno_ssos = $sso_eff['dno_ssos'];
					$reason ="Can't create new user";
					MoOauthCustomer::plugin_efficiency_check($check_email, $appname, $base_url, $c_time, $dno_ssos, 1, $previous_update, $present_update,$reason);
                    echo '<div style="font-family:Calibri;padding:0 3%;">';
                    echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;"> ERROR</div>
                              <div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>Error: </strong>New User could not be created.</p>
                              <p><strong>Cause:</strong>Current version of the plugin does not have the feature auto create user. Please upgrade to Standard or Premium or Enterprise version of the plugin to creare users.</p></div>
                              <div style="text-align:center"><a href="https://login.xecurify.com/moas/login?redirectUrl=https://login.xecurify.com/moas/initializepayment&requestOrigin=joomla_oauth_client_enterprise_plan" type="button" style="color: white; background: #185b91; padding: 10px 20px;">Upgrade Now</a></div>  
                            </div><br>';
                    $home_link = JURI::root();
                    echo '<p align="center"><a href=' . $home_link . ' type="button" style="color: white; background: #185b91; padding: 10px 20px;">Back to Website</a><p>';
                    exit;
                } 

            }catch (Exception $e) 
            {
                exit($e->getMessage());
            }
        }
    }

    function onExtensionBeforeUninstall($id)
    {
        $post = JFactory::getApplication()->input->post->getArray();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('extension_id');
        $query->from('#__extensions');
        $query->where($db->quoteName('name') . " = " . $db->quote('COM_MINIORANGE_OAUTH'));
        $db->setQuery($query);
        $result = $db->loadColumn();
        $tables = JFactory::getDbo()->getTableList();
        $tab = 0;
        foreach ($tables as $table) {
            if (strpos($table, "miniorange_oauth_customer"))
                $tab = $table;
        }
        if ($tab) 
        {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('uninstall_feedback');
            $query->from('#__miniorange_oauth_customer');
            $query->where($db->quoteName('id') . " = " . $db->quote(1));
            $db->setQuery($query);
            $fid = $db->loadColumn();
            $tpostData = $post;
            foreach ($fid as $value) 
            {
                if ($value == 0) 
                {
                    foreach ($result as $results) 
                    {
                        if ($results == $id) 
                        {
                            ?>
                            <div id="myModal" class="modal">
                                <div class="modal-content">
                                    <img src="<?php echo JURI::root() . 'plugins/system/miniorangeoauth/assets/image/think.jpg'; ?>" style="width:70px;height;70px;" alt="">
                                    <p style="font-size:20px;line-height:30px;">Before uninstalling just give us a chance to make your experience better.
                                                            We can Help you in every way possible please feel free to contact us. </p>
                                    <br><br>
                                    <a style="display:inline-block" href="<?php echo JURI::base()?>index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=support" class="mo_btn mo_btn-primary">Contact-Us</a>
                                    &nbsp;&nbsp;&nbsp;
                                    <button  class="mo_btn mo_btn-primary" onclick="skip()" >Skip</button>
                                </div>
                            </div>
                            <div class="form-style-6 " id="form-style-6">
                                <!-- <span class="mojsp_close">&times;</span> -->
                                <h1> Feedback Form for Oauth</h1>
                                <h3>What Happened? </h3>
                                <form name="f" method="post" action="" id="mojsp_feedback">
                                    <input type="hidden" name="mojsp_feedback" value="mojsp_feedback"/>
                                    <div>
                                        <p style="margin-left:2%">
                                            <?php
                                            $deactivate_reasons = array(
                                                "Does not have the features I'm looking for",
                                                "Confusing Interface",
                                                "Not able to Configure",
                                                "Redirecting back to login page after Authentication",
                                                "Not Working",
                                                "Not Receiving OTP During Registration",
                                                "Bugs in the plugin",
                                                "Not able to Configure",
                                                "Other Reasons:"
                                            );
                                            foreach ($deactivate_reasons as $deactivate_reasons) { ?>
                                        <div class=" radio " style="padding:1px;margin-left:2%;cursor:pointer">
                                            <label style="font-weight:normal;font-size:14.6px"
                                                   for="<?php echo $deactivate_reasons; ?>">
                                                <input type="radio" name="deactivate_plugin"
                                                       value="<?php echo $deactivate_reasons; ?>" required>
                                                <?php echo $deactivate_reasons; ?></label>
                                        </div>
                                        <?php } ?>
                                        <br>
                                        <textarea id="query_feedback" name="query_feedback" rows="4"
                                                  style="margin-left:2%"
                                                  cols="50" placeholder="Write your query here"></textarea><br><br><br>
                                        <tr>
                                <td width="20%"><b>Email<span style="color: #ff0000;">*</span>:</b></td>
                                <td><input type="email" name="feedback_email" required placeholder="Enter email to contact." style="width:55%"/></td>
                                       </tr>
                                        <?php
                                        foreach ($tpostData['cid'] as $key) { ?>
                                            <input type="hidden" name="result[]" value=<?php echo $key ?>>
                                        <?php } ?>
                                        <br><br>
                                        <div class="mojsp_modal-footer">
                                            <input type="submit" name="miniorange_feedback_submit"
                                                   class="button button-primary button-large" value="Submit"/>
                                        </div>
                                    </div>
                                </form>
                                <!-- <form name="f" method="post" action="" id="mojsp_feedback_form_close">
                                    <input type="hidden" name="option" value="mojsp_skip_feedback"/>
                                </form> -->
                            </div>
                            <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
                            <script>
                                jQuery('input:radio[name="deactivate_plugin"]').click(function () {
                                    var reason = jQuery(this).val();
                                    jQuery('#query_feedback').removeAttr('required')
                                    if (reason == 'Facing issues During Registration') {
                                        jQuery('#query_feedback').attr("placeholder", "Can you please describe the issue in detail?");
                                    } else if (reason == "Does not have the features I'm looking for") {
                                        jQuery('#query_feedback').attr("placeholder", "Let us know what feature are you looking for");
                                    } else if (reason == "Other Reasons:") {
                                        jQuery('#query_feedback').attr("placeholder", "Can you let us know the reason for deactivation");
                                        jQuery('#query_feedback').prop('required', true);
                                    } else if (reason == "Not able to Configure") {
                                        jQuery('#query_feedback').attr("placeholder", "Not able to Configure? let us know so that we can improve the interface");
                                    } else if (reason == "Confusing Interface") {
                                        jQuery('#query_feedback').attr("placeholder", "Confusing Interface? Reach out to us at joomlasupport@xecurify.com, we'll help set up the plugin");
                                    } else if (reason == "Redirecting back to login page after Authentication") {
                                        jQuery('#query_feedback').attr("placeholder", "Reach out to us at joomlasupport@xecurify.com, we'll help you resolve the issue");
                                    } else if (reason == "Bugs in the plugin") {
                                        jQuery('#query_feedback').attr("placeholder", "Kindly let us know at joomlasupport@xecurify.com, what issues were you facing");
                                    }else if (reason == "Not Working") {
                                        jQuery('#query_feedback').attr("placeholder", "Kindly let us know at joomlasupport@xecurify.com, which functionality of the plugin is not working for you");
                                    }
                                });
                                // For skipping the feedback form
                                // When the user clicks on <span> (x), mojsp_close the mojsp_modal 
                                // var span = document.getElementsByClassName("mojsp_close")[0];
                                // span.onclick = function () {
                                //     mojsp_modal.style.display = "none";
                                //     jQuery('#mojsp_feedback_form_close').submit();
                                // }
                                function skip(){
                                    jQuery("#myModal").css("display","none");
                                    jQuery('#form-style-6').css("display","block");
                                }
                            </script>
                            <style type="text/css">
                                .form-style-6 {
                                    font: 95% Arial, Helvetica, sans-serif;
                                    max-width: 400px;
                                    margin: 10px auto;
                                    padding: 16px;
                                    background: #F7F7F7;
                                    display:none;
                                }
                                .form-style-6 h1 {
                                    background: #43D1AF;
                                    padding: 20px 0;
                                    font-size: 140%;
                                    font-weight: 300;
                                    text-align: center;
                                    color: #fff;
                                    margin: -16px -16px 16px -16px;
                                }
                                .form-style-6 input[type="text"],
                                .form-style-6 input[type="date"],
                                .form-style-6 input[type="datetime"],
                                .form-style-6 input[type="email"],
                                .form-style-6 input[type="number"],
                                .form-style-6 input[type="search"],
                                .form-style-6 input[type="time"],
                                .form-style-6 input[type="url"],
                                .form-style-6 textarea,
                                .form-style-6 select {
                                    -webkit-transition: all 0.30s ease-in-out;
                                    -moz-transition: all 0.30s ease-in-out;
                                    -ms-transition: all 0.30s ease-in-out;
                                    -o-transition: all 0.30s ease-in-out;
                                    outline: none;
                                    box-sizing: border-box;
                                    -webkit-box-sizing: border-box;
                                    -moz-box-sizing: border-box;
                                    width: 100%;
                                    background: #fff;
                                    margin-bottom: 4%;
                                    border: 1px solid #ccc;
                                    padding: 3%;
                                    color: #555;
                                    font: 95% Arial, Helvetica, sans-serif;
                                }
                                .form-style-6 input[type="text"]:focus,
                                .form-style-6 input[type="date"]:focus,
                                .form-style-6 input[type="datetime"]:focus,
                                .form-style-6 input[type="email"]:focus,
                                .form-style-6 input[type="number"]:focus,
                                .form-style-6 input[type="search"]:focus,
                                .form-style-6 input[type="time"]:focus,
                                .form-style-6 input[type="url"]:focus,
                                .form-style-6 textarea:focus,
                                .form-style-6 select:focus {
                                    box-shadow: 0 0 5px #43D1AF;
                                    padding: 3%;
                                    border: 1px solid #43D1AF;
                                }
                                .form-style-6 input[type="submit"],
                                .form-style-6 input[type="button"] {
                                    box-sizing: border-box;
                                    -webkit-box-sizing: border-box;
                                    -moz-box-sizing: border-box;
                                    width: 100%;
                                    padding: 3%;
                                    background: #43D1AF;
                                    border-bottom: 2px solid #30C29E;
                                    border-top-style: none;
                                    border-right-style: none;
                                    border-left-style: none;
                                    color: #fff;
                                }
                                .form-style-6 input[type="submit"]:hover,
                                .form-style-6 input[type="button"]:hover {
                                    background: #2EBC99;
                                }
                                .mo_btn{
                                    border:1px solid #ccc;
                                    padding:10px;
                                    height:auto;
                                    width:auto;
                                    border-radius:10px;
                                }
                                .mo_btn-primary{
                                    background-color:#2384d3;
                                    color:white;
                                    text-decoration:none;
                                }
                               .modal {
                                        position: fixed;
                                        z-index: 1; 
                                        left:0;
                                        top: 0!important;
                                        width: 100%!important;
                                        height: 100%!important; 
                                        overflow: auto; 
                                        background-color: rgb(0,0,0);
                                        background-color: rgba(0,0,0,0.4)!important; 
                                        text-align:center!important;
                                    }
                                    .modal-content {
                                        background-color: #fefefe;
                                        margin: 15% auto;
                                        padding: 20px;
                                        border: 1px solid #888;
                                        width: 30%;
                                        height: auto;
                                        border:3px solid #2384d3;
                                    }
                                    .close {
                                        color: #aaa;
                                        float: right;
                                        font-size: 28px;
                                        font-weight: bold;
                                    }
                                    
                                    .close:hover,
                                    .close:focus {
                                        color: black;
                                        text-decoration: none;
                                        cursor: pointer;
                                    }
                            </style>
                            <?php
                            exit;
                        }
                    }
                }
            }
        }
    }

    function getEmailAndName($resourceOwner,$email_attr,$name_attr)
    {
        //TEST Configuration
        $session = JFactory::getSession();
        $resultAttr = $this->miniOauthFetchDb('#__miniorange_oauth_config',array('id'=>'1'));
        $siteUrl=JURI::root();
        $siteUrl = $siteUrl . '/administrator/components/com_miniorange_oauth/assets/images/';
        if(isset($resourceOwner['email']))
        {
            $email =$resourceOwner['email'];
        }
        else
        {
            $email="there";
        }
        echo '<div style="font-family:Calibri;padding:0 3%;">';
        $test_cookie = JFactory::getApplication()->input->cookie->get('mo_oauth_test');
        if (isset($test_cookie) && !empty($test_cookie))
        {
            $attributesName = "";          
            echo '<div style="color: #3c763d;
                background-color: #dff0d8; padding:2%;margin-bottom:20px;text-align:center; border:1px solid #AEDB9A; font-size:18pt;">TEST SUCCESSFUL</div>
                <div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;"src="' . $siteUrl . 'green_check.png"></div><br>
                <span style="font-size:14pt;"><b>Hello, '.$email.'</b>,<br/> </span><br/>
                <table style="border-collapse:collapse;border-spacing:0; display:table;width:100%; font-size:14pt;background-color:#EDEDED;">
                <tr style="text-align:center;"><td style="font-weight:bold;border:2px solid #949090;padding:2%;">ATTRIBUTE NAME</td><td style="font-weight:bold;padding:2%;border:2px solid #949090; word-wrap:break-word;">ATTRIBUTE VALUE</td></tr>';
            
            echo '<div style="background:#EDEDED;padding:5px;">
                <p style="color:red;"><b><u>Next Steps :</u></b></p>
                <p>In Order to perform SSO successfully you need to atleast map the attribute containing Email-id recieved from the OAuth Provider with default joomla Email attribute in the Step 3. </p>
                </div>
                <p style="font-weight:bold;font-size:14pt;margin-left:1%;">ATTRIBUTES RECEIVED:</p><br>';
            self::testattrmappingconfig("",$resourceOwner);             
            echo "</table> <br><br>";
            $user_attributes = $this->attributesNames;
            $this->miniOauthUpdateDb('#__miniorange_oauth_config',array('test_attribute_name'=>$user_attributes),array("id"=>1));
            exit();
        }
        if(!empty($email_attr))
        {
            $email = $this->getnestedattribute($resourceOwner, $email_attr);
        }
        else
        {
            $session->set('mo_reason','Login not Allowed.Attibute Mapping is empty. Please configure it');
            echo '<div style="font-family:Calibri;padding:0 3%;">';
            echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;"> ERROR</div>
            <div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>Error: </strong> Login not Allowed</p>
            <p><strong>Causes</strong>: Attibute Mapping is empty. Please configure it.</p>
            </div>';
            $base_url = JURI::root();
            echo '<p align="center"><a href="' . $base_url . '" style="text-decoration: none; padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"type="button">Done</a></p>';
            exit;
        }
        if (!empty($name_attr))
            $name = $this->getnestedattribute($resourceOwner, $name_attr);

        if (empty($email)) 
        {
            $home_link = JURI::root();
            $session->set('mo_reason','Email address not received. Check your Attribute Mapping configuration.');
            echo '<div style="font-family:Calibri;padding:0 3%;"><div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;"> ERROR</div>
                    <div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>Error: </strong>Email Address not recived.</p>
                    <p><strong>Cause:</strong>Email Id not received for the email attribute name you configured. Check your <b>Attribute Mapping</b> configuration.</p></div></div><br>';
            $home_link = JURI::root();
            echo '<p align="center"><a href=' . $home_link . ' type="button" style="color: white; background: #185b91; padding: 10px 20px;">Back to Website</a><p>';
            exit;
        }
        return array($email,$name);
    }

    function testattrmappingconfig($nestedprefix, $resourceOwnerDetails)
    {
        if (!empty($nestedprefix))
            $nestedprefix .= ".";
            
        foreach ($resourceOwnerDetails as $key => $resource) 
        {
            if (is_array($resource) || is_object($resource)) 
            {
                $this->testattrmappingconfig($nestedprefix . $key, $resource);
            } 
            else 
            {
                echo "<tr><td style='font-weight:bold;border:2px solid #949090;padding:2%;'>";
                if (!empty($nestedprefix))
                    echo $nestedprefix;
                echo $key."</td><td style='padding:2%;border:2px solid #949090; word-wrap:break-word;'>" . $resource . "</td></tr>";
               $this->attributesNames.= $nestedprefix.$key.',';
            }
        }
    }

    function getnestedattribute($resource, $key)
    {
        if(trim($key)=="")
            return "";

        $keys = explode(".",$key);
        if(sizeof($keys)>1)
        {
            $current_key = $keys[0];
            if(isset($resource[$current_key]))
                return $this->getnestedattribute($resource[$current_key], str_replace($current_key.".","",$key));
        } 
        else
        {
            $current_key = $keys[0];
            if(isset($resource[$current_key]))
            {
                return $resource[$current_key];
            }
        }
        return "";
    }

    function get_user_from_joomla($email)
    {
        //Check if email exist in database
        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__users')
            ->where('email=' . $db->quote($email));
        $db->setQuery($query);
        $checkUser = $db->loadObject();
        return $checkUser;
    }

    function loginCurrentUser($checkUser, $name, $email)
    {
        $app = JFactory::getApplication();
        $user = JUser::getInstance($checkUser->id);
        $this->updateCurrentUserName($user->id, $name);
        $session = JFactory::getSession(); #Get current session vars
        // Register the needed session variables
        $session->set('user', $user);
        //$app->checkSession();
        $sessionId = $session->getId();
        $this->updateUsernameToSessionId($user->id, $user->username, $sessionId);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__miniorange_oauth_customer'));
        $query->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        $result = $db->loadAssoc();
        $test = base64_decode(empty($result['sso_test'])?base64_encode(0):$result['sso_test']);
        $sso_test = (int)$test + 1;
        $sso_test = base64_encode($sso_test);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('sso_test') . ' = ' . $db->quote($sso_test),
        );
        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );
        $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $results = $db->execute();
        $user->setLastVisit();
        $db = JFactory::getDbo();
        
        $returnurl = JFactory::getApplication()->input->cookie->getArray();
        if (isset($returnurl['returnurl'])) 
        {
            $redirectloginuri = $returnurl['returnurl'];
        }
        else 
        {
            $redirectloginuri = JURI::root() . 'index.php?';
        }

        $test = base64_decode($result['sso_var']);
        $test2 = base64_decode($result['sso_test']);

        if ((int)$test2 > (int)$test+25) 
        {
            echo "Reached the authentication limit, Please contact administrator";
            exit;
        }
        $app->redirect($redirectloginuri);
    }

    function updateCurrentUserName($id, $name)
    {
        if (empty($name)) {
            return;
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('name') . ' = ' . $db->quote($name),
        );
        $conditions = array(
            $db->quoteName('id') . ' = ' . $db->quote($id),
        );
        $query->update($db->quoteName('#__users'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $result = $db->execute();
    }

    function updateUsernameToSessionId($userID, $username, $sessionId)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('username') . ' = ' . $db->quote($username),
            $db->quoteName('guest') . ' = ' . $db->quote('0'),
            $db->quoteName('userid') . ' = ' . $db->quote($userID),
        );

        $conditions = array(
            $db->quoteName('session_id') . ' = ' . $db->quote($sessionId),
        );

        $query->update($db->quoteName('#__session'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $result = $db->execute();
    }

    function miniOauthFetchDb($tableName,$condition,$method='loadAssoc',$columns='*')
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $columns = is_array($columns)?$db->quoteName($columns):$columns;
        $query->select($columns);
        $query->from($db->quoteName($tableName));
        foreach ($condition as $key=>$value)
            $query->where($db->quoteName($key) . " = " . $db->quote($value));

        $db->setQuery($query);
        if ($method=='loadColumn')
            return $db->loadColumn();
        else if($method == 'loadObjectList')
            return $db->loadObjectList();
        else if($method== 'loadResult')
            return $db->loadResult();
        else if($method == 'loadRow')
            return $db->loadRow();
        else
            return $db->loadAssoc();
    }

    function miniOauthUpdateDb($tableName,$fields,$conditions)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        // Fields to update.
        $sanFields=array();
        foreach ($fields as $key=>$value)
        {
            array_push($sanFields,$db->quoteName($key) . ' = ' . $db->quote($value));
        }
        // Conditions for which records should be updated.
        $sanConditions=array();
        foreach ($conditions as $key=>$value)
        {
            array_push($sanConditions,$db->quoteName($key) . ' = ' . $db->quote($value));
        }
        $query->update($db->quoteName($tableName))->set($sanFields)->where($sanConditions);
        $db->setQuery($query);
        $db->execute();
    }
}
