<?php

/* @var $this yii\web\View */

use kartik\datetime\DateTimePicker as DateTimePickerAlias;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'My Yii Application';
?>

<? if ($action == "add_statistics"){?>
    <b><? if($message_error) echo 'Ошибка: '. $message_error; else echo 'Запись успешно добавлена!'; ?></b>
<?}else if ($action == "get_statistics"){?>
    <b><? if($message_error) echo 'Ошибка: '. $message_error; else { echo 'Статистика по таблице!'; ?></b>
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Clicks</th>
                <th scope="col">Installs</th>
                <th scope="col">CRi, %</th>
                <th scope="col">Trials</th>
                <th scope="col">CRti, %</th>
            </tr>
            </thead>
            <tbody>
            <?  while ($row = $res->fetch_assoc()) {?>
                <tr>
                    <th scope="row"><?=$row['campaign_id']?></th>
                    <td><?=$row['clicks']?></td>
                    <td><?=$row['installs']?></td>
                    <td>
                        <?if($row['clicks'])
                            echo round((($row['installs'] *100)/$row['clicks']),2);
                        else
                            echo '---';?>
                    </td>
                    <td><?=$row['trials']?></td>
                    <td>
                        <?if($row['installs'])
                            echo round((($row['trials'] *100)/$row['installs']),2);
                        else
                            echo '---';?></td>
                </tr>
            <? }?>
            </tbody>
        </table>

        <?php $form = ActiveForm::begin(['action' => [''], 'method' => 'get', 'id' => 'form-search-stat']) ?>
        <?= $form->field($sort_form_model, 'date1')->widget(DateTimePickerAlias::classname(), [
            'name' => 'date1',
            'value' => '07-11-2020, 11:45',
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'dd-M-yyyy hh:ii:ss'
            ]
        ]); ?>
        <?= $form->field($sort_form_model, 'date2')->widget(DateTimePickerAlias::classname(), [
            'name' => 'date2',
            'value' => '08-11-2020, 14:45',
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'dd-M-yyyy hh:ii:ss'
            ]
        ]); ?>
        <?
        $items = ArrayHelper::map($model_camp_id,'campaign_id','campaign_id');
        $params = [
            'prompt' => 'Укажите id кампании'
        ];
        ?>
        <?=$form->field($sort_form_model, 'campaign_id')->dropDownList($items,$params);?>
        <?=$form->field($sort_form_model, 'group_at_campaign')->checkbox()?>
        <?= Html::submitButton('Send', ['class' => 'btn btn-success']) ?>
        <?php ActiveForm::end() ?>
    <?}?>
<?}?>
<script>
    var $form = $('#form-search-stat');
    $form.on('beforeSubmit', function() {
        //   $('input[name=date1]').val(( Date.parse($('input[name=date1]').val)));
        return true; // prevent default submit
    });
</script>


