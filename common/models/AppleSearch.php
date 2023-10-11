<?php

namespace common\models;

use DateTime;
use yii\data\ActiveDataProvider;

class AppleSearch extends Apple
{

    public function rules(): array
    {
        return [
            [['id'], 'integer'],
//            [['color', 'status'], 'safe'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = Apple::find();

        $query->andFilterWhere(['!=', 'status', Apple::STATUS_DELETED]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->id) {
            $query->andFilterWhere(['id' => $this->id]);
        }

        if ($this->size) {
            $query->andFilterWhere(['size' => $this->size]);
        }

        if ($this->status) {
            $query->andFilterWhere(['status' => $this->status]);
        }

        if ($this->color) {
            $query->andFilterWhere(['color' => $this->color]);
        }

        return $dataProvider;
    }
}