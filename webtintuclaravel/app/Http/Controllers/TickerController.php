<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsTicker;
use Illuminate\Http\Request;

class TickerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Danh sách tin nóng
     */
    public function list(Request $request)
    {
        $query = NewsTicker::with(['news' => function ($query) {
                $query->select('RowID', 'Name', 'Title', 'Alias', 'Status', 'publish');
            }])
            ->orderBy('Sort', 'ASC')
            ->orderBy('RowID', 'DESC');

        if ($request->filled('keyword')) {
            $kw = '%' . trim($request->keyword) . '%';
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', $kw);
                $q->orWhereHas('news', function ($qn) use ($kw) {
                    $qn->where('Name', 'like', $kw);
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('Status', (int) $request->status);
        }

        $Ticker = $query->paginate(20);

        return view('back.ticker.list', compact('Ticker'));
    }

    /**
     * Form thêm tin nóng
     */
    public function getAdd()
    {
        $News = $this->getSelectableNews();

        return view('back.ticker.add', compact('News'));
    }

    /**
     * Xử lý thêm tin nóng
     */
    public function postAdd(Request $request)
    {
        $request->validate([
            'title' => 'nullable|max:255',
            'news_id' => 'nullable|integer|exists:news,RowID',
            'Status' => 'required|in:0,1',
            'Sort' => 'nullable|integer',
        ], [
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'news_id.integer' => 'Bài viết không hợp lệ.',
            'news_id.exists' => 'Bài viết không tồn tại.',
        ]);

        $title = trim($request->title);
        $alias = null;

        if ($request->filled('news_id')) {
            $news = News::find($request->news_id);
            if ($news) {
                $alias = $news->Alias . '.html';
                if (empty($title)) {
                    $title = $news->Name;
                }
            }
        } elseif (!empty($title)) {
            $alias = '/' . \Illuminate\Support\Str::slug($title, '-') . '.html';
        }

        $sort = $request->Sort ?? (NewsTicker::max('Sort') + 1);

        NewsTicker::create([
            'news_id' => $request->filled('news_id') ? $request->news_id : null,
            'title' => $title ?: null,
            'alias' => $alias,
            'Status' => $request->Status,
            'Sort' => $sort,
        ]);

        return redirect('admin/ticker/list')->with('success', 'Đã thêm tin nóng thành công.');
    }

    /**
     * Form sửa tin nóng
     */
    public function getEdit($id)
    {
        $Ticker = NewsTicker::findOrFail($id);
        $News = $this->getSelectableNews();
        if ($Ticker->news_id && !$News->contains('RowID', (int) $Ticker->news_id)) {
            $placeholder = new News([
                'RowID' => (int) $Ticker->news_id,
                'Name' => '[ID ' . $Ticker->news_id . '] Bài viết không còn tồn tại',
                'Status' => 0,
            ]);
            $placeholder->setRelation('category', null);
            $News->prepend($placeholder);
        }

        return view('back.ticker.edit', compact('Ticker', 'News'));
    }

    /**
     * Xử lý sửa tin nóng
     */
    public function postEdit(Request $request, $id)
    {
        $Ticker = NewsTicker::findOrFail($id);

        $request->validate([
            'title' => 'nullable|max:255',
            'news_id' => 'nullable|integer|exists:news,RowID',
            'Status' => 'required|in:0,1',
            'Sort' => 'nullable|integer',
        ], [
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'news_id.integer' => 'Bài viết không hợp lệ.',
            'news_id.exists' => 'Bài viết không tồn tại.',
        ]);

        $title = trim($request->title);
        $alias = null;

        if ($request->filled('news_id')) {
            $news = News::find($request->news_id);
            if ($news) {
                $alias = $news->Alias . '.html';
                if (empty($title)) {
                    $title = $news->Name;
                }
            }
        } elseif (!empty($title)) {
            $alias = '/' . \Illuminate\Support\Str::slug($title, '-') . '.html';
        }

        $Ticker->update([
            'news_id' => $request->filled('news_id') ? $request->news_id : null,
            'title' => $title ?: null,
            'alias' => $alias,
            'Status' => $request->Status,
            'Sort' => $request->Sort ?? $Ticker->Sort,
        ]);

        return redirect('admin/ticker/edit/' . $id)->with('success', 'Đã cập nhật tin nóng.');
    }

    /**
     * Xóa tin nóng
     */
    public function delete($id)
    {
        $Ticker = NewsTicker::findOrFail($id);
        $Ticker->delete();

        return redirect('admin/ticker/list')->with('success', 'Đã xóa tin nóng.');
    }

    /**
     * Toggle trạng thái (ajax)
     */
    public function toggle($id)
    {
        $Ticker = NewsTicker::findOrFail($id);
        $Ticker->update(['Status' => $Ticker->Status == 1 ? 0 : 1]);

        return response()->json([
            'success' => true,
            'status' => $Ticker->Status,
        ]);
    }

    private function getSelectableNews()
    {
        return News::with('category:RowID,Name')
            ->select('RowID', 'RowIDCat', 'Name', 'Alias', 'Status')
            ->orderBy('RowID', 'DESC')
            ->get();
    }
}
