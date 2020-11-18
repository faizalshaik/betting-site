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
                <div class="col-sm-4">
                    <h4 class="page-title">Fund your account</h4>
                    <p>Deposit Request to admin</p>
                </div>
            </div>



            <div class="row">
                <div class="panel panel-border panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please make deposit to the one of the below banks once admin will approve it will be credited to your wallet</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-6">
                            <div class="card-box m-b-10">
                                <div class="table-box opport-box">
                                    <div class="table-detail">
                                        <img src="<?php echo base_url('assets/images/bank.jpg'); ?> " alt="img" class="thumb-lg m-r-15">
                                    </div>
                                    <div class="table-detail">
                                        <div class="member-info">
                                            <h3 class="m-t-0"><b>Bank 2</b></h3>
                                            <p class="text-dark m-b-5"><b>Bank : </b> <span class="text-muted">Diamond bank plc</span></p>
                                            <p class="text-dark m-b-5"><b>Owner : </b> <span class="text-muted">Josystar company Nigeria Limited</span></p>
                                            <p class="text-dark m-b-5"><b>Number: </b> <span class="text-muted">0023773782</span></p>
                                        </div>
                                    </div>

                                    <!-- <div class="table-detail">
                                        <a href="#" class="btn btn-sm btn-primary waves-effect waves-light">Email</a>
                                    </div> -->
                                </div>
                            </div>


                            <div class="card-box m-b-10">
                                <div class="table-box opport-box">
                                    <div class="table-detail">
                                        <img src="<?php echo base_url('assets/images/bank.jpg'); ?> " alt="img" class="thumb-lg m-r-15">
                                    </div>
                                    <div class="table-detail">
                                        <div class="member-info">
                                            <h3 class="m-t-0"><b>Bank 2</b></h3>
                                            <p class="text-dark m-b-5"><b>Bank : </b> <span class="text-muted">Eco Bank plc</span></p>
                                            <p class="text-dark m-b-5"><b>Owner : </b> <span class="text-muted">Josystar company Nigeria Limited</span></p>
                                            <p class="text-dark m-b-5"><b>Number: </b> <span class="text-muted">2100203535</span></p>
                                        </div>
                                    </div>

                                    <!-- <div class="table-detail">
                                        <a href="#" class="btn btn-sm btn-primary waves-effect waves-light">Email</a>
                                    </div> -->
                                </div>
                            </div>
                            <div class="card-box m-b-10">
                                <div class="table-box opport-box">
                                    <div class="table-detail">
                                        <img src="<?php echo base_url('assets/images/bank.jpg'); ?> " alt="img" class="thumb-lg m-r-15">
                                    </div>
                                    <div class="table-detail">
                                        <div class="member-info">
                                            <h3 class="m-t-0"><b>Bank 3</b></h3>
                                            <p class="text-dark m-b-5"><b>Bank : </b> <span class="text-muted">Fidelity Bank plc</span></p>
                                            <p class="text-dark m-b-5"><b>Owner : </b> <span class="text-muted">Josystar company Nigeria Limited</span></p>
                                            <p class="text-dark m-b-5"><b>Number: </b> <span class="text-muted">6017412655</span></p>
                                        </div>
                                    </div>

                                    <!-- <div class="table-detail">
                                        <a href="#" class="btn btn-sm btn-primary waves-effect waves-light">Email</a>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <form class="form-horizontal" role="form">
                                <div class="card-box m-b-10">
                                    <div class="form-group has-success">
                                        <label class="col-md-4 control-label">Account No</label>
                                        <div class="col-md-8">
                                            <input class="vertical-spin form-control" type="text" data-bts-min="0" data-bts-max="1000">
                                        </div>
                                    </div>
                                    <div class="form-group has-success">
                                        <label class="col-md-4 control-label">Bank Name</label>
                                        <div class="col-md-8">
                                            <input class="vertical-spin form-control" type="text" data-bts-min="0" data-bts-max="1000">
                                        </div>
                                    </div>
                                    <div class="form-group has-success">
                                        <label class="col-md-4 control-label">Request Type</label>
                                        <div class="col-md-8">
                                            <input class="vertical-spin form-control" type="text" data-bts-min="0" data-bts-max="1000">
                                        </div>
                                    </div>
                                    <div class="form-group has-success">
                                        <label class="col-md-4 control-label">Amount</label>
                                        <div class="col-md-8">
                                            <input class="vertical-spin form-control" type="text" data-bts-min="0" data-bts-max="1000">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-4"></label>
                                        <div class="col-md-8">
                                            <button type="button" style="width:100%;" class="btn btn-primary waves-effect waves-light" onclick="onSave();">Request Deposit</button>
                                        </div>
                                    </div>



                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

        </div> <!-- container -->
    </div> <!-- content -->

    <!-- <script src="<?php echo base_url('assets/plugins/switchery/js/switchery.min.js'); ?>"></script> -->
    <script src="<?php echo base_url('assets/plugins/bootstrap-daterangepicker/daterangepicker.js'); ?>"></script>
    <script src="<?php echo base_url('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js'); ?>"></script>
    <script type="text/javascript">
    </script>