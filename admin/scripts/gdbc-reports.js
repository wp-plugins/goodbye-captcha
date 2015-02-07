jQuery( document ).ready(function($) {

    if ($("#flot-container").length > 0)
        initializeModulesPage();
    else
        initializeDashboard();

    function initializeDashboard()
    {
        displayDashBoardAttemptsChart();
        //displayEasyCharts();
        displayLatestAttemptsTable();
        displayLocationsOnMap();
        displayPercentagePieChart();
        //displayTopIpAttempts();
        displayTotalAttemptsPerModule()
    }

    function initializeModulesPage()
    {
        displayModulesChart();
        displayModulesTables();
    }

    function displayModulesTables()
    {
        $("[id^='wid-id-']").each(function() {
            var moduleId = $(this).attr('id').substr(7);
            displayModuleTable(moduleId, 1, 'CreatedDate');
        });
    }

    function displayModuleTable(moduleId, pageNumber, $orderBy)
    {
        if (moduleId == null || moduleId < 1 || moduleId >= 100)
            return;
        var ajaxData = {};
        ajaxData['action']       = 'getModuleData';
        ajaxData['moduleId']     = moduleId;
        ajaxData['pageNumber']   = pageNumber;
        ajaxData['orderBy']      = $orderBy;
        ajaxData['reportsNonce'] = GdbcAdmin.reportsNonce;
        $.ajax({
            type : "post",
            cache: false,
            dataType : "json",
            url : GdbcAdmin.ajaxUrl,
            data : ajaxData,
            success: function(response) {
                $.each(response, function(prop, moduleData) {
                    if ('PaginationInfo' == prop) {
                        if (moduleData == 0) {
                            $('#wid-id-' + moduleId + ' table tbody').html('<td style="text-align: center; padding-top: 15px">No records found</td>');
                            return;
                        }
                        $('#mp-' + moduleId).empty();
                        $('#mp-' + moduleId).append(showPagination(eval(moduleData[0]),eval(moduleData[1])));
                        $('#wid-id-' + moduleId + ' .module-pagination li[class!="disabled"] a').on('click', function(){
                            var newPage = $(this).html();
                            if (newPage == '...' || newPage == pageNumber)
                                return;
                            else if (newPage == 'Next')
                                newPage = eval(pageNumber) + 1;
                            else if (newPage == 'Prev')
                                newPage = eval(pageNumber) - 1;

                            var modId = $(this).parent().parent().parent().attr('id').substr(3);
                            displayModuleTable(modId , newPage, '');
                            return false;
                        });
                    }
                    if ('ModuleDataHeader' == prop)
                        displayModuleTableHeader('wid-id-' + moduleId, moduleData);
                    if ('ModuleDataRows' == prop)
                        displayModuleTableBody('wid-id-' +moduleId, moduleData);
                });
            }
        });
    }

    function displayModuleTableHeader(containerId, dataValues)
    {
        var tableHeader = $('#' + containerId + ' table thead tr');
        if (tableHeader.length == 0)
            return;
        tableHeader.empty();
        $.each(dataValues, function(k,v){
            var headerCell = $('<th>' + v + '</th>');
            tableHeader.append(headerCell);
        });
    }

    function displayModuleTableBody(containerId, data)
    {
        var tableBody = $('#' + containerId + ' table tbody');
        if (tableBody.length === 0)
            return;
        tableBody.empty();
        $(data).each(function(kArray, vArray){
            var tableRow = $('<tr></tr>');
            var isIpBlocked = vArray[0];
            if (isIpBlocked == null || isIpBlocked == undefined)
                isIpBlocked = 0;
            vArray.splice(0,1);
            var i = 0;
            var ip = null;
            $.each(vArray, function(key, value){
                ++i;
                if (value == null)
                    value = '';
                if (i == vArray.length) {
                    tableRow.append($('<td></td>').append(generateBlockIcon(isIpBlocked)).append(value));
                    ip = value;
                }
                else {
                    tableRow.append($('<td>' + value + '</td>'));
                }
            });
            var blockIpElement = $('<td class="text-center"></td>');
            blockIpElement.append(createBlockIpButtonGroup(ip));
            tableRow.append(blockIpElement);
            tableBody.append(tableRow);
        });
    }

    function showPagination(pageNumber, totalPages)
    {
        var ulContainer = $('<ul class="pagination pagination-sm"></ul>');
        var firstPageLi = $('<li><a>1</a></li>');
        var lastPageLi = $('<li><a>' + totalPages + '</a>');
        var previousLi = $('<li><a>Prev</a></li>');
        var nextLi = $('<li><a>Next</a></li>');
        var separatorLi1 = $('<li><a>...</a></li>');
        var separatorLi2 = $('<li><a>...</a></li>');

        var currentPageLi = $('<li class="active"><a>' + pageNumber + '</a></li>');
        if (totalPages == 1) {
            firstPageLi = null;
            lastPageLi = null;
            previousLi.addClass('disabled');
            nextLi.addClass('disabled');
        }
        else if (pageNumber == 1) {
            previousLi.addClass('disabled');
            firstPageLi = null;
        }
        else if (pageNumber == totalPages) {
            nextLi.addClass('disabled');
            lastPageLi = null;
        }

        var numberOfAdditionalPages = 1; // = 2; two additional pages in the left, two additional pages in the right (if available)
        var prevPagesArray = new Array();
        var nextPagesArray = new Array();
        for (var i=0; i < numberOfAdditionalPages; i++ ){
            if (pageNumber - i > 2)
                prevPagesArray[i] = $('<li><a>' + (pageNumber - i - 1) + '</a></li>');

            if ((pageNumber + i + 1) < totalPages)
                nextPagesArray[i] = $('<li><a>' + (pageNumber + i + 1) + '</a></li>');
        }
        ulContainer.append(previousLi);
        if (firstPageLi != null) {
            ulContainer.append(firstPageLi);
            if (prevPagesArray.length > 0)
            {
                var firstPageNumber = firstPageLi.text();
                var nextPageNumber = prevPagesArray[0].text();
                if ((eval(nextPageNumber) -  eval(firstPageNumber)) > 1)
                    ulContainer.append(separatorLi1);
            }
        }
        for(var i=numberOfAdditionalPages-1; i >= 0; i--)
            ulContainer.append(prevPagesArray[i]);
        ulContainer.append(currentPageLi);
        for(i=0; i<numberOfAdditionalPages; i++)
            ulContainer.append(nextPagesArray[i]);
        if (lastPageLi != null) {
            if (nextPagesArray.length > 0)
            {
                var lastPageNumber = lastPageLi.text();
                var prevPageNumber = nextPagesArray[nextPagesArray.length - 1].text();
                if ((eval(lastPageNumber) -  eval(prevPageNumber)) > 1)
                    ulContainer.append(separatorLi2);
            }
            ulContainer.append(lastPageLi);
        }
        ulContainer.append(nextLi);

        return ulContainer;
    }

    function displayModulesChart()
    {
        var toggles  = $("#modules-chart");
        var target = $("#flot-container");

        var data = [];

        var options = {
            grid : {
                hoverable : true
            },
            tooltip : true,
            tooltipOpts : {
                content: '%s: %y',
                //dateFormat: '%b %y',
                defaultTheme : false
            },
            legend: {show: false},
            xaxis : {
                mode : "time"
            },
            yaxes : {
                tickFormatter : function(val) {
                    return "$" + val;
                },
                max : 1200
            }

        };


        var ajaxData = {};
        ajaxData['action']      = 'getDisplayableAttemptsArray';
        ajaxData['reportsNonce'] = GdbcAdmin.reportsNonce;
        $.ajax({
            type : "post",
            cache: false,
            dataType : "json",
            url : GdbcAdmin.ajaxUrl,
            data : ajaxData,
            success: function(response){
                $.each(response, function(prop, modulesData){
                    if (prop === 'ModulesDescriptionArray' && $("#flot-container").length) {
                        var i = 0;
                        $.each(modulesData, function(key, value){
                            var label = $('<label class="checkbox" for="gra-' + i + '"></label>');
                            var input = $('<input type="checkbox" checked="checked" id="gra-' + i + '" name="gra-' + i + '">');
                            var italic = $('<i></i>');
                            label.append(input, italic, value);
                            $(".inline-group").append(label);
                            i++;
                        });
                    } else if (prop === 'ModulesAttemptsArray' && $("#flot-container").length) {

                        var colorArray = ['#931313', '#638167', '#65596B', '#60747C', '#B09B5B', '#3276B1', '#C0C0C0', '#FDDC9A', '#575FB5', '#57B599', '#46CC41', '#C93A24'];
                        var i = 0;
                        $.each(modulesData, function(key, value){
                            var $graphObj = {
                                label : '%x - ' + $('#gra-'+i).parent().text() + ' attempts',
                                data : value,
                                color : colorArray[i],
                                lines : {
                                    show : true,
                                    lineWidth : 3
                                },
                                points : {
                                    show : true
                                }
                            };
                            data[i] = $graphObj;
                            i++;
                        });
                    }

                });
                toggles.find(':checkbox').on('change', function() {
                    plotNow();
                });
                plotNow()
            }
        });



        var plot2 = null;

        function plotNow() {
            var d = [];
            toggles.find(':checkbox').each(function() {
                if ($(this).is(':checked')) {
                    d.push(data[$(this).attr("name").substr(4, 1)]);
                }
            });
            if (d.length > 0) {
                if (plot2) {
                    plot2.setData(d);
                    plot2.draw();
                } else {
                    plot2 = $.plot(target, d, options);
                }
            }

        }
    }

    function displayDashBoardAttemptsChart() {
        var ajaxData = {};
        ajaxData['action']      = 'retrieveInitialDashboardData';
        ajaxData['reportsNonce'] = GdbcAdmin.reportsNonce;
        $.ajax({
            type : "post",
            cache: false,
            dataType : "json",
            url : GdbcAdmin.ajaxUrl,
            data : ajaxData,
            success: function(response){
                $.each(response, function(prop, chartData){
                    if (prop === 'ChartDataArray' && $("#chart-container").length) {
                        var chartArray = [];
                        $.each(chartData, function(key, value){
                            chartArray.push([key, value]);
                        });
                        displayAttemptsChart("#chart-container", chartArray);
                    }
                });
            }
        });
    }

    function displayAttemptsChart(placeHolderId, data) {

        var options = {
            xaxis : {
                mode : "time",
                tickLength : 5
            },
            yaxis : {
                mode : "number",
                tickFormatter: suffixFormatter
            },
            series : {
                lines : {
                    show : true,
                    lineWidth : 1,
                    fill : true,
                    fillColor : {
                        colors : [{
                            opacity : 0.1
                        }, {
                            opacity : 0.15
                        }]
                    }
                },
                shadowSize : 0
            },
            selection : {
                mode : "x"
            },
            grid : {
                hoverable : true,
                clickable : true,
                tickColor : "#efefef",
                borderWidth : 0,
                borderColor : "#efefef"
            },
            tooltip : true,
            tooltipOpts : {
                content : "<span>%y</span> attempts on <b>%x</b>",
                dateFormat : "%b %0d, %y",
                defaultTheme : false
            },
            colors : ["#6595b4"]
        };

        $.plot($(placeHolderId), [data], options);
    }

    function displayLatestAttemptsTable()
    {
        if ($('#latest-attempts-table').length == 0)
            return;

        var ajaxData = {};
        ajaxData['action']      = 'retrieveLatestAttemptsTable';
        ajaxData['reportsNonce'] = GdbcAdmin.reportsNonce;
        var dataHeaderArr = null;
        $.ajax({
            type : "post",
            cache: false,
            dataType : "json",
            url : GdbcAdmin.ajaxUrl,
            data : ajaxData,
            success: function(response){
                $.each(response, function(prop, data){
                    if (prop == 'LatestAttemptsArrayHeader') {
                        dataHeaderArr = data;
                        $('#latest-attempts-table thead tr').empty();
                        $.each(data, function(key, value){
                            var cell = $('<th>' + value + '</th>');
                            $('#latest-attempts-table thead tr').append(cell);
                        });
                        $('#latest-attempts-table thead tr').append($('<th></th>'));
                    } else if (prop == 'LatestAttemptsArray') {
                        if (data.length == 0)
                        {
                            $('#latest-attempts-table tbody').append('<tr><td colspan="6" class="text-center">No records found</td>');
                            return;
                        }
                        $('#latest-attempts-table tbody').empty();
                        var i = 0;
                        $.each(data, function(){
                            var j = 0;
                            var row = $('<tr></tr>');
                            var ip = 'N\A';
                            $.each(dataHeaderArr, function(k){
                                ++j;
                                var cell = $('<td></td>');
                                if (j === 3){
                                    if (data[i]['IsIpBlocked'] == 0) {
                                        cell.append('<i class="glyphicon" style="margin-right: 5px"></i>');
                                    } else {
                                        cell.append('<i class="glyphicon glyphicon-minus-sign icon-danger" style="margin-right: 5px"></i>');
                                    }
                                    ip = data[i][k];
                                }
                                cell.append(data[i][k]);
                                row.append(cell);
                            });
                            var blockCell = $('<td></td>');
                            blockCell.append(createBlockIpButtonGroup(ip));
                            row.append(blockCell);
                            $('#latest-attempts-table tbody').append(row);
                            ++i;
                        });
                    }
                });
            }
        });
    }

    function displayLocationsOnMap()
    {
        if ($('#vector-map').length == 0)
            return;

        $('#vector-map').vectorMap({
            map : 'world_mill_en',
            backgroundColor : '#fff',
            regionStyle : {
                initial : {
                    fill : '#c4c4c4'
                },
                hover : {
                    "fill-opacity" : 1
                }
            },
            series : {
                regions : [{
                    values : attemptsCountryArray,
                    scale : ['#85a8b6', '#4d7686'],
                    normalizeFunction : 'polynomial'
                }]
            },
            onRegionLabelShow : function(e, el, code) {
                if ( typeof attemptsCountryArray[code] == 'undefined') {
                    e.preventDefault();
                } else {
                    var countryLbl = attemptsCountryArray[code];
                    var attemptLbl = ' total attempts';
                    if (countryLbl == 1)
                        attemptLbl = ' attempt';
                    el.html(el.html() + ': ' + countryLbl + attemptLbl);
                }
            }
        });
    }

    function displayPercentagePieChart()
    {
        if ($('#gdbc-stats').length == 0)
            return;

        var ajaxData = {};
        ajaxData['action']       = 'getModuleStatsPercentage';
        ajaxData['reportsNonce'] = GdbcAdmin.reportsNonce;
        $.ajax({
            type : "post",
            cache: false,
            dataType : "json",
            url : GdbcAdmin.ajaxUrl,
            data : ajaxData,
            success: function(response) {
                var data_pie = new Array();
                $.each(response, function(prop, moduleData) {
                    if ('PercentageArray' == prop) {
                        var i = 0;
                        $.each(moduleData, function(k, v){
                            data_pie[i++] = {
                                value : v[1],
                                label : v[0]
                            }
                        });
                        Morris.Donut({
                            element: 'gdbc-stats',
                            data: data_pie,
                            formatter: function (x) {
                                return x + "%"
                            }
                        });
                    }
                });
            }
        });
    }

    function generateBlockIcon(type)
    {
        if (type == 0) //block
            return $('<i class="glyphicon" style="margin-right: 5px"></i>');
        else if (type == 1) //unblock
            return $('<i class="glyphicon glyphicon-minus-sign icon-danger" style="margin-right: 5px"></i>');
        else
            return null;
    }

    function displayTotalAttemptsPerModule()
    {
        if ($('#gdbc-top-ip-attempts').length == 0)
            return;

        var ajaxData = {};
        ajaxData['action']       = 'getTotalAttemptsPerModule';
        ajaxData['reportsNonce'] = GdbcAdmin.reportsNonce;
        $.ajax({
            type : "post",
            cache: false,
            dataType : "json",
            url : GdbcAdmin.ajaxUrl,
            data : ajaxData,
            success: function(response) {
                $.each(response, function(prop, moduleData) {
                    if ('TopAttemptsArrayPerModule' == prop) {
                        $('#gdbc-top-ip-attempts table tbody').empty();
                        if (moduleData == 0)
                            $('#gdbc-top-ip-attempts table tbody').append($('<tr><td colspan="4" class="text-center">No records found</tr>'));
                        else {
                            $.each(moduleData, function (k, v) {
                                var tableRow = $('<tr></tr>');
                                tableRow.append($('<td>' + k + '</td>'));
                                tableRow.append($('<td>' + v + '</td>'));
                                $('#gdbc-top-ip-attempts table tbody').append(tableRow);
                            });
                        }
                    }
                });
            }
        });
    }

    function displayTopIpAttempts()
    {
        if ($('#gdbc-top-ip-attempts').length == 0)
            return;

        var ajaxData = {};
        ajaxData['action']       = 'getTopIpAttempts';
        ajaxData['reportsNonce'] = GdbcAdmin.reportsNonce;
        $.ajax({
            type : "post",
            cache: false,
            dataType : "json",
            url : GdbcAdmin.ajaxUrl,
            data : ajaxData,
            success: function(response) {
                $.each(response, function(prop, moduleData) {
                    if ('TopAttemptsArray' == prop) {
                        $('#gdbc-top-ip-attempts table tbody').empty();
                        if (moduleData == 0)
                            $('#gdbc-top-ip-attempts table tbody').append($('<tr><td colspan="4" class="text-center">No records found</tr>'));
                        else {
                            $.each(moduleData, function (k, v) {
                                var tableRow = $('<tr></tr>');
                                var tdIpCell = $('<td></td>');
                                tdIpCell.append(generateBlockIcon(eval(v[3]))).append(v[0]);
                                tableRow.append(tdIpCell);
                                tableRow.append($('<td>' + v[1] + '</td>'));
                                tableRow.append($('<td>' + v[2] + '</td>'));
                                var buttonTd = $('<td></td>');
                                buttonTd.append(createBlockIpButtonGroup(v[0]));
                                tableRow.append(buttonTd);
                                $('#gdbc-top-ip-attempts table tbody').append(tableRow);
                            });
                        }
                    }
                });
            }
        });
    }

    function createBlockIpButtonGroup(ip)
    {
        var group = $('<div class="btn-group display-inline pull-right text-align-left hidden-tablet"></div>');
        group.append(createBlockLinkButton());
        group.append(createBlockLinkOptions(ip));
        return group;
    }

    function createBlockLinkButton()
    {
        return $('<button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle">' +
        '<i class="glyphicon glyphicon-remove-circle icon-primary"></i>' +
        '</button>');
    }

    $("#gdbc-top-ip-attempts, #latest-attempts-table, div[id^='wid-id-'].module table tbody").on('click', 'a', function(){
        var classAttr = $(this).attr('class');
        if (!classAttr)
            return void(0);

        if (classAttr.lastIndexOf('block-') === 0) {
            var ip = classAttr.substr(6);
            manageIp(ip, 1);
        } else if (classAttr.lastIndexOf('unblock-') === 0) {
            var ip = classAttr.substr(8);
            manageIp(ip, 0);
        }
        return void(0);
    });

    function createBlockLinkOptions(ip)
    {
        var blockIpLink = '<a href="javascript:void(0);" class="block-' +ip+ '"> <i class="glyphicon glyphicon-remove icon-danger"></i>Block</a>';
        var unblockIpLink = '<a href="javascript:void(0);" class="unblock-' +ip+ '"> <i class="glyphicon glyphicon-ok icon-success"></i>UnBlock</a>';
        var blockMenu = $('<ul class="dropdown-menu dropdown-menu-xs pull-right">' +
        '<li> ' + blockIpLink + ' <li>' +
        '<li> ' + unblockIpLink + ' <li>' +
        '<li class="divider"><li>' +
        '<li> <a href="javascript:void(0);"> Cancel</a> <li>' +
        '</ul>');
        return blockMenu;
    }

    function manageIp(ip, shouldBlock)
    {
        var ajaxData = {};
        ajaxData['action']         = 'manageIp';
        ajaxData['clientIp']       = ip;
        ajaxData['shouldBlock']    = shouldBlock;
        ajaxData['reportsNonce']   = GdbcAdmin.reportsNonce;
        jQuery.ajax({
            type : "post",
            cache: false,
            dataType : "json",
            url : GdbcAdmin.ajaxUrl,
            data : ajaxData,
            success: function(response) {
                (false !== (!!response) && ip) ? updatePageIpIcons(ip, shouldBlock) : '';
            }
        });
    }

    function updatePageIpIcons(ip, shouldBlock)
    {
        $.each($("td i.glyphicon:first-child"), function(){
            var cellIp = $.trim($(this).parent().text());
            if (cellIp == ip){
                if (shouldBlock) {
                    $(this).attr("class", "glyphicon glyphicon-minus-sign icon-danger");
                }
                else {
                    $(this).attr("class", "glyphicon");
                }
            }
        });
    }

    // Util Functions Section
    function suffixFormatter(val)
    {
        return Math.round(val);
    }

});