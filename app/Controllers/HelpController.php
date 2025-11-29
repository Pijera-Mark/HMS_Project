<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/**
 * Help & Support Controller
 * Provides help documentation and support features
 */
class HelpController extends BaseController
{
    /**
     * Display help center
     */
    public function index()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist', 'patient'])) {
            return $redirect;
        }

        $helpCategories = $this->getHelpCategories();
        $popularArticles = $this->getPopularArticles();

        return view('help/index', [
            'categories' => $helpCategories,
            'popularArticles' => $popularArticles
        ]);
    }

    /**
     * Display help category
     */
    public function category($slug)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist', 'patient'])) {
            return $redirect;
        }

        $category = $this->getCategoryBySlug($slug);
        
        if (!$category) {
            return redirect()->to('/help')->with('error', 'Help category not found');
        }

        $articles = $this->getCategoryArticles($category['id']);

        return view('help/category', [
            'category' => $category,
            'articles' => $articles
        ]);
    }

    /**
     * Display help article
     */
    public function article($slug)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist', 'patient'])) {
            return $redirect;
        }

        $article = $this->getArticleBySlug($slug);
        
        if (!$article) {
            return redirect()->to('/help')->with('error', 'Help article not found');
        }

        $relatedArticles = $this->getRelatedArticles($article['id']);

        return view('help/article', [
            'article' => $article,
            'relatedArticles' => $relatedArticles
        ]);
    }

    /**
     * Search help articles
     */
    public function search()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist', 'patient'])) {
            return $redirect;
        }

        $query = $this->request->getGet('q');
        $results = [];

        if ($query) {
            $results = $this->searchArticles($query);
        }

        return view('help/search', [
            'query' => $query,
            'results' => $results
        ]);
    }

    /**
     * Contact support form
     */
    public function contact()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist', 'patient'])) {
            return $redirect;
        }

        return view('help/contact');
    }

    /**
     * Submit support ticket
     */
    public function submitTicket()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist', 'patient'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        // Validate ticket data
        $rules = [
            'subject' => 'required|min_length[5]|max_length[100]',
            'message' => 'required|min_length[20]|max_length[1000]',
            'priority' => 'required|in_list[low,medium,high,urgent]',
            'category' => 'required|in_list[technical,billing,clinical,general]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Create support ticket
        $ticketData = [
            'user_id' => session()->get('user_id'),
            'subject' => $data['subject'],
            'message' => $data['message'],
            'priority' => $data['priority'],
            'category' => $data['category'],
            'status' => 'open',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $db = \Config\Database::connect();
        if ($db->table('support_tickets')->insert($ticketData)) {
            // Log activity
            $this->logActivity('support_ticket_created', [
                'entity_type' => 'support_ticket',
                'details' => 'Support ticket created: ' . $data['subject']
            ]);

            return redirect()->to('/help')->with('success', 'Support ticket submitted successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to submit support ticket');
    }

    /**
     * Display user tickets
     */
    public function myTickets()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist', 'patient'])) {
            return $redirect;
        }

        $userId = session()->get('user_id');
        $tickets = $this->getUserTickets($userId);

        return view('help/my-tickets', [
            'tickets' => $tickets
        ]);
    }

    /**
     * Display ticket details
     */
    public function ticket($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist', 'patient'])) {
            return $redirect;
        }

        $ticket = $this->getTicket($id);
        
        if (!$ticket) {
            return redirect()->to('/help/my-tickets')->with('error', 'Ticket not found');
        }

        // Check if user owns the ticket or is admin
        if ($ticket['user_id'] !== session()->get('user_id') && session()->get('role') !== 'admin') {
            return redirect()->to('/help/my-tickets')->with('error', 'Access denied');
        }

        $replies = $this->getTicketReplies($id);

        return view('help/ticket', [
            'ticket' => $ticket,
            'replies' => $replies
        ]);
    }

    /**
     * Reply to ticket
     */
    public function replyTicket($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist', 'patient'])) {
            return $redirect;
        }

        $ticket = $this->getTicket($id);
        
        if (!$ticket) {
            return redirect()->to('/help/my-tickets')->with('error', 'Ticket not found');
        }

        // Check if user owns the ticket or is admin
        if ($ticket['user_id'] !== session()->get('user_id') && session()->get('role') !== 'admin') {
            return redirect()->to('/help/my-tickets')->with('error', 'Access denied');
        }

        $data = $this->request->getPost();

        $rules = [
            'message' => 'required|min_length[10]|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $replyData = [
            'ticket_id' => $id,
            'user_id' => session()->get('user_id'),
            'message' => $data['message'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        $db = \Config\Database::connect();
        if ($db->table('ticket_replies')->insert($replyData)) {
            // Update ticket status if needed
            if ($ticket['status'] !== 'closed') {
                $db->table('support_tickets')->where('id', $id)->update([
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            return redirect()->to('/help/ticket/' . $id)->with('success', 'Reply added successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to add reply');
    }

    /**
     * Get help categories
     */
    private function getHelpCategories(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Getting Started',
                'slug' => 'getting-started',
                'description' => 'Learn the basics of HMS',
                'icon' => 'fas fa-rocket'
            ],
            [
                'id' => 2,
                'name' => 'Patient Management',
                'slug' => 'patient-management',
                'description' => 'Managing patient records and appointments',
                'icon' => 'fas fa-users'
            ],
            [
                'id' => 3,
                'name' => 'Doctor Features',
                'slug' => 'doctor-features',
                'description' => 'Doctor-specific features and tools',
                'icon' => 'fas fa-stethoscope'
            ],
            [
                'id' => 4,
                'name' => 'Billing & Payments',
                'slug' => 'billing-payments',
                'description' => 'Invoicing and payment processing',
                'icon' => 'fas fa-credit-card'
            ],
            [
                'id' => 5,
                'name' => 'Reports & Analytics',
                'slug' => 'reports-analytics',
                'description' => 'Generating and understanding reports',
                'icon' => 'fas fa-chart-bar'
            ],
            [
                'id' => 6,
                'name' => 'Technical Support',
                'slug' => 'technical-support',
                'description' => 'Technical issues and troubleshooting',
                'icon' => 'fas fa-cog'
            ]
        ];
    }

    /**
     * Get popular help articles
     */
    private function getPopularArticles(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'How to Create a New Patient',
                'slug' => 'create-new-patient',
                'category' => 'Patient Management',
                'views' => 1250
            ],
            [
                'id' => 2,
                'title' => 'Booking Appointments Guide',
                'slug' => 'booking-appointments',
                'category' => 'Getting Started',
                'views' => 980
            ],
            [
                'id' => 3,
                'title' => 'Managing Doctor Schedules',
                'slug' => 'doctor-schedules',
                'category' => 'Doctor Features',
                'views' => 750
            ],
            [
                'id' => 4,
                'title' => 'Creating Invoices',
                'slug' => 'creating-invoices',
                'category' => 'Billing & Payments',
                'views' => 650
            ]
        ];
    }

    /**
     * Get category by slug
     */
    private function getCategoryBySlug(string $slug): ?array
    {
        $categories = $this->getHelpCategories();
        
        foreach ($categories as $category) {
            if ($category['slug'] === $slug) {
                return $category;
            }
        }
        
        return null;
    }

    /**
     * Get category articles
     */
    private function getCategoryArticles(int $categoryId): array
    {
        // Mock data - in real implementation, this would come from database
        return [
            [
                'id' => 1,
                'title' => 'Sample Article 1',
                'slug' => 'sample-article-1',
                'excerpt' => 'This is a sample article description...',
                'created_at' => '2025-11-20'
            ],
            [
                'id' => 2,
                'title' => 'Sample Article 2',
                'slug' => 'sample-article-2',
                'excerpt' => 'This is another sample article...',
                'created_at' => '2025-11-19'
            ]
        ];
    }

    /**
     * Get article by slug
     */
    private function getArticleBySlug(string $slug): ?array
    {
        // Mock data - in real implementation, this would come from database
        return [
            'id' => 1,
            'title' => 'Sample Article',
            'slug' => $slug,
            'content' => '<h2>Article Content</h2><p>This is the full article content...</p>',
            'created_at' => '2025-11-20',
            'updated_at' => '2025-11-25'
        ];
    }

    /**
     * Get related articles
     */
    private function getRelatedArticles(int $articleId): array
    {
        // Mock data - in real implementation, this would come from database
        return [
            [
                'id' => 2,
                'title' => 'Related Article 1',
                'slug' => 'related-article-1'
            ],
            [
                'id' => 3,
                'title' => 'Related Article 2',
                'slug' => 'related-article-2'
            ]
        ];
    }

    /**
     * Search articles
     */
    private function searchArticles(string $query): array
    {
        // Mock data - in real implementation, this would search the database
        return [
            [
                'id' => 1,
                'title' => 'Search Result 1',
                'slug' => 'search-result-1',
                'excerpt' => 'This article matches your search...',
                'category' => 'Getting Started'
            ]
        ];
    }

    /**
     * Get user tickets
     */
    private function getUserTickets(int $userId): array
    {
        $db = \Config\Database::connect();
        return $db->table('support_tickets')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get ticket details
     */
    private function getTicket(int $id): ?array
    {
        $db = \Config\Database::connect();
        return $db->table('support_tickets')
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Get ticket replies
     */
    private function getTicketReplies(int $ticketId): array
    {
        $db = \Config\Database::connect();
        return $db->table('ticket_replies')
            ->where('ticket_id', $ticketId)
            ->orderBy('created_at', 'ASC')
            ->get()
            ->getResultArray();
    }
}
