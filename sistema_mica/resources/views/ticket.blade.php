@extends('layouts.app_graficos')

@section('content')
    <style>
        .ms-container {
            width: 100%;
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 text-center mb-4">
                <h3>Ticket de Salida</h3>
            </div>
        </div>
        <div class="row">
            <form id="ticket">
                <div class="col-lg-8 mx-auto">
                    <div class="mb-3">
                        <label for="rut" class="form-label">Ingrese su Fecha de nacimiento</label>
                        <input type="date" class="form-control" id="fecha_nac" name="fecha_nac" required>
                    </div>
                </div>
                <div class="col-lg-8 mx-auto">
                    <div class="mb-3">
                        <label for="curso" class="form-label">Selecciona el curso</label>
                        <select class="form-select" id="curso" name="curso" aria-label="Seleccionar curso" required>
                            <option value="">Seleccionar</option>
                            <option value="¿CÓMO FUNCIONA EL SISTEMA CLIMÁTICO DE NUESTRO PLANETA? (Putaendo)">¿CÓMO FUNCIONA EL SISTEMA CLIMÁTICO DE NUESTRO PLANETA? (Putaendo)</option>
                            <option value="¿CÓMO FUNCIONA EL SISTEMA CLIMÁTICO DE NUESTRO PLANETA? (Villarrica)">¿CÓMO FUNCIONA EL SISTEMA CLIMÁTICO DE NUESTRO PLANETA? (Villarrica)</option>
                            <option value="TECNOLOGÍA COMO RECURSO PARA EL RAZONAMIENTO CIENTÍFICO: KIT MICA 3.0 (Santo Domingo)">TECNOLOGÍA COMO RECURSO PARA EL RAZONAMIENTO CIENTÍFICO: KIT MICA 3.0 (Santo Domingo)</option>
                            <option value="TECNOLOGÍA COMO RECURSO PARA EL RAZONAMIENTO CIENTÍFICO: KIT MICA 3.0 (Villarrica)">TECNOLOGÍA COMO RECURSO PARA EL RAZONAMIENTO CIENTÍFICO: KIT MICA 3.0 (Villarrica)</option>
                            <option value="PLANIFICAR LA ENSEÑANZA DEL CAMBIO CLIMÁTICO (Putaendo)">PLANIFICAR LA ENSEÑANZA DEL CAMBIO CLIMÁTICO (Putaendo)</option>
                            <option value="PLANIFICAR LA ENSEÑANZA DEL CAMBIO CLIMÁTICO (Villarrica)">PLANIFICAR LA ENSEÑANZA DEL CAMBIO CLIMÁTICO (Villarrica)</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-8 mx-auto">
                    <div class="mb-3">
                        <label for="p1" class="form-label">Comparte tu perspectiva acerca de la sesión del día de hoy ¿Cuáles son los principales aprendizajes?, ¿y dudas con las que te quedas?</label>
                        <textarea class="form-control" id="p1" name="p1" rows="4" required></textarea>
                    </div>
                </div>
                <div class="col-lg-8 mx-auto">
                    <div class="mb-3">
                        <label for="p2" class="form-label">¿Qué de lo visto en la sesión aplicaría en mi práctica?, ¿por qué y cómo lo haría?</label>
                        <textarea class="form-control" id="p2" name="p2" rows="4" required></textarea>
                    </div>
                </div>
                <div class="col-lg-12 text-center">
                    <button type="submit" class="btn btn-primary">Enviar Ticket</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $("form#ticket").submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            $.ajax({
                url: "{{ route('guardar-ticket') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
            }).done(function (data, textStatus, jqXHR) {
                if(data == 'ok'){
                    Swal.fire({
                        icon: "success",
                        title: "Ticket guardado",
                        text: "El ticket de salida se guardo correctamente.",
                        showConfirmButton: false,
                        timer: 3000
                    });
                    $('#ticket')[0].reset();
                    window.setTimeout( function(){
                        window.location = "https://www.etecc.cl/";
                    }, 2000 );
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText)
            });
        });
        $("form#ticket #rut")
            .rut({formatOn: 'keyup', validateOn: 'change'})
            .on('rutInvalido', function(){
                Swal.fire({
                    icon: "error",
                    title: "RUT Invalido",
                    text: "Ingrese el RUT correctamente."
                });
                $(this).val("");
            })
            .on('rutValido', function(){
                $(this).parents(".control-group").removeClass("error")
            });
    </script>
@endpush
