<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class UserExt extends User implements IdentityInterface {

    /**
     * status
     */
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    const STATUS_LABEL = [
        self::STATUS_DELETED => 'Deleted',
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active',
    ];

    /**
     * role
     */
    const ROLE_SUPERADMIN = 'superadmin';
    const ROLE_ADMIN = 'admin';

    /**
     * role level
     */
    const ROLE_SUPERADMIN_LEVEL = 1;
    const ROLE_ADMIN_LEVEL = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id) {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username) {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token) {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
                    'password_reset_token' => $token,
                    'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
                    'verification_token' => $token,
                    'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token) {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function generateEmailVerificationToken() {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }

    public function beforeDelete() {
        if (!parent::beforeDelete()) {
            return false;
        }
        $this->status = self::STATUS_DELETED;
        $this->save();
        return false;
    }

    public function assign($roleName) {
        $result = [
            'success' => 1,
            'message' => 'Assign role is success'
        ];

        try {
            $auth = Yii::$app->authManager;
            $role = $auth->getRole($roleName);
            $auth->assign($role, $this->id);
        } catch (\Exception $ex) {
            $result['success'] = 0;
            $result['message'] = $ex->getMessage();
        }
        return $result;
    }

    public function revoke($roleName) {
        $result = [
            'success' => 1,
            'message' => 'Revoke role is success'
        ];

        try {
            $auth = Yii::$app->authManager;
            $role = $auth->getRole($roleName);
            $auth->revoke($role, $this->id);
        } catch (\Exception $ex) {
            $result['success'] = 0;
            $result['message'] = $ex->getMessage();
        }
        return $result;
    }

    /**
     * RBAC for specific user
     * 
     * @param string $roleName
     * @return boolean
     */
    public function can($roleName) {
        $auth = Yii::$app->getAuthManager();
        $permissions = $auth->getPermissionsByUser($this->id);
        if (isset($permissions[$roleName])) {
            return true;
        }

        $roles = $auth->getRolesByUser($this->id);
        return isset($roles[$roleName]);
    }

    /**
     * 
     * @return integer
     */
    public function getMaxLevel() {
        return (int) $this->getDb()->createCommand(<<<SQL
            SELECT 
              MIN(
                SUBSTRING(
                  tt1.data,
                  LOCATE('s:5:"level";i:', `data`) + 14,
                  1
                )
              ) max_level 
            FROM
              auth_assignment tt0 
              LEFT JOIN auth_item tt1 
                ON tt0.`item_name` = tt1.`name` 
              WHERE tt0.`user_id` = :uid
SQL
                        , [':uid' => $this->id])->queryScalar();
    }

    /**
     * 
     * @return string
     */
    public function getAllRoles() {
        return $this->getDb()->createCommand(<<<SQL
            SELECT 
              GROUP_CONCAT(
                tt0.item_name 
                ORDER BY tt0.item_name SEPARATOR ', '
              )
            FROM
              auth_assignment tt0 
              LEFT JOIN auth_item tt1 
                ON tt0.`item_name` = tt1.`name` 
            WHERE tt0.user_id = :uid
              GROUP BY tt0.`user_id`
SQL
                        , [':uid' => $this->id])->queryScalar();
    }

}
