<body>
   <div class="tab-pane fade" id="alerts" role="tabpanel" aria-labelledby="alerts-tab">
        <div class="alert alert-warning mb-4 shadow-sm">
            <strong>Alertas y Riesgos:</strong> Identificación y visualización de equipos críticos con fallas recurrentes e inspecciones que están fuera de plazo.
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-center align-items-center">
                        <div class="text-center">
                            <strong class="m-0 font-weight-bold text-danger">EQUIPOS CRÍTICOS POR FALLAS RECURRENTES</strong>
                            <br class="text-muted">Equipos con más de 3 fallas en el último trimestre</br>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="CriticosChart"></canvas>
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
                            <strong class="m-0 font-weight-bold text-warning">INSPECCIONES ATRASADAS POR ÁREA</strong>
                            <br class="text-muted">Número de inspecciones fuera de fecha programada</br>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="AtrasadasChart"></canvas>
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
    <script src="vista/js/alertas_riesgos.js"></script>
</body>