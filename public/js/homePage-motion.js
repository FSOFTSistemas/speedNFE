import { animate, inView, stagger } from 'https://cdn.jsdelivr.net/npm/motion@latest/+esm';

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const system = document.querySelector('#sistema');

if (system && !reduceMotion) {
    const copyItems = system.querySelectorAll('.system-kicker, .showcase-copy h2, .showcase-copy > p, .system-flow li, .showcase-copy .btn');
    const visual = system.querySelector('[data-system-visual]');
    const signals = system.querySelectorAll('.system-signal');

    inView(system, () => {
        animate(copyItems, { opacity: [0, 1], x: [-34, 0] }, {
            duration: .72,
            delay: stagger(.07),
            ease: [.22, 1, .36, 1],
        });
        animate(visual, { opacity: [0, 1], x: [54, 0], rotateY: [-7, 0] }, {
            duration: 1,
            ease: [.22, 1, .36, 1],
        });
        animate(signals, { opacity: [0, 1], y: [22, 0], scale: [.94, 1] }, {
            duration: .68,
            delay: stagger(.14, { startDelay: .52 }),
            ease: [.22, 1, .36, 1],
        });
    }, { amount: .2 });

    const visualWrap = system.querySelector('.system-visual-wrap');
    visualWrap?.addEventListener('pointermove', (event) => {
        if (event.pointerType === 'touch') return;
        const bounds = visualWrap.getBoundingClientRect();
        const x = (event.clientX - bounds.left) / bounds.width - .5;
        const y = (event.clientY - bounds.top) / bounds.height - .5;
        animate(visual, { rotateY: x * 5, rotateX: y * -5, x: x * 7, y: y * 7 }, {
            duration: .35,
            ease: 'easeOut',
        });
    });

    visualWrap?.addEventListener('pointerleave', () => {
        animate(visual, { rotateY: 0, rotateX: 0, x: 0, y: 0 }, { duration: .5 });
    });
}

const focusPositions = {
    notes: ['69%', '30%'],
    stock: ['32%', '47%'],
    finance: ['73%', '68%'],
};

document.querySelectorAll('[data-system-tab]').forEach((button) => {
    button.addEventListener('click', () => {
        const focus = document.querySelector('.system-focus');
        const [x, y] = focusPositions[button.dataset.systemTab];

        document.querySelectorAll('[data-system-tab]').forEach((item) => item.classList.remove('is-active'));
        button.classList.add('is-active');

        if (focus) {
            focus.style.setProperty('--focus-x', x);
            focus.style.setProperty('--focus-y', y);
            if (!reduceMotion) {
                animate(focus, { scale: [1, 1.24, 1] }, { duration: .42, ease: 'easeOut' });
            }
        }
    });
});

// Hero: luz contextual e entrada com ritmo editorial
const hero = document.querySelector('#hero');
const heroLight = hero?.querySelector('.hero-cursor-light');

if (hero && !reduceMotion) {
    const heroCopy = hero.querySelectorAll('.eyebrow, h1, .lead, .market-proof, .hero-actions, .hero-note, .hero-scroll-cue');
    const heroVisual = hero.querySelector('.hero-visual');
    const codeStream = hero.querySelector('.hero-code-stream');

    animate(heroCopy, { opacity: [0, 1], y: [28, 0] }, {
        duration: .8,
        delay: stagger(.075, { startDelay: .12 }),
        ease: [.22, 1, .36, 1],
    });
    animate(heroVisual, { opacity: [0, 1], x: [54, 0], rotateY: [-6, 0] }, {
        duration: 1.05,
        delay: .28,
        ease: [.22, 1, .36, 1],
    });
    animate(codeStream, { x: [70, 0], opacity: [0, 1] }, { duration: 1.4, delay: .35 });

    hero.addEventListener('pointermove', (event) => {
        if (event.pointerType === 'touch') return;
        const bounds = hero.getBoundingClientRect();
        heroLight?.style.setProperty('--hero-x', `${event.clientX - bounds.left}px`);
        heroLight?.style.setProperty('--hero-y', `${event.clientY - bounds.top}px`);
    });
}

// Entradas diferentes por família de conteúdo (evita uma página inteira com o mesmo fade)
if (!reduceMotion) {
    inView('#trust-bar', () => {
        animate('#trust-bar .trust-item', { opacity: [0, 1], y: [18, 0] }, {
            duration: .58,
            delay: stagger(.08),
            ease: [.22, 1, .36, 1],
        });
    }, { amount: .45 });

    inView('#documentos', () => {
        animate('#documentos .doc-card', { opacity: [0, 1], y: [36, 0] }, {
            duration: .7,
            delay: stagger(.12),
            ease: [.22, 1, .36, 1],
        });
    }, { amount: .2 });

    inView('#recursos', () => {
        animate('#recursos .feature-card', { opacity: [0, 1], scale: [.96, 1] }, {
            duration: .68,
            delay: stagger(.065),
            ease: [.22, 1, .36, 1],
        });
    }, { amount: .12 });

    inView('#planos', () => {
        animate('#planos .price-card', { opacity: [0, 1], y: [42, 0] }, {
            duration: .72,
            delay: stagger(.11),
            ease: [.22, 1, .36, 1],
        });
    }, { amount: .16 });

    inView('#sobre', () => {
        animate('#sobre .sobre-media', { opacity: [0, 1], x: [-44, 0] }, { duration: .85, ease: [.22, 1, .36, 1] });
        animate('#sobre .sobre-copy', { opacity: [0, 1], x: [44, 0] }, { duration: .85, delay: .12, ease: [.22, 1, .36, 1] });
    }, { amount: .2 });

    inView('#avaliacoes', () => {
        animate('#avaliacoes .review-card', { opacity: [0, 1], rotate: [-1.5, 0], y: [32, 0] }, {
            duration: .7,
            delay: stagger(.1),
            ease: [.22, 1, .36, 1],
        });
    }, { amount: .2 });

    inView('#cta-final', () => {
        animate('#cta-final > *', { opacity: [0, 1], y: [24, 0] }, {
            duration: .7,
            delay: stagger(.08),
            ease: [.22, 1, .36, 1],
        });
    }, { amount: .35 });
}

