<?php

class modeloController{

    //GLOBALES
    static function IniciarSesion(){
        require('modelo/IniciarSesion.php');
    }

    static function CerrarSesion(){
        require('modelo/CerrarSesion.php');
    }

    static function Login(){
        require_once("vista/modulos/login.php");
    }

    static function Inicio(){
        require_once("vista/modulos/inicio.php");
    }

    static function Perfil(){
        require_once("vista/modulos/perfil.php");
    }

    static function RenovarSesion(){
        require('modelo/RenovarSesion.php');
    }

    static function ValidarSesion(){
        require('modelo/ValidarSesion.php');
    }

    //ADMINISTRADOR
    static function EquiposAdmin(){
        require_once("vista/modulos/equipos_admin.php");
    }

    static function SolicitudesAdmin(){
        require_once("vista/modulos/solicitudes_admin.php");
    }

    static function OrdenesAdmin(){
        require_once("vista/modulos/ordenes_admin.php");
    }

    static function NotificacionesAdmin(){
        require_once("vista/modulos/notificaciones_admin.php");
    }

    static function InspeccionesAdmin(){
        require_once("vista/modulos/inspecciones_admin.php");
    }

    static function MantenimientosAdmin(){
        require_once("vista/modulos/mantenimientos_admin.php");
    }

    static function ReportesAdmin(){
        require_once("vista/modulos/reportes_admin.php");
    }

    //TECNICO
    static function NotificacionesTecnico(){
        require_once("vista/modulos/notificaciones_tecnico.php");
    }

    static function InspeccionesTecnico(){
        require_once("vista/modulos/inspecciones_tecnico.php");
    }

    static function MantenimientosTecnico(){
        require_once("vista/modulos/mantenimientos_tecnico.php");
    }

    //EMPLEADO
    static function EquiposEmpleado(){
        require_once("vista/modulos/equipos_empleado.php");
    }

    static function SolicitudesEmpleado(){
        require_once("vista/modulos/solicitudes_empleado.php");
    }

    static function NotificacionesEmpleado(){
        require_once("vista/modulos/notificaciones_empleado.php");
    }

    //REPORTES ADMIN
    static function ReporteInspecciones(){
        require_once("reportes/inspecciones.php");
    }

        static function ReporteSolicitudes(){
        require_once("reportes/solicitudes.php");
    } 

        static function ReporteCargaTrabajo(){
        require_once("reportes/carga_trabajo.php");
    } 

        static function ReporteTiempoRespuesta(){
        require_once("reportes/tiempo_respuesta.php");
    }

        static function ReporteFallasRecurrentes(){
        require_once("reportes/fallas_recurrentes.php");
    } 

        static function ReporteInspeccionesAtrasadas(){
        require_once("reportes/inspecciones_atrasadas.php");
    } 
        static function ReporteFallasMensuales(){
        require_once("reportes/fallas_mensuales.php");
    }

        static function ReporteSolicitudesArea(){
        require_once("reportes/solicitudes_area.php");
    }

}