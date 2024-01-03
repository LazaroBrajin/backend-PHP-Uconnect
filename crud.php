<?php

// Database connection parameters
$servername = "your_servername";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable CORS (Cross-Origin Resource Sharing)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// Check HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// CRUD operations
switch ($method) {
    case 'GET':
        if (isset($_GET['action'])) {
            $action = $_GET['action'];

            switch ($action) {
                case 'get_incidencias':
                    get_incidencias($conn);
                    break;
                
                case 'get_incidencias_by_personaid':
                    get_incidencias_by_personaid($conn, $_GET['Personaid']);
                    break;

                case 'get_persona':
                    get_persona($conn, $_GET['id']);
                    break;

                case 'get_personas_by_departamento':
                    get_personas_by_departamento($conn, $_GET['Departamentoid']);
                    break;

                case 'get_personas_by_empresa':
                    get_personas_by_empresa($conn, $_GET['Empresaid']);
                    break;

                case 'get_rols':
                    get_rols($conn);
                    break;
                
                case 'get_login':
                    get_login($conn,$_GET['id']);
                    break;

                // Add more cases for other actions

                default:
                    echo "Invalid action";
                    break;
            }
        } else {
            echo "No action specified";
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['action'])) {
            $action = $data['action'];

            switch ($action) {
                case 'login':
                    login($conn, $_POST['data']);
                    break;

                case 'create_incidencia':
                    create_incidencia($conn, $_POST['data']);
                    break;

                case 'create_persona':
                    create_persona($conn, $_POST['data']);
                    break;

                case 'create_rol':
                    create_rol($conn, $_POST['data']);
                    break;

                case 'create_login':
                    create_login($conn, $_POST['data']);
                    break;

                // Add more cases for other actions

                default:
                    echo "Invalid action";
                    break;
            }
        } else {
            echo "No action specified";
        }
        break;

    case 'PUT':
        // Implement PUT operations here
        parse_str(file_get_contents("php://input"), $_PUT);

        if (isset($_PUT['action'])) {
            $action = $_PUT['action'];

            switch ($action) {
                case 'update_persona':
                    update_persona($conn,$_PUT['id'],$_PUT['data']);
                    break;

                case 'update_rol':
                    update_rol($conn,$_PUT['id'],$_PUT['data']);
                    break;

                case 'update_departamento':
                    update_departamento($conn,$_PUT['id'],$_PUT['data']);
                    break;

                case 'update_empresa':
                    update_empresa($conn,$_PUT['id'],$_PUT['data']);
                    break;

                case 'update_incidencia':
                    update_incidencia($conn,$_PUT['id'],$_PUT['data']);
                    break;

                case 'update_multimedia':
                    update_multimedia($conn,$_PUT['id'],$_PUT['data']);
                    break;
                case 'update_login':
                    update_login($conn,$_PUT['id'],$_PUT['data']);
                    break;
                
        break;

    case 'DELETE':
        // Implement DELETE operations here
        parse_str(file_get_contents("php://input"), $_DELETE);

        if (isset($_DELETE['action'])) {
            $action = $_DELETE['action'];

            switch ($action) {
                case 'delete_persona':
                    delete_persona($conn, $_DELETE['id']);
                    break;

                case 'delete_rol':
                    delete_rol($conn, $_DELETE['id']);
                    break;
                                    
                case 'delete_incidencia':
                delete_incidencia($conn, $_DELETE['id']);
                break;

                case'delete_departamento':
                    delete_departamento($conn, $_DELETE['id']);

                case'delete_empresa':
                    delete_empresa($conn, $_DELETE['id']);

                case'delete_multimedia':
                    delete_multimedia($conn, $_DELETE['id']);

                case 'delete_login':
                    delete_login($conn, $_DELETE['id']);

                case'delete_mediodepago':
                    delete_mediodepago($conn, $_DELETE['id']);
        break;

    default:
        echo "Unsupported HTTP method";
        break;
}}
            }}}
// Close the database connection
$conn->close();

// Functions

function login($conn, $data)
{
    if (isset($data['correo']) && isset($data['contrasenna'])) {
        $correo = $conn->real_escape_string($data['correo']);
        $contrasenna = $conn->real_escape_string($data['contrasenna']);

        $stmt = $conn->prepare("SELECT * FROM login WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($contrasenna, $row['contrasenna'])) {
                echo json_encode(['success' => true, 'message' => 'Login successful']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid password']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing credentials']);
    }
}

