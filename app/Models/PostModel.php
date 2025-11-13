<?php declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Post;

class PostModel extends Model
{
    protected $table            = 'posts';
    protected $primaryKey       = 'id';
    protected $returnType       = Post::class;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'title', 'slug', 'excerpt', 'body_html', 'featured_image_url',
        'author_name', 'category_name', 'meta_description', 'status', 'published_at'
    ];

    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug'];

    /**
     * Generates a URL-friendly slug from the post title.
     *
     * @param array $data
     * @return array
     */
    protected function generateSlug(array $data): array
    {
        if (isset($data['data']['title'])) {
            $slug = url_title($data['data']['title'], '-', true);
            
            // Check if an ID is present (for updates)
            $id = $data['id'] ?? null;

            // Check for existing slugs, excluding the current post if it's an update
            $builder = $this->builder();
            $builder->where('slug', $slug);
            if ($id !== null) {
                $builder->where('id !=', $id);
            }
            $existing = $builder->get()->getRow();

            if ($existing) {
                $slug .= '-' . uniqid();
            }
            $data['data']['slug'] = $slug;
        }
        return $data;
    }
}
