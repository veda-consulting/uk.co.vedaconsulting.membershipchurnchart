{literal}
<style>
.axis path,
.axis line {
  fill: none;
  shape-rendering: crispEdges;
}

.axis text {
  font-family: sans-serif;
  font-size: 11px;
}

.dot {
  stroke: #000;
}

.legend {
  padding: 5px;
  font: 10px sans-serif;
  background: yellow;
  box-shadow: 2px 2px 1px #888;
}

.panel1-body a, .panel1-body a:link, .panel1-body a:visited {
  color: #ffffff;
}

.modal-dialog {
  font-family: Georgia, "Times New Roman", Times, serif;
}

.modal-header {
  background-color: #428bca;
  color: #fff;
  font-size: 16px;
}
.crm-container [data-toggle="collapse"]::before,
.crm-container .collapsed[data-toggle="collapse"]::before {
  content: none;
}
</style>
{/literal}

<div class="container-fluid">

    <div style="display:none;" id="spinner">
      <i class="fa fa-refresh fa-spin btn-lg"></i> Refreshing data... Please wait for the page to reload.
    </div>


    <div class="row" id="row_Churnchart">
      <div class="col-lg-12">
        <div class="panel1 panel1-primary">
          <div class="accordion" id="Parent_Churnchar">
            <div class="accordion-group">
              <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#Parent_Churnchar" href="#hrefChurnchar">
                  <div class="panel1-heading" id="heading_Churnchar">
                    <h4 class="panel1-title"><i class="fa fa-bar-chart-o"></i> Churn By Month (<span class="summaryYears"></span>)</h4>
                  </div>
                </a>
              </div>
              <div id="hrefChurnchar" class="accordion-body open">
                <div class="accordion-inner">
                  <div class="panel1-body">
                        <div id="churnChartDiv"></div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="row" id="row_Membershipsummary">
      <div class="col-lg-12">
        <div class="panel1 panel1-primary">
            <div class="accordion" id="Parent_Membershipsummary">
              <div class="accordion-group">
                <div class="accordion-heading">
                  <a class="accordion-toggle" data-toggle="collapse" data-parent="#Parent_Membershipsummary" href="#hrefMembershipsummary">
                    <div class="panel1-heading" id="heading_Membershipsummary">
                      <h4 class="panel1-title"><i class="fa fa-bar-chart-o"></i> Membership Summary by Month (<span class="summaryYears"></span>)</h4>
                    </div>
                  </a>
                </div>
                <div id="hrefMembershipsummary" class="accordion-body open">
                  <div class="accordion-inner">
                    <div class="panel1-body">
                          <div id="MainChart"></div>
                      </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>

    <div class="row" id="row_Filters">
      {if $smarty.get.snippet neq ''}
      <div class="col-lg-12">
      {else}
      <div class="col-lg-9">
      {/if}
        <div class="panel1 panel1-primary">

            <div class="accordion" id="Parent_Filters">
              <div class="accordion-group">
              <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#Parent_Filters" href="#hrefFilters">
                  <div class="panel1-heading" id="heading_Filter">
                    <h4 class="panel1-title"><i class="fa fa-filter"></i> Filters</h4>
                  </div>
                </a>
              </div>
              <div id="hrefFilters" class="accordion-body collapse">
                <div class="accordion-inner">
                  <div class="panel1-body">
                        <div id="Filters">

                          <div class="row">
                            <div class="col-lg-3">
                              <label><strong>Years</strong></label>
                            </div>
                            <div class="col-lg-9">
                              <select id="from" required="required" >
                                <!--<option value=""> -- Select -- </option>-->
                                {$startYearRange}
                              </select>
                              &nbsp;<label><strong>To</strong></label>&nbsp;
                              <select id="to" required="required" >
                                <!--<option value=""> -- Select -- </option>-->
                                {$endYearRange}
                              </select>
                            </div>
                          </div>
                          <br />
                          <!-- Filter by membership type   -->
                          <div class="row">
                            <div class="col-lg-3">
                              <label><strong>Membership Types</strong></label><br />
                              <input type="checkbox" id="select_deselect_all" value="1" checked/>
                              <label for="select_deselect_all">Select / Deselect All</label>
                            </div>
                            <div class="col-lg-9">
                              <div class="row">
                              <div class="checkbox checkbox-success">
                                {foreach from=$memTypes item=mTypes key=mTypeId}
                                <div class="col-xs-6">
                                  <input type="checkbox" name="membership_type_id" id="mem_type_{$mTypeId}" value="{$mTypeId}" checked/>
                                  <label for="mem_type_{$mTypeId}">{$mTypes}</label>
                                </div>
                                {/foreach}
                              </div>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
            </div>
        </div>
      </div>
      <div class="col-lg-3" {if $smarty.get.snippet neq ''} style="display:none;"{/if}>
        <div class="panel1 panel1-primary">
            <div class="accordion" id="Parent_Settings">
              <div class="accordion-group">
              <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#Parent_Settings" href="#hrefSettings">
                  <div class="panel1-heading" id="heading_Settings">
                    <h4 class="panel1-title"><i class="fa fa-gear"></i> Settings</h4>
                  </div>
                </a>
              </div>
              <div id="hrefSettings" class="accordion-body collapse">
                <div class="accordion-inner">
                  <div class="panel1-body">
                    <div class="col-lg-12">
                      <p class="text-center">
                        <a class="btn btn-success" href="{crmURL p="civicrm/admin/setting/membershipchurnchart" q="reset=1"}"><i class="fa fa-wrench"></i> Settings</a>
                      </p>
                      <p class="text-center">
                        <a class="btn btn-success" id="refreshData" href="#"><i class="fa fa-refresh"></i> Refresh Data</a>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
        </div>
      </div>
    </div>

