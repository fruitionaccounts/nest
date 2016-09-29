<?php

class MageLynx_ImportExportPlugin_Model_Source_Column{
    public function getNativeColumns(){
        $standard_columns_fetched_by_regex = "entity_id
        ID
        name
        Name
        custom_name
        Name (store specific)
        type
        Type
        set_name
        Attrib. Set Name
        sku
        SKU
        price
        Price
        qty
        Qty
        visibility
        Visibility
        status
        Status
        websites
        Websites
        action
        Action";
        $columns = array();
        $native_columns = explode("\n", $standard_columns_fetched_by_regex);
        for($i=0; $i<=count($native_columns)-1; $i+=2){
            $columns[]= array( 'value'=>trim($native_columns[$i]), 'name'=>Mage::helper('importexportplugin')->__(trim($native_columns[$i+1])));
        }
        return $columns;
    }

    public function getCustomColumns(){
        return array(
            array(
                'value' =>  'has_children_configurable',
                'name'  =>  'Has children (configurable)',
            ),
            array(
                'value' =>  'is_children_configurable',
                'name'  =>  'Is child (configurable)',
            ),


            array(
                'value' =>  'has_children_grouped',
                'name'  =>  'Has children (grouped)',
            ),
            array(
                'value' =>  'is_children_grouped',
                'name'  =>  'Is child (grouped)',
            ),

/*
            array(
                'value' =>  'has_children_bundle',
                'name'  =>  'Has children (bundle)',
            ),
            array(
                'value' =>  'is_children_bundle',
                'name'  =>  'Is child (bundle)',
            ),
*/

            array(
                'value' =>  'has_custom_options',
                'name'  =>  'Has custom options',
            ),
            array(
                'value' =>  'has_related',
                'name'  =>  'Has related',
            ),
            array(
                'value' =>  'has_crosssell',
                'name'  =>  'Has cross-selling',
            ),
            array(
                'value' =>  'has_upsell',
                'name'  =>  'Has up-selling',
            ),
            array(
                'value' =>  'images',
                'name'  =>  'Images',
            ),
            array(
                'value' =>  'categories',
                'name'  =>  'Categories',
            ),

        );
    }


    public function toOptionArray($addEmpty = true)
    {

        $columns =array_merge($this->getNativeColumns(), $this->getCustomColumns());
        foreach ($columns as $item) {
            $options[] = array(
               'label' => $item['name'],
               'value' => $item['value']
            );
        }

        return $options;
    }
}
