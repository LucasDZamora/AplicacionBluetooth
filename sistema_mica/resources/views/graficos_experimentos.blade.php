@extends('layouts.app')

@section('content')
    <style>
        #chartdiv,#chartdiv2,#chartdiv3,#chartdiv4,#chartdiv5,#chartdiv6,#chartdiv7,#chartdiv8,#chartdiv9,#chartdiv10 {
            width: 100%;
            height: 600px;
            max-width: 100%;
        }
    </style>
    <div class="container-fluid" style="margin-top: -23px;">
        <div class="row pt-3 pb-2" style="background-color: #f2f6ff !important;">
            <div class="col-12">Establecimiento: <strong>{{ $establecimiento->nombre }}</strong></div>
            <div class="col-12">Comuna: <strong>{{ $establecimiento->comuna }}</strong></div>
            <div class="col-12">EMA: {{ $experimentos->ema }} @if($estacion->nombre == '') (Ema) @else {{ $estacion->nombre }} @endif</div>
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
            <div class="col-lg-6"><div id="chartdiv9"></div></div>
            <div class="col-lg-6"><div id="chartdiv10"></div></div>

            <div class="col-lg-12 pt-4">
                <h5>Datos {{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif</h5>
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
                       data-url="{{url('get-grafico')}}/{{ $experimentos->id_experimentos }}"
                       data-export-data-type="all"
                       data-ajax-options="ajaxOptions">
                    <thead>
                    <tr class="text-center">
                        <th data-field="S1_t" data-align="center">Temperatura</th>
                        <th data-field="S1_h" data-align="center">Humedad</th>
                        <th data-field="S1_p" data-align="center">Presión</th>
                        <th data-field="S1_v" data-align="center">Gases</th>
                        <th data-field="S2_r" data-align="center">Radiación</th>
                        <th data-field="S2_n" data-align="center">Nivel UV</th>
                        <th data-field="S3_n" data-align="center">Nivel Luz</th>
                        <th data-field="S5_i" data-align="center">Sonido</th>
                        <th data-field="S6_t" data-align="center">Turbidez</th>
                        <th data-field="S7_c02" data-align="center">CO2</th>
                        <th data-field="reading_time" data-align="center">Fecha</th>
                    </tr>
                    </thead>
                </table>
            </div>

            <div class="col-lg-12 pt-4" style="display: none;">
                <h5>Datos Invernadero</h5>
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
                       data-url="{{url('get-grafico')."/".$experimentos->id_experimento}}"
                       data-export-data-type="all"
                       data-ajax-options="ajaxOptions">
                    <thead>
                    <tr class="text-center">
                        <th data-field="S1_t" data-align="center">Temperatura</th>
                        <th data-field="S1_h" data-align="center">Humedad</th>
                        <th data-field="S1_p" data-align="center">Presión</th>
                        <th data-field="S1_v" data-align="center">Gases</th>
                        <th data-field="S2_r" data-align="center">Radiación</th>
                        <th data-field="S2_n" data-align="center">Nivel UV</th>
                        <th data-field="S3_n" data-align="center">Nivel Luz</th>
                        <th data-field="S5_i" data-align="center">Sonido</th>
                        <th data-field="S6_t" data-align="center">Turbidez</th>
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
                        return '{{ $experimentos->nombre }}'
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
        am5.ready(function() {
            var root = am5.Root.new("chartdiv");
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
                text: "Temperatura registrada por la estación",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Temperatura (°C)",
                fontSize: 20,
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
                    timeUnit: "second",
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
                name: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                stacked: true,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif: {valueY}°C"
                })
            }));

            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1t_final !!};
            series1.data.setAll(data1);
            series1.appear(1000);
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
                text: "Humedad registrada por la estación",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Humedad (%)",
                fontSize: 20,
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
                    timeUnit: "second",
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
                name: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif: {valueY}%"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1h_final !!};
            series1.data.setAll(data1);
            series1.appear(1000);
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
                text: "Presión registrada por la estación",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Presión (atm)",
                fontSize: 20,
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
                    timeUnit: "second",
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
                name: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif: {valueY}atm"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1p_final !!};
            series1.data.setAll(data1);
            series1.appear(1000);
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
                text: "Gases volatiles orgánicos registrada por la estación",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Gases volatiles orgánicos (ppm)",
                fontSize: 20,
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
                    timeUnit: "second",
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
                name: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif: {valueY}ppm"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1v_final !!};
            series1.data.setAll(data1);
            series1.appear(1000);
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
                text: "Radiación UV registrada por la estación",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Radiación UV (mW/cm2)",
                fontSize: 20,
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
                    timeUnit: "second",
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
                name: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif: {valueY}mW/cm2"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1r_final !!};
            series1.data.setAll(data1);
            series1.appear(1000);
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
                text: "Nivel UV registrada por la estación",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Nivel UV",
                fontSize: 20,
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
                    timeUnit: "second",
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
                name: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif: {valueY}"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1n_final !!};
            series1.data.setAll(data1);
            series1.appear(1000);
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
                text: "Nivel Luz registrada por la estación",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Nivel Luz (lux)",
                fontSize: 20,
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
                    timeUnit: "second",
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
                name: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif: {valueY}lux"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1l_final !!};
            series1.data.setAll(data1);
            series1.appear(1000);
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
                text: "Intensidad Sonido registrada por la estación",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Intensidad Sonido (dB)",
                fontSize: 20,
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
                    timeUnit: "second",
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
                name: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif: {valueY}dB"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var seriesRangeDataItem = yAxis.makeDataItem({ value: 55, endValue: 0 });
            var seriesRange = series1.createAxisRange(seriesRangeDataItem);
            seriesRange.fills.template.setAll({
                visible: true,
                opacity: 0.3
            });

            seriesRange.fills.template.set("fill", am5.color(0x000000));
            seriesRange.strokes.template.set("stroke", am5.color(0x000000));

            seriesRangeDataItem.get("grid").setAll({
                strokeOpacity: 1,
                visible: true,
                stroke: am5.color(0x000000),
                strokeDasharray: [2, 2]
            })

            seriesRangeDataItem.get("label").setAll({
                location:0,
                visible:true,
                text:"Normativa",
                inside:true,
                centerX:0,
                centerY:am5.p100,
                fontWeight:"bold"
            })

            var data1 = {!! $ema1i_final !!};
            series1.data.setAll(data1);
            series1.appear(1000);
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
                text: "Turbidez registrada por la estación",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "Turbidez",
                fontSize: 20,
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
                    timeUnit: "second",
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
                name: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif: {valueY}"
                })
            }));
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            series1.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            var data1 = {!! $ema1tb_final !!};
            series1.data.setAll(data1);
            series1.appear(1000);
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
                text: "CO2 registrada por la estación",
                fontSize: 14,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));
            chart.children.unshift(am5.Label.new(root, {
                text: "CO2 (ppm)",
                fontSize: 20,
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
                    timeUnit: "second",
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
                name: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                fill: am5.color(0x095256),
                stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $experimentos->ema }} @if($estacion->nombre == null) (Ema) @else {{ $estacion->nombre }} @endif: {valueY}ppm"
                })
            }));

            series1.fills.template.setAll({
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
            series1.data.setAll(data1);
            series1.appear(1000);
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
