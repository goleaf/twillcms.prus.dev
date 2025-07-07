<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewsletterSubscriber;

class NewsletterSubscriberController extends Controller
{
    protected $moduleName = 'newsletter_subscribers';
    protected $indexColumns = [
        'email' => [
            'title' => 'Email',
            'field' => 'email',
            'sort' => true,
        ],
        'name' => [
            'title' => 'Name',
            'field' => 'name',
            'sort' => true,
        ],
        'status' => [
            'title' => 'Status',
            'field' => 'status',
            'sort' => true,
        ],
        'is_verified' => [
            'title' => 'Verified',
            'field' => 'is_verified',
            'sort' => false,
        ],
        'subscribed_at' => [
            'title' => 'Subscribed At',
            'field' => 'subscribed_at',
            'sort' => true,
        ],
    ];

    protected $filters = [
        'status' => [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'unsubscribed' => 'Unsubscribed',
        ],
        'verified' => [
            '1' => 'Verified',
            '0' => 'Unverified',
        ],
    ];

    protected $defaultOrders = ['created_at' => 'desc'];
    protected $perPage = 20;

    public function getIndexTableData($request)
    {
        $query = NewsletterSubscriber::query()
            ->when($request->get('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->get('verified'), function ($query, $verified) {
                if ($verified === '1') {
                    return $query->whereNotNull('verified_at');
                } else {
                    return $query->whereNull('verified_at');
                }
            })
            ->when($request->get('search'), function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
                });
            });

        return $this->getIndexTableDataQuery($query);
    }

    protected function indexItemData($item)
    {
        return [
            'id' => $item->id,
            'email' => $item->email,
            'name' => $item->name ?? 'N/A',
            'status' => ucfirst($item->status),
            'is_verified' => $item->is_verified ? 'Yes' : 'No',
            'subscribed_at' => $item->subscribed_at ? $item->subscribed_at->format('M d, Y') : 'N/A',
        ];
    }

    public function export()
    {
        $subscribers = NewsletterSubscriber::active()->verified()->get();

        $csv = "Email,Name,Subscribed At\n";
        foreach ($subscribers as $subscriber) {
            $csv .= "{$subscriber->email},{$subscriber->name},{$subscriber->subscribed_at}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="newsletter_subscribers.csv"');
    }
}
