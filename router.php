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

/**
 * @param	array	A named array
 * @return	array
 */
function CatalogoBuildRoute(&$query) {
    $segments = array();

    if (isset($query['task'])) {
        $segments[] = implode('/', explode('.', $query['task']));
        unset($query['task']);
    }
    if (isset($query['view'])) {
        $segments[] = $query['view'];
        unset($query['view']);
    }
    if (isset($query['id'])) {
        $segments[] = $query['id'];
        unset($query['id']);
    }
    if (isset($query['alias'])) {
        $segments[] = $query['alias'];
        unset($query['alias']);
    }
    unset($query['task']);
    unset($query['view']);    
    return $segments;

}

/**
 * @param	array	A named array
 * @param	array
 *
 * Formats:
 *
 * index.php?/catalogo/task/id/Itemid
 *
 * index.php?/catalogo/id/Itemid
 */
function CatalogoParseRoute($segments) {
    $vars = array();

    // view is always the first element of the array
    $vars['view'] = array_shift($segments);

    while (!empty($segments)) {
        $segment = array_pop($segments);
        if (is_numeric($segment)) {
            $vars['id'] = $segment;
        } else {
            $vars['task'] = $vars['view'] . '.' . $segment;
        }
    }

    return $vars;
}