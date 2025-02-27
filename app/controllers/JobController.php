<?php
require_once __DIR__ .  '/../models/Job.php';
require_once __DIR__ . '/../utils/RateLimiter.php';
require_once __DIR__ . '/../utils/SendEmail.php';

class JobController extends BaseController
{
    public function index()
    {
        session_start();

        $data = [
            'csrfToken' => $this->generateCsrfToken(),
            'errors' => $_SESSION['errors'] ?? [],
            'oldInput' => $_SESSION['old_input'] ?? []
        ];
        // set states value incase js is disabled:
        $stateData = json_decode(file_get_contents(__DIR__ . '/../../public/data/states.json'), true);
        $data['stateData'] = array_merge($stateData['CANADA'] ?? [], $stateData['USA'] ?? []);
        // Load the view
        $this->render('form', $data);
        unset($_SESSION['errors'], $_SESSION['old_input']);
    }

    public function handleSubmit()
    {
        session_start();
        // limit 5 submission per hour.
        if (!RateLimiter::check('submit_count')) {
            http_response_code(429);
            echo json_encode(['error' => 'Too many submissions. Wait 1 hour.']);
            exit;
        }

        // check if js is enabled:
        $jsEnabled = isset($_POST['js_enabled']) && ($_POST['js_enabled'] === 'true');
        if ($jsEnabled) {
            header('Contend-Type: application/json');
        }

        // Ensure the request method is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Invalid request method']);
            exit;
        }

        // CSRF validation:
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['error' => 'CSRF validation failed']);
            exit;
        }

        // Input Sanitization and Validation:
        $errors = [];
        $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $script = trim(filter_input(INPUT_POST, 'script', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $country = trim(filter_input(INPUT_POST, 'country', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $state = trim(filter_input(INPUT_POST, 'state', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $budget = trim(filter_input(INPUT_POST, 'budget', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $filePath = null;

        if (empty($title)) {
            $errors['title'] = 'Project name is required.';
        }
        if (!in_array($country, ['CANADA', 'USA'])) {
            $errors['country'] = 'Please select a valid country.';
        }
        if (empty($state)) {
            $errors['state'] = 'State/Province is required.';
        }
        if (!in_array($budget, ['low', 'medium', 'high'])) {
            $errors['budget'] = 'Please select your budget.';
        }

        $job = new Job();
        // Server-side title uniqueness check
        if ($job->titleExists($title)) {
            $errors['title'] = 'Title already exists';
        }

        // Validate country-state relationship
        $statesData = json_decode(file_get_contents(__DIR__ . '/../../public/data/states.json'), true);
        if (!in_array($state, $statesData[$country] ?? [])) {
            $errors['state'] = 'Invalid state for selected country';
        }

        // Handle file upload
        if (!empty($_FILES['file']['name'])) {
            $uploadDir = __DIR__ . '/../../uploads/';

            // Create uploads directory if not exists
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES['file']['tmp_name']);

            $allowedMimes = [
                'application/pdf' => 'pdf',
                'application/msword' => 'doc',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
            ];

            if (!array_key_exists($mime, $allowedMimes)) {
                $errors['file'] = 'Only PDF/DOC/DOCX files allowed';
            } elseif ($_FILES['file']['size'] > 5 * 1024 * 1024) { // 5MB limit
                $errors['file'] = 'File size exceeds 5MB';
            } else {
                $extension = $allowedMimes[$mime];
                $fileName = uniqid() . '_' . basename($_FILES['file']['name']) . '.' . $extension;
                $uploadPath = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
                    $filePath = $fileName;
                } else {
                    $errors['file'] = 'File upload failed - check folder permissions';
                }
                $filePath = $fileName ?? null;
            }
        }

        // Assemble data for saving
        $data = [
            'title'      => $title,
            'script'     => $script,
            'country'    => $country,
            'state'      => $state,
            'file_path'  => $filePath,
            'budget'     => $budget,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        ];
        // Return errors
        if (!empty($errors)) {
            // Check if this is an AJAX request by inspecting a common header.
            // If it's not an AJAX request (i.e., JavaScript is disabled), re-render the form.
            if (!$jsEnabled) {
                // Store errors and old input in session flash data
                $_SESSION['errors'] = $errors;
                $_SESSION['old_input'] = $data;

                // Redirect back to the original form page
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            } else {
                http_response_code(422);
                echo json_encode(['success' => false, 'errors' => $errors]);
                exit;
            }
        }


        // Save data using the Job model
        try {
            $lastInsertId = $job->save($data);
            // Send confirmation email
            $emailService = new SendEmail();
            $emailService->sendJobConfirmation($data);

            // if js is disabled:
            if (!$jsEnabled) {
                // Redirect back to the original form page
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            } else
                // else if js enabled:
                echo json_encode(['success' => 'Job submitted successfully.', 'id' => $lastInsertId]);
        } catch (Exception $e) {
            // header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function checkTitleUnique()
    {
        header('Content-Type: application/json');
        $title = filter_input(INPUT_GET, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
        if (!$title) {
            echo json_encode(['available' => false]);
            exit;
        }
        try {
            $job = new Job();
            $exists = $job->titleExists($title);
            echo json_encode(['available' => !$exists]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Server error']);
        }
        exit;
    }
}
