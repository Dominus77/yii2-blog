<?php

namespace modules\blog\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\blog\models\Post;
use modules\blog\models\Tag;

/**
 * PostSearch represents the model behind the search form of `modules\blog\models\Post`.
 */
class PostSearch extends Post
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'author_id', 'created_at', 'updated_at', 'status', 'sort'], 'integer'],
            [['title', 'slug', 'anons', 'content', 'currentTag'], 'safe'],
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

        $query = Post::find();
        $query->joinWith(['tags']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 25
            ],
            'sort' => [
                //'defaultOrder' => ['id' => SORT_ASC],
                'attributes' => [
                    'title',
                    'slug',
                    'author_id',
                    'currentTag' => [
                        'asc' => [Tag::tableName() . '.title' => SORT_ASC],
                        'desc' => [Tag::tableName() . '.title' => SORT_DESC],
                    ],
                    'category_id',
                    'sort',
                    'created_at',
                    'status',
                ]
            ],
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
            'category_id' => $this->category_id,
            'author_id' => $this->author_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'sort' => $this->sort,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'anons', $this->anons])
            ->andFilterWhere(['like', 'content', $this->content]);

        if (!empty($this->currentTag)) {
            $query->andFilterWhere(['or like', Tag::tableName() . '.title', self::formatStringToArray($this->currentTag)]);
        }

        return $dataProvider;
    }

    /**
     * @param string $str
     * @param string $delimiter
     * @return array
     */
    public static function formatStringToArray($str = '', $delimiter = ',')
    {
        $str = trim($str);
        $str = str_replace(' ', '', $str);
        return explode($delimiter, $str);
    }
}
