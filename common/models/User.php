<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class User extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['password_reset_token', 'verification_token'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 10],
            [['username', 'auth_key', 'password_hash', 'email', 'personal_information_id'], 'required'],
            [['status', 'created_at', 'updated_at', 'personal_information_id'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'verification_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            [['personal_information_id'], 'exist', 'skipOnError' => true, 'targetClass' => PersonalInformation::class, 'targetAttribute' => ['personal_information_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'verification_token' => 'Verification Token',
            'personal_information_id' => 'Personal Information ID',
        ];
    }

    public function getPersonalInformation()
    {
        return $this->hasOne(PersonalInformation::class, ['id' => 'personal_information_id']);
    }

    public function getStudentViolations()
    {
        return $this->hasMany(StudentViolation::class, ['user_id' => 'id']);
    }

    public function getTeacherAdvisoryAssignments()
    {
        return $this->hasMany(TeacherAdvisoryAssignment::class, ['user_id' => 'id']);
    }

    public function getUserProfiles()
    {
        return $this->hasMany(UserProfile::class, ['user_id' => 'id']);
    }
}
