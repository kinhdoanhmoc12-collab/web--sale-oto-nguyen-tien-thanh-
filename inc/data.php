<?php
/**
 * Dữ liệu ban đầu (giống bản thiết kế). Chỉ chạy một lần khi database được tạo.
 */

function seed_all(): void
{
    $now = date('Y-m-d H:i:s');
    db()->beginTransaction();

    $cars = [
        ['mitsubishi-xforce', 'Xforce', 'SUV ĐÔ THỊ CỠ B · 5 CHỖ', "GLX | 599000000\nExceed | 640000000\nPremium | 680000000\nUltimate | 705000000", '05', '1.5L MIVEC', 'Nhập khẩu Indonesia', '5'],
        ['mitsubishi-xpander', 'Xpander', 'MPV GIA ĐÌNH · 7 CHỖ', "MT | 560000000\nAT | 598000000\nAT Premium", '07', '1.5L MIVEC', 'Indonesia', '7'],
        ['mitsubishi-xpander-cross', 'Xpander Cross', 'CROSSOVER MPV · 7 CHỖ', 'AT | 698000000', '07', '1.5L MIVEC', 'Indonesia', '7'],
        ['mitsubishi-attrage', 'Attrage', 'SEDAN HẠNG A · 5 CHỖ', "MT | 380000000\nCVT | 465000000\nCVT Premium | 490000000", '05', '1.2L MIVEC', 'Nhập khẩu Thái Lan', '5'],
        ['mitsubishi-destinator', 'Destinator', 'SUV 7 CHỖ', "Premium | 780000000\nUltimate | 855000000", '07', '1.5L Turbo', 'Indonesia', '7'],
        ['mitsubishi-pajero-sport', 'Pajero Sport', 'SUV CỠ D · 7 CHỖ', "Diesel 4×2 AT | 1130000000\nDiesel 4×4 AT | 1365000000", '07', 'Diesel 2.4L MIVEC', 'Nhập khẩu Thái Lan', '7'],
        ['mitsubishi-triton', 'Triton', 'BÁN TẢI · 5 CHỖ', "4×2 GLX | 655000000\n4×2 Premium | 782000000\n4×4 Athlete | 924000000", '05', 'Diesel 2.4L', 'Nhập khẩu Thái Lan', 'pickup'],
    ];
    foreach ($cars as $i => $c) {
        db_save('cars', [
            'slug' => $c[0], 'name' => $c[1], 'ord' => $i + 1, 'active' => 1, 'segment' => $c[2], 'versions' => $c[3],
            'seats' => $c[4], 'engine' => $c[5], 'origin' => $c[6], 'fee_kind' => $c[7], 'warranty' => '', 'specs' => '',
            'promos' => '', 'feat_exterior' => '', 'feat_interior' => '', 'feat_drive' => '', 'feat_safety' => '',
            'image' => '', 'content' => '', 'excerpt' => '', 'updated_at' => $now,
        ]);
    }

    $banners = [
        ['assets/img/banner-destinator.webp', 'mitsubishi-destinator/', 'Mitsubishi Xforce và Destinator bên hồ mùa thu'],
        ['assets/img/banner-xpandercross.webp', 'mitsubishi-xpander-cross/', 'New Mitsubishi Xpander Cross – Tự tin vững lái, thoải mái phiêu lưu'],
        ['assets/img/banner-xpander.webp', 'mitsubishi-xpander/', 'New Mitsubishi Xpander – Tinh tế mọi góc nhìn'],
        ['assets/img/banner-triton.webp', 'mitsubishi-triton/', 'All-new Mitsubishi Triton – Chiếm lĩnh mọi lãnh địa'],
    ];
    foreach ($banners as $i => $b) {
        db_save('banners', ['image' => $b[0], 'link' => $b[1], 'alt' => $b[2], 'ord' => $i + 1, 'active' => 1]);
    }

    $photos = [
        ['01', 'Xforce', 'Khách hàng cá nhân', 'Chúc mừng anh đã chính thức nhận chiếc Xforce mới. Cảm ơn anh đã tin tưởng để em đồng hành từ lúc chọn xe đến ngày bàn giao!'],
        ['05', 'Destinator', 'Khách hàng cá nhân', 'Chúc mừng anh đã nhận Destinator. Thượng lộ bình an, có gì cần hỗ trợ về xe anh cứ gọi em nhé!'],
        ['06', 'Xforce', 'Khách hàng cá nhân', 'Cảm ơn chị đã tin tưởng. Chúc chị và gia đình có những hành trình thật trọn vẹn cùng Xforce!'],
        ['07', 'Xforce', 'Khách hàng cá nhân', 'Chúc mừng anh đã nhận xe. Bất cứ khi nào cần hỗ trợ về xe, anh cứ liên hệ em nhé!'],
        ['08', 'Destinator', 'Khách hàng cá nhân', 'Chúc mừng anh đã nhận Destinator – mẫu SUV 7 chỗ mới của Mitsubishi. Chúc anh và gia đình thượng lộ bình an!'],
        ['10', 'Xforce', 'Khách hàng gia đình', 'Chúc cả gia đình có thêm người bạn đồng hành mới. Cảm ơn anh chị đã tin tưởng em!'],
    ];
    foreach ($photos as $i => $p) {
        db_save('deliveries', ['image' => 'assets/img/giaoxe-' . $p[0] . '.webp', 'car' => $p[1], 'cust_type' => $p[2], 'msg_by' => 'consultant', 'message' => $p[3], 'cust_name' => '', 'ord' => $i + 1, 'active' => 1]);
    }

    $pages = [
        'home'       => ['Trang chủ', '', '', '', ''],
        'bang-gia'   => ['Bảng giá xe Mitsubishi', 'Giá niêm yết tham khảo, cập nhật năm 2026. Giá lăn bánh thực tế tùy nơi đăng ký và ưu đãi trong tháng – liên hệ để nhận báo giá chính xác.', 'assets/img/bg-banggia.webp', '', 'Bảng giá xe Mitsubishi 2026 mới nhất: Xforce, Xpander, Xpander Cross, Attrage, Destinator, Pajero Sport, Triton. Giá niêm yết từng phiên bản, cập nhật hằng tháng.'],
        'khuyen-mai' => ['Ưu đãi 2026', 'Chương trình áp dụng có thời hạn và số lượng giới hạn. Gọi {phone} để giữ suất ưu đãi.', 'assets/img/bg-khuyenmai.webp', '', 'Khuyến mãi xe Mitsubishi tháng này: ưu đãi lệ phí trước bạ, quà tặng phụ kiện, hỗ trợ vay. Gọi để giữ suất ưu đãi.'],
        'du-tinh'    => ['Dự tính chi phí lăn bánh', 'Chọn xe và nơi đăng ký để xem ước tính các khoản phí. Số liệu mang tính tham khảo.', 'assets/img/bg-dichvu.webp', '', 'Tính giá lăn bánh xe Mitsubishi tại Hà Nội, TP.HCM và các tỉnh: lệ phí trước bạ, biển số, phí đường bộ, bảo hiểm, đăng kiểm.'],
        'tra-gop'    => ['Tính trả góp mua xe', 'Chọn xe, số tiền trả trước, thời hạn và lãi suất để xem số tiền trả hằng tháng (cách tính dư nợ giảm dần).', 'assets/img/banner-xpandercross.webp', '', 'Tính trả góp xe Mitsubishi: trả trước bao nhiêu, mỗi tháng trả bao nhiêu. Hỗ trợ hồ sơ vay ngân hàng và gói MAF.'],
        'lai-thu'    => ['Đăng ký lái thử', 'Trải nghiệm thực tế trước khi quyết định. Lái thử tại showroom hoặc đưa xe đến tận nơi trong khu vực Hà Nội.', 'assets/img/banner-triton.webp', '', 'Đăng ký lái thử xe Mitsubishi tại showroom hoặc tận nhà khu vực Hà Nội. Xforce, Xpander, Destinator, Triton, Pajero Sport.'],
        'dich-vu'    => ['Dịch vụ sau bán hàng', 'Bảo hành, bảo dưỡng, phụ tùng chính hãng tại xưởng dịch vụ Công ty CP Ô tô Hà Nội. Hỗ trợ kỹ thuật: {tech}.', 'assets/img/bg-dichvu.webp', seed_service(), 'Dịch vụ bảo hành, bảo dưỡng, phụ tùng chính hãng Mitsubishi tại xưởng Công ty CP Ô tô Hà Nội. Đặt lịch dịch vụ trực tuyến.'],
        'tin-tuc'    => ['Tin tức & sự kiện', '', 'assets/img/bg-tintuc.webp', '', 'Tin tức xe Mitsubishi: khuyến mãi, bảng giá, kinh nghiệm mua xe và hình ảnh bàn giao xe tại showroom.'],
        'lien-he'    => ['Giới thiệu & liên hệ', '', 'assets/img/bg-lienhe.webp', '', 'Liên hệ chuyên viên tư vấn Mitsubishi Nguyễn Tiến Thành – Công ty CP Ô tô Hà Nội, Tổ 15, Phường Xuân Phương, Hà Nội. Hình ảnh khách hàng nhận xe.'],
        'chinh-sach' => ['Chính sách & điều khoản', '', 'assets/img/banner-xpander.webp', seed_policy(), 'Chính sách bảo vệ dữ liệu cá nhân, điều khoản giao dịch và quy định truy cập website.'],
        'tac-gia'    => ['Nguyễn Tiến Thành', '', '', '', 'Nguyễn Tiến Thành – chuyên viên tư vấn bán hàng Mitsubishi tại Công ty CP Ô tô Hà Nội. 4 năm kinh nghiệm, 388 xe đã bàn giao đến khách hàng.'],
    ];
    foreach ($pages as $k => $p) {
        q('INSERT INTO pages (k, title, subtitle, image, content, meta_desc, updated_at) VALUES (?,?,?,?,?,?,?)', [$k, $p[0], $p[1], $p[2], $p[3], $p[4], $now]);
    }

    db_save('posts', [
        'slug' => 'kinh-nghiem-mua-xe-mitsubishi-lan-dau',
        'title' => 'Kinh nghiệm mua xe Mitsubishi lần đầu: 7 điều tôi luôn dặn khách sau 4 năm tư vấn',
        'excerpt' => 'Giá lăn bánh, trả góp, lái thử, đặt cọc và nhận xe – những điều cần biết trước khi xuống tiền mua xe Mitsubishi.',
        'content' => seed_article(),
        'image' => 'assets/img/bg-tintuc.webp',
        'category' => 'Kinh nghiệm mua xe',
        'status' => 'publish',
        'created_at' => '2026-10-20 09:00:00',
        'updated_at' => '2026-10-20 09:00:00',
    ]);

    db()->commit();
}

