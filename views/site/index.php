<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';

?>
<div class="site-index">
    <div class="body-content">

        <div class="row">
            <?php foreach ($model as $m): ?>
                <div class="col-12">
                     <p>
                         <?= date_format(date_create($m->created),"D, jS F y") ?>
                         <?php if (isset($displayConfig) && isset($displayConfig['showTopic']) && $displayConfig['showTopic']) echo('<span>'.$m->topic->topic_title.'</span>'); ?>
                     </p>
                    <img style="vertical-align: top; margin-top: 4px;" class="md-icon" width="35" src="https://cdn1.iconfinder.com/data/icons/social-links/80/_47-512.png">
                    <a style="width: 80%; display: inline-block" href="../articles/<?= $m->filename ?>">
                        <div>
                            <h2 style="margin-top: 0"><?= $m->title ?></h2>
                            <p><?= $m->first_row ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach;?>
        </div>

    </div>
</div>
