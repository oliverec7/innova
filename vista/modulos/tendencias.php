<body>
    <div class="tab-pane fade" id="trends" role="tabpanel" aria-labelledby="trends-tab">
        <div class="alert alert-info mb-4 shadow-sm">
            <strong>Análisis de Tendencias:</strong> Gráficos de series de tiempo y distribuciones para identificar patrones de fallas, costos y carga laboral a largo plazo.
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-center align-items-center">
                        <div class="text-center">
                            <strong class="m-0 font-weight-bold text-primary">TENDENCIAS DE FALLAS MENSUALES</strong>
                            <br class="text-muted">Línea de tiempo mostrando patrones de fallas en el último año</br>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="FallasMensualesChart"></canvas>
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
                            <strong class="m-0 font-weight-bold text-primary">DISTRIBUCIÓN DE SOLICITUDES POR ÁREA</strong>
                            <br class="text-muted">Gráfico de dona mostrando el origen de las solicitudes</br>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="SolicitudesAreaChart"></canvas>
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
    <script src="vista/js/tendencias.js"></script>
</body>