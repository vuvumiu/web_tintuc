<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\System;
use App\Models\Page;
use App\Models\Social;
use App\Models\Newsletter;
use App\Models\Contact;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsComment;
use App\Models\NewsRating;
use App\Models\NewsViewStat;
use App\Models\CommentVote;
use App\Models\Tag;
use App\Models\Slider;
use App\Models\NewsTicker;
use App\Models\FeaturedNews;
use App\Models\Ad;
use App\Mail\ContactReceivedMail;
use App\Mail\ContactAutoReplyMail;
use App\Mail\NewsletterConfirmMail;
use Illuminate\Support\Facades\Mail;

class FrontController extends Controller
{

    public function __construct()
    {
        @session_start();
    }

    public function home()
    {
        $PageInfo = Page::where("Status", 1)->where("Alias", "/")
            ->selectRaw("Name, Images, Alias, MetaTitle, MetaDescription, MetaKeyword")->first();

        // Lấy bài viết nổi bật (Featured News)
        $featuredNewsScope = function ($q) {
            $q->where('news.Status', 1)
                ->leftJoin('news_cat as b', 'news.RowIDCat', '=', 'b.RowID')
                ->leftJoin('users as c', 'news.author_id', '=', 'c.id')
                ->selectRaw('news.*, COALESCE(news.Name, news.Title) as Name, COALESCE(news.Images, news.Image) as Images, COALESCE(news.Views, news.View, 0) as Views, b.Name as CategoryName, b.Alias as CategoryAlias, c.fullname as AuthorName');
        };

        $FeaturedMain = FeaturedNews::whereHas('news', function ($q) {
            $q->where('Status', 1);
        })->with(['news' => $featuredNewsScope])->mainFeatured()->first();

        $FeaturedSidebar = FeaturedNews::whereHas('news', function ($q) {
            $q->where('Status', 1);
        })->with(['news' => $featuredNewsScope])->sidebarFeatured()->limit(4)->get();

        // Nếu không có featured news, fallback vào bài viết mới nhất (chỉ cho tin chính)
        if (!$FeaturedMain || !$FeaturedMain->news) {
            $fallback = DB::table('news as a')
                ->leftJoin('news_cat as b', 'a.RowIDCat', '=', 'b.RowID')
                ->leftJoin('users as c', 'a.author_id', '=', 'c.id')
                ->selectRaw('a.*, COALESCE(a.Name, a.Title) as Name, COALESCE(a.Images, a.Image) as Images, COALESCE(a.Views, a.View, 0) as Views, b.Name as CategoryName, b.Alias as CategoryAlias, c.fullname as AuthorName')
                ->where('a.Status', 1)
                ->orderBy('a.RowID', 'DESC')
                ->first();

            if ($fallback) {
                $FeaturedMain = (object)[
                    'news' => $fallback,
                ];
            }
        }

        // Tin phụ chỉ hiển thị những gì admin chọn (không fallback)

        $NewsCategories = NewsCategory::where("Status", 1)
            ->orderBy("RowID", "ASC")
            ->get();

        $CategoriesWithNews = [];
        foreach ($NewsCategories as $catIndex => $cat) {
            $categoryLimit = $catIndex === 0 ? 5 : 4;

            $news = DB::table("news as a")
                ->join("news_cat as b", "a.RowIDCat", "=", "b.RowID")
                ->selectRaw("a.*, COALESCE(a.Name, a.Title) as Name, COALESCE(a.Images, a.Image) as Images, COALESCE(a.Views, a.View, 0) as Views, b.Name as CategoryName, b.Alias as CategoryAlias")
                ->where("a.RowIDCat", $cat->RowID)
                ->where("a.Status", 1)
                ->orderBy("a.RowID", "DESC")
                ->limit($categoryLimit)
                ->get();

            $CategoriesWithNews[] = [
                "category" => $cat,
                "news" => $news,
            ];
        }

        $NewsViews = DB::table("news as a")
            ->join("news_cat as b", "a.RowIDCat", "=", "b.RowID")
            ->selectRaw("a.*, COALESCE(a.Name, a.Title) as Name, COALESCE(a.Images, a.Image) as Images, COALESCE(a.Views, a.View, 0) as Views, b.Name as CategoryName")
            ->where("a.Status", 1)
            ->orderByRaw("COALESCE(a.Views, a.View, 0) DESC")
            ->limit(4)->get();

        $Slider = Slider::where("Status", 1)
            ->selectRaw("Name, Alias, Images")
            ->orderBy("Sort", "ASC")
            ->get();

        $Tickers = NewsTicker::active()
            ->where(function ($q) {
                $q->whereNull('news_id');
                $q->orWhereHas('news', function ($newsQuery) {
                    $newsQuery->where('Status', 1);
                });
            })
            ->with(['news' => function ($q) {
                $q->where('Status', 1)
                    ->select('RowID', 'Name', 'Title', 'Alias', 'Status');
            }])
            ->get();

        // Carousel data
        $heroMain = $FeaturedMain ? $FeaturedMain->news : null;
        $heroSidebar = $FeaturedSidebar->map(function($item) { return $item->news; })->toArray();
        $heroSlides = $heroSidebar;
        if ($heroMain) array_unshift($heroSlides, $heroMain);
        $hasCarousel = count($heroSlides) > 1;

        // Quảng cáo popup cho trang chủ
        return view("front.home.home", compact(
            "PageInfo", "CategoriesWithNews", "NewsViews", "Slider", "Tickers",
            "FeaturedMain", "FeaturedSidebar", "heroMain", "heroSidebar", "heroSlides", "hasCarousel"
        ));
    }

