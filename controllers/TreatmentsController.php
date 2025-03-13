<?php
require_once 'BaseController.php';

class TreatmentsController extends BaseController {
    public function __construct() {
        parent::__construct();
        // Verificar que solo odontólogos y administradores puedan acceder
        if (!hasRole('odontologo') && !hasRole('admin')) {
            $this->renderError(403, "No tiene permisos para acceder a esta sección");
            exit();
        }
    }

    public function index() {
        try {
            // Si es odontólogo, mostrar solo sus tratamientos
            $whereClause = '';
            $params = [];
            
            if (hasRole('odontologo')) {
                $whereClause = "WHERE d.odontologo_id = ?";
                $params[] = $_SESSION['user_id'];
            }

            $tratamientos = $this->db->query(
                "SELECT t.*, 
                        d.descripcion as diagnostico_descripcion,
                        p.nombre as paciente_nombre, 
                        p.apellidos as paciente_apellidos,
                        u.nombre as odontologo_nombre, 
                        u.apellidos as odontologo_apellidos
                 FROM tratamientos t
                 JOIN diagnosticos d ON t.diagnostico_id = d.id
                 JOIN pacientes p ON d.paciente_id = p.id
                 JOIN usuarios u ON d.odontologo_id = u.id
                 $whereClause
                 ORDER BY t.fecha_inicio DESC",
                $params
            )->fetchAll();

            $this->render('treatments/index', [
                'tratamientos' => $tratamientos
            ]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->renderError(500, "Error al cargar la lista de tratamientos");
        }
    }

    public function create() {
        try {
            // Obtener diagnóstico si se proporciona ID
            $diagnosticoId = $_GET['diagnostic_id'] ?? null;
            $diagnostico = null;
            
            if ($diagnosticoId) {
                $diagnostico = $this->db->query(
                    "SELECT d.*, 
                            p.nombre as paciente_nombre, 
                            p.apellidos as paciente_apellidos
                     FROM diagnosticos d
                     JOIN pacientes p ON d.paciente_id = p.id
                     WHERE d.id = ?",
                    [$diagnosticoId]
                )->fetch();

                if (!$diagnostico) {
                    throw new Exception("Diagnóstico no encontrado");
                }

                // Verificar que el diagnóstico esté activo
                if ($diagnostico['estado'] !== 'activo') {
                    throw new Exception("No se pueden agregar tratamientos a un diagnóstico " . $diagnostico['estado']);
                }

                // Si es odontólogo, verificar que sea el propietario del diagnóstico
                if (hasRole('odontologo') && $diagnostico['odontologo_id'] !== $_SESSION['user_id']) {
                    throw new Exception("No tiene permisos para agregar tratamientos a este diagnóstico");
                }
            } else {
                // Si no se proporciona diagnóstico, obtener lista de diagnósticos activos
                $whereClause = hasRole('odontologo') ? "AND d.odontologo_id = " . $_SESSION['user_id'] : "";
                
                $diagnosticos = $this->db->query(
                    "SELECT d.id, d.descripcion, 
                            p.nombre as paciente_nombre, 
                            p.apellidos as paciente_apellidos
                     FROM diagnosticos d
                     JOIN pacientes p ON d.paciente_id = p.id
                     WHERE d.estado = 'activo' $whereClause
                     ORDER BY d.fecha_diagnostico DESC"
                )->fetchAll();
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCSRFToken();
                $this->validateRequiredParams(['diagnostico_id', 'tipo', 'descripcion']);

                // Sanitizar inputs
                $data = $this->sanitizeArray($_POST);

                // Validar que el diagnóstico existe y está activo
                $diagnostico = $this->db->query(
                    "SELECT * FROM diagnosticos WHERE id = ? AND estado = 'activo'",
                    [$data['diagnostico_id']]
                )->fetch();

                if (!$diagnostico) {
                    throw new Exception("Diagnóstico no encontrado o no está activo");
                }

                // Si es odontólogo, verificar que sea el propietario del diagnóstico
                if (hasRole('odontologo') && $diagnostico['odontologo_id'] !== $_SESSION['user_id']) {
                    throw new Exception("No tiene permisos para agregar tratamientos a este diagnóstico");
                }

                // Insertar tratamiento
                $tratamientoId = $this->db->insert(
                    "INSERT INTO tratamientos (
                        diagnostico_id, tipo, descripcion, 
                        fecha_inicio, costo, estado
                    ) VALUES (?, ?, ?, ?, ?, 'pendiente')",
                    [
                        $data['diagnostico_id'],
                        $data['tipo'],
                        $data['descripcion'],
                        $data['fecha_inicio'] ?? date('Y-m-d'),
                        $data['costo'] ?? 0
                    ]
                );

                $this->log("Tratamiento creado: ID $tratamientoId");
                redirect('/?page=treatments&success=created');
            }

            $this->render('treatments/create', [
                'diagnostico' => $diagnostico,
                'diagnosticos' => $diagnosticos ?? []
            ]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->render('treatments/create', [
                'error' => $e->getMessage(),
                'oldInput' => $_POST,
                'diagnostico' => $diagnostico ?? null,
                'diagnosticos' => $diagnosticos ?? []
            ]);
        }
    }

    public function view($id = null) {
        $id = $id ?? ($_GET['id'] ?? null);
        if (!$id) {
            $this->renderError(400, "ID de tratamiento no proporcionado");
            return;
        }

        try {
            // Obtener datos del tratamiento
            $tratamiento = $this->db->query(
                "SELECT t.*, 
                        d.descripcion as diagnostico_descripcion,
                        p.nombre as paciente_nombre, 
                        p.apellidos as paciente_apellidos,
                        u.nombre as odontologo_nombre, 
                        u.apellidos as odontologo_apellidos
                 FROM tratamientos t
                 JOIN diagnosticos d ON t.diagnostico_id = d.id
                 JOIN pacientes p ON d.paciente_id = p.id
                 JOIN usuarios u ON d.odontologo_id = u.id
                 WHERE t.id = ?",
                [$id]
            )->fetch();

            if (!$tratamiento) {
                $this->renderError(404, "Tratamiento no encontrado");
                return;
            }

            // Si es odontólogo, verificar que sea el propietario del diagnóstico
            if (hasRole('odontologo') && $tratamiento['odontologo_id'] !== $_SESSION['user_id']) {
                $this->renderError(403, "No tiene permisos para ver este tratamiento");
                return;
            }

            $this->render('treatments/view', [
                'tratamiento' => $tratamiento
            ]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->renderError(500, "Error al cargar el tratamiento");
        }
    }

    public function edit($id = null) {
        $id = $id ?? ($_GET['id'] ?? null);
        if (!$id) {
            $this->renderError(400, "ID de tratamiento no proporcionado");
            return;
        }

        try {
            // Obtener datos del tratamiento
            $tratamiento = $this->db->query(
                "SELECT t.*, 
                        d.descripcion as diagnostico_descripcion,
                        d.odontologo_id,
                        p.nombre as paciente_nombre, 
                        p.apellidos as paciente_apellidos
                 FROM tratamientos t
                 JOIN diagnosticos d ON t.diagnostico_id = d.id
                 JOIN pacientes p ON d.paciente_id = p.id
                 WHERE t.id = ?",
                [$id]
            )->fetch();

            if (!$tratamiento) {
                $this->renderError(404, "Tratamiento no encontrado");
                return;
            }

            // Si es odontólogo, verificar que sea el propietario del diagnóstico
            if (hasRole('odontologo') && $tratamiento['odontologo_id'] !== $_SESSION['user_id']) {
                $this->renderError(403, "No tiene permisos para editar este tratamiento");
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCSRFToken();
                $this->validateRequiredParams(['tipo', 'descripcion']);

                // Sanitizar inputs
                $data = $this->sanitizeArray($_POST);

                // Actualizar tratamiento
                $this->db->update(
                    "UPDATE tratamientos SET 
                        tipo = ?, descripcion = ?, 
                        fecha_inicio = ?, fecha_fin = ?,
                        costo = ?, estado = ?
                     WHERE id = ?",
                    [
                        $data['tipo'],
                        $data['descripcion'],
                        $data['fecha_inicio'],
                        $data['fecha_fin'] ?: null,
                        $data['costo'] ?? 0,
                        $data['estado'],
                        $id
                    ]
                );

                $this->log("Tratamiento actualizado: ID $id");
                redirect('/?page=treatments&success=updated');
            }

            $this->render('treatments/edit', [
                'tratamiento' => $tratamiento
            ]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->render('treatments/edit', [
                'error' => $e->getMessage(),
                'tratamiento' => $tratamiento ?? null
            ]);
        }
    }

    public function delete() {
        try {
            $this->validateCSRFToken();
            $id = $_POST['id'] ?? null;

            if (!$id) {
                throw new Exception("ID de tratamiento no proporcionado");
            }

            // Verificar que el tratamiento existe
            $tratamiento = $this->db->query(
                "SELECT t.*, d.odontologo_id 
                 FROM tratamientos t
                 JOIN diagnosticos d ON t.diagnostico_id = d.id
                 WHERE t.id = ?",
                [$id]
            )->fetch();

            if (!$tratamiento) {
                throw new Exception("Tratamiento no encontrado");
            }

            // Si es odontólogo, verificar que sea el propietario del diagnóstico
            if (hasRole('odontologo') && $tratamiento['odontologo_id'] !== $_SESSION['user_id']) {
                throw new Exception("No tiene permisos para eliminar este tratamiento");
            }

            // Eliminar tratamiento
            $this->db->delete(
                "DELETE FROM tratamientos WHERE id = ?",
                [$id]
            );

            $this->log("Tratamiento eliminado: ID $id");
            $this->jsonResponse(['success' => true]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus() {
        try {
            $this->validateCSRFToken();
            $id = $_POST['id'] ?? null;
            $estado = $_POST['estado'] ?? null;

            if (!$id || !$estado) {
                throw new Exception("Parámetros incompletos");
            }

            // Verificar estado válido
            $estados_validos = ['pendiente', 'en_progreso', 'completado', 'cancelado'];
            if (!in_array($estado, $estados_validos)) {
                throw new Exception("Estado inválido");
            }

            // Verificar que el tratamiento existe
            $tratamiento = $this->db->query(
                "SELECT t.*, d.odontologo_id 
                 FROM tratamientos t
                 JOIN diagnosticos d ON t.diagnostico_id = d.id
                 WHERE t.id = ?",
                [$id]
            )->fetch();

            if (!$tratamiento) {
                throw new Exception("Tratamiento no encontrado");
            }

            // Si es odontólogo, verificar que sea el propietario del diagnóstico
            if (hasRole('odontologo') && $tratamiento['odontologo_id'] !== $_SESSION['user_id']) {
                throw new Exception("No tiene permisos para modificar este tratamiento");
            }

            // Si se está completando el tratamiento, establecer fecha de fin
            $fecha_fin = ($estado === 'completado') ? date('Y-m-d') : null;

            // Actualizar estado
            $this->db->update(
                "UPDATE tratamientos SET estado = ?, fecha_fin = ? WHERE id = ?",
                [$estado, $fecha_fin, $id]
            );

            $this->log("Estado de tratamiento actualizado: ID $id, nuevo estado: $estado");
            $this->jsonResponse(['success' => true]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
