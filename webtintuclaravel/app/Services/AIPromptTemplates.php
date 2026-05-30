<?php

namespace App\Services;

class AIPromptTemplates
{
    public static function metaTagsPrompt(string $title, string $description, string $content): string
    {
        $truncatedContent = mb_substr(strip_tags($content), 0, 2000);
        $truncatedDesc = mb_substr(strip_tags($description), 0, 500);

        return "Bạn là chuyên gia SEO cho website tin tức tiếng Việt.
Dựa vào thông tin bài viết sau, hãy tạo các thẻ Meta tối ưu cho công cụ tìm kiếm.

YÊU CẦU:
- meta_title: tối đa 60 ký tự, hấp dẫn, có từ khóa chính ở đầu.
- meta_description: 150-160 ký tự, rõ ý, tự nhiên, kích thích người đọc bấm vào.
- meta_keywords: 5-8 từ khóa liên quan, phân cách bằng dấu phẩy, viết tiếng Việt có dấu.

THÔNG TIN BÀI VIẾT:
Tiêu đề: {$title}
Mô tả ngắn: {$truncatedDesc}
Nội dung: {$truncatedContent}

Trả lời đúng định dạng JSON, không thêm giải thích:
{
  \"meta_title\": \"...\",
  \"meta_description\": \"...\",
  \"meta_keywords\": \"...\"
}";
    }

