<body>
    <div class="tab-pane fade" id="efficiency" role="tabpanel" aria-labelledby="efficiency-tab">
        <div class="alert alert-info mb-4 shadow-sm">
            <strong>Eficiencia Operativa:</strong> Análisis de la productividad del equipo técnico y la rapidez en la atención de solicitudes (tiempos de respuesta y resolución).
        </div>
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-center align-items-center">
                        <div class="text-center">
                            <strong class="m-0 font-weight-bold text-primary">CARGA DE TRABAJO POR TÉCNICO</strong>
                            <br class="text-muted">Distribución y balance de asignaciones<br>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <div class="chart-container">
                                    <canvas id="CargaTecnicoChart"></canvas>
                                </div>
                            </div>
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
                            <strong class="m-0 font-weight-bold text-primary">TIEMPO DE RESPUESTA PROMEDIO</strong>
                            <br class="text-muted">Desde solicitud hasta asignación de técnico<br>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-12 text-center">
                                <div class="chart-container" id="gaugeContainer">
                                    <canvas id="TiempoRespuestaChart"></canvas>
                                    <div id="gaugeFallback" class="text-center"></div>
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
    <script src="vista/js/eficiencia_operativa.js"></script>
</body>