<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_miniorange_oauth
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
/**
 * AccountSetup Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_miniorange_oauth
 * @since       0.0.9
 */
defined('_JEXEC') or die('Restricted access');

class miniorangeoauthControllerAccountSetup extends JControllerForm
{
    function __construct()
    {
        $this->view_list = 'accountsetup';
        parent::__construct();
    }
    function customerLoginForm() {


        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('login_status') . ' = '.$db->quote(1),
            $db->quoteName('password') . ' = ' . $db->quote(''),
            $db->quoteName('email_count') . ' = ' . $db->quote(0),
            $db->quoteName('sms_count') . ' = ' . $db->quote(0),
        );

        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $result = $db->execute();
        $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account');
    }

    function verifyCustomer()
    {
        $post=	JFactory::getApplication()->input->post->getArray();

        $email = '';
        $password = '';

        if( MoOAuthUtility::check_empty_or_null( $post['email'] ) ||MoOAuthUtility::check_empty_or_null( $post['password'] ) ) {
            JFactory::getApplication()->enqueueMessage( 4711, 'All the fields are required. Please enter valid entries.' );
            return;
        } else{
            $email =$post['email'];
            $password =  $post['password'] ;
        }

        $customer = new MoOauthCustomer();
        $content = $customer->get_customer_key($email,$password);

        $customerKey = json_decode( $content, true );
        if( strcasecmp( $customerKey['apiKey'], 'CURL_ERROR') == 0) {
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account',$customerKey['token'],'error');
        } else if( json_last_error() == JSON_ERROR_NONE ) {
            if(isset($customerKey['id']) && isset($customerKey['apiKey']) && !empty($customerKey['id']) && !empty($customerKey['apiKey'])){
                $this->save_customer_configurations($email,$customerKey['id'], $customerKey['apiKey'], $customerKey['token'],$customerKey['phone']);
                $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=license',JText::_('COM_MINIORANGE_OAUTH_ACCOUNT_RETRIEVED_SUCCESSFULLY'));
            }else{
                $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account',JText::_('COM_MINIORANGE_OAUTH_ERROR_FETCHING_USER_DETAILS'),'error');
            }
        } else {
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account',JText::_('COM_MINIORANGE_OAUTH_INVALID_USERNAME_PASSWORD'),'error');
        }
    }

    function save_customer_configurations($email, $id, $apiKey, $token, $phone) {

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('email') . ' = '.$db->quote($email),
            $db->quoteName('customer_key') . ' = '.$db->quote($id),
            $db->quoteName('api_key') . ' = '.$db->quote($apiKey),
            $db->quoteName('customer_token') . ' = '.$db->quote($token),
            $db->quoteName('admin_phone') . ' = '.$db->quote($phone),
            $db->quoteName('login_status') . ' = '.$db->quote(0),
            $db->quoteName('registration_status') .' = ' . $db->quote('SUCCESS'),
            $db->quoteName('password') . ' = ' . $db->quote(''),
            $db->quoteName('email_count') . ' = ' . $db->quote(0),
            $db->quoteName('sms_count') . ' = ' . $db->quote(0),
        );

        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $result = $db->execute();
    }
    
    function saveAdminMail()
    {
        $post=	JFactory::getApplication()->input->post->getArray();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('contact_admin_email') . ' = '.$db->quote($post['oauth_client_admin_email']),

        );

        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $result = $db->execute();
        $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup',JText::_('COM_MINIORANGE_OAUTH_ADMIN_EMAIL_CHANGED'));
        return;
    }

    function saveConfig() 
    { 
        $post=	JFactory::getApplication()->input->post->getArray();
        $appD = new MoOauthCustomer();
        if(count($post)==0){
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup');
            return;
        }
        else if(isset($post['oauth_config_form_step1']))
        {
            if(isset($post['callbackurl']))
            {
                $callbackurlhttp           = isset($post['callbackurlhttp'])?$post['callbackurlhttp'] : 'http';
                $redirectUri               = isset($post['callbackurl'])? $post['callbackurl'] : '';
                $redirectUri               = $callbackurlhttp."".$redirectUri ;
                $appname                   = isset($post['mo_oauth_app_name'])? $post['mo_oauth_app_name'] : '';
                $db     = JFactory::getDbo();
                $query  = $db->getQuery(true);
                $fields = array(
                    $db->quoteName('appname') . ' = '.$db->quote($appname),
                    $db->quoteName('redirecturi') . ' = '.$db->quote($redirectUri),
                );

                $conditions = array(
                    $db->quoteName('id') . ' = 1'
                );

                $query->update($db->quoteName('#__miniorange_oauth_config'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $result = $db->execute();
                $returnURL  = 'index.php?option=com_miniorange_oauth&view=accountsetup&moAuthAddApp='.$post['mo_oauth_app_name'].'&progress=step2';
                $errMessage = 'Redirect URI configuration completed successfully! Now, proceed to Step 2 to set up the client ID and client secret details';
            }
            else
            {
                $returnURL  = 'index.php?option=com_miniorange_oauth&view=accountsetup&moAuthAddApp='.$post['mo_oauth_app_name'];
                $errMessage = 'Please enter the redirect URI correctly';
                $this->setRedirect($returnURL,$errMessage,'error');
                return;
            }
            
        }
        else if(isset($post['oauth_config_form_step2']))
        {
            $clientid                = isset($post['mo_oauth_client_id'])? $post['mo_oauth_client_id'] : '';
            $clientsecret            = isset($post['mo_oauth_client_secret'])? $post['mo_oauth_client_secret'] : '';
            $scope                   = isset($post['mo_oauth_scope'])? $post['mo_oauth_scope'] : '';
            $appname                 = isset($post['mo_oauth_app_name'])? $post['mo_oauth_app_name'] : '';
            $customappname           = isset($post['mo_oauth_custom_app_name'])? $post['mo_oauth_custom_app_name'] : '';
            $appEndpoints            = json_decode($appD->getAppJason(),true);
            $appEndpoints            = $appEndpoints[$appname];  
            $authorizeurl            = isset($post['mo_oauth_authorizeurl'])? $post['mo_oauth_authorizeurl'] : '';
            $accesstokenurl          = isset($post['mo_oauth_accesstokenurl'])? $post['mo_oauth_accesstokenurl'] : '';
            $resourceownerdetailsurl = isset($post['mo_oauth_resourceownerdetailsurl'])? $post['mo_oauth_resourceownerdetailsurl'] : '';
            $current = "";
            if($authorizeurl =="" && $accesstokenurl=="" && $resourceownerdetailsurl == "")
            {
                $authorizeurl            = isset($appEndpoints['authorize'])? $appEndpoints['authorize'] : '';
                $accesstokenurl          = isset($appEndpoints['token'])? $appEndpoints['token'] : '';
                $resourceownerdetailsurl = isset($appEndpoints['userinfo'])? $appEndpoints['userinfo'] : '';
                $appData                 = json_decode($appD->getAppData(),true);
                $appData                 = explode(",",$appData[$appname]['1']);
                $scope                   = isset($appEndpoints['scope'])? $appEndpoints['scope'] : 'email';
    
    
                foreach($appData as $key=>$val)
                {
                    if(strpos($post[$val], 'http') !==false){
                        if(strpos($post[$val], 'https://') !== false){
                            $current = trim($post[$val],"https:// /");
                        }
                        if(strpos($post[$val], 'http://') !== false){
                            $current = trim($post[$val],"http:// /");
                        }
                    }
                    else{
                        $current = $post[$val];
                    }
                    
                    $authorizeurl            = str_replace("{".strtolower($val)."}",$current,$authorizeurl);
                    $accesstokenurl          = str_replace("{".strtolower($val)."}",$current,$accesstokenurl);
                    $resourceownerdetailsurl = str_replace("{".strtolower($val)."}",$current,$resourceownerdetailsurl);
    
                }
            }
    
            $in_header               = isset($post['mo_oauth_in_header'])?$post['mo_oauth_in_header']:'';
            $enableOAuthLoginButton  = isset( $post['login_link_check']) ? $post['login_link_check'] : '0';
            $in_body                 = isset($post['mo_oauth_body'])?$post['mo_oauth_body']:'';
            $in_header_or_body       = "inHeader" ;
            if($in_header=='1' && $in_body=='1')
            {
                $in_header_or_body = "both";
            }
            else if($in_body=='1')
            {
                $in_header_or_body ="inBody";
            }
    
            $db     = JFactory::getDbo();
            $query  = $db->getQuery(true);
            $fields = array(
                $db->quoteName('appname') . ' = '.$db->quote($appname),
                $db->quoteName('custom_app') . ' = '.$db->quote($customappname),
                $db->quoteName('client_id') . ' = '.$db->quote(trim($clientid)),
                $db->quoteName('client_secret') . ' = '.$db->quote(trim($clientsecret)),
                $db->quoteName('app_scope') . ' = '.$db->quote($scope),
                $db->quoteName('authorize_endpoint') . ' = '.$db->quote(trim($authorizeurl)),
                $db->quoteName('access_token_endpoint') . ' = '.$db->quote(trim($accesstokenurl)),
                $db->quoteName('user_info_endpoint') . ' = '.$db->quote(trim($resourceownerdetailsurl)),
                $db->quoteName('in_header_or_body').'='.$db->quote($in_header_or_body),
                $db->quoteName('login_link_check') . ' = '.$db->quote($enableOAuthLoginButton)
    
            );
            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );
    
            $query->update($db->quoteName('#__miniorange_oauth_config'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();
            $returnURL  = 'index.php?option=com_miniorange_oauth&view=accountsetup&moAuthAddApp='.$post['mo_oauth_app_name'].'&progress=step3';
            $errMessage = 'Your configuration completed successfully! Now, proceed to Step 3 to configure the basic attribute mapping';
        }
        
        $c_date = MoOauthCustomer::getAccountDetails();

        if($c_date['cd_plugin']==''){

            $time = time();
            $c_time = date('m/d/Y H:i:s', time());
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('cd_plugin') . ' = '.$db->quote($time),

            );

            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );

            $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();

        }else{

            $c_time = date('m/d/Y H:i:s', $c_date['cd_plugin']);

        }

        $dVar=new JConfig();
        $check_email = $dVar->mailfrom;
        if($c_date['contact_admin_email']!=NULL)
        {
            $check_email=$c_date['contact_admin_email'];
        }
        $base_url = JURI::root();
        $dno_ssos = 0;
        $tno_ssos = 0;
        $previous_update = '';
        $present_update = '';
        MoOauthCustomer::plugin_efficiency_check($check_email,$appname,$base_url, $c_time, $dno_ssos, $tno_ssos, $previous_update, $present_update,'NA', $scope, $authorizeurl, $accesstokenurl, $resourceownerdetailsurl, $in_header_or_body);
        $this->setRedirect($returnURL,$errMessage );
    }

    function saveMapping(){
        $post=	JFactory::getApplication()->input->post->getArray();

        $email_attr = isset($post['mo_oauth_email_attr'])? $post['mo_oauth_email_attr'] : '';
        $first_name_attr = isset($post['mo_oauth_first_name_attr'])? $post['mo_oauth_first_name_attr'] : '';

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('email_attr') . ' = '.$db->quote($email_attr),
            $db->quoteName('first_name_attr') . ' = '.$db->quote($first_name_attr),
        );

        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__miniorange_oauth_config'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $result = $db->execute();

        $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=configuration&progress=step4',JText::_('COM_MINIORANGE_OAUTH_ATTRIBUTE_MAPPING_SAVED_SUCCESSFULLY') );
    }

    function clearConfig(){
        $post=	JFactory::getApplication()->input->post->getArray();

        $clientid = "";
        $clientsecret = "";
        $scope = "";
        $appname = "";
        $customappname = "";
        $authorizeurl = "";
        $accesstokenurl = "";
        $resourceownerdetailsurl = "";
        $email_attr="";
        $first_name_attr="";
        $test_attribute_name = "";

        $db = JFactory::getDbo(); 
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('appname') . ' = '.$db->quote($appname),
            $db->quoteName('custom_app') . ' = '.$db->quote($customappname),
            $db->quoteName('client_id') . ' = '.$db->quote($clientid),
            $db->quoteName('client_secret') . ' = '.$db->quote($clientsecret),
            $db->quoteName('app_scope') . ' = '.$db->quote($scope),
            $db->quoteName('authorize_endpoint') . ' = '.$db->quote($authorizeurl),
            $db->quoteName('access_token_endpoint') . ' = '.$db->quote($accesstokenurl),
            $db->quoteName('user_info_endpoint') . ' = '.$db->quote($resourceownerdetailsurl),
            $db->quoteName('redirecturi') . ' = '.$db->quote(''),
            $db->quoteName('email_attr') . ' = '.$db->quote($email_attr),
            $db->quoteName('first_name_attr') . ' = '.$db->quote($first_name_attr),
            $db->quoteName('test_attribute_name') . ' = '.$db->quote($test_attribute_name),
        );

        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__miniorange_oauth_config'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $result = $db->execute();

        $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=configuration',JText::_('COM_MINIORANGE_OAUTH_APP_CONFIGURATION_RESET'));
    }

    function moOAuthRegisterCustomer(){

        $email = '';
        $phone = '';
        $password = '';
        $confirmPassword = '';


        $password = (JFactory::getApplication()->input->post->getArray()["password"]);
        $confirmPassword = (JFactory::getApplication()->input->post->getArray()["confirmPassword"]);

        $email=(JFactory::getApplication()->input->post->getArray()["email"]);

        if( MoOAuthUtility::check_empty_or_null( $email ) || MoOAuthUtility::check_empty_or_null($password ) || MoOAuthUtility::check_empty_or_null($confirmPassword ) ) {
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account',  JText::_('COM_MINIORANGE_OAUTH_ALL_FIELDS_REQUIRED_TO_REGISTER'),'error');
            return;
        } else if( strlen( $password ) < 6 || strlen( $confirmPassword ) < 6){	
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account',  JText::_('COM_MINIORANGE_OAUTH_ENTER_PASSWORD_OF_MIN_LENGTH'),'error');
            return;
        } else{
            $email = JFactory::getApplication()->input->post->getArray()["email"];
            $email = strtolower($email);
            $phone = JFactory::getApplication()->input->post->getArray()["phone"];
            $password =JFactory::getApplication()->input->post->getArray()["password"];
            $confirmPassword = JFactory::getApplication()->input->post->getArray()["confirmPassword"];
        }

        if( strcmp( $password, $confirmPassword) == 0 ) {

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('email') . ' = ' . $db->quote($email),
                $db->quoteName('admin_phone') . ' = ' . $db->quote($phone),
                $db->quoteName('password') . ' = ' . $db->quote($password),

            );

            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );

            $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();

            $customer = new MoOauthCustomer();
            $content = json_decode($customer->check_customer($email), true);
            if( strcasecmp( $content['status'], 'CUSTOMER_NOT_FOUND') == 0 ){
                $auth_type = 'EMAIL';
                $content = json_decode($customer->send_otp_token($auth_type, $email), true);
                if(strcasecmp($content['status'], 'SUCCESS') == 0) {

                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $fields = array(
                        $db->quoteName('email_count') . ' = ' . $db->quote(1),
                        $db->quoteName('transaction_id') . ' = ' . $db->quote($content['txId']),
                        $db->quoteName('login_status') . ' = ' . $db->quote(0),
                        $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_DELIVERED_SUCCESS')
                    );
                    $conditions = array(
                        $db->quoteName('id') . ' = 1'
                    );

                    $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
                    $db->setQuery($query);
                    $result = $db->execute();

                    $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account', JText::_('COM_MINIORANGE_OAUTH_ONE_TIME_PASSWORD_SENT1') . $email . JText::_('COM_MINIORANGE_OAUTH_ONE_TIME_PASSWORD_SENT2'));


                } else {

                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $fields = array(
                        $db->quoteName('login_status') . ' = ' . $db->quote(0),
                        $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_DELIVERED_FAILURE')
                    );
                    $conditions = array(
                        $db->quoteName('id') . ' = 1'
                    );

                    $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
                    $db->setQuery($query);
                    $result = $db->execute();

                    $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account',JText::_('COM_MINIORANGE_OAUTH_ERROR_SENDING_EMAIL'),'error');


                }
            } else if( strcasecmp( $content['status'], 'CURL_ERROR') == 0 ){

                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $fields = array(
                    $db->quoteName('login_status') . ' = ' . $db->quote(0),
                    $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_DELIVERED_FAILURE')
                );
                $conditions = array(
                    $db->quoteName('id') . ' = 1'
                );

                $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $result = $db->execute();

                $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account', $content['statusMessage'],'error');

            } else{
                $content = $customer->get_customer_key($email,$password);
                $customerKey = json_decode($content, true);
                if(json_last_error() == JSON_ERROR_NONE) {
                    $this->save_customer_configurations($email,$customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['phone']);
                    $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=license', JText::_('COM_MINIORANGE_OAUTH_ACCOUNT_RETRIEVED_SUCCESSFULLY'));
                } else {
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $fields = array(
                        $db->quoteName('login_status') . ' = ' . $db->quote(1),
                        $db->quoteName('registration_status') . ' = ' . $db->quote('')
                    );
                    $conditions = array(
                        $db->quoteName('id') . ' = 1'
                    );

                    $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
                    $db->setQuery($query);
                    $result = $db->execute();

                    $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account', JText::_('COM_MINIORANGE_OAUTH_ENTER_VALID_PASSWORD'),'error');

                }
            }

        } else {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('login_status') . ' = ' . $db->quote(0)
            );
            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );
            $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account', JText::_('COM_MINIORANGE_OAUTH_PASSWORD_MISMATCH'),'error');
        }
    }

    function validateOtp(){

        $otp_token =JFactory::getApplication()->input->post->getArray()["otp_token"];

        if( MoOAuthUtility::check_empty_or_null( $otp_token) ) {
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account', JText::_('COM_MINIORANGE_OAUTH_ENTER_A_VALID_OTP'),'error');
            return;
        } else{
            $otp_token =  JFactory::getApplication()->input->post->getArray()['otp_token'] ;
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true); 
        $query->select('transaction_id');
        $query->from($db->quoteName('#__miniorange_oauth_customer'));
        $query->where($db->quoteName('id')." = 1");

        $db->setQuery($query);
        $transaction_id = $db->loadResult();

        $customer = new MoOauthCustomer();
        $content = json_decode($customer->validate_otp_token($transaction_id, trim($otp_token) ),true);
        if(strcasecmp($content['status'], 'SUCCESS') == 0) {
            $customerKey = json_decode($customer->create_customer(), true);

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('email_count') . ' = ' . $db->quote(0),
                $db->quoteName('sms_count') . ' = ' . $db->quote(0)
            );
            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );
            $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();
            if(strcasecmp($customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0) {
                $content = $customer->get_customer_key();
                $customerKey = json_decode($content, true);
                if(json_last_error() == JSON_ERROR_NONE) {
                    $this->save_customer_configurations($customerKey['email'], $customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['phone']);
                    $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup', JText::_('COM_MINIORANGE_OAUTH_ACCOUNT_RETRIEVED_SUCCESSFULLY'));
                } else {
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $fields = array(
                        $db->quoteName('login_status') . ' = ' . $db->quote(1),
                        $db->quoteName('password') . ' = ' . $db->quote(''),
                    );
                    $conditions = array(
                        $db->quoteName('id') . ' = 1'
                    );

                    $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
                    $db->setQuery($query);
                    $result = $db->execute();

                    $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account', JText::_('COM_MINIORANGE_OAUTH_ENTER_VALID_PASSWORD'),'error');

                }
            } else if(strcasecmp($customerKey['status'], 'SUCCESS') == 0) {

                $this->save_customer_configurations($customerKey['email'], $customerKey['id'], $customerKey['apiKey'], $customerKey['token'],$customerKey[' phone']);
                $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=license',JText::_('COM_MINIORANGE_OAUTH_THANK_YOU_FOR_REGISTERING_WITH_MINIORANGE_MESSSAGE'));
            }else if(strcasecmp($customerKey['status'],'INVALID_EMAIL_QUICK_EMAIL')==0){

                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $fields = array(
                    $db->quoteName('registration_status') . ' = ' . $db->quote(''),
                    $db->quoteName('email') . ' = ' . $db->quote(''),
                    $db->quoteName('password') . ' = ' . $db->quote(''),
                    $db->quoteName('transaction_id') . ' = ' . $db->quote(''),
                );
                $conditions = array(
                    $db->quoteName('id') . ' = 1'
                );

                $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $result = $db->execute();

                $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account',JText::_('COM_MINIORANGE_OAUTH_ERROR_CREATING_YOUR_ACCOUNT'),'error');

            }
            
        } else if( strcasecmp( $content['status'], 'CURL_ERROR') == 0) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_VALIDATION_FAILURE')
            );
            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );

            $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account', $content['statusMessage'],'error');

        } else {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_VALIDATION_FAILURE')
            );
            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );

            $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account',JText::_('COM_MINIORANGE_OAUTH_INVALID_OTP'),'error');

        }
    }

    function resendOtp(){


        $customer = new MoOauthCustomer();
        $auth_type = 'EMAIL';

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('email');
        $query->from($db->quoteName('#__miniorange_oauth_customer'));
        $query->where($db->quoteName('id')." = 1");

        $db->setQuery($query);
        $email = $db->loadResult();

        $content = json_decode($customer->send_otp_token($auth_type, $email), true);
        if(strcasecmp($content['status'], 'SUCCESS') == 0) {

            $customer_details = MoOAuthUtility::getCustomerDetails();
            $email_count = $customer_details['email_count'];
            $admin_email = $customer_details['email'];

            if($email_count != '' && $email_count >= 1){
                $email_count = $email_count + 1;

                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $fields = array(
                    $db->quoteName('email_count') . ' = ' . $db->quote($email_count),
                    $db->quoteName('transaction_id') . ' = ' . $db->quote($content['txId']),
                    $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_DELIVERED_SUCCESS')
                );
                $conditions = array(
                    $db->quoteName('id') . ' = 1'
                );

                $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $result = $db->execute();

                $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account', JText::_('COM_MINIORANGE_OAUTH_ANOTHER_ONE_TIME_PASSWORD_SENT1'). ( $admin_email) . JText::_('COM_MINIORANGE_OAUTH_ANOTHER_ONE_TIME_PASSWORD_SENT2'));

            }else{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $fields = array(
                    $db->quoteName('email_count') . ' = ' . $db->quote(1),
                    $db->quoteName('transaction_id') . ' = ' . $db->quote($content['txId']),
                    $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_DELIVERED_SUCCESS')
                );
                $conditions = array(
                    $db->quoteName('id') . ' = 1'
                );

                $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $result = $db->execute();
                $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account',  JText::_('COM_MINIORANGE_OAUTH_ONE_TIME_PASSWORD_SENT1'). ($admin_email) . JText::_('COM_MINIORANGE_OAUTH_ONE_TIME_PASSWORD_SENT2'));

            }

        } else if( strcasecmp( $content['status'], 'CURL_ERROR') == 0) {

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_DELIVERED_FAILURE')
            );
            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );

            $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account',  $content['statusMessage'],'error');

        } else{
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_DELIVERED_FAILURE')
            );
            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );

            $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account', JText::_('COM_MINIORANGE_OAUTH_ERROR_SENDING_EMAIL'),'error');

        }
    }

    function cancelform(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('email') . ' = ' . $db->quote(''),
            $db->quoteName('password') . ' = ' . $db->quote(''),
            $db->quoteName('customer_key') . ' = ' . $db->quote(''),
            $db->quoteName('admin_phone') . ' = ' . $db->quote(''),
            $db->quoteName('customer_token') . ' = ' . $db->quote(''),
            $db->quoteName('api_key') . ' = ' . $db->quote(''),
            $db->quoteName('registration_status') . ' = ' . $db->quote(''),
            $db->quoteName('login_status') . ' = ' . $db->quote(0),
            $db->quoteName('transaction_id') . ' = ' . $db->quote(''),
            $db->quoteName('email_count') . ' = ' . $db->quote(0),
            $db->quoteName('sms_count') . ' = ' . $db->quote(0),
        );
        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $result = $db->execute();
        $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account');

    }

    function phoneVerification(){
        $phone = JFactory::getApplication()->input->post->getArray()['phone_number'];
        $phone = str_replace(' ', '', $phone);

        $pattern = "/[\+][0-9]{1,3}[0-9]{10}/";

        if(preg_match($pattern, $phone, $matches, PREG_OFFSET_CAPTURE)){
            $auth_type = 'SMS';
            $customer = new MoOauthCustomer();
            $send_otp_response = json_decode($customer->send_otp_token($auth_type, $phone));
            if($send_otp_response->status == 'SUCCESS'){

                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('sms_count');
                $query->from($db->quoteName('#__miniorange_oauth_customer'));
                $query->where($db->quoteName('id')." = 1");

                $db->setQuery($query);
                $sms_count = $db->loadResult();

                if($sms_count != '' && $sms_count >= 1){
                    $sms_count = $sms_count + 1;
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $fields = array(
                        $db->quoteName('sms_count') . ' = ' . $db->quote($sms_count),
                        $db->quoteName('transaction_id') . ' = ' . $db->quote($send_otp_response->txId)
                    );
                    $conditions = array(
                        $db->quoteName('id') . ' = 1'
                    );

                    $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
                    $db->setQuery($query);
                    $result = $db->execute();

                    $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account', JText::_('COM_MINIORANGE_OAUTH_OTP_SENT1') . $sms_count .JText::_('COM_MINIORANGE_OAUTH_OTP_SENT2'). $phone);


                } else{
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $fields = array(
                        $db->quoteName('sms_count') . ' = ' . $db->quote(1),
                        $db->quoteName('transaction_id') . ' = ' . $db->quote($send_otp_response->txId)
                    );
                    $conditions = array(
                        $db->quoteName('id') . ' = 1'
                    );

                    $query->update($db->quoteName('#__miniorange_oauth_customer'))->set($fields)->where($conditions);
                    $db->setQuery($query);
                    $result = $db->execute();
                    $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account', JText::_('COM_MINIORANGE_OAUTH_ONE_TIME_PASSWORD_FOR_VERIFICATION'). $phone);
                }

            } else{
                $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account', JText::_('COM_MINIORANGE_OAUTH_ERROR_WHILE_SENDING_OTP'));
            }
        }else{

            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account',JText::_('COM_MINIORANGE_OAUTH_ENTER_PHONE_NUMBER_IN_CORRECT_FORMAT'),'error');
        }
    }

    function requestForDemoPlan()
    {
        $post=	JFactory::getApplication()->input->post->getArray();
        if(count($post)==0){
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=support');
            return;
        }
        $email          = $post['email'];
        $plan           = $post['plan'];
        $description    = $post['description'];
        $demo_trial     = $post['demo'];
        $customer       = new MoOauthCustomer();

        if($plan == "Not Sure")
            $description = $post['description'];
        $response = json_decode($customer->request_for_demo($email, $plan, $description, $demo_trial));

        if($response->status != 'ERROR')
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=support', JText::_('COM_MINIORANGE_OAUTH_YOUR_QUERY_IS_SUBMITTED'));
        else
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=support', JText::_('COM_MINIORANGE_OAUTH_AN_ERROR_OCCURRED'), 'error');
    }
    function callContactUs() {
        $post = JFactory::getApplication()->input->post->getArray();
        if(count($post)==0){
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=support');
            return;
        }
        $query_email = $post['mo_oauth_setup_call_email'];
        $query       = $post['mo_oauth_setup_call_issue'] ;
        $description =$post['mo_oauth_setup_call_desc'];
        $callDate    =$post['mo_oauth_setup_call_date'];
        $timeZone    =$post['mo_oauth_setup_call_timezone'];
        if( MoOAuthUtility::check_empty_or_null( $timeZone ) ||MoOAuthUtility::check_empty_or_null( $callDate ) ||MoOAuthUtility::check_empty_or_null( $query_email ) || MoOAuthUtility::check_empty_or_null( $query)||MoOAuthUtility::check_empty_or_null( $description) ) {
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup', JText::_('COM_MINIORANGE_OAUTH_ENTER_ALL_FIELDS_TO_SETUP_A_CALL'), 'error');
            return;
        } else{
            $contact_us = new MoOauthCustomer();
            $submited = json_decode($contact_us->request_for_demo($query_email, $query, $description,'true',$callDate, $timeZone),true);
            if(json_last_error() == JSON_ERROR_NONE) {
                if(is_array($submited) && array_key_exists('status', $submited) && $submited['status'] == 'ERROR'){
                    $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=support', $submited['message'],'error');
                }else{
                    if ( $submited == false ) {
                        $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=support', JText::_('COM_MINIORANGE_OAUTH_YOUR_QUERY_COULD_NOT_BE_SUBMITTED'),'error');
                    } else {
                        $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=support', JText::_('COM_MINIORANGE_OAUTH_YOUR_QUERY_IS_SUBMITTED'));
                    }
                }
            }

        }
    }
    function contactUs() {
        $post = JFactory::getApplication()->input->post->getArray();
        if(count($post)==0){
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=support');
            return;
        }
        $query_email = isset($post['query_email']) ?$post['query_email'] :'';
        $query       = isset($post['query']) ? $post['query']: '';
        $phone       = isset($post['query_phone'])? $post['query_phone']: '';
        $query_withconfig = isset($post['mo_oauth_query_withconfig'])? $post['mo_oauth_query_withconfig'] : ''; 
        $appDetails = $this->retrieveAttributes('#__miniorange_oauth_config');

		if ($query_withconfig != 1) {

			$appDetails['appname'] = '';
			$appDetails['custom_app'] = '';
			$appDetails['app_scope'] = '';
            $appDetails['authorize_endpoint'] = '';
		}


        if( MoOAuthUtility::check_empty_or_null( $query_email ) || MoOAuthUtility::check_empty_or_null( $query) ) {
            $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=support', JText::_('COM_MINIORANGE_OAUTH_SUBMIT_QUERY_WITH_EMAIL'), 'error');
            return;
        } else{
            $contact_us = new MoOauthCustomer();
            $submited = json_decode($contact_us->submit_contact_us($query_email, $phone, $query, $appDetails),true);
            if(json_last_error() == JSON_ERROR_NONE) {
                if(is_array($submited) && array_key_exists('status', $submited) && $submited['status'] == 'ERROR'){
                    $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=support', $submited['message'],'error');
                }else{
                    if ( $submited == false ) {
                        $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=support', JText::_('COM_MINIORANGE_OAUTH_YOUR_QUERY_COULD_NOT_BE_SUBMITTED'),'error');
                    } else {
                        $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=support', JText::_('COM_MINIORANGE_OAUTH_YOUR_QUERY_IS_SUBMITTED'));
                    }
                }
            }

        }
 
 
    }
    function removeAccount()
    {
        $nameOfDatabase = '#__miniorange_oauth_customer';
        $updateFieldsArray = array(
            'email'               => '',
            'password'            => '',
            'customer_key'        => '',
            'api_key'             => '',
            'customer_token'      => '',
            'admin_phone'         => '',
            'login_status'        => 0,
            'registration_status' => 'SUCCESS',
            'email_count'         => 0,
            'sms_count'           => 0, 
        );
        $this->updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
        $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account', JText::_('COM_MINIORANGE_OAUTH_ACCOUNT_REMOVED_SUCCESSFULLY'));
    }
    function updateDatabaseQuery($database_name, $updatefieldsarray){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        foreach ($updatefieldsarray as $key => $value)
        {
            $database_fileds[] = $db->quoteName($key) . ' = ' . $db->quote($value);
        }
        $query->update($db->quoteName($database_name))->set($database_fileds)->where($db->quoteName('id')." = 1");
        $db->setQuery($query);
        $db->execute();
    }
    
    function exportConfiguration()
    {
        $appDetails = $this->retrieveAttributes('#__miniorange_oauth_config');
        $customer_details = $this->retrieveAttributes('#__miniorange_oauth_customer');
        $customapp = $appDetails['custom_app'];
        $clientid = $appDetails['client_id'];

        if($clientid =='' && $clientsecret =='')
        {
			$this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup', JText::_('COM_MINIORANGE_OAUTH_ENTER_CLIENT_ID_BEFORE_DOWNLOADING'), 'error');
			return;
        }

        $plugin_configuration = array();
        array_push($plugin_configuration, $appDetails, $customer_details);
		
		
		$filecontentd = json_encode($plugin_configuration, JSON_PRETTY_PRINT);
		
		header('Content-Disposition: attachment; filename=oauth-client.json'); 
		header('Content-Type: application/json'); 
		print_r($filecontentd);

        $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup',JText::_('COM_MINIORANGE_OAUTH_PLUGIN_CONFIGURATION_DOWNLOADED_SUCCESSFULLY') );
        exit;
    }

    function retrieveAttributes($tablename){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName($tablename));
        $query->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        return $db->loadAssoc();
    }

    function moOAuthProxyConfigReset()
    {
        $nameOfDatabase= '#__miniorange_oauth_config';
        $updateFieldsArray = array('proxy_server_url' => '', 'proxy_server_port' => '80', 'proxy_username' => '', 'proxy_password' => '', 'proxy_set' => '');
		
        $this->updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
        $this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account', JText::_('COM_MINIORANGE_OAUTH_PROXY_SETTING_RESET'));
    }

    function moOAuthProxyServer(){

		$post=	JFactory::getApplication()->input->post->getArray();
		$proxy_server_url = isset($post['proxy_server_url'])? $post['proxy_server_url'] : '';
		$proxy_server_port = isset($post['proxy_server_port'])? $post['proxy_server_port'] : '';
		$proxy_username = isset($post['proxy_username'])? $post['proxy_username'] : '';
		$proxy_password = isset($post['proxy_password'])? $post['proxy_password'] : '';

		$nameOfDatabase = '#__miniorange_oauth_config';
		$updateFieldsArray = array(
			'proxy_server_url' 	  	  => $proxy_server_url,
			'proxy_server_port' 	  => $proxy_server_port,
			'proxy_username'          => $proxy_username,  
			'proxy_password'          => $proxy_password,
			'proxy_set'               => 'yes',
		);

        $this->updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
		$this->setRedirect('index.php?option=com_miniorange_oauth&view=accountsetup&tab-panel=account', JText::_('COM_MINIORANGE_OAUTH_PROXY_SERVER_SAVED_SUCCESSFULLY'));
	}
}