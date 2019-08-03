<div class="sidebar">
  <section class="main-blog">
    <div class="box">
      <div class="box-content">
      <h4 class="title is-4">Featured Articles</h4>
      <hr>
        @foreach ($articles as $article)
        <h3 class="subtitle is-5">
          {{$article->title}}
        </h3>
        @endforeach
      </div>
    </div>
  </section>
</div>
