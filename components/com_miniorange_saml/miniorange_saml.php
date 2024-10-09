<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Miniorange_saml
 * @author     meenakshi <meenakshi@miniorange.com>
 * @copyright  2016 meenakshi
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Miniorange_saml', JPATH_COMPONENT);
JLoader::register('Miniorange_samlController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = JControllerLegacy::getInstance('Miniorange_saml');
if(!empty(Factory::getApplication()->input->get('task')))
{
  $controller->execute(Factory::getApplication()->input->get('task'));
}
else
{
    $controller->execute('');   
}
$controller->redirect();