</div>

{literal}
<script type="text/javascript">

var mainData = {/literal}{$chartData}{literal};
var currentYear = {/literal}{$currentYear}{literal};
var allStatus = {/literal}{$allStatuses}{literal};
var aMinChurn = {/literal}{$minChurn}{literal};

var churn = aMinChurn[currentYear];
var churnstatus = ['Churn'];
var churnChartElementID = 'churnChartDiv';

// Refresh data
cj('#refreshData').click(function(){
  showSpinner();

  // Call the API to refresh chart data
  CRM.api3('Membershipchurnchart', 'preparechurntable', {
    "sequential": 1
  }).done(function(result) {
    // Refresh page
    var url = CRM.url('civicrm/membership/membershipchurnchart');
    window.location.href = url;
  });
});

// Start year filter
cj("#from").change(function(){
  refreshChart();
});

// End year filter
cj("#to").change(function(){
  refreshChart();
});

cj("#select_deselect_all").click(function () {
  if(this.checked){
    cj('input:checkbox[name=membership_type_id]').each(function(){
      this.checked = true;
    });
  } else {
    cj('input:checkbox[name=membership_type_id]').each(function(){
      this.checked = false;
    });
  }
  refreshChart();
});

// Membership type filter
cj('input:checkbox[name=membership_type_id]').change(function() {
  var checkedBoxes = cj('input:checkbox[name=membership_type_id]:checked').length;
  var allCheckBoxes = cj('input:checkbox[name=membership_type_id]').length;
  if(checkedBoxes == allCheckBoxes){
    cj('#select_deselect_all').attr('checked','checked');
  }else{
    cj('#select_deselect_all').removeAttr('checked');
  }
  refreshChart();
});

// Refresh chart if any filters are changed
function refreshChart() {
  data = getChartData();
  buildChart(data, allStatus, churn);
  buildChart(data, churnstatus, -5, churnChartElementID);
}

