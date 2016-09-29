<?php
class MageLynx_ImportExportPlugin_Block_Adminhtml_Product_Column extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function yesno($row){
        return Mage::helper('importexportplugin')->__( $row->getData($this->getColumn()->getIndex()) == 1 ? "Yes" : 'No' );
    }
    public function render(Varien_Object $row)
    {

        $index = $this->getColumn()->getIndex();
        if(in_array($index, array( 'has_children_configurable',
                                   'is_children_configurable',
                                   'has_children_grouped',
                                   'is_children_grouped',
                                   'has_related',
                                   'has_upsell',
                                   'has_crosssell',
                                   'has_custom_options',

                         ))){

            return $this->yesno($row);
        }elseif($index=='categories'){
            return nl2br($this->_getValue($row));
        }
        $product = Mage::getModel('catalog/product')->load($row->getId());
        $html = '';
        $helper = Mage::helper('catalog/image');
        foreach($product->getMediaGallery('images') as $image){
            $image_path = Mage::getBaseDir('media')."/catalog/product".$image['file'];
            if(file_exists($image_path) && !is_dir($image_path)){
                $html .= "<div class='product-image'><img img_data='$image_path' src='".$helper->init($product,'image', $image['file'])->resize(90)."'></div>";    
            }
        }
        return "<div id='sortable_grid_".$row->getSku()."' class='sortable-grid-wrapper'><div class='sortable-grid'>$html</div></div>";
    }
 }
