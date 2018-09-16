@extends('dashboard.layout-dashboard')

@section('content')
	<style>
		.content-group > .spn-fnt{
			font-size: 20px;
			font-weight: 600;
		}
		.content-group > .h4-pad{
			padding-left: 22px;
			font-size: 30px;
		}
		.content-group .h5-pad{
			font-size: 17px;
		}	
		.panel-cus{
			/*padding-bottom: 18px;*/
			/*height: 130px;*/
		}
		.h4-pad > i{
			font-size: 30px;
		}
		.panel-cst{
			margin-bottom: 10px;
			border-bottom:1px solid #e0dcdc;
		}
	</style>
	<div class="page-header">
		<div class="page-header-content">
			<div class="page-title">
				<h4>Dashboard </h4>
			</div>
		</div>
	</div>
	<div class="content">
		<?php
		//print_r($ctlSummary);
		//print_r($ctlSummaryByDate);
		
		//$periodeBln = date("m");
		//$periodeThn = date("Y");
		//if(isset($ctlFilterYear) && isset($ctlFilterMonth)) {
		//	$periodeBln = $ctlFilterMonth;
		//	$periodeThn = $ctlFilterYear;
		//}
		?>
		<div class="panel panel-flat">
			<div class="panel-heading">
				<h6 class="panel-title">Ringkasan Penagihan Per <?php echo tglIndo($ctlPeriode, "SHORT"); ?></h6>
				<div class="heading-elements" style="width:20%;">
					<!--
					<button type="button" class="btn btn-link daterange-ranges heading-btn text-semibold">
						<i class="icon-calendar3 position-left"></i> <span></span> <b class="caret"></b>
					</button>
					//-->
					<!--input type="text" id="tgl" class="form-control pickadate" placeholder="Tanggal&hellip;" data-date-format="dd-mm-yyyy"-->
					<div class="input-group">
            <span class="input-group-addon"><i class="icon-calendar"></i></span>
            <input type="text" id="tgl" class="form-control pickadate" placeholder="Tanggal&hellip;" data-date-format="dd-mm-yyyy">
          </div>
       	</div>
			</div>

			<div class="table-responsive">
				<table class="table table-xlg text-nowrap">
					<tbody>
						<tr>								
							<td class="col-md-4">
								<div class="media-left media-middle">
									<div id="jadwal-status"></div>
								</div>

								<div class="media-left">
									<h5 class="text-semibold no-margin"><?php echo ($ctlSummaryByDate["SUMMARY_BAYAR_JML"] + $ctlSummaryByDate["SUMMARY_TIDAK_BAYAR_JML"] + $ctlSummaryByDate["SUMMARY_TIDAK_BERTEMU_JML"] + $ctlSummaryByDate["SUMMARY_JADWAL_JML"]); ?> orang <!--<small class="text-success text-size-base"><i class="icon-arrow-up12"></i> (+2.9%)</small>--></h5>
									<span class="text-muted"><span class="status-mark border-success position-left"></span> <?php echo tglIndo($ctlPeriode,"LONG"); ?></span>
								</div>
							</td>
							
							<td class="col-md-3">
								<div class="media-left media-middle">
									<a href="#" class="btn border-primary text-primary btn-flat btn-rounded btn-xs btn-icon"><i class="icon-cash4"></i></a>
								</div>

								<div class="media-left">
									<h5 class="text-semibold no-margin">
										Rp. <?php echo number_format($ctlSummaryByDate["SUMMARY_TAGIHAN"]); ?> <small class="display-block no-margin">total tagihan per <?php echo tglIndo($ctlPeriode,"LONG"); ?></small>
									</h5>
								</div>
							</td>

							<td class="col-md-3">
								<div class="media-left media-middle">
									<a href="#" class="btn border-success text-success btn-flat btn-rounded btn-xs btn-icon"><i class="icon-cash2"></i></a>
								</div>

								<div class="media-left">
									<h5 class="text-semibold no-margin">
										Rp. <?php echo number_format($ctlSummaryByDate["SUMMARY_BAYAR_NOMINAL"]); ?> <small class="display-block no-margin">total terbayar per <?php echo tglIndo($ctlPeriode,"LONG"); ?></small>
									</h5>
								</div>
							</td>
							<!--
							<td class="col-md-3">
								<div class="media-left media-middle">
									<a href="#" class="btn border-danger text-danger btn-flat btn-rounded btn-xs btn-icon"><i class=" icon-user-cancel"></i></a>
								</div>

								<div class="media-left">
									<h5 class="text-semibold no-margin">
										<?php echo $ctlJmlTdkBayar; ?> orang <small class="display-block no-margin">tidak bayar</small>
									</h5>
								</div>
							</td>
							-->
							<!--
							<td class="col-md-3">
								<div class="media-left media-middle">
									<a href="#" class="btn border-warning text-warning btn-flat btn-rounded btn-xs btn-icon"><i class="icon-user-block"></i></a>
								</div>

								<div class="media-left">
									<h5 class="text-semibold no-margin">
										<?php echo $ctlJmlTdkBertemu; ?> orang <small class="display-block no-margin">tidak bertemu</small>
									</h5>
								</div>
							</td>
							-->
							<!--
							<td class="text-right col-md-2">
								<a href="#" class="btn bg-teal-400"><i class="icon-statistics position-left"></i> Report</a>
							</td>
							//-->
						</tr>
					</tbody>
				</table>	
			</div>
		</div>

		<!-- grafik penagihan bulan berjalan -->
		<div class="panel panel-flat">
			<div class="panel-heading">
				<h4 class="panel-title">Grafik Data Penagihan</h4>
			</div>

			<div class="panel-body">
				<!--p class="content-group">
					A column graph is a chart that uses <code>vertical</code> bars to show comparisons among categories. One axis of the chart shows the specific categories being compared, and the other axis represents a discrete value. Like all Google charts, column charts display tooltips when the user hovers over the data. By default, text labels are hidden, but can be turned on in chart settings.
				</p-->

				<div class="chart-container">
					<div class="chart" id="c3-bar-chart"></div>
				</div>
			</div>
		</div>

		<!-- grafik penagihan per collector per tgl -->
		<div class="panel panel-flat">
			<div class="panel-heading">
				<h4 class="panel-title">Grafik Data Penagihan Per Collector <?php echo tglIndo($ctlPeriode, "SHORT"); ?></h4>
			</div>

			<div class="panel-body">
				<!--p class="content-group">
					A column graph is a chart that uses <code>vertical</code> bars to show comparisons among categories. One axis of the chart shows the specific categories being compared, and the other axis represents a discrete value. Like all Google charts, column charts display tooltips when the user hovers over the data. By default, text labels are hidden, but can be turned on in chart settings.
				</p-->

				<div class="chart-container">
					<div class="chart" id="c3-bar-chart2"></div>
				</div>
			</div>
		</div>
		
		<!-- data per status per tgl -->
		<div class="panel panel-flat">
			<div class="table-responsive">
				<table class="table text-nowrap">
					<thead>						
						<!-- bayar -->
						<tr class="active border-double">
							<th colspan="3"><strong>Status Bayar Per <?php echo tglIndo($ctlPeriode, "SHORT"); ?> </strong></th>
							<th class="text-right">
								<!--span class="badge bg-success"><?php echo "xxx"; ?> orang</span-->
							</th>
						</th>
					</thead>
					<tbody>
						<tr>
							<td style="width: 50px">&nbsp;</td>
							<td style="width: 250px;">Collector</td>
							<td>Customer</td>
							<td class="text-center" style="width: 50px;"><!--i class="icon-arrow-down12"></i-->Tagihan</td>
						</tr>
						<?php
						if(isset($ctlSummaryByDate["DATA_JADWAL"]) && count($ctlSummaryByDate["DATA_JADWAL"]) > 0) {						
							foreach ($ctlSummaryByDate["DATA_JADWAL"] as $aData) {
								if($aData->{"J_STATUS"} == "ST_BAYAR" || $aData->{"J_STATUS"} == "ST_BAYAR_PARSIAL") {
									?>
									<tr>
										<td class="text-center"><i class="icon-checkmark3 text-success"></i></td>
										<td>
											<div class="media-body">
												<a href="#" class="display-inline-block text-default text-semibold letter-icon-title"><?php echo $aData->{"COLLECTOR_NAMA"}; ?></a>
												<div class="text-muted text-size-small"><span class="status-mark border-blue position-left"></span> <?php echo $aData->{"J_COLL_U_ID"}; ?></div>
											</div>
										</td>
										<td>
											<a href="#" class="text-default display-inline-block">
												<span class="text-semibold"><?php echo $aData->{"BUD_CUST_NAMA"}; ?></span>
												<span class="display-block text-muted"><?php echo $aData->{"BUD_CUST_ALAMAT"}; ?></span>
											</a>
										</td>
										<td class="text-center">
											Rp. <?php echo number_format($aData->{"J_PINJ_JUMLAH"}); ?>
										</td>
									</tr>							
									<?php
								}
							}
						}
						else {
							?>
							<tr>
								<td colspan="4" class="text-center">-- Tidak ada data --</td>
							</tr>
							<?php
						}
						?>

						<!-- tdk bayar -->
						<tr class="active border-double">
							<td colspan="3"><strong>Status Tidak Bayar Per <?php echo tglIndo($ctlPeriode, "SHORT"); ?></strong></td>
							<td class="text-right">
								<!--span class="badge bg-danger"><?php echo "xxx"; ?> orang</span-->
							</td>
						</tr>

						<tr>
							<td style="width: 50px">&nbsp;</td>
							<td style="width: 250px;">Collector</td>
							<td>Customer</td>
							<td class="text-center" style="width: 50px;">Tagihan</td>
						</tr>

						<?php
						if(isset($ctlSummaryByDate["DATA_JADWAL"]) && count($ctlSummaryByDate["DATA_JADWAL"]) > 0) {						
							foreach ($ctlSummaryByDate["DATA_JADWAL"] as $aData) {
								if($aData->{"J_STATUS"} == "ST_TIDAK_BAYAR") {
									?>
									<tr>
										<td class="text-center">
											<i class=" icon-cross2 text-danger"></i>
										</td>
										<td>
											<div class="media-body">
												<a href="#" class="display-inline-block text-default text-semibold letter-icon-title"><?php echo $aData->{"COLLECTOR_NAMA"}; ?></a>
												<div class="text-muted text-size-small"><span class="status-mark border-blue position-left"></span> <?php echo $aData->{"J_COLL_U_ID"}; ?></div>
											</div>
										</td>
										<td>
											<a href="#" class="text-default display-inline-block">
												<span class="text-semibold"><?php echo $aData->{"BUD_CUST_NAMA"}; ?></span>
												<span class="display-block text-muted"><?php echo $aData->{"BUD_CUST_ALAMAT"}; ?></span>
											</a>
										</td>
										<td class="text-center">
											Rp. <?php echo number_format($aData->{"J_PINJ_JUMLAH"}); ?>
										</td>
									</tr>
									<?php	
								}								
							}
						}
						else {
							?>
							<tr>
								<td colspan="4" class="text-center">-- Tidak ada data --</td>
							</tr>
							<?php
						}
						?>

						<!-- tdk bertemu -->
						<tr class="active border-double">
							<td colspan="3"><strong>Status Tidak Bertemu Per <?php echo tglIndo($ctlPeriode, "SHORT"); ?></strong></td>
							<td class="text-right">
								<!--span class="badge bg-warning"><?php echo "xxx"; ?> orang</span-->
							</td>
						</tr>

						<tr>
							<td style="width: 50px">&nbsp;</td>
							<td style="width: 250px;">Collector</td>
							<td>Customer</td>
							<td class="text-center" style="width: 50px;">Tagihan</td>
						</tr>

						<?php
						if(isset($ctlSummaryByDate["DATA_JADWAL"]) && count($ctlSummaryByDate["DATA_JADWAL"]) > 0) {
							foreach ($ctlSummaryByDate["DATA_JADWAL"] as $aData) {
								if($aData->{"J_STATUS"} == "ST_TIDAK_DITEMUKAN") {
									?>
									<tr>
										<td class="text-center">
											<i class=" icon-user-cancel text-warning-400"></i>
										</td>
										<td>
											<div class="media-body">
												<a href="#" class="display-inline-block text-default text-semibold letter-icon-title"><?php echo $aData->{"COLLECTOR_NAMA"}; ?></a>
												<div class="text-muted text-size-small"><span class="status-mark border-blue position-left"></span> <?php echo $aData->{"J_COLL_U_ID"}; ?></div>
											</div>
										</td>
										<td>
											<a href="#" class="text-default display-inline-block">
												<span class="text-semibold"><?php echo $aData->{"BUD_CUST_NAMA"}; ?></span>
												<span class="display-block text-muted"><?php echo $aData->{"BUD_CUST_ALAMAT"}; ?></span>
											</a>
										</td>
										<td class="text-center">
											Rp. <?php echo number_format($aData->{"J_PINJ_JUMLAH"}); ?>
										</td>
									</tr>
									<?php
								}									
							}
						}
						else {
							?>
							<tr>
								<td colspan="4" class="text-center">-- Tidak ada data --</td>
							</tr>
							<?php
						}
						?>

						<!-- dlm jadwal -->
						<tr class="active border-double">
							<td colspan="3"><strong>Status Dalam Penjadwalan Per <?php echo tglIndo($ctlPeriode, "SHORT"); ?></strong></td>
							<td class="text-right">
								<!--span class="badge bg-primary"><?php echo "xxx"; ?> orang</span-->
							</td>
						</tr>

						<tr>
							<td style="width: 50px">&nbsp;</td>
							<td style="width: 250px;">Collector</td>
							<td>Customer</td>
							<td class="text-center" style="width: 50px;">Tagihan</td>
						</tr>

						<?php
						if(isset($ctlSummaryByDate["DATA_JADWAL"]) && count($ctlSummaryByDate["DATA_JADWAL"]) > 0) {
							foreach ($ctlSummaryByDate["DATA_JADWAL"] as $aData) {
								if($aData->{"J_STATUS"} == "ST_JADWAL") {
									?>
									<tr>
										<td class="text-center">
											<i class="  icon-calendar22 text-primary-400"></i>
										</td>
										<td>
											<div class="media-body">
												<a href="#" class="display-inline-block text-default text-semibold letter-icon-title"><?php echo $aData->{"COLLECTOR_NAMA"}; ?></a>
												<div class="text-muted text-size-small"><span class="status-mark border-blue position-left"></span> <?php echo $aData->{"BUD_COLL_U_ID"}; ?></div>
											</div>
										</td>
										<td>
											<a href="#" class="text-default display-inline-block">
												<span class="text-semibold"><?php echo $aData->{"BUD_CUST_NAMA"}; ?></span>
												<span class="display-block text-muted"><?php echo $aData->{"BUD_CUST_ALAMAT"}; ?></span>
											</a>
										</td>
										<td class="text-center">
											Rp. <?php echo number_format($aData->{"J_PINJ_JUMLAH"}); ?>
										</td>
									</tr>
									<?php	
								}								
							}
						}
						else {
							?>
							<tr>
								<td colspan="4" class="text-center">-- Tidak ada data --</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
		<!--		
		<div class="panel panel-flat">
			<div class="panel-heading">
				<h4 class="panel-title">Grafik Data Penagihan</h4>
			</div>

			<div class="panel-body">
				<p class="content-group">
					A column graph is a chart that uses <code>vertical</code> bars to show comparisons among categories. One axis of the chart shows the specific categories being compared, and the other axis represents a discrete value. Like all Google charts, column charts display tooltips when the user hovers over the data. By default, text labels are hidden, but can be turned on in chart settings.
				</p>

				<div class="chart-container">
					<div class="chart" id="google-column"></div>
				</div>
			</div>
		</div>	
		//-->	
	</div>
	<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/visualization/c3/c3.min.js"></script>
	<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/visualization/d3/d3.min.js"></script>
	<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/visualization/d3/d3_tooltip.js"></script>
	<!--script type="text/javascript" src="https://www.google.com/jsapi"></script-->

	<script type="text/javascript">
	  $('.pickadate').pickadate({
	    format: 'dd-mm-yyyy',
	    formatSubmit: 'yyyy-mm-dd',
	    onSet: function() {
      	//console.log('set new date');
			},
			onClose: function() {
				var selectedDate = $("#tgl").pickadate('picker').get('select', 'yyyy-mm-dd');
				//console.log("selectedDate : " + selectedDate);
				window.location = "<?php echo asset_url(); ?>/dashboard?periode=" + selectedDate;
			}
	  });

	  $("#tgl").pickadate("picker").set('select', '<?php echo $ctlPeriode_dmY; ?>');
	  //toastr.info("Periode : <?php echo $ctlPeriode; ?>");

	  function reloadPage() {
	    var filterPeriode = $("#filterPeriode").val();
	    window.location = "<?php echo asset_url(); ?>/dashboard?periodeChart=" + filterPeriode;
	  }

	  // Tickets status donut chart
    // ------------------------------
    // Initialize chart
    jadwalStatusDonut("#jadwal-status", 42);

    // Chart setup
    function jadwalStatusDonut(element, size) {
        // Basic setup
        // ------------------------------
        // Add data set
        var data = [
            {
                "status": "Terbayar",
                "icon": "<i class='status-mark border-success-400 position-left'></i>",
                "value": <?php echo $ctlSummaryByDate["SUMMARY_BAYAR_JML"]; ?>,
                "color": "#08ad60"
            }, {
                "status": "Tidak Terbayar",
                "icon": "<i class='status-mark border-danger-300 position-left'></i>",
                "value": <?php echo $ctlSummaryByDate["SUMMARY_TIDAK_BAYAR_JML"]; ?>,
                "color": "#dd4108"
            }, {
                "status": "Tidak Bertemu",
                "icon": "<i class='status-mark border-warning-300 position-left'></i>",
                "value": <?php echo $ctlSummaryByDate["SUMMARY_TIDAK_BERTEMU_JML"]; ?>,
                "color": "#f29509"
            }, {
                "status": "Dalam Jadwal",
                "icon": "<i class='status-mark border-primary-300 position-left'></i>",
                "value": <?php echo $ctlSummaryByDate["SUMMARY_JADWAL_JML"]; ?>,
                "color": "#166cf7"
            }
        ];

        // Main variables
        var d3Container = d3.select(element),
            distance = 2, // reserve 2px space for mouseover arc moving
            radius = (size/2) - distance,
            sum = d3.sum(data, function(d) { return d.value; })

        // Tooltip
        // ------------------------------
        var tip = d3.tip()
            .attr('class', 'd3-tip')
            .offset([-10, 0])
            .direction('e')
            .html(function (d) {
                return "<ul class='list-unstyled mb-5'>" +
                    "<li>" + "<div class='text-size-base mb-5 mt-5'>" + d.data.icon + d.data.status + "</div>" + "</li>" +
                    "<li>" + "Total: &nbsp;" + "<span class='text-semibold pull-right'>" + d.value + "</span>" + "</li>" +
                    "<li>" + "Share: &nbsp;" + "<span class='text-semibold pull-right'>" + (100 / (sum / d.value)).toFixed(2) + "%" + "</span>" + "</li>" +
                "</ul>";
            })

        // Create chart
        // ------------------------------
        // Add svg element
        var container = d3Container.append("svg").call(tip);
        
        // Add SVG group
        var svg = container
            .attr("width", size)
            .attr("height", size)
            .append("g")
            .attr("transform", "translate(" + (size / 2) + "," + (size / 2) + ")");  

        // Construct chart layout
        // ------------------------------
        // Pie
        var pie = d3.layout.pie()
            .sort(null)
            .startAngle(Math.PI)
            .endAngle(3 * Math.PI)
            .value(function (d) { 
                return d.value;
            }); 

        // Arc
        var arc = d3.svg.arc()
            .outerRadius(radius)
            .innerRadius(radius / 2);

        //
        // Append chart elements
        //
        // Group chart elements
        var arcGroup = svg.selectAll(".d3-arc")
            .data(pie(data))
            .enter()
            .append("g") 
            .attr("class", "d3-arc")
            .style('stroke', '#fff')
            .style('cursor', 'pointer');
        
        // Append path
        var arcPath = arcGroup
            .append("path")
            .style("fill", function (d) { return d.data.color; });

        // Add tooltip
        arcPath
            .on('mouseover', function (d, i) {
                // Transition on mouseover
                d3.select(this)
                .transition()
                    .duration(500)
                    .ease('elastic')
                    .attr('transform', function (d) {
                        d.midAngle = ((d.endAngle - d.startAngle) / 2) + d.startAngle;
                        var x = Math.sin(d.midAngle) * distance;
                        var y = -Math.cos(d.midAngle) * distance;
                        return 'translate(' + x + ',' + y + ')';
                    });
            })

            .on("mousemove", function (d) {
                
                // Show tooltip on mousemove
                tip.show(d)
                    .style("top", (d3.event.pageY - 40) + "px")
                    .style("left", (d3.event.pageX + 30) + "px");
            })

            .on('mouseout', function (d, i) {

                // Mouseout transition
                d3.select(this)
                .transition()
                    .duration(500)
                    .ease('bounce')
                    .attr('transform', 'translate(0,0)');

                // Hide tooltip
                tip.hide(d);
            });

        // Animate chart on load
        arcPath
            .transition()
            .delay(function(d, i) { return i * 500; })
            .duration(500)
            .attrTween("d", function(d) {
                var interpolate = d3.interpolate(d.startAngle,d.endAngle);
                return function(t) {
                    d.endAngle = interpolate(t);
                    return arc(d);  
                }; 
            });
    }

    // Google visualization
    //--------------------
    // Initialize chart
    /*
		google.load("visualization", "1", {packages:["corechart"]});
		google.setOnLoadCallback(drawColumn);

		// Chart settings
		function drawColumn() {
		  // Data
		  var data = google.visualization.arrayToDataTable([
		    ['Tgl', 'Terjadwal', 'Bayar'],
		    ['2004',  1000,      400],
		    ['2005',  1170,      460],
		    ['2006',  660,       1120],
		    ['2007',  1030,      540]
		  ]);

		  // Options
		  var options_column = {
		    fontName: 'Roboto',
		    height: 400,
		    fontSize: 12,
		    chartArea: {
		        left: '5%',
		        width: '90%',
		        height: 350
		    },
		    tooltip: {
		        textStyle: {
		            fontName: 'Roboto',
		            fontSize: 13
		        }
		    },
		    vAxis: {
		        title: 'Penagihan Sesuai Status',
		        titleTextStyle: {
		            fontSize: 13,
		            italic: false
		        },
		        gridlines:{
		            color: '#e5e5e5',
		            count: 10
		        },
		        minValue: 0
		    },
		    legend: {
		        position: 'top',
		        alignment: 'center',
		        textStyle: {
		            fontSize: 12
		        }
		    }
		  };

		  // Draw chart
		  var column = new google.visualization.ColumnChart($('#google-column')[0]);
		  column.draw(data, options_column);
		}

		// Resize chart
		// ------------------------------
		$(function () {
		  // Resize chart on sidebar width change and window resize
		  $(window).on('resize', resize);
		  $(".sidebar-control").on('click', resize);

		  // Resize function
		  function resize() {
				drawColumn();
		  }
		});
		*/
		// C3 Bar chart
    // ------------------------------
    // Generate chart
    /*
    var barChart = c3.generate({
        bindto: '#c3-bar-chart',
        size: { height: 400 },
        data: {
          columns: [
            ['Terjadwal', 30, 200, 100, 400, 150, 250, 30, 200, 100, 400, 150, 250],
            ['Bayar', 130, 100, 140, 200, 150, 50, 130, 100, 140, 200, 150, 50],
            ['Tidak Bayar', 130, 100, 140, 200, 150, 50, 130, 100, 140, 200, 150, 50]
          ],
          type: 'bar'
        },
        color: {
          pattern: ['#2196F3', '#FF9800', '#4CAF50']
        },
        bar: {
          width: {
          	ratio: 0.5
          }
        },
        grid: {
          y: {
          	show: true
          }
        }
    });
		*/
		/*
		var barChart = c3.generate({
	    bindto: '#c3-bar-chart',
	    data: {
	     	x: 'x',
				//xFormat: '%Y%m%d', // 'xFormat' can be used as custom format of 'x'
				columns: [
					["x","2016-09-01", "2016-09-02", "2016-09-03", "2016-09-04", "2016-09-05", "2016-09-06"], 
	        ["Label 1", 35.0, 35.0, 35.0, 35.0, 35.0, 35.0], 
	        ["Label 2", 124.0, 124.0, 124.0, 124.0, 124.0, 124.0]
				]
	    },
	    axis: {
	      x: {
	        show: true,
	        type: 'category',
	        tick: {
	        	rotate:60
	        },
	        categories: ['a', 'b', 'c', 'e']
	      }
	    }
		});
		setTimeout(function () {
		  chart.load({
		  	columns: [
		    	["x", "2015-10-01", "2015-11-01", "2015-12-01", "2016-01-01", "2016-02-01", "2016-03-01", "2016-04-01"],
		      ["Label 1", 2854.0, 4509.0, 5895.0, 6932.0, 4143.0, 3076.0, 1880.0, 1454.0, 1098.0, 1016.0, 1004.0, 1048.0],
		      ["Label 2", 8680.0, 15090.0, 25079.0, 23746.0, 18096.0, 16058.0, 17610.0, 9269.0, 2550.0, 2852.0, 2232.0, 3720.0]
		  	]
		  });
		}, 3000);
		*/
		var barChart = c3.generate({
			bindto: '#c3-bar-chart',
      size: { height: 400 },
		  data: {
		    json: [
		    	<?php
		    	for($i=0; $i<count($ctlSummary); $i++) {
		    		$tgl = $ctlSummary[$i]["TGL_FORMATTED"];
		    		$arrTgl = explode("-", $tgl)
		    		?>
		    		{Tgl: '<?php echo  $arrTgl[0]." ".$arrTgl[1]; ?>', Tagihan: <?php echo $ctlSummary[$i]["SUMMARY_TAGIHAN"]; ?>, Terbayar: <?php echo $ctlSummary[$i]["SUMMARY_BAYAR_NOMINAL"]; ?>},
		    		<?php
		    	}
		    	?>		    	
		    ],
		    keys: {
		      x: 'Tgl', // it's possible to specify 'x' when category axis
		      value: ['Tagihan', 'Terbayar']
		    },
		    type: 'bar'
		  },
		  axis: {
		    x: {
		     type: 'category'
		    }
		  },
		  bar: {
        width: {
        	ratio: 0.5
        }
      },
      grid: {
        y: {
        	show: true
        }
      }
		});

		var barChart2 = c3.generate({
			bindto: '#c3-bar-chart2',
      size: { height: 400 },
		  data: {
		    json: [
		    	<?php
		    	for($i=0; $i<count($ctlSummaryByCollector); $i++) {
		    		?>
		    		{Collector: '<?php echo $ctlSummaryByCollector[$i]->{"U_NAMA"}; ?>', Tagihan: <?php echo $ctlSummaryByCollector[$i]->{"JUMLAH_TAGIHAN"}; ?>, Terbayar: <?php echo $ctlSummaryByCollector[$i]->{"JUMLAH_BAYAR"}; ?>},
		    		<?php
		    	}
		    	?>		    	
		    ],
		    keys: {
		      x: 'Collector', // it's possible to specify 'x' when category axis
		      value: ['Tagihan', 'Terbayar']
		    },
		    type: 'bar'
		  },
		  axis: {
		    x: {
		     type: 'category'
		    }
		  },
		  bar: {
        width: {
        	ratio: 0.5
        }
      },
      grid: {
        y: {
        	show: true
        }
      }
		});

    // Change data
    /*
    setTimeout(function () {
      barChart.load({
        columns: [
        	['data3', 130, -150, 200, 300, -200, 100]
        ]
      });
    }, 6000);
		*/
	</script>
@stop