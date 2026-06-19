    <?php
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // 1. Definisi Konstanta Kredensial Database (Hasil Export MySQL Workbench)
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_USER', 'root');
    define('DB_PASS', 'Root23');
    define('DB_NAME', 'db_dilla_fruits');

    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        $conn->set_charset('utf8mb4');
        
    } catch (mysqli_sql_exception $e) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Gagal terhubung ke database Workbench: ' . $e->getMessage()
        ]);
        exit;
    }

    /**
     * Mencegah SQL Injection dengan membersihkan string input (Sanitasi data form)
     * * @param string $data Input mentah dari $_POST atau $_GET
     * @return string Data aman siap masuk query
     */
    function sanitizeInput($data) {
        global $conn;
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $conn->real_escape_string($data);
    }

    /**
     * Format Response JSON Standard untuk kebutuhan AJAX Fetch API
     * * @param bool $success Status operasi
     * @param string $message Pesan teks info/error
     * @param array $data Payload data opsional untuk tabel/chart
     */
    function sendJSONResponse($success, $message, $data = []) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data'    => $data
        ]);
        exit;
    }