// Spotlight acompanha o ponteiro nos blocos de recurso
document.querySelectorAll('.feature-card').forEach((card) => {
    card.addEventListener('pointermove', (event) => {
        const bounds = card.getBoundingClientRect();
        card.style.setProperty('--spot-x', `${event.clientX - bounds.left}px`);
        card.style.setProperty('--spot-y', `${event.clientY - bounds.top}px`);

        if (!reduceMotion && event.pointerType !== 'touch') {
            const x = (event.clientX - bounds.left) / bounds.width - .5;
            const y = (event.clientY - bounds.top) / bounds.height - .5;
            animate(card, { rotateY: x * 4, rotateX: y * -4, y: -3 }, { duration: .24, ease: 'easeOut' });
        }
    });

    card.addEventListener('pointerleave', () => {
        if (!reduceMotion) {
            animate(card, { rotateY: 0, rotateX: 0, y: 0 }, { duration: .42, ease: 'easeOut' });
        }
    });
});

// Botões ganham uma resposta magnética discreta em desktop
if (!reduceMotion) {
    document.querySelectorAll('.btn').forEach((button) => {
        button.addEventListener('pointermove', (event) => {
            if (event.pointerType === 'touch') return;
            const bounds = button.getBoundingClientRect();
            const x = event.clientX - bounds.left - bounds.width / 2;
            const y = event.clientY - bounds.top - bounds.height / 2;
            animate(button, { x: x * .1, y: y * .16 }, { duration: .18 });
        });
        button.addEventListener('pointerleave', () => {
            animate(button, { x: 0, y: 0 }, { duration: .35, ease: 'easeOut' });
        });
    });
}

// Abertura do FAQ com animação leve no conteúdo
document.querySelectorAll('.faq-item').forEach((item) => {
    item.addEventListener('toggle', () => {
        const paragraph = item.querySelector('p');
        if (item.open && paragraph && !reduceMotion) {
            animate(paragraph, { opacity: [0, 1], y: [-8, 0] }, { duration: .3, ease: 'easeOut' });
        }
    });
});

// Navegador lateral: progresso, seção atual e atalho para a próxima etapa
const companion = document.querySelector('.scroll-companion');

if (companion) {
    const progressCircle = companion.querySelector('.companion-progress');
    const companionLabel = companion.querySelector('strong');
    const companionIcon = companion.querySelector('.scroll-companion-ring i');
    const journey = [
        ['#hero', 'Início'],
        ['#documentos', 'Documentos'],
        ['#recursos', 'Recursos'],
        ['#sistema', 'O sistema'],
        ['#planos', 'Planos'],
        ['#sobre', 'Sobre'],
        ['#avaliacoes', 'Avaliações'],
        ['#faq', 'Dúvidas'],
        ['#cta-final', 'Comece agora'],
    ].map(([selector, label]) => ({ element: document.querySelector(selector), label })).filter(item => item.element);

    let activeIndex = 0;
    let ticking = false;
    const circumference = 125.66;

    const updateCompanion = () => {
        const scrollable = Math.max(document.documentElement.scrollHeight - window.innerHeight, 1);
        const progress = Math.min(Math.max(window.scrollY / scrollable, 0), 1);
        const travel = Math.max(window.innerHeight - 260, 0);
        const viewportMarker = window.scrollY + window.innerHeight * .44;

        companion.style.setProperty('--scroll-progress', progress.toFixed(4));
        companion.style.setProperty('--companion-y', `${progress * travel}px`);
        progressCircle.style.strokeDashoffset = `${circumference * (1 - progress)}`;

        let nextIndex = 0;
        journey.forEach((item, index) => {
            const itemTop = item.element.getBoundingClientRect().top + window.scrollY;
            if (itemTop <= viewportMarker) nextIndex = index;
        });

        if (nextIndex !== activeIndex) {
            activeIndex = nextIndex;
            companionLabel.textContent = journey[activeIndex].label;
            if (!reduceMotion) animate(companionLabel, { opacity: [0, 1], x: [-5, 0] }, { duration: .24 });
        }

        const atEnd = activeIndex === journey.length - 1 && progress > .92;
        companionIcon.className = atEnd ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
        companion.setAttribute('aria-label', atEnd ? 'Voltar ao início da página' : `Avançar após ${journey[activeIndex].label}`);
        companion.dataset.atEnd = atEnd ? 'true' : 'false';
        ticking = false;
    };

    const requestCompanionUpdate = () => {
        if (!ticking) {
            requestAnimationFrame(updateCompanion);
            ticking = true;
        }
    };

    companion.addEventListener('click', () => {
        if (companion.dataset.atEnd === 'true') {
            window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
            return;
        }

        const target = journey[Math.min(activeIndex + 1, journey.length - 1)];
        target?.element.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' });
    });

    window.addEventListener('scroll', requestCompanionUpdate, { passive: true });
    window.addEventListener('resize', requestCompanionUpdate);
    updateCompanion();
}
