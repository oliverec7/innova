<body>
    <div class="tab-pane fade show active" id="kpis" role="tabpanel" aria-labelledby="kpis-tab">
        <div class="alert alert-info mb-4 shadow-sm">
            <strong>Resumen de KPIs Principales:</strong> Indicadores clave de rendimiento para medir el éxito general de las operaciones de mantenimiento.
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-center align-items-center">
                        <div class="text-center">
                            <strong class="m-0 font-weight-bold text-primary">CUMPLIMIENTO DE INSPECCIONES PROGRAMADAS</strong>
                            <br class="text-center">Porcentaje de inspecciones realizadas vs programadas</br>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="InspeccionesChart"></canvas>
                        </div>
                    </div>
                    <div class="card-footer text-muted small d-flex justify-content-center">
                        <span>Actualizado: <script>document.write(new Date().toLocaleString('es-ES'));</script></span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-center align-items-center">
                        <div class="text-center">
                            <strong class="m-0 font-weight-bold text-primary">SOLICITUDES PENDIENTES VS RESUELTAS</strong>
                            <br class="text-muted">Volumen y eficiencia en gestión de solicitudes</br>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <div class="chart-container">
                                    <canvas id="SolicitudesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-muted small d-flex justify-content-center">
                        <span>Actualizado: <script>document.write(new Date().toLocaleString('es-ES'));</script></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script src="vista/js/kpis_principales.js"></script>
</body>