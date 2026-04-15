<?php
define('SITE_NAME', 'VOGUE BR');
define('SITE_TAGLINE', 'Fashion · Cultura · Estilo');
define('DATA_DIR', __DIR__ . '/../data/');

// ─── Flat-file "database" via JSON ────────────────────────────
function db_read(string $table): array {
    $file = DATA_DIR . $table . '.json';
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?? [];
}

function db_write(string $table, array $data): void {
    if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
    file_put_contents(DATA_DIR . $table . '.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function db_next_id(array $rows): int {
    return $rows ? max(array_column($rows, 'id')) + 1 : 1;
}

// ─── Session helpers ──────────────────────────────────────────
function session_start_safe(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

function current_user(): ?array {
    session_start_safe();
    return $_SESSION['user'] ?? null;
}

function require_login(): void {
    if (!current_user()) { header('Location: /index.php'); exit; }
}

function require_admin(): void {
    $u = current_user();
    if (!$u || $u['role'] !== 'admin') { header('Location: /dashboard.php'); exit; }
}

function is_admin(): bool {
    $u = current_user();
    return $u && $u['role'] === 'admin';
}

// ─── Seed initial data ────────────────────────────────────────
function seed_data(): void {
    if (!file_exists(DATA_DIR . 'users.json')) {
        db_write('users', [
            ['id'=>1,'name'=>'Admin Geral','email'=>'admin@vogue.com','pass'=>password_hash('admin123',PASSWORD_DEFAULT),'role'=>'admin','bio'=>'Administrador do portal','created_at'=>'2024-01-01'],
            ['id'=>2,'name'=>'Ana Clara','email'=>'ana@gmail.com','pass'=>password_hash('ana123',PASSWORD_DEFAULT),'role'=>'user','bio'=>'Apaixonada por moda e cultura','created_at'=>'2024-02-15'],
            ['id'=>3,'name'=>'Beatriz Lima','email'=>'bea@gmail.com','pass'=>password_hash('bea123',PASSWORD_DEFAULT),'role'=>'user','bio'=>'Editora de beleza','created_at'=>'2024-03-10'],
        ]);
    }
    if (!file_exists(DATA_DIR . 'news.json')) {
        db_write('news', [
            ['id'=>1,'title'=>'Semana de Moda de Paris 2025: Os Looks que Dominaram as Passarelas','category'=>'Moda','status'=>'published','excerpt'=>'As tendências que vêm direto de Paris, a capital mundial da moda, com tudo o que você precisa saber.','content'=>'A Semana de Moda de Paris 2025 foi uma verdadeira celebração da elegância e inovação. Designers apresentaram coleções que misturaram tradição e vanguarda de forma magistral. Os destaque ficaram para Valentino, com sua linha monocromática em vermelho intenso, e para a Dior, que apostou em silhuetas fluidas e bordados elaborados. O street style parisiense também roubou a cena, com influenciadores de todo o mundo exibindo looks ousados nas ruas da Cidade Luz.','image'=>'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=800&q=80','author_id'=>1,'created_at'=>'2025-03-15'],
            ['id'=>2,'title'=>'Tendências de Beleza para o Verão: Pele Iluminada é a Grande Aposta','category'=>'Beleza','status'=>'published','excerpt'=>'Os cosméticos e técnicas que prometem dominar o verão brasileiro com looks naturais e radiantes.','content'=>'O verão 2025 chega com uma promessa clara: pele iluminada e natural. As marcas de beleza apostam em bases leves de alta cobertura, glosses em tons nude e blushes em pêssego e terracota. O skincare ganhou protagonismo, com séricos vitamina C e protetor solar colorido como itens indispensáveis. O maquiagem "clean girl" continua em alta, com sobrancelhas bem definidas e pele bem cuidada sendo o centro das atenções.','image'=>'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&q=80','author_id'=>2,'created_at'=>'2025-03-10'],
            ['id'=>3,'title'=>'Minimalismo Chic: Como Montar um Armário Cápsula Perfeito','category'=>'Lifestyle','status'=>'published','excerpt'=>'Menos é mais quando se trata de moda consciente. Aprenda a criar combinações infinitas com poucas peças.','content'=>'O conceito de armário cápsula chegou para ficar. Com apenas 30 peças bem escolhidas, é possível criar mais de 100 combinações diferentes para o dia a dia. O segredo está na escolha de cores neutras — branco, preto, bege e camel — aliadas a cortes clássicos e tecidos de qualidade. Peças-chave incluem: uma calça alfaiataria bem cortada, uma camisa branca estruturada, um blazer oversized neutro e um vestido midi versátil.','image'=>'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=800&q=80','author_id'=>1,'created_at'=>'2025-03-05'],
            ['id'=>4,'title'=>'Entrevista Exclusiva: A Estilista Brasileira que Conquista Paris','category'=>'Entrevistas','status'=>'published','excerpt'=>'Conheça a trajetória inspiradora de Carol Bassi, fenômeno da moda nacional nos palcos internacionais.','content'=>'Em entrevista exclusiva ao Vogue BR, Carol Bassi fala sobre suas inspirações, desafios de ser uma mulher brasileira no mercado internacional de moda e o futuro da moda sustentável. "A moda brasileira tem uma identidade única — cores, texturas, a alegria do nosso povo — e é isso que levo para fora", afirma ela. Com coleções vendidas em mais de 20 países, a estilista prova que talento brasileiro tem lugar garantido nos maiores centros de moda do mundo.','image'=>'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=800&q=80','author_id'=>1,'created_at'=>'2025-02-28'],
            ['id'=>5,'title'=>'SPFW N°53: O Melhor do Fashion Week Brasileiro','category'=>'Eventos','status'=>'published','excerpt'=>'Os destaques do São Paulo Fashion Week, o maior evento de moda da América Latina, em uma cobertura completa.','content'=>'O SPFW N°53 surpreendeu com apresentações ousadas e uma forte presença de marcas sustentáveis. O desfile de abertura da Lenny Niemeyer celebrou 30 anos da marca com uma coleção deslumbrante à beira da piscina do Ibirapuera. A nova geração de estilistas brasileiros mostrou força com coleções que dialogam com a cultura periférica, o artesanato regional e a moda consciente. O público e a imprensa especializada saíram unânimes: o Brasil tem muito a dizer ao mundo da moda.','image'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80','author_id'=>2,'created_at'=>'2025-02-20'],
            ['id'=>6,'title'=>'Acessórios em Alta: As Bolsas que Todo Mundo Quer em 2025','category'=>'Tendências','status'=>'published','excerpt'=>'Das it-bags aos modelos artesanais, descubra quais acessórios estão dominando o desejo das fashionistas.','content'=>'2025 é o ano das bolsas com personalidade. As micro-bags continuam presentes, mas dividem espaço com os modelos bucket XXL e as clutches estruturadas em couro. As bolsas com franjas estão de volta com força total, assim como os modelos de palha e crochê para o verão. As marcas brasileiras, como Arezzo e Schutz, apresentaram coleções que rivalizam com as internacionais em qualidade e criatividade.','image'=>'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=800&q=80','author_id'=>1,'created_at'=>'2025-02-15'],
            ['id'=>7,'title'=>'Cores do Ano 2025: As Paletas que Vão Dominar seu Guarda-Roupa','category'=>'Tendências','status'=>'draft','excerpt'=>'Do Mocha Mousse ao azul-cobalto: entenda quais cores as grandes marcas estão apostando para esta temporada.','content'=>'Rascunho em desenvolvimento. As cores desta temporada prometem surpreender com paletas inesperadas...','image'=>'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=800&q=80','author_id'=>1,'created_at'=>'2025-02-10'],
        ]);
    }
}

seed_data();
?>
