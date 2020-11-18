<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FlexAR">
    <meta name="author" content="Lance Bunch">

    <title>Bet2Star</title>
    <?php if ($kind == 'table') { ?>
        <link href="<?php echo base_url('assets/plugins/datatables/jquery.dataTables.min.css'); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url('assets/plugins/datatables/buttons.bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url('assets/plugins/datatables/responsive.bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url('assets/plugins/datatables/scroller.bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url('assets/plugins/datatables/dataTables.bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url('assets/plugins/datatables/fixedColumns.dataTables.min.css'); ?>" rel="stylesheet" type="text/css" />
    <? } ?>


    <link href="<?php echo base_url('assets/plugins/bootstrap-sweetalert/sweet-alert.css'); ?>" rel="stylesheet" type="text/css">

    <!-- Plugins css-->
    <link href="<?php echo base_url('assets/plugins/bootstrap-tagsinput/css/bootstrap-tagsinput.css'); ?>" rel="stylesheet" />
    <link href="<?php echo base_url('assets/plugins/switchery/css/switchery.min.css'); ?>" rel="stylesheet" />
    <link href="<?php echo base_url('assets/plugins/multiselect/css/multi-select.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/plugins/select2/css/select2.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/plugins/bootstrap-select/css/bootstrap-select.min.css'); ?>" rel="stylesheet" />
    <link href="<?php echo base_url('assets/plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css'); ?>" rel="stylesheet" />

    <link href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/css/core.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/css/components.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/css/icons.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/css/pages.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/css/responsive.css'); ?>" rel="stylesheet" type="text/css" />


    <script src="<?php echo base_url('assets/js/modernizr.min.js'); ?>"></script>
    <!-- jQuery  -->
    <script src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/detect.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/fastclick.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/jquery.slimscroll.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/jquery.blockUI.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/waves.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/wow.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/jquery.nicescroll.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/jquery.scrollTo.min.js'); ?>"></script>



    <?php if ($kind == 'table') { ?>
        <script src="<?php echo base_url('assets/plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/plugins/datatables/dataTables.bootstrap.js'); ?>"></script>
        <script src="<?php echo base_url('assets/plugins/datatables/dataTables.buttons.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/plugins/datatables/buttons.bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/plugins/datatables/jszip.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/plugins/datatables/pdfmake.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/plugins/datatables/vfs_fonts.js'); ?>"></script>
        <script src="<?php echo base_url('assets/plugins/datatables/buttons.html5.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/plugins/datatables/buttons.print.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/plugins/datatables/dataTables.responsive.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/plugins/datatables/responsive.bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/plugins/datatables/dataTables.colVis.js'); ?>"></script>
        <script src="<?php echo base_url('assets/plugins/datatables/dataTables.fixedColumns.min.js'); ?>"></script>

    <? } ?>

</head>

<body class="fixed-left">
    <!-- Begin page -->
    <div id="wrapper">
        <!-- Top Bar Start -->
        <div class="topbar">

            <!-- LOGO -->
            <div class="topbar-left">
                <div class="text-center">
                    <a href="<?php echo base_url() . 'Cms/dashboard' ?>" class="logo"><i class="fa fa-star-half-o"></i><span>BET2Star<i class="fa fa-star-half-o"></i></span></a>
                    <!-- Image Logo here -->
                    <!--<a href="index.html" class="logo">-->
                    <!--<i class="icon-c-logo"> <img src="assets/images/logo_sm.png" height="42"/> </i>-->
                    <!--<span><img src="assets/images/logo_light.png" height="20"/></span>-->
                    <!--</a>-->
                </div>
            </div>

            <!-- Button mobile view to collapse sidebar menu -->
            <div class="navbar navbar-default" role="navigation">
                <div class="container">
                    <div class="">
                        <div class="pull-left">
                            <button class="button-menu-mobile open-left waves-effect waves-light">
                                <i class="md md-menu"></i>
                            </button>
                            <span class="clearfix"></span>
                        </div>

                        <ul class="nav navbar-nav hidden-xs">
                            <li class="hidden-xs m-t-20">
                                <i class="ti-alarm-clock text-custom" id="curtime">2019-07-15 00:00:00</i>
                            </li>                        
                        </ul>

                        <ul class="nav navbar-nav navbar-right pull-right">
                            <li class="dropdown top-menu-item-xs open">
                                <a href="#" data-target="#" class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="true">
                                    <i class="icon-bell"></i> <span class="badge badge-xs badge-danger">0</span></a>
                            </li>
                            <li class="hidden-xs">
                                <a href="#" id="btn-fullscreen" class="waves-effect waves-light"><i class="icon-size-fullscreen"></i></a>
                            </li>
                            <li class="dropdown top-menu-item-xs">
                                <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><img src="<?php echo base_url('assets/images/def_avatar.jpg'); ?>" alt="user-img" class="img-circle"> </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo base_url() . 'Cms/personal_details'; ?>"><i class="ti-user m-r-10 text-custom"></i> Profile</a></li>
                                    <li><a href="<?php echo base_url() . 'Cms/logout'; ?>"><i class="ti-power-off m-r-10 text-pink"></i> Logout</a></li>
                                </ul>
                            </li>
                            <li class="hidden-xs">
                                <a href="#" class="waves-effect waves-light"><i class="md md-account-balance text-warning"></i><span class="text-info">1000</span></a>
                            </li>
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>
        <script>
            var cdDt = new Date(Date.parse("<?php echo date('Y-m-d H:i:s'); ?>", "yyyy-MM-dd HH:mm:ss"));
            function timerFunction() 
            {
                cdDt.setSeconds(cdDt.getSeconds() + 1);
                let formatted_date = cdDt.getFullYear() + "-" + (cdDt.getMonth() + 1) + "-" + cdDt.getDate() + " " + cdDt.getHours() + ":" + cdDt.getMinutes() + ":" + cdDt.getSeconds();
                document.getElementById("curtime").innerText = formatted_date;
            }
            setInterval(timerFunction, 1000);
        </script>

        <!-- Top Bar End -->
        <div class="left side-menu">
            <div class="sidebar-inner slimscrollleft">
                <!--- Divider -->
                <div id="sidebar-menu">
                    <ul>
                        <li class="text-muted menu-title">Navigation</li>
                        <li class="">
                            <a href="<?php echo base_url() . 'Cms/stake'; ?>" class="waves-effect <?php if ($uri == 'stake') echo 'active'; ?>">
                                <i class="ti-server"></i><span>Stake</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="<?php echo base_url() . 'Cms/fund_account'; ?>" class="waves-effect <?php if ($uri == 'fund_account') echo 'active'; ?>">
                                <i class="ti-server"></i><span>Fund Account</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="<?php echo base_url() . 'Cms/bet_history'; ?>" class="waves-effect <?php if ($uri == 'bet_history') echo 'active'; ?>">
                                <i class="ti-server"></i><span>Bet History</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="<?php echo base_url() . 'Cms/win_list'; ?>" class="waves-effect <?php if ($uri == 'win_list') echo 'active'; ?>">
                                <i class="ti-server"></i><span>Win List</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="<?php echo base_url() . 'Cms/bet_result'; ?>" class="waves-effect <?php if ($uri == 'bet_result') echo 'active'; ?>">
                                <i class="ti-server"></i><span>Bet Result</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="<?php echo base_url() . 'Cms/personal_details'; ?>" class="waves-effect <?php if ($uri == 'personal_details') echo 'active'; ?>">
                                <i class="ti-server"></i><span>Personal Details</span>
                            </a>
                        </li>

                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <!-- Left Sidebar End -->
        <!-- ========== Left Sidebar Start ========== -->