<?php
/** @var array $post */
$u = full_url('tin-tuc/' . $post['slug'] . '/');
$content = render_content($post['content']);
$toc = [];
$content = preg_replace_callback('~<h2([^>]*)>(.*?)</h2>~s', function ($m) use (&$toc) {
    $text = trim(html_entity_decode(strip_tags($m[2])));
    if (preg_match('~id="([^"]+)"~', $m[1], $id)) { $a = $id[1]; $attrs = $m[1]; }
    else { $a = slugify($text) ?: 'muc-' . (count($toc) + 1); $attrs = $m[1] . ' id="' . e($a) . '"'; }
    $toc[] = [$a, preg_replace('/^\d+\.\s*/', '', $text)];
    return '<h2' . $attrs . '>' . $m[2] . '</h2>';
}, $content);
if (count($toc) >= 3) {
    $nav = '<nav class="toc" aria-label="Mục lục"><div class="toc__title">Nội dung bài viết</div><ol>';
    foreach ($toc as $t) { $nav .= '<li><a href="#' . e($t[0]) . '">' . e($t[1]) . '</a></li>'; }
    $nav .= '</ol></nav>';
    $pos = strpos($content, '</p>');
    $content = $pos !== false ? substr_replace($content, '</p>' . $nav, $pos, 4) : $nav . $content;
}
$img = $post['image'] ?: 'assets/img/bg-tintuc.webp';
$META = [
    'title' => $post['title'] . ' | Mitsubishi ' . opt('consultant_name'),
    'desc' => $post['excerpt'] ?: mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags($post['content']))), 0, 155),
    'canonical' => $u, 'type' => 'article', 'image' => media_full($img),
    'schema' => [
        ['@type' => 'BlogPosting', '@id' => $u . '#bai-viet', 'headline' => $post['title'], 'description' => $post['excerpt'], 'image' => media_full($img), 'datePublished' => date('c', strtotime($post['created_at'])), 'dateModified' => date('c', strtotime($post['updated_at'] ?: $post['created_at'])), 'inLanguage' => 'vi-VN', 'author' => ['@id' => full_url() . '#tu-van-vien'], 'publisher' => ['@id' => full_url() . '#dealer'], 'mainEntityOfPage' => $u],
        schema_person(), schema_dealer(),
        breadcrumb([['Tin tức', full_url(page_map()['tin-tuc'][0])], [$post['title'], $u]]),
    ],
];
include __DIR__ . '/partials/header.php';
$ph = opt('phone');
?>
<section class="hero hero--post" style="background-image:linear-gradient(90deg,rgba(0,0,0,.8) 0%,rgba(0,0,0,.55) 50%,rgba(0,0,0,.2) 85%),url('<?= eu(media($img)) ?>')">
	<div class="container hero__inner">
		<?php if ($post['category']) : ?><a class="hero__kicker" href="<?= e(page_url('tin-tuc')) ?>?cm=<?= e(rawurlencode($post['category'])) ?>"><?= e(upper($post['category'])) ?></a><?php endif; ?>
		<h1 class="hero__title hero__title--post"><?= e($post['title']) ?></h1>
		<div class="byline">
			<img src="<?= eu(media(opt('consultant_photo'))) ?>" alt="<?= e(opt('consultant_name')) ?>" width="44" height="44" loading="lazy" decoding="async">
			<span>Bởi <a href="<?= e(page_url('tac-gia')) ?>"><?= e(opt('consultant_name')) ?></a> · <?= e(opt('consultant_title')) ?></span>
			<span>Đăng ngày <time datetime="<?= e(substr($post['created_at'], 0, 10)) ?>"><?= e(vn_date($post['created_at'])) ?></time> · Cập nhật <time datetime="<?= e(substr((string) ($post['updated_at'] ?: $post['created_at']), 0, 10)) ?>"><?= e(vn_date($post['updated_at'] ?: $post['created_at'])) ?></time></span>
		</div>
	</div>
</section>
<article class="container container--text section"><div class="entry"><?= $content ?></div></article>
<section class="author-box-wrap">
	<div class="container container--text author-box">
		<img src="<?= eu(media(opt('consultant_photo'))) ?>" alt="<?= e(opt('consultant_name')) ?>" width="96" height="96" loading="lazy" decoding="async">
		<div>
			<div class="kicker">VỀ TÁC GIẢ</div>
			<a class="author-box__name" href="<?= e(page_url('tac-gia')) ?>"><?= e(opt('consultant_name')) ?></a>
			<p><?= e(opt('consultant_title')) ?> tại <?= e(opt('company_short')) ?>. <?= e(opt('stat_years')) ?> kinh nghiệm, <?= e(opt('stat_customers')) ?> <?= e(opt('stat_customers_label')) ?>.</p>
			<div class="btn-row">
				<a class="btn btn--red btn--sm" href="tel:<?= e(tel($ph)) ?>">GỌI <?= e($ph) ?></a>
				<a class="btn btn--zalo btn--sm" href="https://zalo.me/<?= e(tel(opt('zalo'))) ?>" target="_blank" rel="noopener">CHAT ZALO</a>
			</div>
		</div>
	</div>
</section>
<?php $related = rows("SELECT * FROM posts WHERE status = 'publish' AND id <> ? ORDER BY created_at DESC LIMIT 3", [$post['id']]); if ($related) : ?>
<section class="container section">
	<h2 class="h-red">BÀI VIẾT KHÁC</h2>
	<div class="post-grid"><?php foreach ($related as $p) { partial('post-card', ['p' => $p]); } ?></div>
</section>
<?php endif; ?>
<?php include __DIR__ . '/partials/footer.php';
