<?php
require_once 'BaseController.php';

class DashboardController extends BaseController {
    public function index() {
        try {
            // Obtener estadísticas generales
            $stats = $this->getGeneralStats();
            
            // Obtener citas del día
            $citasHoy = $this->getAppointmentsToday();
            
            // Obtener últimas actividades
            $ultimasActividades = $this->getRecentActivities();
            
            // Obtener citas próximas (próximos 7 días)
            $citasProximas = $this->getUpcomingAppointments();
            
            // Renderizar la vista con los datos
            $this->render('dashboard/index', [
                'stats' => $stats,
                'citasHoy' => $citasHoy,
                'ultimasActividades' => $ultimasActividades,
                'citasProximas' => $citasProximas
            ]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->renderError(500, "Error al cargar el dashboard");
        }
    }

    private function getGeneralStats() {
        try {
            // Total de pacientes
            $totalPacientes = $this->db->query(
                "SELECT COUNT(*) as total FROM pacientes WHERE estado = 1"
            )->fetch()['total'];

            // Total de citas pendientes
            $citasPendientes = $this->db->query(
                "SELECT COUNT(*) as total FROM citas 
                 WHERE estado IN ('programada', 'confirmada') 
                 AND fecha_hora >= CURRENT_DATE()"
            )->fetch()['total'];

            // Total de tratamientos en curso
            $tratamientosEnCurso = $this->db->query(
                "SELECT COUNT(*) as total FROM tratamientos 
                 WHERE estado = 'en_progreso'"
            )->fetch()['total'];

            // Total de diagnósticos realizados este mes
            $diagnosticosMes = $this->db->query(
                "SELECT COUNT(*) as total FROM diagnosticos 
                 WHERE MONTH(fecha_diagnostico) = MONTH(CURRENT_DATE()) 
                 AND YEAR(fecha_diagnostico) = YEAR(CURRENT_DATE())"
            )->fetch()['total'];

            return [
                'totalPacientes' => $totalPacientes,
                'citasPendientes' => $citasPendientes,
                'tratamientosEnCurso' => $tratamientosEnCurso,
                'diagnosticosMes' => $diagnosticosMes
            ];
        } catch (Exception $e) {
            $this->log("Error al obtener estadísticas: " . $e->getMessage(), 'error');
            return [
                'totalPacientes' => 0,
                'citasPendientes' => 0,
                'tratamientosEnCurso' => 0,
                'diagnosticosMes' => 0
            ];
        }
    }

    private function getAppointmentsToday() {
        try {
            return $this->db->query(
                "SELECT c.*, 
                        p.nombre as paciente_nombre, 
                        p.apellidos as paciente_apellidos,
                        u.nombre as odontologo_nombre, 
                        u.apellidos as odontologo_apellidos
                 FROM citas c
                 JOIN pacientes p ON c.paciente_id = p.id
                 JOIN usuarios u ON c.odontologo_id = u.id
                 WHERE DATE(c.fecha_hora) = CURRENT_DATE()
                 AND c.estado IN ('programada', 'confirmada')
                 ORDER BY c.fecha_hora ASC"
            )->fetchAll();
        } catch (Exception $e) {
            $this->log("Error al obtener citas del día: " . $e->getMessage(), 'error');
            return [];
        }
    }

    private function getRecentActivities() {
        try {
            // Unión de diferentes tipos de actividades recientes
            $sql = "
                (SELECT 
                    'diagnostico' as tipo,
                    d.fecha_diagnostico as fecha,
                    CONCAT('Diagnóstico realizado a ', p.nombre, ' ', p.apellidos) as descripcion
                FROM diagnosticos d
                JOIN pacientes p ON d.paciente_id = p.id
                ORDER BY d.fecha_diagnostico DESC
                LIMIT 5)
                
                UNION ALL
                
                (SELECT 
                    'tratamiento' as tipo,
                    t.fecha_inicio as fecha,
                    CONCAT('Nuevo tratamiento iniciado para ', p.nombre, ' ', p.apellidos) as descripcion
                FROM tratamientos t
                JOIN diagnosticos d ON t.diagnostico_id = d.id
                JOIN pacientes p ON d.paciente_id = p.id
                WHERE t.estado = 'en_progreso'
                ORDER BY t.fecha_inicio DESC
                LIMIT 5)
                
                UNION ALL
                
                (SELECT 
                    'cita' as tipo,
                    c.fecha_hora as fecha,
                    CONCAT('Cita programada: ', p.nombre, ' ', p.apellidos, ' con Dr. ', u.nombre) as descripcion
                FROM citas c
                JOIN pacientes p ON c.paciente_id = p.id
                JOIN usuarios u ON c.odontologo_id = u.id
                WHERE c.fecha_hora >= CURRENT_DATE()
                ORDER BY c.fecha_hora ASC
                LIMIT 5)
                
                ORDER BY fecha DESC
                LIMIT 10";

            return $this->db->query($sql)->fetchAll();
        } catch (Exception $e) {
            $this->log("Error al obtener actividades recientes: " . $e->getMessage(), 'error');
            return [];
        }
    }

    private function getUpcomingAppointments() {
        try {
            // Si es odontólogo, mostrar solo sus citas
            $whereClause = hasRole('odontologo') 
                ? "AND c.odontologo_id = " . $_SESSION['user_id']
                : "";

            return $this->db->query(
                "SELECT c.*, 
                        p.nombre as paciente_nombre, 
                        p.apellidos as paciente_apellidos,
                        u.nombre as odontologo_nombre, 
                        u.apellidos as odontologo_apellidos
                 FROM citas c
                 JOIN pacientes p ON c.paciente_id = p.id
                 JOIN usuarios u ON c.odontologo_id = u.id
                 WHERE c.fecha_hora BETWEEN CURRENT_DATE() AND DATE_ADD(CURRENT_DATE(), INTERVAL 7 DAY)
                 AND c.estado IN ('programada', 'confirmada')
                 $whereClause
                 ORDER BY c.fecha_hora ASC"
            )->fetchAll();
        } catch (Exception $e) {
            $this->log("Error al obtener citas próximas: " . $e->getMessage(), 'error');
            return [];
        }
    }

    public function getCalendarEvents() {
        try {
            // Si es odontólogo, mostrar solo sus citas
            $whereClause = hasRole('odontologo') 
                ? "AND c.odontologo_id = " . $_SESSION['user_id']
                : "";

            $citas = $this->db->query(
                "SELECT 
                    c.id,
                    c.fecha_hora as start,
                    DATE_ADD(c.fecha_hora, INTERVAL c.duracion MINUTE) as end,
                    CONCAT(p.nombre, ' ', p.apellidos) as title,
                    c.estado,
                    c.motivo as description,
                    CASE 
                        WHEN c.estado = 'programada' THEN '#3498db'
                        WHEN c.estado = 'confirmada' THEN '#2ecc71'
                        WHEN c.estado = 'completada' THEN '#95a5a6'
                        WHEN c.estado = 'cancelada' THEN '#e74c3c'
                    END as backgroundColor
                FROM citas c
                JOIN pacientes p ON c.paciente_id = p.id
                WHERE c.fecha_hora >= DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)
                $whereClause
                ORDER BY c.fecha_hora ASC"
            )->fetchAll();

            $this->jsonResponse($citas);
        } catch (Exception $e) {
            $this->log("Error al obtener eventos del calendario: " . $e->getMessage(), 'error');
            $this->jsonResponse(['error' => 'Error al cargar los eventos'], 500);
        }
    }
}
