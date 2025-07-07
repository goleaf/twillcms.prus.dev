<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::with(['post', 'user', 'replies'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->post_id, function ($query, $postId) {
                return $query->where('post_id', $postId);
            })
            ->latest()
            ->paginate(20);

        return response()->json($comments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
            'author_name' => 'required_without:user_id|string|max:100',
            'author_email' => 'required_without:user_id|email|max:100',
        ]);

        $comment = Comment::create([
            'post_id' => $request->post_id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'author_name' => $request->author_name,
            'author_email' => $request->author_email,
            'content' => $request->content,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => Auth::check() ? 'approved' : 'pending',
        ]);

        return response()->json($comment->load(['user', 'replies']), 201);
    }

    public function show(Comment $comment)
    {
        return response()->json($comment->load(['post', 'user', 'replies']));
    }

    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update($request->only(['content']));

        return response()->json($comment);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }

    public function approve(Comment $comment)
    {
        $comment->approve();

        return response()->json(['message' => 'Comment approved successfully']);
    }

    public function reject(Comment $comment)
    {
        $comment->reject();

        return response()->json(['message' => 'Comment rejected successfully']);
    }

    public function getPostComments(Post $post)
    {
        $comments = $post->comments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->approved()
            ->latest()
            ->paginate(10);

        return response()->json($comments);
    }
}