function seed_service(): string
{
    return <<<HTML
<h2 id="bao-hanh">Chính sách bảo hành</h2>
<p>Xe Mitsubishi mới được <strong>bảo hành chính hãng</strong> theo thời hạn và số km ghi trong Sổ bảo hành của từng dòng xe. Liên hệ để được tư vấn chính sách áp dụng cho xe của anh/chị.</p>
<ul><li>Bảo hành tại hệ thống đại lý ủy quyền Mitsubishi trên toàn quốc</li><li>Cần bảo dưỡng đúng mốc để duy trì quyền lợi bảo hành</li><li>Điều kiện và hạng mục bảo hành chi tiết được ghi trong Sổ bảo hành giao kèm xe</li></ul>
<figure><img src="assets/img/xuong-dichvu.webp" alt="Xưởng dịch vụ Mitsubishi – Công ty CP Ô tô Hà Nội" loading="lazy" decoding="async" width="900" height="507"><figcaption>Xưởng dịch vụ tại Công ty CP Ô tô Hà Nội</figcaption></figure>
<h2 id="bao-duong">Bảo dưỡng định kỳ &amp; bảo dưỡng nhanh</h2>
<p>Bảo dưỡng theo mốc km / thời gian trong Sổ bảo hành giúp xe vận hành ổn định và giữ quyền lợi bảo hành. Dịch vụ bảo dưỡng nhanh phù hợp các hạng mục cơ bản như thay dầu, lọc.</p>
<ul><li>Mốc bảo dưỡng và hạng mục chi tiết theo Sổ bảo hành của từng dòng xe.</li><li>Bảo dưỡng nhanh: thay dầu, lọc và kiểm tra tổng quát.</li><li>Đặt lịch trước để được phục vụ đúng giờ, hạn chế thời gian chờ – xem form <a href="#dat-lich">Đặt lịch dịch vụ</a> bên dưới.</li></ul>
<h2 id="phu-tung">Phụ tùng &amp; phụ kiện</h2>
<h3>Phụ tùng chính hãng</h3><p>Phụ tùng thay thế chính hãng Mitsubishi. Liên hệ để kiểm tra mã và báo giá theo xe của anh/chị.</p>
<h3>Phụ kiện chính hãng</h3><p>Camera, cảm biến, bệ bước, phim cách nhiệt, thảm sàn… Liên hệ để nhận danh mục và báo giá theo dòng xe.</p>
<h3>Phụ kiện nhà phân phối</h3><p>Các gói phụ kiện do đại lý cung cấp. Liên hệ <a href="tel:0978271605">0978271605</a> để nhận danh mục và báo giá cập nhật.</p>
<h2 id="connect">Ứng dụng Mitsubishi Connect+</h2>
<p>Ứng dụng kết nối xe của Mitsubishi. Liên hệ để được hướng dẫn cài đặt và sử dụng trên điện thoại.</p>
<h3>Tài liệu hướng dẫn sử dụng</h3><p>Sách hướng dẫn sử dụng được giao kèm xe. Cần bản điện tử theo dòng xe, anh/chị liên hệ để được gửi qua Zalo.</p>
HTML;
}

function seed_policy(): string
{
    return <<<HTML
<h2 id="bao-mat">Chính sách bảo vệ dữ liệu cá nhân</h2>
<h3>1. Thông tin được thu thập</h3><p>Họ tên, số điện thoại, dòng xe quan tâm, khu vực đăng ký xe, hình thức thanh toán và các ghi chú anh/chị tự nguyện cung cấp qua các biểu mẫu trên trang.</p>
<h3>2. Mục đích sử dụng</h3><p>Liên hệ tư vấn, gửi báo giá, sắp xếp lái thử, đặt lịch dịch vụ và thông báo chương trình ưu đãi liên quan đến yêu cầu của anh/chị.</p>
<h3>3. Đơn vị xử lý dữ liệu</h3><p>Công ty Cổ phần Ô tô Hà Nội và chuyên viên tư vấn được công ty ủy quyền. Khi anh/chị đề nghị hỗ trợ vay, thông tin cần thiết cho hồ sơ vay sẽ được chuyển cho ngân hàng hoặc công ty tài chính mà anh/chị lựa chọn.</p>
<h3>4. Thời gian lưu trữ</h3><p>Dữ liệu được lưu trong thời gian cần thiết để tư vấn và chăm sóc khách hàng, và được xóa khi anh/chị yêu cầu, trừ trường hợp pháp luật yêu cầu lưu giữ.</p>
<h3>5. Quyền của anh/chị</h3><p>Anh/chị có quyền xem, chỉnh sửa, yêu cầu xóa dữ liệu hoặc rút lại sự đồng ý bất kỳ lúc nào bằng cách liên hệ <a href="mailto:cskh@hanoiauto.com.vn">cskh@hanoiauto.com.vn</a> hoặc hotline <a href="tel:19004528">19004528</a>, theo quy định của pháp luật Việt Nam về bảo vệ dữ liệu cá nhân.</p>
<h2 id="dieu-khoan">Điều khoản giao dịch</h2>
<h3>1. Giá và thông tin sản phẩm</h3><p>Giá niêm yết và thông số trên trang mang tính tham khảo, có thể thay đổi theo chính sách của nhà phân phối. Giá bán chính thức được ghi trong hợp đồng mua bán.</p>
<h3>2. Đặt cọc và thanh toán</h3><p>Mức đặt cọc và tiến độ thanh toán được ghi rõ trong hợp đồng mua bán. Tiền cọc và tiền xe chỉ chuyển vào tài khoản đứng tên Công ty Cổ phần Ô tô Hà Nội ghi trên hợp đồng.</p>
<h3>3. Giao xe</h3><p>Thời gian giao xe dự kiến được ghi trong hợp đồng. Xe được bàn giao tại showroom kèm hồ sơ xe, Sổ bảo hành và hướng dẫn sử dụng.</p>
<h3>4. Khuyến mãi</h3><p>Chương trình khuyến mãi áp dụng theo thời hạn và điều kiện công bố trong từng tháng.</p>
<h2 id="truy-cap">Quy định truy cập website</h2>
<p>Nội dung, hình ảnh trên trang được sử dụng để giới thiệu sản phẩm và dịch vụ của đại lý. Logo và nhãn hiệu Mitsubishi thuộc quyền sở hữu của chủ sở hữu nhãn hiệu.</p>
<p>Người truy cập không sử dụng trang cho mục đích trái pháp luật, không gửi thông tin giả mạo qua các biểu mẫu.</p>
<p>Người chịu trách nhiệm nội dung: Nguyễn Tiến Thành.</p>
HTML;
}

