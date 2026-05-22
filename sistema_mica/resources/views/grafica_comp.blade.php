    <style>
        #chartdiv,#chartdiv2,#chartdiv3,#chartdiv4,#chartdiv5,#chartdiv6,#chartdiv7,#chartdiv8,#chartdiv9,#chartdiv10 {
            width: 100%;
            height: 600px;
            max-width: 100%;
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12"><div id="chartdiv"></div></div>
            <div class="col-lg-12"><div id="chartdiv2"></div></div>
            <div class="col-lg-12"><div id="chartdiv3"></div></div>
            <div class="col-lg-12"><div id="chartdiv4"></div></div>
            <div class="col-lg-12"><div id="chartdiv5"></div></div>
            <div class="col-lg-12"><div id="chartdiv6"></div></div>
            <div class="col-lg-12"><div id="chartdiv7"></div></div>
            <div class="col-lg-12"><div id="chartdiv8" style="display: none;"></div></div>
            <div class="col-lg-12" style="display: none;"><div id="chartdiv9"></div></div>
            <div class="col-lg-12"><div id="chartdiv10"></div></div>

            @foreach($emasFinal as $ema)
                <div class="col-lg-12 pt-4">
                <h3>{{ $ema->establecimiento }}, {{ $ema->establecimiento_comuna }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif</h3>
                <table id="datosema{{ $ema->id_estacion }}"
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
                       data-url="{{route('get_data_comp',$ema->id_estacion)}}"
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
                        <!--<th data-field="S5_i" data-align="center">Sonido</th> -->
                        <th data-field="S6_t" data-align="center">Turbidez</th>
                        <th data-field="S7_c02" data-align="center">CO2</th>
                        <th data-field="reading_time" data-align="center">Fecha</th>
                    </tr>
                    </thead>
                </table>
            </div>
            @endforeach
        </div>
    </div>
    <script>
        @foreach($emasFinal as $ema)
            $tabledatosema{{ $ema->id_estacion }} = $('#datosema{{ $ema->id_estacion }}');
        @endforeach
        initTable();
        function initTable() {
            @foreach($emasFinal as $ema)
                $tabledatosema{{ $ema->id_estacion }}.bootstrapTable({
                    locale: "es-ES",
                    exportTypes: ['xlsx'],
                    exportOptions: {
                        fileName: function () {
                            return 'Datos_ema{{ $ema->id_estacion }}'
                        }
                    }
                });
            @endforeach
        }
        am5.ready(function() {
            var root = am5.Root.new("chartdiv");
            root.setThemes([
                am5themes_Animated.new(root)
            ]);
            root.locale = am5locales_es_ES;
            root.timezone = am5.Timezone.new("Africa/Abidjan");
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
                maxDeviation: 0.5,
                groupData: true,
                baseInterval: {
                    timeUnit: "minute",
                    count: 30
                },
                gridIntervals: [
                    { timeUnit: "day", count: 5 }
                ],
                renderer: am5xy.AxisRendererX.new(root, {
                    minorGridEnabled:false
                }),
                tooltip: am5.Tooltip.new(root, {})
            }));

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererY.new(root, {
                    pan:"zoom"
                })
            }));
            @foreach($emasFinal as $ema)
                var series{{ $loop->index }} = chart.series.push(am5xy.LineSeries.new(root, {
                    name: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif",
                    connect: false,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "value",
                    valueXField: "date",
                    //fill: am5.color(0x095256),
                    //stroke: am5.color(0x095256),
                    tooltip: am5.Tooltip.new(root, {
                        labelText: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif: {valueY}°C"
                    })
                }));
            @endforeach
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });
            @foreach($emasFinal as $ema)
            series{{$loop->index}}.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });
            var data{{$loop->index}} = {!! ${"emat_final$loop->index"} !!};
            series{{$loop->index}}.data.setAll(data{{$loop->index}});
            series{{$loop->index}}.appear(1000);
            @endforeach
            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.gridLayout
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
            root.timezone = am5.Timezone.new("Africa/Abidjan");
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
                gridIntervals: [
                    { timeUnit: "day", count: 5 }
                ],
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
            @foreach($emasFinal as $ema)
                var series{{ $loop->index }} = chart.series.push(am5xy.LineSeries.new(root, {
                    name: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif",
                    connect: false,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "value",
                    valueXField: "date",
                    //fill: am5.color(0x095256),
                    //stroke: am5.color(0x095256),
                    tooltip: am5.Tooltip.new(root, {
                        labelText: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif: {valueY}%"
                    })
                }));
            @endforeach
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            @foreach($emasFinal as $ema)

            series{{$loop->index}}.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });
            var data{{$loop->index}} = {!! ${"emah_final$loop->index"} !!};
            series{{$loop->index}}.data.setAll(data{{$loop->index}});
            series{{$loop->index}}.appear(1000);
            @endforeach
            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.gridLayout
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
            root.timezone = am5.Timezone.new("Africa/Abidjan");
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
                gridIntervals: [
                    { timeUnit: "day", count: 5 }
                ],
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
            @foreach($emasFinal as $ema)
            var series{{ $loop->index }} = chart.series.push(am5xy.LineSeries.new(root, {
                name: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                //fill: am5.color(0x095256),
                //stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif: {valueY}atm"
                })
            }));
            @endforeach
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            @foreach($emasFinal as $ema)
            series{{$loop->index}}.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });
            var data{{$loop->index}} = {!! ${"emap_final$loop->index"} !!};
            series{{$loop->index}}.data.setAll(data{{$loop->index}});
            series{{$loop->index}}.appear(1000);
            @endforeach
            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.gridLayout
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
            root.timezone = am5.Timezone.new("Africa/Abidjan");
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
                gridIntervals: [
                    { timeUnit: "day", count: 5 }
                ],
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
            @foreach($emasFinal as $ema)
            var series{{ $loop->index }} = chart.series.push(am5xy.LineSeries.new(root, {
                name: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                //fill: am5.color(0x095256),
                //stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif: {valueY}ppm"
                })
            }));
            @endforeach
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            @foreach($emasFinal as $ema)
            series{{$loop->index}}.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });
            var data{{$loop->index}} = {!! ${"emav_final$loop->index"} !!};
            series{{$loop->index}}.data.setAll(data{{$loop->index}});
            series{{$loop->index}}.appear(1000);
            @endforeach

            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.gridLayout
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
            root.timezone = am5.Timezone.new("Africa/Abidjan");
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
                gridIntervals: [
                    { timeUnit: "day", count: 5 }
                ],
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
            @foreach($emasFinal as $ema)
            var series{{ $loop->index }} = chart.series.push(am5xy.LineSeries.new(root, {
                name: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                //fill: am5.color(0x095256),
                //stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif: {valueY}mW/cm2"
                })
            }));
            @endforeach
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            @foreach($emasFinal as $ema)
            series{{$loop->index}}.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });
            var data{{$loop->index}} = {!! ${"emar_final$loop->index"} !!};
            series{{$loop->index}}.data.setAll(data{{$loop->index}});
            series{{$loop->index}}.appear(1000);
            @endforeach
            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.gridLayout
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
            root.timezone = am5.Timezone.new("Africa/Abidjan");
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
                gridIntervals: [
                    { timeUnit: "day", count: 5 }
                ],
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
            @foreach($emasFinal as $ema)
            var series{{ $loop->index }} = chart.series.push(am5xy.LineSeries.new(root, {
                name: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                //fill: am5.color(0x095256),
                //stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif: {valueY}"
                })
            }));
            @endforeach
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            @foreach($emasFinal as $ema)
            series{{$loop->index}}.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });
            var data{{$loop->index}} = {!! ${"eman_final$loop->index"} !!};
            series{{$loop->index}}.data.setAll(data{{$loop->index}});
            series{{$loop->index}}.appear(1000);
            @endforeach

            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.gridLayout
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
            root.timezone = am5.Timezone.new("Africa/Abidjan");
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
                gridIntervals: [
                    { timeUnit: "day", count: 5 }
                ],
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
            @foreach($emasFinal as $ema)
            var series{{ $loop->index }} = chart.series.push(am5xy.LineSeries.new(root, {
                name: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                //fill: am5.color(0x095256),
                //stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif: {valueY}lux"
                })
            }));
            @endforeach
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            @foreach($emasFinal as $ema)
            series{{$loop->index}}.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });
            var data{{$loop->index}} = {!! ${"emal_final$loop->index"} !!};
            series{{$loop->index}}.data.setAll(data{{$loop->index}});
            series{{$loop->index}}.appear(1000);
            @endforeach

            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.gridLayout
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
            root.timezone = am5.Timezone.new("Africa/Abidjan");
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
                gridIntervals: [
                    { timeUnit: "day", count: 5 }
                ],
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
            @foreach($emasFinal as $ema)
            var series{{ $loop->index }} = chart.series.push(am5xy.LineSeries.new(root, {
                name: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                //fill: am5.color(0x095256),
                //stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif: {valueY}dB"
                })
            }));
            @endforeach
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            @foreach($emasFinal as $ema)
            series{{$loop->index}}.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });
            var data{{$loop->index}} = {!! ${"emai_final$loop->index"} !!};
            series{{$loop->index}}.data.setAll(data{{$loop->index}});
            series{{$loop->index}}.appear(1000);
            @endforeach

            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.gridLayout
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
            root.timezone = am5.Timezone.new("Africa/Abidjan");
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
                gridIntervals: [
                    { timeUnit: "day", count: 5 }
                ],
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
            @foreach($emasFinal as $ema)
            var series{{ $loop->index }} = chart.series.push(am5xy.LineSeries.new(root, {
                name: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                //fill: am5.color(0x095256),
                //stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif: {valueY}"
                })
            }));
            @endforeach
            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            @foreach($emasFinal as $ema)
            series{{$loop->index}}.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });
            var data{{$loop->index}} = {!! ${"ematb_final$loop->index"} !!};
            series{{$loop->index}}.data.setAll(data{{$loop->index}});
            series{{$loop->index}}.appear(1000);
            @endforeach

            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.gridLayout
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
            root.timezone = am5.Timezone.new("Africa/Abidjan");
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
                gridIntervals: [
                    { timeUnit: "day", count: 5 }
                ],
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
            @foreach($emasFinal as $ema)
            var series{{ $loop->index }} = chart.series.push(am5xy.LineSeries.new(root, {
                name: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif",
                connect: false,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
                //fill: am5.color(0x095256),
                //stroke: am5.color(0x095256),
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{{ $ema->rbd }} @if(isset($ema->estaciones)) ({{ $ema->estaciones }}) @endif: {valueY}ppm"
                })
            }));
            @endforeach

            var exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {})
            });

            chart.set("scrollbarX", am5.Scrollbar.new(root, {
                orientation: "horizontal"
            }));

            @foreach($emasFinal as $ema)
            series{{$loop->index}}.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });
            var data{{$loop->index}} = {!! ${"emaco2_final$loop->index"} !!};
            series{{$loop->index}}.data.setAll(data{{$loop->index}});
            series{{$loop->index}}.appear(1000);
            @endforeach

            chart.appear(1000, 100);

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    useDefaultMarker: true,
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    layout: root.gridLayout
                })
            );
            legend.data.setAll(chart.series.values);

        });
    </script>
