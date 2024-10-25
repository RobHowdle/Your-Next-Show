<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DocumentController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    public function index($dashboardType)
    {
        $user = Auth::user()->load('roles');
        $role = $user->roles->first()->name;
        $service = $user->otherService(ucfirst($role))->first();
        $dashboardType = lcfirst($service->services);

        if ($service) {
            $documents = Document::where('serviceable_id', $service->id)
                ->where('serviceable_type', get_class($service))
                ->get();
        } else {
            $documents = collect();
        }

        return view('admin.dashboards.show-documents', [
            'userId' => $this->getUserId(),
            'documents' => $documents,
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
        $user = Auth::user();
        $serviceable = $user->otherService()->first();
        $dashboardType = lcfirst($serviceable->services);
        $document = Document::findOrFail($id);

        $userId = auth()->user()->id;

        return view('admin.dashboards.show-document', [
            'userId' => $userId,
            'document' => $document,
            'dashboardType' => $dashboardType,
        ]);
    }

    public function create($dashboardType)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $serviceable = $user->otherService()->first();
        $serviceableId = $serviceable->id;
        $services = $serviceable->services;
        $dashboardType = lcfirst($service->services);
        $serviceableType = 'App\Models\OtherService';
        return view('admin.dashboards.create', [
            'userId' => $userId,
            'serviceableId' => $serviceableId,
            'services' => $services,
            'serviceableType' => $serviceableType,
            'dashboardType' => $dashboardType,
        ]);
    }

    public function storeDocument($dashboardType, Request $request)
    {
        $user = Auth::user();
        $serviceable = $user->otherService()->first();
        $dashboardType = lcfirst($serviceable->services);

        // Validate request data
        $request->validate([
            'title' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'array',
            'description' => 'nullable|string',
        ]);

        // Get uploaded files from the session
        $uploadedFiles = Session::get('uploaded_files', []);
        $user->load('roles');
        $role = $user->roles->first()->name;
        $service = $user->otherService(ucfirst($role))->first();

        if ($service) {
            $serviceableId = $service->id;
            $serviceableType = get_class($service);
            $serviceType = $service->otherServiceList()->first()->service_name;

            // Flatten and remove duplicates from tags
            $tags = [];
            if (isset($request->tags)) {
                foreach ($request->tags as $tag) {
                    if (is_array($tag)) {
                        $tags = array_merge($tags, $tag); // Flatten the array
                    } else {
                        $tags[] = $tag; // Add single tag
                    }
                }
            }

            $tags = array_unique($tags); // Remove duplicates
            $tagsJson = json_encode(array_values($tags)); // Encode tags as JSON

            // Loop through each uploaded file and create a document
            foreach ($uploadedFiles as $filePath) {
                $document = new Document();
                $document->user_id = $user->id;
                $document->serviceable_type = $serviceableType;
                $document->service = $serviceType;
                $document->serviceable_id = $serviceableId;
                $document->title = $request->title;
                $document->description = $request->description;
                $document->category = $tagsJson; // Store tags as JSON
                $document->file_path = $filePath;
                $document->save();
            }
        } else {
            return response()->json(['success' => false, 'message' => 'No service associated with this user'], 400);
        }

        // Clear uploaded files from the session
        Session::forget('uploaded_files');

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully!',
            'redirect_url' => route('admin.dashboard.document.show', ['dashboardType' => $dashboardType, 'id' => $document->id])
        ]);
    }



    public function fileUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt,png,jpg,jpeg|max:2048',
        ]);

        if ($request->file('file')) {
            $userId = auth()->id();
            $user = auth()->user();
            $userType = strtolower($user->getRoleNames()->first());

            $customPath = "documents/{$userType}/{$userId}";
            $path = $request->file('file')->store($customPath);

            $uploadedFiles = Session::get('uploaded_files', []);
            $uploadedFiles[] = $path;
            Session::put('uploaded_files', $uploadedFiles);

            return response()->json(['success' => true, 'path' => $path]);
        }

        return response()->json(['success' => false, 'message' => 'File upload failed.'], 400);
    }

    public function edit($dashboardType, $id)
    {
        $user = Auth::user();
        $serviceable = $user->otherService()->first();
        $dashboardType = lcfirst($serviceable->services);
        $document = Document::findOrFail($id);
        return view('admin.dashboards.edit-document', [
            'document' => $document,
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,

        ]);
    }

    public function update($dashboardType, Request $request, $id)
    {
        $user = Auth::user();
        $serviceable = $user->otherService()->first();
        $dashboardType = lcfirst($serviceable->services);
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



    public function download($id)
    {
        $document = Document::findOrFail($id);
        $filePath = storage_path("app/public/{$document->file_path}");

        return response()->download($filePath);
    }

    public function destroy($dashboardType, $id)
    {
        try {
            $document = Document::findOrFail($id);
            $document->delete();

            return response()->json(['success' => true, 'message' => 'Document deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete the document.'], 500);
        }
    }
}