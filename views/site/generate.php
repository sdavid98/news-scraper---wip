<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Generate';
?>
<div class="site-about">
    <h1><?= $model['id']; ?></h1>

    <form id="generator" method="post" action="generate">
        <div class="form-group">
            <label for="link">Article Link</label>
            <input type="text" name="link" class="form-control" id="link">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <script>
        $('#generatorr').on('submit', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if ($('#link').val() !== '') {
                var data = new FormData();
                data.append('link', $('#link').val());
                $.ajax({
                    url: "<?= \yii\helpers\Url::base() ?>/generate",
                    type: 'POST',
                    data: data,
                    contentType: false,
                    processData: false,
                    success: function (data) {

                    }
                });
            }
        })
    </script>
</div>
