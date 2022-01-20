<?php

namespace api\versions\v1\controllers;

use api\controllers\FrontApiController;
use api\models\forms\ActivateCardForm;
use api\models\forms\AttractionForm;
use api\models\forms\AttractionPaginationForm;
use api\models\forms\AttractionTotalForm;
use api\models\forms\BlockCardForm;
use api\models\forms\CardHistoryForm;
use api\models\forms\CardListForm;
use api\models\forms\CheckCardForm;
use api\models\forms\DeleteAttractionForm;
use api\models\forms\DeleteDiscountForm;
use api\models\forms\DeleteTerminalForm;
use api\models\forms\DeleteUserForm;
use api\models\forms\DiscountForm;
use api\models\forms\ImportAttractionForm;
use api\models\forms\LogoutForm;
use api\models\forms\MessageForm;
use api\models\forms\OperationListForm;
use api\models\forms\ReplenishmentExcelForm;
use api\models\forms\ReplenishmentForm;
use api\models\forms\ReplenishmentPaginationForm;
use api\models\forms\ReportCardCashierForm;
use api\models\forms\ReportCardForm;
use api\models\forms\ReportPaginationCashierForm;
use api\models\forms\ReportPaginationForm;
use api\models\forms\ReportSessionForm;
use api\models\forms\ReportTotalForm;
use api\models\forms\SessionListForm;
use api\models\forms\TerminalForm;
use api\models\forms\TokenForm;
use api\models\forms\TransactionPaginationForm;
use api\models\forms\TransferBalanceForm;
use api\models\forms\UpdateAttractionForm;
use api\models\forms\UpdateDiscountForm;
use api\models\forms\UpdateTerminalForm;
use api\models\forms\UpdateUserForm;
use api\models\forms\UserForm;
use api\models\forms\WriteOffTotalForm;
use api\models\helper\FrontHelper;
use common\models\Attraction;
use common\models\Card;
use common\models\Discount;
use common\models\helpers\ErrorMsgHelper;
use common\models\QrCode;
use common\models\Replenishment;
use common\models\Session;
use common\models\Terminal;
use common\models\Transaction;
use common\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\Pagination;
use yii\web\ConflictHttpException;

/**
 * Class FrontController
 * @package api\versions\v1\controllers
 */
class FrontController extends FrontApiController
{
//    public function behaviors()
//    {
//        return array_merge(
//            parent::behaviors(),
//            [
//                'access' => [
//                    'class' => AccessControl::className(),
//                    'rules' => [
//                        [
//                            'allow' => true,
//                            'roles' => [Role::ROLE_ADMIN]
//                        ],
//                        [
//                            'actions' => ['replenishment-list', 'add-token', 'add-replenishment', 'discount-double-list', 'report-cashier', 'report-cashier-list'],
//                            'allow' => true,
//                            'roles' => [Role::ROLE_CASHIER]
//                        ],
//                        [
//                            'actions' => ['replenishment-pagination-list', 'report-pagination-list', 'url-excel-replenishment', 'url-excel-report', 'url-excel-transaction', 'url-excel-attraction', 'add-token'],
//                            'allow' => true,
//                            'roles' => [Role::ROLE_ACCOUNTANT]
//                        ]
//                    ],
//                ]
//            ]
//        );
//    }

    /**
     * Вывод списка терминалов
     * @return array
     */
    public function actionTerminalList()
    {
        $list = null;

        $terminals = Terminal::find()
            ->leftJoin('attraction', 'attraction.id=terminal.attraction_id')
            ->select(['terminal.id', 'terminal.title', 'attraction.name', 'terminal.role', 'terminal.login'])
            ->where(['terminal.status' => Terminal::STATUS_ACTIVE])
            ->asArray()
            ->all();

        $list = [];

        foreach ($terminals AS $item) {
            $list[] = (object)[
                'id' => $item['id'],
                'device' => $item['title'],
                'login' => $item['login'],
                'attraction' => $item['name'],
                'role' => $item['role'],
                'park' => \Yii::$app->params['parkName'],
                'action' => "Изменить",
            ];
        }

        return $list;
    }

