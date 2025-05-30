<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreUpdateDocumentRequest;

class DocumentController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    public function index($dashboardType)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user()->load(['roles', 'otherService']);
        $role = $user->roles->first()->name;

        // Determine the service based on the user's role
        switch ($dashboardType) {
            case 'designer':
                $service = $user->otherService('Designer')->first();
                break;
            case 'photographer':
                $service = $user->otherService('Photographer')->first();
                break;
            case 'artist':
                $service = $user->otherService('Artist')->first();
                break;
            case 'videographer':
                $service = $user->otherService('Videographer')->first();
                break;
            case 'venue':
                $service = $user->venues()->first();
                break;
            case 'promoter':
                $service = $user->promoters()->first();
                break;
            default:
                $service = null;
        }

        if (is_null($service)) {
            return view('admin.dashboards.show-documents', [
                'user' => $user,
                'userId' => $this->getUserId(),
                'dashboardType' => $dashboardType,
                'modules' => $modules,
                'documents' => collect(),
                'message' => 'No documents available for your role.',
            ]);
        } else {
            $documents = Document::where('serviceable_type', get_class($service))
                ->where('serviceable_id', $service->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }


        return view('admin.dashboards.show-documents', [
            'userId' => $this->getUserId(),
            'documents' => $documents,
            'modules' => $modules,
            'dashboardType' => $dashboardType,
        ]);
    }

    /**
     * Display the specified document.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($dashboardType, $id)
    {
        $modules = collect(session('modules', []));
        $document = Document::findOrFail($id);

        return view('admin.dashboards.show-document', [
            'userId' => $this->getUserId(),
            'document' => $document,
            'dashboardType' => $dashboardType,
            'modules' => $modules,
        ]);
    }

    public function create($dashboardType)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user()->load(['roles', 'promoters', 'venues', 'otherService']);
        $role = $user->roles->first()->name;

        $serviceableId = null;
        $serviceableType = null;
        $service = null;

        $tags = config("document_tags.$dashboardType", []);

        switch (true) {
            case in_array($dashboardType, ['designer', 'photographer', 'artist', 'videographer']):
                $serviceableType = 'App\Models\OtherService';
                $service = $user->otherService(ucfirst($dashboardType))->first();
                $serviceableId = $service->id;
                break;
            case $dashboardType === 'venue':
                $serviceableType = 'App\Models\Venue';
                $service = $user->venues()->first();
                $serviceableId = $service->id;
                break;
            case $dashboardType === 'promoter':
                $serviceableType = 'App\Models\Promoter';
                $service = $user->promoters()->first();
                $serviceableId = $service->id;
                break;
            default:
                $serviceableType = null;
                $service = null;
                $serviceableId = null;
        }

        return view('admin.dashboards.new-document', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'serviceableType'  => $serviceableType,
            'serviceableId' => $serviceableId,
            'service' => $service,
            'tags' => $tags,
        ]);
    }

    public function storeDocument($dashboardType, StoreUpdateDocumentRequest $request)
    {
        $user = Auth::user();

        // Validate request data
        $validated = $request->validated();

        // Find the service
        $service = $validated['serviceable_type']::find($validated['serviceable_id']);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid service'
            ], 403);
        }

        // Process tags from JSON string to array
        $tagsArray = json_decode($request->tags) ?? [];
        $tagsArray = array_map('trim', $tagsArray);
        $tagsArray = array_filter($tagsArray);
        $tagsArray = array_unique($tagsArray);
        $tagsJson = json_encode(array_values($tagsArray));

        // Create document
        $document = Document::create([
            'user_id' => $user->id,
            'serviceable_type' => $validated['serviceable_type'],
            'service' => get_class($service),
            'serviceable_id' => $validated['serviceable_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $tagsJson,
            'file_path' => $validated['uploaded_file_path']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully!',
            'redirect_url' => route('admin.dashboard.document.show', [
                'dashboardType' => $dashboardType,
                'id' => $document->id
            ])
        ]);
    }

    public function fileUpload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx,txt,png,jpg,jpeg,heic,heif|max:25600',
            ]);

            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                $userId = auth()->id();
                $user = auth()->user();
                $userType = strtolower($user->getRoleNames()->first());

                // Get original file name and sanitize it
                $originalName = $request->file('file')->getClientOriginalName();
                $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $request->file('file')->getClientOriginalExtension();

                // Sanitize filename
                $fileName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $fileName));
                $fileName = $fileName . '_' . time() . '.' . $extension;

                // Create custom path with user type and ID
                $customPath = "documents/{$userType}/{$userId}";

                // Store file in public disk
                $path = Storage::disk('public')->putFileAs(
                    $customPath,
                    $request->file('file'),
                    $fileName
                );

                if (!$path) {
                    \Log::error('Failed to store file', [
                        'original_name' => $originalName,
                        'custom_path' => $customPath,
                        'file_name' => $fileName
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to store file'
                    ], 500);
                }

                return response()->json([
                    'success' => true,
                    'path' => $path,
                    'url' => Storage::disk('public')->url($path)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid file upload'
            ], 400);
        } catch (\Exception $e) {
            \Log::error('File upload error: ' . $e->getMessage(), [
                'file' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : 'No file'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing file upload: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteFile(Request $request)
    {
        $path = $request->input('path');

        if (Storage::exists($path)) {
            Storage::delete($path);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function edit($dashboardType, $id)
    {
        $modules = collect(session('modules', []));
        $document = Document::findOrFail($id);
        $tags = config("document_tags.$dashboardType", []);


        return view('admin.dashboards.edit-document', [
            'document' => $document,
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'tags' => $tags,
        ]);
    }

    public function update($dashboardType, StoreUpdateDocumentRequest $request, $id)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'title' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'array',
            'description' => 'nullable|string',
        ]);

        $document = Document::findOrFail($id);
        $tags = [];
        if (isset($validated['tags'])) {
            foreach ($validated['tags'] as $tag) {
                if (is_array($tag)) {
                    $tags = array_merge($tags, $tag); // Flatten the array
                } else {
                    $tags[] = $tag; // Add single tag
                }
            }
        }

        // Remove duplicates
        $tags = array_unique($tags);
        $validated['category'] = json_encode($tags);


        // Find and update the document
        $document = Document::findOrFail($id);
        $document->update($validated);

        return redirect()->route('admin.dashboard.document.show', ['dashboardType' => $dashboardType, 'id' => $document->id])->with('success', 'Document updated successfully!');
    }

    public function download($dashboardType, $id)
    {
        try {
            $document = Document::findOrFail($id);
            $filePath = storage_path("app/{$document->file_path}");

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            return response()->download($filePath);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error downloading file'
            ], 500);
        }
    }

    public function publicDownload($id)
    {
        try {
            // Find the document and ensure it's public
            $document = Document::where('id', $id)
                ->where('private', false)
                ->firstOrFail();

            $filePath = storage_path("app/{$document->file_path}");

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            return response()->download($filePath);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error downloading file'
            ], 500);
        }
    }

    public function delete($dashboardType, $id)
    {
        try {
            $document = Document::findOrFail($id);

            // Delete the file from storage if it exists
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Delete the document record
            $document->delete();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Document deleted successfully',
                    'redirect' => route('admin.dashboard.documents.index', ['dashboardType' => $dashboardType])
                ]);
            }

            return redirect()
                ->route('admin.dashboard.documents.index', ['dashboardType' => $dashboardType])
                ->with('success', 'Document deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Document deletion error: ' . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting document'
                ], 500);
            }

            return back()->with('error', 'Error deleting document');
        }
    }
}
