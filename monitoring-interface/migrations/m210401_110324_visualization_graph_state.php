<?php

use yii\db\Migration;

/**
 * Class m210401_110324_visualization_graph_state
 */
class m210401_110324_visualization_graph_state extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `visualization_graph_state` (
                              `view_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                              `name` varchar(100) DEFAULT NULL,
                              `data` text,
                              `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              PRIMARY KEY (`view_id`)
                            ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("visualization_graph_state");
    }
}
