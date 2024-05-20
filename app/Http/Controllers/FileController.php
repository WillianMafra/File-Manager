<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToFavouritesRequest;
use App\Http\Requests\FilesActionRequest;
use App\Http\Requests\ShareFilesRequest;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\TrashFilesRequest;
use App\Http\Resources\FileResource;
use App\Mail\ShareFilesMail;
use App\Models\File;
use App\Models\FileShare;
use App\Models\StarredFile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;
use ZipArchive;

class FileController extends Controller
{
    public function myFiles(Request $request , string $folder = null)
    {
        if($folder){
            $folder = File::query()
            ->where('created_by', Auth::id())
            ->where('path', $folder)
            ->firstOrFail();
        }

        if(!$folder){
            $folder = $this->getRoot();
        }
        $favourites = (int)$request->get('favourites');


        $query = File::query()
        ->select('files.*')
        ->with('starred')
        ->where('parent_id', $folder->id)
        ->where('created_by', Auth::id())
        ->orderBy('is_folder', 'desc')
        ->orderBy('files.created_at', 'desc')
        ->orderBy('files.id', 'desc');
        

        if($favourites === 1){
            $query->join('starred_files', 'starred_files.file_id', 'files.id')->where('starred_files.user_id', Auth::id());
        }

        $files = $query->paginate(15);

        $files = FileResource::collection($files);

        if($request->wantsJson()) 
        {
            return $files;
        }

        $ancestors = FileResource::collection([...$folder->ancestors, $folder]); 

        $folder = new FileResource($folder);

        return Inertia::render('MyFiles', compact('files', 'folder', 'ancestors'));
    }
    public function trash(Request $request)
    {
        $files = File::onlyTrashed()
        ->where('created_by', auth()->user()->id)
        ->orderBy('is_folder', 'desc')
        ->orderBy('deleted_at', 'desc')
        ->paginate(15);

        $files = FileResource::collection($files);

        if($request->wantsJson()) 
        {
            return $files;
        }

        return Inertia::render('Trash', compact('files'));
    }

    public function createFolder(StoreFolderRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;

        if (!$parent) {
            $parent = $this->getRoot();
        }

        $file = new File();
        $file->is_folder = 1;
        $file->name = $data['name'];

        $parent->appendNode($file);
    }

    public function getRoot(){
        return File::query()->whereIsRoot()->where('created_by', Auth::id())->firstOrFail();
    }

    public function store(StoreFileRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;
        $user = $request->user();
        $fileTree = $request->file_tree;

        // $parent = File::query()->where('created_by', Auth::id())->where('id', $parent)->firstOrFail();

        if (!$parent) {
            $parent = $this->getRoot();
        }

        if (!empty($fileTree)) {
            $this->saveFileTree($fileTree, $parent, $user);
        } else {
            foreach ($data['files'] as $file) {
                /** @var \Illuminate\Http\UploadedFile $file */

                $this->saveFile($file, $user, $parent);
            }
        }
    }

    public function saveFileTree($fileTree, $parent, $user)
    {
        foreach ($fileTree as $name => $file){
            if(is_array($file)) // For folder
            {
                $folder = new File();
                $folder->is_folder = true;
                $folder->name = $name;

                $parent->appendNode($folder);
                $this->saveFileTree($file, $folder, $user);
            } else { // for file

                $this->saveFile($file, $user, $parent);
                
            }
        }
    }

    public function destroy(FilesActionRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;

        if($data['all']){
            $children = $parent->children;

            foreach($children as $child){
                $child->moveToTrash();
            }
        } else {
            foreach($data['ids'] ?? [] as $id)
            {
                $file = File::find($id);
                if($file)
                {
                    $file->moveToTrash();
                }
            }
        }

        return to_route('MyFiles', ['folder' => $parent->path]);
    }

    public function createZip($files): string
    {
        $zipPath = 'zip/'. Str::random().'zip';
        $publicPath = "public/$zipPath";

        if(!is_dir(dirname($publicPath)))
        {
            Storage::makeDirectory(dirname(($publicPath)));
        }

        $zipFile = Storage::path($publicPath);

        $zip = new \ZipArchive();

        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $this->addFilesToZip($zip, $files);
        }

        $zip->close();

