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

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_catalogo', JPATH_ADMINISTRATOR);
JLoader::register('JFile', JPATH_LIBRARIES . '/joomla/filesystem/file.php');
// $browserbar= $this->item->title;
// $document = JFactory::getDocument();
// $document->setTitle($browserbar);

// $doc = JFactory::getDocument();
// $config =& JFactory::getConfig(); 
// $sitename = $config->get( 'sitename' );
// $doc->setTitle($this->item->title . ' - ' . $sitename);
$categoria_title = $this->item->categoria_title;
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

        	<div class="row">

        		<div class="col-sm-6">
        			
                    <?php
                    if ( $this->item->imagen_descripcion!="" ):
						$imagen_contenido = JURI::BASE(true) . "/" .  $this->item->imagen_descripcion;
						$imagen_limpia    = $this->item->imagen_descripcion;
                    elseif ( $this->item->imagen!="" ):
						$imagen_contenido = JURI::BASE(true) . "/" . $this->item->imagen;
						$imagen_limpia    = $this->item->imagen;
                    endif;
                    ?>

					<?php if ( JFile::exists( $imagen_limpia ) ): ?>
                    <img src="<?php echo $imagen_contenido; ?>" title="<?php echo $this->item->title; ?>" alt="<?php echo $this->item->title; ?>" class="img-responsive">
					<?php endif; ?>

                    <?php 
                    $imagen_contenido = "";
                    $imagen_limpia = "";
                    ?>


	        	</div>

        		<div class="col-sm-6">

					<h2 class="titleItem"><?php echo $this->item->title; ?></h2>
					<em class="item-categoria text-muted"><?php echo $categoria_title; ?></em>

					<?php if($this->item->sku): ?>
					<p>Sku: <strong><?php echo $this->item->sku; ?></strong>
					<?php endif;?>

					<div class="contenido-item">
					<?php if($this->item->introtext): ?>
					<p class="item-introtext"><?php echo $this->item->introtext; ?></p>
					<?php endif;?>

					<?php if($this->item->caracteristica): ?>
					<p class="item-caracteristica"><?php echo $this->item->caracteristica; ?></p>
					<?php endif;?>

					<?php if($this->item->descripcion): ?>
					<p class="item-descripcion"><?php echo $this->item->descripcion; ?></p>
					<?php endif;?>

					<?php if($this->item->tags): ?>
		            <p class="item-tags">
		                Marca: <strong><?php echo $this->item->tags; ?></strong>
		            </p>
		            <?php endif; ?>

					<?php if($this->item->precio): ?>
		            <p class="item-precio">
		                Precio: <strong><?php echo $this->item->precio; ?></strong>
		            </p>
		            <?php endif; ?>


                    <?php if($this->item->colores!=""){
                    $lista_colores = str_replace(" ", "", $this->item->colores);
                    $colores = split(",", $lista_colores);

                    if ( count($colores)): ?>

                    <div class="colores">
                    <?php
                    foreach($colores as $color):
                        if ($color!=""):
                        ?>
                        
                        <div class="color" style="background-color:<?php echo $color; ?>;"></div>

                        <?php
                        endif;

                    endforeach;?>

                    </div>

                    <?php endif;
                    }; ?>

					</div>

		            <?php
		            $modules = JModuleHelper::getModules("form-interno");
		            if($modules){
		                $document  = JFactory::getDocument();
		                $renderer  = $document->loadRenderer('module');
		                $attribs   = array();
		                $attribs['cat']   = $categoria_title;
		                $attribs['item']  = $this->item->id;
		                $attribs['style'] = 'none';
		                foreach($modules as $mod){
		                    echo JModuleHelper::renderModule($mod, $attribs);
		                }
		            }
		            ?>
   			
        		</div>
        		
        	</div>


        </div>



    </div>



   <div class="clearfix"></div>

    <div class="row">
        <div class="container">

            
        </div>
    </div>