    public function about()
    {
        $PageInfo = Page::where("Status", 1)
            ->where("Alias", "ve-chung-toi")
            ->selectRaw("Name, Images, Alias, MetaTitle, MetaDescription, MetaKeyword, Description")
            ->first();

        return view("front.about.about", compact("PageInfo"));
    }

    public function contact()
    {
        $PageInfo = Page::where("Status", 1)
            ->where("Alias", "lien-he")
            ->selectRaw("Name, Images, Alias, MetaTitle, MetaDescription, MetaKeyword, Description")
            ->first();

        $Map = System::where("Status", 1)
            ->where("Code", "map")
            ->selectRaw("Description")
            ->first();

        return view("front.contact.contact", compact("PageInfo", "Map"));
    }

    public function search(Request $request)
    {
        $PageInfo = Page::where("Status", 0)->where("Alias", "tim-kiem")
            ->selectRaw("Name, Images, Alias, MetaTitle, MetaDescription, MetaKeyword, Description")
            ->first();

        if (isset($request->keyword) && $request->keyword != null) {
            $searchList = News::where("Status", 1)
                ->where(function ($query) use ($request) {
                    $keyword = "%" . $request->keyword . "%";
                    $query->where("Name", "like", $keyword)
                        ->orWhere("Title", "like", $keyword);
                })
                ->selectRaw("RowID, COALESCE(Name, Title) as Name, Alias, COALESCE(Images, Image) as Images, SmallDescription, COALESCE(Views, View, 0) as Views, created_at")
                ->paginate(12);
        } else {
            $searchList = null;
        }

        return view("front.search.search", compact("PageInfo", "searchList"));
    }

    public function latestNews()
    {
        $pageTitle = "Tin mới nhất";
        $alias = "tin-moi-nhat";

        $listNews = DB::table("news")
            ->where("Status", 1)
            ->selectRaw("Alias, COALESCE(Name, Title) as Name, COALESCE(Images, Image) as Images, SmallDescription, COALESCE(Views, View, 0) as Views, created_at")
            ->orderBy("RowID", "DESC")
            ->paginate(12);

        return view("front.news.list", compact("pageTitle", "listNews", "alias"));
    }

