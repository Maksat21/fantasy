<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 02.03.2018
 * Time: 15:40
 */

namespace api\versions\v1\controllers;


use api\controllers\BaseApiController;
use api\models\forms\CategoriesForm;
use api\models\forms\ChangePasswordForm;
use api\models\forms\CheckPasswordForm;
use api\models\forms\GeoForm;
use api\models\forms\PinCodeChangeForm;
use api\models\forms\PinCodeForm;
use api\models\forms\PinValidateForm;
use api\models\forms\ProfileForm;
use api\models\forms\PromoCodeForm;
use common\models\helpers\ErrorMsgHelper;
use common\models\MProfile;
use common\models\MUser;
use Yii;
use yii\web\ConflictHttpException;

class ProfileController extends BaseApiController
{

    /**
     * Задание пин-кода
     * @return array
     * @throws ConflictHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSetPin(){
        $data  = $this->getDecodedBodyData();
        $model = new PinCodeForm();
        $model->userId = $this->userId;
        if($model->load($data, '') && $model->validate()){
            if($model->setPin()){
                return ['status' => 200];
            }else{
                throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($model));
            }
        }else{
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($model));
        }
    }

    /**
     * Изменение пин-кода
     * @return array
     * @throws ConflictHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionChangePin(){
        $data  = $this->getDecodedBodyData();
        $model = new PinCodeChangeForm();
        $model->userId = $this->userId;
        if($model->load($data, '') && $model->validate()){
            if($model->setPin()){
                return ['status' => 200];
            }else{
                throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($model));
            }
        }else{
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($model));
        }
    }

    /**
     * Проверка пин-кода
     * @return array
     * @throws ConflictHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionValidatePin(){
        $data  = $this->getDecodedBodyData();
        $model = new PinValidateForm();
        $model->userId = $this->userId;
        if($model->load($data, '') && $model->validate()){
            if($model->validatePin()){
                return ['status' => 200];
            }else{
                throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($model));
            }
        }else{
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($model));
        }
    }

//    public function actionChangePassword(){
//        $changeForm = new ChangePasswordForm();
//        $changeForm->userId = $this->userId;
//        if($changeForm->load(Yii::$app->request->post(), '') && $changeForm->validate()){
//            if($changeForm->change()){
//                return ['status' => 200];
//            }
//        }
//        throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($changeForm));
//    }

    /**
     * Изменение пароля
     * @return array
     * @throws ConflictHttpException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionChangePassword(){
//        print_r($this->userId); die;
        $changePasswordForm = new ChangePasswordForm();
        $data = $this->getDecodedBodyData();
        if($changePasswordForm->load($data, '') && $changePasswordForm->validate()){
            if($changePasswordForm->change($this->userId)){
                return ['status' => 200];
            } else {
                throw new ConflictHttpException(Yii::t('api', 'Fatal Error'));
            }
        }else{
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($changePasswordForm));
        }
    }

    /**
     * Проверка пароля
     * @return array
     * @throws ConflictHttpException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCheckPassword(){
        $checkPasswordForm = new CheckPasswordForm();
        $data = $this->getDecodedBodyData();
        if($checkPasswordForm->load($data, '') && $checkPasswordForm->validate()){
//            print_r($this->userId); die;
            $checkPasswordForm->_user = mUser::findOne(['id' => $this->userId,'status' => mUser::STATUS_ACTIVE]);
            if($checkPasswordForm->_user){
                if($checkPasswordForm->_user->validatePassword($checkPasswordForm->password)){
                    return ['status' => 200];
                }else{
                    throw new ConflictHttpException(Yii::t('api', 'Password invalid'));
                }
            }else{
                throw new ConflictHttpException(Yii::t('api', 'User not found'));
            }
        }else{
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($checkPasswordForm));
        }
    }

//////////////////////////////////////////////////////////////////////////////////

    public function actionEdit(){
        $model = new ProfileForm();
        $data = $this->decodePostData(Yii::$app->request->post());
        $data['userId'] = $this->userId;
        if($model->load($data,'')){
            $user = $model->save();
            if(!$user){
                throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($user));
            }

            if($user->errors){
                throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($user));
            }
            return ['status' => 200];
        }else{
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($model));
        }
    }

    public function actionInfo(){
        $profile = mUser::find()
            ->select(['iin', 'phone', 'fio', 'created_at', 'f_token', 'app_ver'])
            ->where(['id' => $this->userId])
            ->andWhere(['status' => mUser::STATUS_ACTIVE])
            ->one();
        if($profile){
            return ['status' => 200, 'profile' => $profile];
        }else{
            throw new ConflictHttpException(Yii::t('api', 'User not found'));
        }
    }

    /**
     * Вывод всех юзеров(Для теста)
     * @return array
     * @throws ConflictHttpException
     */
    public function actionAll(){
        $profile = mUser::find()
            ->all();
        if($profile){
            return ['status' => 200, 'profile' => $profile];
        }else{
            throw new ConflictHttpException(Yii::t('api', 'User not found'));
        }
    }

    /**
     * Загрузка аватарки
     * @return array
     * @throws ConflictHttpException
     */
    public function actionUpload(){
        if(Yii::$app->request->post('ava')){
            $user   = mUser::findOne(['id' => $this->userId]);
            $base64Img = Yii::$app->request->post('ava');
            $today    = date('Ymd');
            $filenamePath = md5(time().uniqid()).".jpg";
            $dir = $today.DIRECTORY_SEPARATOR;
            for($i = 0; $i < 6; $i++){
                $dir .= $filenamePath[$i].DIRECTORY_SEPARATOR;
            }
            $dirForUser = $dir;
            $dir = $user->getDirectory().$dir;
            if(!is_dir($dir)){
                mkdir($dir, 0777, true);
            }
            $decoded = base64_decode($base64Img);
            $fileName = file_put_contents($dir.$filenamePath, $decoded);
            if(!$fileName){
                throw new ConflictHttpException(Yii::t('api', "Error img"));
            }

            $user->ava = $dirForUser.$filenamePath;
            if($user->save()){
                return ['status' => 200, 'user' => $user];
            }else{
                throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($user));
            }
        }else{
            throw new ConflictHttpException(Yii::t('api', 'Cant find img in request'));
        }
    }






//    public function actionToken(){
//        $fToken = Yii::$app->request->post('f_token');
//        if(!$fToken){
//            throw new ConflictHttpException('Пустой токен для firebase push');
//        }
//        Yii::$app->db->createCommand("UPDATE m_user SET f_token = null WHERE f_token = '" . $fToken . "'")->execute();
//        Yii::$app->db->createCommand()->update('m_user',
//            ['f_token' => $fToken],
//            ['id' => $this->userId])
//            ->execute();
////        if($result){
//            return ['status' => 200];
////        }else{
////            throw new ConflictHttpException('Ошибка при обновлении firebase токена');
////        }
//    }

}