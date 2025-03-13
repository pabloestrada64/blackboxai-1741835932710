<?php
require_once 'BaseController.php';

class UsersController extends BaseController {
    public function __construct() {
        parent::__construct();
        // Verificar que solo el administrador pueda acceder
        $this->checkPermission('admin');
    }

    public function index() {
        try {
            $usuarios = $this->db->query(
                "SELECT id, nombre, apellidos, email, rol, estado, fecha_registro 
                 FROM usuarios 
                 ORDER BY fecha_registro DESC"
            )->fetchAll();

            $this->render('users/index', [
                'usuarios' => $usuarios
            ]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->renderError(500, "Error al cargar la lista de usuarios");
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRFToken();
                $this->validateRequiredParams(['nombre', 'apellidos', 'email', 'password', 'rol']);

                // Sanitizar inputs
                $nombre = sanitizeInput($_POST['nombre']);
                $apellidos = sanitizeInput($_POST['apellidos']);
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'];
                $rol = sanitizeInput($_POST['rol']);

                // Validar email único
                $existingUser = $this->db->query(
                    "SELECT id FROM usuarios WHERE email = ?",
                    [$email]
                )->fetch();

                if ($existingUser) {
                    throw new Exception("El correo electrónico ya está registrado");
                }

                // Procesar imagen de perfil si se proporcionó
                $imagen_perfil = null;
                if (isset($_FILES['imagen_perfil']) && $_FILES['imagen_perfil']['error'] === UPLOAD_ERR_OK) {
                    $imagen_perfil = $this->handleFileUpload($_FILES['imagen_perfil']);
                }

                // Insertar usuario
                $userId = $this->db->insert(
                    "INSERT INTO usuarios (nombre, apellidos, email, password, rol, imagen_perfil, estado) 
                     VALUES (?, ?, ?, ?, ?, ?, 1)",
                    [
                        $nombre,
                        $apellidos,
                        $email,
                        password_hash($password, PASSWORD_DEFAULT),
                        $rol,
                        $imagen_perfil
                    ]
                );

                $this->log("Usuario creado: ID $userId");
                redirect('/?page=users&success=created');
            } catch (Exception $e) {
                $this->log($e->getMessage(), 'error');
                $this->render('users/create', [
                    'error' => $e->getMessage(),
                    'oldInput' => $_POST
                ]);
                return;
            }
        }

        $this->render('users/create');
    }

