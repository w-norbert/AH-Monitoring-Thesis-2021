<?php

use yii\db\Migration;

/**
 * Class m210403_111458_validation_rule
 */
class m210403_111458_validation_rule extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `validation_rule` (
                              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                              `name` varchar(100) COLLATE utf8_hungarian_ci NOT NULL,
                              `rule` text COLLATE utf8_hungarian_ci NOT NULL,
                              `positive_evaluation` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'true = query must return result to be valid',
                              `active` tinyint(1) NOT NULL DEFAULT '1',
                              `fulfilled` tinyint(1) DEFAULT NULL,
                              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              `last_validation` datetime DEFAULT NULL,
                              PRIMARY KEY (`id`)
                            ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("validation_rule");
    }
}
