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
                <h4 class="page-title">Stake</h4>
                <div class="text-center">
                    <p class="text-info"><b>Week <?php echo $curWeekNo; ?>:</b><span class="text-muted">(<?php if($curWeek!=null) echo $curWeek->start_at.' ~ '.$curWeek->close_at; ?>)</span></p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <div class="panel panel-border panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">Select Sort:</h3>
                        </div>
                        <div class="panel-body">
                            <div class="radio radio-custom">
                                <input type="radio" name="radio_type" id="radio_type_nap_perm" value="Nap/Perm" checked onclick="onSelectSort();">
                                <label for="radio2">Nap Or Perm</label>
                            </div>
                            <div class="radio radio-primary">
                                <input type="radio" name="radio_type" id="radio_type_grouping" value="Grouping" onclick="onSelectSort();">
                                <label for="radio2">Grouping</label>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-border panel-custom">
                        <div class="panel-heading">
                            <h3 class="panel-title">Select Unders:</h3>
                        </div>
                        <div class="panel-body">
                            <div class="checkbox checkbox-custom">
                                <input id="chk_u3" type="checkbox" checked onclick="onSelectUnder(3);">
                                <label for="chk_u3">U3</label>
                            </div>
                            <div class="checkbox checkbox-custom">
                                <input id="chk_u4" type="checkbox" checked onclick="onSelectUnder(4);">
                                <label for="chk_u4">U4</label>
                            </div>
                            <div class="checkbox checkbox-custom">
                                <input id="chk_u5" type="checkbox" checked onclick="onSelectUnder(5);">
                                <label for="chk_u5">U5</label>
                            </div>
                            <div class="checkbox checkbox-custom">
                                <input id="chk_u6" type="checkbox" checked onclick="onSelectUnder(6);">
                                <label for="chk_u6">U6</label>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-border panel-success" id="groupMgr" hidden>
                        <div class="panel-heading">
                            <div class="btn-group pull-right">
                                <a href="javascript:void(0);" onclick="onAddNewGroup();" class="list-group-item b-0"><span class="ion-plus-round text-info"></span></a>
                            </div>
                            <h3 class="panel-title">Groups:</h3>
                        </div>

                        <div class="panel-body">
                            <form class="form-horizontal" role="form">
                                <?php $colors = array("info", "warning", "success", "pink", "primary", "danger");
                                for ($i = 0; $i < 6; $i++) { ?>
                                    <div class="input-group m-t-10 p-t-4" id="<?php echo 'group_' . $i; ?>">
                                        <a href="javascript:void(0);" onclick="onSelectGroup(<?php echo $i; ?>);">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn waves-effect waves-light btn-custom text-right"><i class="fa fa-circle <?php echo 'text-' . $colors[$i]; ?>"><?php echo ' ' . chr(0x41 + $i) . ' :'; ?></i></button>
                                            </div>
                                            <div class="input-group-btn">
                                                <input class="vertical-spin  <?php echo 'bg-' . $colors[$i]; ?>" type="text" id="<?php echo 'grp_under_' . $i; ?>" value="1" readonly data-bts-min="1" data-bts-max="6" onchange="onChangeGroupUnder();">
                                            </div>
                                            <div class="input-group-btn">
                                                <button type="button" onclick="onRemoveGroup();" class="btn btn-custom waves-effect waves-light" id="<?php echo 'grp_close_' . $i; ?>" style="overflow: hidden; position: relative;"><span class="md md-clear text-danger"></span></button>
                                            </div>
                                        </a>
                                    </div>
                                <?php } ?>

                            </form>

                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="panel panel-border panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">Select Games:</h3>
                        </div>
                        <div class="panel-body" id="games">
                            <?php
                            $lines = count($games) / 4;
                            if (count($games) % 4) $lines++;
                            $iGame = 0;
                            for ($iLine = 0; $iLine < $lines; $iLine++) {
                                ?>
                                <div class="row">
                                    <?php for ($i = 0; $i < 4; $i++) {
                                        if ($iGame >= count($games)) break;
                                        $game = $games[$iGame];
                                        $iGame++; ?>
                                        <div class="col-xs-3 m-t-20">
                                            <button type="button" style="width:100%;" id="<?php echo 'game_' . $game->game_no; ?>" class="btn btn-lg btn-default btn-custom waves-effect waves-light" onclick="onSlelectGame(<?php echo $game->game_no; ?>);"><?php echo $game->game_no; ?></button>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="panel panel-border panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">Select Odd:</h3>
                        </div>
                        <div class="panel-body">
                            <?php for ($i = 0; $i < count($options); $i++) { ?>
                                <div class="radio radio-custom">
                                    <input type="radio" name="radio_opt" value="<?php echo $options[$i]->name; ?>" <?php if ($i == 0) echo 'checked'; ?>>
                                    <label for="radio2"><?php echo $options[$i]->name; ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="alert alert-success fade in m-b-10" id="status">
                    </div>
                    <div class="panel panel-border panel-custom">
                        <div class="panel-heading">
                            <h3 class="panel-title">Stake Amount:</h3>
                        </div>
                        <div class="panel-body">
                            <div class="input-group m-t-10">
                                <div class="input-group-btn">
                                    <button type="button" class="btn waves-effect waves-light btn-custom text-right"><i class="fa fa-money text-warning"><b> Stake:</b></i></button>
                                </div>
                                <div class="input-group-btn">
                                    <input class="vertical-spin bg-warning" type="text" id="stake" value="1" data-bts-min="1" data-bts-max="10000">
                                </div>
                            </div>

                            <div class="form-group has-success">
                                <button type="button" style="width:100%;" class="btn btn-lg  btn-primary waves-effect waves-light m-t-20" onclick="onStake();">
                                    <i class="md md-done">Stake</i>
                                </button>
                            </div>
                            <div class="form-group has-success">
                                <button type="button" style="width:100%;" class="btn btn-lg  btn-success waves-effect waves-light">
                                    <i class="md md-print">Print</i>
                                </button>
                            </div>
                            <div class="alert alert-danger alert-dismissable" id="alert_message" hidden>
                                <button type="button" class="close" onclick="document.getElementById('alert_message').style.display='none';">×</button>
                                <strong id="alert_title">Oh snap!</strong>
                                <p id="alert_text">Change a few things up and try submitting again.</p>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-color panel-warning">
                        <div class="panel-heading">
                            <h3 class="panel-title">Coupon Info:</h3>
                        </div>
                        <div class="panel-body">
                            <p>Max. on 3=<span class="text-danger">NGN 5000</span></p>
                            <p>Max. on 3=<span class="text-danger">NGN 5000</span></p>
                            <p>Max. on 3=<span class="text-danger">NGN 5000</span></p>
                            <p>Max. on 3=<span class="text-danger">NGN 5000</span></p>
                        </div>
                    </div>

                </div>
            </div>

        </div> <!-- container -->
    </div> <!-- content -->


    <!-- <script src="<?php echo base_url('assets/plugins/switchery/js/switchery.min.js'); ?>"></script> -->
    <script type="text/javascript">
        var _bGroup = false;
        var _nGroup = 0;
        var _iSelectedGroup = -1;
        var _nUnders = 3;

        var _colorList = ["info", "warning", "success", "pink", "primary", "danger"];

        function clearGame(ele) {
            for (let i = 0; i < _colorList.length; i++) {
                ele.classList.remove("btn-" + _colorList[i]);
            }
            ele.classList.add("btn-custom");
        }

        function clearGames(grpIdex) {
            var clr = "btn-" + _colorList[grpIdex];
            var eles = document.getElementById("games").getElementsByTagName("button");
            for (let i = 0; i < eles.length; i++) {
                if (eles[i].classList.contains(clr)) {
                    eles[i].classList.remove(clr);
                    eles[i].classList.add("btn-custom");
                }
            }
        }

        function clearAllGames() {
            var eles = document.getElementById("games").getElementsByTagName("button");
            for (let i = 0; i < eles.length; i++) {
                clearGame(eles[i]);
            }
        }

        function clearUnders() {
            document.getElementById('chk_u3').checked = false;
            document.getElementById('chk_u4').checked = false;
            document.getElementById('chk_u5').checked = false;
            document.getElementById('chk_u6').checked = false;
            _nUnders = 3;
        }

        function selectUnder(index) {
            if (index == 3) document.getElementById('chk_u3').checked = true;
            else if (index == 4) document.getElementById('chk_u4').checked = true;
            else if (index == 5) document.getElementById('chk_u5').checked = true;
            else if (index == 6) document.getElementById('chk_u6').checked = true;
        }

        function refreshGroups() {
            for (let i = 0; i < 6; i++) {
                var strId = "group_" + i;
                if (i < _nGroup) {
                    document.getElementById(strId).style.display = "block";
                    // if (i == _nGroup - 1)
                    //     document.getElementById("grp_close_" + i).style.display = "block";
                    // else
                    //     document.getElementById("grp_close_" + i).style.display = "none";
                    if (i == _iSelectedGroup)
                        document.getElementById(strId).style.backgroundColor = "antiquewhite";
                    else
                        document.getElementById(strId).style.backgroundColor = "";
                } else
                    document.getElementById(strId).style.display = "none";
            }
        }


        function onSelectSort() {
            clearAllGames();
            clearUnders();
            selectUnder(3);
            _nUnders = 3;

            var bNapPerm = document.getElementById('radio_type_nap_perm').checked;
            if (bNapPerm) {
                _bGroup = false;
                document.getElementById('groupMgr').style.display = "none";
            } else {
                _bGroup = true;
                document.getElementById('groupMgr').style.display = "block";
                _nGroup = 0;
                refreshGroups();
            }

            refreshStatus();
        }

        function onSelectUnder(index) {
            if (_bGroup) {
                clearAllGames();
                clearUnders();
                selectUnder(index);
                _nUnders = index;
                _nGroup = 0;
                _iSelectedGroup = -1;
                refreshGroups();
            } else {
                _nUnders = 3;
                if (document.getElementById('chk_u3').checked) _nUnders = 3;
                if (document.getElementById('chk_u4').checked) _nUnders = 4;
                if (document.getElementById('chk_u5').checked) _nUnders = 5;
                if (document.getElementById('chk_u6').checked) _nUnders = 6;
            }
            refreshStatus();
        }

        function onAddNewGroup() {
            //get total under count
            var totalUnder = 0;
            var strId;
            if (_nGroup >= 6) {
                refreshGroups();
                return;
            }

            for (let i = 0; i < _nGroup; i++) {
                strId = "grp_under_" + i;
                totalUnder += Number(document.getElementById(strId).value);
            }
            if (totalUnder >= _nUnders) return;
            _nGroup++;
            refreshGroups();
            refreshStatus();
        }

        function onRemoveGroup() {
            if (_nGroup > 0) {
                _nGroup--;
                clearGames(_nGroup);
                _iSelectedGroup = -1;
            }
            refreshGroups();
            refreshStatus();
        }

        function onSelectGroup(index) {
            _iSelectedGroup = index;
            refreshGroups();
        }

        function onSlelectGame(gameNo) {
            var ele = document.getElementById("game_" + gameNo);
            if (_bGroup == false) {
                if (ele.classList.contains("btn-custom")) {
                    ele.classList.remove("btn-custom");
                    ele.classList.add("btn-info");
                } else
                    clearGame(ele);
            } else {
                if (_iSelectedGroup >= 0 && ele.classList.contains("btn-custom")) {
                    ele.classList.remove("btn-custom");
                    ele.classList.add("btn-" + _colorList[_iSelectedGroup]);
                } else clearGame(ele);
            }

            refreshStatus();
        }

        function refreshStatus() {
            if (_bGroup == false) {
                var strContent = "";
                let unders = [];

                if (document.getElementById('chk_u3').checked) unders.push("U3");
                if (document.getElementById('chk_u4').checked) {
                    if (unders.length == 0) unders.push("U4")
                    else unders.push("4");
                }
                if (document.getElementById('chk_u5').checked) {
                    if (unders.length == 0) unders.push("U5")
                    else unders.push("5");
                }
                if (document.getElementById('chk_u6').checked) {
                    if (unders.length == 0) unders.push("U6")
                    else unders.push("6");
                }
                strContent = "<p><b>" + unders.join(", ");
                strContent += " : </b></p>";

                let games = [];
                var eles = document.getElementById("games").getElementsByTagName("button");
                for (let i = 0; i < eles.length; i++) {
                    if (eles[i].classList.contains("btn-custom") == false)
                        games.push("<span class='badge badge-primary'>" + eles[i].innerText + "</span>");
                }
                strContent += "<p>" + games.join(" ") + "</p>";
                document.getElementById("status").innerHTML = strContent;
            } else {
                var strContent = "";
                var strGrp = "";
                var eles = document.getElementById("games").getElementsByTagName("button");
                for (let i = 0; i < _nGroup; i++) {
                    strGrp = "<p><b>" + String.fromCharCode(0x41 + i) + "(" + document.getElementById("grp_under_" + i).value + "): </b>";
                    let games = [];
                    for (let ii = 0; ii < eles.length; ii++) {
                        if (eles[ii].classList.contains("btn-" + _colorList[i]) == true)
                            games.push("<span class='badge badge-" + _colorList[i] + "'>" + eles[ii].innerText + "</span>");
                    }
                    strGrp += games.join(" ") + "</p>";
                    strContent += strGrp;
                }
                document.getElementById("status").innerHTML = strContent;
            }
        }

        function onChangeGroupUnder() {
            refreshStatus();
        }
        function alertMessage(title, txt)
        {
            document.getElementById("alert_title").innerText = title;
            document.getElementById("alert_text").innerText = txt;
            document.getElementById("alert_message").style.display = "block";
        }


        function onStake() {
            //unders
            var unders = [];
            var grpUnders = [];
            var under = 3;
            if (document.getElementById('chk_u3').checked) unders.push(3);
            if (document.getElementById('chk_u4').checked) {
                unders.push(4);
                under = 4;
            }
            if (document.getElementById('chk_u5').checked) {
                unders.push(5);
                under = 5;
            }
            if (document.getElementById('chk_u6').checked) {
                unders.push(6);
                under = 6;
            }

            //optionId
            var optionName = "";
            var eles = document.getElementsByName('radio_opt');
            for (let i = 0; i < eles.length; i++) {
                if (eles[i].checked) {
                    optionName = eles[i].value;
                    break;
                }
            }
            //alert(optionName);

            //collect games;
            var gameList = [
                [],
                [],
                [],
                [],
                [],
                []
            ];
            eles = document.getElementById("games").getElementsByTagName("button");
            for (let i = 0; i < 6; i++) {
                var games = [];
                for (let ii = 0; ii < eles.length; ii++) {
                    if (eles[ii].classList.contains("btn-" + _colorList[i]) == true)
                        games.push(eles[ii].innerText);
                }
                gameList[i] = games;
            }

            if (_bGroup == false) {
                //check under and game count
                if (gameList[0].length < under) {
                    alertMessage("Staking..", "Please select more games");
                    return;
                }
            } else {
                //check under count 
                let totalUnder = 0;
                for(let i =0; i < _nGroup; i++)
                {
                    grpUnders.push(Number(document.getElementById("grp_under_" + i).value));
                    totalUnder += grpUnders[i];
                }
                if(under != totalUnder)
                {
                    alertMessage("Staking..", "Please make sure the same unders.");
                    return;
                }

                //check under and game count
                for (let i = 0; i < _nGroup; i++) {
                    if (gameList[i].length < grpUnders[i]) {
                        alertMessage("Staking..", "Please select more games for group " + String.fromCharCode(0x41 + i));
                        return;
                    }
                }
            }

            //make betting.
            var bet = {};
            bet.under = unders;
            bet.option = optionName;
            bet.stake_amount = document.getElementById('stake').value;

            if(_bGroup)
            {
                var grpGameList = [];
                bet.type = "Group";
                for(let i=0; i<_nGroup; i++)
                {
                    grpGameList.push({under:[grpUnders[i]], list:gameList[i]});
                }
                bet.gamelist = grpGameList;
            }
            else
            {
                bet.type = "Nap/Perm";
                bet.gamelist = gameList[0];
            }

            $.ajax({
                url: "<?php echo site_url('User_api/make_bet') ?>",
                data: {
                    bets: [bet]
                },                
                type: "POST",
                dataType: "JSON",
                success: function(data) {
                    //console.log(data);
                    if(data.result==1)
                    {
                        swal("Bet success!", data.data.bets[0].message, "success"); 
                        tbl2.ajax.reload();
                    }
                    else
                    {
                        swal("Bet Error!", data.message, "error");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    swal("Error!", "", "error");
                }
            });            
            //alert(JSON.stringify(bet));
            


        }

    </script>