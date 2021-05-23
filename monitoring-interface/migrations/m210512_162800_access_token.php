<?php

use yii\db\Migration;

/**
 * Class m210512_162800_access_token
 */
class m210512_162800_access_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `access_token` (
                              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                              `value` varchar(100) COLLATE utf8_hungarian_ci NOT NULL,
                              `active` tinyint(1) DEFAULT 1,
                              PRIMARY KEY (`id`)
                            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("access_token");
    }
}
