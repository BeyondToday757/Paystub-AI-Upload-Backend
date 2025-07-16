<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Google\Cloud\DocumentAI\V1\Client\DocumentProcessorServiceClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Events\FileUploadProgress;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        $uuid = (string) Str::uuid();
        $path = $request->file('file')->store('uploads');
        $localPath = Storage::path($path);

        broadcast(new FileUploadProgress($uuid, 'File uploaded. Starting processing...'));

        // try {
        //     $projectId = env('GOOGLE_PROJECT_ID');
        //     $location = env('GOOGLE_LOCATION');
        //     $processorId = env('GOOGLE_PROCESSOR_ID');
        //     putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('app/google/paystub-465907-5f6004480b79.json'));
    
        //     $client = new DocumentProcessorServiceClient();
        //     $name = $client->processorName($projectId, $location, $processorId);
    
        //     broadcast(new DocumentProgress($uuid, 'Sending to Document AI...'));

        //     $document = (new \Google\Cloud\DocumentAI\V1\RawDocument())
        //         ->setContent(file_get_contents($localPath))
        //         ->setMimeType(mime_content_type($localPath));
    
        //     $request = (new \Google\Cloud\DocumentAI\V1\ProcessRequest())
        //         ->setName($name)
        //         ->setRawDocument($document);

        //     broadcast(new DocumentProgress($uuid, 'Processing complete. Generating summary...'));

        //     $response = $client->processDocument($request);
        //     $doc = $response->getDocument();

        //     $text = $doc->getText();
        //     $fields = [];
    
        //     foreach ($doc->getEntities() as $entity) {
        //         $fields[] = [
        //             'type' => $entity->getType(),
        //             'mentionText' => $entity->getMentionText(),
        //             'confidence' => $entity->getConfidence()
        //         ];
        //     }

        //     broadcast(new DocumentProgress($uuid, 'Summary ready.'));
    
        //     return response()->json([
        //         'summary' => collect($fields)->map(fn($item) => [
        //             'Label' => $item['type'],
        //             'Value' => $item['mentionText'],
        //             'Confidence' => round($item['confidence'] * 100) . '%'
        //         ])->values(),
        //         'rawText' => $text,
        //         'uuid' => $uuid,
        //     ]);
        // } catch (\Throwable $e) {
        //     broadcast(new FileUploadProgress($uuid, 'Processing failed: ' . $e->getMessage()));
        //     return response()->json(['error' => 'Document processing failed.'], 500);
        // }
        
        return response()->json([
            'summary' => [
                [
                    'Label' => 'Employee Name',
                    'Value' => 'George Mathew',
                    'Confidence' => '99%'
                ],
                [
                    'Label' => 'Pay Date',
                    'Value' => 'Jan 06, 2023',
                    'Confidence' => '99%'
                ],
                [
                    'Label' => 'Pay Period',
                    'Value' => 'Jan 01, 2023 to Jan 07, 2023',
                    'Confidence' => '98%'
                ],
                [
                    'Label' => 'Pay Schedule',
                    'Value' => 'Weekly',
                    'Confidence' => '98%'
                ],
                [
                    'Label' => 'Gross Earnings',
                    'Value' => '$600.00',
                    'Confidence' => '98%'
                ],
                [
                    'Label' => 'Net Pay',
                    'Value' => '$503.16',
                    'Confidence' => '97%'
                ],
                [
                    'Label' => 'Federal Tax',
                    'Value' => '$35.81',
                    'Confidence' => '96%'
                ],
                [
                    'Label' => 'Medicare',
                    'Value' => '$8.70',
                    'Confidence' => '96%'
                ],
                [
                    'Label' => 'FICA',
                    'Value' => '$37.20',
                    'Confidence' => '96%'
                ],
                [
                    'Label' => 'CA State',
                    'Value' => '$9.73',
                    'Confidence' => '96%'
                ],
                [
                    'Label' => 'CA SDI',
                    'Value' => '$5.40',
                    'Confidence' => '96%'
                ]
            ],
            'rawText' => "Design LLC\n(00-0012345)\n414, Any Street\nAny Town, CA 94578\nEarnings Statement\nCheck Number: 2810\nGeorge Mathew\n(XXX-XX-0909)\n1839, Echo Lane\nSan Leandro, CA 94578\n$600.00 $96.84 $503.16 $600.00 $96.84 $503.16"
        ]);
    }
}
