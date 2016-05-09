<?php
/**
 * @version     1.0.1
 * @package     com_catalogo
 * @copyright   Copyright (C) 2014. Todos los derechos reservados.
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 * @author      Javier Mata <javimata@gmail.com> - http://www.javimata.com
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$showPagination = JComponentHelper::getParams('com_catalogo')->get('pagination');
$segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));

//JHtml::_('bootstrap.tooltip');
//JHtml::_('behavior.multiselect');
//JHtml::_('formbehavior.chosen');

// $user = JFactory::getUser();
// $userId = $user->get('id');

$app    = JFactory::getApplication();
$menus  = $app->getMenu();
$itemId = $menus->getActive();

$categoria = $itemId->params->get("category_id");

if ($categoria!=""){

    $db=JFactory::getDBO();
    $sqld = "SELECT id,title,parent_id,level,description,params FROM #__categories WHERE id=$categoria LIMIT 1";
    $db->setQuery( $sqld );
    $cat_info = $db->loadObject();

    if ($cat_info->level > 1){
        $sqlc = "SELECT title,description FROM #__categories WHERE id=" . $cat_info->id . " LIMIT 1";
        $db->setQuery( $sqlc );
        $categoria_info = $db->loadObject();
        $categoria_title = $categoria_info->title;

    }else{
        $categoria_title = $cat_info->title;
    }

    $categoria_descr = $cat_info->description;

    $catparams = json_decode($cat_info->params);
    $catimagen = $catparams->image;
}


$max_width = 150;
$max_height= 150;

?>

    <div class="row">

        <div class="columna_izq col-sm-3">

            <div class="box">
                <div id="submenu">

                    <div class="box-content">
                    <?php
                        //Load Menu-Right Module
                        $modules = JModuleHelper::getModules("aside-left-producto");
                        if($modules){
                            $document  = JFactory::getDocument();
                            $renderer  = $document->loadRenderer('module');
                            $attribs   = array();
                            $attribs['style'] = 'none';
                            foreach($modules as $mod){
                                echo JModuleHelper::renderModule($mod, $attribs);
                            }
                        }
                    ?>
                    </div>
                </div>
            </div>

            <?php 
            $tags = array();
            foreach ($this->items as $i => $item):
                if ($item->tags!=""){
                    $tags[] .= $item->tags;
                }
            endforeach; 
            $etiquetas = array_unique($tags);

            if (count($etiquetas)>0 ): 
            ?>

            <div class="box hidden-xs hidden-sm">
                <div class="box-heading">
                    <div>
                        <span><?php echo JText::_('COM_MARCAS'); ?></span>
                    </div>

                </div>
            
                <div class="box-content marcas">

                <?php foreach ($etiquetas as $tag) : 
                $h = rand(2,6);
                ?>
                    <h<?php echo $h; ?> class="display-inline"><?php echo $tag; ?></h<?php echo $h; ?>>
                <?php endforeach;?>

                </div>
            </div>
            <?php endif; ?>


            <?php
            $modules = JModuleHelper::getModules("banner-aside");
            if($modules){
                $document  = JFactory::getDocument();
                $renderer  = $document->loadRenderer('module');
                $attribs   = array();
                $attribs['cat']   = $categoria_title;
                $attribs['style'] = 'none';
                foreach($modules as $mod){
                    echo JModuleHelper::renderModule($mod, $attribs);
                }
            }
            ?>

        </div>


        <div class="columna_productos col-sm-9">


            <?php
            $modules = JModuleHelper::getModules("banner-interno");
            if($modules):
            ?>
            <div class="banner-interno">
            <?php
                $document  = JFactory::getDocument();
                $renderer  = $document->loadRenderer('module');
                $attribs   = array();
                $attribs['style'] = 'none';
                foreach($modules as $mod){
                    echo JModuleHelper::renderModule($mod, $attribs);
                }
            ?>
            </div>
            <?php endif; ?>

            <?php if($catimagen!=""):?>
                <div class="category-image">
                    <img src="<?php echo $catimagen; ?>" class="img-responsive">
                </div>
            <?php endif; ?>

            <?php if ($categoria_title!="" && $catimagen=="") : ?>
            <div class="category-title">
            <h2><?php echo $categoria_title; ?></h2>
            <?php echo $catid; ?>
            </div>
            <?php endif; ?>

            <?php if ($categoria_descr!="") : ?>
            <div class="category-description">
                <?php echo $categoria_descr; ?>
            </div>
            <?php endif; ?>

            <div class="clearfix"></div>

            <?php // echo $this->pagination->total; ?>

            <div class="listado-productos relative margin-top-20 margin-bottom-20">

            <?php if (count($this->items)):?>
            <?php foreach ($this->items as $i => $item) : 

            $urlItem = JRoute::_('index.php?option=com_catalogo&view=producto&id='.(int) $item->id."&alias=".$item->alias);
            ?>
                
                <div class="producto col-xs-6 col-sm-4 col-md-4 text-center">

                    <div class="imagen_producto">

                        <?php


                        if ($item->imagen_descripcion!="" && JFile::exists($item->imagen_descripcion)): 
                            $imagen_contenido = JURI::BASE(true) . "/" .  $item->imagen_descripcion;
                            $imagen_limpia = $item->imagen_descripcion;

                        elseif ($item->imagen!="" && JFile::exists($item->imagen)):   
                            $imagen_contenido = JURI::BASE(true) . "/" . $item->imagen;
                            $imagen_limpia = $item->imagen;

                        elseif ($item->images!=""):

                            $array_images = array();
                            $imagenes = json_decode($item->images);

                            if (count($imagenes)):
                                foreach ($imagenes as $key => $value) {
                                    if ( $value!="" && JFile::exists($value) ):
                                        array_push($array_images, $value);
                                    endif;
                                }
                            endif;

                            if ($array_images[0]):
                                $imagen_contenido = $array_images[0];
                            endif;

                        endif;
            
                        ?>

                        <?php if ($imagen_contenido): 
                        $medidas = getimagesize(JURI::BASE() . $imagen_limpia);
                        $width = $medidas[0];
                        $height= $medidas[1];
                        //if ($width >= 150 ){ $ancho=150; } else { $ancho=$width; }
                        if ($width >= $max_width  ) { $med = " style='width:150px;height:auto;max-height:150px;max-width:150px;' "; } 
                        if ($height>= $max_height ) { $med = " style='height:150px;width:auto;max-width:150px;max-height:150px;' "; }

                        if ($width >= $max_width  ) {
                            $ancho = $max_width;
                        }

                        ?>

                        <a href="<?php echo $urlItem; ?>">
                        <img src="<?php echo $imagen_contenido; ?>" title="<?php echo $item->title; ?>" alt="<?php echo $item->title; ?>" class="img-responsive">
                        </a>

                        <?php 
                        $imagen_contenido = "";
                        $imagen_limpia = "";
                        endif; ?>

                        <?php if($item->colores!=""){
                        $lista_colores = str_replace(" ", "", $item->colores);
                        $colores = split(",", $lista_colores);

                        if ( count($colores)): ?>

                        <div class="colores">
                        <?php
                        foreach($colores as $color):
                            if ($color!=""):
                            ?>
                            
                            <div class="color" style="background-color:<?php echo $color; ?>;">
                            </div>

                            <?php
                            endif;

                        endforeach;?>

                        </div>

                        <?php endif;
                        }; ?>
                        
                    </div>

                    <div class="datos_producto">

                        <h3><?php echo $this->escape($item->title); ?></h3>
                        <?php if ($item->caracteristica!=""): ?>
                            <div class="producto_caracteristica"><?php echo $item->caracteristica; ?></div>
                        <?php endif; ?>
                        <?php if ($item->introtext!=""): ?>
                            <div class="producto_introtext"><?php echo $item->introtext; ?></div>
                        <?php endif; ?>

                        <?php if ($item->precio!="" && $item->precio!="0"): ?>
                            <div class="producto_precio"><?php echo JText::_('COM_PRICE'); ?>: <?php echo $item->precio; ?></div>
                        <?php endif; ?>

                        <?php if ($item->sku!=""): ?>
                            <div class="producto_sku"><?php echo JText::_('COM_SKU'); ?>: <?php echo $item->sku; ?></div>
                        <?php endif; ?>

                    </div>

                    <a href="<?php echo $urlItem; ?>" class="btn"><?php echo JText::_('TPL_VER_DETALLE'); ?></a>
                
                </div>

            <?php endforeach; ?>

            </div>

        </div>

    </div>

    <div class="clearfix"></div>

    <div class="row">
        <div class="container">

            <div class="col-xs-12 col-sm-9 col-sm-offset-3">
            <?php if ($showPagination): ?>
                <nav class="paginador">
                    <?php echo $this->pagination->getPagesLinks(); ?>
                </nav>
            <?php endif; ?>

            <?php else:?>

                No hay productos en esta categoria

            <?php endif;?>
            </div>
            
        </div>
    </div>


<script type="text/javascript">

(function($)
{

    $(document).ready(function () {

        $('.listado-productos .producto').matchHeight({property: 'height'});

        $('.listado-productos .producto .imagen_producto').hover(function(){
            $(this).animate({ opacity:0.8 });
        }, function(){
            $(this).animate({ opacity:1 });
        });

    });

})(jQuery);

</script>