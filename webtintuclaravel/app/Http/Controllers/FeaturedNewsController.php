<?php

namespace App\Http\Controllers;

use App\Models\FeaturedNews;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeaturedNewsController extends Controller
{
    public function index()
    {
        $featured = FeaturedNews::with(['news' => function ($query) {
            $query->select('RowID', 'RowIDCat', 'Name', 'Title', 'Alias', 'Images', 'Image', 'SmallDescription', 'Views', 'View', 'Status', 'created_at');
        }])
            ->orderBy('position', 'ASC')
            ->orderBy('Sort', 'ASC')
            ->get();

        $grouped = [
            'main' => $featured->where('position', 1)->values(),
            'sidebar' => $featured->where('position', 2)->values(),
        ];

        return view('back.featured.list', compact('grouped'));
    }

    public function create()
    {
        $news = $this->getSelectableNews();

        return view('back.featured.add', compact('news'));
    }

    public function store(Request $request)
    {
        Log::info('FeaturedNews store:START', [
            'news_id' => $request->news_id,
            'position' => $request->position,
            'method' => $request->method(),
        ]);

        $news_id = (int) $request->news_id;
        $position = (int) $request->position;
        $sort = FeaturedNews::getNextSort($position);
        $status = (int) ($request->Status ?? 1);

        if ($news_id < 1) {
            return back()->withInput()->with('error', 'ID bài viết không hợp lệ!');
        }

        if (!in_array($position, [1, 2], true)) {
            return back()->withInput()->with('error', 'Vị trí không hợp lệ!');
        }

        $newsExists = News::where('RowID', $news_id)->exists();
        Log::info('FeaturedNews store:news_check', ['news_id' => $news_id, 'found' => $newsExists]);
        if (!$newsExists) {
            return back()->withInput()->with('error', 'Bài viết không tồn tại!');
        }

        $existingRow = DB::select(
            'SELECT RowID FROM featured_news WHERE news_id = ? AND position = ?',
            [$news_id, $position]
        );
        if (!empty($existingRow)) {
            return back()->withInput()->with('error', 'Bài viết này đã được thêm vào vị trí này!');
        }

        DB::insert(
            'INSERT INTO featured_news (news_id, position, Sort, Status, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())',
            [$news_id, $position, $sort, $status]
        );

        Log::info('FeaturedNews store:SUCCESS', ['news_id' => $news_id, 'position' => $position]);
        return redirect()->route('featured.list')->with('success', 'Đã thêm bài viết nổi bật!');
    }

    public function edit($id)
    {
        $item = FeaturedNews::findOrFail($id);
        $news = $this->getSelectableNews();
        if ($item->news_id && !$news->contains('RowID', (int) $item->news_id)) {
            $placeholder = new News([
                'RowID' => (int) $item->news_id,
                'Name' => '[ID ' . $item->news_id . '] Bài viết không còn tồn tại',
                'Status' => 0,
            ]);
            $placeholder->setRelation('category', null);
            $news->prepend($placeholder);
        }

        return view('back.featured.edit', compact('item', 'news'));
    }

    public function update(Request $request, $id)
    {
        Log::info('FeaturedNews update:START', [
            'id' => $id,
            'news_id' => $request->news_id,
            'position' => $request->position,
        ]);

        $news_id = (int) $request->news_id;
        $position = (int) $request->position;
        $sort = (int) ($request->Sort ?? 0);
        $status = (int) ($request->Status ?? 1);

        $item = FeaturedNews::findOrFail($id);

        $newsExists = News::where('RowID', $news_id)->exists();
        Log::info('FeaturedNews update:news_check', ['news_id' => $news_id, 'found' => $newsExists]);
        if (!$newsExists) {
            return back()->withInput()->with('error', 'Bài viết không tồn tại!');
        }

        $existingRow = DB::select(
            'SELECT RowID FROM featured_news WHERE news_id = ? AND position = ? AND RowID != ?',
            [$news_id, $position, $id]
        );
        if (!empty($existingRow)) {
            return back()->withInput()->with('error', 'Bài viết này đã được thêm vào vị trí này!');
        }

        DB::update(
            'UPDATE featured_news SET news_id = ?, position = ?, Sort = ?, Status = ?, updated_at = NOW() WHERE RowID = ?',
            [$news_id, $position, $sort, $status, $id]
        );

        Log::info('FeaturedNews update:SUCCESS', ['id' => $id]);
        return redirect()->route('featured.list')->with('success', 'Đã cập nhật!');
    }

    public function destroy($id)
    {
        $item = FeaturedNews::findOrFail($id);
        $item->delete();

        return back()->with('success', 'Đã xóa!');
    }

    public function toggle($id)
    {
        $item = FeaturedNews::findOrFail($id);
        $item->Status = $item->Status == 1 ? 0 : 1;
        $item->save();

        return back()->with('success', $item->Status == 1 ? 'Đã bật!' : 'Đã tắt!');
    }

    public function bulkAction(Request $request)
    {
        $action = $request->action;
        $ids = $request->ids ?? [];

        if (empty($ids)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một mục!');
        }

        if ($action === 'delete') {
            FeaturedNews::whereIn('RowID', $ids)->delete();
            return back()->with('success', 'Đã xóa ' . count($ids) . ' mục!');
        }

        if ($action === 'toggle') {
            FeaturedNews::whereIn('RowID', $ids)->update([
                'Status' => DB::raw('IF(Status=1,0,1)')
            ]);
            return back()->with('success', 'Đã cập nhật trạng thái!');
        }

        return back()->with('error', 'Hành động không hợp lệ!');
    }

    private function getSelectableNews()
    {
        return News::with('category:RowID,Name')
            ->select('RowID', 'RowIDCat', 'Name', 'Alias', 'Status')
            ->orderBy('RowID', 'DESC')
            ->get();
    }
}
