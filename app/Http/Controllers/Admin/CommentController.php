<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    protected $moduleName = 'comments';
    protected $indexWith = ['post', 'user'];
    protected $indexColumns = [
        'content' => [
            'title' => 'Content',
            'field' => 'content',
            'sort' => true,
        ],
        'post' => [
            'title' => 'Post',
            'field' => 'post.title',
            'sort' => true,
            'relationship' => 'post',
        ],
        'author_display_name' => [
            'title' => 'Author',
            'field' => 'author_display_name',
            'sort' => false,
        ],
        'status' => [
            'title' => 'Status',
            'field' => 'status',
            'sort' => true,
        ],
    ];

    protected $filters = [
        'status' => [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ],
    ];

    protected $defaultOrders = ['created_at' => 'desc'];
    protected $perPage = 20;

    public function getIndexTableData($request)
    {
        $query = Comment::with(['post', 'user'])
            ->when($request->get('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->get('search'), function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('content', 'like', "%{$search}%")
                      ->orWhere('author_name', 'like', "%{$search}%")
                      ->orWhere('author_email', 'like', "%{$search}%");
                });
            });

        return $this->getIndexTableDataQuery($query);
    }

    protected function indexItemData($item)
    {
        return [
            'id' => $item->id,
            'content' => \Str::limit(strip_tags($item->content), 100),
            'post' => $item->post ? $item->post->title : 'N/A',
            'author_display_name' => $item->author_display_name,
            'status' => ucfirst($item->status),
            'created_at' => $item->created_at->format('M d, Y H:i'),
        ];
    }

    public function approve($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->approve();

        return response()->json(['message' => 'Comment approved successfully']);
    }

    public function reject($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->reject();

        return response()->json(['message' => 'Comment rejected successfully']);
    }

    protected function formData($request)
    {
        $posts = Post::published()->orderBy('title')->get();

        return [
            'posts' => $posts->map(function ($post) {
                return [
                    'value' => $post->id,
                    'label' => $post->title,
                ];
            }),
        ];
    }
}
