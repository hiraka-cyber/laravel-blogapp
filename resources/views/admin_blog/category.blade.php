@extends('admin_blog.app')
@section('title', 'カテゴリ一覧')

@section('head')
    {{--jQuery は下記のファイルに記述し読み込むようにする--}}
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
    $(function () {
    // メタタグに設定したトークンを使って、全リクエストヘッダにCSRFトークンを追加
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // 登録もしくは編集ボタン押下時
    $('[data-toggle=modal]').on('click', function() {
        var target_category_id = $(this).attr('data-category_id');

        // 既存カテゴリーならフォームに値をセット
        if (target_category_id) {
            var target_object = $('tr[data-category_id=' + target_category_id + ']');
            var target_value = {
                category_id   : target_category_id,
                name          : target_object.find('span.name').text(),
                display_order : target_object.find('span.display_order').text()
            };
            $('input[name=category_id]').val(target_value.category_id);
            $('input[name=name]').val(target_value.name);
            $('input[name=display_order]').val(target_value.display_order);
        }
    });

    // モーダルが閉じられるとき、アラートを消し、フォームを空にしておく
    $('#categoryModal').on('hidden.bs.modal', function() {
        $('#api_result').html('').removeClass().addClass('hidden');
        $('input[name=category_id]').val(null);
        $('input[name=name]').val(null);
        $('input[name=display_order]').val(null);
    });

    // 保存ボタン押下時
    $('#category_submit').on('click', function() {
        var category_id = $('input[name=category_id]').val();
        category_id = (category_id) ? category_id : null;
        var data = {
            category_id   : category_id,
            name          : $('input[name=name]').val(),
            display_order : $('input[name=display_order]').val()
        };

        // APIを呼び出してDBに保存
        $.ajax({
            type : 'POST',
            url : 'category/edit',
            data : data

        }).done(function(data) {
            // 正常時 結果表示
            $('#api_result').html('<span>正常に処理が完了しました</span>')
                .removeClass()
                .addClass('alert alert-success show');
            // 少しせこいが、リロードして変更が反映された画面を表示する
            location.reload();

        }).fail(function(data) {
            // エラー時 エラーメッセージ生成
            var error_message = '';
            $.each(data.responseJSON.errors, function(element, message_array) {
                $.each(message_array, function(index, message) {
                    error_message += message + '<br>';
                })
            });

            // エラーメッセージ表示
            $('#api_result').html('<span>' + error_message + '</span>')
                .removeClass()
                .addClass('alert alert-danger show');
        });
    });

    // 削除ボタン押下時
    $('#category_delete').on('click', function() {
        var data = {
            category_id : $('input[name=category_id]').val()
        };

        // APIを呼び出して削除
        $.ajax({
            type : 'POST',
            url : 'category/delete',
            data : data

        }).done(function(data) {
            // 正常時 結果表示
            $('#api_result').html('<span>削除しました</span>')
                .removeClass()
                .addClass('alert alert-success show');
            // リロード
            location.reload();

        }).fail(function(data) {
            // エラー時 エラーメッセージ表示
            $('#api_result').html('<span>削除に失敗しました</span>')
                .removeClass()
                .addClass('alert alert-danger show');
        });
    });
});
    </script>

@endsection

@section('body')
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-md-offset-2">
                <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#categoryModal">
                    Insert
                </button>
                <br>

                @if (count($list) > 0)
                    <br>

                    {{ $list->links() }}
                    <table class="table table-striped">
                        <tr>
                            <th>カテゴリ名</th>
                            <th width="100px">表示順</th>
                            <th width="100px">編集</th>
                        </tr>

                        @foreach ($list as $category)
                            <tr data-category_id="{{ $category->category_id }}">
                                <td>
                                    <span class="name">{{ $category->name }}</span>
                                </td>
                                <td>
                                    <span class="display_order">{{ $category->display_order }}</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#categoryModal" data-category_id="{{ $category->category_id }}">編集</button>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    <br>
                    <p>カテゴリがありません。</p>
                @endif

            </div>
        </div>
    </div>

    <!-- モーダル・ダイアログ -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                <h4 class="modal-title">Edit Category</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>×</span>
                    </button>

                </div>

                <div class="modal-body">
                    {{--API 通信結果表示部分--}}
                    <div id="api_result" class="hidden"></div>

                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label">カテゴリ名</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" placeholder="カテゴリ名">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">表示順</label>
                            <div class="col-sm-10">
                                <input type="text" name="display_order" size="20" class="form-control" placeholder="表示順">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">閉じる</button>
                    <button type="button" id="category_delete" class="btn btn-danger">削除</button>
                    <button type="button" id="category_submit" class="btn btn-primary">保存</button>
                    <input type="hidden" name="category_id">
                </div>

            </div>
        </div>
    </div>
@endsection
