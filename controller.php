<?php

/**
 * @version     1.0.1
 * @package     com_catalogo
 * @copyright   Copyright (C) 2014. Todos los derechos reservados.
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 * @author      Javier Mata <javimata@gmail.com> - http://www.javimata.com
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class CatalogoController extends JControllerLegacy {

    /**
     * Method to display a view.
     *
     * @param	boolean			$cachable	If true, the view output will be cached
     * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController		This object to support chaining.
     * @since	1.5
     */
    public function display($cachable = false, $urlparams = false) {
        require_once JPATH_COMPONENT . '/helpers/catalogo.php';

        $view = JFactory::getApplication()->input->getCmd('view', 'productos');
        JFactory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;
    }

}
