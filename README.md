Not supported [![No Maintenance Intended](http://unmaintained.tech/badge.svg)](http://unmaintained.tech/)
=============

This project is not supported anymore, you can try [matryoshka](https://github.com/laracasts/matryoshka) instead.

laravel-fragment-caching
========================

Add a Fragment caching support helper. [Blog post](http://gustaf.espontanea.io/blog/2014/02/09/laravel-fragment-caching)

Installation
============

Run: `composer require gchaincl/laravel-fragment-caching:dev-master`
or
 * add: 	`"require": { "gchaincl/laravel-fragment-caching": "dev-master" }, `to composer.json
 * run: `composer install`
 * add: The following to your `app/config/app.php`
```php
$providers => array(
  ...
 	'Gchaincl\LaravelFragmentCaching\ViewServiceProvider',
)
``` 


Usage
=====

In your view:
```php
<ul>
@foreach ($posts as $post)

@cache("post" . $post->id)
    <li> {{ link_to_route('post.show', $post->title, $post->id) }} ({{ $post->user->username }})</li>
@endcache

@endforeach
</ul>
```

First time we load that view, Framework will run 3 queries:
```sql
select * from "posts"
select * from "users" where "users"."id" = '5' limit 1
select * from "users" where "users"."id" = '5' limit 1
```

Second time, as fragments are already cached, there will be just one query:
```sql
select * from "posts"
```

Conditional caching
===================

In situations where you don't always want to cache a block you can use `@cacheif($condition, $cacheId)`

```php
{{-- Only use the cache for guests, admins will always get content rendered from the template --}}
@cacheif( Auth::guest(), "post" . $post->id)
    <li> {{ link_to_route('post.show', $post->title, $post->id) }} (@if (Auth::guest()) {{ $post->user->username }} @else {{ $post->user->email }} @endif)</li>
@endcacheif
```

Tip
---

To update view rendering on model changes, you should expire your fragments:

```php
// app/model/Post.php

class Post extends Eloquent {

    public static function boot() {
        parent::boot();
        static::updated(function($model) {
            Cache::forget("post" . $model->id);
        });
    }
}
```