    /**
     * Вывод Z отчета
     * @return array
     * @throws ConflictHttpException
     * @var $incompleteSession Session
     */
    public function actionZReport()
    {
        $sessionId = FrontHelper::getSession($this->userId);
        $replenishments = Replenishment::findAll(['session_id' => $sessionId]);
        $sessionCount = count(Session::findAll(['user_id' => $this->userId]));
        $user = User::findOne(['id' => $this->userId]);

        $sumByCash = 0;
        $sumByCard = 0;
        $sumByKaspi = 0;
        $count = 0;
        $sum = 0;

        foreach ($replenishments as $replenishment) {
            $count++;
            $sum += $replenishment->amount;
            if ($replenishment->payment_method == Replenishment::PAYMENT_BY_CASH) {
                $sumByCash += $replenishment->amount;
            } elseif ($replenishment->payment_method == Replenishment::PAYMENT_BY_CARD) {
                $sumByCard += $replenishment->amount;
            } else {
                $sumByKaspi += $replenishment->amount;
            }
        }

        $list = [
            'cashier' => $user['fullname'],
            'sessionId' => $sessionId,
            'time' => date('d.m.Y H:i'),
            'sessionCount' => $sessionCount,
            'replenishmentCount' => count($replenishments),
            'countResult' => $count,
            'sumByCash' => $sumByCash,
            'sumByCard' => $sumByCard,
            'sumByKaspi' => $sumByKaspi,
            'sumResult' => $sum,
        ];

        // Закрытие последней сессии
        $incompleteSession = Session::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['session_end' => null])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        /** @var $incompleteSession Session */
        if ($incompleteSession) {
            $transactions = Replenishment::find()
                ->select(['sum(amount) as sum', 'count(amount) as count'])
//                ->where(['terminal_id' => $user->id])
                ->where(['session_id' => $incompleteSession->id])
                ->asArray()
                ->one();

            $incompleteSession->transaction_count = $transactions['count'] ? $transactions['count'] : 0;
            $incompleteSession->transaction_amount = $transactions['sum'] ? $transactions['sum'] : 0;
            $incompleteSession->session_end = date('Y-m-d H:i:s');
            $incompleteSession->save();
        }

        $loginForm = new LogoutForm();
        $loginForm->userId = $this->userId;
        $user = $loginForm->getUser();
        $user->access_token = '';
        $user->save();

