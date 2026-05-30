<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\News;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $query = Tag::query();

        if ($request->has('keyword') && $request->keyword != '') {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', (int) $request->status);
        }

        $tags = $query->orderBy('popular_count', 'DESC')->paginate(20);

        return view('back.tag.list', compact('tags'));
    }

    public function create()
    {
        return view('back.tag.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:tags,name',
        ], [
            'name.required' => 'Vui lòng nhập tên tag.',
            'name.unique'   => 'Tag này đã tồn tại.',
        ]);

        Tag::create([
            'name'       => trim($request->name),
            'slug'       => Str::slug($request->name),
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'status'     => $request->has('status') ? 1 : 0,
        ]);

        return redirect('admin/tag/list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Thêm tag thành công.',
        ]);
    }

    public function edit($id)
    {
        $tag = Tag::findOrFail($id);
        return view('back.tag.edit', compact('tag'));
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100|unique:tags,name,' . $id,
            'slug' => 'required|string|max:120|unique:tags,slug,' . $id,
        ], [
            'name.required' => 'Vui lòng nhập tên tag.',
            'name.unique'   => 'Tag này đã tồn tại.',
            'slug.required' => 'Vui lòng nhập slug.',
            'slug.unique'   => 'Slug này đã được sử dụng.',
        ]);

        $tag->update([
            'name'       => trim($request->name),
            'slug'       => Str::slug($request->slug),
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'status'     => $request->has('status') ? 1 : 0,
        ]);

        return redirect()->back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Cập nhật tag thành công.',
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);
        // Xoa tat ca pivot records truoc khi xoa tag de tranh orphan data
        DB::table('news_tags')->where('tag_id', $id)->delete();
        $tag->delete();

        return redirect()->route('tag.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Xóa tag thành công.',
        ]);
    }

    public function toggleStatus(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->status = $tag->status ? 0 : 1;
        $tag->save();

        return back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Cập nhật trạng thái thành công.',
        ]);
    }

    // Frontend: tag detail
    public function show($slug, Request $request)
    {
        $tag = Tag::where('slug', $slug)->where('status', 1)->firstOrFail();

        $newsIds = DB::table('news_tags')
            ->where('tag_id', $tag->id)
            ->pluck('news_id');

        $query = News::whereIn('RowID', $newsIds)
            ->where('Status', 1)
            ->orderBy('RowID', 'DESC');

        $listNews = $query->paginate(12);

        return view('front.news.tag', compact('tag', 'listNews'));
    }

    public function apiSearch(Request $request)
    {
        $q = $request->q ?? '';
        $tags = Tag::where('name', 'like', '%' . $q . '%')
            ->where('status', 1)
            ->orderBy('popular_count', 'DESC')
            ->limit(10)
            ->get(['id', 'name', 'slug']);

        return response()->json($tags);
    }
}