    public function topViewedNews()
    {
        $pageTitle = "Tin nổi bật";
        $alias = "tin-noi-bat";

        $listNews = DB::table("news")
            ->where("Status", 1)
            ->selectRaw("Alias, COALESCE(Name, Title) as Name, COALESCE(Images, Image) as Images, SmallDescription, COALESCE(Views, View, 0) as Views, created_at")
            ->orderByRaw("COALESCE(Views, View, 0) DESC")
            ->paginate(12);

        return view("front.news.list", compact("pageTitle", "listNews", "alias"));
    }

    public function slug($slug, Request $request)
    {
        $newsCat = NewsCategory::where("Status", 1)->where("Alias", $slug)->first();

        if (isset($newsCat) && $newsCat != null) {
            if (isset($request->sapxep) && $request->sapxep == "luotxem") {
                $listNews = DB::table("news as a")
                    ->join("news_cat as b", "a.RowIDCat", "=", "b.RowID")
                    ->where("a.Status", 1)
                    ->where("b.Alias", $slug)
                    ->selectRaw("a.Alias, COALESCE(a.Name, a.Title) as Name, COALESCE(a.Images, a.Image) as Images, a.SmallDescription, COALESCE(a.Views, a.View, 0) as Views, a.created_at, b.Name as CategoryName, b.Alias as CategoryAlias")
                    ->orderByRaw("COALESCE(a.Views, a.View, 0) DESC")
                    ->paginate(12);

                $sort = "luotxem";
            } else {
                $listNews = DB::table("news as a")
                    ->join("news_cat as b", "a.RowIDCat", "=", "b.RowID")
                    ->where("a.Status", 1)
                    ->where("b.Alias", $slug)
                    ->selectRaw("a.Alias, COALESCE(a.Name, a.Title) as Name, COALESCE(a.Images, a.Image) as Images, a.SmallDescription, COALESCE(a.Views, a.View, 0) as Views, a.created_at, b.Name as CategoryName, b.Alias as CategoryAlias")
                    ->orderBy("a.RowID", "DESC")
                    ->paginate(12);

                $sort = "tinmoi";
            }

            return view("front.news.cat", compact("newsCat", "listNews", "sort"));
        }

        $PageInfo = Page::where("Status", 1)->where("Alias", $slug)->first();

        if (isset($PageInfo) && $PageInfo != null) {
            return view("front.about.about", compact("PageInfo"));
        }

        return redirect("/");
    }

