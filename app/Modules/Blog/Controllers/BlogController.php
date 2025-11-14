<?php declare(strict_types=1);

namespace App\Modules\Blog\Controllers;

use App\Controllers\BaseController;
use App\Modules\Blog\Models\PostModel;
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
        return view('App\Modules\Blog\Views\blog\index', $data);
    }

    public function show(string $slug): string
    {
        $post = $this->postModel->where('slug', $slug)->where('status', 'published')->first();
        if (!$post) {
            throw PageNotFoundException::forPageNotFound();
        }

        $schema = [
            "@context"        => "https://schema.org",
            "@type"           => "BlogPosting",
            "headline"        => $post->title,
            "image"           => $post->featured_image_url,
            "datePublished"   => $post->published_at ? $post->published_at->toDateTimeString() : null,
            "dateModified"    => $post->updated_at ? $post->updated_at->toDateTimeString() : null,
            "author"          => [ "@type" => "Person", "name"  => $post->author_name ],
            "publisher"       => [
                "@type" => "Organization", "name"  => "Afrikenkid",
                "logo"  => [ "@type" => "ImageObject", "url"   => base_url('assets/images/logo.png') ],
            ],
            "description"     => $post->meta_description,
            "mainEntityOfPage" => [ "@type" => "WebPage", "@id"   => url_to('blog.show', $slug) ],
        ];

        $data = [
            'pageTitle'       => esc($post->title) . ' | Afrikenkid Blog',
            'metaDescription' => esc($post->meta_description),
            'canonicalUrl'    => url_to('blog.show', $slug),
            'post'            => $post,
            'json_ld_schema'  => '<script type="application/ld+json">' . json_encode($schema) . '</script>',
        ];
        return view('App\Modules\Blog\Views\blog\post', $data);
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
        return view('App\Modules\Blog\Views\admin\blog\index', $data);
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
        return view('App\Modules\Blog\Views\admin\blog\form', $data);
    }

    public function edit(int $id)
    {
        if (!session()->get('is_admin')) { return redirect()->to(url_to('home')); }
        $post = $this->postModel->find($id);
        if (!$post) { throw PageNotFoundException::forPageNotFound(); }
        $data = [
            'pageTitle'  => 'Edit Post | Admin',
            'formTitle'  => 'Edit Post: ' . esc($post->title),
            'formAction' => url_to('admin.blog.update', $id),
            'post'       => $post,
            'robotsTag'  => 'noindex, nofollow',
        ];
        return view('App\Modules\Blog\Views\admin\blog\form', $data);
    }

    public function store()
    {
        if (!session()->get('is_admin')) { return redirect()->to(url_to('home')); }
        return $this->processPost();
    }

    public function update(int $id)
    {
        if (!session()->get('is_admin')) { return redirect()->to(url_to('home')); }
        return $this->processPost($id);
    }

    private function processPost(?int $id = null)
    {
        $postData = $this->request->getPost();
        
        $contentBlocks = [];
        if (isset($postData['content_type'])) {
            foreach ($postData['content_type'] as $index => $type) {
                $block = ['type' => $type];
                switch ($type) {
                    case 'text':
                        $block['content'] = $postData['content_text'][$index] ?? '';
                        break;
                    case 'image':
                        $block['url'] = $postData['content_text'][$index] ?? '';
                        break;
                    case 'code':
                        $block['code'] = $postData['content_text'][$index] ?? '';
                        $block['language'] = $postData['content_language'][$index] ?? 'plaintext';
                        break;
                }
                $contentBlocks[] = $block;
            }
        }
        
        $payload = [
            'title'              => $postData['title'],
            'excerpt'            => $postData['excerpt'],
            'status'             => $postData['status'],
            'published_at'       => $postData['published_at'],
            'featured_image_url' => $postData['featured_image_url'],
            'category_name'      => $postData['category_name'],
            'meta_description'   => $postData['meta_description'],
            'body_content'       => json_encode($contentBlocks)
        ];

        if ($id !== null) {
            $payload['id'] = $id;
        }

        if ($this->postModel->save($payload)) {
            return redirect()->to(url_to('admin.blog.index'))->with('success', 'Post ' . ($id ? 'updated' : 'created') . ' successfully.');
        }
        return redirect()->back()->withInput()->with('errors', $this->postModel->errors());
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
