<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Check if user is authenticated
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session()->get('user');
        
        if (!$user) {
            // For API requests, return JSON response
            if ($request->isAJAX() || $request->getHeaderLine('Accept') === 'application/json') {
                $response = service('response');
                return $response->setJSON([
                    'success' => false,
                    'message' => 'Authentication required',
                    'error' => 'unauthorized'
                ])->setStatusCode(401);
            }
            
            // For web requests, redirect to login
            return redirect()->to('/login')->with('error', 'Please login to continue');
        }

        // Check if user account is active
        if ($user['status'] !== 'active') {
            session()->destroy();
            
            if ($request->isAJAX() || $request->getHeaderLine('Accept') === 'application/json') {
                $response = service('response');
                return $response->setJSON([
                    'success' => false,
                    'message' => 'Account is inactive',
                    'error' => 'account_inactive'
                ])->setStatusCode(403);
            }
            
            return redirect()->to('/login')->with('error', 'Your account has been deactivated');
        }

        // Check session timeout (30 minutes)
        $lastActivity = session()->get('last_activity');
        if ($lastActivity && (time() - $lastActivity) > 1800) {
            session()->destroy();
            
            if ($request->isAJAX() || $request->getHeaderLine('Accept') === 'application/json') {
                $response = service('response');
                return $response->setJSON([
                    'success' => false,
                    'message' => 'Session expired',
                    'error' => 'session_expired'
                ])->setStatusCode(401);
            }
            
            return redirect()->to('/login')->with('error', 'Session expired, please login again');
        }

        // Update last activity
        session()->set('last_activity', time());

        // Check for role-based access if arguments provided
        if ($arguments && !empty($arguments)) {
            $userRole = $user['role'] ?? null;
            
            if (!in_array($userRole, $arguments)) {
                log_message('warning', 'Unauthorized access attempt: User ID ' . $user['id'] . ' with role ' . $userRole . ' attempted to access restricted resource');
                
                if ($request->isAJAX() || $request->getHeaderLine('Accept') === 'application/json') {
                    $response = service('response');
                    return $response->setJSON([
                        'success' => false,
                        'message' => 'Insufficient permissions',
                        'error' => 'insufficient_permissions'
                    ])->setStatusCode(403);
                }
                
                return redirect()->to('/dashboard')->with('error', 'You do not have permission to access this resource');
            }
        }

        return null;
    }

    /**
     * After filter execution
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Add security headers
        $response->setHeader('X-Content-Type-Options', 'nosniff')
                 ->setHeader('X-Frame-Options', 'SAMEORIGIN')
                 ->setHeader('X-XSS-Protection', '1; mode=block')
                 ->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Add CORS headers for API requests
        if ($request->isAJAX() || $request->getHeaderLine('Accept') === 'application/json') {
            $response->setHeader('Access-Control-Allow-Origin', '*')
                     ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                     ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        }

        return $response;
    }
}
