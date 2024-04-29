<div>
    <form action="">
        <header>
            <div class="card">
                <div class="card-body">
                    <label for="">Chave</label>
                    <input class="form-control" type="text" name="" wire:model="chNFe">
                </div>
            </div>
        </header>
        <main>
            <section>
                @foreach ($prods as $item)
                @endforeach
            </section>
        </main>
    </form>
</div>
