<?php

use yii\db\Migration;

/**
 * Class m210323_115144_base_tables
 */
class m210323_115144_base_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        return true;
        $this->execute('CREATE TABLE `orchestration_log` (
                                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                                  `requester_id` bigint(20) NOT NULL,
                                  `provider_id` bigint(20) NOT NULL,
                                  `service_id` bigint(20) NOT NULL,
                                  `interface_id` bigint(20) NOT NULL,
                                  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                  PRIMARY KEY (`id`),
                                  KEY `orchestration_connections_requester_id_IDX` (`requester_id`) USING BTREE,
                                  KEY `orchestration_log_FK_1` (`service_id`) USING BTREE,
                                  KEY `orchestration_log_FK_2` (`interface_id`) USING BTREE,
                                  KEY `orchestration_log_provider_id_IDX` (`provider_id`,`service_id`,`interface_id`) USING BTREE,
                                  CONSTRAINT `orchestration_connections_FK_copy` FOREIGN KEY (`requester_id`) REFERENCES `system_` (`id`) ON UPDATE CASCADE,
                                  CONSTRAINT `orchestration_log_FK_1_copy_copy` FOREIGN KEY (`service_id`) REFERENCES `service_definition` (`id`) ON UPDATE CASCADE,
                                  CONSTRAINT `orchestration_log_FK_2_copy_copy` FOREIGN KEY (`interface_id`) REFERENCES `service_interface` (`id`) ON UPDATE CASCADE,
                                  CONSTRAINT `orchestration_log_FK_copy_copy` FOREIGN KEY (`provider_id`) REFERENCES `system_` (`id`) ON UPDATE CASCADE
                                ) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;');
        $this->execute('CREATE TABLE `orchestration_connection` (
                              `id` bigint(20) NOT NULL AUTO_INCREMENT,
                              `requester_id` bigint(20) NOT NULL,
                              `provider_id` bigint(20) NOT NULL,
                              `service_id` bigint(20) NOT NULL,
                              `interface_id` bigint(20) NOT NULL,
                              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              `terminated_at` datetime DEFAULT NULL,
                              PRIMARY KEY (`id`),
                              UNIQUE KEY `orchestration_connections_UN` (`requester_id`,`provider_id`,`service_id`,`interface_id`),
                              KEY `orchestration_log_FK_1` (`service_id`) USING BTREE,
                              KEY `orchestration_log_FK_2` (`interface_id`) USING BTREE,
                              KEY `orchestration_log_provider_id_IDX` (`provider_id`,`service_id`,`interface_id`) USING BTREE,
                              KEY `orchestration_connections_requester_id_IDX` (`requester_id`) USING BTREE,
                              CONSTRAINT `orchestration_connections_FK` FOREIGN KEY (`requester_id`) REFERENCES `system_` (`id`) ON UPDATE CASCADE,
                              CONSTRAINT `orchestration_log_FK_1_copy` FOREIGN KEY (`service_id`) REFERENCES `service_definition` (`id`) ON UPDATE CASCADE,
                              CONSTRAINT `orchestration_log_FK_2_copy` FOREIGN KEY (`interface_id`) REFERENCES `service_interface` (`id`) ON UPDATE CASCADE,
                              CONSTRAINT `orchestration_log_FK_copy` FOREIGN KEY (`provider_id`) REFERENCES `system_` (`id`) ON UPDATE CASCADE
                            ) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8;');

        $this->execute("CREATE TABLE `communication_log` (
                              `id` bigint(20) NOT NULL AUTO_INCREMENT,
                              `requester_id` bigint(20) NOT NULL,
                              `http_method` enum('GET','HEAD','POST','PUT','PATCH','DELETE','OPTIONS','TRACE') NOT NULL,
                              `provider_address` varchar(100) DEFAULT NULL,
                              `provider_port` int(11) DEFAULT NULL,
                              `service_uri` varchar(255) DEFAULT NULL,
                              `interface_name` varchar(255) DEFAULT NULL,
                              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              `uri_components` varchar(255) DEFAULT NULL,
                              PRIMARY KEY (`id`),
                              KEY `communication_log_FK` (`requester_id`),
                              CONSTRAINT `communication_log_FK` FOREIGN KEY (`requester_id`) REFERENCES `system_` (`id`) ON UPDATE CASCADE
                            ) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('communication_log');
        $this->dropTable('orchestration_log');
        $this->dropTable('orchestration_connection');
    }
}