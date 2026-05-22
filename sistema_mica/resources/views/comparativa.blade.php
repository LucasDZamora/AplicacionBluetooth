@extends('layouts.app_graficos')

@section('content')
    <style>
        .ms-container {
            width: 100%;
        }
        .lds-roller {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 80px;
        }
        .lds-roller div {
            animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
            transform-origin: 40px 40px;
        }
        .lds-roller div:after {
            content: " ";
            display: block;
            position: absolute;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #000000;
            margin: -4px 0 0 -4px;
        }
        .lds-roller div:nth-child(1) {
            animation-delay: -0.036s;
        }
        .lds-roller div:nth-child(1):after {
            top: 63px;
            left: 63px;
        }
        .lds-roller div:nth-child(2) {
            animation-delay: -0.072s;
        }
        .lds-roller div:nth-child(2):after {
            top: 68px;
            left: 56px;
        }
        .lds-roller div:nth-child(3) {
            animation-delay: -0.108s;
        }
        .lds-roller div:nth-child(3):after {
            top: 71px;
            left: 48px;
        }
        .lds-roller div:nth-child(4) {
            animation-delay: -0.144s;
        }
        .lds-roller div:nth-child(4):after {
            top: 72px;
            left: 40px;
        }
        .lds-roller div:nth-child(5) {
            animation-delay: -0.18s;
        }
        .lds-roller div:nth-child(5):after {
            top: 71px;
            left: 32px;
        }
        .lds-roller div:nth-child(6) {
            animation-delay: -0.216s;
        }
        .lds-roller div:nth-child(6):after {
            top: 68px;
            left: 24px;
        }
        .lds-roller div:nth-child(7) {
            animation-delay: -0.252s;
        }
        .lds-roller div:nth-child(7):after {
            top: 63px;
            left: 17px;
        }
        .lds-roller div:nth-child(8) {
            animation-delay: -0.288s;
        }
        .lds-roller div:nth-child(8):after {
            top: 56px;
            left: 12px;
        }
        @keyframes lds-roller {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2>Comparativa entre Estaciones MICA</h2>
                <br>
                <p style="text-align: justify;padding-left: 50px;padding-right: 50px;">En esta sección podrás comparar las variables que recogen las distintas estaciones MICA que están distribuidas en los centros escolares del proyecto.
                    A la izquierda encontrarás el listado de estaciones que cuentan con datos registrados, las que podrás escoger para comparar.
                    Para escoger las estaciones que quieres comparar sólo debes seleccionarlas con el mouse, con lo cual aparecerán en el listado
                    de la derecha "Estaciones Seleccionadas". Cuando hayas seleccionado todas las estaciones que quieras comparar,
                    haz click en el botón "Comparar". </p>
                <br>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <h5 class="form-label text-center" >Estaciones Disponibles</h5>
            </div>
            <div class="col-6">
                <h5 class="form-label text-center" >Estaciones Seleccionadas</h5>
            </div>
        </div>
        <div class="row">
            <form id="comparar">
                <div class="col-lg-12">
                    <div class="mb-3">
                        <select multiple="multiple" class="searchable" id="emas" name="emas[]" required>
                            @foreach($estaciones as $estacion)
                                <option value='{{ $estacion->id_estacion }}'>{{ $estacion->rbd }}, {{ $estacion->establecimiento }}, {{ $estacion->establecimiento_comuna }} @if(isset($estacion->estaciones)) ({{ $estacion->estaciones }}) @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Comparar</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div id="graficos" style="text-align: center;"></div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $('.searchable').multiSelect({
            selectableHeader: "<input type='text' class='search-input form-control'  style='width: 100%;' autocomplete='off' placeholder=' Buscar...'>",
            selectionHeader: "<input type='text' class='search-input form-control' style='width: 100%;' autocomplete='off' placeholder=' Buscar...'>",

            afterInit: function(ms){
                var that = this,
                    $selectableSearch = that.$selectableUl.prev(),
                    $selectionSearch = that.$selectionUl.prev(),
                    selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                    selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                    .on('keydown', function(e){
                        if (e.which === 40){
                            that.$selectableUl.focus();
                            return false;
                        }
                    });

                that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                    .on('keydown', function(e){
                        if (e.which == 40){
                            that.$selectionUl.focus();
                            return false;
                        }
                    });
            },
            afterSelect: function(){
                this.qs1.cache();
                this.qs2.cache();
            },

            afterDeselect: function(){
                this.qs1.cache();
                this.qs2.cache();
            },
            selectableOptgroup: true
        });
        $("form#comparar").submit(function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            if ($('#emas').val().length < 2){
                Swal.fire({
                    icon: 'warning',
                    title: 'Debe seleccionar más de una estación',
                    //text: 'Experimento creado correctamente',
                    showConfirmButton: false,
                    timer: 2000
                });
                return false;
            }
            $("#graficos").html("<div class='lds-roller' style='margin-top: 70px;'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>");
            $.ajax({
                url: "{{ route('cargar-grafico') }}",
                type: "POST",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
            }).done(function (data, textStatus, jqXHR) {
                $("#graficos").html(data.graficohtml);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText)
            });
        });
    </script>
@endpush
