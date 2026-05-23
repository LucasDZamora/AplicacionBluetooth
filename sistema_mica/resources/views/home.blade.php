@extends('layouts.app')

@section('content')
    <style>
        .kv-upload-progress{
            display: none !important;
        }
        body{
            background-color: #f4f0e9 !important;
        }
    </style>
<div class="container-fluid" style="width: 95%;">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="background-color: #407ebf;color: #ffffff;"><strong>Estaciones</strong></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearExperimento">Crear Experimento</button>
                        <table id="experimentos"
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
                               data-show-export="false"
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
                               data-url="{{route('get_experimento',Auth::user()->id_establecimiento)}}"
                               data-export-data-type="all"
                               data-ajax-options="ajaxOptions">
                            <thead>
                            <tr class="text-center">
                                <th data-field="nombre" data-align="center">Nombre</th>
                                <th data-field="ema" data-align="center">EMA</th>
                                <th data-field="fechaInicio" data-align="center">Inicio</th>
                                <th data-field="fechaTermino" data-align="center">Termino</th>
                                <th data-field="Creado" data-align="center">Creado</th>
                                <th data-field="registros" data-align="center">N° Registros</th>
                                <th data-field="" data-formatter="registros" data-align="center">Cargar Registros</th>
                                <th data-field="" data-formatter="grafico" data-align="center">Gráfico</th>
                                <th data-field="" data-formatter="editar" data-align="center">Editar</th>
                                <th data-field="" data-formatter="eliminar" data-align="center">Eliminar</th>
                            </tr>
                            </thead>
                        </table>
                        <div class="col-lg-12 pt-4">
                            <h5>Datos @if($emas[0]->nombre == '')Ema1 @else{{ $emas[0]->nombre }} @endif</h5>
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
                                   data-url="{{route('get_data_ema01',Auth::user()->id_establecimiento)}}"
                                   data-export-data-type="all"
                                   data-ajax-options="ajaxOptions">
                                <thead>
                                <tr class="text-center">
                                    <th data-field="S1_t" data-align="center">Temperatura</th>
                                    <th data-field="S1_h" data-align="center">Humedad</th>
                                    <th data-field="S1_p" data-formatter="" data-align="center">Presión</th>
                                    <th data-field="S1_v" data-align="center">Gases</th>
                                    <th data-field="S2_r" data-align="center">Radiación</th>
                                    <th data-field="S2_n" data-align="center">Nivel UV</th>
                                    <th data-field="S3_n" data-align="center">Nivel Luz</th>
                                    <th data-field="S5_i" data-align="center">Sonido</th>
                                    <th data-field="S7_c02" data-align="center">CO2</th>
                                    <th data-field="reading_time" data-align="center">Fecha</th>
                                    <th data-field="id" data-formatter="editarDato" data-align="center">Editar</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        @if($emas[1]->mac != 'AA:AA:AA:AA:AA:AA')
                            <div class="col-lg-12 pt-4">
                            <h5>Datos @if($emas[1]->nombre == '')Ema2 @else{{ $emas[1]->nombre }} @endif</h5>
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
                                   data-url="{{route('get_data_ema02',Auth::user()->id_establecimiento)}}"
                                   data-export-data-type="all"
                                   data-ajax-options="ajaxOptions">
                                <thead>
                                <tr class="text-center">
                                    <th data-field="S1_t" data-align="center">Temperatura</th>
                                    <th data-field="S1_h" data-align="center">Humedad</th>
                                    <th data-field="S1_p" data-formatter="" data-align="center">Presión</th>
                                    <th data-field="S1_v" data-align="center">Gases</th>
                                    <th data-field="S2_r" data-align="center">Radiación</th>
                                    <th data-field="S2_n" data-align="center">Nivel UV</th>
                                    <th data-field="S3_n" data-align="center">Nivel Luz</th>
                                    <th data-field="S5_i" data-align="center">Sonido</th>
                                    <!--<th data-field="S6_t" data-align="center">Turbidez</th>-->
                                    <th data-field="S7_c02" data-align="center">CO2</th>
                                    <th data-field="reading_time" data-align="center">Fecha</th>
                                    <th data-field="id" data-formatter="editarDato" data-align="center">Editar</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        @endif
                </div>
            </div>
        </div>
        <div class="col-md-12 pt-4">
            <div class="card">
                <div class="card-header" style="background-color: #407ebf;color: #ffffff;"><strong>Importar Registros</strong></div>
                <div class="card-body">
                    <form id="importar" enctype="multipart/form-data">
                        <input id="importar_datos" name="importar_datos" type="file" class="file" data-browse-on-zone-click="true" accept=".txt">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Modal para Cargar Registros -->
    <div class="modal fade" id="modalCargarRegistros" tabindex="-1" aria-labelledby="modalLabelCargar" aria-hidden="true" data-bs-backdrop='static' data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelCargar">Cargar registros al experimento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="importar" enctype="multipart/form-data">
                        <input id="importar_datos_experimentos" name="importar_datos_experimentos" type="file" class="file" data-browse-on-zone-click="true" accept=".txt">
                        <input type="hidden" id="id_experimento_input" name="id_experimento" value="0">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<!-- Modal Crear-->
