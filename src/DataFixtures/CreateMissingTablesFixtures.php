<?php

declare(strict_types=1);

namespace AwsLightsailBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * 创建缺失的数据库表
 *
 * 用于解决测试环境中缺少关联表的问题
 */
class CreateMissingTablesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        if (!$manager instanceof EntityManagerInterface) {
            return;
        }

        $connection    = $manager->getConnection();
        $schemaManager = $connection->createSchemaManager();

        // 检查 biz_user 表是否存在
        if (!$schemaManager->tablesExist(['biz_user'])) {
            $table = new Table('biz_user');
            $table->addColumn('id', 'integer', ['autoincrement' => true]);
            $table->addColumn('email', 'string', ['length' => 255, 'notnull' => false]);
            $table->addColumn('password', 'string', ['length' => 255, 'notnull' => false]);
            $table->addColumn('username', 'string', ['length' => 255, 'notnull' => false]);
            $table->addColumn('roles', 'json', ['notnull' => false]);
            $table->addPrimaryKeyConstraint(
                PrimaryKeyConstraint::editor()
                    ->setUnquotedColumnNames('id')
                    ->setUnquotedName('pk_biz_user')
                    ->create()
            );

            $schemaManager->createTable($table);
        }

        // 检查 biz_role 表是否存在
        if (!$schemaManager->tablesExist(['biz_role'])) {
            $table = new Table('biz_role');
            $table->addColumn('id', 'integer', ['autoincrement' => true]);
            $table->addColumn('name', 'string', ['length' => 255, 'notnull' => false]);
            $table->addColumn('code', 'string', ['length' => 255, 'notnull' => false]);
            $table->addPrimaryKeyConstraint(
                PrimaryKeyConstraint::editor()
                    ->setUnquotedColumnNames('id')
                    ->setUnquotedName('pk_biz_role')
                    ->create()
            );

            $schemaManager->createTable($table);
        }

        // 检查 biz_user_biz_role 表是否存在
        if (!$schemaManager->tablesExist(['biz_user_biz_role'])) {
            // 创建一个空的 biz_user_biz_role 表
            $table = new Table('biz_user_biz_role');
            $table->addColumn('biz_user_id', 'integer');
            $table->addColumn('biz_role_id', 'integer');
            $table->addPrimaryKeyConstraint(
                PrimaryKeyConstraint::editor()
                    ->setUnquotedColumnNames('biz_user_id', 'biz_role_id')
                    ->setUnquotedName('pk_biz_user_biz_role')
                    ->create()
            );

            $schemaManager->createTable($table);
        }
    }
}
