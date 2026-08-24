<?php

namespace App\Console\Commands;

use App\Models\NFSeIndOp;
use App\Models\NFSeNbs;
use App\Models\NFSeRegraIncidencia;
use App\Models\NFSeServicoNacional;
use Illuminate\Console\Command;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class ImportarDominiosNFSe extends Command
{
    protected $signature = 'nfse:importar-dominios
        {--anexo-b= : Caminho do Anexo B com lista nacional de servicos e NBS}
        {--anexo-c= : Caminho do Anexo C com codigos IndOp IBS/CBS}
        {--anexo-i= : Caminho do Anexo I com regras de incidencia por servico}
        {--fonte-versao=v1.01-prodrest : Identificador da versao/fonte dos anexos}';

    protected $description = 'Importa tabelas de dominio da NFS-e Nacional a partir dos anexos XLSX versionados';

    private const XLSX_NS = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
    private const REL_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    private const PACKAGE_REL_NS = 'http://schemas.openxmlformats.org/package/2006/relationships';

    public function handle(): int
    {
        $fonteVersao = (string) $this->option('fonte-versao');
        $anexoB = $this->option('anexo-b') ?: resource_path('domains/nfse/v1.01/anexo_b-nbs2-lista_servico_nacional-snnfse-prodrest-v1-01-20260122.xlsx');
        $anexoC = $this->option('anexo-c') ?: resource_path('domains/nfse/v1.01/anexo_c-indop_ibscbs-snnfse-prodrest-v1-01-20260122.xlsx');
        $anexoI = $this->option('anexo-i') ?: resource_path('domains/nfse/v1.01/anexo_i-sefin_adn-dps_nfse-snnfse-prodrest-v1-01-20260209.xlsx');

        $this->info('Importando dominios NFS-e...');

        $servicos = $this->importarServicosNacionais($anexoB, $fonteVersao);
        $nbs = $this->importarNbs($anexoB, $fonteVersao);
        $indOps = $this->importarIndOps($anexoC, $fonteVersao);
        $regras = $this->importarRegrasIncidencia($anexoI, $fonteVersao);

        $this->newLine();
        $this->info('Resumo:');
        $this->info("  Servicos nacionais: {$servicos}");
        $this->info("  Codigos NBS: {$nbs}");
        $this->info("  Codigos IndOp: {$indOps}");
        $this->info("  Regras de incidencia: {$regras}");

        return self::SUCCESS;
    }

    private function importarServicosNacionais(string $path, string $fonteVersao): int
    {
        $rows = $this->readSheet($path, 'LISTA.SERV.NAC.');
        $count = 0;

        foreach (array_slice($rows, 1) as $row) {
            $codigo = $this->cell($row, 0);
            if (! preg_match('/^\d{5}$/', $codigo)) {
                continue;
            }

            NFSeServicoNacional::updateOrCreate(
                ['codigo' => $codigo],
                [
                    'item' => $this->intOrNull($this->cell($row, 1)),
                    'subitem' => $this->intOrNull($this->cell($row, 2)),
                    'desdobro' => $this->intOrNull($this->cell($row, 3)),
                    'descricao' => $this->cell($row, 4),
                    'fonte_versao' => $fonteVersao,
                ]
            );

            $count++;
        }

        return $count;
    }

    private function importarNbs(string $path, string $fonteVersao): int
    {
        $rows = $this->readSheet($path, 'LISTA.NBS_v2.0');
        $count = 0;

        foreach (array_slice($rows, 1) as $row) {
            $codigo = $this->cell($row, 0);
            $descricao = $this->cell($row, 1);
            if ($codigo === '' || $descricao === '') {
                continue;
            }

            NFSeNbs::updateOrCreate(
                ['codigo' => $codigo],
                [
                    'descricao' => $descricao,
                    'fonte_versao' => $fonteVersao,
                ]
            );

            $count++;
        }

        return $count;
    }

    private function importarIndOps(string $path, string $fonteVersao): int
    {
        $rows = $this->readSheet($path, 'IndOp');
        $count = 0;
        $carry = [];

        foreach (array_slice($rows, 1) as $row) {
            for ($i = 0; $i <= 3; $i++) {
                $value = $this->cell($row, $i);
                if ($value !== '') {
                    $carry[$i] = $value;
                }
            }

            $codigo = $this->cell($row, 6);
            if (! preg_match('/^\d{6}$/', $codigo)) {
                continue;
            }

            NFSeIndOp::updateOrCreate(
                ['codigo' => $codigo],
                [
                    'artigo' => $carry[0] ?? null,
                    'tipo_operacao' => $carry[1] ?? null,
                    'local_operacao' => $carry[2] ?? null,
                    'caracteristica_fornecimento' => $carry[3] ?? null,
                    'grupo' => $this->cell($row, 4) ?: null,
                    'subgrupo' => $this->cell($row, 5) ?: null,
                    'local_fornecimento' => $this->cell($row, 7) ?: null,
                    'campo_layout' => $this->cell($row, 8) ?: null,
                    'fonte_versao' => $fonteVersao,
                ]
            );

            $count++;
        }

        return $count;
    }

    private function importarRegrasIncidencia(string $path, string $fonteVersao): int
    {
        $rows = $this->readSheet($path, 'MUN.INCID_INFO.SERV.');
        $count = 0;

        foreach ($rows as $row) {
            $codigo = $this->cell($row, 0);
            if (! preg_match('/^\d{5}$/', $codigo)) {
                continue;
            }

            NFSeRegraIncidencia::updateOrCreate(
                ['cTribNac' => $codigo],
                [
                    'descricao' => $this->cell($row, 1) ?: null,
                    'li_estabelecimento_prestador' => $this->isMarked($this->cell($row, 2)),
                    'li_local_prestacao' => $this->isMarked($this->cell($row, 3)),
                    'li_estabelecimento_tomador' => $this->isMarked($this->cell($row, 4)),
                    'li_estabelecimento_emitente' => $this->isMarked($this->cell($row, 5)),
                    'exige_info_servico' => $this->isMarked($this->cell($row, 6)),
                    'informacoes_complementares' => $this->cell($row, 7) ?: null,
                    'fonte_versao' => $fonteVersao,
                ]
            );

            $count++;
        }

        return $count;
    }

    private function readSheet(string $path, string $sheetName): array
    {
        if (! is_file($path)) {
            throw new RuntimeException("Arquivo XLSX nao encontrado: {$path}");
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException("Nao foi possivel abrir o XLSX: {$path}");
        }

        $sharedStrings = $this->loadSharedStrings($zip);
        $sheetPath = $this->resolveSheetPath($zip, $sheetName);
        $sheetXml = $this->xmlFromZip($zip, $sheetPath);
        $sheetXml->registerXPathNamespace('m', self::XLSX_NS);

        $rows = [];
        foreach ($sheetXml->xpath('//m:sheetData/m:row') as $rowXml) {
            $rowXml->registerXPathNamespace('m', self::XLSX_NS);
            $row = [];
            foreach ($rowXml->xpath('m:c') as $cellXml) {
                $cellRef = (string) $cellXml['r'];
                $column = $this->columnIndex($cellRef);
                while (count($row) < $column) {
                    $row[] = '';
                }
                $row[$column] = $this->cellValue($cellXml, $sharedStrings);
            }

            $rows[] = $this->trimRight($row);
        }

        $zip->close();

        return $rows;
    }

    private function loadSharedStrings(ZipArchive $zip): array
    {
        if ($zip->locateName('xl/sharedStrings.xml') === false) {
            return [];
        }

        $xml = $this->xmlFromZip($zip, 'xl/sharedStrings.xml');
        $xml->registerXPathNamespace('m', self::XLSX_NS);
        $strings = [];

        foreach ($xml->xpath('//m:si') as $si) {
            $si->registerXPathNamespace('m', self::XLSX_NS);
            $parts = [];
            foreach ($si->xpath('.//m:t') as $text) {
                $parts[] = (string) $text;
            }
            $strings[] = implode('', $parts);
        }

        return $strings;
    }

    private function resolveSheetPath(ZipArchive $zip, string $sheetName): string
    {
        $workbook = $this->xmlFromZip($zip, 'xl/workbook.xml');
        $workbook->registerXPathNamespace('m', self::XLSX_NS);
        $rels = $this->xmlFromZip($zip, 'xl/_rels/workbook.xml.rels');
        $rels->registerXPathNamespace('r', self::PACKAGE_REL_NS);

        $targets = [];
        foreach ($rels->xpath('//r:Relationship') as $relationship) {
            $attrs = $relationship->attributes();
            $targets[(string) $attrs['Id']] = (string) $attrs['Target'];
        }

        foreach ($workbook->xpath('//m:sheets/m:sheet') as $sheet) {
            if ((string) $sheet['name'] !== $sheetName) {
                continue;
            }

            $relationId = (string) $sheet->attributes(self::REL_NS)['id'];
            $target = $targets[$relationId] ?? null;
            if (! $target) {
                break;
            }

            return str_starts_with($target, 'xl/')
                ? $target
                : 'xl/' . ltrim($target, '/');
        }

        throw new RuntimeException("Planilha '{$sheetName}' nao encontrada no XLSX.");
    }

    private function xmlFromZip(ZipArchive $zip, string $path): SimpleXMLElement
    {
        $contents = $zip->getFromName($path);
        if ($contents === false) {
            throw new RuntimeException("Arquivo interno nao encontrado no XLSX: {$path}");
        }

        $xml = simplexml_load_string($contents);
        if (! $xml instanceof SimpleXMLElement) {
            throw new RuntimeException("XML interno invalido no XLSX: {$path}");
        }

        return $xml;
    }

    private function cellValue(SimpleXMLElement $cellXml, array $sharedStrings): string
    {
        $cellXml->registerXPathNamespace('m', self::XLSX_NS);
        $type = (string) $cellXml['t'];

        if ($type === 'inlineStr') {
            $parts = [];
            foreach ($cellXml->xpath('.//m:t') as $text) {
                $parts[] = (string) $text;
            }
            return trim(implode('', $parts));
        }

        $valueNodes = $cellXml->xpath('m:v');
        $value = isset($valueNodes[0]) ? (string) $valueNodes[0] : '';

        if ($type === 's' && $value !== '') {
            return trim((string) ($sharedStrings[(int) $value] ?? ''));
        }

        return trim($value);
    }

    private function columnIndex(string $cellRef): int
    {
        if (! preg_match('/^([A-Z]+)/', $cellRef, $matches)) {
            return 0;
        }

        $index = 0;
        foreach (str_split($matches[1]) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return $index - 1;
    }

    private function cell(array $row, int $index): string
    {
        return trim((string) ($row[$index] ?? ''));
    }

    private function intOrNull(string $value): ?int
    {
        return preg_match('/^\d+$/', $value) ? (int) $value : null;
    }

    private function isMarked(string $value): bool
    {
        $normalized = mb_strtoupper(trim($value));

        return in_array($normalized, ['X', 'S', 'SIM', 'OBRIG.', 'OBRIGATORIO', 'OBRIGATORIO.', 'OBRIGATÓRIO', 'OBRIGATÓRIO.'], true);
    }

    private function trimRight(array $row): array
    {
        while ($row && end($row) === '') {
            array_pop($row);
        }

        return $row;
    }
}