<div class="modal fade" id="crearExperimento" tabindex="-1" aria-labelledby="crearExperimento" aria-hidden="true" data-bs-backdrop='static' data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearExperimento">Crear Experimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="crear_experimento">
                <input type="hidden" name="id_establecimiento" value="{{ Auth::user()->id_establecimiento }}">
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Experimento</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="ema" class="form-label">Seleccionar EMA</label>
                    <select id="ema" name="ema" class="form-select" required>
                        <option value="">Seleccionar</option>
                        @foreach($emas as $ema)
                            @if($ema->mac != 'AA:AA:AA:AA:AA:AA')
                                <option value="{{ $ema->mac }}">{{ $ema->mac }} - @if($ema->nombre == '')Ema{{$loop->iteration}}@else{{ $ema->nombre }}@endif</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="fecha_inicio" class="form-label">Inicio</label>
                    <div class="input-group" id="kt_td_picker_modal" data-td-target-input="nearest" data-td-target-toggle="nearest">
                        <input id="fecha_inicio" name="fecha_inicio" type="datetime-local" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Termino</label>
                    <div class="input-group" id="kt_td_picker_modal" data-td-target-input="nearest" data-td-target-toggle="nearest">
                        <input id="fecha_termino" name="fecha_termino" type="datetime-local" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cerrar_modal" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Crear</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Editar-->
<div class="modal fade" id="editarExperimento" tabindex="-1" aria-labelledby="editarExperimento" aria-hidden="true" data-bs-backdrop='static' data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarExperimento">Editar Experimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editar_experimento">
                <input type="hidden" id="eid_experimento" name="id_experimento" value="">
                <input type="hidden" name="id_establecimiento" value="{{ Auth::user()->id_establecimiento }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Experimento</label>
                        <input type="text" class="form-control" id="enombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="ema" class="form-label">Seleccionar EMA</label>
                        <select id="eema" name="ema" class="form-select" required>
                            <option value="">Seleccionar</option>
                            @foreach($emas as $ema)
                                <option value="{{ $ema->mac }}">{{ $ema->mac }} - @if($ema->nombre == '')Ema{{$loop->iteration}}@else{{ $ema->nombre }}@endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Inicio</label>
                        <div class="input-group" id="kt_td_picker_modal" data-td-target-input="nearest" data-td-target-toggle="nearest">
                            <input id="efecha_inicio" name="fecha_inicio" type="datetime-local" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Termino</label>
                        <div class="input-group" id="kt_td_picker_modal" data-td-target-input="nearest" data-td-target-toggle="nearest">
                            <input id="efecha_termino" name="fecha_termino" type="datetime-local" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edescripcion" name="descripcion" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary cerrar_modal" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Editar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Editar Datos-->
