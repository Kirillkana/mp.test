<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "statistics".
 *
 * @property int $id
 * @property string $cid
 * @property int $campaign_id
 * @property string $event
 * @property int $time
 * @property string $sub1
 */
class Statistics extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'statistics';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cid', 'campaign_id', 'event', 'time', 'sub1'], 'required'],
            [['campaign_id', 'time'], 'integer'],
            [['event'], 'string', 'max' => 50],
            [['cid','sub1'], 'string', 'max' => 255],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cid' => 'Cid',
            'campaign_id' => 'Campaign ID',
            'event' => 'Event',
            'time' => 'Time',
            'sub1' => 'Sub1',
        ];
    }


}