function create_incidencia($conn, $data)
{
    if (
        isset($data['asunto']) && isset($data['descripcion']) &&
        isset($data['prioridad']) && isset($data['estado']) &&
        isset($data['Personaid']) && isset($data['Departamentoid'])
    ) {
        $asunto = $conn->real_escape_string($data['asunto']);
        $descripcion = $conn->real_escape_string($data['descripcion']);
        $prioridad = intval($data['prioridad']);
        $estado = $conn->real_escape_string($data['estado']);
        $Personaid = intval($data['Personaid']);
        $Departamentoid = intval($data['Departamentoid']);

        $stmt = $conn->prepare("INSERT INTO incidencia (asunto, descripcion, prioridad, estado, Personaid, Departamentoid) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssissi", $asunto, $descripcion, $prioridad, $estado, $Personaid, $Departamentoid);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Incidencia created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error creating incidencia']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}

function create_persona($conn, $data)
{
    if (
        isset($data['nombre']) && isset($data['apellidos']) &&
        isset($data['telefono']) && isset($data['Rolid']) &&
        isset($data['Departamentoid']) && isset($data['Empresaid'])
    ) {
        $nombre = $conn->real_escape_string($data['nombre']);
        $apellidos = $conn->real_escape_string($data['apellidos']);
        $telefono = $conn->real_escape_string($data['telefono']);
        $Rolid = intval($data['Rolid']);
        $Departamentoid = intval($data['Departamentoid']);
        $Empresaid = intval($data['Empresaid']);

        $stmt = $conn->prepare("INSERT INTO persona (nombre, apellidos, telefono, Rolid, Departamentoid, Empresaid) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiii", $nombre, $apellidos, $telefono, $Rolid, $Departamentoid, $Empresaid);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Persona created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error creating persona']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}

function create_rol($conn, $data)
{
    if (isset($data['tipo'])) {
        $tipo = $conn->real_escape_string($data['tipo']);

        $stmt = $conn->prepare("INSERT INTO rol (tipo) VALUES (?)");
        $stmt->bind_param("s", $tipo);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Rol created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error creating rol']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}

function create_login($conn, $data)
{
    if (isset($data['correo']) && isset($data['contrasenna']) && isset($data['Personaid'])) {
        $correo = $conn->real_escape_string($data['correo']);
        $contrasenna = password_hash($conn->real_escape_string($data['contrasenna']), PASSWORD_DEFAULT);
        $Personaid = intval($data['Personaid']);

        $stmt = $conn->prepare("INSERT INTO login (correo, contrasenna, Personaid) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $correo, $contrasenna, $Personaid);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Login created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error creating login']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}

function get_incidencias($conn)
{
    $result = $conn->query("SELECT * FROM incidencia");
    $incidencias = array();
    while ($row = $result->fetch_assoc()) {
        $incidencias[] = $row;
    }
    echo json_encode($incidencias);
}

function get_persona($conn, $id)
{
    $id = intval($id);
    $stmt = $conn->prepare("SELECT * FROM persona WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_assoc());
}

function get_personas_by_departamento($conn, $Departamentoid)
{
    $Departamentoid = intval($Departamentoid);
    $stmt = $conn->prepare("SELECT * FROM persona WHERE Departamentoid = ?");
    $stmt->bind_param("i", $Departamentoid);
    $stmt->execute();
    $result = $stmt->get_result();
    $personas = array();
    while ($row = $result->fetch_assoc()) {
        $personas[] = $row;
    }
    echo json_encode($personas);
}

function get_personas_by_empresa($conn, $Empresaid)
{
    $Empresaid = intval($Empresaid);
    $stmt = $conn->prepare("SELECT * FROM persona WHERE Empresaid = ?");
    $stmt->bind_param("i", $Empresaid);
    $stmt->execute();
    $result = $stmt->get_result();
    $personas = array();
    while ($row = $result->fetch_assoc()) {
        $personas[] = $row;
    }
    echo json_encode($personas);
}

function get_rols($conn)
{
    $result = $conn->query("SELECT * FROM rol");
    $rols = array();
    while ($row = $result->fetch_assoc()) {
        $rols[] = $row;
    }
    echo json_encode($rols);
}



function create_mediodepago($conn, $data)
{
    if (isset($data['datos']) && isset($data['Personaid'])) {
        $datos = $conn->real_escape_string($data['datos']);
        $Personaid = intval($data['Personaid']);

        $stmt = $conn->prepare("INSERT INTO mediodepago (datos, Personaid) VALUES (?, ?)");
        $stmt->bind_param("si", $datos, $Personaid);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Medio de pago created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error creating medio de pago']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}

function create_multimedia($conn, $data)
{
    if (isset($data['url']) && isset($data['Incidenciaid'])) {
        $url = $conn->real_escape_string($data['url']);
        $Incidenciaid = intval($data['Incidenciaid']);

        $stmt = $conn->prepare("INSERT INTO multimedia (url, Incidenciaid) VALUES (?, ?)");
        $stmt->bind_param("si", $url, $Incidenciaid);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Multimedia created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error creating multimedia']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}



function create_departamento($conn, $data)
{
    if (isset($data['nombre']) && isset($data['Empresaid'])) {
        $nombre = $conn->real_escape_string($data['nombre']);
        $Empresaid = intval($data['Empresaid']);

        $stmt = $conn->prepare("INSERT INTO departamento (nombre, Empresaid) VALUES (?, ?)");
        $stmt->bind_param("si", $nombre, $Empresaid);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Departamento created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error creating departamento']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}

function create_empresa($conn, $data)
{
    if (
        isset($data['codigo']) && isset($data['nombre']) &&
        isset($data['correo']) && isset($data['direccion']) &&
        isset($data['pais'])
    ) {
        $codigo = $conn->real_escape_string($data['codigo']);
        $nombre = $conn->real_escape_string($data['nombre']);
        $correo = $conn->real_escape_string($data['correo']);
        $direccion = $conn->real_escape_string($data['direccion']);
        $pais = $conn->real_escape_string($data['pais']);

        $stmt = $conn->prepare("INSERT INTO empresa (codigo, nombre, correo, direccion, pais) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $codigo, $nombre, $correo, $direccion, $pais);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Empresa created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error creating empresa']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}



function create_personaid_departamentoid($conn, $data)
{
    if (
        isset($data['nombre']) && isset($data['apellidos']) &&
        isset($data['telefono']) && isset($data['Rolid']) &&
        isset($data['Departamentoid']) && isset($data['Empresaid'])
    ) {
        $nombre = $conn->real_escape_string($data['nombre']);
        $apellidos = $conn->real_escape_string($data['apellidos']);
        $telefono = $conn->real_escape_string($data['telefono']);
        $Rolid = intval($data['Rolid']);
        $Departamentoid = intval($data['Departamentoid']);
        $Empresaid = intval($data['Empresaid']);

        // Check if the department exists
        $checkDept = $conn->prepare("SELECT id FROM departamento WHERE id = ?");
        $checkDept->bind_param("i", $Departamentoid);
        $checkDept->execute();
        $deptResult = $checkDept->get_result();

        if ($deptResult->num_rows > 0) {
            // Department exists, proceed to create persona
            $stmt = $conn->prepare("INSERT INTO persona (nombre, apellidos, telefono, Rolid, Departamentoid, Empresaid) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssiii", $nombre, $apellidos, $telefono, $Rolid, $Departamentoid, $Empresaid);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Persona created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error creating persona']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid department ID']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}

function get_login($conn, $id)
{
    $id = intval($id);
    $stmt = $conn->prepare("SELECT * FROM login WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_assoc());
}

function get_mediodepagos_by_personaid($conn, $Personaid)
{
    $Personaid = intval($Personaid);
    $stmt = $conn->prepare("SELECT * FROM mediodepago WHERE Personaid = ?");
    $stmt->bind_param("i", $Personaid);
    $stmt->execute();
    $result = $stmt->get_result();
    $mediodepagos = array();
    while ($row = $result->fetch_assoc()) {
        $mediodepagos[] = $row;
    }
    echo json_encode($mediodepagos);
}

function get_multimedia_by_incidenciaid($conn, $Incidenciaid)
{
    $Incidenciaid = intval($Incidenciaid);
    $stmt = $conn->prepare("SELECT * FROM multimedia WHERE Incidenciaid = ?");
    $stmt->bind_param("i", $Incidenciaid);
    $stmt->execute();
    $result = $stmt->get_result();
    $multimedia = array();
    while ($row = $result->fetch_assoc()) {
        $multimedia[] = $row;
    }
    echo json_encode($multimedia);
}

function get_departamento($conn, $id)
{
    $id = intval($id);
    $stmt = $conn->prepare("SELECT * FROM departamento WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_assoc());
}

function get_empresas($conn)
{
    $result = $conn->query("SELECT * FROM empresa");
    $empresas = array();
    while ($row = $result->fetch_assoc()) {
        $empresas[] = $row;
    }
    echo json_encode($empresas);
}

function get_empresa($conn, $id)
{
    $id = intval($id);
    $stmt = $conn->prepare("SELECT * FROM empresa WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_assoc());
}



function get_incidencias_by_personaid($conn, $Personaid)
{
    $Personaid = intval($Personaid);
    $stmt = $conn->prepare("SELECT * FROM incidencia WHERE Personaid = ?");
    $stmt->bind_param("i", $Personaid);
    $stmt->execute();
    $result = $stmt->get_result();
    $incidencias = array();
    while ($row = $result->fetch_assoc()) {
        $incidencias[] = $row;
    }
    echo json_encode($incidencias);
}



function update_persona($conn, $id, $data)
{
    $id = intval($id);

    if (
        isset($data['nombre']) && isset($data['apellidos']) &&
        isset($data['telefono']) && isset($data['Rolid']) &&
        isset($data['Departamentoid']) && isset($data['Empresaid'])
    ) {
        $nombre = $conn->real_escape_string($data['nombre']);
        $apellidos = $conn->real_escape_string($data['apellidos']);
        $telefono = $conn->real_escape_string($data['telefono']);
        $Rolid = intval($data['Rolid']);
        $Departamentoid = intval($data['Departamentoid']);
        $Empresaid = intval($data['Empresaid']);

        // Check if the department exists
        $checkDept = $conn->prepare("SELECT id FROM departamento WHERE id = ?");
        $checkDept->bind_param("i", $Departamentoid);
        $checkDept->execute();
        $deptResult = $checkDept->get_result();

        if ($deptResult->num_rows > 0) {
            // Department exists, proceed to update persona
            $stmt = $conn->prepare("UPDATE persona SET nombre=?, apellidos=?, telefono=?, Rolid=?, Departamentoid=?, Empresaid=? WHERE id=?");
            $stmt->bind_param("sssiiii", $nombre, $apellidos, $telefono, $Rolid, $Departamentoid, $Empresaid, $id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Persona updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating persona']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid department ID']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}

function delete_persona($conn, $id)
{
    $id = intval($id);

    $stmt = $conn->prepare("DELETE FROM persona WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Persona deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting persona']);
    }
}



function update_departamento($conn, $id, $data)
{
    $id = intval($id);

    if (isset($data['nombre']) && isset($data['Empresaid'])) {
        $nombre = $conn->real_escape_string($data['nombre']);
        $Empresaid = intval($data['Empresaid']);

        $stmt = $conn->prepare("UPDATE departamento SET nombre=?, Empresaid=? WHERE id=?");
        $stmt->bind_param("sii", $nombre, $Empresaid, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Departamento updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating departamento']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}

function delete_departamento($conn, $id)
{
    $id = intval($id);

    $stmt = $conn->prepare("DELETE FROM departamento WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Departamento deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting departamento']);
    }
}

function update_empresa($conn, $id, $data)
{
    $id = intval($id);

    if (
        isset($data['codigo']) && isset($data['nombre']) &&
        isset($data['correo']) && isset($data['direccion']) &&
        isset($data['pais'])
    ) {
        $codigo = $conn->real_escape_string($data['codigo']);
        $nombre = $conn->real_escape_string($data['nombre']);
        $correo = $conn->real_escape_string($data['correo']);
        $direccion = $conn->real_escape_string($data['direccion']);
        $pais = $conn->real_escape_string($data['pais']);

        $stmt = $conn->prepare("UPDATE empresa SET codigo=?, nombre=?, correo=?, direccion=?, pais=? WHERE id=?");
        $stmt->bind_param("sssssi", $codigo, $nombre, $correo, $direccion, $pais, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Empresa updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating empresa']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}

function delete_empresa($conn, $id)
{
    $id = intval($id);

    $stmt = $conn->prepare("DELETE FROM empresa WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Empresa deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting empresa']);
    }
}

function update_rol($conn, $id, $data)
{
    $id = intval($id);

    if (isset($data['tipo'])) {
        $tipo = $conn->real_escape_string($data['tipo']);

        $stmt = $conn->prepare("UPDATE rol SET tipo=? WHERE id=?");
        $stmt->bind_param("si", $tipo, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Rol updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating rol']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}

function delete_rol($conn, $id)
{
    $id = intval($id);

    $stmt = $conn->prepare("DELETE FROM rol WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Rol deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting rol']);
    }
}

function update_mediodepago($conn, $id, $data)
{
    $id = intval($id);

    if (isset($data['datos']) && isset($data['Personaid'])) {
        $datos = $conn->real_escape_string($data['datos']);
        $Personaid = intval($data['Personaid']);

        $stmt = $conn->prepare("UPDATE mediodepago SET datos=?, Personaid=? WHERE id=?");
        $stmt->bind_param("sii", $datos, $Personaid, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Medio de pago updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating medio de pago']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}

function delete_mediodepago($conn, $id)
{
    $id = intval($id);

    $stmt = $conn->prepare("DELETE FROM mediodepago WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Medio de pago deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting medio de pago']);
    }
}

function update_multimedia($conn, $id, $data)
{
    $id = intval($id);

    if (isset($data['url']) && isset($data['Incidenciaid'])) {
        $url = $conn->real_escape_string($data['url']);
        $Incidenciaid = intval($data['Incidenciaid']);

        $stmt = $conn->prepare("UPDATE multimedia SET url=?, Incidenciaid=? WHERE id=?");
        $stmt->bind_param("sii", $url, $Incidenciaid, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Multimedia updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating multimedia']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}

function delete_multimedia($conn, $id)
{
    $id = intval($id);

    $stmt = $conn->prepare("DELETE FROM multimedia WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Multimedia deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting multimedia']);
    }
}

function update_incidencia($conn, $id, $data)
{
    $id = intval($id);

    if (
        isset($data['asunto']) && isset($data['descripcion']) &&
        isset($data['prioridad']) && isset($data['estado']) &&
        isset($data['Personaid']) && isset($data['Departamentoid'])
    ) {
        $asunto = $conn->real_escape_string($data['asunto']);
        $descripcion = $conn->real_escape_string($data['descripcion']);
        $prioridad = intval($data['prioridad']);
        $estado = $conn->real_escape_string($data['estado']);
        $Personaid = intval($data['Personaid']);
        $Departamentoid = intval($data['Departamentoid']);

        $stmt = $conn->prepare("UPDATE incidencia SET asunto=?, descripcion=?, prioridad=?, estado=?, Personaid=?, Departamentoid=? WHERE id=?");
        $stmt->bind_param("ssissii", $asunto, $descripcion, $prioridad, $estado, $Personaid, $Departamentoid, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Incidencia updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating incidencia']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}

function delete_incidencia($conn, $id)
{
    $id = intval($id);

    $stmt = $conn->prepare("DELETE FROM incidencia WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Incidencia deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting incidencia']);
    }
}



function update_login($conn, $id, $data)
{
    $id = intval($id);

    if (
        isset($data['correo']) && isset($data['contrasenna']) &&
        isset($data['Personaid'])
    ) {
        $correo = $conn->real_escape_string($data['correo']);
        $contrasenna = password_hash($conn->real_escape_string($data['contrasenna']), PASSWORD_DEFAULT);
        $Personaid = intval($data['Personaid']);

        $stmt = $conn->prepare("UPDATE login SET correo=?, contrasenna=?, Personaid=? WHERE id=?");
        $stmt->bind_param("ssii", $correo, $contrasenna, $Personaid, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Login updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating login']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
}



function delete_login($conn, $id)
{
    $id = intval($id);

    $stmt = $conn->prepare("DELETE FROM login WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Login deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting login']);
    }
}

















?>
