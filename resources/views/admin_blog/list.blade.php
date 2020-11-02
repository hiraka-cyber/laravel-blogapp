{{--@extends ディレクティブで「継承」するテンプレートを指定--}}
@extends('admin_blog.app')
{{--@section ディレクティブで title セクションを定義--}}
@section('title', 'ブログ記事一覧')

{{--@section ディレクティブで body セクションを定義--}}
@section('body')
    <div class="container">
        <div class="row">
            <div class="col-md-9 col-md-offset-2">

                @if (session('message'))
                    <span class="alert alert-success">
                        {{ session('message') }}
                    </span>
                @endif

                @if (count($list) > 0)
                    <br>

                    {{--links メソッドでページングが生成される。しかも生成されるHTMLは Bootstrap と互換性がある--}}
                    {{ $list->links() }}

                        {{--このまま foreach ループにかけることができる--}}
                        @foreach ($list as $article)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="panel-title">{{ $article->title }}</h3>
                            </div>
                            <div class="card-body">
                                {{--nl2br 関数で改行文字を <br> に変換する。これをエスケープせずに表示させるため {!! !!} で囲む--}}
                                {{--ただし、このまま出力するととても危険なので e 関数で htmlspecialchars 関数を通しておく--}}
                                <a href="{{ route('admin_form', ['article_id' => $article->article_id]) }}">
                                {!! nl2br(e($article->body)) !!}
                                </a><br>
                                <div class="text-right">
                                <a href="{{ route('admin_list', ['category_id' => $article->category->category_id]) }}">
                                    {{ $article->category->name }}
                                </a>
                                &nbsp;&nbsp;
                                {{--updated_at も同様に自動的に Carbon インスタンスにキャストされる--}}
                                {{ $article->updated_at->format('Y/m/d H:i:s') }}
                                </div>
                            </div>
                        </div>
                        @endforeach

                @else
                    <br>
                    <p>記事がありません。</p>
                @endif
            </div>
            <div class="col-md-3">
            <ul class="list-group mt-4">
            <strong class="list-group-item active">Category</strong>
                @forelse($category_list as $category)

                    <li class="list-group-item">
                        <a href="{{ route('admin_list', ['category_id' => $category->category_id]) }}">
                            {{ $category->name }}
                        </a>
                    </li>
                @empty
                    <p>カテゴリーがありません</p>
                @endforelse
            </ul>

        </div>
        </div>
    </div>
@endsection