    public function slugHtml($slug, Request $request)
    {
        $newsDetail = DB::table("news as a")
            ->join("news_cat as b", "a.RowIDCat", "=", "b.RowID")
            ->leftJoin("users as c", "a.author_id", "=", "c.id")
            ->where("a.Status", 1)
            ->where("a.Alias", $slug)
            ->selectRaw("a.*, COALESCE(a.Name, a.Title) as Name, COALESCE(a.Images, a.Image) as Images, COALESCE(a.Views, a.View, 0) as Views, b.Name as NewsCatName, b.Alias as NewsCatAlias, c.fullname as AuthorName, c.username as AuthorUsername, c.avatar as AuthorAvatar")
            ->first();

        if (!$newsDetail) {
            return redirect("/");
        }

        // Tăng lượt xem (mỗi session/IP chỉ tăng 1 lần mỗi bài viết trong 24h)
        $viewKey = 'viewed_news_' . $newsDetail->RowID;
        $lastViewedAt = session($viewKey);
        $shouldCountView = true;

        if ($lastViewedAt) {
            try {
                $shouldCountView = Carbon::createFromTimestamp((int) $lastViewedAt)->diffInHours(now()) >= 24;
            } catch (\Throwable $exception) {
                $shouldCountView = true;
            }
        }

        if ($shouldCountView) {
            News::where('RowID', $newsDetail->RowID)->increment('Views');
            NewsViewStat::recordView((int) $newsDetail->RowID, now());
            session([$viewKey => now()->timestamp]);
            $newsDetail->Views = ($newsDetail->Views ?? 0) + 1;
            $isNewView = true;
        } else {
            $isNewView = false;
        }

        // Get tags
        $newsTags = DB::table("news_tags as nt")
            ->join("tags as t", "nt.tag_id", "=", "t.id")
            ->where("nt.news_id", $newsDetail->RowID)
            ->where("t.status", 1)
            ->select("t.id", "t.name", "t.slug")
            ->get();

        // Related news: same category first, then tags, then latest fallback.
        $relatedNews = optional(News::find($newsDetail->RowID))->getRelatedNews(4) ?? collect();

        // Comments with pagination
        $comments = NewsComment::with(["user", "replies.user"])
            ->where("news_id", $newsDetail->RowID)
            ->where("is_active", true)
            ->root()
            ->orderBy("created_at", "DESC")
            ->paginate(10);

        $avgRating = NewsRating::where("news_id", $newsDetail->RowID)->avg("score") ?? 0;
        $totalRating = NewsRating::where("news_id", $newsDetail->RowID)->count();

        $distRows = NewsRating::where("news_id", $newsDetail->RowID)
            ->selectRaw("score, COUNT(*) as cnt")
            ->groupBy("score")
            ->pluck("cnt", "score")
            ->toArray();
        $ratingDistribution = [];
        for ($s = 1; $s <= 5; $s++) {
            $ratingDistribution[$s] = (int) ($distRows[$s] ?? 0);
        }

        $userRating = 0;
        $userFavorite = false;
        $commentVoteMap = [];
        if (Auth::check()) {
            $userRatingObj = NewsRating::where("news_id", $newsDetail->RowID)
                ->where("user_id", Auth::id())
                ->first();
            if ($userRatingObj) {
                $userRating = $userRatingObj->score;
            }
            $userFavorite = \App\Models\UserFavorite::isFavorited(Auth::id(), $newsDetail->RowID);

            // Load vote map for all comment IDs
            $commentIds = $comments->pluck('id')->toArray();
            $replyIds = [];
            foreach ($comments as $c) {
                foreach ($c->replies as $r) {
                    $replyIds[] = $r->id;
                }
            }
            $allIds = array_merge($commentIds, $replyIds);
            if (!empty($allIds)) {
                $votes = CommentVote::where('user_id', Auth::id())
                    ->whereIn('comment_id', $allIds)
                    ->get();
                foreach ($votes as $v) {
                    $commentVoteMap[$v->comment_id] = $v->vote_type;
                }
            }
        }

        $newsDetail->average_rating = round($avgRating, 1);
        $newsDetail->total_rating = $totalRating;
        $newsDetail->user_rating = $userRating;
        $newsDetail->user_favorite = $userFavorite;
        $newsDetail->rating_distribution = $ratingDistribution;

        // Quảng cáo popup cho trang bài viết
        return view("front.news.detail", compact(
            "newsDetail", "comments", "newsTags", "relatedNews", "commentVoteMap", "isNewView"
        ));
    }

