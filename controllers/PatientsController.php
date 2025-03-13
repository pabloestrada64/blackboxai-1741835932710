<?php
require_once 'BaseController.php';

class PatientsController extends BaseController {
    public function index() {
        try {
            // Si es odontólogo, mostrar solo sus pacientes asignados
            $whereClause = '';
            $params = [];
            
            if (hasRole('odontologo')) {
                $whereClause = "WHERE EXISTS (
                    SELECT 1 FROM citas c 
                    WHERE c.paciente_id = p.id 
                    AND c.odontologo_id = ?
                )";
                $params[] = $_SESSION['user_id'];
            }

            $pacientes = $this->db->query(
                "SELECT p.*, 
                        (SELECT COUNT(*) FROM citas c WHERE c.paciente_id = p.id) as total_citas,
                        (SELECT COUNT(*) FROM diagnosticos d WHERE d.paciente_id = p.id) as total_diagnosticos,
                        (SELECT COUNT(*) FROM tratamientos t 
                         INNER JOIN diagnosticos d ON t.diagnostico_id = d.id 
                         WHERE d.paciente_id = p.id AND t.estado = 'en_progreso') as tratamientos_activos
                 FROM pacientes p
                 $whereClause
                 ORDER BY p.fecha_registro DESC",
                $params
            )->fetchAll();

            $this->render('patients/index', [
                'pacientes' => $pacientes
            ]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->renderError(500, "Error al cargar la lista de pacientes");
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRFToken();
                $this->validateRequiredParams([
                    'nombre', 'apellidos', 'fecha_nacimiento', 
                    'genero', 'telefono', 'direccion'
                ]);

                // Sanitizar inputs
                $data = $this->sanitizeArray($_POST);

                // Validar fecha de nacimiento
                if (!strtotime($data['fecha_nacimiento'])) {
                    throw new Exception("Fecha de nacimiento inválida");
                }

                // Validar email si se proporcionó
                if (!empty($data['email'])) {
                    $existingPatient = $this->db->query(
                        "SELECT id FROM pacientes WHERE email = ?",
                        [$data['email']]
                    )->fetch();

                    if ($existingPatient) {
                        throw new Exception("El correo electrónico ya está registrado");
                    }
                }

                // Procesar imagen de perfil si se proporcionó
                $imagen_perfil = null;
                if (isset($_FILES['imagen_perfil']) && $_FILES['imagen_perfil']['error'] === UPLOAD_ERR_OK) {
                    $imagen_perfil = $this->handleFileUpload($_FILES['imagen_perfil']);
                }

                // Insertar paciente
                $pacienteId = $this->db->insert(
                    "INSERT INTO pacientes (
                        nombre, apellidos, fecha_nacimiento, genero, 
                        telefono, email, direccion, imagen_perfil
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $data['nombre'],
                        $data['apellidos'],
                        $data['fecha_nacimiento'],
                        $data['genero'],
                        $data['telefono'],
                        $data['email'] ?: null,
                        $data['direccion'],
                        $imagen_perfil
                    ]
                );

                // Crear historial médico inicial
                $this->db->insert(
                    "INSERT INTO historiales_medicos (
                        paciente_id, tipo_sangre, alergias, 
                        enfermedades_cronicas, medicamentos_actuales, 
                        antecedentes_familiares
                    ) VALUES (?, ?, ?, ?, ?, ?)",
                    [
                        $pacienteId,
                        $data['tipo_sangre'] ?? null,
                        $data['alergias'] ?? null,
                        $data['enfermedades_cronicas'] ?? null,
                        $data['medicamentos_actuales'] ?? null,
                        $data['antecedentes_familiares'] ?? null
                    ]
                );