        return $list;
    }

    /**
     * Вывод X отчета
     * @return array
     * @throws ConflictHttpException
     */
    public function actionXReport()
    {
        $sessionId = FrontHelper::getSession($this->userId);
        $replenishments = Replenishment::findAll(['session_id' => $sessionId]);
        $sessionCount = count(Session::findAll(['user_id' => $this->userId]));
        $user = User::findOne(['id' => $this->userId]);

        $sumByCash = 0;
        $sumByCard = 0;
        $sumByKaspi = 0;
        $count = 0;
        $sum = 0;

        foreach ($replenishments as $replenishment) {
            $count++;
            $sum += $replenishment->amount;
            if ($replenishment->payment_method == Replenishment::PAYMENT_BY_CASH) {
                $sumByCash += $replenishment->amount;
            } elseif ($replenishment->payment_method == Replenishment::PAYMENT_BY_CARD) {
                $sumByCard += $replenishment->amount;
            } else {
                $sumByKaspi += $replenishment->amount;
            }
        }

        $list = [
            'cashier' => $user['fullname'],
            'sessionId' => $sessionId,
            'time' => date('d.m.Y H:i'),
            'sessionCount' => $sessionCount,
            'replenishmentCount' => count($replenishments),
            'countResult' => $count,
            'sumByCash' => $sumByCash,
            'sumByCard' => $sumByCard,
            'sumByKaspi' => $sumByKaspi,
            'sumResult' => $sum,
        ];

        return $list;
    }

    /**
     * Вывод списка операций
     * @return array
     * @throws ConflictHttpException
     */
    public function actionOperationList()
    {
        $form = new OperationListForm();

        if ($form->load(\Yii::$app->request->post(), '') && $form->validate()) {
            $pagination = $form->pagination();
            $query = $pagination['list'];
            $count = $pagination['count'];
            if ($form->page >= $count - 1) {
                $hasNext = false;
            } else {
                $hasNext = true;
            }
            return ['status' => 200, 'data' => $query, 'pages' => $form->page, 'hasNext' => $hasNext, 'pagesCount' => $count];
        } else {
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($form));
        }
    }

    /**
     * Вывод истории карты
     * @return array
     * @throws InvalidConfigException
     * @throws ConflictHttpException
     */
    public function actionCardHistory()
    {
        $form = new CardHistoryForm();
        if ($form->load(\Yii::$app->request->post(), '') && $form->validate()) {
            $card = Card::findOne(['code' => $form->code]);
            if (!$card) {
                throw new ConflictHttpException('Нет карточки');
            }
            $replenishments = Replenishment::find()
                ->leftJoin('user', 'user.id=replenishment.user_id')
                ->select(['replenishment.amount', 'replenishment.created_at', 'user.fullname'])
                ->where(['card_id' => $card->id])
                ->asArray()
                ->all();

            $list = [];

            foreach ($replenishments AS $replenishment) {
                $list[] = (object)[
                    'amount' => $replenishment['amount'],
                    'type' => 0,
                    'date' => $replenishment['created_at'],
                    'description' => preg_replace('~^(\S++)\s++(\S)\S++\s++(\S)\S++$~u', '$1 $2.', $replenishment['fullname']),
                ];
            }

            $transactions = Transaction::find()
                ->leftJoin('terminal', 'terminal.id=transaction.terminal_id')
                ->leftJoin('attraction', 'attraction.id=transaction.attraction_id')
                ->select(['attraction.name', 'amount', 'transaction.created_at'])
                ->where(['card_id' => $card->id])
                ->asArray()
                ->all();

            foreach ($transactions AS $transaction) {
                $list[] = (object)[
                    'amount' => $transaction['amount'],
                    'type' => 1,
                    'date' => $transaction['created_at'],
                    'description' => $transaction['name'],
                ];
            }

            array_multisort(array_column($list, 'date'), SORT_DESC, $list);

            // Не верное время что то с таимзоной
            foreach ($list as $l) {
                $l->date = \Yii::$app->formatter->asDatetime($l->date, 'php:H:i d.m.Y');
            }

            return $list;
        } else {
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($form));
        }
    }

    /**
     * Вывод списка пользователей
     * @return array
     * @throws \Exception
     */
    public function actionUserList()
    {
        $users = User::find()
            ->select(['id', 'username', 'role', 'status', 'fullname', 'created_at'])
//            ->where(["status" => Terminal::STATUS_ACTIVE])
            ->asArray()
            ->all();

        $list = [];

        foreach ($users AS $item) {
            $list[] = (object)[
                'id' => $item['id'],
                'fullName' => $item['fullname'],
                'login' => $item['username'],
                'profile' => $item['role'],
                'active' => $item['status'] == User::STATUS_ACTIVE ? true : false,
                'created_at' => date('Y-m-d H:i:s', $item['created_at']),
                'action' => "Изменить",
            ];
        }

        return $list;
    }

    /**
     * Вывод списка платежей
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function actionReplenishmentList()
    {
        $replenishments = Replenishment::find()
            ->leftJoin('card', 'card.id=replenishment.card_id')
            ->leftJoin('user', 'user.id=replenishment.user_id')
            ->leftJoin('discount', 'discount.id=replenishment.discount_id')
            ->select(['replenishment.amount', 'replenishment.created_at', 'replenishment.payment_method', 'card.code', 'user.fullname', 'discount.name'])
            ->asArray()
            ->all();

        $list = [];

        foreach ($replenishments AS $replenishment) {
            $list[] = (object)[
                'id' => trim(chunk_split($replenishment['code'], 3, ' ')),
                'sum' => round($replenishment['amount']) . ' тг',
                'date' => $replenishment['created_at'] ? \Yii::$app->formatter->asDatetime($replenishment['created_at'], 'php:H:i d.m.Y') : '',
                'cashier' => preg_replace('~^(\S++)\s++(\S)\S++\s++(\S)\S++$~u', '$1 $2.', $replenishment['fullname']),
                'paymentMethod' => Replenishment::getStaticPaymentLabel($replenishment['payment_method']),
                'status' => $replenishment['name'],
                'action' => "Посмотреть",
            ];
        }

        return $list;
    }

    /**
     * Вывод списка аттракционов
     * @return array
     */
    public function actionAttractionList()
    {
        $attractions = Attraction::find()
            ->select(['id', 'name', 'price'])
            ->asArray()
            ->all();

        $list = [];

        foreach ($attractions AS $attraction) {
            $list[] = (object)[
                'id' => $attraction['id'],
                'name' => $attraction['name'],
                'sum' => round($attraction['price']) . ' тг',
                'action' => "Изменить",
            ];
        }

        return $list;
    }

    /**
     * Список сессий
     * @return array
     * @throws ConflictHttpException
     */
    public function actionSessionList()
    {
        $sessionListForm = new SessionListForm();

        if ($sessionListForm->load(Yii::$app->request->post(), '') && $sessionListForm->validate()) {
            $query = $sessionListForm->pagination();

            $countQuery = clone $query;
            $pages = new Pagination(['totalCount' => $countQuery->count()]);
            $pages->setPage($sessionListForm->page);
            $pages->setPageSize($sessionListForm->limit);

            $models = $query->offset($pages->offset)
                ->limit($pages->limit)
                ->all();

            $arr = $sessionListForm::format($models);
            $pageCount = $pages->getPageCount();
            if ($sessionListForm->page >= $pageCount - 1) {
                $hasNext = false;
            } else {
                $hasNext = true;
            }
            if ($sessionListForm->limit == 0) {
                $arr = [];
            }
            return ['status' => 200, 'data' => $arr, 'pages' => $sessionListForm->page, 'hasNext' => $hasNext, 'pagesCount' => $pageCount];
        } else {
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($sessionListForm));
        }
    }

    /**
     * Список пополнений с пагинацией и фильтром
     * @return array
     * @throws ConflictHttpException
     */
    public function actionReplenishmentPaginationList()
    {
        return FrontHelper::pagination(new ReplenishmentPaginationForm());
    }

    /**
     * Список Отчеты->пополнения с пагинацией
     * @return array
     * @throws ConflictHttpException
     */
    public function actionReportPaginationList()
    {
        return FrontHelper::pagination(new ReportPaginationForm($this->userId));
    }

    /**
     * Список аттракционов с пагинацией
     * @return array
     * @throws ConflictHttpException
     */
    public function actionAttractionPaginationList()
    {
        return FrontHelper::pagination(new AttractionPaginationForm());
    }

    /**
     * Сумма и количество аттракционов
     * @return array
     * @throws ConflictHttpException
     */
    public function actionAttractionTotalList()
    {
        return FrontHelper::getTotalSum(new AttractionTotalForm());
    }

    /**
     * Сумма и количество отчетов
     * @return array
     * @throws ConflictHttpException
     */
    public function actionReportTotalList()
    {
        return FrontHelper::getTotalSum(new ReportTotalForm());
    }

    /**
     * Сумма и количество транзакций
     * @return array
     * @throws ConflictHttpException
     */
    public function actionWriteOffTotalList()
    {
        return FrontHelper::getTotalSum(new WriteOffTotalForm());
    }

    /**
     * Список Отчеты->пополнения с пагинацией (касса)
     * @return array
     * @throws ConflictHttpException
     */
    public function actionReportCashierList()
    {
        return FrontHelper::pagination(new ReportPaginationCashierForm($this->userId));
    }

    /**
     * Список отчетов по текущей сессии
     * @return array
     * @throws ConflictHttpException
     */
    public function actionReportSessionList()
    {
        return FrontHelper::pagination(new ReportSessionForm($this->userId));
    }

    /**
     * Список транзакций с пагинацией
     * @return array
     * @throws ConflictHttpException
     */
    public function actionTransactionPaginationList()
    {
        return FrontHelper::pagination(new TransactionPaginationForm());
    }

    /**
     * Получение ссылки на скачивания пополнений
     * @return array
     * @throws ConflictHttpException
     */
    public function actionUrlExcelReplenishment()
    {
        $form = new ReplenishmentExcelForm();
        if ($form->load(\Yii::$app->request->post(), '') && $form->validate()) {
            return ['status' => 200, 'url' => Yii::$app->params['apiDomain'] . '/v1/excel/replenishment' . $form->getString()];
        } else {
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($form));
        }
    }

    /**
     * Получение ссылки на скачивания отчетов
     * @return array
     */
    public function actionUrlExcelReport()
    {
        return ['status' => 200, 'url' => Yii::$app->params['apiDomain'] . '/v1/excel/replenishment'];
    }

    /**
     * Получение ссылки на скачивания транзакций
     * @return array
     */
    public function actionUrlExcelTransaction()
    {
        $form = new TransactionPaginationForm();
        $form->load(\Yii::$app->request->post(), '');
        if ($form->cashier) {
            $string = '?cashier=' . $form->cashier;
        } else {
            $string = '';
        }
        return ['status' => 200, 'url' => Yii::$app->params['apiDomain'] . '/v1/excel/transaction' . $string];
    }

    /**
     * Получение ссылки на скачивания прайс листа
     * @return array
     */
    public function actionUrlExcelAttraction()
    {
        return ['status' => 200, 'url' => Yii::$app->params['apiDomain'] . '/v1/excel/attraction'];
    }

    /**
     * Импорт прайс листа
     * @return array
     * @throws ConflictHttpException
     */
    public function actionImportExcelAttraction()
    {
        $form = new ImportAttractionForm();
        if (isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name']) {
            $form->file = file($_FILES['file']['tmp_name']);
            $model = $form->save();
//            var_dump($model); die;
            if (!$model) {
                throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($form));
            }
            return ['status' => 200];
        } else {
            throw new ConflictHttpException("Выберите файл");
        }
    }

    /**
     * Добавление терминала
     * @return array
     * @throws ConflictHttpException
     */
    public function actionAddTerminal()
    {
        return FrontHelper::helper(new TerminalForm());
    }

    /**
     * Добавление карты
     * @return array
     * @throws ConflictHttpException
     */