<div class="modal fade" id="editarDatos" tabindex="-1" aria-labelledby="editarDatos" aria-hidden="true" data-bs-backdrop='static' data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarDatos">Editar Datos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarDatos">
                <input type="hidden" id="id_dato" name="id_dato" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Temperatura</label>
                        <input type="text" class="form-control" id="S1_t" name="S1_t" readonly>
                        <input type="checkbox" class="form-check-input" id="S1_t_C" name="S1_t_C">
                        <label class="form-check-label" for="flexCheckDefault">Anular</label>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Humedad</label>
                        <input type="text" class="form-control" id="S1_h" name="S1_h" readonly>
                        <input type="checkbox" class="form-check-input" id="S1_h_C" name="S1_h_C">
                        <label class="form-check-label" for="flexCheckDefault">Anular</label>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Presión</label>
                        <input type="text" class="form-control" id="S1_p" name="S1_p" readonly>
                        <input type="checkbox" class="form-check-input" id="S1_p_C" name="S1_p_C">
                        <label class="form-check-label" for="flexCheckDefault">Anular</label>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Gases</label>
                        <input type="text" class="form-control" id="S1_v" name="S1_v" readonly>
                        <input type="checkbox" class="form-check-input" id="S1_v_C" name="S1_v_C">
                        <label class="form-check-label" for="flexCheckDefault">Anular</label>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Radiación</label>
                        <input type="text" class="form-control" id="S2_r" name="S2_r" readonly>
                        <input type="checkbox" class="form-check-input" id="S2_r_C" name="S2_r_C">
                        <label class="form-check-label" for="flexCheckDefault">Anular</label>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nivel UV</label>
                        <input type="text" class="form-control" id="S2_n" name="S2_n" readonly>
                        <input type="checkbox" class="form-check-input" id="S2_n_C" name="S2_n_C">
                        <label class="form-check-label" for="flexCheckDefault">Anular</label>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nivel Luz</label>
                        <input type="text" class="form-control" id="S3_n" name="S3_n" readonly>
                        <input type="checkbox" class="form-check-input" id="S3_n_C" name="S3_n_C">
                        <label class="form-check-label" for="flexCheckDefault">Anular</label>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Sonido</label>
                        <input type="text" class="form-control" id="S5_i" name="S5_i" readonly>
                        <input type="checkbox" class="form-check-input" id="S5_i_C" name="S5_i_C">
                        <label class="form-check-label" for="flexCheckDefault">Anular</label>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">CO2</label>
                        <input type="text" class="form-control" id="S7_c02" name="S7_c02" readonly>
                        <input type="checkbox" class="form-check-input" id="S7_c02_C" name="S7_c02_C">
                        <label class="form-check-label" for="flexCheckDefault">Anular</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary cerrar_modal" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Editar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Guardar-->
<div class="modal fade" id="nombrarEma" tabindex="-1" aria-labelledby="nombrarEma" aria-hidden="true" data-bs-backdrop='static' data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nombrarEma">Nombrar EMA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="guardar_nombre">
                <input type="hidden" name="id_establecimiento" value="{{ Auth::user()->id_establecimiento }}">
                <div class="modal-body">
                    @foreach($emas as $ema)
                        <input type="hidden" value="{{ $ema->mac }}" name="mac[]">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre EMA {{ $ema->mac }}</label>
                            <input type="text" class="form-control nombre_ema" maxlength="12" id="nombre" name="nombre[]" value="{{ $ema->nombre }}" required>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary cerrar_modal" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Cambiar-->
