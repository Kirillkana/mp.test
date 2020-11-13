<?php
namespace app\models;

use yii\base\Model;


class SortForm extends Model
{

    public $date1;
    public $date2;
    public $campaign_id;
    public $group_at_campaign;

    public function attributeLabels()
    {
        return [
            'date1' => 'Дата от: ',
            'date2' => 'Дата до: ',
            'campaign_id' => 'Id кампании: ',
            'group_at_campaign' => 'Группировать по id кампании'
        ];
    }

    public function rules() {
        return [
            [['date1', 'date2','campaign_id','group_at_campaign'], 'safe']
        ];
    }

    public function formName()
    {
        return '';
    }

}

?>