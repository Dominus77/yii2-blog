<?php

namespace modules\blog\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\blog\models\Category;

/**
 * CategorySearch represents the model behind the search form of `modules\blog\models\Category`.
 */
class CategorySearch extends Category
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'tree', 'lft', 'rgt', 'depth', 'position', 'created_at', 'updated_at', 'status'], 'integer'],
            [['title', 'slug', 'description'], 'safe'],
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
        $query = Category::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    /*'tree' => SORT_ASC,
                    'lft' => SORT_ASC,*/
                    'tree' => SORT_ASC,
                    'lft' => SORT_ASC,
                    //'depth' => SORT_ASC,
                    'position' => SORT_ASC,
                    //'created_at' => SORT_ASC,
                ],
            ]
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
            'tree' => $this->tree,
            'lft' => $this->lft,
            'rgt' => $this->rgt,
            'depth' => $this->depth,
            'position' => $this->position,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