<div class="modal fade" id="cambiarContrasena" tabindex="-1" aria-labelledby="cambiarContrasena" aria-hidden="true" data-bs-backdrop='static' data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nombrarEma">Cambiar Contraseña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cambiar_contrasena">
                <input type="hidden" name="id_usuario" value="{{ Auth::user()->id_establecimiento }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Ingrese nueva contraseña</label>
                        <input type="password" class="form-control" id="ncontrasena" name="ncontrasena" required>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Confirme nueva contraseña</label>
                        <input type="password" class="form-control" id="ccontrasena" name="ccontrasena" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary cerrar_modal" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('js')
    <script>
        let $tabledatosema1 = $('#datosema1');
        let $tabledatosema2 = $('#datosema2');
        let $tableExperimentos = $('#experimentos');
        $("#importar_datos").fileinput({
            language: "es",
            allowedFileExtensions: ["txt"],
            uploadUrl: "{{ route('importar_txt') }}",
            uploadExtraData:{'_token': $('meta[name="csrf-token"]').attr('content'),'id_establecimiento': {{ Auth::user()->id_establecimiento }}},
        }).on('filepreupload', function(event, data, id, index) {
            //msgLoading();
        }).on('filebatchuploadsuccess', function(event, data, id, index) {
            /*
            Swal.fire({
                title: "Subida de archivo",
                text: "No se encontró ningún dato nuevo!",
                icon: "warning"
            });*/
        });

        $("#importar_datos_experimentos").fileinput({
            language: "es",
            allowedFileExtensions: ["txt"],
            uploadUrl: "{{ route('importar_txt_experimentos') }}",
            uploadExtraData:{'_token': $('meta[name="csrf-token"]').attr('content'),'id_establecimiento': {{ Auth::user()->id_establecimiento }}},
        }).on('filepreupload', function(event, data, id, index) {
            //msgLoading();
        }).on('filebatchuploadsuccess', function(event, data, id, index) {
            $tableExperimentos.bootstrapTable('refresh');
        });

        function msgLoading()
        {
            Swal.fire({
                title: "Procesando...",
                icon: "info",
                html:'<center><div class="spinner-grow" role="status"><span class="sr-only">Enviando...</span></div></center>',
                showCloseButton: false,
                showCancelButton: false,
                showConfirmButton: false,
                background: '#fff',
                backdrop: 'rgba(73, 73, 73, 0.62)',
                allowEscapeKey: false,
                allowEnterKey: false,
                showLoaderOnConfirm: false,
                allowOutsideClick: false
            });
        }
        initTable();
        function initTable() {
            $tableExperimentos.bootstrapTable({
                locale: "es-ES"
            });
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
        $('.nombre_ema').maxlength({
            threshold: 12
        });
        $("#crear_experimento").submit(function (event) {
            event.preventDefault();
            let formData = $(this).serializeArray();
            $.ajax({
                url: "{{ route('crear_experimento') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
            }).done(function (data, textStatus, jqXHR) {
                if(data == 'ok'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Experimento creado',
                        text: 'Experimento creado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
                $tableExperimentos.bootstrapTable('refresh');
                $(".cerrar_modal").trigger("click");
                $('#crear_experimento')[0].reset();
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText)
            });
        });

        $("#editar_experimento").submit(function (event) {
            event.preventDefault();
            let formData = $(this).serializeArray();
            $.ajax({
                url: "{{ route('editar_experimento') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
            }).done(function (data, textStatus, jqXHR) {
                if(data == 'ok'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Experimento editado',
                        text: 'Experimento editado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
                $tableExperimentos.bootstrapTable('refresh');
                $(".cerrar_modal").trigger("click");
                $('#editar_experimento')[0].reset();
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText)
            });
        });

        $("#guardar_nombre").submit(function (event) {
            event.preventDefault();
            let formData = $(this).serializeArray();
            $.ajax({
                url: "{{ route('guardar_nombre') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
            }).done(function (data, textStatus, jqXHR) {
                if(data == 'ok'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Nombre guardado',
                        text: 'Nombre guardado correctamente',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
                window.setTimeout( function(){
                    location.reload();
                }, 3000 );
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText)
            });
        });

        $("#formEditarDatos").submit(function (event) {
            event.preventDefault();
            let formData = $(this).serializeArray();
            $.ajax({
                url: "{{ route('editar_dato') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
            }).done(function (data, textStatus, jqXHR) {
                if(data == 'ok'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Datos guardados',
                        text: 'Datos guardado correctamente',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }else{
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sin cambios',
                        text: 'No se realizaron cambios',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
                $tabledatosema1.bootstrapTable('refresh');
                $(".cerrar_modal").trigger("click");
                $('#formEditarDatos')[0].reset();
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText)
            });
        });

        function presion(value, row) {
            return parseFloat(value) * 100
        }

        $("#cambiar_contrasena").submit(function (event) {
            event.preventDefault();
            let ncontrasena = $('#ncontrasena').val();
            let ccontrasena = $('#ccontrasena').val();
            if (ncontrasena != ccontrasena){
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Las contraseñas ingresadas son distintas"
                });
                return false;
            }
            if (ncontrasena.length < 8){
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Las contraseñas debe tener al menos 8 caracteres"
                });
                return false;
            }
            let formData = $(this).serializeArray();
            $.ajax({
                url: "{{ route('cambiar_contrasena') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
            }).done(function (data, textStatus, jqXHR) {
                if(data == 'ok'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Contraseña cambiada',
                        text: 'La contraseña fue cambiada correctamente',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
                $('#cambiar_contrasena')[0].reset();
                $(".cerrar_modal").trigger("click");
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText)
            });
        });

        function registros(value, row, index) {
            return `<button type="button" class="btn btn-sm btn-primary" onclick="abrirCargaRegistros(${row.id_experimentos})"><i class="fa fa-upload"></i></button>`;
        }

        function grafico(value, row, index) {
            return [
                '<button type="button" title="Ver Gráfico" class="btn btn-sm btn-primary verGrafico" data-id_experimento="'+ row.id_experimentos +'"><i class="fa-solid fa-chart-line"></i></button> '
            ].join('')
        }
        function editar(value, row, index) {
            return [
                '<button type="button" title="Editar Experimento" data-bs-toggle="modal" data-bs-target="#editarExperimento" class="btn btn-sm btn-success editarGrafico" data-id_experimento="'+ row.id_experimentos +'"><i class="fa-solid fa-pen-to-square"></i></button> '
            ].join('')
        }
        function editarDato(value, row, index) {
            return [
                '<button type="button" title="Editar Datos" data-bs-toggle="modal" data-bs-target="#editarDatos" class="btn btn-sm btn-success editarDatos" data-id_ema="'+ row.id +'"><i class="fa-solid fa-pen-to-square"></i></button> '
            ].join('')
        }
        function eliminar(value, row, index) {
            return [
                '<button type="button" title="Eliminar Experimento" class="btn btn-sm btn-danger eliminarGrafico" data-id_experimento="'+ row.id_experimentos +'"><i class="fa-solid fa-trash-can"></i></button> '
            ].join('')
        }
        function abrirCargaRegistros(id_experimento) {
            $('#id_experimento_input').val(id_experimento);
            $('#inputFileExperimento').fileinput('clear');
            $('#modalCargarRegistros').modal('show');
        }

        $tableExperimentos.on("click", ".verGrafico", function (e) {
            e.preventDefault();
            let id_experimento = $(this).data('id_experimento');
            let url = '{{ url('ver-grafico') }}';
            location.href=url+"/"+id_experimento;
        });

        $tabledatosema1.on("click", ".editarDatos", function (e) {
            e.preventDefault();
            let id_ema = $(this).data('id_ema');
            $.ajax({
                url: "{{ route('datos_ema') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {id_ema:id_ema},
            }).done(function (data, textStatus, jqXHR) {
                console.log(data);
                $('#id_dato').val(data.id);
                $('#S1_t').val(data.S1_t);
                $('#S1_h').val(data.S1_h);
                $('#S1_p').val(data.S1_p);
                $('#S1_v').val(data.S1_v);
                $('#S2_r').val(data.S2_r);
                $('#S2_n').val(data.S2_n);
                $('#S3_n').val(data.S3_n);
                $('#S5_i').val(data.S5_i);
                $('#S7_c02').val(data.S7_c02);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText)
            });
        });

        $tabledatosema2.on("click", ".editarDatos", function (e) {
            e.preventDefault();
            let id_ema = $(this).data('id_ema');
            $.ajax({
                url: "{{ route('datos_ema') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {id_ema:id_ema},
            }).done(function (data, textStatus, jqXHR) {
                console.log(data);
                $('#id_dato').val(data.id);
                $('#S1_t').val(data.S1_t);
                $('#S1_h').val(data.S1_h);
                $('#S1_p').val(data.S1_p);
                $('#S1_v').val(data.S1_v);
                $('#S2_r').val(data.S2_r);
                $('#S2_n').val(data.S2_n);
                $('#S3_n').val(data.S3_n);
                $('#S5_i').val(data.S5_i);
                $('#S7_c02').val(data.S7_c02);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText)
            });
        });

        $tableExperimentos.on("click", ".editarGrafico", function (e) {
            e.preventDefault();
            let id_experimento = $(this).data('id_experimento');
            $.ajax({
                url: "{{ route('datos_experimento') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {id_experimento:id_experimento},
            }).done(function (data, textStatus, jqXHR) {
                console.log(data);
                $('#eid_experimento').val(data.id_experimentos);
                $('#enombre').val(data.nombre);
                $('#eema').val(data.ema);
                $('#efecha_inicio').val(data.fecha_inicio);
                $('#efecha_termino').val(data.fecha_termino);
                $('#edescripcion').val(data.descripcion);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText)
            });
        });

        $tableExperimentos.on("click", ".eliminarGrafico", function (e) {
            e.preventDefault();
            let id_experimento = $(this).data('id_experimento');
            Swal.fire({
                title: "Está seguro/a de eliminar el experimento?",
                showCancelButton: true,
                confirmButtonText: "Eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('eliminar_experimento') }}",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {id_experimento:id_experimento},
                    }).done(function (data, textStatus, jqXHR) {
                        if(data == 'ok'){
                            Swal.fire({
                                icon: 'success',
                                title: 'Experimento eliminado',
                                text: 'El Experimento fue eliminado correctamente',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            $tableExperimentos.bootstrapTable('refresh');
                        }
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR.responseText)
                    });
                }
            });
        });
        /*
        $("#importar").submit(function (event) {
            event.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: "",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
            }).done(function (data, textStatus, jqXHR) {

            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText)
            });
        });
        */
    </script>
@endpush
