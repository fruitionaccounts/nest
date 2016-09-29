<?php

class MageLynx_ImportExportPlugin_Model_Source_Exporttype
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'type_row',
                'label' => 'Row-by-Row (initial method, slow, stable)',
            ),
            array(
                'value' => 'type_fast',
                'label' => 'Fast (beta)',
            ),
        );
    }
}