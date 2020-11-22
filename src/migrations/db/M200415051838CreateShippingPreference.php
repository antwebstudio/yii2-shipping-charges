<?php

namespace ant\shipping\migrations\db;

use ant\db\Migration;

/**
 * Class M200415051838CreateShippingPreference
 */
class M200415051838CreateShippingPreference extends Migration
{
    protected $tableName = '{{%shipping_preference}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->autoId(),
			'shippable_id' => $this->morphId(),
			'shippable_class_id' => $this->morphClass(),
            'courier_id' => $this->foreignId(),
			'option' => $this->text()->null()->defaultValue(null),
			'delivery_date' => $this->date()->null()->defaultValue(null),
			'delivery_remark' => $this->string()->null()->defaultValue(null),
			'created_at' => $this->timestamp()->null()->defaultValue(null),
			'updated_at' => $this->timestamp()->null()->defaultValue(null),
            'created_by' => $this->integer(11)->unsigned(),
            'updated_by' => $this->integer(11)->unsigned(),
        ], $this->getTableOptions());
		
		$this->addForeignKeyTo('{{%model_class}}', 'shippable_class_id');
		$this->createIndexFor('shippable_id');
		
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
        echo "M200415051838CreateShippingPreference cannot be reverted.\n";

        return false;
    }
    */
}