        return asset(Storage::url($zipPath));
    }

    public function download(FilesActionRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;

        $all = $data['all'] ?? false;
        $ids = $data['ids'] ?? [];

        if(!$all && empty($ids)) {
            return [
                'message' => 'Please select files to download'
            ];
        }

        if($all) {
            $url = $this->createZip($parent->children);
            $filename = $parent->name . '.zip';
        } else {
            if(count($ids) === 1){
                $file = File::find($ids[0]);
                if($file->is_folder){
                    // is a folder
                    if($file->children->count() === 0 )
                    {
                        return [
                            'message' => 'The folder is empty'
                        ];
                    }
                    $url = $this->createZip($file->children);
                    $filename = $file->name . '.zip';
                } else {
                    // Its a file
                    $destination = 'public/'. pathinfo($file->storage_path, PATHINFO_BASENAME);
                    Storage::copy($file->storage_path, $destination);
                    
                    $url = asset(Storage::url($destination));
                    $filename = $file->name;
                }
            } else {
                $files = File::query()->whereIn('id', $ids)->get();

                $url = $this->createZip($files);
                $filename = $parent->name.'.zip';
            }
        }

        return [
            'url' => $url,
            'filename' => $filename
        ];
    }

    /**  @var \Illuminate\Http\UploadedFile $file */
    private function saveFile($file, $user, $parent): void
    {
        $path = $file->store('/files/' . $user->id);

        $model = new File();
        $model->storage_path = $path;
        $model->is_folder = false;
        $model->name = $file->getClientOriginalName();
        $model->mime = $file->getMimeType();
        $model->size = $file->getSize();

        $parent->appendNode($model);

    }

    private function addFilesToZip($zip, $files, $ancestors = '')
    {
        foreach($files as $file){
            if($file->is_folder)
            {
                $this->addFilesToZip($zip, $file->children, $ancestors . $file->name . '/');
            } else {
                $zip->addFile(Storage::path($file->storage_path), $ancestors . $file->name);
            }
        }
    }

    public function restore(TrashFilesRequest $request)
    {
        $data = $request->validated();

        if($data['all']){
            $children = File::onlyTrashed()->get();
            foreach ($children as $child){
                $child->restore();
            }
        } else {
            $ids = $data['ids'] ?? [];
            $children = File::onlyTrashed()->whereIn('id', $ids)->get();
            foreach ($children as $child){
                $child->restore();
            }
        }

        return to_route('trash');
    }

    public function deleteForever(TrashFilesRequest $request)
    {
        $data = $request->validated();

        if($data['all']){
            $children = File::onlyTrashed()->get();
            foreach ($children as $child){
                $child->deleteForever();
            }
        } else {
            $ids = $data['ids'] ?? [];
            $children = File::onlyTrashed()->whereIn('id', $ids)->get();
            foreach ($children as $child){
                $child->deleteForever();
            }
        }

        return to_route('trash');
    }

    public function addToFavourites(AddToFavouritesRequest $request)
    {
        $data = $request->validated();

        $id = $data['id'] ?? [];
        $file = File::find($id);

        $userId = Auth::id();

        $starredFile= StarredFile::query()
        ->where('user_id', $userId)
        ->where('file_id', $id)
        ->first();

        if($starredFile){
            $starredFile->delete();
        } else {
            $data = [
                'file_id' => $file->id,
                'user_id' => $userId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
    
           StarredFile::create($data);
        }
        

        return redirect()->back();
    }

    public function share(ShareFilesRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;

        $all = $data['all'] ?? false;
        $email = $data['email'] ?? false;
        $ids = $data['ids'] ?? [];

        if(!$all && empty($ids)) {
            return [
                'message' => 'Please select files to share'
            ];
        }

        $user = User::query()->where('email', $email)->first();

        if(!$user){
            return redirect()->back();
        }


        if($all){
            $files = $parent->children;
        } else {
            $files = File::find($ids);
        }

        $data = [];
        $ids =  Arr::pluck($files, 'id');

        $alreadyShared = FileShare::query()
        ->whereIn('file_id', $ids)
        ->where('user_id', $user->id)
        ->get()
        ->keyBy('file_id');

        foreach ($files as $file){
            if($alreadyShared->has($file->id)){
                continue;
            }
            $data[] = [
                'file_id' => $file->id,
                'user_id' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }

        FileShare::insert($data);
        
        Mail::to($user)->send(new ShareFilesMail($user, Auth::user(), $files));

        return redirect()->back();
    }

    public function sharedWithMe(Request $request)
    {
        $files = File::query()
        ->select('files.*')
        ->join('file_shares', 'file_shares.file_id', 'files.id')
        ->where('user_id', auth()->user()->id)
        ->orderBy('file_shares.created_at', 'desc')
        ->orderBy('files.id', 'desc')
        ->paginate(15);

        $files = FileResource::collection($files);

        if($request->wantsJson()){
            return $files;
        }

        return Inertia::render('SharedWithMe', compact('files'));
    }

    public function sharedByMe(Request $request)
    {
        $files = File::query()
        ->join('file_shares', 'file_shares.file_id', 'files.id')
        ->where('files.created_by', auth()->user()->id)
        ->orderBy('file_shares.created_at', 'desc')
        ->orderBy('files.id', 'desc')
        ->paginate(15);

        $files = FileResource::collection($files);

        if($request->wantsJson()){
            return $files;
        }

        return Inertia::render('SharedByMe', compact('files'));
    }
}
