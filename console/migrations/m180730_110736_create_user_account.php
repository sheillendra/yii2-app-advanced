<?php

use yii\db\Migration;
use yii\base\InvalidConfigException;
use yii\rbac\DbManager;
use common\models\UserExt;

/**
 * Class m180730_110736_create_user_account
 */
class m180730_110736_create_user_account extends Migration
{
    
    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager() {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }

        return $authManager;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up() {
        $authManager = $this->getAuthManager();

        $superadmin = $authManager->createRole(UserExt::ROLE_SUPERADMIN);
        $superadmin->description = 'Superadmin';
        $superadmin->data = ['level' => UserExt::ROLE_SUPERADMIN_LEVEL];
        $authManager->add($superadmin);

        $admin = $authManager->createRole(UserExt::ROLE_ADMIN);
        $admin->description = 'Administrator';
        $admin->data = ['level' => UserExt::ROLE_ADMIN_LEVEL];
        $authManager->add($admin);

        $authManager->addChild($superadmin, $admin);

        $user = new UserExt();
        $user->username = 'superadmin';
        $user->email = 'superadmin@example.com';
        $user->setPassword('123456');
        $user->status = UserExt::STATUS_ACTIVE;
        $user->created_at = time();
        $user->updated_at = time();
        $user->generateAuthKey();
        $user->save();

        $authManager->assign($superadmin, $user->id);
        
        $system = new UserExt();
        $system->username = 'system';
        $system->email = 'system@example.com';
        $system->setPassword('123456');
        $system->status = UserExt::STATUS_ACTIVE;
        $system->created_at = time();
        $system->updated_at = time();
        $system->generateAuthKey();
        $system->save();
        $authManager->assign($superadmin, $system->id);
    }

    public function down() {
        $authManager = $this->getAuthManager();
        $authManager->removeAllRoles();
        $authManager->removeAllPermissions();

        $this->truncateTable('{{%user}}');
    }

}
