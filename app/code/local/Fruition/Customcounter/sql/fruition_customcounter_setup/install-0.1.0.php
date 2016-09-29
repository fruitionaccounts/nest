<?php
/**
 * Created by PhpStorm.
 * User: scotttaylor
 * Date: 2/2/16
 * Time: 1:47 PM
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getTable('counter_product');

if($installer->tableExists($table)) {
    $installer->getConnection()->dropTable($table);
}

$ddlTable = $installer->getConnection()->newTable($table);

$ddlTable
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'primary' => true,
        'identity' => true,
        'unsigned' => true,
        'nullable' => false
    ), 'id')
    ->addColumn('serial', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => true
    ), 'Serial')
    ->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'nullable' => false,
        'unsigned' => true
    ), 'Quote ID')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'nullable' => false,
        'unsigned' => true
    ), 'Order ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
        'default' => '1970-01-01 00:00:01'
    ), 'created_at')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
        'default' => '1970-01-01 00:00:01'
    ), 'updated_at');

$installer->getConnection()->createTable($ddlTable);

$installer->endSetup();