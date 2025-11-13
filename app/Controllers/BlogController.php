<?php declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PostModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class BlogController extends BaseController
{
    protected PostModel $postModel;

    public function __construct()
    {
        $this->postModel = new PostModel();
        helper('form');
    }

    // --- PUBLIC-FACING METHODS ---

    public function index(): string
    {
        $data = [
            'pageTitle'       => 'Tech Insights & Tutorials | Afrikenkid Blog',
            'metaDescription' => 'Explore articles on fintech, software development, AI, and consumer tech tailored for the Kenyan and African market.',
            'canonicalUrl'    => url_to('blog.index'),
            'posts'           => $this->postModel->where('status', 'published')->orderBy('published_at', 'DESC')->paginate(6),
            'pager'           => $this->postModel->pager,
        ];
        return view('blog/index', $data);
    }

    public function show(string $slug): string
    {
        $post = $this->postModel->where('slug', $slug)->where('status', 'published')->first();
        if (!$post) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Placeholder for JSON-LD Schema (to be implemented later)
        $schema = [
            "@context"        => "https://schema.org",
            "@type"           => "BlogPosting",
            "headline"        => $post->title,
            "image"           => $post->featured_image_url,
            "datePublished"   => $post->published_at ? $post->published_at->toDateTimeString() : null,
            "dateModified"    => $post->updated_at ? $post->updated_at->toDateTimeString() : null,
            "author"          => [
                "@type" => "Person",
                "name"  => $post->author_name,
            ],
            "publisher"       => [
                "@type" => "Organization",
                "name"  => "Afrikenkid",
                "logo"  => [
                    "@type" => "ImageObject",
                    "url"   => base_url('assets/images/logo.png'), // Adjust path to your logo
                ],
            ],
            "description"     => $post->meta_description,
            "mainEntityOfPage" => [
                "@type" => "WebPage",
                "@id"   => url_to('blog.show', $slug),
            ],
        ];

        $data = [
            'pageTitle'       => esc($post->title) . ' | Afrikenkid Blog',
            'metaDescription' => esc($post->meta_description),
            'canonicalUrl'    => url_to('blog.show', $slug),
            'post'            => $post,
            'json_ld_schema'  => '<script type="application/ld+json">' . json_encode($schema) . '</script>',
        ];
        return view('blog/post', $data);
    }

    // --- ADMIN-ONLY METHODS ---

    public function adminIndex()
    {
        if (!session()->get('is_admin')) { return redirect()->to(url_to('home')); }

        $data = [
            'pageTitle' => 'Manage Blog Posts | Admin',
            'posts'     => $this->postModel->orderBy('created_at', 'DESC')->paginate(10),
            'pager'     => $this->postModel->pager,
            'robotsTag' => 'noindex, nofollow',
        ];
        return view('admin/blog/index', $data);
    }

    public function create()
    {
        if (!session()->get('is_admin')) { return redirect()->to(url_to('home')); }
        
        $data = [
            'pageTitle'  => 'Create New Post | Admin',
            'formTitle'  => 'Create New Post',
            'formAction' => url_to('admin.blog.store'),
            'post'       => null,
            'robotsTag'  => 'noindex, nofollow',
        ];
        return view('admin/blog/form', $data);
    }

    public function store()
    {
        if (!session()->get('is_admin')) { return redirect()->to(url_to('home')); }

        $postData = $this->request->getPost();
        if ($this->postModel->save($postData)) {
            return redirect()->to(url_to('admin.blog.index'))->with('success', 'Post created successfully.');
        }
        return redirect()->back()->withInput()->with('error', $this->postModel->errors());
    }

    public function edit(int $id)
    {
        if (!session()->get('is_admin')) { return redirect()->to(url_to('home')); }
        
        $post = $this->postModel->find($id);
        if (!$post) {
            throw PageNotFoundException::forPageNotFound();
        }

        $data = [
            'pageTitle'  => 'Edit Post | Admin',
            'formTitle'  => 'Edit Post: ' . esc($post->title),
            'formAction' => url_to('admin.blog.update', $id),
            'post'       => $post,
            'robotsTag'  => 'noindex, nofollow',
        ];
        return view('admin/blog/form', $data);
    }

    public function update(int $id)
    {
        if (!session()->get('is_admin')) { return redirect()->to(url_to('home')); }

        $postData = $this->request->getPost();
        if ($this->postModel->update($id, $postData)) {
            return redirect()->to(url_to('admin.blog.index'))->with('success', 'Post updated successfully.');
        }
        return redirect()->back()->withInput()->with('error', $this->postModel->errors());
    }

    public function delete(int $id)
    {
        if (!session()->get('is_admin')) { return redirect()->to(url_to('home')); }

        if ($this->postModel->delete($id)) {
            return redirect()->to(url_to('admin.blog.index'))->with('success', 'Post deleted successfully.');
        }
        return redirect()->to(url_to('admin.blog.index'))->with('error', 'Failed to delete post.');
    }
}
