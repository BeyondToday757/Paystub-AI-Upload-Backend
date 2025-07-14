<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Google\Cloud\DocumentAI\V1\Client\DocumentProcessorServiceClient;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        return response()->json(['message' => 'File is valid.']);
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $path = $request->file('file')->store('uploads');

        return response()->json([
            'message' => 'File uploaded successfully.',
            'path' => $path,
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'filePath' => 'required|string',
        ]);

        $filePath = $request->input('filePath');
        $localPath = Storage::path($filePath);

        $projectId = env('GOOGLE_PROJECT_ID');
        $location = env('GOOGLE_LOCATION');
        $processorId = env('GOOGLE_PROCESSOR_ID');
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('app/google/paystub-465907-5f6004480b79.json'));

        $client = new DocumentProcessorServiceClient();
        $name = $client->processorName($projectId, $location, $processorId);

        $document = (new \Google\Cloud\DocumentAI\V1\RawDocument())
            ->setContent(file_get_contents($localPath))
            ->setMimeType(mime_content_type($localPath));

        $request = (new \Google\Cloud\DocumentAI\V1\ProcessRequest())
            ->setName($name)
            ->setRawDocument($document);

        $response = $client->processDocument($request);
        $doc = $response->getDocument();

        $text = $doc->getText();
        $fields = [];

        foreach ($doc->getEntities() as $entity) {
            $fields[] = [
                'type' => $entity->getType(),
                'mentionText' => $entity->getMentionText(),
                'confidence' => $entity->getConfidence()
            ];
        }

        return response()->json([
            'summary' => collect($fields)->map(fn($item) => [
                'Label' => $item['type'],
                'Value' => $item['mentionText'],
                'Confidence' => round($item['confidence'] * 100) . '%'
            ])->values(),
            'rawText' => $text,
        ]);
    }
}
