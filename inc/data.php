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
        db_save('deliveries', ['image' => 'assets/img/giaoxe-' . $p[0] . '.jpg', 'car' => $p[1], 'cust_type' => $p[2], 'msg_by' => 'consultant', 'message' => $p[3], 'cust_name' => '', 'ord' => $i + 1, 'active' => 1]);
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
        'tac-gia'    => ['Nguyễn Tiến Thành', '', '', '', 'Nguyễn Tiến Thành – chuyên viên tư vấn bán hàng Mitsubishi tại Công ty CP Ô tô Hà Nội. 4 năm kinh nghiệm, gần 120 khách hàng đã nhận xe.'],
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
<figure><img src="assets/img/xuong-dichvu.jpg" alt="Xưởng dịch vụ Mitsubishi – Công ty CP Ô tô Hà Nội"><figcaption>Xưởng dịch vụ tại Công ty CP Ô tô Hà Nội</figcaption></figure>
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
<p>Sau 4 năm tư vấn và gần 120 lần bàn giao xe tại showroom, tôi nhận ra phần lớn băn khoăn của khách mua xe lần đầu đều giống nhau: <strong>tổng cộng phải trả bao nhiêu, vay thế nào cho an toàn, và ký giấy tờ gì để không bị thiệt</strong>. Dưới đây là 7 điều tôi luôn dặn trước khi khách xuống tiền.</p>
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
