<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    if (!empty($_POST['csrf_token']) && verifyCsrf($_POST['csrf_token'])) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $stmt = db()->prepare('INSERT INTO newsletter_subscribers (email) VALUES (:email) ON DUPLICATE KEY UPDATE status = 1');
            $stmt->execute([':email' => $email]);
            setFlash('Thank you for subscribing to NURIN updates.', 'success');
        } else {
            setFlash('Please enter a valid email address.', 'danger');
        }
    } else {
        setFlash('Security validation failed. Please try again.', 'danger');
    }
    redirect('index.php');
}

$heroSlides = db()->query('SELECT * FROM hero_slides WHERE status = 1 ORDER BY sort_order ASC')->fetchAll();
$stats = db()->query('SELECT * FROM statistics WHERE status = 1 ORDER BY sort_order ASC')->fetchAll();
$programs = db()->query('SELECT * FROM programs WHERE status = 1 ORDER BY sort_order ASC LIMIT 4')->fetchAll();
$projects = db()->query('SELECT * FROM projects WHERE status = 1 ORDER BY id DESC LIMIT 3')->fetchAll();
$news = db()->query("
    (SELECT id, title, excerpt, image_path, category, published_at AS item_date, 'news' AS item_type
     FROM news WHERE status = 1)
    UNION ALL
    (SELECT id, title, excerpt, image_path, 'Event' AS category, event_date AS item_date, 'event' AS item_type
     FROM events WHERE status = 1)
    ORDER BY item_date DESC
    LIMIT 3
")->fetchAll();
$partners = db()->query('SELECT * FROM partners WHERE status = 1 ORDER BY sort_order ASC')->fetchAll();

if (!$heroSlides) {
    $heroSlides = [[
        'title' => 'Building Resilient Communities through Science, Innovation & Technology',
        'subtitle' => 'Empowering young people and communities in Northern Uganda through science, innovation and technology for a sustainable future.',
        'image_path' => 'includes/ncc4.jpg',
        'button_text' => 'Discover Our Programs',
        'button_link' => 'programs.php',
    ]];
}

include __DIR__ . '/includes/header.php';
$flash = getFlash();
?>
<?php if ($flash): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= e($flash['type']) ?>" role="alert"><?= e($flash['message']) ?></div>
    </div>
<?php endif; ?>

<section class="hero-section">
    <div id="homeHeroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
        <?php if (count($heroSlides) > 1): ?>
            <div class="carousel-indicators">
                <?php foreach ($heroSlides as $index => $slide): ?>
                    <button type="button" data-bs-target="#homeHeroCarousel" data-bs-slide-to="<?= (int)$index ?>" class="<?= $index === 0 ? 'active' : '' ?>" <?= $index === 0 ? 'aria-current="true"' : '' ?> aria-label="Slide <?= (int)($index + 1) ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="carousel-inner">
            <?php foreach ($heroSlides as $index => $slide): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" style="background-image: url('<?= e(getImagePath($slide['image_path'] ?? null, 'includes/ncc4.jpg')) ?>')">
                    <div class="hero-slide-overlay"></div>
                    <div class="container hero-inner">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-8 hero-copy">
                                <span class="eyebrow">NURIN | Northern Uganda Resilience Innovation Network</span>
                                <h1><?= nl2br(e($slide['title'])) ?></h1>
                                <?php if (!empty($slide['subtitle'])): ?><p><?= e($slide['subtitle']) ?></p><?php endif; ?>
                                <div class="hero-actions">
                                    <?php if (!empty($slide['button_text']) && !empty($slide['button_link'])): ?>
                                        <a href="<?= e($slide['button_link']) ?>" class="btn btn-primary"><?= e($slide['button_text']) ?> <i class="fa-solid fa-arrow-right ms-2"></i></a>
                                    <?php endif; ?>
                                    <a href="about.php" class="btn btn-outline-light">Learn More About NURIN</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($heroSlides) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="prev" aria-label="Previous slide">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="next" aria-label="Next slide">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
        <?php endif; ?>
    </div>
</section>

<section class="feature-cards">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-robot"></i></div>
                    <h3>Robotics &amp; AI</h3>
                    <p>Promoting robotics, artificial intelligence and emerging technologies for community transformation.</p>
                    <a href="programs.php">Learn More <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-people-group"></i></div>
                    <h3>Community Resilience</h3>
                    <p>Strengthening communities to adapt, recover and build sustainable livelihoods.</p>
                    <a href="programs.php">Learn More <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-laptop-code"></i></div>
                    <h3>Digital Skills</h3>
                    <p>Equipping youth and communities with practical digital skills for education, employability and entrepreneurship.</p>
                    <a href="programs.php">Learn More <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-network-wired"></i></div>
                    <h3>Networking &amp; Partnerships</h3>
                    <p>Connecting innovators, communities, organizations and partners for greater impact.</p>
                    <a href="programs.php">Learn More <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="stats-section">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($stats as $stat): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="<?= e($stat['icon']) ?>"></i></div>
                        <div class="stat-value count-up" data-target="<?= (int)$stat['value'] ?>" data-suffix="<?= e($stat['suffix']) ?>">0</div>
                        <div class="stat-label"><?= e($stat['label']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="program-layout">
            <div class="program-copy">
                <div class="section-head">
                    <span class="eyebrow-title">What We Do</span>
                    <h2 class="section-title">Programs That Create Impact</h2>
                </div>
                <p class="section-desc">NURIN implements programs that empower communities, promote innovation, build digital capacity and connect people for a sustainable Northern Uganda.</p>
                <a href="programs.php" class="btn btn-primary mt-3">Explore All Programs <i class="fa-solid fa-arrow-right ms-2"></i></a>
            </div>
            <div class="program-list">
                <?php foreach ($programs as $program): ?>
                    <article class="program-card">
                        <img src="<?= e(getImagePath($program['image_path'])) ?>" alt="<?= e($program['title']) ?>">
                        <div class="program-card-body">
                            <div class="meta-row"><span class="badge badge-green">ID #<?= (int)$program['id'] ?></span><span><i class="fa-solid fa-arrow-right"></i></span></div>
                            <h3><?= e($program['title']) ?></h3>
                            <p><?= e($program['short_description']) ?></p>
                            <a href="program-details.php?id=<?= (int)$program['id'] ?>" class="read-more">Read More</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="section bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <span class="eyebrow-title">Latest Projects</span>
                <h2 class="section-title">Latest Projects</h2>
            </div>
            <a href="projects.php" class="btn btn-primary">View All Projects <i class="fa-solid fa-arrow-right ms-2"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach ($projects as $project): ?>
                <div class="col-lg-4 col-md-6">
                    <article class="project-card">
                        <img src="<?= e(getImagePath($project['image_path'])) ?>" alt="<?= e($project['title']) ?>">
                        <div class="project-card-body">
                            <div class="meta-row">
                                <span class="badge badge-green"><?= e($project['category']) ?></span>
                                <span><?= e(formatDate($project['project_date'])) ?></span>
                            </div>
                            <h3><?= e($project['title']) ?></h3>
                            <p><?= e($project['short_description']) ?></p>
                            <a class="btn-link" href="project-details.php?id=<?= (int)$project['id'] ?>">Read More</a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="news-wrap">
            <div>
                <span class="eyebrow-title">News &amp; Events</span>
                <h2 class="section-title mb-4">News &amp; Events</h2>
                <div class="news-stack">
                    <?php foreach ($news as $item): ?>
                        <article class="news-item">
                            <?php if (!empty($item['image_path'])): ?>
                                <img src="<?= e(getImagePath($item['image_path'])) ?>" alt="<?= e($item['title']) ?>">
                            <?php endif; ?>
                            <div class="news-item-body">
                                <div class="meta-row">
                                    <span class="badge badge-green"><?= e($item['category']) ?></span>
                                    <span><?= e(formatDate($item['item_date'])) ?></span>
                                </div>
                                <h3><?= e($item['title']) ?></h3>
                                <p><?= e($item['excerpt']) ?></p>
                                <?php if ($item['item_type'] === 'news'): ?>
                                    <a href="news-details.php?id=<?= (int)$item['id'] ?>" class="btn-link">Read More</a>
                                <?php else: ?>
                                    <a href="events.php#event-<?= (int)$item['id'] ?>" class="btn-link">View Event</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="content-card">
                <span class="eyebrow-title">Our Focus</span>
                <h3>Science, technology and entrepreneurship for resilient communities.</h3>
                <p class="mb-3">We support scientific thinking, innovation and business skills that help communities solve local challenges and build sustainable futures.</p>
                <ul class="footer-links">
                    <li><i class="fa-solid fa-check text-success me-2"></i> Science &amp; problem solving</li>
                    <li><i class="fa-solid fa-check text-success me-2"></i> Technology &amp; AI</li>
                    <li><i class="fa-solid fa-check text-success me-2"></i> Innovation &amp; entrepreneurship</li>
                    <li><i class="fa-solid fa-check text-success me-2"></i> Community resilience</li>
                </ul>
                <a href="about.php" class="btn btn-primary mt-4">Learn About Us</a>
            </div>
        </div>
    </div>
</section>

<section class="partner-section">
    <div class="container">
        <div class="text-center mb-4">
            <span class="eyebrow-title">Our Partners</span>
            <h2 class="section-title">Our Partners</h2>
        </div>
        <div class="partner-marquee" aria-label="Partner logos marquee">
            <div class="partner-marquee-track">
                <?php foreach (array_merge($partners, $partners) as $partner): ?>
                    <div class="partner-logo-item<?= $partner['name'] === 'netLab!UG' ? ' partner-logo-item-netlab' : '' ?>">
                        <?php $partnerLogo = !empty($partner['logo_path']) && file_exists(__DIR__ . '/' . $partner['logo_path']) ? $partner['logo_path'] : ''; ?>
                        <?php if ($partnerLogo !== ''): ?>
                            <img src="<?= e($partnerLogo) ?>" alt="<?= e($partner['name']) ?> logo" class="partner-logo-image">
                            <?php if ($partner['name'] === 'netLab!UG'): ?>
                                <span class="partner-logo-caption">netLab!UG</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span><?= e($partner['name']) ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
