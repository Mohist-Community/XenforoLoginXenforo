<?php

namespace XenforoLoginXenforo;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Setup extends AbstractSetup
{
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;
    public function install(array $stepParams = [])
    {
        $this->schemaManager()->createTable('xf_xenforo_login_token', function(Create $table)
        {
            $table->addColumn('user_id', 'int');
            $table->addColumn('clientToken', 'char',128);
            $table->addColumn('accessToken', 'char',128);
            $table->addColumn('time','int');
        });
        $this->schemaManager()->createTable('xf_xenforo_session', function(Create $table)
        {
            $table->addColumn('serverId', 'char',128);
            $table->addColumn('accessToken', 'char',128);
            $table->addColumn('time','int');
        });
        $this->schemaManager()->createTable('xf_xenforo_login_skin', function(Create $table)
        {
            $table->addColumn('id', 'int')->primaryKey();
            $table->addColumn('md5', 'char',128);
        });
        $this->schemaManager()->alterTable('xf_user', function(Alter $table)
        {
            $table->addColumn('uuid', 'char', 32);
            $table->addColumn('skin', 'int')->setDefault(1);
        });
    }
    public function uninstall(array $stepParams = [])
    {
        $this->schemaManager()->dropTable('xf_xenforo_login_token');
        $this->schemaManager()->dropTable('xf_xenforo_login_skin');
        $this->schemaManager()->dropTable('xf_xenforo_session');
        $this->schemaManager()->alterTable('xf_user', function(Alter $table)
        {
            $table->dropColumns('uuid');
            $table->dropColumns('skin');
        });
    }
}