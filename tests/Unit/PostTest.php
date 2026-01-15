<?php

namespace App\Tests\Unit;

use App\Entity\Post;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    /**
     * Test 1 : Création d'un post
     */
    public function testCanCreatePost(): void
    {
        $post = new Post();
        
        $this->assertInstanceOf(Post::class, $post);
        $this->assertNull($post->getId());
    }

    /**
     * Test 2 : Titre du post
     */
    public function testTitleGetterAndSetter(): void
    {
        $post = new Post();
        $title = 'Mon premier article';
        
        $post->setTitle($title);
        
        $this->assertEquals($title, $post->getTitle());
    }

    /**
     * Test 3 : Contenu du post
     */
    public function testContentGetterAndSetter(): void
    {
        $post = new Post();
        $content = 'Ceci est le contenu de mon article de blog.';
        
        $post->setContent($content);
        
        $this->assertEquals($content, $post->getContent());
    }

    /**
     * Test 4 : Date de publication
     */
    public function testPublishedAtGetterAndSetter(): void
    {
        $post = new Post();
        $publishedAt = new \DateTime('2026-01-13');
        
        $post->setPublishedAt($publishedAt);
        
        $this->assertEquals($publishedAt, $post->getPublishedAt());
        $this->assertInstanceOf(\DateTimeInterface::class, $post->getPublishedAt());
    }

    /**
     * Test 5 : Image du post
     */
    public function testPictureGetterAndSetter(): void
    {
        $post = new Post();
        
        // Par défaut, pas d'image
        $this->assertNull($post->getPicture());
        
        // Ajouter une image
        $picture = 'article-image.jpg';
        $post->setPicture($picture);
        
        $this->assertEquals($picture, $post->getPicture());
    }

    /**
     * Test 6 : Chaînage des méthodes
     */
    public function testFluentInterface(): void
    {
        $post = new Post();
        
        $result = $post
            ->setTitle('Article test')
            ->setContent('Contenu test')
            ->setPicture('image.jpg');
        
        // Le résultat doit être la même instance
        $this->assertSame($post, $result);
    }

    /**
     * Test 7 : Un post peut avoir un titre et contenu en anglais
     */
    public function testEnglishTitleAndContent(): void
    {
        $post = new Post();
        
        $post->setTitleEn('My First Post');
        $post->setContentEn('This is my first blog post in English.');
        
        $this->assertEquals('My First Post', $post->getTitleEn());
        $this->assertEquals('This is my first blog post in English.', $post->getContentEn());
    }
}
