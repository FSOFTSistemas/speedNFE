(() => {
    'use strict';
    let initialized = false;
    const init = () => {
        const root = document.getElementById('pdv-app');
        if (!root || !window.Livewire || initialized) return;
        initialized = true;
        const component = () => window.Livewire.find(root.getAttribute('wire:id'));
        const call = (method, ...args) => component().call(method, ...args);
        let pending = 0;
        let submitting = false;
        let previousFocus;
        const money = value => Number(value || 0).toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
        const normalize = value => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
        const close = dialog => { if (dialog && dialog.open) dialog.close(); };
        const open = id => {
            const dialog = document.getElementById(id);
            previousFocus = document.activeElement;
            if (!dialog.open) dialog.showModal();
            const input = dialog.querySelector('input');
            if (input) { input.focus(); if (input.type === 'number') input.select(); }
        };
        const notice = message => {
            const dialog = root.querySelector('dialog[open]');
            let box = dialog ? dialog.querySelector('.pdv-dialog-notice') : document.getElementById('pdv-notice');
            if (!box && dialog) {
                box = document.createElement('p'); box.className = 'pdv-notice pdv-dialog-notice'; box.setAttribute('role', 'alert'); dialog.append(box);
            }
            box.textContent = message; box.hidden = false;
        };
        root.querySelectorAll('dialog').forEach(dialog => {
            dialog.addEventListener('close', () => {
                dialog.querySelectorAll('.pdv-dialog-notice').forEach(box => box.remove());
                if (previousFocus && previousFocus.isConnected) previousFocus.focus();
            });
        });
        const clearItems = () => {
            const button = root.querySelector('[data-pdv-clear]');
            if (pending || !button || button.disabled) return;
            if (window.confirm('Remover todos os itens desta venda?')) call('clearItems');
        };
        root.addEventListener('click', event => {
            const target = event.target.closest('button');
            if (!target || target.disabled) return;
            if (target.hasAttribute('data-pdv-close')) close(target.closest('dialog'));
            if (target.hasAttribute('data-pdv-clear')) clearItems();
            if (target.hasAttribute('data-pdv-finish') && !pending) open('pdv-confirm');
            if (target.hasAttribute('data-pdv-submit')) {
                if (submitting || pending || document.getElementById('pdv-finish')?.disabled) return;
                const form = document.getElementById('pdv-form');
                document.getElementById('acao_pos_salvar').value = target.dataset.pdvSubmit;
                if (!form.reportValidity()) return;
                submitting = true;
                root.querySelectorAll('button').forEach(button => button.disabled = true);
                target.textContent = 'Processando venda…';
                HTMLFormElement.prototype.submit.call(form);
            }
        });
        document.getElementById('pdv-form').addEventListener('submit', event => event.preventDefault());
        root.addEventListener('input', event => {
            const id = event.target.dataset.pdvFilter;
            if (!id) return;
            const list = document.getElementById(id);
            const query = normalize(event.target.value);
            let visible = 0;
            list.querySelectorAll('.pdv-picker-item').forEach(item => {
                item.hidden = !normalize(item.textContent).includes(query);
                if (!item.hidden) visible++;
            });
            const empty = list.parentElement.querySelector('.pdv-filter-empty');
            if (empty) empty.hidden = visible > 0;
        });
        document.addEventListener('keydown', event => {
            if (event.ctrlKey || event.metaKey || event.altKey || event.repeat || submitting || pending || root.querySelector('dialog[open]')) return;
            const methods = {F1: 'searchCustomers', F2: 'searchProducts', F5: 'showPaymentArea'};
            if (event.key === 'F4') { event.preventDefault(); clearItems(); return; }
            if (!methods[event.key]) return;
            event.preventDefault();
            if (event.key === 'F2' && root.querySelector('.pdv-composer fieldset').disabled) return;
            if (event.key === 'F5' && document.getElementById('pdv-start-payment').disabled) return;
            call(methods[event.key]);
        });
        Livewire.hook('message.sent', (message, instance) => { if (instance.id === root.getAttribute('wire:id')) pending++; });
        Livewire.hook('message.processed', (message, instance) => { if (instance.id === root.getAttribute('wire:id')) pending = Math.max(0, pending - 1); });
        Livewire.hook('message.failed', (message, instance) => { if (instance.id === root.getAttribute('wire:id')) { pending = Math.max(0, pending - 1); notice('Não foi possível atualizar a venda. Confira sua conexão e tente novamente.'); } });
        Livewire.on('OpenAddProdModal', products => {
            const list = document.getElementById('pdv-product-results'); list.replaceChildren();
            document.getElementById('pdv-product-filter').value = '';
            (Array.isArray(products) ? products : Object.values(products || {})).forEach(product => {
                const button = document.createElement('button'); button.type = 'button'; button.className = 'pdv-picker-item';
                const description = document.createElement('span'); const name = document.createElement('strong'); const code = document.createElement('small');
                name.textContent = product.produto || 'Produto sem descrição'; code.textContent = `Código: ${product.codigo || '—'}`;
                description.append(name, code);
                const price = document.createElement('span'); price.className = 'pdv-picker-price'; price.textContent = money(product.precovenda);
                button.append(description, price);
                button.addEventListener('click', () => { if (!pending) call('selectProd', product.id, product.produto, product.codigo, product.precovenda); });
                list.append(button);
            });
            if (!list.children.length) { const empty = document.createElement('p'); empty.className = 'pdv-muted'; empty.textContent = 'Nenhum produto encontrado. Tente outro nome ou código.'; list.append(empty); }
            open('AddProdModal');
        });
        Livewire.on('OpenCustomersModal', () => {
            const input = document.getElementById('pdv-customer-filter'); input.value = ''; input.dispatchEvent(new Event('input', {bubbles: true})); open('SearchClientModal');
        });
        Livewire.on('OpenPaymentModal', () => open('PaymentModal'));
        [['CloseCustomersModal','SearchClientModal'], ['CloseAddProdModal','AddProdModal'], ['ClosePaymentModal','PaymentModal']].forEach(([event,id]) => Livewire.on(event, () => close(document.getElementById(id))));
        Livewire.on('ShowPaymentArea', () => requestAnimationFrame(() => {
            const panel = document.getElementById('addPaymentMethod');
            if (panel) { panel.scrollIntoView({behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'nearest'}); panel.focus({preventScroll: true}); }
        }));
        Livewire.on('ProdutoJaInserido', notice); Livewire.on('ErrorInPayment', notice);
    };
    document.addEventListener('livewire:load', init);
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