    public static function suggestTagsPrompt(string $title, string $content, string $category = ''): string
    {
        $truncatedContent = mb_substr(strip_tags($content), 0, 2000);
        $categoryPart = $category ? "Danh mục: {$category}" : "Danh mục: Tổng hợp";

        return "Bạn là chuyên gia phân loại nội dung cho website tin tức Việt Nam.
Dựa vào bài viết sau, hãy gợi ý 5-8 Tags phù hợp nhất.

QUY TẮC:
- Mỗi Tag dài 1-3 từ tiếng Việt.
- Tags phải liên quan trực tiếp đến nội dung.
- Không lặp ý giữa các Tags.
- Ưu tiên tên người, sự kiện, địa điểm, tổ chức hoặc chủ đề chính nếu có.

THÔNG TIN BÀI VIẾT:
Tiêu đề: {$title}
{$categoryPart}
Nội dung tóm tắt: {$truncatedContent}

Trả lời đúng định dạng JSON array, không thêm giải thích:
[
  {\"name\": \"Tên Tag 1\", \"slug\": \"ten-tag-1\"},
  {\"name\": \"Tên Tag 2\", \"slug\": \"ten-tag-2\"}
]";
    }

    public static function moderateCommentPrompt(string $comment, string $authorName = ''): string
    {
        $author = $authorName ?: 'Người dùng ẩn danh';

        return "Bạn là hệ thống kiểm duyệt bình luận cho website tin tức Việt Nam.
Hãy phân tích bình luận sau và đưa ra quyết định.

PHÂN LOẠI:
1. APPROVE: bình luận hợp lệ, liên quan, lịch sự, văn minh.
2. REJECT: spam, quảng cáo, tục tĩu, thù ghét, phản động, khiêu dâm, lừa đảo hoặc có liên kết đáng ngờ.
3. FLAG: bình luận đáng ngờ, cần quản trị viên xem lại.

Bình luận: \"{$comment}\"
Người viết: {$author}

Trả lời đúng định dạng JSON, không thêm giải thích:
{
  \"action\": \"APPROVE\",
  \"reason\": \"Lý do ngắn gọn bằng tiếng Việt\",
  \"confidence\": 0.0
}";
    }

    public static function chatSystemPrompt(): string
    {
        $siteName = config('app.name', 'VNXpress');

        return "Bạn là trợ lý AI của website tin tức {$siteName}.
PHONG CÁCH:
- Trả lời ngắn gọn, thân thiện, bằng tiếng Việt có dấu.
- Nếu có ngữ cảnh bài viết, ưu tiên bám sát ngữ cảnh thay vì trả lời chung chung.
- Nếu có đoạn văn bản được bôi đen, hãy tập trung phân tích đúng đoạn đó.

NGUYÊN TẮC:
- Không bịa đặt thông tin. Nếu không có đủ dữ liệu, nói rõ là chưa đủ cơ sở kết luận.
- Dựa trên nội dung bài viết đầy đủ, đoạn được chọn và lịch sử chat gần đây nếu được cung cấp.
- Không bỏ qua chi tiết quan trọng trong nội dung bài viết.
- Nếu câu hỏi mơ hồ, hỏi lại một câu ngắn để làm rõ.

GIỚI HẠN:
- Mỗi câu trả lời tối đa 220 từ.
- Không tự nhắc rằng bạn là AI hay mô hình ngôn ngữ.";
    }

    public static function chatPrompt(string $message, array $context = []): string
    {
        $blocks = [];

        if (!empty($context['title']) || !empty($context['category']) || !empty($context['description'])) {
            $articleBlock = "NGỮ CẢNH BÀI VIẾT ĐANG XEM:";

            if (!empty($context['title'])) {
                $articleBlock .= "\n- Tiêu đề: {$context['title']}";
            }
            if (!empty($context['category'])) {
                $articleBlock .= "\n- Danh mục: {$context['category']}";
            }
            if (!empty($context['description'])) {
                $desc = mb_substr(strip_tags((string) $context['description']), 0, 500);
                $articleBlock .= "\n- Mô tả: {$desc}";
            }
            if (!empty($context['tags'])) {
                $tags = is_array($context['tags']) ? implode(', ', $context['tags']) : (string) $context['tags'];
                $articleBlock .= "\n- Tags: {$tags}";
            }

            $blocks[] = $articleBlock;
        }

        if (!empty($context['full_content'])) {
            $blocks[] = "NỘI DUNG BÀI VIẾT ĐẦY ĐỦ:\n" . mb_substr((string) $context['full_content'], 0, 4000);
        }

        if (!empty($context['selected_text'])) {
            $blocks[] = "ĐOẠN VĂN BẢN NGƯỜI DÙNG VỪA BÔI ĐEN:\n" . (string) $context['selected_text'];
        }

        if (!empty($context['selected_excerpt'])) {
            $blocks[] = "NGỮ CẢNH XUNG QUANH ĐOẠN ĐƯỢC CHỌN:\n" . (string) $context['selected_excerpt'];
        }

        if (!empty($context['related_news'])) {
            $blocks[] = "CÁC BÀI VIẾT LIÊN QUAN:\n" . (string) $context['related_news'];
        }

        if (!empty($context['recent_news'])) {
            $blocks[] = "TIN MỚI NHẤT TRÊN WEBSITE:\n" . (string) $context['recent_news'];
        }

        if (!empty($context['chat_history']) && is_array($context['chat_history'])) {
            $historyLines = [];

            foreach ($context['chat_history'] as $item) {
                $role = ($item['role'] ?? '') === 'assistant' ? 'AI' : 'Người dùng';
                $text = trim((string) ($item['text'] ?? ''));

                if ($text !== '') {
                    $historyLines[] = "- {$role}: " . mb_substr($text, 0, 1000);
                }
            }

            if (!empty($historyLines)) {
                $blocks[] = "LỊCH SỬ CHAT GẦN ĐÂY:\n" . implode("\n", $historyLines);
            }
        }

        $blocks[] = "CÂU HỎI HIỆN TẠI CỦA NGƯỜI DÙNG:\n{$message}";
        $blocks[] = "Hãy trả lời dựa trên ngữ cảnh ở trên. Nếu câu hỏi đang nói về đoạn được bôi đen, hãy ưu tiên giải thích đúng đoạn đó và liên hệ với toàn bài viết.";

        return implode("\n\n", $blocks);
    }
}
