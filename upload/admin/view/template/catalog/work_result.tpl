<?php echo $header; ?><?php echo $column_left; ?>




<div id="content">
    <div class="page-header">
        <div class="container-fluid">

            <h1>Результат последнего обновления</h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> Результат последнего обновления</h3>
            </div>
            <div class="panel-body">

               <p>Дата последней отправки данных <?php echo $date; ?></p>
                <br>

                <?php foreach ($result11 as $r) { ?>
                <p><b>product_id:</b><?php echo $r['values']['id']; ?></p>
                <p><b>vendor:</b> <?php echo $r['values']['vendor']; ?> </p>
                <p><b>model:</b> <?php echo $r['values']['model']; ?></p>
                <?php if(isset($r['errors']['model'])) { ?>
                <?php foreach($r['errors']['model'] as $er) { ?>
                <p><?php echo $er['code']; ?></p>
                <p><?php echo $er['message']; ?></p>

                <?php } ?>
                <?php } ?>
                <?php if(isset($r['errors']['vendor'])) { ?>
                <?php foreach($r['errors']['vendor'] as $er) { ?>
                <p><?php echo $er['code']; ?></p>
                <p><?php echo $er['message']; ?></p>

                <?php } ?>
                <?php } ?>





                <hr>

                <?php } ?>




            </div>
        </div>
    </div>
</div>


<?php echo $footer; ?>
