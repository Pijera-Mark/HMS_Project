<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Role Filter - Checks if user has required role
 * Applied to routes that require specific user roles
 */
class RoleFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do before a route is executed.
     *
     * @param RequestInterface $request
     * @param array|null       $params
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $params = null)
    {
        if (!$params) {
            return redirect()->to('/login')->with('error', 'Access denied');
        }

        // Get user session
        $session = session();
        $userRole = $session->get('role');
        
        if (!$userRole) {
            return redirect()->to('/login')->with('error', 'Please login to continue');
        }

        // Check if user has required role
        $allowedRoles = is_array($params) ? $params : explode(',', $params);
        
        if (!in_array($userRole, $allowedRoles)) {
            // Log unauthorized access attempt
            log_message('warning', 'Unauthorized access attempt. User role: ' . $userRole . ', Required roles: ' . implode(', ', $allowedRoles));
            
            return redirect()->to('/dashboard')->with('error', 'You do not have permission to access this page');
        }

        return null;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution and must not return anything.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null       $arguments
     *
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
        // Do nothing here
    }
}