//    public function actionAddCard()
//    {
//        return FrontHelper::helper(new CardForm());
//    }

    /**
     * Добавление токена пользователю
     * @return array
     * @throws ConflictHttpException
     */
    public function actionAddToken()
    {
        return FrontHelper::helper(new TokenForm($this->userId));
    }

    /**
     * Добавление сообщения
     * @return array
     * @throws ConflictHttpException
     */
    public function actionAddMessage()
    {
        return FrontHelper::helper(new MessageForm());
    }

    /**
     * Обновление карты
     * @return array
     * @throws ConflictHttpException
     */
    public function actionUpdateCard()
    {
        return FrontHelper::helper(new BlockCardForm());
    }

    /**
     * Добавление пополнения
     * @return array
     * @throws ConflictHttpException
     * @throws \Exception
     */
    public function actionAddReplenishment()
    {
        $form = new ReplenishmentForm($this->userId);
        if ($form->load(\Yii::$app->request->post(), '')) {
            $model = $form->save();
            if (!$model) {
                throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($form));
            }

            if ($model->errors) {
                throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($model));
            }
            $cashier = User::findOne(['id' => $model->user_id])->fullname;
            $code = Card::findOne(['id' => $model->card_id])->code;
            $discount = Discount::findOne(['id' => $model->discount_id]);
            if ($discount) {
                $discount = $discount->name;
            } else {
                $discount = null;
            }
            return ['status' => 200, 'id' => $model->id, 'date' => $model->created_at, 'cashier' => $cashier, 'code' => $code, 'sum' => $model->amount, 'paymentMethod' => Replenishment::getStaticPaymentLabel($model->payment_method), 'discount' => $discount, 'discountAmount' => $model->discount_amount, 'type' => $model->getTypeLabel()];
        } else {
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($form));
        }
    }

    /**
     * Добавление транзакции
     * @return array
     * @throws ConflictHttpException
     */
