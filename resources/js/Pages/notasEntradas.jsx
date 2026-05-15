import React, { useMemo, useState } from "react";
import axios from "axios";
import { Head, Link, router, useForm } from "@inertiajs/react";

export default function NotasEntradas({ entradas = [], filters = {} }) {
    const [showImportModal, setShowImportModal] = useState(false);
    const [importarXML, setImportarXML] = useState(false);
    const [showDetalhesModal, setShowDetalhesModal] = useState(false);
    const [entradaSelecionada, setEntradaSelecionada] = useState(null);
    const [loadingDetalhes, setLoadingDetalhes] = useState(false);
    const [filtroProdutoModal, setFiltroProdutoModal] = useState("");

    const filtroForm = useForm({
        data_inicio: filters.data_inicio || "",
        data_fim: filters.data_fim || "",
    });

    const importForm = useForm({
        nota: "",
        type: false,
    });

    const resumo = useMemo(() => {
        const totalNotas = entradas.length;

        const valorTotal = entradas.reduce((total, entrada) => {
            return total + Number(entrada.valor || 0);
        }, 0);

        const fornecedores = new Set(
            entradas.map((entrada) => entrada.fornecedor).filter(Boolean)
        ).size;

        return {
            totalNotas,
            valorTotal,
            fornecedores,
        };
    }, [entradas]);

    function formatCurrency(value) {
        return Number(value || 0).toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL",
        });
    }

    function parseNumber(value) {
        if (value === undefined || value === null || value === "") {
            return 0;
        }

        if (typeof value === "number") {
            return value;
        }

        const normalized = String(value)
            .replace(/\./g, "")
            .replace(",", ".");

        const number = Number(normalized);

        return Number.isNaN(number) ? 0 : number;
    }

    function submitFiltro(event) {
        event.preventDefault();

        filtroForm.get(route("entradas.index"), {
            preserveState: true,
            preserveScroll: true,
        });
    }

    function limparFiltros() {
        router.get(route("entradas.index"));
    }

    function excluirEntrada(entradaId) {
        if (!confirm("Deseja realmente excluir esta entrada?")) {
            return;
        }

        router.delete(route("entradas.destroy", entradaId), {
            preserveScroll: true,
        });
    }

    function getProdutosEntrada(entrada) {
        if (!entrada) {
            return [];
        }

        if (Array.isArray(entrada.produtos)) {
            return entrada.produtos;
        }

        if (Array.isArray(entrada.itens)) {
            return entrada.itens;
        }

        if (Array.isArray(entrada.items)) {
            return entrada.items;
        }

        if (Array.isArray(entrada.det)) {
            return entrada.det;
        }

        return [];
    }

    function getValorProduto(produto, campos) {
        for (const campo of campos) {
            if (produto && produto[campo] !== undefined && produto[campo] !== null) {
                const valor = produto[campo];

                if (typeof valor === "object") {
                    continue;
                }

                return valor;
            }
        }

        return "";
    }

    function getProdutoRelacionado(item) {
        if (!item || typeof item.produto !== "object" || item.produto === null) {
            return null;
        }

        return item.produto;
    }

    function getDescricaoProduto(item) {
        const produtoRelacionado = getProdutoRelacionado(item);

        return (
            getValorProduto(item, ["descricao", "xProd", "nome", "nome_produto"]) ||
            getValorProduto(produtoRelacionado, ["produto", "descricao", "xProd", "nome", "nome_produto"]) ||
            "Produto sem descrição"
        );
    }

    function getCodigoProduto(item) {
        const produtoRelacionado = getProdutoRelacionado(item);

        return (
            getValorProduto(item, ["codigo", "cProd", "cod_produto", "produto_codigo"]) ||
            getValorProduto(produtoRelacionado, ["codigo", "cProd", "cod_produto", "produto_codigo", "id"]) ||
            getValorProduto(item, ["id"])
        );
    }

    function getValorUnitarioProduto(item) {
        const produtoRelacionado = getProdutoRelacionado(item);

        return (
            getValorProduto(item, ["valor_unitario", "vUnCom", "preco", "preco_unitario", "unitario"]) ||
            getValorProduto(produtoRelacionado, ["precocusto", "preco_custo", "precovenda", "preco_venda"])
        );
    }

    function getTotalProduto(item) {
        const totalInformado = getValorProduto(item, ["valor_total", "vProd", "total", "subtotal"]);

        if (totalInformado !== "") {
            return totalInformado;
        }

        const quantidade = getValorProduto(item, ["quantidade", "qCom", "qtd", "qtde"]);
        const valorUnitario = getValorUnitarioProduto(item);

        const totalCalculado = parseNumber(quantidade) * parseNumber(valorUnitario);

        return totalCalculado > 0 ? totalCalculado : "";
    }

    function getProdutosFiltradosModal() {
        const produtos = getProdutosEntrada(entradaSelecionada);
        const termo = filtroProdutoModal.trim().toLowerCase();

        if (!termo) {
            return produtos;
        }

        return produtos.filter((produto) => {
            const codigo = String(getCodigoProduto(produto) || "").toLowerCase();
            const descricao = String(getDescricaoProduto(produto) || "").toLowerCase();
            const quantidade = String(getValorProduto(produto, ["quantidade", "qCom", "qtd", "qtde"]) || "").toLowerCase();

            return (
                codigo.includes(termo) ||
                descricao.includes(termo) ||
                quantidade.includes(termo)
            );
        });
    }

    async function visualizarEntrada(entradaId) {
        try {
            setShowDetalhesModal(true);
            setLoadingDetalhes(true);
            setEntradaSelecionada(null);
            setFiltroProdutoModal("");

            const response = await axios.get(route("itens-entradas.show", entradaId));

            setEntradaSelecionada(response.data);
        } catch (error) {
            console.error(error);
            setEntradaSelecionada({
                error: true,
                message: "Não foi possível carregar os detalhes da entrada.",
            });
        } finally {
            setLoadingDetalhes(false);
        }
    }

    function fecharDetalhesModal() {
        setShowDetalhesModal(false);
        setEntradaSelecionada(null);
        setLoadingDetalhes(false);
        setFiltroProdutoModal("");
    }

    function alterarTipoImportacao(event) {
        const checked = event.target.checked;

        setImportarXML(checked);
        importForm.setData({
            nota: "",
            type: checked,
        });
    }

    function alterarChaveNFe(event) {
        const chave = event.target.value.replace(/[^0-9]/g, "").slice(0, 44);

        importForm.setData("nota", chave);
    }

    function alterarArquivoXML(event) {
        const arquivo = event.target.files?.[0] || "";

        importForm.setData("nota", arquivo);
    }

    function fecharImportModal() {
        setShowImportModal(false);
        setImportarXML(false);
        importForm.reset();
        importForm.clearErrors();
    }

    function submitImportacao(event) {
        event.preventDefault();

        if (!importarXML && importForm.data.nota.length !== 44) {
            importForm.setError("nota", "A chave da NFe deve conter exatamente 44 números.");
            return;
        }

        if (importarXML && !importForm.data.nota) {
            importForm.setError("nota", "Selecione um arquivo XML para importar.");
            return;
        }

        importForm.post(route("importar_produtos"), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                fecharImportModal();
            },
        });
    }

    return (
        <>
            <Head title="Entradas de NFe" />

            <div
                className="container-fluid py-4"
                style={{
                    background: "#f4f7fb",
                    minHeight: "100vh",
                }}
            >
                <div
                    className="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center mb-4 p-4 rounded-4 shadow-sm"
                    style={{
                        background:
                            "linear-gradient(135deg, #0f172a 0%, #1e293b 100%)",
                        border: "1px solid rgba(255,255,255,0.05)",
                    }}
                >
                    <div>
                        <div className="d-flex align-items-center mb-2">
                            <div
                                className="d-flex align-items-center justify-content-center mr-3"
                                style={{
                                    width: 56,
                                    height: 56,
                                    borderRadius: 16,
                                    background: "rgba(34,211,238,0.15)",
                                    color: "#22d3ee",
                                    fontSize: 22,
                                }}
                            >
                                <i className="fas fa-file-invoice"></i>
                            </div>

                            <div>
                                <h1
                                    className="mb-1 font-weight-bold"
                                    style={{ color: "#fff", fontSize: "2rem" }}
                                >
                                    Notas Fiscais de Entrada
                                </h1>

                                <p
                                    className="mb-0"
                                    style={{ color: "#cbd5e1" }}
                                >
                                    Gerencie importações, acompanhe fornecedores
                                    e controle entradas fiscais.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="mt-4 mt-xl-0 d-flex flex-wrap gap-2">
                        <button
                            type="button"
                            className="btn"
                            style={{
                                background: "#22d3ee",
                                color: "#0f172a",
                                borderRadius: 12,
                                padding: "10px 18px",
                                fontWeight: 600,
                                border: "none",
                                boxShadow: "0 10px 25px rgba(34,211,238,0.25)",
                            }}
                            onClick={() => setShowImportModal(true)}
                        >
                            <i className="fas fa-file-import mr-2"></i>
                            Importar NFe
                        </button>

                        <Link
                            href={route("entradas.manual")}
                            className="btn"
                            style={{
                                background: "#16a34a",
                                color: "#fff",
                                borderRadius: 12,
                                padding: "10px 18px",
                                fontWeight: 600,
                            }}
                        >
                            <i className="fas fa-plus mr-2"></i>
                            Nova Entrada
                        </Link>

                        <Link
                            href={route("produto.index")}
                            className="btn"
                            style={{
                                background: "rgba(255,255,255,0.08)",
                                color: "#fff",
                                borderRadius: 12,
                                padding: "10px 18px",
                                fontWeight: 600,
                                border: "1px solid rgba(255,255,255,0.08)",
                            }}
                        >
                            <i className="fas fa-arrow-left mr-2"></i>
                            Produtos
                        </Link>
                    </div>
                </div>

                <div className="row mb-4">
                    <div
                        className="shadow-sm"
                        style={{
                            borderRadius: 20,
                            overflow: "hidden",
                            background: "#fff",
                            border: "1px solid #e2e8f0",
                        }}
                    >
                        <div className="inner p-4">
                            <h3
                                className="font-weight-bold mb-1"
                                style={{ color: "#0f172a" }}
                            >
                                {resumo.totalNotas}
                            </h3>
                            <p className="mb-0 text-muted">Total de Notas</p>
                        </div>
                        <div className="icon" style={{ opacity: 0.08 }}>
                            <i className="fas fa-file-invoice"></i>
                        </div>
                    </div>

                    <div
                        className="shadow-sm"
                        style={{
                            borderRadius: 20,
                            overflow: "hidden",
                            background: "#fff",
                            border: "1px solid #e2e8f0",
                        }}
                    >
                        <div className="inner p-4">
                            <h3
                                className="font-weight-bold mb-1"
                                style={{ color: "#0f172a" }}
                            >
                                {formatCurrency(resumo.valorTotal)}
                            </h3>
                            <p className="mb-0 text-muted">Valor Total</p>
                        </div>
                        <div className="icon" style={{ opacity: 0.08 }}>
                            <i className="fas fa-dollar-sign"></i>
                        </div>
                    </div>

                    <div
                        className="shadow-sm"
                        style={{
                            borderRadius: 20,
                            overflow: "hidden",
                            background: "#fff",
                            border: "1px solid #e2e8f0",
                        }}
                    >
                        <div className="inner p-4">
                            <h3
                                className="font-weight-bold mb-1"
                                style={{ color: "#0f172a" }}
                            >
                                {resumo.fornecedores}
                            </h3>
                            <p className="mb-0 text-muted">Fornecedores</p>
                        </div>
                        <div className="icon" style={{ opacity: 0.08 }}>
                            <i className="fas fa-truck"></i>
                        </div>
                    </div>
                </div>

                <div
                    className="card border-0 shadow-sm mb-4"
                    style={{ borderRadius: 20 }}
                >
                    <div className="card-body">
                        <form onSubmit={submitFiltro}>
                            <div className="row">
                                <div className="col-md-4 mb-3">
                                    <label>Data Inicial</label>
                                    <input
                                        type="date"
                                        className="form-control"
                                        value={filtroForm.data.data_inicio}
                                        onChange={(e) =>
                                            filtroForm.setData(
                                                "data_inicio",
                                                e.target.value
                                            )
                                        }
                                    />
                                </div>

                                <div className="col-md-4 mb-3">
                                    <label>Data Final</label>
                                    <input
                                        type="date"
                                        className="form-control"
                                        value={filtroForm.data.data_fim}
                                        onChange={(e) =>
                                            filtroForm.setData(
                                                "data_fim",
                                                e.target.value
                                            )
                                        }
                                    />
                                </div>

                                <div className="col-md-4 d-flex align-items-end mb-3">
                                    <button
                                        type="submit"
                                        className="btn btn-primary mr-2"
                                    >
                                        <i className="fas fa-search mr-1"></i>
                                        Filtrar
                                    </button>

                                    <button
                                        type="button"
                                        className="btn btn-secondary"
                                        onClick={limparFiltros}
                                    >
                                        Limpar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div
                    className="card border-0 shadow-sm"
                    style={{ borderRadius: 20, overflow: "hidden" }}
                >
                    <div className="card-body table-responsive p-0">
                        <table className="table table-hover mb-0 align-middle">
                            <thead
                                style={{
                                    background: "#f8fafc",
                                    borderBottom: "1px solid #e2e8f0",
                                }}
                            >
                                <tr>
                                    <th>Emissão</th>
                                    <th>Entrada</th>
                                    <th>Número</th>
                                    <th>Fornecedor</th>
                                    <th>Valor</th>
                                    <th width="180">Ações</th>
                                </tr>
                            </thead>

                            <tbody>
                                {entradas.length > 0 ? (
                                    entradas.map((entrada) => (
                                        <tr
                                            key={entrada.id}
                                            style={{
                                                borderBottom:
                                                    "1px solid #f1f5f9",
                                            }}
                                        >
                                            <td>{entrada.dataEmissao}</td>
                                            <td>{entrada.dataEntrada}</td>
                                            <td>{entrada.numeroNota}</td>
                                            <td>{entrada.fornecedor}</td>
                                            <td>
                                                {formatCurrency(entrada.valor)}
                                            </td>
                                            <td>
                                                <div className="d-flex" style={{ gap: 8 }}>
                                                    <button
                                                        type="button"
                                                        className="btn btn-sm"
                                                        style={{
                                                            background: "#0f172a",
                                                            color: "#fff",
                                                            borderRadius: 10,
                                                            padding: "8px 12px",
                                                        }}
                                                        onClick={() => visualizarEntrada(entrada.id)}
                                                    >
                                                        <i className="fas fa-eye"></i>
                                                    </button>

                                                    <button
                                                        type="button"
                                                        className="btn btn-sm"
                                                        style={{
                                                            background:
                                                                "#dc2626",
                                                            color: "#fff",
                                                            borderRadius: 10,
                                                            padding: "8px 12px",
                                                        }}
                                                        onClick={() =>
                                                            excluirEntrada(
                                                                entrada.id
                                                            )
                                                        }
                                                    >
                                                        <i className="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td
                                            colSpan="6"
                                            className="text-center py-4 text-muted"
                                        >
                                            Nenhuma entrada encontrada.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {showImportModal && (
                    <div
                        className="modal show d-block"
                        tabIndex="-1"
                        style={{
                            backgroundColor: "rgba(15,23,42,0.75)",
                            backdropFilter: "blur(6px)",
                            zIndex: 9999,
                        }}
                    >
                        <div className="modal-dialog modal-dialog-centered">
                            <div
                                className="modal-content border-0"
                                style={{
                                    borderRadius: 24,
                                    overflow: "hidden",
                                }}
                            >
                                <div
                                    className="modal-header border-0"
                                    style={{
                                        background: "#0f172a",
                                        color: "#fff",
                                        padding: "1.5rem",
                                    }}
                                >
                                    <h5 className="modal-title font-weight-bold">
                                        Importar NFe
                                    </h5>

                                    <button
                                        type="button"
                                        className="close"
                                        onClick={fecharImportModal}
                                    >
                                        <span>&times;</span>
                                    </button>
                                </div>

                                <form onSubmit={submitImportacao}>
                                    <div className="modal-body p-4">
                                        <div
                                            className="p-3 mb-4"
                                            style={{
                                                background: "#f8fafc",
                                                border: "1px solid #e2e8f0",
                                                borderRadius: 16,
                                            }}
                                        >
                                            <div className="d-flex align-items-start">
                                                <div
                                                    className="d-flex align-items-center justify-content-center mr-3"
                                                    style={{
                                                        width: 42,
                                                        height: 42,
                                                        borderRadius: 12,
                                                        background: "#e0f2fe",
                                                        color: "#0369a1",
                                                        flexShrink: 0,
                                                    }}
                                                >
                                                    <i className="fas fa-file-invoice"></i>
                                                </div>

                                                <div>
                                                    <strong style={{ color: "#0f172a" }}>
                                                        Importação de Nota Fiscal Eletrônica
                                                    </strong>
                                                    <p className="text-muted mb-0 mt-1">
                                                        Informe a chave de 44 dígitos ou envie o arquivo XML da NFe.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div className="form-group mb-3">
                                            <label className="font-weight-bold" htmlFor="nota">
                                                {importarXML ? "Arquivo XML" : "Chave da NFe"}
                                            </label>

                                            {!importarXML ? (
                                                <>
                                                    <input
                                                        type="text"
                                                        className={`form-control ${importForm.errors.nota ? "is-invalid" : ""}`}
                                                        id="nota"
                                                        name="nota"
                                                        placeholder="Digite os 44 números da chave"
                                                        minLength="44"
                                                        maxLength="44"
                                                        value={importForm.data.nota}
                                                        onChange={alterarChaveNFe}
                                                        required
                                                        style={{
                                                            height: 48,
                                                            borderRadius: 12,
                                                            borderColor: importForm.errors.nota ? "#dc3545" : "#e2e8f0",
                                                        }}
                                                    />
                                                    <small className="text-muted d-block mt-2">
                                                        {importForm.data.nota.length}/44 dígitos informados.
                                                    </small>
                                                </>
                                            ) : (
                                                <input
                                                    type="file"
                                                    className={`form-control ${importForm.errors.nota ? "is-invalid" : ""}`}
                                                    id="nota"
                                                    name="nota"
                                                    accept=".xml,text/xml,application/xml"
                                                    onChange={alterarArquivoXML}
                                                    required
                                                    style={{
                                                        height: 48,
                                                        borderRadius: 12,
                                                        borderColor: importForm.errors.nota ? "#dc3545" : "#e2e8f0",
                                                    }}
                                                />
                                            )}

                                            {importForm.errors.nota && (
                                                <div className="invalid-feedback d-block">
                                                    {importForm.errors.nota}
                                                </div>
                                            )}
                                        </div>

                                        <div
                                            className="custom-control custom-switch mt-3"
                                            style={{ userSelect: "none" }}
                                        >
                                            <input
                                                type="checkbox"
                                                className="custom-control-input"
                                                id="type"
                                                name="type"
                                                checked={importarXML}
                                                onChange={alterarTipoImportacao}
                                            />
                                            <label
                                                className="custom-control-label"
                                                htmlFor="type"
                                            >
                                                Importar usando arquivo XML
                                            </label>
                                        </div>
                                    </div>

                                    <div className="modal-footer border-0 px-4 pb-4">
                                        <button
                                            type="button"
                                            className="btn"
                                            style={{
                                                background: "#e2e8f0",
                                                borderRadius: 12,
                                                padding: "10px 18px",
                                                fontWeight: 600,
                                            }}
                                            onClick={fecharImportModal}
                                            disabled={importForm.processing}
                                        >
                                            Fechar
                                        </button>

                                        <button
                                            type="submit"
                                            className="btn"
                                            style={{
                                                background: "#22d3ee",
                                                color: "#0f172a",
                                                borderRadius: 12,
                                                padding: "10px 18px",
                                                fontWeight: 700,
                                                border: "none",
                                                minWidth: 145,
                                            }}
                                            disabled={importForm.processing}
                                        >
                                            {importForm.processing ? (
                                                <>
                                                    <span className="spinner-border spinner-border-sm mr-2"></span>
                                                    Importando
                                                </>
                                            ) : (
                                                <>
                                                    <i className="fas fa-check mr-2"></i>
                                                    Importar Nota
                                                </>
                                            )}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                )}
                {showDetalhesModal && (
                    <div
                        className="modal show d-block"
                        tabIndex="-1"
                        style={{
                            backgroundColor: "rgba(15,23,42,0.75)",
                            backdropFilter: "blur(6px)",
                            zIndex: 10000,
                        }}
                    >
                        <div className="modal-dialog modal-dialog-centered modal-xl" style={{ maxHeight: "88vh" }}>
                            <div
                                className="modal-content border-0"
                                style={{
                                    borderRadius: 24,
                                    overflow: "hidden",
                                    maxHeight: "88vh",
                                }}
                            >
                                <div
                                    className="modal-header border-0"
                                    style={{
                                        background: "#0f172a",
                                        color: "#fff",
                                        padding: "1.5rem",
                                    }}
                                >
                                    <div>
                                        <h5 className="modal-title font-weight-bold mb-1">
                                            Detalhes da Entrada
                                        </h5>
                                        <small style={{ color: "#cbd5e1" }}>
                                            Visualização da nota e produtos importados.
                                        </small>
                                    </div>

                                    <button
                                        type="button"
                                        className="close text-white"
                                        onClick={fecharDetalhesModal}
                                    >
                                        <span>&times;</span>
                                    </button>
                                </div>

                                <div
                                    className="modal-body p-4"
                                    style={{
                                        background: "#f8fafc",
                                        maxHeight: "62vh",
                                        overflowY: "auto",
                                    }}
                                >
                                    {loadingDetalhes ? (
                                        <div className="text-center py-5">
                                            <div className="spinner-border text-info"></div>
                                            <p className="mt-3 text-muted mb-0">
                                                Carregando detalhes da entrada...
                                            </p>
                                        </div>
                                    ) : entradaSelecionada?.error ? (
                                        <div className="alert alert-danger mb-0">
                                            {entradaSelecionada.message || "Erro ao carregar os detalhes."}
                                        </div>
                                    ) : entradaSelecionada ? (
                                        <>
                                            <div className="row mb-4">
                                                <div className="col-md-4 mb-3">
                                                    <div
                                                        className="p-3 h-100"
                                                        style={{
                                                            background: "#fff",
                                                            borderRadius: 16,
                                                            border: "1px solid #e2e8f0",
                                                        }}
                                                    >
                                                        <small className="text-muted d-block mb-1">Número</small>
                                                        <strong style={{ color: "#0f172a" }}>
                                                            {entradaSelecionada.numeroNota || entradaSelecionada.numero || entradaSelecionada.nNF || "-"}
                                                        </strong>
                                                    </div>
                                                </div>

                                                <div className="col-md-4 mb-3">
                                                    <div
                                                        className="p-3 h-100"
                                                        style={{
                                                            background: "#fff",
                                                            borderRadius: 16,
                                                            border: "1px solid #e2e8f0",
                                                        }}
                                                    >
                                                        <small className="text-muted d-block mb-1">Fornecedor</small>
                                                        <strong style={{ color: "#0f172a" }}>
                                                            {entradaSelecionada.fornecedor || entradaSelecionada.emitente || entradaSelecionada.xNome || "-"}
                                                        </strong>
                                                    </div>
                                                </div>

                                                <div className="col-md-4 mb-3">
                                                    <div
                                                        className="p-3 h-100"
                                                        style={{
                                                            background: "#fff",
                                                            borderRadius: 16,
                                                            border: "1px solid #e2e8f0",
                                                        }}
                                                    >
                                                        <small className="text-muted d-block mb-1">Valor</small>
                                                        <strong style={{ color: "#0f172a" }}>
                                                            {formatCurrency(entradaSelecionada.valor || entradaSelecionada.valorTotal || entradaSelecionada.vNF)}
                                                        </strong>
                                                    </div>
                                                </div>

                                                <div className="col-12">
                                                    <div
                                                        className="p-3"
                                                        style={{
                                                            background: "#fff",
                                                            borderRadius: 16,
                                                            border: "1px solid #e2e8f0",
                                                        }}
                                                    >
                                                        <small className="text-muted d-block mb-1">Chave da NFe</small>
                                                        <div style={{ wordBreak: "break-all", color: "#475569" }}>
                                                            {entradaSelecionada.chave || entradaSelecionada.chave_nfe || entradaSelecionada.chNFe || "-"}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                className="card border-0 shadow-sm mb-0"
                                                style={{ borderRadius: 18, overflow: "hidden" }}
                                            >
                                                <div
                                                    className="card-header border-0"
                                                    style={{ background: "#fff" }}
                                                >
                                                    <div className="d-flex flex-column flex-md-row justify-content-between align-items-md-center" style={{ gap: 12 }}>
                                                        <div>
                                                            <h6 className="mb-1 font-weight-bold" style={{ color: "#0f172a" }}>
                                                                Produtos da Nota
                                                            </h6>
                                                            <small className="text-muted">
                                                                Filtre por código, descrição ou quantidade.
                                                            </small>
                                                        </div>

                                                        <span
                                                            className="badge"
                                                            style={{
                                                                background: "#e0f2fe",
                                                                color: "#0369a1",
                                                                borderRadius: 999,
                                                                padding: "8px 12px",
                                                            }}
                                                        >
                                                            {getProdutosFiltradosModal().length} de {getProdutosEntrada(entradaSelecionada).length} item(ns)
                                                        </span>
                                                    </div>

                                                    <div className="mt-3">
                                                        <div className="input-group">
                                                            <div className="input-group-prepend">
                                                                <span className="input-group-text" style={{ background: "#f8fafc", borderColor: "#e2e8f0" }}>
                                                                    <i className="fas fa-search text-muted"></i>
                                                                </span>
                                                            </div>
                                                            <input
                                                                type="text"
                                                                className="form-control"
                                                                placeholder="Buscar produto dentro da nota..."
                                                                value={filtroProdutoModal}
                                                                onChange={(event) => setFiltroProdutoModal(event.target.value)}
                                                                style={{ borderColor: "#e2e8f0" }}
                                                            />
                                                            {filtroProdutoModal && (
                                                                <div className="input-group-append">
                                                                    <button
                                                                        type="button"
                                                                        className="btn"
                                                                        style={{ background: "#e2e8f0", color: "#0f172a" }}
                                                                        onClick={() => setFiltroProdutoModal("")}
                                                                    >
                                                                        Limpar
                                                                    </button>
                                                                </div>
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div
                                                    className="table-responsive"
                                                    style={{
                                                        maxHeight: 300,
                                                        overflowY: "auto",
                                                        borderTop: "1px solid #e2e8f0",
                                                    }}
                                                >
                                                    <table className="table table-hover mb-0">
                                                        <thead
                                                            style={{
                                                                background: "#f8fafc",
                                                                position: "sticky",
                                                                top: 0,
                                                                zIndex: 2,
                                                            }}
                                                        >
                                                            <tr>
                                                                <th>Código</th>
                                                                <th>Produto</th>
                                                                <th className="text-right">Qtd</th>
                                                                <th className="text-right">Unitário</th>
                                                                <th className="text-right">Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {getProdutosFiltradosModal().length > 0 ? (
                                                                getProdutosFiltradosModal().map((produto, index) => {
                                                                    const codigo = getCodigoProduto(produto);
                                                                    const descricao = getDescricaoProduto(produto);
                                                                    const quantidade = getValorProduto(produto, ["quantidade", "qCom", "qtd", "qtde"]);
                                                                    const valorUnitario = getValorUnitarioProduto(produto);
                                                                    const valorTotal = getTotalProduto(produto);

                                                                    return (
                                                                        <tr key={`${codigo || "produto"}-${index}`}>
                                                                            <td>{String(codigo || "-")}</td>
                                                                            <td style={{ minWidth: 260 }}>
                                                                                <strong style={{ color: "#0f172a" }}>
                                                                                    {String(descricao || "Produto sem descrição")}
                                                                                </strong>
                                                                            </td>
                                                                            <td className="text-right">{String(quantidade || "-")}</td>
                                                                            <td className="text-right">
                                                                                {valorUnitario !== "" ? formatCurrency(valorUnitario) : "-"}
                                                                            </td>
                                                                            <td className="text-right font-weight-bold">
                                                                                {valorTotal !== "" ? formatCurrency(valorTotal) : "-"}
                                                                            </td>
                                                                        </tr>
                                                                    );
                                                                })
                                                            ) : (
                                                                <tr>
                                                                    <td colSpan="5" className="text-center text-muted py-4">
                                                                        {filtroProdutoModal ? "Nenhum produto encontrado com esse filtro." : "Nenhum produto encontrado no JSON retornado."}
                                                                    </td>
                                                                </tr>
                                                            )}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </>
                                    ) : null}
                                </div>

                                <div className="modal-footer border-0 px-4 pb-4" style={{ background: "#f8fafc" }}>
                                    <button
                                        type="button"
                                        className="btn"
                                        style={{
                                            background: "#e2e8f0",
                                            borderRadius: 12,
                                            padding: "10px 18px",
                                            fontWeight: 600,
                                        }}
                                        onClick={fecharDetalhesModal}
                                    >
                                        Fechar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}
