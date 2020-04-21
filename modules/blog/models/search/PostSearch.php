<?php

namespace modules\blog\models\search;

use modules\users\models\User;
use modules\users\models\UserProfile;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\blog\models\Post;
use modules\blog\models\Tag;

/**
 * PostSearch represents the model behind the search form of `modules\blog\models\Post`.
 *
 * @property string $date_from
 * @property string $date_to
 * @property string $tagNames
 */
class PostSearch extends Post
{
    public $date_from;
    public $date_to;
    public $tagNames;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'author_id', 'created_at', 'updated_at', 'status', 'sort', 'is_comment'], 'integer'],
            [['date_from', 'date_to'], 'date', 'format' => 'php:Y-m-d'],
            [['title', 'slug', 'anons', 'content', 'tagNames', 'authorName'], 'safe'],
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
        $query->joinWith(['author']);
        $query->joinWith(['authorProfile']);

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
                    'authorName' => [
                        'asc' => [UserProfile::tableName() . '.first_name' => SORT_ASC],
                        'desc' => [UserProfile::tableName() . '.first_name' => SORT_DESC],
                    ],
                    'tagNames' => [
                        'asc' => [Tag::tableName() . '.title' => SORT_ASC],
                        'desc' => [Tag::tableName() . '.title' => SORT_DESC],
                    ],
                    'category_id',
                    'sort',
                    'created_at',
                    'status',
                    'is_comment'
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
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'sort' => $this->sort,
            'is_comment' => $this->is_comment
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'anons', $this->anons])
            ->andFilterWhere(['like', 'content', $this->content]);

        $query->andFilterWhere(['>=', Post::tableName() . '.created_at', $this->date_from ? strtotime($this->date_from . ' 00:00:00') : null])
            ->andFilterWhere(['<=', Post::tableName() . '.created_at', $this->date_to ? strtotime($this->date_to . ' 23:59:59') : null]);

        if (!empty($this->tagNames)) {
            $query->andFilterWhere(['or like', Tag::tableName() . '.title', self::formatStringToArray($this->tagNames)])
                ->andWhere([Tag::tableName() . '.status' => Tag::STATUS_PUBLISH]);
        }

        if (!empty($this->authorName)) {
            $query->andFilterWhere(['like', User::tableName() . '.username', trim($this->authorName)]);
            $query->orFilterWhere(['or like', UserProfile::tableName() . '.first_name', explode(' ', trim($this->authorName))]);
            $query->orFilterWhere(['or like', UserProfile::tableName() . '.last_name', explode(' ', trim($this->authorName))]);
        }

        return $dataProvider;
    }

    /**
     * @param string $str
     * @param string $delimiter
     * @return array
     */
    /**
     * @param string $str
     * @param string $delimiter
     * @return array|string|string[]
     */
    public static function formatStringToArray($str = '', $delimiter = ',')
    {
        $str = str_replace(' ', '', trim($str));
        return explode($delimiter, $str);
    }
}
