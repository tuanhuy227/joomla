<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Miniorange_saml
 * @author     meenakshi <meenakshi@miniorange.com>
 * @copyright  2016 meenakshi
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
// use Joomla\CMS\Component\ComponentHelper;
require_once JPATH_COMPONENT . '/helpers/mo-saml-utility.php';
require_once JPATH_COMPONENT . '/helpers/mo-saml-customer-setup.php';
require_once JPATH_COMPONENT . '/helpers/mo_saml_support.php';
require_once JPATH_COMPONENT . '/helpers/miniorange_saml.php';
require_once JPATH_COMPONENT . '/helpers/MoConstants.php';
require_once JPATH_COMPONENT . '/helpers/saml_handler.php';
 
// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_miniorange_saml'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Miniorange_saml', JPATH_COMPONENT_ADMINISTRATOR);


$controller = BaseController::getInstance('Miniorange_saml');
if(!empty(Factory::getApplication()->input->get('task')))
{
  $controller->execute(Factory::getApplication()->input->get('task'));
}
else
{
    $controller->execute('');   
}
$controller->redirect();

