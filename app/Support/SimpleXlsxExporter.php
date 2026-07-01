<?php

namespace App\Support;

use Illuminate\Http\Response;
use Illuminate\Support\Str;

class SimpleXlsxExporter
{
    public static function download(string $fileName, array $headers, array $rows): Response
    {
        $content = self::build($headers, $rows);

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'public',
        ]);
    }

    public static function build(array $headers, array $rows): string
    {
        $sheetRows = [$headers, ...$rows];
        $files = [
            '[Content_Types].xml' => self::contentTypesXml(),
            '_rels/.rels' => self::rootRelationshipsXml(),
            'docProps/app.xml' => self::appPropertiesXml(),
            'docProps/core.xml' => self::corePropertiesXml(),
            'xl/workbook.xml' => self::workbookXml(),
            'xl/_rels/workbook.xml.rels' => self::workbookRelationshipsXml(),
            'xl/styles.xml' => self::stylesXml(),
            'xl/worksheets/sheet1.xml' => self::worksheetXml($sheetRows),
        ];

        return self::zip($files);
    }

    private static function worksheetXml(array $rows): string
    {
        $xmlRows = [];

        foreach ($rows as $rowIndex => $row) {
            $cells = [];
            $styleIndex = $rowIndex === 0 ? '1' : '0';

            foreach (array_values($row) as $columnIndex => $value) {
                $reference = self::columnName($columnIndex + 1).($rowIndex + 1);
                $escapedValue = self::xml((string) $value);

                $cells[] = '<c r="'.$reference.'" t="inlineStr" s="'.$styleIndex.'"><is><t>'.$escapedValue.'</t></is></c>';
            }

            $xmlRows[] = '<row r="'.($rowIndex + 1).'">'.implode('', $cells).'</row>';
        }

        $dimension = 'A1:'.self::columnName(max(count($rows[0] ?? []), 1)).max(count($rows), 1);

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<dimension ref="'.$dimension.'"/>'
            .'<sheetViews><sheetView workbookViewId="0"/></sheetViews>'
            .'<sheetFormatPr defaultRowHeight="15"/>'
            .'<cols>'
            .self::columnDefinitions(count($rows[0] ?? []))
            .'</cols>'
            .'<sheetData>'.implode('', $xmlRows).'</sheetData>'
            .'</worksheet>';
    }

    private static function columnDefinitions(int $count): string
    {
        $columns = [];

        for ($i = 1; $i <= max($count, 1); $i++) {
            $columns[] = '<col min="'.$i.'" max="'.$i.'" width="20" customWidth="1"/>';
        }

        return implode('', $columns);
    }

    private static function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            .'<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>';
    }

    private static function rootRelationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            .'<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            .'</Relationships>';
    }

    private static function appPropertiesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            .'<Application>Laravel</Application>'
            .'<HeadingPairs><vt:vector size="2" baseType="variant"><vt:variant><vt:lpstr>Worksheets</vt:lpstr></vt:variant><vt:variant><vt:i4>1</vt:i4></vt:variant></vt:vector></HeadingPairs>'
            .'<TitlesOfParts><vt:vector size="1" baseType="lpstr"><vt:lpstr>Cadastros</vt:lpstr></vt:vector></TitlesOfParts>'
            .'</Properties>';
    }

    private static function corePropertiesXml(): string
    {
        $created = now()->utc()->format('Y-m-d\TH:i:s\Z');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            .'<dc:creator>Laravel</dc:creator>'
            .'<cp:lastModifiedBy>Laravel</cp:lastModifiedBy>'
            .'<dcterms:created xsi:type="dcterms:W3CDTF">'.$created.'</dcterms:created>'
            .'<dcterms:modified xsi:type="dcterms:W3CDTF">'.$created.'</dcterms:modified>'
            .'</cp:coreProperties>';
    }

    private static function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="Cadastros" sheetId="1" r:id="rId1"/></sheets>'
            .'</workbook>';
    }

    private static function workbookRelationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>';
    }

    private static function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
            .'<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            .'<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/></cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>';
    }

    private static function zip(array $files): string
    {
        $data = '';
        $directory = '';
        $offset = 0;

        foreach ($files as $name => $content) {
            $name = str_replace('\\', '/', $name);
            $crc = crc32($content);
            $size = strlen($content);
            [$time, $date] = self::dosDateTime();

            $localHeader = pack('VvvvvvVVVvv',
                0x04034b50,
                20,
                0,
                0,
                $time,
                $date,
                $crc,
                $size,
                $size,
                strlen($name),
                0
            );

            $data .= $localHeader.$name.$content;

            $directory .= pack('VvvvvvvVVVvvvvvVV',
                0x02014b50,
                20,
                20,
                0,
                0,
                $time,
                $date,
                $crc,
                $size,
                $size,
                strlen($name),
                0,
                0,
                0,
                0,
                0,
                $offset
            ).$name;

            $offset += strlen($localHeader) + strlen($name) + $size;
        }

        $end = pack('VvvvvVVv',
            0x06054b50,
            0,
            0,
            count($files),
            count($files),
            strlen($directory),
            strlen($data),
            0
        );

        return $data.$directory.$end;
    }

    private static function dosDateTime(): array
    {
        $time = now();

        $dosTime = ($time->hour << 11) | ($time->minute << 5) | intdiv($time->second, 2);
        $dosDate = (($time->year - 1980) << 9) | ($time->month << 5) | $time->day;

        return [$dosTime, $dosDate];
    }

    private static function columnName(int $index): string
    {
        $name = '';

        while ($index > 0) {
            $index--;
            $name = chr(65 + ($index % 26)).$name;
            $index = intdiv($index, 26);
        }

        return $name;
    }

    private static function xml(string $value): string
    {
        return Str::of($value)
            ->replace('&', '&amp;')
            ->replace('<', '&lt;')
            ->replace('>', '&gt;')
            ->replace('"', '&quot;')
            ->replace("'", '&apos;')
            ->toString();
    }
}