function seed_article(): string
{
    return <<<HTML
<p>Sau 4 năm tư vấn và hơn 388 lần bàn giao xe tại showroom, tôi nhận ra phần lớn băn khoăn của khách mua xe lần đầu đều giống nhau: <strong>tổng cộng phải trả bao nhiêu, vay thế nào cho an toàn, và ký giấy tờ gì để không bị thiệt</strong>. Dưới đây là 7 điều tôi luôn dặn trước khi khách xuống tiền.</p>
<h2 id="y1">1. Giá niêm yết khác giá lăn bánh</h2>
<p>Giá niêm yết là giá xe. Muốn xe lăn bánh hợp pháp, anh/chị còn phải trả thêm lệ phí trước bạ, phí biển số, phí đường bộ, bảo hiểm trách nhiệm dân sự bắt buộc và phí đăng kiểm. Ví dụ ước tính cho <a href="/mitsubishi-xpander/">Mitsubishi Xpander</a> bản giá từ 560 triệu, đăng ký tại Hà Nội:</p>
<table><tbody><tr><td>Giá niêm yết</td><td>560.000.000 đ</td></tr><tr><td>Lệ phí trước bạ (Hà Nội 12%)</td><td>67.200.000 đ</td></tr><tr><td>Lệ phí biển số Hà Nội (tối đa)</td><td>20.000.000 đ</td></tr><tr><td>Phí đường bộ 12 tháng</td><td>1.560.000 đ</td></tr><tr><td>Bảo hiểm TNDS bắt buộc (xe 7 chỗ)</td><td>873.400 đ</td></tr><tr><td>Giấy chứng nhận đăng kiểm</td><td>90.000 đ</td></tr><tr><td><strong>Tổng ước tính</strong></td><td><strong>≈ 649.700.000 đ</strong></td></tr></tbody></table>
<p>Như vậy, ngoài tiền xe nên chuẩn bị thêm khoảng 15–16%, chưa kể bảo hiểm vật chất và phí dịch vụ đăng ký. Lệ phí trước bạ thực tế tính theo bảng giá của Bộ Tài chính nên có thể lệch đôi chút. Anh/chị có thể tự tính cho từng xe và từng tỉnh ở trang <a href="/tinh-gia-lan-banh/">Dự tính chi phí lăn bánh</a>.</p>
<h2 id="y2">2. Chọn xe theo nhu cầu thật</h2>
<p>Câu đầu tiên tôi hỏi khách không phải là "thích xe nào" mà là "xe chủ yếu dùng để làm gì":</p>
<ul><li><strong>Gia đình 5–7 người, đi phố là chính:</strong> nhóm MPV như <a href="/mitsubishi-xpander/">Xpander</a>, <a href="/mitsubishi-xpander-cross/">Xpander Cross</a> rộng rãi, chi phí vận hành hợp lý.</li><li><strong>Đi phố gọn gàng, ưu tiên công nghệ:</strong> SUV cỡ B như <a href="/mitsubishi-xforce/">Xforce</a>.</li><li><strong>Cần 7 chỗ, gầm cao, đi tỉnh nhiều:</strong> <a href="/mitsubishi-destinator/">Destinator</a> hoặc <a href="/mitsubishi-pajero-sport/">Pajero Sport</a>.</li><li><strong>Vừa chở hàng vừa đi lại:</strong> bán tải <a href="/mitsubishi-triton/">Triton</a>. Lệ phí trước bạ của xe bán tải chỉ bằng 60% mức của xe con, nên chi phí lăn bánh thấp hơn đáng kể.</li></ul>
<h2 id="y3">3. Trả góp: đừng chỉ nhìn lãi suất năm đầu</h2>
<p>Nhiều gói vay quảng cáo lãi suất thấp nhưng chỉ cố định 6–12 tháng đầu, sau đó thả nổi theo ngân hàng. Khi so sánh, tôi khuyên khách hỏi đủ 4 câu:</p>
<ol><li>Lãi suất cố định bao lâu, sau đó tính theo công thức nào?</li><li>Trả trước tối thiểu bao nhiêu phần trăm?</li><li>Phí trả nợ trước hạn là bao nhiêu?</li><li>Có bắt buộc mua kèm bảo hiểm nào không?</li></ol>
<p>Một nguyên tắc an toàn là tiền góp mỗi tháng không nên vượt quá khoảng 30–40% thu nhập ổn định của gia đình. Anh/chị có thể thử các phương án ở trang <a href="/mua-xe-tra-gop/">Tính trả góp</a>.</p>
<h2 id="y4">4. Luôn lái thử trước khi chốt</h2>
<p>Thông số trên giấy không thay được cảm giác thực tế. Khi lái thử, hãy để ý:</p>
<ul><li>Tư thế ngồi, tầm nhìn và điểm mù ở cột A.</li><li>Độ êm và độ ồn khi đi qua đường xấu, gờ giảm tốc.</li><li>Khả năng tăng tốc khi vượt và lên dốc, đặc biệt khi chở đủ người.</li><li>Không gian hàng ghế sau và cốp nếu thường xuyên đi cả gia đình.</li></ul>
<p>Nếu không tiện qua showroom, có thể <a href="/dang-ky-lai-thu/">đăng ký lái thử tận nhà</a>.</p>
<h2 id="y5">5. Đọc kỹ điều kiện khuyến mãi</h2>
<p>Ưu đãi thay đổi theo tháng và theo phiên bản. Trước khi chốt, hãy hỏi rõ: ưu đãi áp dụng đến ngày nào, cho phiên bản nào, quà tặng có quy đổi được tiền mặt không (thường là không), và có cộng dồn với chương trình khác được không. Mọi ưu đãi nên được ghi vào hợp đồng hoặc phụ lục. Xem các chương trình hiện có tại trang <a href="/khuyen-mai/">Khuyến mãi</a>.</p>
<h2 id="y6">6. Đặt cọc và hợp đồng phải rõ ràng</h2>
<p>Hợp đồng mua bán cần ghi đủ: phiên bản, màu xe, giá bán, các khoản ưu đãi, thời gian giao xe dự kiến và điều kiện hoàn cọc nếu không giao xe đúng hẹn. <strong>Tiền cọc và tiền xe chỉ chuyển vào tài khoản đứng tên công ty</strong> ghi trên hợp đồng, không chuyển vào tài khoản cá nhân của bất kỳ ai, kể cả tư vấn viên.</p>
<h2 id="y7">7. Kiểm tra kỹ trong ngày nhận xe</h2>
<ul><li>Đi một vòng quanh xe dưới ánh sáng tốt để kiểm tra sơn, kính, la-zăng.</li><li>Đối chiếu số khung, số máy trên xe với giấy tờ.</li><li>Kiểm tra đủ phụ kiện, quà tặng như đã ghi trong hợp đồng.</li><li>Nhận sổ bảo hành, hỏi rõ mốc bảo dưỡng đầu tiên và cách đặt lịch.</li><li>Nhờ tư vấn viên hướng dẫn các tính năng chính trước khi lái xe về.</li></ul>
<p>Nếu anh/chị đang cân nhắc mua xe Mitsubishi và muốn có bảng tính lăn bánh, trả góp cụ thể cho trường hợp của mình, cứ nhắn tôi. Tư vấn hoàn toàn miễn phí.</p>
HTML;
}

/**
 * Dữ liệu mẫu chuẩn hãng cho trang chi tiết xe của toàn bộ các dòng xe Mitsubishi.
 * Bao gồm: phiên bản, màu sắc thực tế, thông số kỹ thuật đa cột, banner đặc điểm nổi bật,
 * các tính năng xen kẽ và bộ phụ kiện chính hãng.
 */
