@extends('layouts.app')

@section('title', 'Nueva Póliza')

@section('header')
    <h2 class="page-title">Nueva Póliza Contable</h2>
    <div class="text-muted">Registrar movimiento contable (Debe = Haber)</div>
@endsection

@section('content')
<form action="{{ route('polizas.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información General</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Fecha</label>
                            <input type="date" name="fecha" class="form-control" value="{{ old('fecha', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Tipo</label>
                            <select name="tipo" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <option value="INGRESO">Ingreso</option>
                                <option value="EGRESO">Egreso</option>
                                <option value="DIARIO">Diario</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label required">Concepto</label>
                            <textarea name="concepto" class="form-control" rows="2" required></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Detalle Contable</h3>
                    <div class="card-actions">
                        <button type="button" class="btn btn-sm btn-primary" onclick="agregarDetalle()">+ Agregar línea</button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table" id="tablaDetalles">
                        <thead>
                            <tr>
                                <th width="25%">Cuenta</th>
                                <th width="35%">Descripción</th>
                                <th width="15%">Debe</th>
                                <th width="15%">Haber</th>
                                <th width="10%"></th>
                            </tr>
                        </thead>
                        <tbody id="detallesBody"></tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="2" class="text-end">TOTALES:</td>
                                <td><span id="totalDebe">$0.00</span></td>
                                <td><span id="totalHaber">$0.00</span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('polizas.index') }}" class="btn btn-link">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar Póliza</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
let detalleIndex = 0;

function agregarDetalle() {
    const html = '<tr>' +
        '<td><input type="text" name="detalles[' + detalleIndex + '][cuenta]" class="form-control" required></td>' +
        '<td><input type="text" name="detalles[' + detalleIndex + '][descripcion]" class="form-control" required></td>' +
        '<td><input type="number" name="detalles[' + detalleIndex + '][debe]" class="form-control debe-input" step="0.01" value="0" onchange="calcularTotales()"></td>' +
        '<td><input type="number" name="detalles[' + detalleIndex + '][haber]" class="form-control haber-input" step="0.01" value="0" onchange="calcularTotales()"></td>' +
        '<td><button type="button" class="btn btn-sm btn-danger" onclick="eliminarDetalle(this)">×</button></td>' +
        '</tr>';
    document.getElementById('detallesBody').insertAdjacentHTML('beforeend', html);
    detalleIndex++;
}

function eliminarDetalle(btn) {
    btn.closest('tr').remove();
    calcularTotales();
}

function calcularTotales() {
    let totalDebe = 0;
    let totalHaber = 0;
    
    document.querySelectorAll('.debe-input').forEach(input => {
        totalDebe += parseFloat(input.value) || 0;
    });
    
    document.querySelectorAll('.haber-input').forEach(input => {
        totalHaber += parseFloat(input.value) || 0;
    });
    
    document.getElementById('totalDebe').textContent = '$' + totalDebe.toFixed(2);
    document.getElementById('totalHaber').textContent = '$' + totalHaber.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    agregarDetalle();
    agregarDetalle();
});
</script>
@endpush