//    public function actionAddTransaction()
//    {
//        return FrontHelper::helper(new TransactionForm());
//    }

    /**
     * Добавление аттракциона
     * @return array
     * @throws ConflictHttpException
     */
    public function actionAddAttraction()
    {
        return FrontHelper::helper(new AttractionForm());
    }

    /**
     * Добавление пользователя
     * @return array
     * @throws ConflictHttpException
     */
    public function actionAddUser()
    {
        return FrontHelper::helper(new UserForm());
    }

    /**
     * Добавление скидки/бонуса
     * @return array
     * @throws ConflictHttpException
     */
    public function actionAddDiscount()
    {
        return FrontHelper::helper(new DiscountForm());
    }

    /**
     * Проверка карты
     * @return array
     * @throws ConflictHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCheckCard()
    {
        $checkCardForm = new CheckCardForm();
        $formData = Yii::$app->request->getBodyParams();
        $requestData = $this->getSpecificData();
        $formData = array_merge($formData, $requestData);

        if ($checkCardForm->load($formData, '') && $checkCardForm->validate()) {
            $card = Card::findOne(['code' => $checkCardForm->card_code]);
            if ($card) {
                return ['status' => 200, 'id' => $card->id, 'balance' => $card->balance, 'is_active' => $card->status];
            } else {
                $qrCode = QrCode::find()->where(['uuid' => $checkCardForm->card_code])->all();
                if ($qrCode) {
//                    return ['status' => 200, 'message' => "CARD_IS_NEW"];
                    throw new ConflictHttpException(Yii::t('api', 'CARD_IS_NEW'));

                }
//                return ['status' => 400, 'message' => 'Not Found'];
//                return ['status' => 200, 'message' => "CARD_NOT_FOUND"];
                throw new ConflictHttpException(Yii::t('api', 'CARD_NOT_FOUND'));
            }
        }
    }

    /**
     * Активация карты
     * @return array
     * @throws ConflictHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function actionActivateCard()
    {
        $checkCardForm = new ActivateCardForm();
        $formData = Yii::$app->request->getBodyParams();
        $requestData = $this->getSpecificData();
        $formData = array_merge($formData, $requestData);

        if ($checkCardForm->load($formData, '') && $checkCardForm->validate()) {
            $uuid = QrCode::findOne(['uuid' => $checkCardForm->card_code]);
            if ($uuid) {
                $card = new Card();
                $card->code = $uuid->uuid;
                $card->balance = 0;
                $card->status = Card::STATUS_ACTIVE;
                if ($card->save()) {
                    $transaction = new Replenishment();
                    $transaction->card_id = $card->id;
                    $transaction->user_id = $this->userId;
                    $transaction->payment_method = $checkCardForm->payment_method;
                    $transaction->amount = Yii::$app->params['cardAmount'];
                    $transaction->discount_id = null;
                    $transaction->discount_amount = 0;
                    $transaction->type = Replenishment::TYPE_ACTIVATE;
                    $transaction->session_id = FrontHelper::getSession($this->userId);
                    if ($transaction->save()) {
                        $cashier = User::findOne(['id' => $transaction->user_id])->fullname;
                        return ['status' => 200, 'id' => $transaction->id, 'date' => $transaction->created_at, 'cashier' => $cashier, 'code' => $card->code, 'sum' => $transaction->amount, 'paymentMethod' => Replenishment::getStaticPaymentLabel($transaction->payment_method), 'discount' => null, 'discountAmount' => 0, 'type' => $transaction->getTypeLabel()];
//                        return ['status' => 200, 'id' => $->id, 'balance' => $card->balance, 'is_active' => $card->status];
                    } else {
                        throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($transaction));
                    }
                } else {
                    throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($card));
                }
            } else {
//                return ['status' => 400, 'message' => 'Not Found'];
                throw new ConflictHttpException(Yii::t('api', 'NOT_FOUND'));
            }
        }
    }

    /**
     * Изменение терминала
     * @return array
     * @throws ConflictHttpException
     */
    public function actionUpdateTerminal()
    {
        $form = new UpdateTerminalForm();
        if ($form->load(\Yii::$app->request->post(), '')) {
            $model = $form->save();
            if (!$model['status']) {
                if ($model['code'] == 1) {
                    throw new ConflictHttpException($form->getErrorSummary(true)[0]);
                } elseif ($model['code'] == 3) {
                    throw new ConflictHttpException($model['message'][0]);
                } else {
                    throw new ConflictHttpException($model['message']);
                }
            }

            if ($model['message']->errors) {
                throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($model['message']));
            }
            return ['status' => 200];
        } else {
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($form));
        }
    }

    /**
     * Изменение аттракциона
     * @return array
     * @throws ConflictHttpException
     */
    public function actionUpdateAttraction()
    {
        return FrontHelper::helper(new UpdateAttractionForm());
    }

    /**
     * Изменение пользователя
     * @return array
     * @throws ConflictHttpException
     */
    public function actionUpdateUser()
    {
        return FrontHelper::helper(new UpdateUserForm());
    }

    /**
     * Изменение скидки/бонуса
     * @return array
     * @throws ConflictHttpException
     */
    public function actionUpdateDiscount()
    {
        return FrontHelper::helper(new UpdateDiscountForm());
    }


    /**
     * История пополнений
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws ConflictHttpException
     */
    public function actionReport()
    {
        $reportCardForm = new ReportCardForm();
        $formData = Yii::$app->request->getBodyParams();
        $requestData = $this->getSpecificData();
        $formData = array_merge($formData, $requestData);

        $replenishment = Replenishment::find()
            ->select([
                'card.code',
                'user.fullname',
                'replenishment.payment_method',
                'discount.name',
                'replenishment.discount_amount',
                'replenishment.amount',
                'replenishment.created_at'
            ])
            ->leftJoin('card', 'card.id=replenishment.card_id')
            ->leftJoin('user', 'user.id=replenishment.user_id')
            ->leftJoin('discount', 'discount.id=replenishment.discount_id');

        if ($reportCardForm->load($formData, '') && $reportCardForm->validate()) {
            if ($reportCardForm->card_code) {
                $card = Card::findOne(['code' => $reportCardForm->card_code]);
                $replenishment->where(['card_id' => $card->id]);
            }

            if ($replenishment) {
                return ['status' => 200, 'replenishment_history' => $replenishment->asArray()->all()];
            } else {
//                return ['status' => 400, 'message' => 'Not Found'];
                throw new ConflictHttpException(Yii::t('api', 'NOT_FOUND'));
            }
        }
    }

    /**
     * История пополнений (касса)
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws ConflictHttpException
     */
    public function actionReportCashier()
    {
        $reportCardForm = new ReportCardCashierForm();
        $formData = Yii::$app->request->getBodyParams();
        $requestData = $this->getSpecificData();
        $formData = array_merge($formData, $requestData);

        $replenishment = Replenishment::find()
            ->select([
                'card.code',
                'user.fullname',
                'replenishment.payment_method',
                'discount.name',
                'replenishment.discount_amount',
                'replenishment.amount',
                'replenishment.created_at',
                'replenishment.user_id'
            ])
            ->leftJoin('card', 'card.id=replenishment.card_id')
            ->leftJoin('user', 'user.id=replenishment.user_id')
            ->leftJoin('discount', 'discount.id=replenishment.discount_id')
            ->where(['user_id' => $this->userId]);

        if ($reportCardForm->load($formData, '') && $reportCardForm->validate()) {
            if ($reportCardForm->card_code) {
                $card = Card::findOne(['code' => $reportCardForm->card_code]);
                $replenishment->where(['card_id' => $card->id]);
            }

            if ($replenishment) {
                return ['status' => 200, 'replenishment_history' => $replenishment->asArray()->all()];
            } else {
//                return ['status' => 400, 'message' => 'Not Found'];
                throw new ConflictHttpException(Yii::t('api', 'NOT_FOUND'));
            }
        }
    }

    /**
     * Список дисконтных систем(админка)
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionDiscountList()
    {
        $discount = Discount::find()
            ->select(['id', 'name', 'type', 'quantity'])
            ->asArray()
            ->all();

        return $discount;
    }

    /**
     * Список дисконтных систем касса
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionDiscountDoubleList()
    {
        $discountBonus = Discount::find()
            ->select(['id', 'name', 'type', 'quantity'])
            ->where(['type' => Discount::TYPE_BONUS])
            ->asArray()
            ->all();

        $discountPercent = Discount::find()
            ->select(['id', 'name', 'type', 'quantity'])
            ->where(['type' => Discount::TYPE_DISCOUNT])
            ->asArray()
            ->all();

        return [
            'bonus' => $discountBonus,
            'discount' => $discountPercent
        ];
    }

    /**
     * Список карт
     * @return array|\yii\db\ActiveRecord[]
     * @throws ConflictHttpException
     */
    public function actionCardList()
    {
        $form = new CardListForm();

        if (!$form->load(Yii::$app->request->post(), '') && !$form->validate())
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($form));

        $cards = Card::find()
            ->select(['id', 'code', 'balance', 'status', 'created_at']);


        $countQuery = clone $cards;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->setPage($form->page);
        $pages->setPageSize($form->limit);

        $models = $cards->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        $arr = [];
        foreach ($models as $item) {
            $arr[] = (object)$item->toArray();
        }
        $pageCount = $pages->getPageCount();
        if ($form->page >= $pageCount - 1) {
            $hasNext = false;
        } else {
            $hasNext = true;
        }

        return [
            'status' => 200,
            'data' => $arr,
            'pages' => $pageCount,
            'hasNext' => $hasNext
        ];
    }

    /**
     * Удаление атракциона
     * @return array
     * @throws ConflictHttpException
     */
    public function actionDeleteAttraction()
    {
        return FrontHelper::helper(new DeleteAttractionForm());
    }

    /**
     * Удаление терминала
     * @return array
     * @throws ConflictHttpException
     */
    public function actionDeleteTerminal()
    {
        return FrontHelper::helper(new DeleteTerminalForm());
    }

    /**
     * Удаление пользователя
     * @return array
     * @throws ConflictHttpException
     */
    public function actionDeleteUser()
    {
        return FrontHelper::helper(new DeleteUserForm());
    }

    /**
     * Удаление скидки
     * @return array
     * @throws ConflictHttpException
     */
    public function actionDeleteDiscount()
    {
        return FrontHelper::helper(new DeleteDiscountForm());
    }

    /**
     * Перенос баланса
     * @return array
     * @throws ConflictHttpException
     */
    public function actionTransferBalance()
    {
        return FrontHelper::helper(new TransferBalanceForm($this->userId));
    }


    /**
     * Статистика пополнений
     * @return array
     * @throws ConflictHttpException
     */
    public function actionStatistic()
    {
        $sessionId = FrontHelper::getSession($this->userId);
        $replenishments = Replenishment::findAll(['session_id' => $sessionId]);

        $countByCash = 0;
        $countByKaspi = 0;
        $countByCard = 0;
        $sumByCash = 0;
        $sumByCard = 0;
        $sumByKaspi = 0;
        $count = 0;
        $sum = 0;
        foreach ($replenishments as $replenishment) {
            $count++;
            $sum += $replenishment->amount;
            if ($replenishment->payment_method == Replenishment::PAYMENT_BY_CASH) {
                $countByCash++;
                $sumByCash += $replenishment->amount;
            } elseif ($replenishment->payment_method == Replenishment::PAYMENT_BY_CARD) {
                $countByCard++;
                $sumByCard += $replenishment->amount;
            } else {
                $countByKaspi++;
                $sumByKaspi += $replenishment->amount;
            }
        }
        $list = [
            'countByCash' => $countByCash,
            'countByCard' => $countByCard,
            'countByKaspi' => $countByKaspi,
            'countResult' => $count,
            'sumByCash' => $sumByCash,
            'sumByCard' => $sumByCard,
            'sumByKaspi' => $sumByKaspi,
            'sumResult' => $sum,
        ];
        return $list;
    }

    /**
     * Статистика списаний
     * @return array
     */
    public function actionStatisticTransaction()
    {
        if (date("Y-m-d H:i:s") >= date("Y-m-d 03:00:00")) {
            $transactions = Transaction::find()
                ->andWhere([">=", "transaction.created_at", date("Y-m-d 03:00:00")])
                ->andWhere(["<=", "transaction.created_at", date("Y-m-d 02:59:59", strtotime('+1 day'))])
                ->all();
        } else {
            $transactions = Transaction::find()
                ->andWhere([">=", "transaction.created_at", date("Y-m-d 03:00:00", strtotime('-1 day'))])
                ->andWhere(["<=", "transaction.created_at", date("Y-m-d 02:59:59")])
                ->all();
        }

        $countByCash = 0;
        $sumByCash = 0;
        $count = 0;
        $sum = 0;
        foreach ($transactions as $transaction) {
            $count++;
            $sum += $transaction->amount;
            $countByCash++;
            $sumByCash += $transaction->amount;
        }
        $list = [
            'countByCash' => $countByCash,
            'countResult' => $count,
            'sumByCash' => $sumByCash,
            'sumResult' => $sum,
        ];
        return $list;
    }
}