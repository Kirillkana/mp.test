<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%statistics}}`.
 */
class m201113_064928_create_statistics_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%statistics}}', [
            'id' => $this->primaryKey(),
            'cid' => $this->string()->notNull(),
            'campaign_id' => $this->integer()->notNull(),
            'event' => $this->string()->notNull(),
            'time' => $this->integer()->notNull(),
            'sub1' => $this->string()->notNull(),
            'trials' => $this->integer()->null()->defaultValue(null),
            'CRti' => $this->float()->null()->defaultValue(null),
            'installs' => $this->integer()->null()->defaultValue(null),
            'CRi' => $this->float()->null()->defaultValue(null),
            'clicks' => $this->integer()->null()->defaultValue(null),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%statistics}}');
    }
}
