<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\GenerateQrTokenRequest;
use App\Models\Site;
use App\Models\QrToken;
use App\Services\QrCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QrTokenController extends Controller
{
    public function index(Request $request, QrCodeService $service)
    {
        $this->authorize('viewAny', QrToken::class);
        $site = Site::query()->whereKey($request->integer('site_id', 1))->where('is_active', true)->firstOrFail();
        $currentQr = $service->current($site);
        $tokens = $site->qrTokens()->with('creator')->latest('valid_on')->latest()->paginate(20)->withQueryString();
        return view('attendance.qr-management', [
            'site' => $site,
            'currentQr' => $currentQr,
            'currentToken' => $currentQr ? $service->plainToken($currentQr) : null,
            'tokens' => $tokens,
        ]);
    }
    public function store(GenerateQrTokenRequest $request, QrCodeService $service): mixed
    {
        $this->authorize('create', QrToken::class);
        $result = $service->createDaily(Site::findOrFail($request->integer('site_id')), $request->user());
        if (! $request->expectsJson()) {
            return redirect()->route('dashboard')->with(['daily_qr_token' => $result['token'], 'daily_qr_message' => $result['created'] ? 'QR du jour créé.' : 'Le QR du jour existe déjà.']);
        }
        return response()->json($result, $result['created'] ? 201 : 200);
    }

    public function current(Request $request, QrCodeService $service): JsonResponse
    {
        $this->authorize('viewAny', QrToken::class);
        $site = Site::query()->whereKey($request->integer('site_id'))->where('is_active', true)->firstOrFail();
        return response()->json($service->current($site));
    }

    public function regenerate(GenerateQrTokenRequest $request, QrCodeService $service): mixed
    {
        $this->authorize('create', QrToken::class);
        $result = $service->regenerate(Site::findOrFail($request->integer('site_id')), $request->user());
        if (! $request->expectsJson()) {
            return redirect()->route('dashboard')->with(['daily_qr_token' => $result['token'], 'daily_qr_message' => 'QR du jour régénéré.']);
        }
        return response()->json($result, 201);
    }

    public function deactivate(Request $request, QrToken $qrToken, QrCodeService $service): mixed
    {
        $this->authorize('update', $qrToken);
        $service->deactivate($qrToken);
        if (! $request->expectsJson()) {
            return redirect()->route('dashboard')->with('daily_qr_message', 'QR du jour désactivé.');
        }
        return response()->json(['message' => 'QR code désactivé.']);
    }

    public function export(Request $request, QrToken $qrToken): \Symfony\Component\HttpFoundation\Response
    {
        $this->authorize('export', $qrToken);
        abort_unless($qrToken->isValidForToday(), 422, 'Le QR code n’est plus actif.');
        $token = trim((string) $request->input('qr_token'));
        abort_unless($token !== '' && hash_equals($qrToken->token_hash, hash('sha256', $token)), 422, 'Le token QR ne correspond pas au QR enregistré.');
        $image = (string) $request->input('qr_image');
        if (! preg_match('#^data:image/png;base64,(?<data>[A-Za-z0-9+/=]+)$#', $image, $matches)) {
            abort(422, 'L’image du QR est invalide.');
        }
        $png = base64_decode($matches['data'], true);
        abort_unless($png !== false && strlen($png) > 20 && @getimagesizefromstring($png) !== false, 422, 'L’image du QR est invalide.');

        $title = htmlspecialchars('QR code de pointage - '.$qrToken->site->name, ENT_XML1 | ENT_COMPAT, 'UTF-8');
        $document = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><w:body><w:p><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:b/></w:rPr><w:t>'.$title.'</w:t></w:r></w:p><w:p><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:t>Généré le '.now()->format('d/m/Y à H:i').'</w:t></w:r></w:p><w:p><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:drawing><wp:inline xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"><wp:extent cx="3556000" cy="3556000"/><wp:docPr id="1" name="QR code"/><a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"><a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture"><pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture"><pic:nvPicPr><pic:cNvPr id="0" name="qr.png"/><pic:cNvPicPr/></pic:nvPicPr><pic:blipFill><a:blip r:embed="rIdImage"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill><pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="3556000" cy="3556000"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr></pic:pic></a:graphicData></a:graphic></wp:inline></w:drawing></w:r></w:p><w:p><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:t>Présentez ce QR code pour effectuer un pointage.</w:t></w:r></w:p><w:sectPr><w:pgSz w:w="11906" w:h="16838"/><w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440"/></w:sectPr></w:body></w:document>';
        $contentTypes = '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Default Extension="png" ContentType="image/png"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/></Types>';
        $rels = '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/></Relationships>';
        $documentRels = '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rIdImage" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/qr.png"/></Relationships>';
        $zip = new \ZipArchive();
        $path = tempnam(sys_get_temp_dir(), 'qr-docx-');
        abort_unless($path !== false && $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true, 500, 'Impossible de créer le document Word.');
        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rels);
        $zip->addFromString('word/document.xml', $document);
        $zip->addFromString('word/_rels/document.xml.rels', $documentRels);
        $zip->addFromString('word/media/qr.png', $png);
        $zip->close();
        return response()->download($path, 'qr-pointage-'.$qrToken->valid_on->format('Y-m-d').'.docx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])->deleteFileAfterSend(true);
    }
}
