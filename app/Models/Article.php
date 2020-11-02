<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class Article extends Model
{
    use HasFactory;
    // SoftDeletes トレイトを使う
    use SoftDeletes;

    // 対象テーブルのプライマリキーのカラム名を指定する。デフォルトは 'id' というカラム名が想定されている。
    protected $primaryKey = 'article_id';

    // 「複数代入」を利用するときに指定する。追加・編集可能なカラム名のみを指定する。
    // $guarded プロパティを利用すると、逆に、追加・編集不可能なカラムを指定できる。
    protected $fillable = ['category_id', 'title', 'body'];

    // $dates プロパティには、日時が入るカラムを設定する（日付ミューテタ）
    // そうすると、その値が自動的に Carbon インスタンスに変換される
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Category モデルのリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        // 記事は1つのカテゴリーと関係しているので、hasOne メソッドを利用する
        // 第一引数は関係するモデルの名前で、第二・第三引数は外部キーです
        return $this->hasOne('App\Models\Category', 'category_id', 'category_id');
    }

    /**
     * 記事リストを取得する
     *
     * @param  int   $num_per_page 1ページ当たりの表示件数
     * @param  array $condition    検索条件
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getArticleList(int $num_per_page = 10, array $condition = [])
    {
        // パラメータの取得
        $category_id = Arr::get($condition, 'category_id');
        $year        = Arr::get($condition, 'year');
        $month       = Arr::get($condition, 'month');

        // Eager ロードの設定を追加
        $query = $this->with('category')->orderBy('article_id', 'desc');

        // カテゴリーIDの指定
        if ($category_id) {
            $query->where('category_id', $category_id);
        }

        // 期間の指定
        if ($year) {
            if ($month) {
                // 月の指定がある場合はその月を設定し、Carbonインスタンスを生成
                $start_date = Carbon::createFromDate($year, $month, 1);
                $end_date   = Carbon::createFromDate($year, $month, 1)->addMonth();     // 1ヶ月後
            } else {
                // 月の指定が無い場合は1月に設定し、Carbonインスタンスを生成
                $start_date = Carbon::createFromDate($year, 1, 1);
                $end_date   = Carbon::createFromDate($year, 1, 1)->addYear();           // 1年後
            }

        }

        // paginate メソッドを使うと、ページネーションに必要な全件数やオフセットの指定などは全部やってくれる
        return $query->paginate($num_per_page);
    }

    /**
     * 月別アーカイブの対象月のリストを取得
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getMonthList()
    {
        // selectRaw メソッドを使うと、引数にSELECT文の中身を書いてそのまま実行できる
        // 返り値はコレクション（Illuminate\Database\Eloquent\Collection Object）
        // コレクションとは配列データを操作するための便利なラッパーで、多種多様なメソッドが用意されている
        $month_list = $this->get();

        foreach ($month_list as $value) {
            // YYYY-MM をハイフンで分解して、YYYY年MM月という表記を作る
            list($year, $month) = explode('-', $value);
            $value->year  = (int)$year;
            $value->month = (int)$month;
            $value->year_month = sprintf("%04d年%02d月", $year, $month);
        }
        return $month_list;
    }

}
