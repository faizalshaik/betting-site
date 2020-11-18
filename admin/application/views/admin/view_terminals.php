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
                <div class="col-sm-6">
                    <h4 class="page-title">Terminals</h4>
                    <ol class="breadcrumb"> </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <div class="btn-group pull-right m-b-30">
                                <button class="btn btn-success waves-effect waves-light  m-r-10" onclick="enableAll();">Enable All</button>
                                <button class="btn btn-danger waves-effect waves-light m-r-10" onclick="disableAll();">Disable All</button>
                                <button id="addToTable" class="btn btn-default waves-effect waves-light m-r-10" onclick="addNew();">Add Terminal <i class="fa fa-plus"></i></button>
                            </div>
                        </div>

                        <table id="table1" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Password</th>
                                    <th>Agent</th>
                                    <th>Credit Limit</th>
                                    <th>Max Stake</th>
                                    <th>Odd Options</th>
                                    <th>Unders</th>
                                    <th>Actions</th>
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

<!-- dialog -->

<div id="eidt-modal" class="modal-demo col-sm-12" style="padding: 0px !important;">
    <button type="button" class="close" onclick="Custombox.close(); tbl.ajax.reload();">
        <span>&times;</span><span class="sr-only">Close</span>
    </button>
    <h4 class="custom-modal-title">Edit Terminal</h4>
    <div class="custom-modal-text text-left">
        <div class="profile-detail card-box">
            <form class="form-horizontal" role="form" style="width:480px;">
                <input type="hidden" id="Id" value="" />
                <div class="form-group has-success">
                    <label class="col-md-3 control-label">SN</label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" id="sn" name="sn">
                    </div>
                </div>
                <div class="form-group has-success">
                    <label class="col-md-3 control-label">Agent</label>
                    <div class="col-md-9">                                            
                        <select class="selectpicker" name="agent" id="agent" data-style="btn-default btn-custom">
                            <option value="0" selected></option>
                            <?php foreach ($agents as $agent) { ?>
                                <option value="<?php echo $agent->Id; ?>"><?php echo $agent->user_id; ?></option>
                            <?php } ?>
                        </select>                        
                    </div>
                </div>
                <div class="form-group has-success">
                    <label class="col-md-3 control-label">Credit Limit</label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" id="credit_limit" name="credit_limit">
                    </div>
                </div>

                <div class="form-group has-success">
                    <label class="col-md-3 control-label">Min Stake</label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" id="min_stake" name="min_stake">
                    </div>
                </div>

                <div class="form-group has-success">
                    <label class="col-md-3 control-label">Max Stake</label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" id="max_stake" name="max_stake">
                    </div>
                </div>
                <div class="form-group has-success">
                    <label class="col-md-3 control-label">Password</label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" id="password" name="password">
                    </div>
                </div>

                <ul class="list-inline status-list  has-success m-t-20">
                    <li><label class="control-label text-primary">Unders:</label></li>
                    <li>
                        <div class="checkbox checkbox-custom">
                            <input id="chk_u3" type="checkbox" checked>
                            <label for="chk_u3">U3</label>
                        </div>
                    </li>
                    <li>
                        <div class="checkbox checkbox-custom">
                            <input id="chk_u4" type="checkbox" checked>
                            <label for="chk_u4">U4</label>
                        </div>

                    </li>
                    <li>
                        <div class="checkbox checkbox-custom">
                            <input id="chk_u5" type="checkbox" checked>
                            <label for="chk_u5">U5</label>
                        </div>

                    </li>
                    <li>
                        <div class="checkbox checkbox-custom">
                            <input id="chk_u6" type="checkbox" checked>
                            <label for="chk_u6">U6</label>
                        </div>
                    </li>
                </ul>

                <div>                    
                    <button type="button" class="btn btn-pink btn-custom waves-effect waves-light" onclick="onSave();">Save</button>
                </div>

                <table id="tblOpts" class="table table-striped table-bordered  m-t-10">
                    <thead>
                        <tr>
                            <th><i class="icon-settings"></i> Option</th>
                            <th><i class="ion-checkmark-circled"></i> State</th>
                            <th><i class="ion-ios7-paper-outline"></i> Commission</th>
                            <th><i class="ion-ios7-paper-outline"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="form-group  m-t-10">
                    <label class="col-md-3 control-label text-primary">Option</label>
                    <div class="col-md-4">
                        <select class="selectpicker" name="Opts" id="Opts" data-style="btn-default btn-custom">
                            <option value="0" selected></option>
                            <?php foreach ($options as $opt) { ?>
                                <option value="<?php echo $opt->Id; ?>"><?php echo $opt->name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4 input-group">
                        <span class="input-group-btn">
                            <input type="number" id="commision" name="commision" class="form-control">
                            <button type="button" class="btn waves-effect waves-light btn-primary" onclick="saveOption();">Save</button>
                        </span>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- <script src="<?php echo base_url('assets/plugins/switchery/js/switchery.min.js'); ?>"></script> -->
<script type="text/javascript">
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
    var tbl, tblOpt;

    tbl = initTable("#table1",
        [{
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
                targets: [-1], //last column
                orderable: false, //set not orderable
                className: "actions dt-center"
            }
        ], "<?php echo site_url('Cms_api/get_terminals') ?>"
    );

    var tblOpt = $("#tblOpts").DataTable({
        dom: "lfBrtip",
        buttons: [],
        responsive: !0,
        processing: true,
        serverSide: false,
        "paging": false,
        bFilter: false,
        bInfo: false,
        //Set column definition initialisation properties.
        columnDefs: [{
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
                orderable: false, //set not orderable
                className: "dt-center"
            },
            {
                targets: [-1], //first column 
                orderable: false, //set not orderable
                className: "dt-center"
            }            
        ],
        ajax: {
            url: "<?php echo site_url('Cms_api/get_terminal_options/0') ?>",
            type: "POST",
        },
    });

    var $dom = {
        Id: $("#Id"),
        sn: $("#sn"),
        password: $("#password"),
        agent: $("#agent"),
        credit_limit: $("#credit_limit"),
        min_stake: $("#min_stake"),
        max_stake: $("#max_stake"),
        u3: $("#chk_u3"),
        u4: $("#chk_u4"),
        u5: $("#chk_u5"),
        u6: $("#chk_u6"),
        Opts: $("#Opts"),
        commision: $("#commision"),
    }        

    function clearData() {
        $dom.Id.val("");
        $dom.sn.val("");
        $dom.password.val("");
        $dom.agent.val("");
        $dom.credit_limit.val("");
        $dom.max_stake.val("");
        $dom.u3.prop('checked', true);
        $dom.u4.prop('checked', true);
        $dom.u5.prop('checked', true);
        $dom.u6.prop('checked', true);
        $dom.Opts.val("0");
        $dom.commision.val("");
    }

    function addNew() {
        clearData();
        Custombox.open({
            target: "#eidt-modal",
            effect: "fadein",
            overlaySpeed: "200",
            overlayColor: "#36404a"
        });
    }

    function onEdit(_idx) {
        clearData();
        $.ajax({
            url: "<?php echo site_url('Cms_api/getDataById') ?>",
            data: {
                Id: _idx,
                tbl_Name: tableName
            },
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                $dom.Id.val(data.Id);
                $dom.sn.val(data.terminal_no);
                $dom.password.val(data.password);
                $dom.agent.val(data.agent_id);
                $dom.agent.selectpicker('refresh');

                $dom.credit_limit.val(data.credit_limit);
                $dom.min_stake.val(data.min_stake);
                $dom.max_stake.val(data.max_stake);

                if (data.unders & 1) $dom.u3.prop('checked', true);
                else $dom.u3.prop('checked', false);
                if (data.unders & 2) $dom.u4.prop('checked', true);
                else $dom.u4.prop('checked', false);
                if (data.unders & 4) $dom.u5.prop('checked', true);
                else $dom.u5.prop('checked', false);
                if (data.unders & 8) $dom.u6.prop('checked', true);
                else $dom.u6.prop('checked', false);

                tblOpt.ajax.url("<?php echo site_url('Cms_api/get_terminal_options') ?>" + "/" + data.Id);
                tblOpt.ajax.reload();

                Custombox.open({
                    target: "#eidt-modal",
                    effect: "fadein",
                    overlaySpeed: "200",
                    overlayColor: "#36404a"
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                swal("Error!", "", "error");
            }
        });
    }

    function onDelete(_idx) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this user information!",
            type: "error",
            showCancelButton: true,
            cancelButtonClass: 'btn-white btn-md waves-effect',
            confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
            confirmButtonText: 'Remove',
            closeOnConfirm: false
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "<?php echo site_url('Cms_api/delData') ?>",
                    data: {
                        Id: _idx,
                        tbl_Name: tableName
                    },
                    type: "POST",
                    dataType: "JSON",
                    success: function(data) {
                        swal("Remove!", "", "success");
                        tbl.ajax.reload();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        swal("Error!", "", "error");
                    }
                });
            }
        });
    }

    function onSave() {
        if($dom.agent.val() == "0") 
            return;

        Custombox.close();
        var unders = 0;
        if (document.getElementById("chk_u3").checked) unders += 1;
        if (document.getElementById("chk_u4").checked) unders += 2;
        if (document.getElementById("chk_u5").checked) unders += 4;
        if (document.getElementById("chk_u6").checked) unders += 8;

        $.ajax({
            url: "<?php echo site_url('Cms_api/edit_terminal') ?>",
            data: {
                Id: $dom.Id.val(),
                terminal_no: $dom.sn.val(),
                password: $dom.password.val(),
                agent_id: $dom.agent.val(),
                credit_limit: $dom.credit_limit.val(),
                min_stake: $dom.min_stake.val(),
                max_stake: $dom.max_stake.val(),
                unders: unders
            },
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                tbl.ajax.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                swal("Error!", "", "error");
            }
        });
    }

    function onEnable(id, status)
    {
        $.ajax({
            url: "<?php echo site_url('Cms_api/enable_terminal') ?>",
            data: {
                Id:id,
                status: status
            },
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                tbl.ajax.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                swal("Error!", "", "error");
            }
        });
    }

    function saveOption() {
        if ($dom.Id.val() == "") return;
        var opId = $dom.Opts.val();
        if (opId == "0") return;
        var commision = $dom.commision.val();

        $.ajax({
            url: "<?php echo site_url('Cms_api/edit_terminal_option') ?>",
            data: {
                terminal_id: $dom.Id.val(),
                option_id: opId,
                commision: commision
            },
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                tblOpt.ajax.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                swal("Error!", "", "error");
            }
        });
    }


    function onEnableOption(id, status) {
        $.ajax({
            url: "<?php echo site_url('Cms_api/enable_terminal_option') ?>",
            data: {
                Id: id,
                status:status
            },
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                tblOpt.ajax.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                swal("Error!", "", "error");
            }
        });
    }



    function enableAll()
    {
        $.ajax({
            url: "<?php echo site_url('Cms_api/enable_all_terminal') ?>",
            data: {},
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                tbl.ajax.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                swal("Error!", "", "error");
            }
        });
    }
    function disableAll()
    {
        $.ajax({
            url: "<?php echo site_url('Cms_api/disable_all_terminal') ?>",
            data: {},
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                tbl.ajax.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                swal("Error!", "", "error");
            }
        });
    }
</script>