function seed_car_details(): void
{
    $seeds = [
        'mitsubishi-xforce' => [
            'versions' => [
                [
                    'name' => 'GLX',
                    'desc' => 'Bản tiêu chuẩn trang bị tiện nghi thiết yếu, động cơ 1.5L MIVEC tiết kiệm nhiên liệu và gầm cao 219mm',
                    'image' => '',
                    'equipment' => "Động cơ 1.5L MIVEC kết hợp hộp số tự động vô cấp CVT\nKhoảng sáng gầm xe 219mm hàng đầu phân khúc\nMâm xe hợp kim 17-inch thiết kế hiện đại\nĐèn chiếu sáng trước LED T-Shape thời thượng\nPhanh tay điện tử & Tự động giữ phanh Auto Hold\nMàn hình giải trí cảm ứng 8-inch hỗ trợ Apple CarPlay & Android Auto\nBảng đồng hồ kỹ thuật số đa thông tin\n4 túi khí an toàn tiêu chuẩn",
                    'colors' => [['name' => 'Trắng', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Đen', 'hex' => '#1a1a1a', 'image' => ''], ['name' => 'Xám', 'hex' => '#7a7f86', 'image' => ''], ['name' => 'Đỏ', 'hex' => '#cc0000', 'image' => '']],
                ],
                [
                    'name' => 'Exceed',
                    'desc' => 'Nâng cấp tiện nghi với mâm 18-inch, 4 chế độ lái, điều hòa tự động 2 vùng độc lập công nghệ nanoe™ X',
                    'image' => '',
                    'equipment' => "Động cơ 1.5L MIVEC cùng hộp số tự động vô cấp CVT\nKhoảng sáng gầm xe 222mm vượt trội phân khúc\nMâm xe hợp kim 18-inch phay bóng thể thao\n4 chế độ lái thích ứng: Đường nhựa, Ngập nước (Wet), Sỏi đá, Bùn lầy\nHệ thống kiểm soát vào cua chủ động AYC (Active Yaw Control)\nĐiều hòa tự động 2 vùng độc lập tích hợp công nghệ lọc không khí nanoe™ X\nVô lăng bọc da cao cấp tích hợp điều khiển hành trình Cruise Control\nĐèn sương mù LED phía trước\nCảm biến lùi hỗ trợ đỗ xe",
                    'colors' => [['name' => 'Vàng', 'hex' => '#d4a017', 'image' => ''], ['name' => 'Trắng', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Đen', 'hex' => '#1a1a1a', 'image' => ''], ['name' => 'Xám', 'hex' => '#7a7f86', 'image' => ''], ['name' => 'Đỏ', 'hex' => '#cc0000', 'image' => '']],
                ],
                [
                    'name' => 'Premium',
                    'desc' => 'Trang bị sang trọng với sạc không dây, đèn viền nội thất, cảnh báo điểm mù BSW và phương tiện cắt ngang RCTA',
                    'image' => '',
                    'equipment' => "Tất cả trang bị của bản Exceed\nTính năng an toàn chủ động: Cảnh báo điểm mù BSW & Cảnh báo phương tiện cắt ngang khi lùi RCTA\nSạc điện thoại không dây chuẩn Qi tiện lợi\nĐèn chiếu viền nội thất Ambient Light tinh tế\nMàn hình trung tâm cảm ứng cỡ lớn 12.3-inch siêu nét\nBảng đồng hồ kỹ thuật số 8-inch đa thông tin tùy biến giao diện\nChức năng mở cốp rảnh tay (đá cốp thông minh)",
                    'colors' => [['name' => 'Vàng', 'hex' => '#d4a017', 'image' => ''], ['name' => 'Trắng', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Đen', 'hex' => '#1a1a1a', 'image' => ''], ['name' => 'Xám', 'hex' => '#7a7f86', 'image' => ''], ['name' => 'Đỏ', 'hex' => '#cc0000', 'image' => '']],
                ],
                [
                    'name' => 'Ultimate',
                    'desc' => 'Phiên bản cao cấp nhất với âm thanh Yamaha Premium 8 loa độc quyền và gói an toàn chủ động thông minh Diamond Sense (ADAS)',
                    'image' => '',
                    'equipment' => "Hệ thống âm thanh cao cấp Dynamic Sound Yamaha Premium 8 loa độc quyền\nGói an toàn chủ động thông minh Diamond Sense (ADAS):\n- Kiểm soát hành trình thích ứng ACC\n- Hệ thống cảnh báo & giảm thiểu va chạm phía trước FCM\n- Đèn pha tự động thích ứng AHB\n- Cảnh báo xe phía trước khởi hành LCDN\nCửa cốp đóng/mở điện thông minh tích hợp cảm biến đá cốp rảnh tay\nCảm biến áp suất lốp TPMS hiển thị từng bánh\n6 túi khí an toàn toàn diện",
                    'colors' => [['name' => 'Vàng - Nóc đen', 'hex' => '#d4a017', 'image' => ''], ['name' => 'Đỏ - Nóc đen', 'hex' => '#cc0000', 'image' => ''], ['name' => 'Trắng - Nóc đen', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Đen', 'hex' => '#1a1a1a', 'image' => ''], ['name' => 'Xám', 'hex' => '#7a7f86', 'image' => '']],
                ],
            ],
            'specs' => [
                ['title' => 'Kích thước và trọng lượng', 'rows' => "Số chỗ ngồi | 5 | 5 | 5 | 5\nKích thước tổng thể (DxRxC) | 4.390 x 1.810 x 1.660 mm\nChiều dài cơ sở | 2.650 mm\nKhoảng sáng gầm xe | 219 mm | 222 mm | 222 mm | 222 mm\nBán kính quay vòng | 5.2 m\nDung tích bình nhiên liệu | 42 L"],
                ['title' => 'Động cơ và vận hành', 'rows' => "Loại động cơ | 1.5L MIVEC DOHC 16 van\nCông suất cực đại | 105 PS tại 6.000 vòng/phút\nMô-men xoắn cực đại | 141 Nm tại 4.000 vòng/phút\nHộp số | Tự động vô cấp CVT\nChế độ lái | Tiêu chuẩn | 4 chế độ: Normal, Wet, Gravel, Mud | 4 chế độ | 4 chế độ\nHệ thống vào cua chủ động AYC | Có | Có | Có | Có"],
                ['title' => 'Khung gầm và truyền động', 'rows' => "Hệ thống truyền động | Cầu trước (FWD)\nHệ thống treo trước | Kiểu MacPherson với thanh cân bằng\nHệ thống treo sau | Dầm xoắn\nPhanh trước / sau | Đĩa thông gió / Đĩa\nThông số lốp | 205/60R17 | 225/50R18 | 225/50R18 | 225/50R18"],
                ['title' => 'Ngoại thất', 'rows' => "Đèn chiếu sáng trước | LED T-Shape\nĐèn chiếu sáng ban ngày | LED\nĐèn sương mù | Không | LED | LED | LED\nCửa cốp đóng mở điện | Không | Không | Đá cốp rảnh tay | Đá cốp rảnh tay"],
                ['title' => 'Nội thất & Tiện nghi', 'rows' => "Chất liệu ghế | Nỉ cao cấp | Da giảm tích tụ nhiệt | Da giảm tích tụ nhiệt | Da giảm tích tụ nhiệt\nĐiều hòa nhiệt độ | Chỉnh tay | Tự động 2 vùng + nanoe™ X | Tự động 2 vùng + nanoe™ X | Tự động 2 vùng + nanoe™ X\nMàn hình giải trí | 8-inch | 8-inch | 12.3-inch | 12.3-inch\nHệ thống âm thanh | 4 loa | 6 loa | 6 loa | 8 loa Dynamic Sound Yamaha Premium"],
                ['title' => 'An toàn', 'rows' => "Túi khí an toàn | 4 túi khí | 4 túi khí | 4 túi khí | 6 túi khí\nGói an toàn chủ động ADAS | — | — | — | Tiêu chuẩn\nCảnh báo điểm mù BSW & RCTA | — | — | Tiêu chuẩn | Tiêu chuẩn\nCảm biến áp suất lốp TPMS | — | — | — | Tiêu chuẩn"],
            ],
            'highlight' => ['image' => '', 'kicker' => 'MẪU SUV ĐÔ THỊ THẾ HỆ MỚI', 'title' => 'MITSUBISHI XFORCE', 'desc' => 'Khởi đầu kỷ nguyên SUV đô thị với thiết kế Silky & Solid mạnh mẽ, nội thất tiện nghi hàng đầu phân khúc cùng 4 chế độ lái thích ứng thời tiết Việt Nam.'],
            'features' => [
                ['image' => '', 'kicker' => 'THIẾT KẾ KHÍ CHẤT', 'title' => 'NGÔN NGỮ THIẾT KẾ SILKY & SOLID', 'desc' => 'Thân xe mượt mà kết hợp các đường dập nổi cơ bắp, cụm đèn T-Shape đặc trưng tạo nên diện mạo SUV đô thị đầy cuốn hút.'],
                ['image' => '', 'kicker' => 'KHÔNG GIAN TIỆN NGHI', 'title' => 'NỘI THẤT RỘNG RÃI & SANG TRỌNG', 'desc' => 'Ghế ngồi bọc da giảm tích tụ nhiệt, hàng ghế sau có thể ngả 8 cấp độ mang lại sự thư thái tuyệt đối trên mọi cung đường.'],
                ['image' => '', 'kicker' => 'ÂM THANH ĐỈNH CAO', 'title' => 'DYNAMIC SOUND YAMAHA PREMIUM', 'desc' => 'Hệ thống 8 loa cao cấp được hiệu chỉnh riêng bởi kỹ sư âm thanh Yamaha với 4 chế độ âm thanh: Lively, Signature, Powerful và Relaxing.'],
                ['image' => '', 'kicker' => 'CHẾ ĐỘ LÁI ĐA DẠNG', 'title' => 'CHẾ ĐỘ ĐƯỜNG NGẬP NƯỚC (WET MODE)', 'desc' => 'Lần đầu tiên xuất hiện trên xe Mitsubishi, tối ưu kiểm soát lực kéo và độ bám đường, mang lại sự an tâm tối đa khi lái xe dưới trời mưa lớn.'],
                ['image' => '', 'kicker' => 'AN TOÀN CHỦ ĐỘNG', 'title' => 'HỆ THỐNG AN TOÀN DIAMOND SENSE', 'desc' => 'Tích hợp đầy đủ các công nghệ an toàn tiên tiến: Phanh giảm thiểu va chạm FCM, Kiểm soát hành trình thích ứng ACC, Cảnh báo điểm mù BSW.'],
            ],
            'accessories' => [
                'title' => 'PHỤ KIỆN CHÍNH HÃNG XFORCE', 'desc' => 'Bộ phụ kiện đẳng cấp giúp bạn khẳng định cá tính riêng.', 'image' => '',
                'items' => [
                    ['image' => '', 'name' => 'Ốp cản trước thể thao'], ['image' => '', 'name' => 'Ốp cản sau thể thao'],
                    ['image' => '', 'name' => 'Bộ chữ nổi XFORCE'], ['image' => '', 'name' => 'Đèn LED bệ bước chân'],
                    ['image' => '', 'name' => 'Ốp lướt gió đuôi xe'], ['image' => '', 'name' => 'Thảm lót sàn cao cấp'],
                ],
            ],
            'partner' => ['image' => '', 'kicker' => 'PHỤ KIỆN HỢP TÁC', 'title' => 'ĐA DẠNG PHỤ KIỆN LỰA CHỌN', 'desc' => 'Kết nối sáng tạo, cá nhân hóa chiếc xe của bạn theo phong cách riêng.'],
        ],

        'mitsubishi-xpander' => [
            'versions' => [
                [
                    'name' => 'MT',
                    'desc' => 'Bản số sàn 5 cấp bền bỉ, tiết kiệm nhiên liệu tối ưu, chi phí vận hành cực thấp',
                    'image' => '',
                    'equipment' => "Động cơ 1.5L MIVEC tiết kiệm nhiên liệu\nHộp số sàn 5 cấp êm ái\nKhoảng sáng gầm xe 205mm linh hoạt\n7 chỗ ngồi rộng rãi, gập phẳng linh hoạt\nĐèn halogen phản xạ đa chiều\nĐiều hòa nhiệt độ chỉnh cơ 2 dàn lạnh độc lập\nPhanh ABS, EBD, BA, Cân bằng điện tử ASC",
                    'colors' => [['name' => 'Trắng', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Đen', 'hex' => '#1a1a1a', 'image' => ''], ['name' => 'Bạc', 'hex' => '#c0c0c0', 'image' => ''], ['name' => 'Nâu', 'hex' => '#6b4c35', 'image' => '']],
                ],
                [
                    'name' => 'AT',
                    'desc' => 'Bản số tự động 4 cấp vận hành mượt mà, đầy đủ tiện nghi cho gia đình',
                    'image' => '',
                    'equipment' => "Hộp số tự động 4 cấp vận hành nhẹ nhàng\nKhoảng sáng gầm xe 225mm vượt trội\nĐèn chiếu sáng LED T-Shape hiện đại\nNội thất nỉ cao cấp phối màu đen thể thao\nMàn hình giải trí cảm ứng 7-inch kết nối Apple CarPlay & Android Auto\nCamera lùi góc rộng",
                    'colors' => [['name' => 'Trắng', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Đen', 'hex' => '#1a1a1a', 'image' => ''], ['name' => 'Bạc', 'hex' => '#c0c0c0', 'image' => ''], ['name' => 'Nâu', 'hex' => '#6b4c35', 'image' => '']],
                ],
                [
                    'name' => 'AT Premium',
                    'desc' => 'Phiên bản cao cấp nhất với phanh tay điện tử Auto Hold, điều hòa kỹ thuật số Max Cool, nội thất da sang trọng',
                    'image' => '',
                    'equipment' => "Phanh tay điện tử & Tự động giữ phanh Auto Hold\nKhoảng sáng gầm xe 225mm cao nhất phân khúc\nMâm xe hợp kim 17-inch phay bóng thể thao\nGhế da cao cấp kháng nhiệt, nội thất phối 2 tông màu Nâu - Đen\nĐiều hòa kỹ thuật số có chế độ làm mát nhanh Max Cool\nMàn hình giải trí cảm ứng 9-inch độ nét cao\nCảm biến gạt mưa tự động & Cảm biến bật/tắt đèn chiếu sáng",
                    'colors' => [['name' => 'Trắng', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Đen', 'hex' => '#1a1a1a', 'image' => ''], ['name' => 'Bạc', 'hex' => '#c0c0c0', 'image' => ''], ['name' => 'Nâu', 'hex' => '#6b4c35', 'image' => '']],
                ],
            ],
            'specs' => [
                ['title' => 'Kích thước và trọng lượng', 'rows' => "Số chỗ ngồi | 7 | 7 | 7\nKích thước tổng thể (DxRxC) | 4.475 x 1.750 x 1.700 mm | 4.595 x 1.750 x 1.750 mm | 4.595 x 1.750 x 1.750 mm\nChiều dài cơ sở | 2.775 mm\nKhoảng sáng gầm xe | 205 mm | 225 mm | 225 mm\nBán kính quay vòng | 5.2 m"],
                ['title' => 'Động cơ và vận hành', 'rows' => "Loại động cơ | 1.5L MIVEC DOHC 16 van\nCông suất cực đại | 104 PS tại 6.000 vòng/phút\nMô-men xoắn cực đại | 141 Nm tại 4.000 vòng/phút\nHộp số | Số sàn 5 cấp | Tự động 4 cấp | Tự động 4 cấp\nDung tích bình xăng | 45 L"],
                ['title' => 'Ngoại thất', 'rows' => "Cụm đèn chiếu sáng | Halogen | LED T-Shape | LED T-Shape\nMâm xe | 16-inch | 16-inch | 17-inch hợp kim 2 tông màu\nĂng-ten vây cá | Có | Có | Có"],
                ['title' => 'Nội thất & Tiện nghi', 'rows' => "Chất liệu ghế | Nỉ cao cấp | Nỉ cao cấp | Da kháng nhiệt cao cấp\nPhanh tay | Cơ | Cơ | Điện tử & Tự động giữ phanh Auto Hold\nĐiều hòa | Chỉnh cơ | Chỉnh cơ | Kỹ thuật số (Max Cool)\nMàn hình giải trí | Tiêu chuẩn | 7-inch | 9-inch Apple CarPlay / Android Auto"],
                ['title' => 'An toàn', 'rows' => "Hệ thống túi khí | 2 túi khí\nHệ thống phanh ABS, EBD, BA | Có\nCân bằng điện tử ASC & Khởi hành ngang dốc HSA | Có\nCamera lùi | Không | Có | Có"],
            ],
            'highlight' => ['image' => '', 'kicker' => 'VUA DOANH SỐ PHÂN KHÚC MPV', 'title' => 'MITSUBISHI XPANDER', 'desc' => 'Dòng xe 7 chỗ bán chạy nhất thị trường Việt Nam với thiết kế SUV hiện đại, không gian rộng rãi vượt trội và khả năng tiết kiệm nhiên liệu bền bỉ.'],
            'features' => [
                ['image' => '', 'kicker' => 'NGOẠI THẤT ẤN TƯỢNG', 'title' => 'THIẾT KẾ ĐẬM CHẤT SUV', 'desc' => 'Ngôn ngữ thiết kế Dynamic Shield hiện đại với cụm đèn chiếu sáng T-Shape nổi bật, mâm xe 17-inch thể thao.'],
                ['image' => '', 'kicker' => 'KHÔNG GIAN RỘNG RÃI', 'title' => 'NỘI THẤT 7 CHỖ LINH HOẠT', 'desc' => 'Các hàng ghế có thể gập phẳng hoàn toàn tạo khoang hành lý siêu lớn, trang bị nhiều cổng sạc cho mọi vị trí ngồi.'],
                ['image' => '', 'kicker' => 'VẬN HÀNH ÊM ÁI', 'title' => 'KHOẢNG SÁNG GẦM 225MM CAO NHẤT PHÂN KHÚC', 'desc' => 'Dễ dàng vượt qua các đoạn đường gồ ghề hay ngập nước mùa mưa, hệ thống treo cải tiến mang lại sự êm ái đầm chắc.'],
                ['image' => '', 'kicker' => 'TIỆN NGHI CAO CẤP', 'title' => 'PHANH TAY ĐIỆN TỬ & AUTO HOLD', 'desc' => 'Thao tác lái xe trong đô thị nhàn nhã và an toàn hơn bao giờ hết, tự động giữ xe khi dừng đèn đỏ.'],
            ],
            'accessories' => [
                'title' => 'PHỤ KIỆN CHÍNH HÃNG XPANDER', 'desc' => 'Nâng tầm phong cách và sự tiện dụng cho chiếc xe gia đình.', 'image' => '',
                'items' => [
                    ['image' => '', 'name' => 'Ốp cản trước'], ['image' => '', 'name' => 'Ốp cản sau'],
                    ['image' => '', 'name' => 'Vè che mưa'], ['image' => '', 'name' => 'Ốp bậc lên xuống có đèn'],
                    ['image' => '', 'name' => 'Khay hành lý'], ['image' => '', 'name' => 'Thảm trải sàn đúc khuôn'],
                ],
            ],
            'partner' => ['image' => '', 'kicker' => 'TIỆN ÍCH GIA ĐÌNH', 'title' => 'PHỤ KIỆN TIỆN ÍCH BÊN THỨ 3', 'desc' => 'Màn hình trần, camera 360, dán phim cách nhiệt cao cấp.'],
        ],

        'mitsubishi-xpander-cross' => [
            'versions' => [
                [
                    'name' => 'AT',
                    'desc' => 'Crossover MPV 7 chỗ phong cách địa hình mạnh mẽ, hệ thống kiểm soát vào cua chủ động AYC và khoảng sáng gầm 225mm',
                    'image' => '',
                    'equipment' => "Động cơ 1.5L MIVEC tiết kiệm nhiên liệu\nHệ thống kiểm soát vào cua chủ động AYC (Active Yaw Control)\nKhoảng sáng gầm xe 225mm vượt trội\nỐp vòm bánh xe và bodykit SUV thể thao cá tính\nVô lăng 4 chấu thể thao bọc da sang trọng kế thừa từ Pajero Sport\nBảng đồng hồ kỹ thuật số 8-inch hiện đại\nPhanh tay điện tử & Giữ phanh tự động Auto Hold\nĐèn chiếu sáng Full LED T-Shape & Đèn sương mù LED",
                    'colors' => [['name' => 'Cam - Đen', 'hex' => '#e65c00', 'image' => ''], ['name' => 'Trắng', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Đen', 'hex' => '#1a1a1a', 'image' => ''], ['name' => 'Nâu', 'hex' => '#6b4c35', 'image' => '']],
                ],
            ],
            'specs' => [
                ['title' => 'Kích thước và trọng lượng', 'rows' => "Số chỗ ngồi | 7\nKích thước tổng thể (DxRxC) | 4.595 x 1.790 x 1.750 mm\nChiều dài cơ sở | 2.775 mm\nKhoảng sáng gầm xe | 225 mm\nBán kính quay vòng | 5.2 m"],
                ['title' => 'Động cơ và vận hành', 'rows' => "Loại động cơ | 1.5L MIVEC DOHC\nCông suất cực đại | 104 PS tại 6.000 vòng/phút\nMô-men xoắn cực đại | 141 Nm tại 4.000 vòng/phút\nHộp số | Tự động 4 cấp\nHệ thống vào cua chủ động AYC | Tiêu chuẩn"],
                ['title' => 'Ngoại thất & Nội thất', 'rows' => "Mâm xe | 17-inch hợp kim 5 chấu kép thể thao\nGhế ngồi | Da cao cấp kháng nhiệt 2 tông màu Xanh Navy - Đen\nVô lăng | Bọc da 4 chấu tích hợp Cruise Control\nĐồng hồ lái | Màn hình điện tử 8-inch"],
                ['title' => 'An toàn', 'rows' => "Túi khí | 2 túi khí\nHệ thống an toàn | ABS, EBD, BA, ASC, TCL, HSA\nCamera | Camera 360 toàn cảnh sắc nét"],
            ],
            'highlight' => ['image' => '', 'kicker' => 'BẢN LĨNH ĐỊA HÌNH · PHONG CÁCH SUV', 'title' => 'MITSUBISHI XPANDER CROSS', 'desc' => 'Sự kết hợp hoàn hảo giữa không gian rộng rãi của MPV và khả năng chinh phục địa hình mạnh mẽ của dòng SUV thể thao.'],
            'features' => [
                ['image' => '', 'kicker' => 'VẬN HÀNH TỰ TIN', 'title' => 'HỆ THỐNG KIỂM SOÁT VÀO CUA CHỦ ĐỘNG AYC', 'desc' => 'Tự động can thiệp lực phanh ở bánh trước khi vào cua, giúp xe ôm cua chuẩn xác và an toàn ngay cả trên mặt đường trơn trượt.'],
                ['image' => '', 'kicker' => 'PHONG CÁCH SUV', 'title' => 'BODYKIT THỂ THAO ĐẬM CHẤT VI DÃ NGOẠI', 'desc' => 'Thanh giá nóc thể thao, ốp viền cua lốp cơ bắp và mâm đúc 17-inch phay bóng tạo nên chất SUV đích thực.'],
            ],
            'accessories' => [
                'title' => 'PHỤ KIỆN CHÍNH HÃNG XPANDER CROSS', 'desc' => 'Gói phụ kiện dã ngoại chuyên dụng.', 'image' => '',
                'items' => [
                    ['image' => '', 'name' => 'Ốp nắp capo thể thao'], ['image' => '', 'name' => 'Thanh ngang giá nóc'],
                    ['image' => '', 'name' => 'Ốp bậc cửa phát sáng'], ['image' => '', 'name' => 'Bao da chìa khóa'],
                ],
            ],
            'partner' => ['image' => '', 'kicker' => 'TRANG BỊ DÃ NGOẠI', 'title' => 'PHỤ KIỆN DÃ NGOẠI BÊN THỨ 3', 'desc' => 'Lều nóc ô tô, khay chở xe đạp thể thao.'],
        ],

        'mitsubishi-destinator' => [
            'versions' => [
                [
                    'name' => 'Ultimate',
                    'desc' => 'Động cơ xăng Turbo 1.5L MIVEC, ngoại thất SUV mạnh mẽ, nội thất 7 chỗ rộng rãi và nhiều tiện ích hiện đại',
                    'image' => '',
                    'equipment' => "Hộp số tự động vô cấp CVT hiệu suất cao\nKhoảng sáng gầm hàng đầu phân khúc 214mm\nĐèn chiếu sáng phía trước & phía sau công nghệ LED\nĐèn sương mù phía trước công nghệ LED\nCửa cốp điều khiển điện & chức năng đóng/mở rảnh tay\nPhanh tay điện tử & giữ phanh tự động\nGhế người lái & ghế hành khách phía trước chỉnh điện 6 hướng\nĐiều hòa tự động, hai vùng độc lập, với công nghệ lọc không khí nanoe™ X\nHệ thống kết nối thông minh MITSUBISHI CONNECT (Điều khiển điều hòa từ xa, tìm xe, hỗ trợ khẩn cấp)\nĐèn viền nội thất 64 màu\nBảng đồng hồ kỹ thuật số 8-inch\nMàn hình giải trí 12.3 inch hỗ trợ kết nối Android Auto và Apple CarPlay không dây\nHệ thống âm thanh 8 loa Dynamic Sound Yamaha Premium\n6 túi khí, Camera toàn cảnh 360",
                    'colors' => [['name' => 'Trắng - Đen', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Đen', 'hex' => '#1a1a1a', 'image' => ''], ['name' => 'Xám', 'hex' => '#7a7f86', 'image' => '']],
                ],
                [
                    'name' => 'Premium',
                    'desc' => 'Động cơ Turbo mạnh mẽ, 7 chỗ tiện nghi sang trọng cho gia đình với mức chi phí hợp lý',
                    'image' => '',
                    'equipment' => "Động cơ xăng Turbo 1.5L MIVEC công suất 163 PS\nHộp số tự động vô cấp CVT êm ái\nKhoảng sáng gầm 214mm\nNội thất 7 chỗ ghế da cao cấp\nMàn hình kỹ thuật số 8-inch & Màn hình giải trí cảm ứng sắc nét\nĐiều hòa tự động 2 vùng độc lập",
                    'colors' => [['name' => 'Trắng', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Đen', 'hex' => '#1a1a1a', 'image' => '']],
                ],
            ],
            'specs' => [
                ['title' => 'Kích thước và trọng lượng', 'rows' => "Số chỗ ngồi | 7 | 7\nKhoảng sáng gầm xe | 214 mm | 214 mm\nDung tích bình nhiên liệu | 50 L"],
                ['title' => 'Động cơ và vận hành', 'rows' => "Loại động cơ | Xăng Turbo 1.5L MIVEC\nCông suất cực đại | 163 PS\nMô-men xoắn cực đại | 250 Nm\nHộp số | Tự động vô cấp CVT\nChế độ lái | 5 chế độ: Đường nhựa, Tiêu chuẩn, Đường ướt, Đường sỏi, Đường bùn lầy"],
                ['title' => 'Nội thất & Tiện nghi', 'rows' => "Chất liệu ghế | Da cao cấp\nMàn hình giải trí | 12.3-inch\nÂm thanh | 6 loa | 8 loa Dynamic Sound Yamaha Premium"],
                ['title' => 'An toàn', 'rows' => "Túi khí | 4 túi khí | 6 túi khí\nHệ thống an toàn chủ động | Diamond Sense"],
            ],
            'highlight' => ['image' => '', 'kicker' => 'ĐẶC ĐIỂM NỔI BẬT', 'title' => 'KHỞI SỰ VẠN ĐIỂM ĐẾN', 'desc' => 'SUV 7 chỗ đa năng, đẳng cấp dành cho gia đình.'],
            'features' => [
                ['image' => '', 'kicker' => 'Ý TƯỞNG THIẾT KẾ', 'title' => 'UY LỰC & NĂNG ĐỘNG', 'desc' => 'Thiết kế SUV tinh tế, đặc trưng.'],
                ['image' => '', 'kicker' => 'KHÔNG GIAN NỘI THẤT', 'title' => '7 CHỖ TIỆN NGHI', 'desc' => 'Nội thất rộng rãi, đẳng cấp, thoải mái cho mọi hành khách.'],
                ['image' => '', 'kicker' => 'ĐỘNG CƠ MẠNH MẼ', 'title' => 'ĐỘNG CƠ XĂNG TURBO 1.5L MIVEC', 'desc' => 'Tăng tốc mạnh mẽ, hiệu quả nhờ hệ thống kim phun xăng kép và làm mát khí nạp bằng dung dịch.'],
                ['image' => '', 'kicker' => 'VẬN HÀNH LINH HOẠT', 'title' => '5 CHẾ ĐỘ LÁI TÙY CHỌN', 'desc' => 'Đường nhựa (Mới), Tiêu chuẩn, Đường ướt, Đường sỏi, Đường bùn lầy.'],
                ['image' => '', 'kicker' => 'AN TOÀN', 'title' => 'DIAMOND SENSE', 'desc' => 'Hệ thống an toàn chủ động thông minh đa chức năng, hỗ trợ tay lái trên mọi hành trình.'],
                ['image' => '', 'kicker' => 'MITSUBISHI CONNECT', 'title' => 'HỆ THỐNG KẾT NỐI THÔNG MINH MITSUBISHI CONNECT', 'desc' => 'Kết nối dễ dàng với chiếc xe của bạn thông qua ứng dụng My MITSUBISHI CONNECT.'],
                ['image' => '', 'kicker' => 'TIỆN NGHI VƯỢT TRỘI', 'title' => 'HỆ THỐNG ÂM THANH DYNAMIC SOUND YAMAHA PREMIUM', 'desc' => 'Cho trải nghiệm âm thanh chân thực, sống động và thăng hoa với 4 chế độ tùy chỉnh.'],
            ],
            'accessories' => [
                'title' => 'PHỤ KIỆN CHÍNH HÃNG', 'desc' => 'Bộ phụ kiện đẳng cấp giúp bạn khẳng định cá tính riêng.', 'image' => '',
                'items' => [
                    ['image' => '', 'name' => 'Ốp cản trước'], ['image' => '', 'name' => 'Ốp cản sau'],
                    ['image' => '', 'name' => 'Bộ chữ Destinator'], ['image' => '', 'name' => 'Ốp lướt gió đuôi xe'],
                    ['image' => '', 'name' => 'Ốp bệ bước chân có đèn LED'], ['image' => '', 'name' => 'Thảm lót khoang hành lý'],
                ],
            ],
            'partner' => ['image' => '', 'kicker' => 'PHỤ KIỆN HỢP TÁC BÊN THỨ 3', 'title' => 'PHỤ KIỆN HỢP TÁC BÊN THỨ 3', 'desc' => 'Kết nối sáng tạo, đa dạng lựa chọn.'],
        ],

        'mitsubishi-attrage' => [
            'versions' => [
                [
                    'name' => 'MT',
                    'desc' => 'Bản số sàn nhập khẩu Thái Lan, tiết kiệm nhiên liệu kỷ lục chỉ từ 5.09L/100km, bán kính quay vòng 4.8m siêu linh hoạt',
                    'image' => '',
                    'equipment' => "Động cơ 1.2L MIVEC siêu tiết kiệm nhiên liệu\nHộp số sàn 5 cấp nhẹ nhàng\nKhoảng sáng gầm xe 170mm cao ráo trong phố\nBán kính quay vòng chỉ 4.8m nhỏ nhất phân khúc\nĐèn phản xạ đa chiều Halogen\nĐiều hòa chỉnh cơ làm mát sâu\nPhanh ABS, EBD, 2 túi khí an toàn",
                    'colors' => [['name' => 'Trắng', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Xám', 'hex' => '#7a7f86', 'image' => ''], ['name' => 'Đỏ', 'hex' => '#cc0000', 'image' => '']],
                ],
                [
                    'name' => 'CVT',
                    'desc' => 'Bản số tự động vô cấp CVT vận hành êm mượt, mâm đúc 15-inch, đèn chiếu sáng Bi-LED thấu kính hiện đại',
                    'image' => '',
                    'equipment' => "Hộp số tự động vô cấp CVT INVECS-III thông minh\nĐèn chiếu sáng trước Bi-LED thấu kính sắc sảo\nMâm đúc hợp kim 15-inch 2 tông màu\nNội thất bọc da cao cấp\nMàn hình giải trí cảm ứng 7-inch Apple CarPlay / Android Auto\nCamera lùi hỗ trợ lùi xe an toàn",
                    'colors' => [['name' => 'Trắng', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Xám', 'hex' => '#7a7f86', 'image' => ''], ['name' => 'Đỏ', 'hex' => '#cc0000', 'image' => '']],
                ],
                [
                    'name' => 'CVT Premium',
                    'desc' => 'Phiên bản đầy đủ trang bị cao cấp nhất: Cân bằng điện tử ASC, Hỗ trợ khởi hành ngang dốc HSA, Cruise Control',
                    'image' => '',
                    'equipment' => "Tất cả trang bị của bản CVT\nHệ thống cân bằng điện tử ASC & Kiểm soát lực kéo TCL\nHệ thống hỗ trợ khởi hành ngang dốc HSA\nHỗ trợ lực phanh khẩn cấp BA\nGa tự động Cruise Control điều khiển trên vô lăng\nCảm biến gạt mưa tự động & Cảm biến bật tắt đèn pha",
                    'colors' => [['name' => 'Trắng', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Xám', 'hex' => '#7a7f86', 'image' => ''], ['name' => 'Đỏ', 'hex' => '#cc0000', 'image' => '']],
                ],
            ],
            'specs' => [
                ['title' => 'Kích thước và trọng lượng', 'rows' => "Số chỗ ngồi | 5 | 5 | 5\nKích thước tổng thể (DxRxC) | 4.305 x 1.670 x 1.515 mm\nChiều dài cơ sở | 2.550 mm\nKhoảng sáng gầm xe | 170 mm\nBán kính quay vòng | 4.8 m\nDung tích khoang hành lý | 450 L"],
                ['title' => 'Động cơ và vận hành', 'rows' => "Loại động cơ | 1.2L MIVEC DOHC 12 van\nCông suất cực đại | 78 PS tại 6.000 vòng/phút\nMô-men xoắn cực đại | 100 Nm tại 4.000 vòng/phút\nHộp số | Số sàn 5 cấp | Vô cấp CVT | Vô cấp CVT\nMức tiêu hao nhiên liệu (ngoài đô thị) | 4.71 L/100km | 4.74 L/100km | 4.74 L/100km"],
                ['title' => 'Ngoại thất & Tiện nghi', 'rows' => "Đèn chiếu sáng trước | Halogen | Bi-LED | Bi-LED\nChất liệu ghế | Nỉ cao cấp | Da | Da\nMàn hình giải trí | Tiêu chuẩn | 7-inch Apple CarPlay / Android Auto | 7-inch Apple CarPlay / Android Auto\nĐiều hòa nhiệt độ | Chỉnh cơ | Tự động | Tự động"],
                ['title' => 'An toàn', 'rows' => "Túi khí | 2 túi khí\nHệ thống phanh | ABS, EBD\nCân bằng điện tử ASC & Khởi hành ngang dốc HSA | — | — | Tiêu chuẩn\nCamera lùi | Không | Có | Có"],
            ],
            'highlight' => ['image' => '', 'kicker' => 'SEDAN NHẬP KHẨU TIẾT KIỆM HÀNG ĐẦU', 'title' => 'MITSUBISHI ATTRAGE', 'desc' => 'Dòng sedan 5 chỗ nhập khẩu nguyên chiếc từ Thái Lan, sở hữu chi phí đầu tư ban đầu hợp lý và khả năng tiết kiệm nhiên liệu kỷ lục.'],
            'features' => [
                ['image' => '', 'kicker' => 'TIẾT KIỆM VƯỢT TRỘI', 'title' => 'TIÊU THỤ CHỈ TỪ 5.09L/100KM', 'desc' => 'Khả năng tiết kiệm nhiên liệu hàng đầu phân khúc đã được chứng minh qua nhiều cuộc thi lái xe sinh thái thực tế.'],
                ['image' => '', 'kicker' => 'LINH HOẠT TRONG PHỐ', 'title' => 'BÁN KÍNH QUAY VÒNG CHỈ 4.8M', 'desc' => 'Dễ dàng xoay xở, quay đầu và đỗ xe trong những con ngõ nhỏ và phố xá đông đúc tại các đô thị Việt Nam.'],
                ['image' => '', 'kicker' => 'CỐP XE RỘNG RÃI', 'title' => 'DUNG TÍCH KHOANG HÀNH LÝ 450L', 'desc' => 'Khoang hành lý rộng rãi đáp ứng hoàn hảo nhu cầu chở đồ đạc cho cả gia đình trong các chuyến đi xa.'],
            ],
            'accessories' => [
                'title' => 'PHỤ KIỆN CHÍNH HÃNG ATTRAGE', 'desc' => 'Gói phụ kiện trang trí thể thao và tiện ích.', 'image' => '',
                'items' => [
                    ['image' => '', 'name' => 'Ốp lướt gió đuôi xe'], ['image' => '', 'name' => 'Vè che mưa chính hãng'],
                    ['image' => '', 'name' => 'Bộ viền chân kính inox'], ['image' => '', 'name' => 'Thảm lót sàn cao cấp'],
                ],
            ],
            'partner' => ['image' => '', 'kicker' => 'NÂNG CẤP TIỆN NGHI', 'title' => 'PHỤ KIỆN CÔNG NGHỆ BÊN THỨ 3', 'desc' => 'Camera hành trình, cảm biến áp suất lốp, dán phim cách nhiệt.'],
        ],

        'mitsubishi-pajero-sport' => [
            'versions' => [
                [
                    'name' => 'Diesel 4×2 AT',
                    'desc' => 'Bản 1 cầu máy dầu 2.4L MIVEC công nghệ van biến thiên điện tử, hộp số 8 cấp mượt mà, nội thất 7 chỗ da cao cấp',
                    'image' => '',
                    'equipment' => "Động cơ Diesel 2.4L MIVEC công suất 181 PS, mô-men xoắn 430 Nm\nHộp số tự động 8 cấp tích hợp lẫy chuyển số thể thao trên vô lăng\nCụm đèn chiếu sáng Bi-LED thấu kính với chức năng tự động bật/tắt\nCửa cốp đóng mở điện thông minh rảnh tay (đá cốp 2 bên)\nGhế da cao cấp chỉnh điện 8 hướng cho cả ghế lái và ghế phụ\nBảng đồng hồ kỹ thuật số 8-inch 3 chế độ hiển thị\nMàn hình giải trí 8-inch kết nối Apple CarPlay & Android Auto\nĐiều hòa tự động 2 vùng độc lập có công nghệ lọc nanoe™ X\n7 túi khí an toàn toàn diện",
                    'colors' => [['name' => 'Trắng', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Đen', 'hex' => '#1a1a1a', 'image' => ''], ['name' => 'Nâu', 'hex' => '#6b4c35', 'image' => '']],
                ],
                [
                    'name' => 'Diesel 4×4 AT',
                    'desc' => 'Phiên bản 2 cầu đỉnh cao với hệ dẫn động Super Select 4WD-II trứ danh, khóa vi sai cầu sau, gói an toàn chủ động cao cấp e-Assist',
                    'image' => '',
                    'equipment' => "Hệ dẫn động 4 bánh Super Select 4WD-II với 4 chế độ gài cầu linh hoạt & khóa vi sai cầu sau\n4 chế độ lái địa hình chuyên nghiệp: Sỏi, Bùn, Cát, Đá (Off-road Mode)\nGói an toàn chủ động thông minh e-Assist:\n- Hệ thống cảnh báo và giảm thiểu va chạm phía trước (FCM)\n- Hệ thống kiểm soát hành trình thích ứng (ACC)\n- Hệ thống cảnh báo điểm mù (BSW) & Hỗ trợ chuyển làn (LCA)\n- Hệ thống cảnh báo phương tiện cắt ngang khi lùi (RCTA)\n- Hệ thống chống tăng tốc ngoài ý muốn (UMS)\nCamera toàn cảnh 360 độ siêu nét\nỨng dụng điều khiển xe từ xa qua điện thoại thông minh MITSUBISHI REMOTE CONTROL",
                    'colors' => [['name' => 'Trắng', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Đen', 'hex' => '#1a1a1a', 'image' => ''], ['name' => 'Nâu', 'hex' => '#6b4c35', 'image' => '']],
                ],
            ],
            'specs' => [
                ['title' => 'Kích thước và trọng lượng', 'rows' => "Số chỗ ngồi | 7 | 7\nKích thước tổng thể (DxRxC) | 4.825 x 1.815 x 1.835 mm\nChiều dài cơ sở | 2.800 mm\nKhoảng sáng gầm xe | 218 mm\nBán kính quay vòng | 5.6 m\nDung tích bình nhiên liệu | 68 L"],
                ['title' => 'Động cơ và vận hành', 'rows' => "Loại động cơ | 2.4L Diesel MIVEC Turbo\nCông suất cực đại | 181 PS tại 3.500 vòng/phút\nMô-men xoắn cực đại | 430 Nm tại 2.500 vòng/phút\nHộp số | Tự động 8 cấp có lẫy chuyển số\nHệ dẫn động | 1 cầu sau (2WD) | 2 cầu Super Select 4WD-II với khóa vi sai\nChế độ off-road | — | 4 chế độ: Sỏi, Bùn, Cát, Đá"],
                ['title' => 'An toàn', 'rows' => "Túi khí an toàn | 7 túi khí\nGói an toàn chủ động e-Assist | Cơ bản | Tiêu chuẩn đầy đủ (FCM, ACC, BSW, LCA, RCTA, UMS)\nCamera | Camera lùi | Camera toàn cảnh 360"],
            ],
            'highlight' => ['image' => '', 'kicker' => 'SUV CỠ D UY LỰC ĐÍCH THỰC', 'title' => 'MITSUBISHI PAJERO SPORT', 'desc' => 'Mẫu SUV 7 chỗ khung gầm rời đích thực, kế thừa ADN đường đua Dakar Rally với hệ dẫn động Super Select 4WD-II lừng danh và tiện nghi cao cấp.'],
            'features' => [
                ['image' => '', 'kicker' => 'DẪN ĐỘNG TRỨ DANH', 'title' => 'SUPER SELECT 4WD-II & KHÓA VI SAI CẦU SAU', 'desc' => 'Cho phép chạy 2 cầu nhanh (4H) toàn thời gian trên cả đường nhựa như xe AWD, vượt địa hình hiểm trở dễ dàng.'],
                ['image' => '', 'kicker' => 'AN TOÀN HÀNG ĐẦU', 'title' => 'HỆ THỐNG AN TOÀN CHỦ ĐỘNG E-ASSIST', 'desc' => 'Bảo vệ toàn diện cho cả gia đình với hệ thống radar và camera thông minh phát hiện nguy cơ va chạm từ sớm.'],
                ['image' => '', 'kicker' => 'CÔNG NGHỆ THÔNG MINH', 'title' => 'ĐIỀU KHIỂN XE QUA ĐIỆN THOẠI THÔNG MINH', 'desc' => 'Mở cốp xe tự động khi đến gần, tìm xe trong bãi đỗ, kiểm tra tình trạng xe từ xa qua ứng dụng Mitsubishi Remote Control.'],
            ],
            'accessories' => [
                'title' => 'PHỤ KIỆN CHÍNH HÃNG PAJERO SPORT', 'desc' => 'Bộ phụ kiện thể thao phong cách uy phong.', 'image' => '',
                'items' => [
                    ['image' => '', 'name' => 'Ốp cản trước'], ['image' => '', 'name' => 'Ốp cản sau'],
                    ['image' => '', 'name' => 'Bộ chữ nổi nắp capo PAJERO SPORT'], ['image' => '', 'name' => 'Ốp cánh lướt gió đuôi xe'],
                ],
            ],
            'partner' => ['image' => '', 'kicker' => 'OFF-ROAD CHUYÊN NGHIỆP', 'title' => 'PHỤ KIỆN OFF-ROAD BÊN THỨ 3', 'desc' => 'Cản sắt chuyên dụng, tời điện cứu hộ, lốp địa hình gai to.'],
        ],

        'mitsubishi-triton' => [
            'versions' => [
                [
                    'name' => '4×2 GLX',
                    'desc' => 'Bản 1 cầu số tự động đa dụng, bền bỉ, tiết kiệm nhiên liệu, khung gầm MEGA FRAME thế hệ mới vững chãi',
                    'image' => '',
                    'equipment' => "Động cơ Diesel 2.4L MIVEC thế hệ mới mạnh mẽ\nHộp số tự động 6 cấp vận hành êm ái\nKhung gầm MEGA FRAME thép siêu cường tăng độ cứng vững\nKhoảng sáng gầm xe 222mm lý tưởng\nĐèn chiếu sáng LED thấu kính tự động bật tắt\nMàn hình giải trí cảm ứng 8-inch kết nối không dây Apple CarPlay / Android Auto\nBảng đồng hồ đa thông tin 7-inch\n3 túi khí an toàn tiêu chuẩn",
                    'colors' => [['name' => 'Trắng', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Đen', 'hex' => '#1a1a1a', 'image' => ''], ['name' => 'Xám', 'hex' => '#7a7f86', 'image' => '']],
                ],
                [
                    'name' => '4×2 Premium',
                    'desc' => 'Nâng cấp nội thất da cao cấp, sạc không dây, điều hòa tự động 2 vùng, mâm xe 18-inch hợp kim thể thao',
                    'image' => '',
                    'equipment' => "Tất cả trang bị của bản GLX\nMâm xe hợp kim 18-inch phay bóng thể thao\nGhế da cao cấp, ghế lái chỉnh điện 8 hướng\nĐiều hòa tự động 2 vùng độc lập\nSạc điện thoại không dây chuẩn Qi\nCảnh báo điểm mù BSW & Cảnh báo phương tiện cắt ngang RCTA\nCảm biến hỗ trợ đỗ xe trước & sau",
                    'colors' => [['name' => 'Trắng', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Đen', 'hex' => '#1a1a1a', 'image' => ''], ['name' => 'Xám', 'hex' => '#7a7f86', 'image' => ''], ['name' => 'Cam', 'hex' => '#e65c00', 'image' => '']],
                ],
                [
                    'name' => '4×4 Athlete',
                    'desc' => 'Phiên bản bán tải thể thao cao cấp nhất với động cơ Bi-Turbo 204 PS, 2 cầu Super Select 4WD-II, 7 chế độ lái và gói an toàn ADAS',
                    'image' => '',
                    'equipment' => "Động cơ 2.4L Bi-Turbo kép công suất vượt trội 204 PS, mô-men xoắn cực đại 470 Nm\nHệ dẫn động 4 bánh Super Select 4WD-II với khóa vi sai cầu sau\n7 chế độ lái thích ứng địa hình thông minh\nHệ thống an toàn chủ động ADAS toàn diện (FCM, ACC, BSW, LCA, RCTA, AHB)\nHệ thống vào cua chủ động AYC (Active Yaw Control)\nBodykit thể thao Athlete độc quyền màu cam - đen\n7 túi khí an toàn tối đa\nCamera toàn cảnh 360 độ",
                    'colors' => [['name' => 'Cam Athlete', 'hex' => '#e65c00', 'image' => ''], ['name' => 'Trắng Diamond', 'hex' => '#f5f5f5', 'image' => ''], ['name' => 'Đen', 'hex' => '#1a1a1a', 'image' => '']],
                ],
            ],
            'specs' => [
                ['title' => 'Kích thước và trọng lượng', 'rows' => "Số chỗ ngồi | 5 | 5 | 5\nKích thước tổng thể (DxRxC) | 5.360 x 1.930 x 1.815 mm\nChiều dài cơ sở | 3.130 mm\nKhoảng sáng gầm xe | 222 mm | 222 mm | 228 mm\nKích thước thùng xe (DxRxC) | 1.555 x 1.545 x 525 mm"],
                ['title' => 'Động cơ và vận hành', 'rows' => "Loại động cơ | 2.4L Diesel MIVEC | 2.4L Diesel MIVEC | 2.4L Diesel Bi-Turbo kép\nCông suất cực đại | 184 PS | 184 PS | 204 PS tại 3.500 vòng/phút\nMô-men xoắn cực đại | 430 Nm | 430 Nm | 470 Nm tại 1.500 - 2.750 vòng/phút\nHộp số | Tự động 6 cấp\nHệ dẫn động | 1 cầu sau (2WD) | 1 cầu sau (2WD) | 2 cầu Super Select 4WD-II (4WD)\nChế độ lái địa hình | — | — | 7 chế độ lái"],
                ['title' => 'An toàn', 'rows' => "Túi khí | 3 túi khí | 3 túi khí | 7 túi khí\nGói an toàn chủ động ADAS | — | — | Tiêu chuẩn đầy đủ\nCảnh báo điểm mù BSW & RCTA | — | Có | Có\nHệ thống vào cua chủ động AYC | Có | Có | Có"],
            ],
            'highlight' => ['image' => '', 'kicker' => 'UY MÃNH VƯỢT THỜI GIAN', 'title' => 'MITSUBISHI ALL-NEW TRITON', 'desc' => 'Thế hệ bán tải hoàn toàn mới với khung gầm MEGA FRAME vững chắc, động cơ Bi-Turbo uy lực cùng diện mạo Beast Mode hầm hố.'],
            'features' => [
                ['image' => '', 'kicker' => 'KHUNG GẦM ĐỘT PHÁ', 'title' => 'KHUNG GẦM MEGA FRAME THẾ HỆ MỚI', 'desc' => 'Tăng 65% độ cứng chống uốn và 40% độ cứng chống xoắn, mang lại sự đầm chắc, vững vàng trên mọi địa hình hiểm trở.'],
                ['image' => '', 'kicker' => 'SỨC MẠNH VƯỢT TRỘI', 'title' => 'ĐỘNG CƠ 2.4L DIESEL BI-TURBO 204 PS', 'desc' => 'Hệ thống turbo tăng áp kép mang lại sức kéo 470 Nm ngay từ dải tua thấp 1.500 vòng/phút, tăng tốc mượt mà và tiết kiệm nhiên liệu.'],
                ['image' => '', 'kicker' => 'CHINH PHỤC MỌI ĐỊA HÌNH', 'title' => 'SUPER SELECT 4WD-II VỚI 7 CHẾ ĐỘ LÁI', 'desc' => 'Chuyển cầu linh hoạt ở tốc độ lên tới 100 km/h, 7 chế độ lái địa hình thích ứng hoàn hảo với bùn lầy, cát, tuyết, đá.'],
            ],
            'accessories' => [
                'title' => 'PHỤ KIỆN BÁN TẢI CHÍNH HÃNG', 'desc' => 'Các trang bị thùng xe và bảo vệ cao cấp.', 'image' => '',
                'items' => [
                    ['image' => '', 'name' => 'Nắp thùng cuộn điện'], ['image' => '', 'name' => 'Thanh thể thao bán tải'],
                    ['image' => '', 'name' => 'Lót thùng chính hãng'], ['image' => '', 'name' => 'Bộ viền cua lốp đen nhám'],
                    ['image' => '', 'name' => 'Ốp viền đèn trước sau'], ['image' => '', 'name' => 'Bộ chữ nổi TRITON'],
                ],
            ],
            'partner' => ['image' => '', 'kicker' => 'PHỤ KIỆN THÙNG XE', 'title' => 'NẮP THÙNG & GIÁ NÓC BÊN THỨ 3', 'desc' => 'Nắp thùng cao canopy, nắp thùng thấp thể thao, thanh giá nóc dã ngoại.'],
        ],
    ];

    foreach ($seeds as $slug => $data) {
        q("UPDATE cars SET detail = ? WHERE slug = ? AND (detail IS NULL OR detail = '' OR detail = '{}')", [
            json_encode($data, JSON_UNESCAPED_UNICODE),
            $slug,
        ]);
    }
}