function getChartData() {

  // Main chart data
  var mainData = {/literal}{$chartData}{literal};

  var fromYear = cj("#from").val();
  var toYear = cj("#to").val();
  var data = [];
  var tempdata = {};
  var selectedYears = [];
  var brought_forward_stats = 0;
  var current_stats = 0;
  var joined_stats = 0;
  var resigned_stats = 0;
  var rejoined_stats = 0;
  var churn_stats = 0;

  if (mainData.length === 0) {
    return;
  }

  // Prepare data needed to render the chart
  // Loop through start and end year from the filters
  for (var i = fromYear; i <= toYear; i++) {

    selectedYears.push(i);

    // Get all checked membership types from the filters
    cj('input:checkbox[name=membership_type_id]').each(function()
    {
      if(cj(this).is(':checked')) {
        var memTypeId = cj(this).val();

        // Check if there is data for the membership type
        if (memTypeId in mainData[i]) {
          // Loop through all the data available for the membership type
          cj.each( mainData[i][memTypeId], function( key, value ) {

            var date = value['month'] + '/' + value['year'];

            // Check if data row is already there for current month/year
            // if yes, add to the values
            if (date in tempdata) {
              tempdata[date]['Brought_Forward'] = parseInt(tempdata[date]['Brought_Forward']) + parseInt(value['Brought_Forward']);
              tempdata[date]['Current'] = parseInt(tempdata[date]['Current']) + parseInt(value['Current']);
              tempdata[date]['Joined'] = parseInt(tempdata[date]['Joined']) + parseInt(value['Joined']);
              tempdata[date]['Resigned'] = parseInt(tempdata[date]['Resigned']) + parseInt(value['Resigned']);
              tempdata[date]['Rejoined'] = parseInt(tempdata[date]['Rejoined']) + parseInt(value['Rejoined']);
              //tempdata[date]['Churn'] = parseFloat(tempdata[date]['Churn']) + parseFloat(value['Churn']);
            }
            // if no data row available for current month/year, add a new row
            else {
              tempdata[date] = value;
            }

            // Stats
            // brought_forward_stats = brought_forward_stats + parseInt(value['Brought_Forward']);
            // current_stats = current_stats + parseInt(value['Current']);
            // joined_stats = joined_stats + parseInt(value['Joined']);
            // resigned_stats = resigned_stats + parseInt(value['Resigned']);
            // rejoined_stats = rejoined_stats + parseInt(value['Rejoined']);
            // churn_stats = churn_stats + parseFloat(value['Churn']);

          });
        }
      }
    });

    // Clean up the data needed to render the chart
    // by removing key from the array, as we dont need the key
    cj.each( tempdata, function( key, value ) {
      var churn = (parseInt(value['Joined']) + parseInt(value['Rejoined']) - parseInt(value['Resigned'])) / parseInt(value['Brought_Forward']) * 100;
      churn = churn.toFixed(2);
      value['Churn'] = churn;
      data.push(value);
    });
  }

  // Display stats
  // cj('#brought_forward_stats').text(brought_forward_stats);
  // cj('#current_stats').text(current_stats);
  // cj('#joined_stats').text(joined_stats);
  // cj('#resigned_stats').text(resigned_stats);
  // cj('#rejoined_stats').text(rejoined_stats);
  // cj('#churn_stats').text(churn_stats + ' %');

  // Get number of years between the start and end year filters
  var yearsCount = 0;
  for (var year in selectedYears) {
    if (selectedYears.hasOwnProperty(year)) {
      yearsCount++;
    }
  }

  // Display selected years next to each panel's heading
  var selectedYearsStr = '';
  if(yearsCount > 1) {
    lastYearKey =  parseInt(yearsCount) - 1;
    selectedYearsStr = selectedYears[0] + ' to ' + selectedYears[lastYearKey];
  } else {
    selectedYearsStr = selectedYears[0];
  }
  cj('.summaryYears').text(selectedYearsStr);

  return data;
}

// Get chart dayta
data = getChartData();

if (data !== undefined) {
  // Render membership summary chart
  buildChart(data, allStatus, churn);
  // Render churn chart
  buildChart(data, churnstatus, -5, churnChartElementID);
}

/*
 * Function to build bar chart using D3 charts
 */
