<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Vast Development Method 
/-------------------------------------------------------------------------------------------------------/

	@version		1.2.9
	@build			30th November, 2015
	@created		22nd October, 2015
	@package		Sermon Distributor
	@subpackage		preachers.php
	@author			Llewellyn van der Merwe <https://www.vdm.io/>	
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
  ____  _____  _____  __  __  __      __       ___  _____  __  __  ____  _____  _  _  ____  _  _  ____ 
 (_  _)(  _  )(  _  )(  \/  )(  )    /__\     / __)(  _  )(  \/  )(  _ \(  _  )( \( )( ___)( \( )(_  _)
.-_)(   )(_)(  )(_)(  )    (  )(__  /(__)\   ( (__  )(_)(  )    (  )___/ )(_)(  )  (  )__)  )  (   )(  
\____) (_____)(_____)(_/\/\_)(____)(__)(__)   \___)(_____)(_/\/\_)(__)  (_____)(_)\_)(____)(_)\_) (__) 

/------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * Preachers Controller
 */
class SermondistributorControllerPreachers extends JControllerAdmin
{
	protected $text_prefix = 'COM_SERMONDISTRIBUTOR_PREACHERS';
	/**
	 * Proxy for getModel.
	 * @since	2.5
	 */
	public function getModel($name = 'Preacher', $prefix = 'SermondistributorModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		
		return $model;
	}

	public function exportData()
	{
		// [7261] Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// [7263] check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('preacher.export', 'com_sermondistributor') && $user->authorise('core.export', 'com_sermondistributor'))
		{
			// [7267] Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// [7270] Sanitize the input
			JArrayHelper::toInteger($pks);
			// [7272] Get the model
			$model = $this->getModel('Preachers');
			// [7274] get the data to export
			$data = $model->getExportData($pks);
			if (SermondistributorHelper::checkArray($data))
			{
				// [7278] now set the data to the spreadsheet
				$date = JFactory::getDate();
				SermondistributorHelper::xls($data,'Preachers_'.$date->format('jS_F_Y'),'Preachers exported ('.$date->format('jS F, Y').')','preachers');
			}
		}
		// [7283] Redirect to the list screen with error.
		$message = JText::_('COM_SERMONDISTRIBUTOR_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_sermondistributor&view=preachers', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// [7292] Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// [7294] check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('preacher.import', 'com_sermondistributor') && $user->authorise('core.import', 'com_sermondistributor'))
		{
			// [7298] Get the import model
			$model = $this->getModel('Preachers');
			// [7300] get the headers to import
			$headers = $model->getExImPortHeaders();
			if (SermondistributorHelper::checkObject($headers))
			{
				// [7304] Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('preacher_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'preachers');
				$session->set('dataType_VDM_IMPORTINTO', 'preacher');
				// [7310] Redirect to import view.
				$message = JText::_('COM_SERMONDISTRIBUTOR_IMPORT_SELECT_FILE_FOR_PREACHERS');
				$this->setRedirect(JRoute::_('index.php?option=com_sermondistributor&view=import', false), $message);
				return;
			}
		}
		// [7322] Redirect to the list screen with error.
		$message = JText::_('COM_SERMONDISTRIBUTOR_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_sermondistributor&view=preachers', false), $message, 'error');
		return;
	} 
}