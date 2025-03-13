<?php
require_once 'BaseController.php';

class DiagnosticsController extends BaseController {
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
            // Si es odontólogo, mostrar solo sus diagnósticos
            $whereClause = '';
            $params = [];
            
            if (hasRole('odontologo')) {
                $whereClause = "WHERE d.odontologo_id = ?";
                $params[] = $_SESSION['user_id'];
            }

            $diagnosticos = $this->db->query(
                "SELECT d.*, 
                        p.nombre as paciente_nombre, 
                        p.apellidos as paciente_apellidos,
                        u.nombre as odontologo_nombre, 
                        u.apellidos as odontologo_apellidos,
                        (SELECT COUNT(*) FROM tratamientos t WHERE t.diagnostico_id = d.id) as total_tratamientos
                 FROM diagnosticos d
                 JOIN pacientes p ON d.paciente_id = p.id
                 JOIN usuarios u ON d.odontologo_id = u.id
                 $whereClause
                 ORDER BY d.fecha_diagnostico DESC",
                $params
            )->fetchAll();

            $this->render('diagnostics/index', [
                'diagnosticos' => $diagnosticos
            ]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->renderError(500, "Error al cargar la lista de diagnósticos");
        }
    }

    public function create() {
        try {
            // Obtener lista de pacientes para el selector
            $pacientes = $this->db->query(
                "SELECT id, nombre, apellidos FROM pacientes ORDER BY nombre, apellidos"
            )->fetchAll();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCSRFToken();
                $this->validateRequiredParams(['paciente_id', 'descripcion', 'odontograma']);

                // Sanitizar inputs
                $data = $this->sanitizeArray($_POST);
                
                // Validar que el paciente existe
                $paciente = $this->db->query(
                    "SELECT id FROM pacientes WHERE id = ?",
                    [$data['paciente_id']]
                )->fetch();

                if (!$paciente) {
                    throw new Exception("Paciente no encontrado");
                }

                // Validar el JSON del odontograma
                $odontograma = json_decode($data['odontograma'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception("Formato de odontograma inválido");
                }

                // Procesar imagen del diagnóstico si se proporcionó
                $imagen_diagnostico = null;
                if (isset($_FILES['imagen_diagnostico']) && $_FILES['imagen_diagnostico']['error'] === UPLOAD_ERR_OK) {
                    $imagen_diagnostico = $this->handleFileUpload($_FILES['imagen_diagnostico']);
                }

                // Insertar diagnóstico
                $diagnosticoId = $this->db->insert(
                    "INSERT INTO diagnosticos (
                        paciente_id, odontologo_id, descripcion, 
                        odontograma, imagen_diagnostico, estado
                    ) VALUES (?, ?, ?, ?, ?, 'activo')",
                    [
                        $data['paciente_id'],
                        $_SESSION['user_id'],
                        $data['descripcion'],
                        $data['odontograma'],
                        $imagen_diagnostico
                    ]
                );

                $this->log("Diagnóstico creado: ID $diagnosticoId");
                redirect('/?page=diagnostics&success=created');
            }

            $this->render('diagnostics/create', [
                'pacientes' => $pacientes
            ]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->render('diagnostics/create', [
                'error' => $e->getMessage(),
                'oldInput' => $_POST,
                'pacientes' => $pacientes ?? []
            ]);
        }
    }

    public function view($id = null) {
        $id = $id ?? ($_GET['id'] ?? null);
        if (!$id) {
            $this->renderError(400, "ID de diagnóstico no proporcionado");
            return;
        }

        try {
            // Obtener datos del diagnóstico
            $diagnostico = $this->db->query(
                "SELECT d.*, 
                        p.nombre as paciente_nombre, 
                        p.apellidos as paciente_apellidos,
                        u.nombre as odontologo_nombre, 
                        u.apellidos as odontologo_apellidos
                 FROM diagnosticos d
                 JOIN pacientes p ON d.paciente_id = p.id
                 JOIN usuarios u ON d.odontologo_id = u.id
                 WHERE d.id = ?",
                [$id]
            )->fetch();

            if (!$diagnostico) {
                $this->renderError(404, "Diagnóstico no encontrado");
                return;
            }

            // Si es odontólogo, verificar que sea el propietario del diagnóstico
            if (hasRole('odontologo') && $diagnostico['odontologo_id'] !== $_SESSION['user_id']) {
                $this->renderError(403, "No tiene permisos para ver este diagnóstico");
                return;
            }

            // Obtener tratamientos asociados
            $tratamientos = $this->db->query(
                "SELECT * FROM tratamientos WHERE diagnostico_id = ? ORDER BY fecha_inicio",
                [$id]
            )->fetchAll();

            $this->render('diagnostics/view', [
                'diagnostico' => $diagnostico,
                'tratamientos' => $tratamientos
            ]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->renderError(500, "Error al cargar el diagnóstico");
        }
    }

    public function edit($id = null) {
        $id = $id ?? ($_GET['id'] ?? null);
        if (!$id) {
            $this->renderError(400, "ID de diagnóstico no proporcionado");
            return;
        }

        try {
            // Obtener datos del diagnóstico
            $diagnostico = $this->db->query(
                "SELECT d.*, 
                        p.nombre as paciente_nombre, 
                        p.apellidos as paciente_apellidos
                 FROM diagnosticos d
                 JOIN pacientes p ON d.paciente_id = p.id
                 WHERE d.id = ?",
                [$id]
            )->fetch();

            if (!$diagnostico) {
                $this->renderError(404, "Diagnóstico no encontrado");
                return;
            }

            // Si es odontólogo, verificar que sea el propietario del diagnóstico
            if (hasRole('odontologo') && $diagnostico['odontologo_id'] !== $_SESSION['user_id']) {
                $this->renderError(403, "No tiene permisos para editar este diagnóstico");
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCSRFToken();
                $this->validateRequiredParams(['descripcion', 'odontograma']);

                // Sanitizar inputs
                $data = $this->sanitizeArray($_POST);

                // Validar el JSON del odontograma
                $odontograma = json_decode($data['odontograma'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception("Formato de odontograma inválido");
                }

                // Procesar nueva imagen si se proporcionó
                $imagen_diagnostico = $diagnostico['imagen_diagnostico'];
                if (isset($_FILES['imagen_diagnostico']) && $_FILES['imagen_diagnostico']['error'] === UPLOAD_ERR_OK) {
                    $imagen_diagnostico = $this->handleFileUpload($_FILES['imagen_diagnostico']);
                    // Eliminar imagen anterior si existe
                    if ($diagnostico['imagen_diagnostico']) {
                        @unlink(UPLOADS_PATH . '/' . $diagnostico['imagen_diagnostico']);
                    }
                }

                // Actualizar diagnóstico
                $this->db->update(
                    "UPDATE diagnosticos SET 
                        descripcion = ?, odontograma = ?, 
                        imagen_diagnostico = ?, estado = ?
                     WHERE id = ?",
                    [
                        $data['descripcion'],
                        $data['odontograma'],
                        $imagen_diagnostico,
                        $data['estado'],
                        $id
                    ]
                );

                $this->log("Diagnóstico actualizado: ID $id");
                redirect('/?page=diagnostics&success=updated');
            }

            $this->render('diagnostics/edit', [
                'diagnostico' => $diagnostico
            ]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->render('diagnostics/edit', [
                'error' => $e->getMessage(),
                'diagnostico' => $diagnostico ?? null
            ]);
        }
    }

    public function delete() {
        try {
            $this->validateCSRFToken();
            $id = $_POST['id'] ?? null;

            if (!$id) {
                throw new Exception("ID de diagnóstico no proporcionado");
            }

            // Verificar que el diagnóstico existe y obtener datos
            $diagnostico = $this->db->query(
                "SELECT * FROM diagnosticos WHERE id = ?",
                [$id]
            )->fetch();

            if (!$diagnostico) {
                throw new Exception("Diagnóstico no encontrado");
            }

            // Si es odontólogo, verificar que sea el propietario del diagnóstico
            if (hasRole('odontologo') && $diagnostico['odontologo_id'] !== $_SESSION['user_id']) {
                throw new Exception("No tiene permisos para eliminar este diagnóstico");
            }

            // Iniciar transacción
            $this->db->beginTransaction();

            try {
                // Eliminar tratamientos asociados
                $this->db->delete(
                    "DELETE FROM tratamientos WHERE diagnostico_id = ?",
                    [$id]
                );

                // Eliminar imagen si existe
                if ($diagnostico['imagen_diagnostico']) {
                    @unlink(UPLOADS_PATH . '/' . $diagnostico['imagen_diagnostico']);
                }

                // Eliminar diagnóstico
                $this->db->delete(
                    "DELETE FROM diagnosticos WHERE id = ?",
                    [$id]
                );

                $this->db->commit();
                $this->log("Diagnóstico eliminado: ID $id");
                $this->jsonResponse(['success' => true]);
            } catch (Exception $e) {
                $this->db->rollback();
                throw $e;
            }
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
            $estados_validos = ['activo', 'completado', 'cancelado'];
            if (!in_array($estado, $estados_validos)) {
                throw new Exception("Estado inválido");
            }

            // Verificar que el diagnóstico existe
            $diagnostico = $this->db->query(
                "SELECT * FROM diagnosticos WHERE id = ?",
                [$id]
            )->fetch();

            if (!$diagnostico) {
                throw new Exception("Diagnóstico no encontrado");
            }

            // Si es odontólogo, verificar que sea el propietario del diagnóstico
            if (hasRole('odontologo') && $diagnostico['odontologo_id'] !== $_SESSION['user_id']) {
                throw new Exception("No tiene permisos para modificar este diagnóstico");
            }

            // Actualizar estado
            $this->db->update(
                "UPDATE diagnosticos SET estado = ? WHERE id = ?",
                [$estado, $id]
            );

            $this->log("Estado de diagnóstico actualizado: ID $id, nuevo estado: $estado");
            $this->jsonResponse(['success' => true]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
