<link href="<?php echo base_url('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/plugins/bootstrap-daterangepicker/daterangepicker.css'); ?>" rel="stylesheet">


<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="page-title">Bet History</h4>
                    <ol class="breadcrumb"> </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-border panel-primary">
                        <div class="panel-heading">
                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-pink btn-custom waves-effect waves-light" onclick="onSearch();">Search</button>
                            </div>
                            <h3 class="panel-title">Bet History</h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group has-success">
                                            <label class="col-md-6 control-label">Week</label>
                                            <div class="col-md-6">
                                                <select class="selectpicker" name="week" id="week" data-style="btn-default btn-custom">
                                                    <?php foreach($weeks as $week) { ?>
                                                        <option value="<?php echo $week->week_no; ?>" <?php if($curWeekNo==$week->week_no) echo 'selected'; ?> ><?php echo $week->week_no; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group has-success">
                                            <label class="col-md-6 control-label">Terminal</label>
                                            <div class="col-md-6">
                                                <select class="selectpicker" name="terminal" id="terminal" data-style="btn-default btn-custom">
                                                    <option value="0" selected>ALL</option>
                                                    <?php foreach($terminals as $terminal) { ?>
                                                        <option value="<?php echo $terminal->Id;?>"><?php echo $terminal->terminal_no;?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="table1" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Sales</th>
                                    <th>Total Sale</th>
                                    <th>Total Payable to Agents</th>
                                    <th>Win</th>
                                    <th>Total Winning</th>
                                    <th>Bal Agents</th>
                                    <th>Bal Company</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="table2" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Week</th>
                                    <th>Bet ID</th>
                                    <th>Player</th>
                                    <th>Type</th>
                                    <th>Option</th>
                                    <th>Under</th>
                                    <th>Game List</th>
                                    <th>Score List</th>
                                    <th>APL</th>
                                    <th>Amount Staked</th>
                                    <th>Status</th>
                                    <th>Win Result</th>
                                    <th>Winning</th>
                                    <th>TSN</th>
                                    <th>Agent</th>
                                    <th>Bet Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div> <!-- container -->
    </div> <!-- content -->


<!-- <script src="<?php echo base_url('assets/plugins/switchery/js/switchery.min.js'); ?>"></script> -->
<script src="<?php echo base_url('assets/plugins/bootstrap-daterangepicker/daterangepicker.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js'); ?>"></script>
<script type="text/javascript">
    jQuery('#date-range').datepicker({
        toggleActive: true
    });

    function initTable(tagId, cols, dataUrl) {
        var tblObj = $(tagId).DataTable({
            dom: "lfBrtip",
            buttons: [{
                extend: "copy",
                className: "btn-sm"
            }, {
                extend: "csv",
                className: "btn-sm"
            }, {
                extend: "excel",
                className: "btn-sm"
            }, {
                extend: "pdf",
                className: "btn-sm"
            }, {
                extend: "print",
                className: "btn-sm"
            }],
            responsive: !0,
            processing: true,
            serverSide: false,
            sPaginationType: "full_numbers",
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-right"></i>',
                    previous: '<i class="fa fa-angle-left"></i>',
                    first: '<i class="fa fa-angle-double-left"></i>',
                    last: '<i class="fa fa-angle-double-right"></i>'
                }
            },
            //Set column definition initialisation properties.
            columnDefs: cols,
            ajax: {
                url: dataUrl,
                type: "POST",
            },
        });
        return tblObj;
    }
    var tableName = "<?php echo $table; ?>";
    var tbl1, tbl2;

    tbl1 = initTable("#table1",
        [  {
                targets: [0], //first column 
                orderable: true, //set not orderable
                className: "dt-center"
            },
            {
                targets: [1], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            },
            {
                targets: [2], //first column 
                orderable: true, //set not orderable
                className: "dt-center"
            },
            {
                targets: [3], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            },
            {
                targets: [4], //first column 
                orderable: true, //set not orderable
                className: "dt-center"
            },
            {
                targets: [5], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            },
            {
                targets: [6], //first column 
                orderable: true, //set not orderable
                className: "dt-center"
            },
            {
                targets: [7], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            }            
        ], "<?php echo site_url('Cms_api/get_report/'.$curWeekNo.'/0/0') ?>");


    tbl2 = initTable("#table2",
        [   {
                targets: [0], //first column 
                orderable: true, //set not orderable
                className: "dt-center"
            },
            {
                targets: [1], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            },
            {
                targets: [2], //first column 
                orderable: true, //set not orderable
                className: "dt-center"
            },
            {
                targets: [3], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            },{
                targets: [4], //first column 
                orderable: true, //set not orderable
                className: "dt-center"
            },
            {
                targets: [5], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            },{
                targets: [6], //first column 
                orderable: true, //set not orderable
                className: "dt-center"
            },
            {
                targets: [7], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            },
            {
                targets: [8], //first column 
                orderable: true, //set not orderable
                className: "dt-center"
            },
            {
                targets: [9], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            },            
            {
                targets: [10], //first column 
                orderable: true, //set not orderable
                className: "dt-center"
            },
            {
                targets: [11], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            },{
                targets: [12], //first column 
                orderable: true, //set not orderable
                className: "dt-center"
            },
            {
                targets: [13], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            },{
                targets: [14], //first column 
                orderable: true, //set not orderable
                className: "dt-center"
            },
            {
                targets: [15], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            },                        
            {
                targets: [-1], //last column
                orderable: false, //set not orderable
                className: "actions dt-center"
            }
        ], "<?php echo site_url('Cms_api/get_bets/'.$curWeekNo.'/0/0') ?>");

        function onSearch()
        {
            var week = document.getElementById('week').value;
            var terminalId = document.getElementById('terminal').value;
            tbl2.ajax.url("<?php echo site_url('Cms_api/get_bets')?>" + "/" + week + "/0/" + terminalId);
            tbl2.ajax.reload();

            tbl1.ajax.url("<?php echo site_url('Cms_api/get_report')?>" + "/" + week + "/0/" + terminalId);
            tbl1.ajax.reload();            
        }

        function onVoid(betId)
        {
            $.ajax({
                url: "<?php echo site_url('User_api/void_bet') ?>",
                data: {
                    betId: betId
                },                
                type: "POST",
                dataType: "JSON",
                success: function(data) {
                    if(data.result==1)
                    {
                        swal("Void success!", "", "success"); 
                        tbl2.ajax.reload();
                    }
                    else
                    {
                        swal("Void Error!", data.message, "error");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    swal("Error!", "", "error");
                }
            });
        }

</script>