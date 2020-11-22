<?php

namespace ant\shipping\migrations\db;

use ant\db\Migration;

/**
 * Class M200505070324CreateShippingRules
 */
class M200505070324CreateShippingRules extends Migration
{
    protected $tableName = '{{%shipping_rule}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->autoId(),
			'rule_class_id' => $this->morphClass(false),
			'option' => $this->text()->null()->defaultValue(null),
			'status' => $this->smallInteger()->unsigned()->defaultValue(0),
			'priority' => $this->smallInteger()->unsigned()->defaultValue(0),
			'created_at' => $this->timestamp()->null()->defaultValue(null),
			'updated_at' => $this->timestamp()->null()->defaultValue(null),
            'created_by' => $this->integer(11)->unsigned(),
            'updated_by' => $this->integer(11)->unsigned(),
        ], $this->getTableOptions());

		$this->addForeignKeyTo('{{%model_class}}', 'rule_class_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropTable($this->tableName);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M200505070324CreateShippingRules cannot be reverted.\n";

        return false;
    }
    */
}