    // ============================================================
    // LIEN HE
    // ============================================================
    public function contactSendEmail(Request $request)
    {
        try {
            $validated = $request->validate([
                "txtName"    => "required|string|max:100",
                "txtEmail"   => "required|email|max:150",
                "txtPhone"   => "required|string|max:20",
                "txtSubject" => "nullable|string|max:255",
                "selCategory" => "nullable|string|in:consult,complaint,cooperation,other",
                "txtMessage" => "required|string|min:10|max:2000",
            ], [
                "txtName.required"    => "Vui long nhap ho va ten.",
                "txtEmail.required"   => "Vui long nhap email.",
                "txtEmail.email"      => "Email khong hop le.",
                "txtPhone.required"   => "Vui long nhap so dien thoai.",
                "txtMessage.required" => "Vui long nhap loi nhan.",
                "txtMessage.min"      => "Loi nhan qua ngan (toi thieu 10 ky tu).",
                "txtMessage.max"      => "Loi nhan khong duoc qua 2000 ky tu.",
            ]);

            $contactData = [
                "Name"       => trim($validated["txtName"]),
                "Email"      => trim($validated["txtEmail"]),
                "Phone"      => trim($validated["txtPhone"]),
                "subject"    => $validated["txtSubject"] ?? null,
                "category"   => $validated["selCategory"] ?? "consult",
                "priority"   => Contact::PRIORITY_MEDIUM,
                "Message"    => trim($validated["txtMessage"]),
                "ip_address" => $request->ip(),
            ];

            Contact::create($contactData);
            $this->sendContactAdminNotification($contactData);
            $this->sendContactAutoReply($contactData);

            return response()->json([
                "success" => true,
                "message" => "Chung toi da nhan duoc lien he cua ban va se som tra loi!",
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            return response()->json([
                "success" => false,
                "message" => $firstError ?? "Vui long kiem tra lai thong tin.",
            ], 422);
        } catch (\Throwable $e) {
            Log::error("Loi xu ly lien he: " . $e->getMessage());
            return response()->json([
                "success" => false,
                "message" => "Co loi xay ra, vui long thu lai sau.",
            ], 500);
        }
    }

    // ============================================================
    // NEWSLETTER
    // ============================================================
    public function subEmail(Request $request)
    {
        try {
            $validated = $request->validate([
                "txtEmailSub" => "required|email|max:150",
            ], [
                "txtEmailSub.required" => "Vui long nhap email.",
                "txtEmailSub.email"    => "Email khong hop le.",
            ]);

            $email = trim($validated["txtEmailSub"]);

            $existing = Newsletter::where("Email", $email)->first();

            if ($existing) {
                if ($existing->is_active && !$existing->unsubscribed_at) {
                    return response()->json([
                        "success" => false,
                        "message" => "Email nay da duoc dang ky nhan tin.",
                    ]);
                }

                $existing->markAsSubscribed();
                $existing->ip_address = $request->ip();
                $existing->save();

                $this->sendNewsletterConfirmEmail($existing);

                return response()->json([
                    "success" => true,
                    "message" => "Dang ky lai thanh cong! Vui long kiem tra email de xac nhan.",
                ]);
            }

            $newsletter = Newsletter::create([
                "Email"      => $email,
                "is_active"  => false,
                "ip_address"  => $request->ip(),
            ]);

            $this->sendNewsletterConfirmEmail($newsletter);

            return response()->json([
                "success" => true,
                "message" => "Dang ky thanh cong! Vui long kiem tra email de xac nhan dang ky.",
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            return response()->json([
                "success" => false,
                "message" => $firstError ?? "Email khong hop le.",
            ], 422);
        } catch (\Throwable $e) {
            Log::error("Loi xu ly newsletter: " . $e->getMessage());
            return response()->json([
                "success" => false,
                "message" => "Co loi xay ra, vui long thu lai sau.",
            ], 500);
        }
    }

    public function confirmNewsletter(Request $request, $token)
    {
        $newsletter = Newsletter::where("token", $token)->first();

        if (!$newsletter) {
            return redirect("/")->with([
                "flash_level"   => "danger",
                "flash_message" => "Link xac nhan khong hop le hoac da het han.",
            ]);
        }

        if ($newsletter->is_active && !$newsletter->unsubscribed_at) {
            return redirect("/")->with([
                "flash_level"   => "info",
                "flash_message" => "Email nay da duoc xac nhan truoc do.",
            ]);
        }

        $newsletter->markAsSubscribed();

        return redirect("/")->with([
            "flash_level"   => "success",
            "flash_message" => "Xac nhan dang ky thanh cong! Ban se nhan duoc tin khuyen mai tu chung toi.",
        ]);
    }

    public function unsubscribeNewsletter(Request $request)
    {
        $email = $request->get("email");

        if (!$email) {
            return redirect("/")->with([
                "flash_level"   => "danger",
                "flash_message" => "Email khong hop le.",
            ]);
        }

        $newsletter = Newsletter::where("Email", $email)->first();

        if (!$newsletter) {
            return redirect("/")->with([
                "flash_level"   => "danger",
                "flash_message" => "Email khong ton tai trong danh sach dang ky.",
            ]);
        }

        $newsletter->markAsUnsubscribed();

        return redirect("/")->with([
            "flash_level"   => "success",
            "flash_message" => "Ban da huy dang ky nhan tin khuyen mai thanh cong.",
        ]);
    }

    // ============================================================
    // HELPERS
    // ============================================================
    private function sendNewsletterConfirmEmail(Newsletter $newsletter)
    {
        $confirmUrl = url("/newsletter/confirm/" . $newsletter->token);

        try {
            Mail::to($newsletter->Email)->send(new NewsletterConfirmMail($newsletter->Email, $confirmUrl));
        } catch (\Throwable $e) {
            Log::error("Loi gui email xac nhan newsletter: " . $e->getMessage());
        }
    }

    private function sendContactAdminNotification(array $contactData): void
    {
        try {
            $adminEmail = System::where("Status", 1)->where("Code", "email")->first();
            if (!$adminEmail || !$adminEmail->Description) {
                return;
            }

            $emailData = $contactData;
            $priority = $contactData["priority"] ?? Contact::PRIORITY_MEDIUM;
            $emailData["category_label"] = Contact::categoryLabels()[$contactData["category"]] ?? $contactData["category"];
            $emailData["category_color"] = Contact::categoryColors()[$contactData["category"]] ?? "secondary";
            $emailData["priority_label"] = Contact::priorityLabels()[$priority] ?? "Trung binh";
            $emailData["priority_color"] = Contact::priorityColors()[$priority] ?? "secondary";

            Mail::to($adminEmail->Description)->send(new ContactReceivedMail($emailData));
        } catch (\Throwable $e) {
            Log::error("Loi gui email thong bao lien he cho admin: " . $e->getMessage());
        }
    }

    private function sendContactAutoReply(array $contactData): void
    {
        try {
            Mail::to($contactData["Email"])->send(new ContactAutoReplyMail($contactData));
        } catch (\Throwable $e) {
            Log::error("Loi gui email auto-reply lien he: " . $e->getMessage());
        }
    }

    // ============================================================
    // ADS HELPERS
    // ============================================================

    /**
     * Lấy popup ngẫu nhiên cho trang hiện tại
     */
    public function getPopupAd(string $location = 'all'): ?Ad
    {
        return Ad::getRandomPopup($location);
    }

    /**
     * Lấy danh sách banner cho trang hiện tại
     */
    public function getBannerAds(string $location = 'all', int $limit = 3): \Illuminate\Database\Eloquent\Collection
    {
        return Ad::getFor(Ad::TYPE_BANNER, $location, $limit);
    }

    /**
     * Lấy quảng cáo sidebar cho trang hiện tại
     */
    public function getSidebarAds(string $location = 'all', int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Ad::getFor(Ad::TYPE_SIDEBAR, $location, $limit);
    }

    /**
     * Lấy quảng cáo trong bài viết
     */
    public function getInArticleAds(string $location = 'all', int $limit = 2): \Illuminate\Database\Eloquent\Collection
    {
        return Ad::getFor(Ad::TYPE_IN_ARTICLE, $location, $limit);
    }

    /**
     * Xác định location dựa trên route hiện tại
     */
    public function getCurrentAdLocation(): string
    {
        $currentPath = request()->path();
        
        // Kiểm tra nếu là trang chi tiết bài viết
        if (preg_match('/\.html$/', $currentPath)) {
            return Ad::LOC_ARTICLE;
        }
        
        // Kiểm tra nếu là trang chủ
        if ($currentPath === '/' || $currentPath === '') {
            return Ad::LOC_HOME;
        }
        
        return Ad::LOC_ALL;
    }

    /**
     * Track ad view after the popup is actually shown in the browser.
     */
    public function trackAdView(int $id)
    {
        $ad = Ad::popup()->active()->withinDateRange()->find($id);

        if ($ad) {
            $ad->incrementView();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Track ad click
     */
    public function trackAdClick(int $id)
    {
        $ad = Ad::popup()->find($id);
        
        if ($ad) {
            $ad->incrementClick();
        }
        
        return response()->json(['success' => true]);
    }
}
