<link href="<?php echo base_url('assets/plugins/custombox/css/custombox.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/plugins/custombox/js/custombox.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/custombox/js/legacy.min.js'); ?>"></script>

<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-10">
                    <h4 class="page-title">Prize Value</h4>
                    <div class="text-center">
                        <p class="text-info"><b>Week <?php echo $curWeekNo; ?>:</b><span class="text-muted">(<?php if ($curWeek != null) echo $curWeek->start_at . ' ~ ' . $curWeek->close_at; ?>)</span></p>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="btn-group pull-right">
                        <div class="m-b-30">
                            <button id="addToTable" class="btn btn-default waves-effect waves-light" onclick="onApply();"><i class="fa fa-check-square-o"></i> Apply</button>
                        </div>
                    </div>
                </div>
            </div>

            <?php foreach ($prizes as $prize) {
                $optionId = $prize['option_id']; ?>
                <div class="col-sm-3">
                    <div class="panel panel-color panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php echo $prize['option']; ?></h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form">
                                <?php for ($j = 0; $j < 4; $j++) { ?>
                                    <div class="form-group has-success">
                                        <label class="col-md-3 control-label">U<?php echo (3 + $j); ?></label>
                                        <div class="col-md-9">
                                            <input class="vertical-spin form-control" type="text" value="<?php echo $prize['values'][$j]; ?>" data-bts-min="0" data-bts-max="1000" id="<?php echo 'val_' . $optionId . '_' . ($j + 3); ?>">
                                        </div>
                                    </div>
                                <?php } ?>
                                <hr>
                                <div class="btn-group pull-right">
                                    <button type="button" class="btn btn-pink btn-custom btn-rounded waves-effect waves-light" onclick="onSave(<?php echo $optionId; ?>);">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>


        </div> <!-- container -->
    </div> <!-- content -->


    <!-- <script src="<?php echo base_url('assets/plugins/switchery/js/switchery.min.js'); ?>"></script> -->
    <script type="text/javascript">
        function onSave(optionId) {
            var v3 = document.getElementById("val_" + optionId + "_3").value;
            var v4 = document.getElementById("val_" + optionId + "_4").value;
            var v5 = document.getElementById("val_" + optionId + "_5").value;
            var v6 = document.getElementById("val_" + optionId + "_6").value;

            $.ajax({
                url: "<?php echo site_url('Cms_api/edit_prize') ?>",
                data: {
                    optionId: optionId,
                    v3: v3,
                    v4: v4,
                    v5: v5,
                    v6: v6,
                },
                type: "POST",
                dataType: "JSON",
                success: function(data) {

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    swal("Error!", "", "error");
                }
            });
        }


        function onApply()
        {
            $.ajax({
                url: "<?php echo site_url('Api/apply_game_result') ?>",
                data: {
                },
                type: "POST",
                dataType: "JSON",
                success: function(data) {
                    if(data.status==200)
                    {
                        swal("Apply Success!", "", "success");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    swal("Error!", "", "error");
                }
            });
        }
        
    </script>