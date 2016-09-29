<?php
$this->startSetup();

$this->getConnection()
    ->addColumn(
        $this->getTable('counter_product'),
        'diagram_id',
        array(
            'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'   => 32,
            'nullable' => true,
            'default'  => NULL,
            'comment'  => 'Diagram Id',
        )
    );
$this->endSetup();