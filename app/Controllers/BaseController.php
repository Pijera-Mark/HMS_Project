<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'form', 'html', 'text'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session;
    protected $security;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Initialize services
        $this->session = service('session');
        $this->security = service('security');
        
        // Set security headers
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->setHeader('X-XSS-Protection', '1; mode=block');
    }

    protected function enforceRoles(array $roles)
    {
        $user = $this->session->get('user');
        $role = $user['role'] ?? null;

        if (! $user || ! in_array($role, $roles, true)) {
            // Log unauthorized access attempt
            log_message('warning', 'Unauthorized access attempt by user ID: ' . ($user['id'] ?? 'unknown') . ' to role-protected resource');
            
            // Set flash message for better UX
            $this->session->setFlashdata('error', 'You do not have permission to access this resource.');
            return redirect()->to('/dashboard');
        }

        return null;
    }

    /**
     * Check if user has specific permission
     */
    protected function checkPermission(string $permission): bool
    {
        $user = $this->session->get('user');
        if (!$user) {
            return false;
        }

        $userModel = new \App\Models\UserModel();
        return $userModel->hasPermission($user['id'], $permission);
    }

    /**
     * Enforce permission check with redirect
     */
    protected function enforcePermission(string $permission)
    {
        if (!$this->checkPermission($permission)) {
            $user = $this->session->get('user');
            log_message('warning', 'Permission denied: User ID ' . ($user['id'] ?? 'unknown') . ' attempted to access ' . $permission);
            
            $this->session->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to('/dashboard');
        }

        return null;
    }

    /**
     * Validate CSRF token
     */
    protected function validateCsrf(): bool
    {
        return $this->security->verifyToken();
    }

    /**
     * Sanitize input data
     */
    protected function sanitize(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = $this->security->sanitizeFilename($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }

    /**
     * Log user activity
     */
    protected function logActivity(string $action, array $details = []): void
    {
        $user = $this->session->get('user');
        
        $activityLogModel = new \App\Models\ActivityLogModel();
        
        $activityLogModel->logActivity(
            $user['id'] ?? null,
            $user['name'] ?? 'Unknown',
            $action,
            $details['entity_type'] ?? null,
            $details['entity_id'] ?? null,
            $details['details'] ?? null,
            $this->request->getIPAddress(),
            $this->request->getUserAgent(),
            $user['branch_id'] ?? null
        );
    }

    /**
     * Send JSON response
     */
    protected function jsonResponse(array $data, int $statusCode = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setContentType('application/json')
            ->setJSON($data);
    }

    /**
     * Send error response
     */
    protected function errorResponse(string $message, int $statusCode = 400, array $errors = []): ResponseInterface
    {
        $data = [
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ];
        
        if ($this->request->isAJAX()) {
            return $this->jsonResponse($data, $statusCode);
        }
        
        $this->session->setFlashdata('error', $message);
        return redirect()->back()->with('errors', $errors);
    }

    /**
     * Send success response
     */
    protected function successResponse(string $message, array $data = [], int $statusCode = 200): ResponseInterface
    {
        $response = [
            'success' => true,
            'message' => $message
        ];
        
        if (!empty($data)) {
            $response['data'] = $data;
        }
        
        if ($this->request->isAJAX()) {
            return $this->jsonResponse($response, $statusCode);
        }
        
        $this->session->setFlashdata('success', $message);
        return redirect()->back()->with('data', $data);
    }
}
