<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_miniorange_oauth
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('jquery.framework');
/**
 * Account Setup View
 *
 * @since  0.0.1
 */
class miniorangeoauthViewAccountSetup extends JViewLegacy
{
    function display($tpl = null)
    {
        // Get data from the model
        $this->lists        = $this->get('List');
        //$this->pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JFactory::getApplication()->enqueueMessage(500, implode('<br />', $errors));

            return false;
        }
        $this->setLayout('accountsetup');
        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function addToolBar()
    {
        JToolBarHelper::title(JText::_('COM_MINIORANGE_OAUTH_PLUGIN_TITLE'),'mo_oauth_logo mo_oauth_icon');
    }
}