function buildChart(data, allStatus, minChurn = 1, churnId = null) {

  var metricElementWeek = '#MainChart';

  //seperate chart for Churn rate
  if (churnId) {
   var churnChartElement = '#'+churnId;
   metricElementWeek = churnChartElement;
   // console.log(data);
  }

  cj(metricElementWeek).html('');

  var margin = {top: 20, right: 160, bottom: 60, left: 60};
  var rowWidth = parseInt(d3.select('#row_Churnchart').style('width'), 10);

  var width = rowWidth - margin.left - margin.right,
      height = 600 - margin.top - margin.bottom,
      padding = 100; // space around the chart, not including labels

  var svg = d3.select(metricElementWeek)
    .append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
    .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

  var dataset = d3.layout.stack()(allStatus.map(function(status) {
      return data.map(function(d) {
        return {
          date: d['date'],
          x: d['month'] +  '/' + d['year'],
          y: +d[status],
          brought_forward: +d['Brought_Forward'],
          current: +d['Current'],
          joined: +d['Joined'],
          resigned: +d['Resigned'],
          rejoined: +d['Rejoined'],
          churn: +d['Churn'],
        };
      });
  }));

  // Set x, y and colors
  var x = d3.scale.ordinal()
    .domain(dataset[0].map(function(d) { return d.x; }))
    .rangeRoundBands([0, width], 0.02);

  //temp Fix to get minChurn from data array. minchurn already taken as var from PHP.
  var minChurnFromData = d3.min(dataset, function(d) {  return d3.min(d, function(d) { return d.y0 + d.y; });  });
  if (minChurnFromData < minChurn) {
    minChurn = minChurnFromData;
  }

  //For churn rate chart positive values might be less than 1 so, keep atleast y axis up to 0-1;
  var maxChurnFromData = d3.max(dataset, function(d) {  return d3.max(d, function(d) { return d.y0 + d.y; });  });
  maxChurn = 5;
  if (maxChurnFromData > maxChurn) {
    maxChurn = maxChurnFromData;
  }

  var y = d3.scale.linear()
    .domain([minChurn, maxChurn])
    .range([height, 0]);

  // var colors = ["#b33040", '#F58876', "#87A9CE", "#5C7F79", "#9467bd", "#c5b0d5", "#4FCAB7"];
  var colors = ["#d95f02","#66a61e","#7570b3","#e7298a"];
  if (churnId) {
    colors = ["#b33040"];
    postiveNegativeColor = ["#b33040", '#0AAD18'];
  }

  // Define and draw axes
  var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left");

  var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

  svg.append("g")
    .attr("class", "y axis")
    .call(yAxis);

  svg.append("g")
    .attr("id", "x-axis")
    .attr("class", "x axis")
    //.attr("class", "xaxis")
    .attr("transform", "translate(0," + height + ")")
    .call(xAxis)
    .selectAll("text")
    .attr("y", 0)
    .attr("x", 9)
    .attr("dy", ".35em")
    .attr("transform", "rotate(90)")
    .style("text-anchor", "start");

  // Create groups for each series, rects for each segment
  // don't need to fill churn rate chart colors, we display based on value not by segment.
  if(churnId){
    var groups = svg.selectAll("g.cost")
    .data(dataset)
    .enter().append("g")
    .attr("class", "cost");
    // .style("fill", function(d, i) { return colors[i]; });
  }
  else{

  var groups = svg.selectAll("g.cost")
    .data(dataset)
    .enter().append("g")
    .attr("class", "cost")
    .style("fill", function(d, i) { return colors[i]; });
  }

  var rect = groups.selectAll("rect")
    .data(function(d) { return d; })
    .enter()
    .append("rect")
    .attr("x", function(d) { return x(d.x); })
    .attr("y", function(d) { if(churnId && d.y < 0) { return y(0); } else { return y(d.y0 + d.y);} })
    .attr("height", function(d) { return Math.abs(y(d.y0) - y(d.y0 + d.y)); })
    .attr("width", x.rangeBand())
    .on("mouseover", function (d) { showPopover.call(this, d, churnId); })
    .on("mouseout",  function (d) { removePopovers(); })

  //Positive / Negative  colors for churn rate chart.
  if(churnId){
    groups.selectAll("rect").style("fill", function(d, i) { return d.y < 0 ? postiveNegativeColor[0] : postiveNegativeColor[1]; });
  }

  // Draw legend
  var legend = svg.selectAll(".legend")
    .data(colors)
    .enter().append("g")
    .attr("class", "legend")
    .attr("transform", function(d, i) { return "translate(30," + i * 19 + ")"; });

  legend.append("rect")
    .attr("x", width - 18)
    .attr("width", 18)
    .attr("height", 18)
    .style("fill", function(d, i) {return colors[i];});

  legend.append("text")
    .attr("x", width + 5)
    .attr("y", 9)
    .attr("dy", ".35em")
    .style("text-anchor", "start")
    .text(function(d, i) {
      return allStatus[i];
  });
}

/*
 * Function to show hover popover element
 */
function showPopover (d, churnId) {
  cj(this).popover({
    title: '<b>' + d.date + '</b>',
    placement: 'auto top',
    container: 'body',
    trigger: 'manual',
    html : true,
    content: function() {
      if (churnId) {
        return "Churn: " + d.churn + " %";
      } else {
        return "Brought Forward: " + d.brought_forward
           + "<br />Current: " + d.current
           + "<br/>Joined: " + d.joined
           + "<br/>Resigned: " + d.resigned
           + "<br/>Rejoined: " + d.rejoined
           ;
        //"Brought Forward: " + d.brought_forward
      }
    }
  });
  cj(this).popover('show')
}

/*
 * Function to remove hover popover element
 */
function removePopovers () {
  cj('.popover').each(function() {
    cj(this).remove();
  });
}

function type(d) {
  d.date = parseDate(d.date);
  causes.forEach(function(c) { d[c] = +d[c]; });
  return d;
}

/*
 * Function to show font awesome spinner
 */
function showSpinner() {
  message = cj('#spinner').html();
  BootstrapDialog.show({
    title: 'Refresh Data',
    message: message ,
    closable: true
  });
}
</script>
{/literal}
