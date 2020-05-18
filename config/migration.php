<?php

return [
    'name'                    => 'Test',
    'migrations_namespace'    => 'migrations',
    'table_name'              => 'doctrine_migration_versions',
    'column_name'             => 'version',
    'column_length'           => 14,
    'executed_at_column_name' => 'executed_at',
    'migrations_directory'    => dirname(__DIR__) . '/migration',
    'all_or_nothing'          => true,
    'check_database_platform' => true,
];