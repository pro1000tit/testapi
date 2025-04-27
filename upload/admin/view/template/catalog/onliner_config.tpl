<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" id="button-save" form="form-setting" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        </div>
      <h1>Настройки интеграции с onliner.by</h1>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i> Настройки интеграции с onliner.by</h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-setting" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab">Настройки</a></li>

          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">


              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-deliverycity">Доставка по Минску</label>
                <div class="col-sm-10">
                  <input type="text" name="config_deliverycity" value="<?php echo $config_deliverycity; ?>" placeholder="Доставка по Минску" id="input-deliverycity" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-deliverycountry">Доставка по РБ</label>
                <div class="col-sm-10">
                  <input type="text" name="config_deliverycountry" value="<?php echo $config_deliverycountry; ?>" placeholder="Доставка по РБ" id="input-deliverycountry" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-clientid111">Гарантия</label>
                <div class="col-sm-10">
                  <input type="text" name="config_gar" value="<?php echo $config_gar; ?>" placeholder="Гарантия" id="input-clientid111" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-clientid11">Импортер</label>
                <div class="col-sm-10">
                  <input type="text" name='config_importer' value='<?php echo $config_importer; ?>' placeholder="Импортер" id="input-clientid11" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-clientid111">Сервисный центр</label>
                <div class="col-sm-10">
                  <input type="text" name="config_servis" value='<?php echo $config_servis; ?>' placeholder="Сервисный центр" id="input-clientid111" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-clientid1">Описание предложения</label>
                <div class="col-sm-10">
                  <input type="text" name="config_comment" value="<?php echo $config_comment; ?>" placeholder="Описание предложения" id="input-clientid1" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-clientid">Client ID</label>
                <div class="col-sm-10">
                  <input type="text" name="config_clientid" value="<?php echo $config_clientid; ?>" placeholder="Client ID" id="input-clientid" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-cliensecret">Client Secret</label>
                <div class="col-sm-10">
                  <input type="text" name="config_cliensecret" value="<?php echo $config_cliensecret; ?>" placeholder="Client Secret" id="input-cliensecret" class="form-control" />
                </div>
              </div>




            </div>



          </div>
        </form>
      </div>
    </div>
  </div>
  <?php echo $footer; ?>
