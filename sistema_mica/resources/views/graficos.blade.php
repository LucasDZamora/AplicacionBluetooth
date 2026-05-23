@extends('layouts.app_graficos')

@section('content')
    <style>
        #chartdiv,#chartdiv2,#chartdiv3,#chartdiv4,#chartdiv5,#chartdiv6,#chartdiv7,#chartdiv8,#chartdiv9,#chartdiv10 {
            width: 100%;
            height: 600px;
            max-width: 100%;
        }
    </style>
    <div class="container-fluid">
        <div class="row" style="background-color: #f2f6ff !important;">
            <div class="col-12">Establecimiento: <strong>{{ $establecimiento->nombre }}</strong></div>
            <div class="col-12">Comuna: <strong>{{ $establecimiento->comuna }}</strong></div>
            <div class="col-12">RBD: {{ $establecimiento->rbd }}</div>
        </div>
        <div class="w-100"></div>
        <div class="row">
            <div class="col-lg-6"><div id="chartdiv"></div></div>
            <div class="col-lg-6"><div id="chartdiv2"></div></div>
            <div class="col-lg-6"><div id="chartdiv3"></div></div>
            <div class="col-lg-6"><div id="chartdiv4"></div></div>
            <div class="col-lg-6"><div id="chartdiv5"></div></div>
            <div class="col-lg-6"><div id="chartdiv6"></div></div>
            <div class="col-lg-6"><div id="chartdiv7"></div></div>
            <div class="col-lg-6"><div id="chartdiv8"></div></div>
            <div class="col-lg-6" style="display: none;"><div id="chartdiv9"></div></div>
            <div class="col-lg-12"><div id="chartdiv10"></div></div>

            <div class="col-lg-12 pt-4">
                <h5>Datos @if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif</h5>
                <table id="datosema1"
                       data-classes="table text-nowrap mb-0"
                       style="" data-toolbar="#toolbar"
                       data-search="true"
                       data-show-refresh="true"
                       data-show-toggle="false"
                       data-show-fullscreen="false"
                       data-buttons-class="primary"
                       data-show-columns="false"
                       data-show-columns-toggle-all="false"
                       data-detail-view="false"
                       data-show-export="true"
                       data-click-to-select="false"
                       data-detail-formatter="detailFormatter"
                       data-minimum-count-columns="2"
                       data-show-pagination-switch="false"
                       data-pagination="true"
                       data-id-field="id"
                       data-page-list="[10, 25, 50, 100, All]"
                       data-page-size="10"
                       data-show-footer="false"
                       data-filter-control="true"
                       data-url="{{route('get_data_ema01',$establecimiento->id_establecimiento)}}"
                       data-export-data-type="all"
                       data-ajax-options="ajaxOptions">
                    <thead>
                    <tr class="text-center">
                        <th data-field="S1_t" data-align="center">Temperatura</th>
                        <th data-field="S1_h" data-align="center">Humedad</th>
                        <th data-field="S1_p" data-formatter="presion" data-align="center">Presión</th>
                        <th data-field="S1_v" data-align="center">Gases</th>
                        <th data-field="S2_r" data-align="center">Radiación</th>
                        <th data-field="S2_n" data-align="center">Nivel UV</th>
                        <th data-field="S3_n" data-align="center">Nivel Luz</th>
                        <th data-field="S5_i" data-align="center">Sonido</th>
                        <th data-field="S7_c02" data-align="center">CO2</th>
                        <th data-field="reading_time" data-align="center">Fecha</th>
                    </tr>
                    </thead>
                </table>
            </div>

            <div class="col-lg-12 pt-4">
                <h5>Datos @if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif</h5>
                <table id="datosema2"
                       data-classes="table text-nowrap mb-0"
                       style="" data-toolbar="#toolbar"
                       data-search="true"
                       data-show-refresh="true"
                       data-show-toggle="false"
                       data-show-fullscreen="false"
                       data-buttons-class="primary"
                       data-show-columns="false"
                       data-show-columns-toggle-all="false"
                       data-detail-view="false"
                       data-show-export="true"
                       data-click-to-select="false"
                       data-detail-formatter="detailFormatter"
                       data-minimum-count-columns="2"
                       data-show-pagination-switch="false"
                       data-pagination="true"
                       data-id-field="id"
                       data-page-list="[10, 25, 50, 100, All]"
                       data-page-size="10"
                       data-show-footer="false"
                       data-filter-control="true"
                       data-url="{{route('get_data_ema02',$establecimiento->id_establecimiento)}}"
                       data-export-data-type="all"
                       data-ajax-options="ajaxOptions">
                    <thead>
                    <tr class="text-center">
                        <th data-field="S1_t" data-align="center">Temperatura</th>
                        <th data-field="S1_h" data-align="center">Humedad</th>
                        <th data-field="S1_p" data-formatter="presion" data-align="center">Presión</th>
                        <th data-field="S1_v" data-align="center">Gases</th>
                        <th data-field="S2_r" data-align="center">Radiación</th>
                        <th data-field="S2_n" data-align="center">Nivel UV</th>
                        <th data-field="S3_n" data-align="center">Nivel Luz</th>
                        <th data-field="S5_i" data-align="center">Sonido</th>
                        <!--<th data-field="S6_t" data-align="center">Turbidez</th>-->
                        <th data-field="S7_c02" data-align="center">CO2</th>
                        <th data-field="reading_time" data-align="center">Fecha</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        let $tabledatosema1 = $('#datosema1');
        let $tabledatosema2 = $('#datosema2');
        initTable();
        function initTable() {
            $tabledatosema1.bootstrapTable({
                locale: "es-ES",
                exportTypes: ['xlsx'],
                exportOptions: {
                    fileName: function () {
                        return 'Datos_ema1'
                    }
                }
            });

            $tabledatosema2.bootstrapTable({
                locale: "es-ES",
                exportTypes: ['xlsx'],
                exportOptions: {
                    fileName: function () {
                        return 'Datos_ema2'
                    }
                }
            });
        }

        function presion(value, row) {
            //return parseFloat(value) * 100
            return value.toFixed(3);
        }
        am5.ready(function() {
            var root = am5.Root.new("chartdiv");
            root.setThemes([
                am5themes_Animated.new(root)
            ]);
            root.locale = am5locales_es_ES;
            //root.timezone = am5.Timezone.new("America/Santiago");
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                pinchZoomX:true,
                paddingLeft: 0,
                layout: root.verticalLayout,
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Temperatura registrada por la(s) estación(es)",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Temperatura (°C)",
                fontSize: 25,
                fontWeight: "500",
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50),
                paddingTop: 0,
                paddingBottom: 0
            }));
            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "none"
            }));
            cursor.lineY.set("visible", false);
            var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
                maxDeviation: 0.1,
                groupData: true,
                baseInterval: {
                    timeUnit: "minute",
                    count: 30
                },
                renderer: am5xy.AxisRendererX.new(root, {
                    minorGridEnabled:true
                }),
                tooltip: am5.Tooltip.new(root, {})
            }));

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererY.new(root, {
                    pan:"zoom"
                })
            }));
            var series1 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                stacked: true,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif: {valueY}°C"
                })
            }));

            var series2 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color("#D52600"),
                stroke: am5.color("#D52600"),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif: {valueY}°"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            series2.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1t_final !!};
            var data2 = {!! $ema2t_final !!};
            series1.data.setAll(data1);
            series2.data.setAll(data2);
            series1.appear(1000);
            series2.appear(1000);
            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.horizontalLayout
                })
            );
            legend.data.setAll(chart.series.values);

        });

        am5.ready(function() {
            var root = am5.Root.new("chartdiv2");
            root.setThemes([
                am5themes_Animated.new(root)
            ]);
            root.locale = am5locales_es_ES;
            root.timezone = am5.Timezone.new("America/Santiago");
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                pinchZoomX:true,
                paddingLeft: 0,
                layout: root.verticalLayout,
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Humedad registrada por la(s) estación(es)",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Humedad (%)",
                fontSize: 25,
                fontWeight: "500",
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50),
                paddingTop: 0,
                paddingBottom: 0
            }));
            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "none"
            }));
            cursor.lineY.set("visible", false);
            var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
                maxDeviation: 0.1,
                groupData: true,
                baseInterval: {
                    timeUnit: "minute",
                    count: 30
                },
                renderer: am5xy.AxisRendererX.new(root, {
                    minorGridEnabled:true
                }),
                tooltip: am5.Tooltip.new(root, {})
            }));

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererY.new(root, {
                    pan:"zoom"
                })
            }));
            var series1 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif: {valueY}%"
                })
            }));

            var series2 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color("#D52600"),
                stroke: am5.color("#D52600"),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif: {valueY}%"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            series2.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1h_final !!};
            var data2 = {!! $ema2h_final !!};
            series1.data.setAll(data1);
            series2.data.setAll(data2);
            series1.appear(1000);
            series2.appear(1000);
            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.horizontalLayout
                })
            );
            legend.data.setAll(chart.series.values);

        });

        am5.ready(function() {
            var root = am5.Root.new("chartdiv3");
            root.setThemes([
                am5themes_Animated.new(root)
            ]);
            root.locale = am5locales_es_ES;
            root.timezone = am5.Timezone.new("America/Santiago");
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                pinchZoomX:true,
                paddingLeft: 0,
                layout: root.verticalLayout,
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Presión registrada por la(s) estación(es)",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Presión (atm)",
                fontSize: 25,
                fontWeight: "500",
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50),
                paddingTop: 0,
                paddingBottom: 0
            }));
            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "none"
            }));
            cursor.lineY.set("visible", false);
            var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
                maxDeviation: 0.1,
                groupData: true,
                baseInterval: {
                    timeUnit: "minute",
                    count: 30
                },
                renderer: am5xy.AxisRendererX.new(root, {
                    minorGridEnabled:true
                }),
                tooltip: am5.Tooltip.new(root, {})
            }));

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                min: 0,
                max: 2,
                renderer: am5xy.AxisRendererY.new(root, {
                    pan:"zoom"
                })
            }));
            var series1 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif: {valueY}atm"
                })
            }));

            var series2 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color("#D52600"),
                stroke: am5.color("#D52600"),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif: {valueY}atm"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            series2.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1p_final !!};
            var data2 = {!! $ema2p_final !!};
            series1.data.setAll(data1);
            series2.data.setAll(data2);
            series1.appear(1000);
            series2.appear(1000);
            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.horizontalLayout
                })
            );
            legend.data.setAll(chart.series.values);

        });

        am5.ready(function() {
            var root = am5.Root.new("chartdiv4");
            root.setThemes([
                am5themes_Animated.new(root)
            ]);
            root.locale = am5locales_es_ES;
            root.timezone = am5.Timezone.new("America/Santiago");
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                pinchZoomX:true,
                paddingLeft: 0,
                layout: root.verticalLayout,
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Gases volatiles orgánicos registrada por la(s) estación(es)",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Gases volatiles orgánicos (ppm)",
                fontSize: 25,
                fontWeight: "500",
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50),
                paddingTop: 0,
                paddingBottom: 0
            }));
            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "none"
            }));
            cursor.lineY.set("visible", false);
            var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
                maxDeviation: 0.1,
                groupData: true,
                baseInterval: {
                    timeUnit: "minute",
                    count: 30
                },
                renderer: am5xy.AxisRendererX.new(root, {
                    minorGridEnabled:true
                }),
                tooltip: am5.Tooltip.new(root, {})
            }));

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererY.new(root, {
                    pan:"zoom"
                })
            }));
            var series1 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif: {valueY}ppm"
                })
            }));

            var series2 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color("#D52600"),
                stroke: am5.color("#D52600"),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif: {valueY}ppm"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            series2.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1v_final !!};
            var data2 = {!! $ema2v_final !!};
            series1.data.setAll(data1);
            series2.data.setAll(data2);
            series1.appear(1000);
            series2.appear(1000);
            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.horizontalLayout
                })
            );
            legend.data.setAll(chart.series.values);

        });

        am5.ready(function() {
            var root = am5.Root.new("chartdiv5");
            root.setThemes([
                am5themes_Animated.new(root)
            ]);
            root.locale = am5locales_es_ES;
            root.timezone = am5.Timezone.new("America/Santiago");
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                pinchZoomX:true,
                paddingLeft: 0,
                layout: root.verticalLayout,
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Radiación UV registrada por la(s) estación(es)",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Radiación UV (mW/cm2)",
                fontSize: 25,
                fontWeight: "500",
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50),
                paddingTop: 0,
                paddingBottom: 0
            }));
            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "none"
            }));
            cursor.lineY.set("visible", false);
            var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
                maxDeviation: 0.1,
                groupData: true,
                baseInterval: {
                    timeUnit: "minute",
                    count: 30
                },
                renderer: am5xy.AxisRendererX.new(root, {
                    minorGridEnabled:true
                }),
                tooltip: am5.Tooltip.new(root, {})
            }));

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererY.new(root, {
                    pan:"zoom"
                })
            }));
            var series1 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif: {valueY}mW/cm2"
                })
            }));

            var series2 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color("#D52600"),
                stroke: am5.color("#D52600"),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif: {valueY}miliwatt/cm2"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            series2.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1r_final !!};
            var data2 = {!! $ema2r_final !!};
            series1.data.setAll(data1);
            series2.data.setAll(data2);
            series1.appear(1000);
            series2.appear(1000);
            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.horizontalLayout
                })
            );
            legend.data.setAll(chart.series.values);

        });

        am5.ready(function() {
            var root = am5.Root.new("chartdiv6");
            root.setThemes([
                am5themes_Animated.new(root)
            ]);
            root.locale = am5locales_es_ES;
            root.timezone = am5.Timezone.new("America/Santiago");
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                pinchZoomX:true,
                paddingLeft: 0,
                layout: root.verticalLayout,
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Nivel UV registrada por la(s) estación(es)",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Nivel UV",
                fontSize: 25,
                fontWeight: "500",
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50),
                paddingTop: 0,
                paddingBottom: 0
            }));
            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "none"
            }));
            cursor.lineY.set("visible", false);
            var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
                maxDeviation: 0.1,
                groupData: true,
                baseInterval: {
                    timeUnit: "minute",
                    count: 30
                },
                renderer: am5xy.AxisRendererX.new(root, {
                    minorGridEnabled:true
                }),
                tooltip: am5.Tooltip.new(root, {})
            }));

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                min: 1,
                max: 11,
                renderer: am5xy.AxisRendererY.new(root, {
                    pan:"zoom"
                })
            }));
            var series1 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif: {valueY}"
                })
            }));

            var series2 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color("#D52600"),
                stroke: am5.color("#D52600"),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif: {valueY}"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            series2.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1n_final !!};
            var data2 = {!! $ema2n_final !!};
            series1.data.setAll(data1);
            series2.data.setAll(data2);
            series1.appear(1000);
            series2.appear(1000);
            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.horizontalLayout
                })
            );
            legend.data.setAll(chart.series.values);

        });

        am5.ready(function() {
            var root = am5.Root.new("chartdiv7");
            root.setThemes([
                am5themes_Animated.new(root)
            ]);
            root.locale = am5locales_es_ES;
            root.timezone = am5.Timezone.new("America/Santiago");
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                pinchZoomX:true,
                paddingLeft: 0,
                layout: root.verticalLayout,
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Nivel Luz registrada por la(s) estación(es)",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Nivel Luz (lux)",
                fontSize: 25,
                fontWeight: "500",
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50),
                paddingTop: 0,
                paddingBottom: 0
            }));
            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "none"
            }));
            cursor.lineY.set("visible", false);
            var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
                maxDeviation: 0.1,
                groupData: true,
                baseInterval: {
                    timeUnit: "minute",
                    count: 30
                },
                renderer: am5xy.AxisRendererX.new(root, {
                    minorGridEnabled:true
                }),
                tooltip: am5.Tooltip.new(root, {})
            }));

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererY.new(root, {
                    pan:"zoom"
                })
            }));
            var series1 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif: {valueY}lux"
                })
            }));

            var series2 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color("#D52600"),
                stroke: am5.color("#D52600"),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif: {valueY}lux"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            series2.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1l_final !!};
            var data2 = {!! $ema2l_final !!};
            series1.data.setAll(data1);
            series2.data.setAll(data2);
            series1.appear(1000);
            series2.appear(1000);
            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.horizontalLayout
                })
            );
            legend.data.setAll(chart.series.values);

        });

        am5.ready(function() {
            var root = am5.Root.new("chartdiv8");
            root.setThemes([
                am5themes_Animated.new(root)
            ]);
            root.locale = am5locales_es_ES;
            root.timezone = am5.Timezone.new("America/Santiago");
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                pinchZoomX:true,
                paddingLeft: 0,
                layout: root.verticalLayout,
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Intensidad Sonido registrada por la(s) estación(es)",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Intensidad Sonido (dB)",
                fontSize: 25,
                fontWeight: "500",
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50),
                paddingTop: 0,
                paddingBottom: 0
            }));
            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "none"
            }));
            cursor.lineY.set("visible", false);
            var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
                maxDeviation: 0.1,
                groupData: true,
                baseInterval: {
                    timeUnit: "minute",
                    count: 30
                },
                renderer: am5xy.AxisRendererX.new(root, {
                    minorGridEnabled:true
                }),
                tooltip: am5.Tooltip.new(root, {})
            }));

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererY.new(root, {
                    pan:"zoom"
                })
            }));
            var series1 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif: {valueY}dB"
                })
            }));

            var series2 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color("#D52600"),
                stroke: am5.color("#D52600"),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif: {valueY}db"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            series2.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1i_final !!};
            var data2 = {!! $ema2i_final !!};
            series1.data.setAll(data1);
            series2.data.setAll(data2);
            series1.appear(1000);
            series2.appear(1000);
            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.horizontalLayout
                })
            );
            legend.data.setAll(chart.series.values);

        });

        am5.ready(function() {
            var root = am5.Root.new("chartdiv9");
            root.setThemes([
                am5themes_Animated.new(root)
            ]);
            root.locale = am5locales_es_ES;
            root.timezone = am5.Timezone.new("America/Santiago");
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                pinchZoomX:true,
                paddingLeft: 0,
                layout: root.verticalLayout,
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Turbidez registrada por la(s) estación(es)",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Turbidez",
                fontSize: 25,
                fontWeight: "500",
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50),
                paddingTop: 0,
                paddingBottom: 0
            }));
            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "none"
            }));
            cursor.lineY.set("visible", false);
            var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
                maxDeviation: 0.1,
                groupData: true,
                baseInterval: {
                    timeUnit: "minute",
                    count: 30
                },
                renderer: am5xy.AxisRendererX.new(root, {
                    minorGridEnabled:true
                }),
                tooltip: am5.Tooltip.new(root, {})
            }));

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererY.new(root, {
                    pan:"zoom"
                })
            }));
            var series1 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif: {valueY}"
                })
            }));

            var series2 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color("#D52600"),
                stroke: am5.color("#D52600"),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif: {valueY}"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            series2.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1tb_final !!};
            var data2 = {!! $ema2tb_final !!};
            series1.data.setAll(data1);
            series2.data.setAll(data2);
            series1.appear(1000);
            series2.appear(1000);
            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.horizontalLayout
                })
            );
            legend.data.setAll(chart.series.values);

        });

        am5.ready(function() {
            var root = am5.Root.new("chartdiv10");
            root.setThemes([
                am5themes_Animated.new(root)
            ]);
            root.locale = am5locales_es_ES;
            root.timezone = am5.Timezone.new("America/Santiago");
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                pinchZoomX:true,
                paddingLeft: 0,
                layout: root.verticalLayout,
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "CO2 registrada por la(s) estación(es)",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "CO2 (ppm)",
                fontSize: 25,
                fontWeight: "500",
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50),
                paddingTop: 0,
                paddingBottom: 0
            }));
            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "none"
            }));
            cursor.lineY.set("visible", false);
            var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
                maxDeviation: 0.1,
                groupData: true,
                baseInterval: {
                    timeUnit: "minute",
                    count: 30
                },
                renderer: am5xy.AxisRendererX.new(root, {
                    minorGridEnabled:true
                }),
                tooltip: am5.Tooltip.new(root, {})
            }));

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererY.new(root, {
                    pan:"zoom"
                })
            }));
            var series1 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[0]->nombre == '')Ema1 @else{{ $estaciones[0]->nombre }} @endif: {valueY}ppm"
                })
            }));

            var series2 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                    fill: am5.color("#D52600"),
                stroke: am5.color("#D52600"),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "@if($estaciones[1]->nombre == '')Ema2 @else{{ $estaciones[1]->nombre }} @endif: {valueY}ppm"
                })
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            series2.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            var data1 = {!! $ema1co2_final !!};
            var data2 = {!! $ema2co2_final !!};
            series1.data.setAll(data1);
            series2.data.setAll(data2);
            series1.appear(1000);
            series2.appear(1000);
            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.horizontalLayout
                })
            );
            legend.data.setAll(chart.series.values);

        });
    </script>
@endpush
