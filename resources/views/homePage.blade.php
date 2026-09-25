<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <title>SpeedNFE — Emita NFe, NFC-e e MDF-e sem sair do celular</title>
    <meta name="description" content="Emita notas fiscais eletrônicas de qualquer lugar, sem instalar programa e sem precisar de computador. NFe, NFC-e e MDF-e em poucos cliques, com certificação SEFAZ, PDV, estoque e financeiro integrados." />

    <link rel="stylesheet" href="{{ asset('css/fontawesome-all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/homePage.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/homePage-modern.css') }}" />
    <link rel="shortcut icon" href="{{ asset('site/img/logo.png') }}" />

    <!-- PWA -->
    <meta name="theme-color" content="#00033a" />
    <link rel="apple-touch-icon" href="{{ asset('logo.PNG') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
</head>

<body class="homepage-preview">

    <!-- Header -->
    <header id="site-header">
        <div class="container">
            <a href="{{ route('homePage') }}" class="brand">
                <img src="{{ asset('site/img/logo.png') }}" alt="SpeedNFE" />
                SpeedNFE
            </a>

            <nav id="main-nav">
                <ul>
                    <li><a href="#documentos">Documentos</a></li>
                    <li><a href="#recursos">Recursos</a></li>
                    <li><a href="#sistema">O sistema</a></li>
                    <li><a href="#avaliacoes">Avaliações</a></li>
                    <li><a href="#planos">Planos</a></li>
                    <li><a href="#faq">Dúvidas</a></li>
                </ul>
            </nav>

            <div class="header-actions">
                <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Entrar</a>
                <a href="https://wa.me/5587981753993?text=Ol%C3%A1!+Quero+testar+o+SpeedNFE+gratuitamente." target="_blank" rel="noopener" class="btn btn-accent btn-sm">
                    <i class="fab fa-whatsapp"></i> Teste grátis
                </a>
                <button class="nav-toggle" id="navToggle" aria-label="Abrir menu" aria-expanded="false" aria-controls="main-nav"><i class="fas fa-bars"></i></button>
            </div>
        </div>
        <button class="nav-backdrop" id="navBackdrop" aria-label="Fechar menu"></button>
    </header>

    <!-- Hero -->
    <section id="hero">
        <div class="hero-code-stream" aria-hidden="true">
            <span>NFe</span><span>NFC-e</span><span>MDF-e</span><span>SEFAZ</span><span>XML</span>
        </div>
        <div class="hero-cursor-light" aria-hidden="true"></div>
        <div class="container hero-grid">
            <div class="hero-copy">
                <span class="eyebrow"><i class="fas fa-bolt"></i> Emissão fiscal sem complicação</span>
                <h1>Emita <span>NFe, NFC-e e MDF-e</span> direto do celular, em qualquer lugar</h1>
                <p class="lead">O SpeedNFE é o sistema completo de gestão fiscal que cabe no seu bolso: emissão de notas, PDV, estoque, financeiro e Pix em uma única plataforma na nuvem — sem instalar nada e sem depender de computador.</p>

                <div class="market-proof">
                    <strong>Mais de 10 anos no mercado fiscal</strong>
                    <span>experiência prática em emissão, suporte e conformidade com a SEFAZ.</span>
                </div>

                <div class="hero-actions">
                    <a href="https://wa.me/5587981753993?text=Ol%C3%A1!+Quero+testar+o+SpeedNFE+gratuitamente." target="_blank" rel="noopener" class="btn btn-accent">
                        <i class="fab fa-whatsapp"></i> Testar grátis agora
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline">Já tenho conta</a>
                </div>

                <div class="hero-note">
                    <i class="fas fa-check-circle"></i> Sem cartão de crédito · 1ª nota fiscal de teste grátis · Suporte via WhatsApp
                </div>
                <div class="hero-scroll-cue" aria-hidden="true"><span></span> Explore a plataforma</div>
            </div>

            <div class="hero-visual reveal">
                <div class="browser-frame">
                    <div class="browser-bar"><span></span><span></span><span></span></div>
                    <img src="{{ asset('css/images/tela.png') }}" alt="Painel de emissão de notas fiscais do SpeedNFE" />
                </div>
                <div class="phone-frame">
                    <div class="phone-notch"></div>
                    <div class="phone-screen">
                        <img src="{{ asset('css/images/tela.png') }}" alt="SpeedNFE acessado pelo celular" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust bar -->
    <section id="trust-bar">
        <div class="container">
            <div class="trust-item trust-strong"><i class="fas fa-history"></i><span>+10 anos de mercado<small>autoridade em soluções fiscais</small></span></div>
            <div class="trust-item"><i class="fas fa-shield-alt"></i><span>Certificada pela SEFAZ<small>emissões 100% dentro da lei</small></span></div>
            <div class="trust-item"><i class="fas fa-cloud"></i><span>100% na nuvem<small>acesse de qualquer dispositivo</small></span></div>
            <div class="trust-item"><i class="fab fa-whatsapp"></i><span>Suporte especializado<small>atendimento humano via WhatsApp</small></span></div>
        </div>
        <div class="trust-carousel" aria-label="Recursos disponíveis na plataforma">
            <div class="trust-carousel-track">
                <div class="trust-carousel-group">
                    <span><i class="fas fa-file-invoice"></i> NFe</span>
                    <span><i class="fas fa-receipt"></i> NFC-e</span>
                    <span><i class="fas fa-truck"></i> MDF-e</span>
                    <span><i class="fas fa-shield-alt"></i> Integração SEFAZ</span>
                    <span><i class="fas fa-boxes"></i> Estoque conectado</span>
                    <span><i class="fas fa-chart-line"></i> Financeiro</span>
                    <span><i class="fas fa-qrcode"></i> Pix integrado</span>
                    <span><i class="fas fa-mobile-alt"></i> Acesso mobile</span>
                </div>
                <div class="trust-carousel-group" aria-hidden="true">
                    <span><i class="fas fa-file-invoice"></i> NFe</span>
                    <span><i class="fas fa-receipt"></i> NFC-e</span>
                    <span><i class="fas fa-truck"></i> MDF-e</span>
                    <span><i class="fas fa-shield-alt"></i> Integração SEFAZ</span>
                    <span><i class="fas fa-boxes"></i> Estoque conectado</span>
                    <span><i class="fas fa-chart-line"></i> Financeiro</span>
                    <span><i class="fas fa-qrcode"></i> Pix integrado</span>
                    <span><i class="fas fa-mobile-alt"></i> Acesso mobile</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Documentos -->
    <section id="documentos">
        <div class="container">
            <div class="section-head reveal">
                <span class="eyebrow">Documentos fiscais</span>
                <h2>Tudo o que sua empresa precisa emitir, em um só lugar</h2>
                <p>Deixe de usar sistemas separados para cada tipo de documento. No SpeedNFE você emite tudo pelo mesmo painel.</p>
            </div>

            <div class="doc-grid">
                <div class="doc-card reveal">
                    <div class="doc-icon"><i class="fas fa-file-invoice"></i></div>
                    <h3>NFe</h3>
                    <p>Nota Fiscal Eletrônica para vendas entre empresas, com validação e envio automático à SEFAZ.</p>
                </div>
                <div class="doc-card reveal">
                    <div class="doc-icon"><i class="fas fa-receipt"></i></div>
                    <h3>NFC-e</h3>
                    <p>Nota Fiscal de Consumidor Eletrônica, ideal para vendas no varejo e PDV.</p>
                </div>
                <div class="doc-card reveal">
                    <div class="doc-icon"><i class="fas fa-clipboard-list"></i></div>
                    <h3>MDF-e</h3>
                    <p>Manifesto Eletrônico de Documentos Fiscais para controle de cargas em viagem.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Recursos -->
    <section id="recursos">
        <div class="resource-3d-stack scene-3d" aria-hidden="true">
            <span></span><span></span><span></span>
        </div>
        <div class="container">
            <div class="section-head reveal">
                <span class="eyebrow">Muito além da nota fiscal</span>
                <h2>Um sistema completo para gerir o seu negócio</h2>
                <p>Emitir nota é só o começo. O SpeedNFE reúne as ferramentas que sua empresa usa todo dia.</p>
            </div>

            <div class="feature-grid">
                <div class="feature-card reveal">
                    <i class="fas fa-mobile-alt"></i>
                    <h3>Emissão mobile</h3>
                    <p>Emita notas fiscais direto do smartphone ou tablet, sem precisar de computador nem instalar programas.</p>
                </div>
                <div class="feature-card reveal">
                    <i class="fas fa-cash-register"></i>
                    <h3>PDV e cupom</h3>
                    <p>Frente de caixa integrada para vender rápido no balcão e emitir o cupom fiscal na hora.</p>
                </div>
                <div class="feature-card reveal">
                    <i class="fas fa-boxes"></i>
                    <h3>Controle de estoque</h3>
                    <p>Cadastre produtos, acompanhe entradas e saídas e nunca mais venda o que não tem em estoque.</p>
                </div>
                <div class="feature-card reveal">
                    <i class="fas fa-hand-holding-usd"></i>
                    <h3>Financeiro completo</h3>
                    <p>Contas a pagar e a receber, fluxo de caixa e visão clara da saúde financeira da sua empresa.</p>
                </div>
                <div class="feature-card reveal">
                    <i class="fas fa-qrcode"></i>
                    <h3>Pagamentos via Pix</h3>
                    <p>Receba mais rápido com Pix integrado diretamente aos seus pedidos e notas.</p>
                </div>
                <div class="feature-card reveal">
                    <i class="fas fa-headset"></i>
                    <h3>Suporte especializado</h3>
                    <p>Equipe própria pronta para ajudar em cada etapa, do cadastro à primeira nota emitida.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Showcase -->
    <section id="sistema" aria-labelledby="sistema-title">
        <div id="showcase">
            <div class="system-grid-lines" aria-hidden="true"></div>
            <div class="container showcase-grid">
                <div class="showcase-copy system-copy">
                    <div class="system-kicker">
                        <span class="system-index">02</span>
                        <span class="eyebrow">Veja o sistema por dentro</span>
                    </div>
                    <h2 id="sistema-title">Sua operação fiscal,<br><em>sem pontos cegos.</em></h2>
                    <p>O painel organiza as rotinas fiscais em fluxos claros: cadastro, venda, emissão, consulta e reenvio. Assim a equipe encontra o que precisa sem depender de treinamento técnico.</p>
                    <ul class="showcase-list system-flow">
                        <li><span>01</span><p><strong>Emita com segurança</strong>Tela objetiva e prévia antes do envio à SEFAZ.</p></li>
                        <li><span>02</span><p><strong>Acompanhe em tempo real</strong>Autorizadas, canceladas e rejeitadas em um só fluxo.</p></li>
                        <li><span>03</span><p><strong>Resolva sem procurar</strong>XML, DANFE, estoque e financeiro sempre conectados.</p></li>
                    </ul>
                    <a href="https://wa.me/5587981753993?text=Ol%C3%A1!+Gostaria+de+solicitar+uma+demonstra%C3%A7%C3%A3o+do+SpeedNFE." target="_blank" rel="noopener" class="btn btn-accent">
                        <i class="fab fa-whatsapp"></i> Solicitar demonstração
                    </a>
                </div>
                <div class="system-visual-wrap">
                    <div class="system-orbit orbit-one" aria-hidden="true"></div>
                    <div class="system-orbit orbit-two" aria-hidden="true"></div>
                    <div class="screens-preview" data-system-visual>
                        <div class="system-live"><span></span> Ambiente operacional</div>
                        <div class="showcase-frame browser-frame">
                            <div class="browser-bar"><span></span><span></span><span></span></div>
                            <img src="{{ asset('css/images/tela.png') }}" alt="Tela de notas fiscais emitidas no SpeedNFE" />
                            <div class="system-focus" aria-hidden="true"></div>
                        </div>
                        <div class="system-signal signal-top"><i class="fas fa-check"></i><span><strong>Nota autorizada</strong>SEFAZ · agora</span></div>
                        <div class="system-signal signal-bottom"><span class="signal-value">+18%</span><span><strong>Mais agilidade</strong>na rotina fiscal</span></div>
                        <div class="screen-details" aria-label="Explore as principais áreas do sistema">
                            <button type="button" class="is-active" data-system-tab="notes"><i class="fas fa-file-invoice"></i> Notas fiscais</button>
                            <button type="button" data-system-tab="stock"><i class="fas fa-boxes"></i> Estoque</button>
                            <button type="button" data-system-tab="finance"><i class="fas fa-chart-line"></i> Financeiro</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Planos -->
    <section id="planos">
        <div class="container">
            <div class="section-head reveal">
                <span class="eyebrow">Planos</span>
                <h2>Escolha o plano ideal para o tamanho da sua empresa</h2>
                <p>Comece pequeno e cresça sem trocar de sistema. Todos os planos emitem NFe com certificação SEFAZ.</p>
            </div>

            <p class="pricing-note reveal"><i class="fas fa-gift"></i> Todos os planos incluem 1 nota fiscal de teste totalmente grátis</p>

            <div class="pricing-grid">
                <div class="price-card reveal">
                    <h3>Plano Básico</h3>
                    <p class="price-tag">Ideal para quem está começando</p>
                    <ul class="price-list">
                        <li><i class="fas fa-check"></i> Cadastro de até 12 clientes</li>
                        <li><i class="fas fa-check"></i> Cadastro de até 10 produtos</li>
                        <li><i class="fas fa-check"></i> Emissão de até 10 notas por mês</li>
                        <li><i class="fas fa-check"></i> Emissão de NFe</li>
                        <li><i class="fas fa-check"></i> Suporte via e-mail</li>
                    </ul>
                    <a href="https://wa.me/5587981753993?text=Ol%C3%A1!+Tenho+interesse+no+Plano+B%C3%A1sico+do+SpeedNFE." target="_blank" rel="noopener" class="btn btn-navy btn-block">Assinar Básico</a>
                </div>

                <div class="price-card is-highlight reveal">
                    <h3>Plano Advanced</h3>
                    <p class="price-tag">Para empresas em crescimento</p>
                    <ul class="price-list">
                        <li><i class="fas fa-check"></i> Cadastro de até 20 clientes</li>
                        <li><i class="fas fa-check"></i> Cadastro de até 20 produtos</li>
                        <li><i class="fas fa-check"></i> Emissão de até 20 notas por mês</li>
                        <li><i class="fas fa-check"></i> Emissão de NFe</li>
                        <li><i class="fas fa-check"></i> Suporte via WhatsApp</li>
                    </ul>
                    <a href="https://wa.me/5587981753993?text=Ol%C3%A1!+Tenho+interesse+no+Plano+Advanced+do+SpeedNFE." target="_blank" rel="noopener" class="btn btn-accent btn-block">Assinar Advanced</a>
                </div>

                <div class="price-card reveal">
                    <h3>Plano Premium</h3>
                    <p class="price-tag">Para quem precisa de mais volume</p>
                    <ul class="price-list">
                        <li><i class="fas fa-check"></i> Cadastro ilimitado de clientes</li>
                        <li><i class="fas fa-check"></i> Cadastro ilimitado de produtos</li>
                        <li><i class="fas fa-check"></i> Emissão de até 40 notas por mês</li>
                        <li><i class="fas fa-check"></i> Emissão de NFe e MDF-e</li>
                        <li><i class="fas fa-check"></i> Suporte via WhatsApp prioritário</li>
                    </ul>
                    <a href="https://wa.me/5587981753993?text=Ol%C3%A1!+Tenho+interesse+no+Plano+Premium+do+SpeedNFE." target="_blank" rel="noopener" class="btn btn-navy btn-block">Assinar Premium</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Sobre -->
    <section id="sobre">
        <div class="container">
            <div class="sobre-media reveal">
                <img src="{{ asset('css/images/banner.jpg') }}" alt="Equipe SpeedNFE" />
                <div class="sobre-stat">
                    <strong>+10</strong>
                    <span>anos de experiência em soluções fiscais</span>
                </div>
            </div>
            <div class="sobre-copy reveal">
                <span class="eyebrow">Sobre a SpeedNFE</span>
                <h2>Uma plataforma criada por especialistas em gestão fiscal</h2>
                <p>A SpeedNFE é uma solução completa para emissão e gestão de NFe, NFC-e e MDF-e, criada por uma empresa especializada em soluções fiscais, com mais de 10 anos de experiência no mercado.</p>
                <p>Somos certificados pela SEFAZ e protegemos seus dados com criptografia e boas práticas de segurança, para que você emita notas com tranquilidade e total conformidade legal.</p>
                <div class="sobre-points">
                    <p><strong>Experiência fiscal:</strong> acompanhamento das regras de emissão e rotinas exigidas pela SEFAZ.</p>
                    <p><strong>Atendimento próximo:</strong> suporte humano para implantação, dúvidas e operação diária.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Avaliações -->
    <section id="avaliacoes">
        <div class="container">
            <div class="section-head reveal">
                <span class="eyebrow">Avaliações</span>
                <h2>Empresas que precisam emitir sem travar a rotina</h2>
                <p>Clientes de varejo, serviços e distribuição usam o SpeedNFE para simplificar emissão, cadastros e acompanhamento fiscal.</p>
            </div>

            <div class="reviews-grid">
                <article class="review-card reveal">
                    <div class="review-stars" aria-label="5 de 5 estrelas">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p>“Conseguimos emitir notas pelo celular e resolver dúvidas direto com o suporte. Isso agilizou muito a rotina da loja.”</p>
                    <strong>Cliente do varejo</strong>
                </article>
                <article class="review-card reveal">
                    <div class="review-stars" aria-label="5 de 5 estrelas">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p>“A organização das notas, clientes e produtos deixou o fechamento mais rápido. O painel é direto e fácil de acompanhar.”</p>
                    <strong>Empresa de distribuição</strong>
                </article>
                <article class="review-card reveal">
                    <div class="review-stars" aria-label="5 de 5 estrelas">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p>“A implantação foi simples e a equipe ajudou na configuração fiscal. Hoje emitimos com mais segurança.”</p>
                    <strong>Prestador de serviços</strong>
                </article>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section>
        <div class="section-head reveal">
            <span class="eyebrow">Dúvidas frequentes</span>
            <h2>Perguntas que a gente mais recebe</h2>
        </div>
        <div id="faq" class="reveal">
            <details class="faq-item" open>
                <summary>Preciso de computador para emitir notas fiscais?</summary>
                <p>Não. O SpeedNFE funciona 100% na nuvem e pode ser acessado de qualquer smartphone, tablet ou computador com internet, sem instalar nenhum programa.</p>
            </details>
            <details class="faq-item">
                <summary>Preciso de certificado digital?</summary>
                <p>Sim, o certificado digital é exigido pela SEFAZ para emissão de notas fiscais eletrônicas. Nossa equipe de suporte ajuda você a configurá-lo dentro do sistema.</p>
            </details>
            <details class="faq-item">
                <summary>Como funciona a nota fiscal de teste grátis?</summary>
                <p>Todo novo cliente pode emitir 1 nota fiscal de teste sem custo, para conhecer o sistema antes de contratar um plano.</p>
            </details>
            <details class="faq-item">
                <summary>Posso trocar de plano depois?</summary>
                <p>Sim. Você pode fazer upgrade do seu plano a qualquer momento conforme sua empresa crescer, sem perder seu histórico de notas e cadastros.</p>
            </details>
            <details class="faq-item">
                <summary>Meus dados fiscais estão seguros?</summary>
                <p>Sim. Seus dados ficam armazenados na nuvem com criptografia e boas práticas de segurança, e o sistema é mantido atualizado com as regras da SEFAZ.</p>
            </details>
            <details class="faq-item">
                <summary>Como funciona o suporte?</summary>
                <p>Nosso suporte é feito por uma equipe especializada em NFe, NFC-e e MDF-e, disponível via WhatsApp para tirar dúvidas em qualquer etapa.</p>
            </details>
        </div>
    </section>

    <!-- CTA final -->
    <section>
        <div id="cta-final" class="reveal">
            <div class="cta-3d-orbit scene-3d" aria-hidden="true"><span></span><i></i></div>
            <span class="eyebrow"><i class="fas fa-bolt"></i> Comece agora</span>
            <h2>Pare de perder tempo com nota fiscal. Emita em minutos, de onde estiver.</h2>
            <p>Fale agora com nossa equipe pelo WhatsApp e comece a emitir sua primeira nota fiscal de teste, gratuitamente.</p>
            <div class="cta-actions">
                <a href="https://wa.me/5587981753993?text=Ol%C3%A1!+Quero+falar+com+um+especialista+do+SpeedNFE." target="_blank" rel="noopener" class="btn btn-accent">
                    <i class="fab fa-whatsapp"></i> Falar com um especialista
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline">Já tenho conta, entrar</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-top">
                <a href="{{ route('homePage') }}" class="footer-brand">
                    <img src="{{ asset('site/img/logo.png') }}" alt="SpeedNFE" /> SpeedNFE
                </a>
                <div class="footer-social">
                    <a href="https://f-softsistemas.com.br/" target="_blank" rel="noopener" aria-label="Site da FSOFT Sistemas"><i class="fas fa-globe"></i></a>
                    <a href="https://www.instagram.com/fsoft_sistemas/" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} FSOFT SISTEMAS. Todos os direitos reservados.</span>
                <a href="{{ route('privacidade') }}">Política de privacidade</a>
                <a href="https://f-softsistemas.com.br/" target="_blank" rel="noopener">f-softsistemas.com.br</a>
            </div>
        </div>
    </footer>

    <button type="button" class="scroll-companion" aria-label="Avançar para a próxima seção">
        <span class="scroll-companion-ring" aria-hidden="true">
            <svg viewBox="0 0 48 48">
                <circle class="companion-track" cx="24" cy="24" r="20"></circle>
                <circle class="companion-progress" cx="24" cy="24" r="20"></circle>
            </svg>
            <i class="fas fa-chevron-down"></i>
        </span>
        <span class="scroll-companion-copy">
            <small>Explorando</small>
            <strong>Início</strong>
        </span>
    </button>

    <a href="https://wa.me/5587981753993?text=Ol%C3%A1!+Quero+saber+mais+sobre+o+SpeedNFE." target="_blank" rel="noopener" class="whatsapp-float" aria-label="Falar no WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Scripts -->
    <script src="{{ asset('/sw.js') }}"></script>
    <script type="module" src="{{ asset('js/homePage-motion.js') }}"></script>
    <script>
        if ("serviceWorker" in navigator) {
            navigator.serviceWorker.register("/sw.js").then(
                (registration) => console.log("Service worker registration succeeded:", registration),
                (error) => console.error(`Service worker registration failed: ${error}`),
            );
        }

        // Header shrink on scroll
        const header = document.getElementById('site-header');
        const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 20);
        document.addEventListener('scroll', onScroll);
        onScroll();

        // Mobile nav toggle
        const navToggle = document.getElementById('navToggle');
        const mainNav = document.getElementById('main-nav');
        const navBackdrop = document.getElementById('navBackdrop');
        const setMenuState = (isOpen) => {
            mainNav.classList.toggle('is-open', isOpen);
            document.body.classList.toggle('nav-open', isOpen);
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            navToggle.innerHTML = isOpen ? '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        };

        navToggle.addEventListener('click', () => setMenuState(!mainNav.classList.contains('is-open')));
        navBackdrop.addEventListener('click', () => setMenuState(false));
        mainNav.querySelectorAll('a').forEach(link => link.addEventListener('click', () => setMenuState(false)));
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setMenuState(false);
            }
        });

        // Reveal on scroll
        const revealEls = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });
        revealEls.forEach(el => observer.observe(el));
    </script>
</body>

</html>
