<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 02.03.2018
 * Time: 15:41
 */

namespace api\models\forms;


use common\models\Badge;
use common\models\Cities;
use common\models\helpers\DeviceVersionHelper;
use common\models\MProfile;
use common\models\MUser;
use common\models\UserBans;
use common\models\VolunteerLinks;
use common\models\Volunteers;
use yii\base\Model;
use yii\web\ConflictHttpException;

class NotificationForm extends Model
{
    public $userId;
    public $page;
    public $limit;

    public function rules()
    {
        return [
            [['userId','page', 'limit'], 'required'],
            [['page', 'limit'], 'string'],
        ];
    }

}