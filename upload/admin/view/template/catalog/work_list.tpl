<?php echo $header; ?><?php echo $column_left; ?>



<div id="content">
    <div class="page-header">
        <div class="container-fluid">

            <h1>Связи категорий</h1>
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
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
            </div>
            <div class="panel-body">

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>

                            <td class="text-left">Категория на сайте</td>

                            <td class="text-right">Категория поставщика</td>
                        </tr>
                        </thead>
                        <tbody>

                        <?php if ($categories): ?>
                        <?php foreach ($categories as $review) { ?>

                        <tr>
                            <td class="text-left" style="width: 400px;"><?php if ($review['href'] != ''): ?><a href="<?php echo $review['href']; ?>"><?php endif; ?><?php echo $review['name']; ?><?php if ($review['href'] != ''): ?></a><?php endif; ?>
                                <div>
                                    <?php if($review['manufs'] AND $review['href']=='') { ?>

                                    <?php foreach ($review['manufs'] as $m) { ?>
                                    <input type="checkbox" value="1" <?php if ($m['add'] == 1): ?>checked="checked"<?php endif; ?> name="manu<?php echo $m['id']; ?>_<?php echo $review['id']; ?>" id="manu<?php echo $m['id']; ?>_<?php echo $review['id']; ?>" onclick="setManu(<?php echo $m['id']; ?>, <?php echo $review['id']; ?>);"> <label  for="manu<?php echo $m['id']; ?>_<?php echo $review['id']; ?>"><?php echo $m['name']; ?></label> |
                                    <?php } ?>
                                    <?php } ?>
                                </div>

                            </td>

                            <td class="text-right"><?php if ($review['href'] == ''): ?>

                                <select id="parent_id_<?php echo $review['id']; ?>" onchange="set_cat(<?php echo $review['id']; ?>);" name="parent_id_<?php echo $review['id']; ?>" class="form-control js-example-basic-single">
                                    <option value="0" selected="selected">Укажите категорию</option>
                                    <?php foreach ($onliner_cat as $cat) { ?>
                                    <?php if($cat['id'] == $review['onliner_id']) { ?>


                                    <option value="<?php echo $cat['id']; ?>" selected="selected"><?php echo $cat['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                                <div id="result_<?php echo $review['id']; ?>" style="display: none;">Сохранили</div>
                                <?php endif; ?></td>
                        </tr>
                        <?php } ?>
                        <?php else: ?>
                        <tr>
                            <td class="text-center" colspan="7"><?php echo $text_no_results; ?></td>
                        </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </div>
</div>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
<script>

    function setManu(m_id, cat_id){
        if ($('#manu'+m_id+'_'+cat_id).is(':checked')){
            var add=1;
        } else {
            var add=0;
        }

        $.ajax({
            type:  'POST',
            cache:  false ,
            url:  'index.php?route=catalog/tmp/setmanu&token=<?php echo $token; ?>',
            data:  { 'cat_id' : cat_id, 'm_id': m_id, 'add': add },
            success: function(data) {
                $('#result_'+cat_id).show();
                setTimeout (function() {
                    $('#result_'+cat_id).hide();
                }, 3000);
            }
        });

    }
    jQuery(document).ready(function() {
        jQuery('.js-example-basic-single').select2();
    });
</script>
<script>
    function set_cat(cat_id){
        var real_cat = $('#parent_id_'+cat_id).val();

        $.ajax({
            type:  'POST',
            cache:  false ,
            url:  'index.php?route=catalog/tmp/savecat&token=<?php echo $token; ?>',
            data:  { 'real_cat' : real_cat, 'cat_id': cat_id },
            success: function(data) {
                $('#result_'+cat_id).show();
                setTimeout (function() {
                    $('#result_'+cat_id).hide();
                }, 3000);
            }
        });

    }
</script>

<?php echo $footer; ?>
