<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MediaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate([
            'search' => ['sometimes', 'string', 'max:100'],
        ]);

        $items = Media::query()
            ->when(isset($filters['search']) && $filters['search'] !== '', function ($query) use ($filters): void {
                $query->where('title', 'like', '%' . $filters['search'] . '%');
            })
            ->withCount('users')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return MediaResource::collection($items);
    }

    public function show(Media $media): MediaResource
    {
        return new MediaResource($media->load('users'));
    }

    public function store(SaveMediaRequest $request): JsonResponse
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media))
            ->additional(['message' => 'Media berhasil ditambahkan.'])
            ->response()
            ->setStatusCode(201);
    }

    public function update(SaveMediaRequest $request, Media $media): MediaResource
    {
        $media->update($request->validated());

        return (new MediaResource($media->refresh()))
            ->additional(['message' => 'Media berhasil diperbarui.']);
    }

    public function destroy(Media $media): JsonResponse
    {
        $media->delete(); // Relasi pivot ikut terhapus melalui ON DELETE CASCADE.

        return response()->json([
            'message' => 'Media berhasil dihapus.',
            'data' => null,
        ], 200);
    }
}
