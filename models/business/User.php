<?php

namespace app\models\business;

use app\common\constants\UserState;
use app\common\utils\Result;
use app\models\base\BaseUser;
use DateInterval;
use DateTime;
use Exception;
use Yii;
use yii\base\Action;
use yii\db\ActiveQuery;
use yii\filters\RateLimitInterface;
use yii\redis\Connection;
use yii\web\IdentityInterface;
use yii\web\Request;

/**
 * 用户
 * @property Authorization[] $authorizations 用户授权列表
 * @property Role[]          $roles          角色用户列表
 * @property string          $stateName      状态名称
 * @property Permission[]    $permissions    权限列表
 */
class User extends BaseUser implements IdentityInterface, RateLimitInterface
{
    /**
     * 规则
     * @return array
     */
    public function rules(): array
    {
        return [
            //用户名
            [
                'username',
                'required'
            ],
            [
                'username',
                'string',
                'strict' => false,
                'max' => 20
            ],
            [
                'username',
                'unique'
            ],
            //登录密码
            [
                'password',
                'default',
                'value' => ''
            ],
            [
                'password',
                'string',
                'strict' => false,
                'max' => 64
            ],
            //姓名
            [
                'name',
                'required'
            ],
            [
                'name',
                'string',
                'strict' => false,
                'max' => 32
            ],
            //头像
            [
                'avatar',
                'string',
                'strict' => false,
                'max' => 100
            ],
            //状态
            [
                'state',
                'default',
                'value' => 1
            ],
            [
                'state',
                'integer',
                'min' => 0,
                'max' => 255
            ],
            [
                'state',
                'in',
                'range' => UserState::values()
            ],
            //上次登录时间
            [
                'login_time',
                'date',
                'format' => 'php:Y-m-d H:i:s'
            ],
        ];
    }

    /**
     * 字段
     * @return array
     */
    public function fields(): array
    {
        return [
            //主键ID
            'id',
            //用户名
            'username',
            //登录密码
            'password',
            //姓名
            'name',
            //头像
            'avatar',
            //状态
            'state',
            //状态名称
            'stateName' => 'stateName',
            //上次登录时间
            'loginTime' => 'login_time',
            //角色
            'roles' => function () {
                return array_map(function (Role $role) {
                    return [
                        'id' => $role->id,
                        'description' => $role->description
                    ];
                }, $this->roles);
            }
        ];
    }

    /**
     * 属性标签
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'username' => '用户名',
            'password' => '登录密码',
            'name' => '姓名',
            'avatar' => '头像',
            'state' => '状态',
            'login_time' => '上次登录时间',
        ];
    }

    /**
     * 用户授权列表
     * @return ActiveQuery
     */
    public function getAuthorizations(): ActiveQuery
    {
        return $this->hasMany(Authorization::class, ['user_id' => 'id']);
    }

    /**
     * 角色用户列表
     * @return ActiveQuery
     */
    public function getRoles(): ActiveQuery
    {
        return $this->hasMany(Role::class, ['id' => 'role_id'])
            ->viaTable('user_role', ['user_id' => 'id']);;
    }

    /**
     * 获取状态名称
     * @return string|null
     */
    public function getStateName(): ?string
    {
        return UserState::name($this->state);
    }

    /**
     * 根据ID获取用户实例
     * @param int|string $id
     * @return IdentityInterface|null
     */
    public static function findIdentity($id)
    {
        return static::find()->where(['id' => $id])->one();
    }

    /**
     * 根据令牌获取用户实例
     * @param mixed      $token
     * @param mixed|null $type
     * @return IdentityInterface|null
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $authorization = Authorization::find()->where(['value' => $token])->one();
        if (!$authorization) {
            return null;
        }
        if (time() > strtotime($authorization->expiresTime)) {
            return null;
        }
        return $authorization->user;
    }

    /**
     * 获得用户ID
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 获得用户认证Key（暂不使用）
     * @return string|null
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * 验证用户认证Key（暂不使用）
     * @param string $authKey
     * @return bool|null
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }

    /**
     * 获得限制速率
     * @param Request $request
     * @param Action  $action
     * @return int[]
     */
    public function getRateLimit($request, $action)
    {
        return [3, 1];
    }

    /**
     * @param Request $request
     * @param Action  $action
     */
    public function loadAllowance($request, $action): array
    {
        /** @var \Redis $redis */
        $redis = Yii::$app->memory->redis;
        $key = 'rate-limit:' . $this->id;
        $data = $redis->hGetAll($key);
        if (!$data) {
            return [3, 0];
        }
        return [$data['allowance'], $data['timestamp']];
    }

    /**
     * @param Request $request
     * @param Action  $action
     * @param int     $allowance
     * @param int     $timestamp
     * @return void
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        /** @var \Redis $redis */
        $redis = Yii::$app->memory->redis;
        $key = 'rate-limit:' . $this->id;
        $redis->hMSet($key, [
            'allowance' => $allowance,
            'timestamp' => $timestamp
        ]);
    }


    /**
     * 获得拥有的权限列表（已去重）
     * @return Permission[]
     */
    public function getPermissions(): array
    {
        $permissions = [];
        foreach ($this->roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[$permission->id] = $permission;
            }
        }
        return array_values($permissions);
    }

    /**
     * 使用账户信息登录
     * @param array $data 登录数据
     * @return array
     * @throws Exception
     */
    public static function loginByAccount(array $data): array
    {
        list($username, $password) = $data;
        if ($username === null || $password === null) {
            throw new Exception('用户名/密码缺失');
        }
        $user = User::find()->where(['username' => $username])->one();
        if (!$user) {
            throw new Exception('用户不存在');
        }
        if ($user->state === UserState::DISABLED) {
            throw new Exception('用户已禁用');
        }
        $success = Yii::$app->security->validatePassword($password, $user->password);
        if (!$success) {
            throw new Exception('用户名/密码不正确');
        }
        //if ($user->authorizationTotal() >= 10) {
        //    throw new Exception('已经达到最大登录数，请退出其他设备');
        //}
        $login = Yii::$app->user->login($user);
        if (!$login) {
            throw new Exception('登录异常');
        }
        $expires = DateInterval::createFromDateString('7 day');
        $authorization = new Authorization();
        $authorization->userId = $user->id;
        $authorization->value = Yii::$app->security->generateRandomString(64);
        $authorization->expiresTime = (new DateTime())->add($expires)->format('Y-m-d H:i:s');
        if (!$authorization->save(false)) {
            throw new Exception('处理授权出现异常');
        }
        $permissions = array_map(function (Permission $permission) {
            return $permission->name;
        }, $user->getPermissions());
        return [
            'name' => $user->name,
            'token' => 'Bearer ' . $authorization->value,
            'expires' => $authorization->expiresTime,
            'permissions' => $permissions,
            'avatar' => $user->avatar ? $_ENV['ASSET_BASE_URL'] . $user->avatar : null
        ];
    }

    /**
     * 当前用户授权的总数
     * @return bool|int|string|null
     */
    public function authorizationTotal()
    {
        return Authorization::find()
            ->where(['user_id' => $this->id])
            ->count();
    }
}
