<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VnExpressInspiredContentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $adminId = (int) (DB::table('users')->where('username', 'admin')->value('id') ?? 1);
        $authors = DB::table('users')
            ->where('level', 2)
            ->orderBy('id')
            ->pluck('id')
            ->values()
            ->all();

        if (empty($authors)) {
            $authors = [$adminId];
        }

        $members = DB::table('users')
            ->where('level', 5)
            ->orderBy('id')
            ->pluck('id')
            ->values()
            ->all();

        if (empty($members)) {
            $members = DB::table('users')
                ->where('is_admin_account', 0)
                ->orderBy('id')
                ->pluck('id')
                ->values()
                ->all();
        }

        $categoryColors = [
            'thoi-su' => '#d1a53d',
            'the-gioi' => '#60a5fa',
            'kinh-doanh' => '#34d399',
            'phap-luat' => '#f87171',
            'khoa-hoc-cong-nghe' => '#a78bfa',
            'the-thao' => '#fb923c',
            'giai-tri' => '#e879f9',
            'suc-khoe' => '#22c55e',
            'du-lich' => '#14b8a6',
            'xe' => '#38bdf8',
            'bat-dong-san' => '#f59e0b',
            'giao-duc' => '#818cf8',
        ];

        $articles = $this->articles();
        $createdIds = [];
        $day = $now->format('Ymd');
        $imageDir = public_path("images/news/{$day}");

        if (!is_dir($imageDir)) {
            mkdir($imageDir, 0755, true);
        }

        DB::transaction(function () use ($articles, $authors, $members, $adminId, $now, $day, $imageDir, $categoryColors, &$createdIds) {
            $rowId = (int) (DB::table('news')->max('RowID') ?? 0);

            foreach ($articles as $index => $article) {
                $alias = Str::slug($article['title']);
                $existing = DB::table('news')->where('Alias', $alias)->first();
                $imageName = sprintf('%s/vne-inspired-%02d.svg', $day, $index + 1);

                if ($existing) {
                    $this->writeCoverSvg($imageDir . DIRECTORY_SEPARATOR . basename($imageName), $article['category_label'], $article['title'], $index);
                    $createdIds[] = (int) $existing->RowID;
                    continue;
                }

                $categoryId = $this->categoryId($article['category'], $categoryColors[$article['category']] ?? '#6c757d', $now);
                $this->writeCoverSvg($imageDir . DIRECTORY_SEPARATOR . basename($imageName), $article['category_label'], $article['title'], $index);

                $status = $article['workflow'] === 'published' ? 1 : 0;
                $rowId++;

                DB::table('news')->insert([
                    'RowID' => $rowId,
                    'RowIDCat' => $categoryId,
                    'cat_id' => $categoryId,
                    'Name' => $article['title'],
                    'Title' => $article['title'],
                    'Alias' => $alias,
                    'Images' => $imageName,
                    'Image' => $imageName,
                    'Status' => $status,
                    'publish' => $status,
                    'hot' => $index < 4 ? 1 : 0,
                    'tags' => implode(',', $article['tags']),
                    'MetaTitle' => $article['title'],
                    'MetaDescription' => $article['summary'],
                    'MetaKeyword' => implode(', ', $article['tags']),
                    'SmallDescription' => $article['summary'],
                    'Description' => $article['content'],
                    'Content' => $article['content'],
                    'Views' => 200,
                    'View' => 200,
                    'author_id' => $authors[$index % count($authors)],
                    'Author' => 'SCF Newsroom',
                    'Date' => $now->copy()->subDays($index % 5)->toDateString(),
                    'created_at' => $now->copy()->subHours($index + 1),
                    'updated_at' => $now,
                ]);

                $this->syncTags($rowId, $article['tags'], $now);
                $this->schedule($rowId, $article['workflow'], $adminId, $now);
                $this->viewStats($rowId, $now);

                $createdIds[] = $rowId;
            }

            if (!empty($members) && !empty($createdIds)) {
                $this->seedInteractions($createdIds, $members, $now);
            }
        });

        $this->command?->info('Seeded or verified ' . count($createdIds) . ' VnExpress-inspired articles.');
    }

    private function categoryId(string $alias, string $color, Carbon $now): int
    {
        $nameMap = [
            'thoi-su' => 'Thời sự',
            'the-gioi' => 'Thế giới',
            'kinh-doanh' => 'Kinh doanh',
            'phap-luat' => 'Pháp luật',
            'khoa-hoc-cong-nghe' => 'Khoa học công nghệ',
            'the-thao' => 'Thể thao',
            'giai-tri' => 'Giải trí',
            'suc-khoe' => 'Sức khỏe',
            'du-lich' => 'Du lịch',
            'xe' => 'Xe',
            'bat-dong-san' => 'Bất động sản',
            'giao-duc' => 'Giáo dục',
        ];

        $existing = DB::table('news_cat')->where('Alias', $alias)->first();
        if ($existing) {
            return (int) $existing->RowID;
        }

        return (int) DB::table('news_cat')->insertGetId([
            'Name' => $nameMap[$alias] ?? Str::title(str_replace('-', ' ', $alias)),
            'Alias' => $alias,
            'Status' => 1,
            'color' => $color,
            'description' => 'Danh mục được bổ sung cho nhóm bài viết biên tập mới.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function syncTags(int $newsId, array $tags, Carbon $now): void
    {
        DB::table('news_tags')->where('news_id', $newsId)->delete();

        foreach ($tags as $tagName) {
            $slug = Str::slug($tagName);
            $tagId = DB::table('tags')->where('slug', $slug)->value('id');

            if (!$tagId) {
                $tagId = DB::table('tags')->insertGetId([
                    'name' => $tagName,
                    'slug' => $slug,
                    'meta_title' => $tagName,
                    'meta_description' => 'Tag nội dung: ' . $tagName,
                    'popular_count' => 0,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            DB::table('news_tags')->insert([
                'news_id' => $newsId,
                'tag_id' => $tagId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function schedule(int $newsId, string $workflow, int $adminId, Carbon $now): void
    {
        $status = match ($workflow) {
            'published' => 'published',
            'pending' => 'pending',
            default => 'draft',
        };

        DB::table('news_schedules')->updateOrInsert(
            ['news_id' => $newsId],
            [
                'created_by' => $adminId,
                'approved_by' => $status === 'published' ? $adminId : null,
                'status' => $status,
                'publish_type' => 'now',
                'scheduled_at' => null,
                'published_at' => $status === 'published' ? $now : null,
                'reject_reason' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

    private function viewStats(int $newsId, Carbon $now): void
    {
        DB::table('news_view_stats')->updateOrInsert(
            ['news_id' => $newsId, 'view_date' => $now->toDateString()],
            [
                'total_views' => 200,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

    private function seedInteractions(array $newsIds, array $members, Carbon $now): void
    {
        $comments = [
            'Bài viết có góc nhìn rõ ràng, phần bối cảnh được trình bày dễ theo dõi.',
            'Nội dung hữu ích, tôi muốn xem thêm dữ liệu cập nhật ở các bản tin sau.',
            'Cách phân tích khá cân bằng, phù hợp để nắm nhanh tình hình.',
            'Phần tóm tắt tốt, ảnh minh họa cũng giúp bài viết dễ đọc hơn.',
            'Thông tin được sắp xếp mạch lạc, có thể bổ sung thêm biểu đồ nếu cần.',
            'Bài này đáng theo dõi vì chủ đề đang được nhiều người quan tâm.',
            'Tôi đánh giá cao phần giải thích tác động tới độc giả trong nước.',
            'Nội dung vừa đủ chi tiết, không bị lan man.',
            'Chủ đề này nên được cập nhật tiếp khi có diễn biến mới.',
            'Bài viết giúp tôi hiểu nhanh vấn đề chính trong vài phút.',
        ];

        $publishedIds = array_slice($newsIds, 0, min(12, count($newsIds)));

        foreach ($comments as $index => $content) {
            $newsId = $publishedIds[$index % count($publishedIds)];
            $userId = $members[$index % count($members)];

            $exists = DB::table('news_comments')
                ->where('news_id', $newsId)
                ->where('user_id', $userId)
                ->where('content', $content)
                ->exists();

            if (!$exists) {
                DB::table('news_comments')->insert([
                    'news_id' => $newsId,
                    'user_id' => $userId,
                    'parent_id' => null,
                    'content' => $content,
                    'is_active' => 1,
                    'upvote_count' => ($index % 4) + 1,
                    'downvote_count' => 0,
                    'reply_count' => 0,
                    'created_at' => $now->copy()->subMinutes(120 - ($index * 7)),
                    'updated_at' => $now,
                ]);
            }
        }

        foreach ($publishedIds as $index => $newsId) {
            for ($offset = 0; $offset < min(3, count($members)); $offset++) {
                $userId = $members[($index + $offset) % count($members)];
                $score = [5, 4, 5, 3, 4][($index + $offset) % 5];

                DB::table('news_ratings')->updateOrInsert(
                    ['news_id' => $newsId, 'user_id' => $userId],
                    [
                        'score' => $score,
                        'created_at' => $now->copy()->subMinutes(90 - ($index * 3) - $offset),
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }

    private function writeCoverSvg(string $path, string $category, string $title, int $index): void
    {
        $palettes = [
            ['#0f172a', '#d1a53d', '#60a5fa'],
            ['#111827', '#34d399', '#f0c060'],
            ['#15111f', '#a78bfa', '#fb923c'],
            ['#121826', '#38bdf8', '#f87171'],
        ];

        [$bg, $primary, $secondary] = $palettes[$index % count($palettes)];
        $safeCategory = htmlspecialchars($category, ENT_QUOTES, 'UTF-8');
        $titleLines = $this->titleLines($title);
        $titleSvg = '';
        foreach ($titleLines as $lineIndex => $line) {
            $safeLine = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');
            $dy = $lineIndex === 0 ? 0 : 64;
            $titleSvg .= '<tspan x="96" dy="' . $dy . '">' . $safeLine . '</tspan>';
        }

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="675" viewBox="0 0 1200 675">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="{$bg}"/>
      <stop offset="1" stop-color="#0d0f14"/>
    </linearGradient>
    <radialGradient id="r" cx="75%" cy="20%" r="65%">
      <stop offset="0" stop-color="{$primary}" stop-opacity=".48"/>
      <stop offset="1" stop-color="{$primary}" stop-opacity="0"/>
    </radialGradient>
  </defs>
  <rect width="1200" height="675" fill="url(#g)"/>
  <rect width="1200" height="675" fill="url(#r)"/>
  <circle cx="1030" cy="145" r="165" fill="{$secondary}" opacity=".18"/>
  <circle cx="160" cy="560" r="240" fill="{$primary}" opacity=".12"/>
  <path d="M0 520 C260 460 390 610 610 545 C800 488 930 405 1200 455 L1200 675 L0 675 Z" fill="{$secondary}" opacity=".16"/>
  <rect x="72" y="72" width="1056" height="531" rx="32" fill="none" stroke="rgba(255,255,255,.14)" stroke-width="2"/>
  <text x="96" y="130" fill="{$primary}" font-family="Arial, sans-serif" font-size="26" font-weight="700" letter-spacing="2">{$safeCategory}</text>
  <text x="96" y="274" fill="#f8fafc" font-family="Arial, sans-serif" font-size="52" font-weight="800">{$titleSvg}</text>
  <text x="96" y="510" fill="#cbd5e1" font-family="Arial, sans-serif" font-size="24">SCF Newsroom · Ảnh minh họa biên tập</text>
  <rect x="96" y="548" width="220" height="8" rx="4" fill="{$primary}"/>
</svg>
SVG;

        file_put_contents($path, $svg);
    }

    private function titleLines(string $title): array
    {
        $words = preg_split('/\s+/', trim($title)) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = trim($current . ' ' . $word);
            if (mb_strlen($candidate) > 34 && $current !== '') {
                $lines[] = $current;
                $current = $word;
                continue;
            }
            $current = $candidate;
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return array_slice($lines, 0, 3);
    }

    private function articles(): array
    {
        return [
            [
                'title' => 'Cao tốc miền Trung - Tây Nguyên chuẩn bị nối mạch khai thác',
                'category' => 'thoi-su',
                'category_label' => 'Thời sự',
                'workflow' => 'published',
                'summary' => 'Các tuyến cao tốc mới được kỳ vọng rút ngắn thời gian di chuyển, mở thêm trục kết nối kinh tế giữa duyên hải miền Trung và Tây Nguyên.',
                'tags' => ['cao tốc', 'hạ tầng', 'miền Trung'],
                'content' => $this->content('Các đoạn cao tốc mới tại miền Trung và Tây Nguyên đang được chuẩn bị đưa vào khai thác, tạo thêm một hành lang giao thông quan trọng cho khu vực.', 'Tuyến đường giúp giảm áp lực cho quốc lộ hiện hữu, đồng thời mở ra cơ hội vận chuyển hàng hóa nhanh hơn giữa cảng biển, vùng sản xuất và các đô thị trung tâm.', 'Về dài hạn, chuyên gia giao thông cho rằng hiệu quả lớn nhất nằm ở khả năng kết nối liên vùng, khi doanh nghiệp có thể tối ưu chi phí logistics và địa phương có thêm điều kiện thu hút đầu tư.', 'Bài viết được biên tập độc lập, tham khảo chủ đề thời sự công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Đề xuất hòa bình mới làm nóng hồ sơ Iran - Mỹ',
                'category' => 'the-gioi',
                'category_label' => 'Thế giới',
                'workflow' => 'published',
                'summary' => 'Một đề xuất ngoại giao mới khiến hồ sơ Iran - Mỹ được chú ý trở lại, trong bối cảnh các bên đều cần giảm rủi ro leo thang.',
                'tags' => ['Iran', 'Mỹ', 'ngoại giao'],
                'content' => $this->content('Diễn biến ngoại giao quanh quan hệ Iran - Mỹ tiếp tục thu hút sự quan tâm khi các tín hiệu đối thoại xuất hiện song song với những tuyên bố cứng rắn.', 'Các nhà quan sát cho rằng bất kỳ sáng kiến nào cũng phải giải quyết được ba điểm nghẽn: cam kết an ninh, cơ chế giám sát và lợi ích kinh tế cụ thể.', 'Dù triển vọng chưa rõ ràng, việc các bên duy trì kênh liên lạc vẫn được xem là yếu tố cần thiết để hạn chế tính toán sai lầm trong khu vực.', 'Bài viết được biên tập độc lập, tham khảo chủ đề thời sự công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'HLV trẻ Brazil và hành trình mới cùng bóng đá U17 Việt Nam',
                'category' => 'the-thao',
                'category_label' => 'Thể thao',
                'workflow' => 'published',
                'summary' => 'Câu chuyện một HLV ngoại dẫn dắt đội trẻ Việt Nam cho thấy bóng đá đào tạo trẻ đang cần thêm phương pháp và sự kiên nhẫn.',
                'tags' => ['U17 Việt Nam', 'bóng đá trẻ', 'huấn luyện'],
                'content' => $this->content('Việc một HLV trẻ từ Brazil tham gia công tác huấn luyện đội U17 Việt Nam đem lại góc nhìn mới về cách xây dựng nền tảng kỹ thuật cho cầu thủ trẻ.', 'Trọng tâm không chỉ là kết quả trước mắt, mà còn là cách cầu thủ hiểu chiến thuật, ra quyết định trong tốc độ cao và duy trì kỷ luật thi đấu.', 'Nếu được triển khai nhất quán, mô hình huấn luyện đa văn hóa có thể giúp cầu thủ trẻ Việt Nam tiếp cận tiêu chuẩn thi đấu hiện đại sớm hơn.', 'Bài viết được biên tập độc lập, tham khảo chủ đề thể thao công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Sự cố máy bay hạng nhẹ tại Mỹ đặt lại câu hỏi về an toàn hàng không cá nhân',
                'category' => 'the-gioi',
                'category_label' => 'Thế giới',
                'workflow' => 'published',
                'summary' => 'Các vụ tai nạn máy bay nhỏ cho thấy quản lý bay cá nhân cần đồng thời chú ý kỹ thuật, thời tiết và năng lực phi công.',
                'tags' => ['hàng không', 'Mỹ', 'an toàn bay'],
                'content' => $this->content('Một sự cố liên quan máy bay hạng nhẹ tại Mỹ tiếp tục đặt ra câu hỏi về mức độ an toàn của các chuyến bay cá nhân và hoạt động hàng không nhỏ.', 'Khác với hàng không thương mại, nhóm phương tiện này phụ thuộc nhiều vào điều kiện thời tiết địa phương, bảo dưỡng định kỳ và kinh nghiệm xử lý tình huống của phi công.', 'Các chuyên gia khuyến nghị tăng minh bạch dữ liệu tai nạn, chuẩn hóa quy trình kiểm tra và nâng yêu cầu đào tạo đối với người điều khiển.', 'Bài viết được biên tập độc lập, tham khảo chủ đề thời sự công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Bộ ảnh mùa hè Đà Nẵng tạo hiệu ứng du lịch trên mạng xã hội',
                'category' => 'du-lich',
                'category_label' => 'Du lịch',
                'workflow' => 'published',
                'summary' => 'Hình ảnh nghệ sĩ quốc tế tại Đà Nẵng và Hội An cho thấy truyền thông thị giác vẫn là động lực mạnh của ngành du lịch.',
                'tags' => ['Đà Nẵng', 'Hội An', 'du lịch hè'],
                'content' => $this->content('Những bộ ảnh mùa hè chụp tại Đà Nẵng và Hội An thường tạo hiệu ứng lan tỏa lớn nhờ kết hợp cảnh quan biển, phố cổ và phong cách trẻ trung.', 'Với ngành du lịch, các nội dung hình ảnh có sức gợi mạnh hơn quảng cáo truyền thống vì người xem dễ hình dung trải nghiệm thực tế tại điểm đến.', 'Để chuyển hiệu ứng truyền thông thành lượng khách, địa phương cần đồng bộ dịch vụ, giao thông và sản phẩm trải nghiệm vào mùa cao điểm.', 'Bài viết được biên tập độc lập, tham khảo chủ đề giải trí - du lịch công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Startup xe điện và bài học từ những lần thử sai',
                'category' => 'kinh-doanh',
                'category_label' => 'Kinh doanh',
                'workflow' => 'published',
                'summary' => 'Câu chuyện khởi nghiệp trong ngành xe điện cho thấy đổi mới sản phẩm cần vốn, thời gian và khả năng chịu áp lực rất lớn.',
                'tags' => ['startup', 'xe điện', 'đổi mới'],
                'content' => $this->content('Khởi nghiệp trong ngành xe điện không chỉ là câu chuyện sản phẩm, mà còn là thử thách về chuỗi cung ứng, vốn, dịch vụ hậu mãi và niềm tin người dùng.', 'Những lần thử sai có thể khiến doanh nghiệp tốn kém, nhưng cũng tạo dữ liệu thực tế để cải tiến thiết kế, tối ưu chi phí và hiểu rõ hành vi khách hàng.', 'Trong bối cảnh cạnh tranh mạnh, lợi thế của startup nằm ở tốc độ học hỏi và khả năng chọn một phân khúc đủ rõ để phục vụ tốt hơn.', 'Bài viết được biên tập độc lập, tham khảo chủ đề kinh doanh công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Falcon Heavy tái xuất và cuộc đua vệ tinh băng thông rộng',
                'category' => 'khoa-hoc-cong-nghe',
                'category_label' => 'Khoa học công nghệ',
                'workflow' => 'published',
                'summary' => 'Các lần phóng tên lửa hạng nặng cho thấy thị trường vệ tinh Internet tiếp tục bước vào giai đoạn cạnh tranh về năng lực phủ sóng.',
                'tags' => ['SpaceX', 'vệ tinh', 'Internet'],
                'content' => $this->content('Việc tên lửa hạng nặng quay lại bệ phóng nhấn mạnh vai trò ngày càng lớn của hạ tầng không gian trong thị trường Internet băng thông rộng.', 'Các vệ tinh thế hệ mới có khối lượng lớn hơn, yêu cầu quỹ đạo phức tạp hơn và cần năng lực phóng ổn định để bảo đảm kế hoạch thương mại.', 'Cuộc đua không chỉ nằm ở công nghệ phóng, mà còn ở giá dịch vụ, độ trễ, vùng phủ và khả năng kết nối tại khu vực xa hạ tầng mặt đất.', 'Bài viết được biên tập độc lập, tham khảo chủ đề khoa học công nghệ công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Nắng nóng cực đoan và cách cơ thể tự bảo vệ',
                'category' => 'suc-khoe',
                'category_label' => 'Sức khỏe',
                'workflow' => 'published',
                'summary' => 'Khi nhiệt độ tăng cao, cơ thể phải huy động nhiều cơ chế làm mát; nếu vượt ngưỡng chịu đựng, nguy cơ sốc nhiệt có thể xảy ra nhanh.',
                'tags' => ['nắng nóng', 'sức khỏe', 'sốc nhiệt'],
                'content' => $this->content('Trong thời tiết nắng nóng, cơ thể liên tục điều chỉnh bằng cách tăng tiết mồ hôi, giãn mạch ngoại vi và thay đổi nhịp tim để tản nhiệt.', 'Nguy cơ xuất hiện khi người dân làm việc ngoài trời quá lâu, uống ít nước hoặc ở trong môi trường bí khí khiến quá trình làm mát bị quá tải.', 'Các bác sĩ khuyến cáo hạn chế hoạt động nặng vào giữa trưa, bổ sung nước đúng cách và theo dõi dấu hiệu chóng mặt, mệt lả, lú lẫn để xử trí sớm.', 'Bài viết được biên tập độc lập, tham khảo chủ đề sức khỏe công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Tranh cãi quanh phản ứng của lực lượng bảo vệ trong sự kiện chính trị Mỹ',
                'category' => 'the-gioi',
                'category_label' => 'Thế giới',
                'workflow' => 'published',
                'summary' => 'Một sự cố an ninh tại Mỹ làm dấy lên tranh luận về tốc độ sơ tán, quy trình bảo vệ và cách truyền thông xử lý khủng hoảng.',
                'tags' => ['Mỹ', 'an ninh', 'chính trị'],
                'content' => $this->content('Các sự kiện chính trị lớn luôn đặt lực lượng bảo vệ trước áp lực phải ra quyết định chỉ trong vài giây, đặc biệt khi thông tin tại hiện trường còn chưa đầy đủ.', 'Sau mỗi sự cố, câu hỏi thường xoay quanh việc sơ tán có đủ nhanh hay không, ranh giới giữa bảo vệ nhân vật và kiểm soát đám đông được thiết lập ra sao.', 'Bài học quan trọng là quy trình an ninh cần được diễn tập thường xuyên, đồng thời truyền thông phải tránh suy đoán khi chưa có dữ kiện chính thức.', 'Bài viết được biên tập độc lập, tham khảo chủ đề quốc tế công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Bayern và PSG trước đại chiến: khác biệt có thể chỉ là khoảnh khắc',
                'category' => 'the-thao',
                'category_label' => 'Thể thao',
                'workflow' => 'published',
                'summary' => 'Những trận cầu đỉnh cao tại Champions League thường được quyết định bởi chi tiết nhỏ, từ tốc độ chuyển trạng thái đến khả năng tận dụng cơ hội.',
                'tags' => ['Champions League', 'Bayern', 'PSG'],
                'content' => $this->content('Khi hai đội bóng lớn gặp nhau, chất lượng cá nhân chỉ là một phần; yếu tố then chốt nằm ở cách họ kiểm soát nhịp độ và phản ứng sau khi mất bóng.', 'Bayern có truyền thống pressing mạnh và khai thác biên tốt, trong khi PSG sở hữu nhiều cầu thủ có thể tạo đột biến ở không gian hẹp.', 'Ở cấp độ này, một pha xử lý chậm nửa nhịp hoặc một quyết định chuyền bóng sai có thể thay đổi toàn bộ cục diện trận đấu.', 'Bài viết được biên tập độc lập, tham khảo chủ đề thể thao công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Di sản Phật giáo trên vách đá và hành trình hồi hương cổ vật',
                'category' => 'giai-tri',
                'category_label' => 'Giải trí',
                'workflow' => 'published',
                'summary' => 'Câu chuyện hồi hương cổ vật cho thấy bảo tồn di sản không chỉ là phục dựng vật chất mà còn là khôi phục ký ức văn hóa.',
                'tags' => ['di sản', 'văn hóa', 'cổ vật'],
                'content' => $this->content('Những di tích Phật giáo tạc trên vách đá là bằng chứng sống động về kỹ thuật, tín ngưỡng và đời sống thẩm mỹ của nhiều thế hệ.', 'Khi các chi tiết thất lạc được đưa trở về, giá trị không chỉ nằm ở hiện vật mà còn ở quá trình xác minh nguồn gốc, bảo quản và kể lại câu chuyện lịch sử.', 'Các chuyên gia văn hóa cho rằng hồi hương cổ vật cần đi kèm giáo dục công chúng để di sản không chỉ được trưng bày mà còn được hiểu đúng.', 'Bài viết được biên tập độc lập, tham khảo chủ đề văn hóa công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Cá lớn ở vùng điện gió và cơ hội phát triển du lịch trải nghiệm',
                'category' => 'du-lich',
                'category_label' => 'Du lịch',
                'workflow' => 'published',
                'summary' => 'Những điểm câu cá gần công trình điện gió có thể trở thành sản phẩm du lịch địa phương nếu được quản lý an toàn và bền vững.',
                'tags' => ['Cà Mau', 'điện gió', 'du lịch trải nghiệm'],
                'content' => $this->content('Các khu vực gần công trình điện gió ven biển thường tạo nên cảnh quan đặc biệt, thu hút người dân và du khách tới tham quan, chụp ảnh, câu cá.', 'Nếu được tổ chức bài bản, hoạt động trải nghiệm có thể tạo thêm thu nhập cho cộng đồng địa phương mà vẫn bảo đảm an toàn giao thông thủy và bảo vệ môi trường.', 'Điều kiện cần là phân luồng khu vực, hướng dẫn du khách và kiểm soát rác thải để điểm đến không bị quá tải vào mùa cao điểm.', 'Bài viết được biên tập độc lập, tham khảo chủ đề đời sống - du lịch công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Bác sĩ nội soi đường thở cứu trẻ nhỏ khỏi dị vật nguy hiểm',
                'category' => 'suc-khoe',
                'category_label' => 'Sức khỏe',
                'workflow' => 'draft',
                'summary' => 'Các ca dị vật đường thở ở trẻ nhỏ đòi hỏi chẩn đoán nhanh, thao tác chính xác và sự phối hợp chặt giữa bác sĩ với gia đình.',
                'tags' => ['nhi khoa', 'dị vật', 'cấp cứu'],
                'content' => $this->content('Dị vật đường thở là tình huống cấp cứu nguy hiểm ở trẻ nhỏ vì có thể gây khó thở, xẹp phổi hoặc tổn thương niêm mạc nếu xử trí chậm.', 'Kỹ thuật nội soi giúp bác sĩ quan sát trực tiếp vị trí dị vật, từ đó lựa chọn hướng tiếp cận phù hợp để giảm nguy cơ làm vật kẹt sâu hơn.', 'Gia đình cần tránh tự móc họng hoặc cho trẻ ăn uống thêm khi nghi ngờ hóc dị vật, thay vào đó đưa trẻ tới cơ sở y tế càng sớm càng tốt.', 'Bản nháp biên tập độc lập, tham khảo chủ đề sức khỏe công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Tình huống bạo lực trên sân cỏ và giới hạn của cảm xúc thi đấu',
                'category' => 'the-thao',
                'category_label' => 'Thể thao',
                'workflow' => 'draft',
                'summary' => 'Một pha va chạm nóng nảy nhắc lại câu hỏi về kỷ luật, VAR và trách nhiệm hình ảnh của cầu thủ chuyên nghiệp.',
                'tags' => ['bóng đá', 'VAR', 'kỷ luật'],
                'content' => $this->content('Bóng đá luôn có không gian cho cảm xúc, nhưng hành vi bạo lực vượt quá giới hạn có thể làm tổn hại hình ảnh cầu thủ và đội bóng.', 'Sự xuất hiện của VAR giúp trọng tài có thêm dữ liệu để xử lý các tình huống kín, đồng thời khiến hành vi thiếu kiểm soát khó bị bỏ qua hơn.', 'Các CLB hiện đại ngày càng chú trọng đào tạo tâm lý thi đấu, bởi một khoảnh khắc mất bình tĩnh có thể dẫn tới án phạt dài hạn và thiệt hại chuyên môn.', 'Bản nháp biên tập độc lập, tham khảo chủ đề thể thao công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Sân bay Phan Thiết và kỳ vọng mở thêm cực tăng trưởng du lịch biển',
                'category' => 'du-lich',
                'category_label' => 'Du lịch',
                'workflow' => 'draft',
                'summary' => 'Hạ tầng hàng không mới có thể rút ngắn thời gian tiếp cận Bình Thuận, nhưng hiệu quả còn phụ thuộc vào sản phẩm du lịch và năng lực phục vụ.',
                'tags' => ['Phan Thiết', 'sân bay', 'du lịch biển'],
                'content' => $this->content('Một dự án sân bay dân dụng tại khu vực du lịch biển có thể thay đổi đáng kể cách du khách tiếp cận điểm đến, nhất là nhóm khách từ xa.', 'Tuy vậy, sân bay chỉ là điều kiện ban đầu; địa phương cần chuẩn bị hệ sinh thái dịch vụ từ lưu trú, vận chuyển, ẩm thực tới trải nghiệm văn hóa.', 'Nếu kết nối tốt với các tuyến đường bộ và khu nghỉ dưỡng, hạ tầng mới có thể giúp Bình Thuận kéo dài mùa du lịch thay vì phụ thuộc vào vài cao điểm ngắn.', 'Bản nháp biên tập độc lập, tham khảo chủ đề hạ tầng công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Giá dầu biến động và áp lực chi phí đối với doanh nghiệp',
                'category' => 'kinh-doanh',
                'category_label' => 'Kinh doanh',
                'workflow' => 'draft',
                'summary' => 'Các cú sốc giá dầu có thể lan sang vận tải, sản xuất và tiêu dùng, khiến doanh nghiệp phải điều chỉnh kế hoạch chi phí.',
                'tags' => ['giá dầu', 'doanh nghiệp', 'chi phí'],
                'content' => $this->content('Giá dầu tăng thường tạo hiệu ứng dây chuyền vì nhiên liệu là đầu vào quan trọng của vận tải, sản xuất, logistics và nhiều dịch vụ thiết yếu.', 'Doanh nghiệp có biên lợi nhuận mỏng sẽ chịu áp lực rõ hơn, đặc biệt khi không thể ngay lập tức chuyển toàn bộ chi phí tăng thêm sang giá bán.', 'Các chuyên gia tài chính khuyến nghị doanh nghiệp xây dựng kịch bản giá năng lượng, đa dạng nhà cung cấp và tối ưu tồn kho để giảm rủi ro.', 'Bản nháp biên tập độc lập, tham khảo chủ đề kinh doanh công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Chiêu lừa phạt nguội giá rẻ và rủi ro mất thông tin ngân hàng',
                'category' => 'phap-luat',
                'category_label' => 'Pháp luật',
                'workflow' => 'pending',
                'summary' => 'Các tin nhắn giả mạo phạt nguội thường đánh vào tâm lý sợ rắc rối để dụ người dùng nhập thông tin thẻ và mã xác thực.',
                'tags' => ['lừa đảo', 'phạt nguội', 'an toàn thông tin'],
                'content' => $this->content('Hình thức giả mạo thông báo phạt nguội với số tiền nhỏ đang được nhiều nhóm lừa đảo sử dụng vì dễ khiến người nhận mất cảnh giác.', 'Khi bấm vào đường dẫn giả, nạn nhân thường được yêu cầu nhập thông tin thẻ, số điện thoại hoặc mã OTP, từ đó kẻ gian có thể chiếm đoạt tiền.', 'Người dân nên tra cứu vi phạm qua kênh chính thức, không nhập dữ liệu ngân hàng từ liên kết lạ và báo ngay cho ngân hàng khi nghi ngờ lộ thông tin.', 'Bài gửi duyệt được biên tập độc lập, tham khảo chủ đề pháp luật công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Container bốc cháy gần cảng và bài toán an toàn vận tải đô thị',
                'category' => 'xe',
                'category_label' => 'Xe',
                'workflow' => 'pending',
                'summary' => 'Sự cố cháy xe tải nặng tại khu vực cửa ngõ cảng cho thấy cần kiểm soát kỹ phương tiện, hàng hóa và hạ tầng thoát hiểm.',
                'tags' => ['container', 'cháy xe', 'giao thông'],
                'content' => $this->content('Các tuyến đường dẫn vào cảng thường có mật độ xe tải nặng cao, vì vậy một sự cố cháy nổ có thể nhanh chóng gây ùn tắc và ảnh hưởng an toàn khu vực.', 'Nguyên nhân có thể đến từ kỹ thuật phương tiện, va chạm, hàng hóa hoặc điều kiện vận hành liên tục trong thời gian dài.', 'Để giảm rủi ro, doanh nghiệp vận tải cần tăng kiểm tra trước chuyến, còn đô thị cần bố trí điểm dừng khẩn cấp và lực lượng phản ứng nhanh tại các trục trọng yếu.', 'Bài gửi duyệt được biên tập độc lập, tham khảo chủ đề giao thông công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'iPhone kỷ niệm 20 năm và cuộc đua thiết kế không viền',
                'category' => 'khoa-hoc-cong-nghe',
                'category_label' => 'Khoa học công nghệ',
                'workflow' => 'pending',
                'summary' => 'Tin đồn về mẫu iPhone tương lai cho thấy thiết kế smartphone cao cấp vẫn tập trung vào màn hình, vật liệu và trải nghiệm liền mạch.',
                'tags' => ['iPhone', 'smartphone', 'màn hình'],
                'content' => $this->content('Các thế hệ smartphone cao cấp ngày càng khó tạo khác biệt lớn, vì vậy thiết kế màn hình gần như không viền tiếp tục là hướng được nhiều hãng theo đuổi.', 'Nếu công nghệ uốn cong, cảm biến dưới màn hình và khung máy mới được hoàn thiện, người dùng có thể có trải nghiệm hiển thị liền mạch hơn.', 'Tuy nhiên, thách thức vẫn nằm ở độ bền, chi phí sửa chữa và khả năng sản xuất hàng loạt với tỷ lệ lỗi thấp.', 'Bài gửi duyệt được biên tập độc lập, tham khảo chủ đề công nghệ công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
            [
                'title' => 'Bất động sản giải trí nhìn từ kết quả kinh doanh của công viên đô thị',
                'category' => 'bat-dong-san',
                'category_label' => 'Bất động sản',
                'workflow' => 'pending',
                'summary' => 'Hoạt động của các công viên giải trí đô thị phản ánh sức mua tiêu dùng, chi phí vận hành và áp lực làm mới sản phẩm.',
                'tags' => ['bất động sản', 'công viên', 'dịch vụ'],
                'content' => $this->content('Các đơn vị vận hành công viên giải trí trong đô thị chịu tác động trực tiếp từ sức mua của hộ gia đình, thời tiết và cạnh tranh của nhiều loại hình giải trí mới.', 'Khi chi phí nhân sự, bảo trì và mặt bằng tăng, doanh nghiệp phải liên tục làm mới trải nghiệm để giữ chân khách thay vì chỉ dựa vào tài sản sẵn có.', 'Nhìn rộng hơn, bất động sản giải trí cần được xem như một mô hình dịch vụ dài hạn, nơi hiệu quả phụ thuộc vào vận hành chứ không chỉ vị trí.', 'Bài gửi duyệt được biên tập độc lập, tham khảo chủ đề kinh doanh công khai từ VNExpress; không sao chép nguyên văn nội dung gốc.'),
            ],
        ];
    }

    private function content(string $p1, string $p2, string $p3, string $note): string
    {
        return '<p>' . $p1 . '</p><p>' . $p2 . '</p><p>' . $p3 . '</p><p><em>' . $note . '</em></p>';
    }
}
