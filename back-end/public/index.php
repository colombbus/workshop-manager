<?php
require_once '../controllers/ReportController.php';
require_once '../services/Application.php';
require_once '../utils/Logger.php';

// define('BASE_PATH', '/declick-workshops-survey/back-end');
define('BASE_PATH', '/back-end');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

function handleRequest () {
  $method = $_SERVER['REQUEST_METHOD'];
  $path = substr($_SERVER['REQUEST_URI'], strlen(BASE_PATH));
  $rawBody = file_get_contents('php://input');
  if (!$rawBody) {
    $rawBody = '{}';
  }
  $body = json_decode($rawBody);
  if ($method === 'OPTIONS') {
    exit;
  }
  if ($method === 'POST' && $path === '/install') {
    Application::install();
  } else if ($method === 'GET' && $path === '/reports.csv') {
    ReportController::download($body);
  } else if ($method === 'POST' && $path === '/reports') {
    ReportController::create($body);
  } else {
    http_response_code(404);
  }
}

function handleError ($id, $message, $file, $line) {
  throw new ErrorException($message, $id, 0, $file, $line);
}
set_error_handler('handleError');

function output ($data) {
  header('Content-type: application/json');
  echo json_encode($data);
}

try {
  handleRequest();
} catch (BadRequestException $exception) {
  http_response_code(400);
  output([
    'error' => 'bad-request-error',
    'description' => $exception->getMessage()
  ]);
} catch (Exception $exception) {
  Logger::error($exception);
  http_response_code(500);
}
