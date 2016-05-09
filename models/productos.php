<?php

/**
 * @version     1.0.1
 * @package     com_catalogo
 * @copyright   Copyright (C) 2014. Todos los derechos reservados.
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 * @author      Javier Mata <javimata@gmail.com> - http://www.javimata.com
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Catalogo records.
 */
class CatalogoModelProductos extends JModelList
{

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                                'id', 'a.id',
                'title', 'a.title',
                'alias', 'a.alias',
                'tags', 'a.tags',
                'categoria', 'a.categoria',
                'introtext', 'a.introtext',
                'descripcion', 'a.descripcion',
                'imagen', 'a.imagen',
                'imagen_descripcion', 'a.imagen_descripcion',
                'images', 'a.images',
                'galeria', 'a.galeria',
                'archivo', 'a.archivo',
                'precio', 'a.precio',
                'precio_oferta', 'a.precio_oferta',
                'oferta', 'a.oferta',
                'featured', 'a.featured',
                'created_by', 'a.created_by',
                'state', 'a.state',
                'ordering', 'a.ordering',

            );
        }
        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since    1.6
     */
    protected function populateState($ordering = null, $direction = null)
    {


        // Initialise variables.
        $app = JFactory::getApplication();
        $showPagination = JComponentHelper::getParams('com_catalogo')->get('pagination');
        $limite         = JComponentHelper::getParams('com_catalogo')->get('paginar_limite');

        // List state information
        if ($showPagination==1):
        $limit = $limite;
        $this->setState('list.limit', $limit);
        endif;

        $limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);

        if ($list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array')) {
            foreach ($list as $name => $value) {
                // Extra validations
                switch ($name) {
                    case 'fullordering':
                        $orderingParts = explode(' ', $value);

                        if (count($orderingParts) >= 2) {
                            // Latest part will be considered the direction
                            $fullDirection = end($orderingParts);

                            if (in_array(strtoupper($fullDirection), array('ASC', 'DESC', ''))) {
                                $this->setState('list.direction', $fullDirection);
                            }

                            unset($orderingParts[count($orderingParts) - 1]);

                            // The rest will be the ordering
                            $fullOrdering = implode(' ', $orderingParts);

                            if (in_array($fullOrdering, $this->filter_fields)) {
                                $this->setState('list.ordering', $fullOrdering);
                            }
                        } else {
                            $this->setState('list.ordering', $ordering);
                            $this->setState('list.direction', $direction);
                        }
                        break;

                    case 'ordering':
                        if (!in_array($value, $this->filter_fields)) {
                            $value = $ordering;
                        }
                        break;

                    case 'direction':
                        if (!in_array(strtoupper($value), array('ASC', 'DESC', ''))) {
                            $value = $direction;
                        }
                        break;

                    case 'limit':
                        $limit = $value;
                        break;

                    // Just to keep the default case
                    default:
                        $value = $value;
                        break;
                }

                $this->setState('list.' . $name, $value);
            }
        }

        // Receive & set filters
        if ($filters = $app->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array')) {
            foreach ($filters as $name => $value) {
                $this->setState('filter.' . $name, $value);
            }
        }

        $this->setState('list.ordering', $app->input->get('filter_order'));
        $this->setState('list.direction', $app->input->get('filter_order_Dir'));
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return    JDatabaseQuery
     * @since    1.6
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $limitstart = JRequest::getUInt('limitstart', 0);
        $start = JRequest::getUInt('start', 0);

        // Select the required fields from the table.
        $query
            ->select(
                $this->getState(
                    'list.select', 'DISTINCT a.*'
                )
            );

        $query->from('`#__catalogo` AS a');

		// Join over the category 'categoria'
		$query->select('categoria.title AS categoria_title, categoria.id AS categoria_id');
		$query->join('LEFT', '#__categories AS categoria ON categoria.id = a.categoria');

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.title LIKE '.$search.'  OR  a.introtext LIKE '.$search.' )');
            }
        }

        $query->where('a.state = 1');

        //OBTENER CATEGORIA SI SE ESPECIFICA EN EL MENU ITEM
        $item_id        = $_REQUEST["Itemid"];
        $cid            = $_REQUEST["catid"];
        $categoria      = $_REQUEST["catid"];
        if ($item_id):

            $app        = JFactory::getApplication();
            $menuitem   = $app->getMenu()->getItem($item_id);
            $params     = $menuitem->params;

            $catid = $params->get("category_id");
            if ($catid>0){
                //$query->where('a.categoria = ' . $catid );
            }

        endif;

        /*

        if ($cid):

            if ($cid>0){
                $query->where('a.categoria = ' . $cid );
            }

        endif;

        */

        //echo $catid;

        if ($categoria>0){
            $catid = $categoria;
        }

        if ($subcategoria>0){
            $catid = $subcategoria;
        }


        if ($catid):

            if ($catid>0){
                //$query->where('a.category = ' . $catid , 'OR');
                $q = "a.categoria = " . $catid . " ";
            }

            // SUBCATEGORIAS
            $dbm=JFactory::getDBO();
            $sql = 'SELECT id FROM #__categories WHERE published=1 AND parent_id='.$catid;
            $dbm->setQuery( $sql );
            $subcats = $dbm->loadObjectList();

            if (count($subcats)):
                $q .= " OR ";
                $cuantos = 1;
                foreach ($subcats as $scat) {

                    //$query->where('a.category = ' . $scat->id,'OR');
                    //var_dump($query->where);
                    if ($cuantos>1){ $q .= "OR "; }
                    $q .= "a.categoria = ".$scat->id . " ";
                    $cuantos++;

                    $sql = 'SELECT id FROM #__categories WHERE published=1 AND parent_id='.$scat->id;
                    $dbm->setQuery( $sql );
                    $subsubcats = $dbm->loadObjectList();
                    if (count($subsubcats)):
                    foreach ($subsubcats as $sscat) {
                        if ($cuantos>1){ $q .= "OR "; }
                        $q .= "a.categoria = ".$sscat->id . " ";
                        $cuantos++;
                    }
                    endif;
                }


            endif;

            $query->where('('.$q.')');


        endif;

        // echo $query->where;

        $promociones = $params->get("promociones");

        if ($promociones==2):

            $query->where('a.oferta = 0');

        elseif ($promociones==3):

            $query->where('a.oferta = 1','AND');

        endif;

        /*
        if ( $_SERVER['REMOTE_ADDR'] == "187.133.60.64"):
            echo $query->where;
        endif;
        */


		//Filtering categoria
		$filter_categoria = $this->state->get("filter.categoria");
		if ($filter_categoria) {
			$query->where("a.categoria = '".$db->escape($filter_categoria)."'");
		}

        //Filtering start_package
        $filter_start_package_from = $this->state->get("filter.start_package.from");
        if ($filter_start_package_from) {
            $query->where("a.start_package >= '".$filter_start_package_from."'");
        }
        $filter_start_package_to = $this->state->get("filter.start_package.to");
        if ($filter_start_package_to) {
            $query->where("a.start_package <= '".$filter_start_package_to."'");
        }

        //Filtering finish_package
        $filter_finish_package_from = $this->state->get("filter.finish_package.from");
        if ($filter_finish_package_from) {
            $query->where("a.finish_package >= '".$filter_finish_package_from."'");
        }
        $filter_finish_package_to = $this->state->get("filter.finish_package.to");
        if ($filter_finish_package_to) {
            $query->where("a.finish_package <= '".$filter_finish_package_to."'");
        }
        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }else{

            // $app = JFactory::getApplication();
            $ordenBy  = JRequest::getString('by');
            $ordenDir = JRequest::getString('dir');

            if ( $ordenBy == "" ){

                $ordenBy  = JComponentHelper::getParams('com_catalogo')->get('ordenBy');
                $ordenDir = JComponentHelper::getParams('com_catalogo')->get('ordenDireccion');

            }

            if ( $ordenDir == "" && $ordenBy != "" ){

                $ordenDir = "ASC";

            }


            if ($ordenBy!="RAND()"){
                $query->order('a.' . $ordenBy . ' ' . $ordenDir);
            }else{                
                $query->order('RAND()');

            }

        }

        // echo $query;
        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        foreach($items as $item){
	

				if ( isset($item->tags) ) {
					// Catch the item tags (string with ',' coma glue)
					$tags = explode(",",$item->tags);

					$db = JFactory::getDbo();
					$namedTags = array(); // Cleaning and initalization of named tags array

					// Get the tag names of each tag id
					foreach ($tags as $tag) {

						$query = $db->getQuery(true);
						$query->select("title");
						$query->from('`#__tags`');
						$query->where( "id=" . intval($tag) );

						$db->setQuery($query);
						$row = $db->loadObjectList();

						// Read the row and get the tag name (title)
						if (!is_null($row)) {
							foreach ($row as $value) {
								if ( $value && isset($value->title) ) {
									$namedTags[] = trim($value->title);
								}
							}
						}

					}

					// Finally replace the data object with proper information
					$item->tags = !empty($namedTags) ? implode(', ',$namedTags) : $item->tags;
		        }

			if ( isset($item->categoria) ) {

			// Get the title of that particular template
				$title = CatalogoFrontendHelper::getCategoryNameByCategoryId($item->categoria);

				// Finally replace the data object with proper information
				$item->categoria = !empty($title) ? $title : $item->categoria;
			}
        }
        return $items;
    }
}