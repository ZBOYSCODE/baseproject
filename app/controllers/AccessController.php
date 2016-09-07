<?php

	namespace Gabs\Controllers;

	class AccessController extends ControllerBase
	{

		public function initialize()
		{
	    	$this->_themeArray = array('topMenu'=>true, 'menuSel'=>'','pcView'=>'', 'pcData'=>'', 'jsScript'=>'');
	    }
	
		public function denegadoAction()
		{
			$themeArray = $this->_themeArray;
    		$themeArray['pcView'] = 'access/denegado';
    		echo $this->view->render('theme', $themeArray);
		}
	}