    public function edit($id = null) {
        $id = $id ?? ($_GET['id'] ?? null);
        if (!$id) {
            $this->renderError(400, "ID de usuario no proporcionado");
            return;
        }

        try {
            $usuario = $this->db->query(
                "SELECT * FROM usuarios WHERE id = ?",
                [$id]
            )->fetch();

            if (!$usuario) {
                $this->renderError(404, "Usuario no encontrado");
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCSRFToken();
                $this->validateRequiredParams(['nombre', 'apellidos', 'email', 'rol']);

                // Sanitizar inputs
                $nombre = sanitizeInput($_POST['nombre']);
                $apellidos = sanitizeInput($_POST['apellidos']);
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $rol = sanitizeInput($_POST['rol']);

                // Validar email único (excepto para el usuario actual)
                $existingUser = $this->db->query(
                    "SELECT id FROM usuarios WHERE email = ? AND id != ?",
                    [$email, $id]
                )->fetch();

                if ($existingUser) {
                    throw new Exception("El correo electrónico ya está registrado");
                }

                // Procesar nueva imagen de perfil si se proporcionó
                $imagen_perfil = $usuario['imagen_perfil'];
                if (isset($_FILES['imagen_perfil']) && $_FILES['imagen_perfil']['error'] === UPLOAD_ERR_OK) {
                    $imagen_perfil = $this->handleFileUpload($_FILES['imagen_perfil']);
                    // Eliminar imagen anterior si existe
                    if ($usuario['imagen_perfil']) {
                        @unlink(UPLOADS_PATH . '/' . $usuario['imagen_perfil']);
                    }
                }

                // Actualizar usuario
                $this->db->update(
                    "UPDATE usuarios 
                     SET nombre = ?, apellidos = ?, email = ?, rol = ?, imagen_perfil = ?
                     WHERE id = ?",
                    [$nombre, $apellidos, $email, $rol, $imagen_perfil, $id]
                );

                // Si se proporcionó nueva contraseña, actualizarla
                if (!empty($_POST['password'])) {
                    $this->db->update(
                        "UPDATE usuarios SET password = ? WHERE id = ?",
                        [password_hash($_POST['password'], PASSWORD_DEFAULT), $id]
                    );
                }

                $this->log("Usuario actualizado: ID $id");
                redirect('/?page=users&success=updated');
            }

            $this->render('users/edit', [
                'usuario' => $usuario
            ]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->renderError(500, "Error al procesar la solicitud");
        }
    }

    public function delete() {
        try {
            $this->validateCSRFToken();
            $id = $_POST['id'] ?? null;

            if (!$id) {
                throw new Exception("ID de usuario no proporcionado");
            }

            // Verificar si el usuario existe
            $usuario = $this->db->query(
                "SELECT imagen_perfil FROM usuarios WHERE id = ?",
                [$id]
            )->fetch();

            if (!$usuario) {
                throw new Exception("Usuario no encontrado");
            }

            // Eliminar imagen de perfil si existe
            if ($usuario['imagen_perfil']) {
                @unlink(UPLOADS_PATH . '/' . $usuario['imagen_perfil']);
            }

            // Eliminar usuario
            $this->db->delete(
                "DELETE FROM usuarios WHERE id = ?",
                [$id]
            );

            $this->log("Usuario eliminado: ID $id");
            $this->jsonResponse(['success' => true]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function toggleStatus() {
        try {
            $this->validateCSRFToken();
            $id = $_POST['id'] ?? null;

            if (!$id) {
                throw new Exception("ID de usuario no proporcionado");
            }

            // Obtener estado actual
            $usuario = $this->db->query(
                "SELECT estado FROM usuarios WHERE id = ?",
                [$id]
            )->fetch();

            if (!$usuario) {
                throw new Exception("Usuario no encontrado");
            }

            // Cambiar estado
            $nuevoEstado = $usuario['estado'] ? 0 : 1;
            $this->db->update(
                "UPDATE usuarios SET estado = ? WHERE id = ?",
                [$nuevoEstado, $id]
            );

            $this->log("Estado de usuario actualizado: ID $id, nuevo estado: $nuevoEstado");
            $this->jsonResponse(['success' => true, 'newStatus' => $nuevoEstado]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function profile() {
        try {
            $usuario = $this->db->query(
                "SELECT * FROM usuarios WHERE id = ?",
                [$_SESSION['user_id']]
            )->fetch();

            if (!$usuario) {
                throw new Exception("Usuario no encontrado");
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCSRFToken();
                $this->validateRequiredParams(['nombre', 'apellidos', 'email']);

                // Sanitizar inputs
                $nombre = sanitizeInput($_POST['nombre']);
                $apellidos = sanitizeInput($_POST['apellidos']);
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

                // Validar email único (excepto para el usuario actual)
                $existingUser = $this->db->query(
                    "SELECT id FROM usuarios WHERE email = ? AND id != ?",
                    [$email, $_SESSION['user_id']]
                )->fetch();

                if ($existingUser) {
                    throw new Exception("El correo electrónico ya está registrado");
                }

                // Procesar nueva imagen de perfil si se proporcionó
                $imagen_perfil = $usuario['imagen_perfil'];
                if (isset($_FILES['imagen_perfil']) && $_FILES['imagen_perfil']['error'] === UPLOAD_ERR_OK) {
                    $imagen_perfil = $this->handleFileUpload($_FILES['imagen_perfil']);
                    // Eliminar imagen anterior si existe
                    if ($usuario['imagen_perfil']) {
                        @unlink(UPLOADS_PATH . '/' . $usuario['imagen_perfil']);
                    }
                }

                // Actualizar usuario
                $this->db->update(
                    "UPDATE usuarios 
                     SET nombre = ?, apellidos = ?, email = ?, imagen_perfil = ?
                     WHERE id = ?",
                    [$nombre, $apellidos, $email, $imagen_perfil, $_SESSION['user_id']]
                );

                // Si se proporcionó nueva contraseña, actualizarla
                if (!empty($_POST['password'])) {
                    $this->db->update(
                        "UPDATE usuarios SET password = ? WHERE id = ?",
                        [password_hash($_POST['password'], PASSWORD_DEFAULT), $_SESSION['user_id']]
                    );
                }

                // Actualizar datos de sesión
                $_SESSION['user_name'] = $nombre . ' ' . $apellidos;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_image'] = $imagen_perfil;

                $this->log("Perfil actualizado: ID " . $_SESSION['user_id']);
                redirect('/?page=users&action=profile&success=updated');
            }

            $this->render('users/profile', [
                'usuario' => $usuario
            ]);
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->renderError(500, "Error al procesar la solicitud");
        }
    }
}
