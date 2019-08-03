@extends('layouts.manage', ['title_text' => '<i class="fa fa-file-o m-r-10"></i>', 'item_type' => 'posts',
'customButton' => '<a href="'.route('posts.edit', $post->id).'" class="button is-info is-pulled-right"><i class="fa fa-pencil m-r-10"></i>Edit post</a>'
])
@section('content')
  <div class="card p-b-25 m-b-25">
    <div class="card-content">
      <div class="columns">
        <div class="column">

          <div class="field">
            <label for="title" class="label">Title</label>
            <pre>{{$post->title}}</pre>
          </div>

          <div class="field">
            <div class="field">
              <label for="content" class="label">Excerpt</label>
              <pre>{{$post->excerpt}}</pre>
            </div>
          </div>

          <div class="field">
            <div class="field">
              <label for="content" class="label">Language</label>
              <pre>{{$post->language->name}}</pre>
            </div>
          </div>
          <div class="field">
            <div class="field">
              <label for="content" class="label">Categories</label>
              <pre>@for ($i=0; $i < count($post->categories); $i++){{ $post->categories[$i]->name }}<br>@endfor</pre>
            </div>
          </div>

          <div class="field">
            <div class="field">
              <label for="content" class="label">Published</label>
              <pre>{{$post->published_at}}</pre>
            </div>
          </div>

          <div class="field">
            <div class="field">
              <label for="content" class="label">Content</label>
              <div>{!! $post->content !!}</div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
@endsection