                $this->log("Paciente creado: ID $pacienteId");
                redirect('/?page=patients&success=created');
            } catch (Exception $e) {
                $this->log($e->getMessage(), 'error');
                $this->render('patients/create', [
                    'error' => $e->getMessage(),
                    'oldInput' => $_POST
                ]);
                return;
            }
        }

        $this->render('patients/create');
    }

    public function edit($id = null) {
        $id = $id ?? ($_GET['id'] ?? null);
        if (!$id) {
            $this->renderError(400, "ID de paciente no proporcionado");
            return;
        }

        try {
            // Obtener datos del paciente y su historial médico
            $paciente = $this->db->query(
                "SELECT p.*, h.* 
                 FROM pacientes p
                 LEFT JOIN historiales_medicos h ON p.id = h.paciente_id
                 WHERE p.id = ?",
                [$id]
            )->fetch();

            if (!$paciente) {
                $this->renderError(404, "Paciente no encontrado");
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCSRFToken();
                $this->validateRequiredParams([
                    'nombre', 'apellidos', 'fecha_nacimiento', 
                    'genero', 'telefono', 'direccion'
                ]);

                // Sanitizar inputs
                $data = $this->sanitizeArray($_POST);

                // Validar fecha de nacimiento
                if (!strtotime($data['fecha_nacimiento'])) {
                    throw new Exception("Fecha de nacimiento inválida");
                }

                // Validar email si se proporcionó
                if (!empty($data['email'])) {
                    $existingPatient = $this->db->query(
                        "SELECT id FROM pacientes WHERE email = ? AND id != ?",
                        [$data['email'], $id]
                    )->fetch();

                    if ($existingPatient) {
                        throw new Exception("El correo electrónico ya está registrado");
                    }
                }

                // Procesar nueva imagen de perfil si se proporcionó
                $imagen_perfil = $paciente['imagen_perfil'];
                if (isset($_FILES['imagen_perfil']) && $_FILES['imagen_perfil']['error'] === UPLOAD_ERR_OK) {
                    $imagen_perfil = $this->handleFileUpload($_FILES['imagen_perfil']);
                    // Eliminar imagen anterior si existe
                    if ($paciente['imagen_perfil']) {
                        @unlink(UPLOADS_PATH . '/' . $paciente['imagen_perfil']);
                    }
                }

                // Iniciar transacción
                $this->db->beginTransaction();

                try {
                    // Actualizar paciente
                    $this->db->update(
                        "UPDATE pacientes SET 
                            nombre = ?, apellidos = ?, fecha_nacimiento = ?,
                            genero = ?, telefono = ?, email = ?,
                            direccion = ?, imagen_perfil = ?
                         WHERE id = ?",
                        [
                            $data['nombre'],
                            $data['apellidos'],
                            $data['fecha_nacimiento'],
                            $data['genero'],
                            $data['telefono'],
                            $data['email'] ?: null,
                            $data['direccion'],
                            $imagen_perfil,
                            $id
                        ]
                    );

                    // Actualizar historial médico
                    $this->db->update(
                        "UPDATE historiales_medicos SET 
                            tipo_sangre = ?, alergias = ?,
                            enfermedades_cronicas = ?, medicamentos_actuales = ?,
                            antecedentes_familiares = ?
                         WHERE paciente_id = ?",
                        [
                            $data['tipo_sangre'] ?? null,
                            $data['alergias'] ?? null,
                            $data['enfermedades_cronicas'] ?? null,
                            $data['medicamentos_actuales'] ?? null,
                            $data['antecedentes_familiares'] ?? null,
                            $id
                        ]
                    );

                    $this->db->commit();
                    $this->log("Paciente actualizado: ID $id");
                    redirect('/?page=patients&success=updated');
                } catch (Exception $e) {
                    $this->db->rollback();
                    throw $e;
                }
            }

            $this->render('patients/edit', [
                'paciente' => $paciente
            ]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->renderError(500, "Error al procesar la solicitud");
        }
    }

    public function view($id = null) {
        $id = $id ?? ($_GET['id'] ?? null);
        if (!$id) {
            $this->renderError(400, "ID de paciente no proporcionado");
            return;
        }

        try {
            // Obtener datos completos del paciente
            $paciente = $this->db->query(
                "SELECT p.*, h.*,
                        (SELECT COUNT(*) FROM citas c WHERE c.paciente_id = p.id) as total_citas,
                        (SELECT COUNT(*) FROM diagnosticos d WHERE d.paciente_id = p.id) as total_diagnosticos
                 FROM pacientes p
                 LEFT JOIN historiales_medicos h ON p.id = h.paciente_id
                 WHERE p.id = ?",
                [$id]
            )->fetch();

            if (!$paciente) {
                $this->renderError(404, "Paciente no encontrado");
                return;
            }

            // Obtener citas del paciente
            $citas = $this->db->query(
                "SELECT c.*, u.nombre as odontologo_nombre, u.apellidos as odontologo_apellidos
                 FROM citas c
                 JOIN usuarios u ON c.odontologo_id = u.id
                 WHERE c.paciente_id = ?
                 ORDER BY c.fecha_hora DESC",
                [$id]
            )->fetchAll();

            // Obtener diagnósticos y tratamientos
            $diagnosticos = $this->db->query(
                "SELECT d.*, u.nombre as odontologo_nombre, u.apellidos as odontologo_apellidos,
                        (SELECT COUNT(*) FROM tratamientos t WHERE t.diagnostico_id = d.id) as total_tratamientos
                 FROM diagnosticos d
                 JOIN usuarios u ON d.odontologo_id = u.id
                 WHERE d.paciente_id = ?
                 ORDER BY d.fecha_diagnostico DESC",
                [$id]
            )->fetchAll();

            $this->render('patients/view', [
                'paciente' => $paciente,
                'citas' => $citas,
                'diagnosticos' => $diagnosticos
            ]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->renderError(500, "Error al cargar los datos del paciente");
        }
    }

    public function delete() {
        try {
            $this->validateCSRFToken();
            $id = $_POST['id'] ?? null;

            if (!$id) {
                throw new Exception("ID de paciente no proporcionado");
            }

            // Verificar si el paciente existe y obtener su imagen
            $paciente = $this->db->query(
                "SELECT imagen_perfil FROM pacientes WHERE id = ?",
                [$id]
            )->fetch();

            if (!$paciente) {
                throw new Exception("Paciente no encontrado");
            }

            // Eliminar imagen de perfil si existe
            if ($paciente['imagen_perfil']) {
                @unlink(UPLOADS_PATH . '/' . $paciente['imagen_perfil']);
            }

            // Eliminar paciente y sus registros relacionados
            $this->db->beginTransaction();

            try {
                // Eliminar historial médico
                $this->db->delete(
                    "DELETE FROM historiales_medicos WHERE paciente_id = ?",
                    [$id]
                );

                // Eliminar diagnósticos y tratamientos
                $diagnosticos = $this->db->query(
                    "SELECT id FROM diagnosticos WHERE paciente_id = ?",
                    [$id]
                )->fetchAll();

                foreach ($diagnosticos as $diagnostico) {
                    $this->db->delete(
                        "DELETE FROM tratamientos WHERE diagnostico_id = ?",
                        [$diagnostico['id']]
                    );
                }

                $this->db->delete(
                    "DELETE FROM diagnosticos WHERE paciente_id = ?",
                    [$id]
                );

                // Eliminar citas
                $this->db->delete(
                    "DELETE FROM citas WHERE paciente_id = ?",
                    [$id]
                );

                // Finalmente, eliminar el paciente
                $this->db->delete(
                    "DELETE FROM pacientes WHERE id = ?",
                    [$id]
                );

                $this->db->commit();
                $this->log("Paciente eliminado: ID $id");
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
}
