<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Terminal;

/**
 * TerminalSearch represents the model behind the search form of `common\models\Terminal`.
 */
class TerminalSearch extends Terminal
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'os_type', 'status'], 'integer'],
            [['title', 'login', 'access_token', 'password_hash', 'f_token', 'app_ver', 'created_at', 'updated_at', 'attraction_id'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Terminal::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'os_type' => $this->os_type,
            'status' => $this->status,
            'attraction_id' => $this->attraction_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'login', $this->login])
            ->andFilterWhere(['like', 'access_token', $this->access_token])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'f_token', $this->f_token])
            ->andFilterWhere(['like', 'app_ver', $this->app_ver]);

        return $dataProvider;
    }
}
